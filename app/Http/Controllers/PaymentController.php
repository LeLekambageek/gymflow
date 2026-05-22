<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Member;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('member');

        if ($request->month) {
            $query->whereMonth('payment_date', Carbon::parse($request->month)->month)
                  ->whereYear('payment_date', Carbon::parse($request->month)->year);
        }

        if ($request->method) $query->where('method', $request->method);
        if ($request->status) $query->where('status', $request->status);

        $payments  = $query->latest('payment_date')->paginate(20)->withQueryString();
        $totalPaid = $query->clone()->where('status', 'paid')->sum('amount');

        // Stats mensuelles
        $monthlyRevenue = Payment::where('status', 'paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        return view('payments.index', compact('payments', 'totalPaid', 'monthlyRevenue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id'    => 'required|exists:members,id',
            'amount'       => 'required|numeric|min:0',
            'type'         => 'required|string',
            'method'       => 'required|string',
            'notes'        => 'nullable|string',
            'payment_date' => 'required|date',
        ]);

        Payment::create(array_merge($validated, ['status' => 'paid']));
        return back()->with('success', 'Paiement enregistré.');
    }

    public function report()
    {
        $now = now();
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $data[] = [
                'month'        => $month->format('M Y'),
                'subscription' => Payment::where('type', 'subscription')
                    ->where('status', 'paid')
                    ->whereMonth('payment_date', $month->month)
                    ->whereYear('payment_date', $month->year)
                    ->sum('amount'),
                'course'       => Payment::where('type', 'course')
                    ->where('status', 'paid')
                    ->whereMonth('payment_date', $month->month)
                    ->whereYear('payment_date', $month->year)
                    ->sum('amount'),
                'other'        => Payment::where('type', 'other')
                    ->where('status', 'paid')
                    ->whereMonth('payment_date', $month->month)
                    ->whereYear('payment_date', $month->year)
                    ->sum('amount'),
            ];
        }

        $topMembers = Member::withSum(['payments as total_paid' => fn($q) => $q->where('status', 'paid')], 'amount')
            ->orderByDesc('total_paid')
            ->limit(10)
            ->get();

        return view('payments.report', compact('data', 'topMembers'));
    }
}
