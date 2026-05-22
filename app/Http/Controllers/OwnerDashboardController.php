<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    /**
     * Dashboard principal propriétaire
     */
    public function index()
    {
        $today = Carbon::today();

        // ── Revenus ──────────────────────────────────────────────
        $revenueToday = Payment::where('payment_date', $today)
            ->where('status', 'paid')->sum('amount');

        $revenueWeek = Payment::whereBetween('payment_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()])
            ->where('status', 'paid')->sum('amount');

        $revenueMonth = Payment::whereMonth('payment_date', $today->month)
            ->whereYear('payment_date', $today->year)
            ->where('status', 'paid')->sum('amount');

        $revenueYear = Payment::whereYear('payment_date', $today->year)
            ->where('status', 'paid')->sum('amount');

        // ── Membres ───────────────────────────────────────────────
        $totalMembers       = Member::count();
        $activeMembers      = Member::where('status', 'active')->count();
        $newMembersToday    = Member::whereDate('created_at', $today)->count();
        $newMembersMonth    = Member::whereMonth('created_at', $today->month)->count();

        $expiringCount = Subscription::where('status', 'active')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(7)])
            ->count();

        // ── Graphique 6 mois ──────────────────────────────────────
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $revenueChart[] = [
                'month'   => $month->format('M'),
                'revenue' => Payment::whereMonth('payment_date', $month->month)
                    ->whereYear('payment_date', $month->year)
                    ->where('status', 'paid')->sum('amount'),
            ];
        }

        // ── Répartition par type d'abonnement (ce mois) ──────────
        $plans = SubscriptionPlan::with(['subscriptions' => fn($q) => $q
                ->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
        ])->get();
        
        $planBreakdown = $plans->map(function($plan) {
            $plan->month_revenue = $plan->subscriptions->sum('amount_paid');
            $plan->month_count = $plan->subscriptions->count();
            return $plan;
        });

        // ── Derniers paiements du jour ────────────────────────────
        $todayPayments = Payment::with(['member', 'subscription.plan'])
            ->where('payment_date', $today)
            ->where('status', 'paid')
            ->latest()
            ->limit(8)
            ->get();

        return view('owner.dashboard', compact(
            'revenueToday', 'revenueWeek', 'revenueMonth', 'revenueYear',
            'totalMembers', 'activeMembers', 'newMembersToday', 'newMembersMonth',
            'expiringCount', 'revenueChart', 'planBreakdown', 'todayPayments'
        ));
    }

    /**
     * Drill-down : détail des paiements d'une période
     * Ex: /owner/revenue/detail?period=today | week | month | year | custom&date=2024-01
     */
    public function revenueDetail(Request $request)
    {
        $period = $request->get('period', 'today');
        $today  = Carbon::today();

        switch ($period) {
            case 'today':
                $query     = Payment::whereDate('payment_date', $today);
                $label     = 'Aujourd\'hui — ' . $today->format('d/m/Y');
                $dateRange = [$today, $today];
                break;
            case 'week':
                $start     = $today->copy()->startOfWeek();
                $end       = $today->copy()->endOfWeek();
                $query     = Payment::whereBetween('payment_date', [$start, $end]);
                $label     = 'Cette semaine — du ' . $start->format('d/m') . ' au ' . $end->format('d/m/Y');
                $dateRange = [$start, $end];
                break;
            case 'month':
                $query     = Payment::whereMonth('payment_date', $today->month)->whereYear('payment_date', $today->year);
                $label     = $today->format('F Y');
                $dateRange = [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()];
                break;
            case 'year':
                $query     = Payment::whereYear('payment_date', $today->year);
                $label     = 'Année ' . $today->year;
                $dateRange = [$today->copy()->startOfYear(), $today->copy()->endOfYear()];
                break;
            case 'custom':
                // ?date=2024-01 → janvier 2024
                $date  = Carbon::parse($request->get('date', now()->format('Y-m')));
                $query = Payment::whereMonth('payment_date', $date->month)->whereYear('payment_date', $date->year);
                $label = $date->format('F Y');
                $dateRange = [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()];
                break;
            default:
                $query     = Payment::whereDate('payment_date', $today);
                $label     = 'Aujourd\'hui';
                $dateRange = [$today, $today];
        }

        $payments = $query->with(['member', 'subscription.plan'])
            ->where('status', 'paid')
            ->latest('payment_date')
            ->get();

        $total = $payments->sum('amount');

        // Grouper par plan
        $byPlan = $payments
            ->filter(fn($p) => $p->subscription?->plan)
            ->groupBy(fn($p) => $p->subscription->plan->name)
            ->map(fn($group, $name) => [
                'name'    => $name,
                'count'   => $group->count(),
                'total'   => $group->sum('amount'),
                'members' => $group->map(fn($p) => $p->member)->unique('id'),
            ])
            ->sortByDesc('total')
            ->values();

        return view('owner.revenue-detail', compact(
            'payments', 'total', 'label', 'period', 'byPlan', 'dateRange'
        ));
    }

    /**
     * Vue synthèse des abonnements actifs par plan
     */
    public function subscriptionOverview()
    {
        $plans = SubscriptionPlan::with([
            'subscriptions' => fn($q) => $q->where('status', 'active')->with('member'),
        ])->get();

        $expiringSoon = Subscription::where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->with(['member', 'plan'])
            ->orderBy('end_date')
            ->get();

        return view('owner.subscriptions', compact('plans', 'expiringSoon'));
    }
}
