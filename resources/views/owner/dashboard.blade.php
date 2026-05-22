@extends('layouts.owner')
@section('title', 'Dashboard Propriétaire')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">PROPRIÉTAIRE</div>
        <div class="page-subtitle">{{ now()->isoFormat('ddd D MMM Y') }}</div>
    </div>
    <a href="{{ route('owner.subscriptions') }}" class="btn btn-secondary btn-sm">Abonnements</a>
</div>

{{-- REVENUS CLIQUABLES --}}
<div class="stats-grid" style="grid-template-columns:repeat(2,1fr)">
    <a href="{{ route('owner.revenue.detail',['period'=>'today']) }}" style="text-decoration:none">
        <div class="stat-card" style="--accent:var(--orange);cursor:pointer">
            <div class="stat-label">Aujourd'hui</div>
            <div class="stat-value" style="font-size:24px">{{ number_format($revenueToday,0,',',' ') }}</div>
            <div class="stat-note">FCFA <span style="color:var(--orange)">↗ Détail</span></div>
        </div>
    </a>
    <a href="{{ route('owner.revenue.detail',['period'=>'week']) }}" style="text-decoration:none">
        <div class="stat-card" style="--accent:var(--blue);cursor:pointer">
            <div class="stat-label">Cette semaine</div>
            <div class="stat-value" style="font-size:24px">{{ number_format($revenueWeek,0,',',' ') }}</div>
            <div class="stat-note">FCFA <span style="color:var(--blue)">↗ Détail</span></div>
        </div>
    </a>
    <a href="{{ route('owner.revenue.detail',['period'=>'month']) }}" style="text-decoration:none">
        <div class="stat-card" style="--accent:var(--green);cursor:pointer">
            <div class="stat-label">{{ now()->format('F') }}</div>
            <div class="stat-value" style="font-size:24px">{{ number_format($revenueMonth,0,',',' ') }}</div>
            <div class="stat-note">FCFA <span style="color:var(--green)">↗ Détail</span></div>
        </div>
    </a>
    <a href="{{ route('owner.revenue.detail',['period'=>'year']) }}" style="text-decoration:none">
        <div class="stat-card" style="--accent:#a855f7;cursor:pointer">
            <div class="stat-label">Année {{ now()->year }}</div>
            <div class="stat-value" style="font-size:22px">{{ number_format($revenueYear,0,',',' ') }}</div>
            <div class="stat-note">FCFA <span style="color:#a855f7">↗ Détail</span></div>
        </div>
    </a>
</div>

{{-- MEMBRES --}}
<div class="stats-grid" style="grid-template-columns:repeat(2,1fr)">
    <div class="stat-card" style="--accent:var(--muted)">
        <div class="stat-label">Total membres</div>
        <div class="stat-value">{{ $totalMembers }}</div>
        <div class="stat-note">{{ $activeMembers }} actifs</div>
    </div>
    <div class="stat-card" style="--accent:var(--green)">
        <div class="stat-label">Nouveaux auj.</div>
        <div class="stat-value">{{ $newMembersToday }}</div>
        <div class="stat-note">{{ $newMembersMonth }} ce mois</div>
    </div>
    <div class="stat-card" style="--accent:var(--blue)">
        <div class="stat-label">Abonnements</div>
        <div class="stat-value">{{ $activeMembers }}</div>
        <div class="stat-note">Actifs</div>
    </div>
    <div class="stat-card" style="--accent:var(--red)">
        <div class="stat-label">Expirent 7j</div>
        <div class="stat-value" style="{{ $expiringCount>0?'color:var(--red)':'' }}">{{ $expiringCount }}</div>
        <div class="stat-note">{{ $expiringCount>0?'À contacter !':'RAS' }}</div>
    </div>
</div>

{{-- GRAPHIQUE --}}
<div class="card">
    <div class="card-title">Revenus — 6 derniers mois</div>
    <div style="position:relative;height:180px">
        <canvas id="revenueChart"></canvas>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:12px">
        @foreach(['today'=>"Aujourd'hui",'week'=>'Semaine','month'=>'Mois','year'=>'Année'] as $p=>$lbl)
        <a href="{{ route('owner.revenue.detail',['period'=>$p]) }}" class="btn btn-secondary btn-sm" style="flex:1;min-width:70px;font-size:11px;text-align:center">{{ $lbl }}</a>
        @endforeach
    </div>
</div>

{{-- RÉPARTITION PLANS --}}
<div class="card">
    <div class="card-title">Répartition par plan — ce mois</div>
    @foreach($planBreakdown as $plan)
    @php $rev=$plan->month_revenue??0; $max=$planBreakdown->max('month_revenue')?:1; @endphp
    <div style="margin-bottom:12px">
        <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:13px">
            <span class="fw-500">{{ $plan->name }}</span>
            <span class="fw-500 text-orange">{{ number_format($rev,0,',',' ') }} F</span>
        </div>
        <div class="progress"><div class="progress-bar" style="width:{{ $max>0?($rev/$max*100):0 }}%"></div></div>
        <div class="text-sm text-muted mt-4">{{ $plan->month_count??0 }} souscriptions</div>
    </div>
    @endforeach
</div>

{{-- PAIEMENTS DU JOUR --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div class="card-title" style="margin:0">Paiements aujourd'hui ({{ $todayPayments->count() }})</div>
        <a href="{{ route('owner.revenue.detail',['period'=>'today']) }}" class="btn btn-secondary btn-sm">Tout →</a>
    </div>
    @forelse($todayPayments as $p)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:9px">
            <div class="avatar" style="color:var(--orange)">{{ strtoupper(substr($p->member?->first_name??'?',0,1).substr($p->member?->last_name??'?',0,1)) }}</div>
            <div>
                <div class="member-name">{{ $p->member?->full_name??'N/A' }}</div>
                <div class="member-sub">{{ $p->subscription?->plan?->name ?? $p->type }} · {{ $p->method }}</div>
            </div>
        </div>
        <div class="fw-500 text-green mono" style="font-size:13px;flex-shrink:0;margin-left:8px">+{{ number_format($p->amount,0,',',' ') }} F</div>
    </div>
    @empty
    <div class="empty-state" style="padding:24px">Aucun paiement aujourd'hui</div>
    @endforelse
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
var ctx=document.getElementById('revenueChart').getContext('2d');
var data=@json($revenueChart);
new Chart(ctx,{type:'bar',data:{labels:data.map(function(d){return d.month;}),datasets:[{data:data.map(function(d){return d.revenue;}),backgroundColor:'rgba(249,115,22,0.75)',borderRadius:6,borderSkipped:false}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{color:'#2a3347'},ticks:{color:'#6b7a99',font:{size:10}}},y:{grid:{color:'#2a3347'},ticks:{color:'#6b7a99',font:{size:10},callback:function(v){return v>=1000?Math.round(v/1000)+'k':v;}}}}}});
</script>
@endpush
@endsection
