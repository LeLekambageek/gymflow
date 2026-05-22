<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\CourseSession;
use App\Models\Booking;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Stats globales
        $totalMembers      = Member::count();
        $activeMembers     = Member::where('status', 'active')->count();
        $newMembersMonth   = Member::whereMonth('created_at', $today->month)->count();

        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('end_date', '>=', $today)->count();

        $expiringSubscriptions = Subscription::where('status', 'active')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(7)])->count();

        // Revenus
        $revenueToday   = Payment::where('payment_date', $today)->where('status', 'paid')->sum('amount');
        $revenueMonth   = Payment::whereMonth('payment_date', $today->month)
                                  ->whereYear('payment_date', $today->year)
                                  ->where('status', 'paid')->sum('amount');

        // Cours aujourd'hui
        $todaySessions = CourseSession::with(['course.coach'])
            ->whereDate('start_time', $today)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        // Derniers membres inscrits
        $recentMembers = Member::with('activeSubscription.plan')
            ->latest()->limit(5)->get();

        // Derniers paiements
        $recentPayments = Payment::with('member')
            ->latest()->limit(5)->get();

        // Graphique revenus 6 derniers mois
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

        return view('dashboard.index', compact(
            'totalMembers', 'activeMembers', 'newMembersMonth',
            'activeSubscriptions', 'expiringSubscriptions',
            'revenueToday', 'revenueMonth',
            'todaySessions', 'recentMembers', 'recentPayments',
            'revenueChart'
        ));
    }
}
