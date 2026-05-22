@extends('layouts.owner')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">TABLEAU DE BORD</div>
        <div class="page-subtitle">{{ now()->isoFormat('dddd D MMMM Y') }}</div>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(2,1fr)">
    <div class="stat-card" style="--accent:var(--orange)">
        <div class="stat-label">Membres actifs</div>
        <div class="stat-value">{{ $activeMembers }}</div>
        <div class="stat-note">{{ $totalMembers }} au total</div>
    </div>
    <div class="stat-card" style="--accent:var(--green)">
        <div class="stat-label">Abonnements</div>
        <div class="stat-value">{{ $activeSubscriptions }}</div>
        @if($expiringSubscriptions > 0)
        <div class="stat-note" style="color:var(--orange)">⚠ {{ $expiringSubscriptions }} expirent 7j</div>
        @else
        <div class="stat-note">Aucune expiration</div>
        @endif
    </div>
    <div class="stat-card" style="--accent:var(--blue)">
        <div class="stat-label">Revenus auj.</div>
        <div class="stat-value" style="font-size:22px">{{ number_format($revenueToday,0,',',' ') }}</div>
        <div class="stat-note">FCFA</div>
    </div>
    <div class="stat-card" style="--accent:var(--green)">
        <div class="stat-label">Revenus mois</div>
        <div class="stat-value" style="font-size:22px">{{ number_format($revenueMonth,0,',',' ') }}</div>
        <div class="stat-note">FCFA</div>
    </div>
</div>

{{-- GRAPHIQUE --}}
<div class="card">
    <div class="card-title">Revenus — 6 derniers mois</div>
    <div style="position:relative;height:180px">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

{{-- COURS DU JOUR --}}
<div class="card">
    <div class="card-title">Séances aujourd'hui</div>
    @if($todaySessions->count() > 0)
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($todaySessions as $session)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--bg3);border-radius:8px;border-left:3px solid var(--orange)">
            <div>
                <div class="fw-500" style="font-size:13px">{{ $session->course->name }}</div>
                <div class="text-sm text-muted">
                    {{ $session->start_time->format('H:i') }}
                    @if($session->course->coach) · {{ $session->course->coach->full_name }}@endif
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0;margin-left:8px">
                <div class="text-sm fw-500">{{ $session->registered_count }}/{{ $session->course->capacity }}</div>
                <div class="progress" style="width:60px;margin-top:3px">
                    <div class="progress-bar" style="width:{{ $session->course->capacity>0?($session->registered_count/$session->course->capacity*100):0 }}%"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state" style="padding:20px">Aucune séance aujourd'hui</div>
    @endif
</div>

{{-- DERNIERS MEMBRES --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <div class="card-title" style="margin:0">Derniers membres</div>
        <a href="{{ route('members.index') }}" class="btn btn-secondary btn-sm">Voir tout →</a>
    </div>
    <div style="display:flex;flex-direction:column;gap:2px">
        @foreach($recentMembers as $member)
        <a href="{{ route('members.show',$member) }}" style="display:flex;align-items:center;justify-content:space-between;padding:9px 10px;border-radius:8px;text-decoration:none;color:inherit">
            <div style="display:flex;align-items:center;gap:9px">
                <div class="avatar">{{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}</div>
                <div>
                    <div class="member-name">{{ $member->full_name }}</div>
                    <div class="member-sub">{{ $member->activeSubscription?->plan->name ?? 'Aucun abonnement' }}</div>
                </div>
            </div>
            <span class="badge badge-{{ $member->status }}">{{ $member->status }}</span>
        </a>
        @endforeach
    </div>
</div>

{{-- DERNIERS PAIEMENTS --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <div class="card-title" style="margin:0">Derniers paiements</div>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary btn-sm">Voir tout →</a>
    </div>
    <div style="display:flex;flex-direction:column;gap:2px">
        @foreach($recentPayments as $payment)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 10px;border-radius:8px">
            <div>
                <div class="fw-500" style="font-size:13px">{{ $payment->member?->full_name ?? 'N/A' }}</div>
                <div class="text-sm text-muted">{{ $payment->payment_date->format('d/m/Y') }} · {{ $payment->method }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;margin-left:8px">
                <div class="fw-500 text-green mono" style="font-size:13px">+{{ number_format($payment->amount,0,',',' ') }} F</div>
                <span class="badge badge-{{ $payment->status }}">{{ $payment->status }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
var ctx=document.getElementById('revenueChart').getContext('2d');
var data=@json($revenueChart);
new Chart(ctx,{
    type:'bar',
    data:{
        labels:data.map(function(d){return d.month;}),
        datasets:[{
            data:data.map(function(d){return d.revenue;}),
            backgroundColor:'rgba(249,115,22,0.75)',
            borderRadius:6,
            borderSkipped:false
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{legend:{display:false}},
        scales:{
            x:{grid:{color:'#2a3347'},ticks:{color:'#6b7a99',font:{size:10}}},
            y:{grid:{color:'#2a3347'},ticks:{color:'#6b7a99',font:{size:10},callback:function(v){return v>=1000?Math.round(v/1000)+'k':v;}}}
        }
    }
});
</script>
@endpush
@endsection
