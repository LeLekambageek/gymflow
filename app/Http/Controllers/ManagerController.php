<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManagerController extends Controller
{
    /**
     * Dashboard gérant
     */
    public function dashboard()
    {
        $today = Carbon::today();

        // Stats du jour pour le gérant
        $newToday         = Member::whereDate('created_at', $today)->count();
        $renewalsToday    = Subscription::whereDate('created_at', $today)->where('status', 'active')->count();
        $activeCount      = Subscription::where('status', 'active')->where('end_date', '>=', $today)->count();
        $expiringCount    = Subscription::where('status', 'active')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(7)])
            ->count();

        $revenueToday = Payment::where('payment_date', $today)->where('status', 'paid')->sum('amount');

        // Plans disponibles
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();

        // Dernières inscriptions du jour
        $todayRegistrations = Member::with('activeSubscription.plan')
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        // Abonnements expirant bientôt
        $expiringSoon = Subscription::where('status', 'active')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(7)])
            ->with(['member', 'plan'])
            ->orderBy('end_date')
            ->get();

        // Tous les abonnements actifs (liste complète)
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('end_date', '>=', $today)
            ->with(['member', 'plan'])
            ->orderBy('end_date')
            ->paginate(20);

        return view('manager.dashboard', compact(
            'newToday', 'renewalsToday', 'activeCount', 'expiringCount',
            'revenueToday', 'plans', 'todayRegistrations',
            'expiringSoon', 'activeSubscriptions'
        ));
    }

    /**
     * Enregistrer un nouveau client + abonnement en une action
     */
    public function registerMember(Request $request)
    {
        $validated = $request->validate([
            'first_name'           => 'required|string|max:100',
            'last_name'            => 'required|string|max:100',
            'phone'                => 'required|string|max:20',
            'email'                => 'nullable|email|unique:members',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_method'       => 'required|string',
        ]);

        // Créer le membre
        $member = Member::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'phone'      => $validated['phone'],
            'email'      => $validated['email'] ?? strtolower($validated['first_name'].'.'.$validated['last_name'].'@gymflow.local'),
            'status'     => 'active',
            'qr_code'    => Str::uuid(),
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

        // Créer l'abonnement
        $subscription = Subscription::create([
            'member_id'            => $member->id,
            'subscription_plan_id' => $plan->id,
            'start_date'           => now(),
            'end_date'             => now()->addDays($plan->duration_days),
            'status'               => 'active',
            'amount_paid'          => $plan->price,
            'payment_method'       => $validated['payment_method'],
        ]);

        // Enregistrer le paiement
        Payment::create([
            'member_id'       => $member->id,
            'subscription_id' => $subscription->id,
            'amount'          => $plan->price,
            'type'            => 'subscription',
            'method'          => $validated['payment_method'],
            'status'          => 'paid',
            'payment_date'    => now(),
        ]);

        return back()->with('success', "✓ {$member->full_name} inscrit(e) — {$plan->name} ({$plan->price} FCFA)");
    }

    /**
     * Renouveler l'abonnement d'un membre existant
     */
    public function renewSubscription(Request $request)
    {
        $validated = $request->validate([
            'member_id'            => 'required|exists:members,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'payment_method'       => 'required|string',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $plan   = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

        // Récupérer l'ancien abonnement actif s'il existe
        $oldSubscription = Subscription::where('member_id', $member->id)
            ->where('status', 'active')
            ->first();

        // Calculer la nouvelle date de fin:
        // Si l'ancien abonnement existe et n'a pas expiré, prolonger à partir de sa date d'expiration
        // Sinon, commencer à partir d'aujourd'hui
        $newEndDate = today()->addDays($plan->duration_days);
        if ($oldSubscription && $oldSubscription->end_date > today()) {
            // Ajouter les jours du nouvel abonnement à la date d'expiration de l'ancien
            $newEndDate = $oldSubscription->end_date->addDays($plan->duration_days);
        }

        // Marquer l'ancien abonnement comme expiré
        if ($oldSubscription) {
            $oldSubscription->update(['status' => 'expired']);
        }

        // Créer le nouvel abonnement
        $subscription = Subscription::create([
            'member_id'            => $member->id,
            'subscription_plan_id' => $plan->id,
            'start_date'           => now(),
            'end_date'             => $newEndDate,
            'status'               => 'active',
            'amount_paid'          => $plan->price,
            'payment_method'       => $validated['payment_method'],
        ]);

        Payment::create([
            'member_id'       => $member->id,
            'subscription_id' => $subscription->id,
            'amount'          => $plan->price,
            'type'            => 'subscription',
            'method'          => $validated['payment_method'],
            'status'          => 'paid',
            'payment_date'    => now(),
        ]);

        return back()->with('success', "✓ Abonnement de {$member->full_name} renouvelé — {$plan->name}");
    }

    /**
     * Liste des membres — vue gérant (avec layout manager)
     */
    public function members(Request $request)
    {
        $query = Member::with('activeSubscription.plan');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name',  'like', "%{$request->search}%")
                  ->orWhere('phone',      'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(20)->withQueryString();

        return view('manager.members', compact('members'));
    }

    /**
     * Recherche de membres (AJAX pour le champ de renouvellement)
     */
    public function searchMembers(Request $request)
    {
        $q = $request->get('q', '');

        $members = Member::where(function ($query) use ($q) {
            $query->where('first_name', 'like', "%{$q}%")
                  ->orWhere('last_name',  'like', "%{$q}%")
                  ->orWhere('phone',      'like', "%{$q}%");
        })
        ->with('activeSubscription.plan')
        ->limit(8)
        ->get()
        ->map(fn($m) => [
            'id'           => $m->id,
            'name'         => $m->full_name,
            'phone'        => $m->phone,
            'plan'         => $m->activeSubscription?->plan->name ?? 'Aucun abonnement',
            'expires'      => $m->activeSubscription?->end_date->format('d/m/Y') ?? '—',
            'days_left'    => $m->activeSubscription?->days_remaining ?? 0,
        ]);

        return response()->json($members);
    }
}
