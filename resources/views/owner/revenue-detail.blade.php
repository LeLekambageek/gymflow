@extends('layouts.owner')
@section('title', 'Détail revenus')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">REVENUS</div>
        <div class="page-subtitle">{{ $label }}</div>
    </div>
    <a href="{{ route('owner.dashboard') }}" class="btn btn-secondary btn-sm">← Retour</a>
</div>

{{-- TOTAL + NAVIGATION --}}
<div class="stat-card" style="--accent:var(--orange);margin-bottom:14px">
    <div class="stat-label">Total encaissé</div>
    <div class="stat-value">{{ number_format($total,0,',',' ') }}</div>
    <div class="stat-note">FCFA sur la période</div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:14px">
    @foreach(['today'=>"Aujourd'hui",'week'=>'Semaine','month'=>'Ce mois','year'=>'Cette année'] as $p=>$lbl)
    <a href="{{ route('owner.revenue.detail',['period'=>$p]) }}"
        class="btn {{ $period===$p?'btn-primary':'btn-secondary' }} btn-sm" style="font-size:12px">
        {{ $lbl }}
    </a>
    @endforeach
</div>

{{-- RÉPARTITION PAR PLAN --}}
@if($byPlan->count()>0)
<div style="display:flex;flex-direction:column;gap:10px;margin-bottom:14px">
    @foreach($byPlan as $group)
    <div class="card" style="border-left:3px solid var(--orange);padding:12px 14px">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
            <div class="fw-600" style="font-size:13px">{{ $group['name'] }}</div>
            <div class="fw-600 text-orange mono" style="font-size:15px">{{ number_format($group['total'],0,',',' ') }} F</div>
        </div>
        <div class="text-sm text-muted">{{ $group['count'] }} paiement(s) · {{ $group['members']->count() }} client(s)</div>
        <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:8px">
            @foreach($group['members']->take(5) as $m)
            <div title="{{ $m->full_name }}" style="width:28px;height:28px;border-radius:50%;background:var(--bg3);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;color:var(--orange)">
                {{ strtoupper(substr($m->first_name,0,1).substr($m->last_name,0,1)) }}
            </div>
            @endforeach
            @if($group['members']->count()>5)
            <div style="width:28px;height:28px;border-radius:50%;background:var(--bg3);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:10px;color:var(--muted)">
                +{{ $group['members']->count()-5 }}
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- LISTE DÉTAILLÉE EN CARTES --}}
<div class="card">
    <div class="card-title">Tous les paiements ({{ $payments->count() }})</div>
    @forelse($payments as $p)
    <div style="padding:10px 0;border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
            <div style="display:flex;align-items:center;gap:8px">
                <div class="avatar" style="width:28px;height:28px;font-size:10px;color:var(--orange)">
                    {{ strtoupper(substr($p->member?->first_name??'?',0,1).substr($p->member?->last_name??'?',0,1)) }}
                </div>
                <div class="fw-500" style="font-size:13px">{{ $p->member?->full_name??'N/A' }}</div>
            </div>
            <div class="fw-500 text-green mono" style="font-size:14px">+{{ number_format($p->amount,0,',',' ') }} F</div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);padding-left:36px">
            <span>{{ $p->subscription?->plan?->name ?? $p->type }}</span>
            <span>{{ ['cash'=>'💵 Espèces','mobile'=>'📱 Mobile','card'=>'💳 Carte','transfer'=>'🏦 Virement'][$p->method]??$p->method }}</span>
        </div>
    </div>
    @empty
    <div class="empty-state">Aucun paiement sur cette période</div>
    @endforelse

    @if($payments->count()>0)
    <div style="display:flex;justify-content:space-between;padding:12px 0;font-size:14px;font-weight:600;border-top:2px solid var(--border);margin-top:4px">
        <span>TOTAL</span>
        <span class="text-orange mono">{{ number_format($total,0,',',' ') }} FCFA</span>
    </div>
    @endif
</div>
@endsection
