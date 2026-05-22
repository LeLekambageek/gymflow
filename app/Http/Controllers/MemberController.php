<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with('activeSubscription.plan');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(15)->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('members.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email|unique:members',
            'phone'             => 'nullable|string|max:20',
            'birth_date'        => 'nullable|date',
            'address'           => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone'   => 'nullable|string|max:20',
            'status'            => 'required|in:active,inactive,suspended',
            'plan_id'           => 'nullable|exists:subscription_plans,id',
        ]);

        $member = Member::create(array_merge($validated, [
            'qr_code' => Str::uuid(),
        ]));

        // Créer un abonnement si un plan est sélectionné
        if ($request->plan_id) {
            $plan = SubscriptionPlan::findOrFail($request->plan_id);
            Subscription::create([
                'member_id'            => $member->id,
                'subscription_plan_id' => $plan->id,
                'start_date'           => now(),
                'end_date'             => now()->addDays($plan->duration_days),
                'status'               => 'active',
                'amount_paid'          => $plan->price,
                'payment_method'       => $request->payment_method ?? 'cash',
            ]);
        }

        return redirect()->route('members.show', $member)
            ->with('success', "Membre {$member->full_name} créé avec succès.");
    }

    public function show(Member $member)
    {
        $member->load(['subscriptions.plan', 'bookings.session.course', 'payments']);
        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('members.edit', compact('member', 'plans'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => "required|email|unique:members,email,{$member->id}",
            'phone'             => 'nullable|string|max:20',
            'birth_date'        => 'nullable|date',
            'address'           => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone'   => 'nullable|string|max:20',
            'status'            => 'required|in:active,inactive,suspended',
        ]);

        $member->update($validated);

        return redirect()->route('members.show', $member)
            ->with('success', 'Informations mises à jour.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('members.index')
            ->with('success', 'Membre supprimé.');
    }
}
