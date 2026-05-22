<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['member', 'plan']);

        if ($request->status) $query->where('status', $request->status);

        if ($request->expiring) {
            $query->where('status', 'active')
                  ->where('end_date', '<=', now()->addDays(7));
        }

        $subscriptions = $query->latest()->paginate(15)->withQueryString();
        $plans = SubscriptionPlan::where('is_active', true)->get();

        return view('subscriptions.index', compact('subscriptions', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'            => 'required|exists:members,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'start_date'           => 'required|date',
            'payment_method'       => 'required|string',
            'notes'                => 'nullable|string',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

        $subscription = Subscription::create(array_merge($validated, [
            'end_date'    => \Carbon\Carbon::parse($validated['start_date'])->addDays($plan->duration_days),
            'status'      => 'active',
            'amount_paid' => $plan->price,
        ]));

        Payment::create([
            'member_id'       => $validated['member_id'],
            'subscription_id' => $subscription->id,
            'amount'          => $plan->price,
            'type'            => 'subscription',
            'method'          => $validated['payment_method'],
            'status'          => 'paid',
            'payment_date'    => now(),
        ]);

        return back()->with('success', 'Abonnement créé avec succès.');
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);
        return back()->with('success', 'Abonnement annulé.');
    }

    public function plans()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->get();
        return view('subscriptions.plans', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:100',
            'description'           => 'nullable|string',
            'price'                 => 'required|numeric|min:0',
            'duration_days'         => 'required|integer|min:1',
            'max_courses_per_week'  => 'nullable|integer|min:0',
        ]);

        SubscriptionPlan::create(array_merge($validated, ['is_active' => true]));

        return back()->with('success', 'Plan créé.');
    }
}
