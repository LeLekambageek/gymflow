@extends('layouts.owner')
@section('title', 'Abonnements')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">ABONNEMENTS</div>
        <div class="page-subtitle">Vue par plan tarifaire</div>
    </div>
    <a href="{{ route('owner.dashboard') }}" class="btn btn-secondary btn-sm">← Retour</a>
</div>

@foreach($plans as $plan)
@php $actives=$plan->subscriptions->where('status','active'); @endphp
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <div>
            <div class="fw-600" style="font-size:15px">{{ $plan->name }}</div>
            <div class="text-sm text-muted">{{ number_format($plan->price,0,',',' ') }} FCFA · {{ $plan->duration_days }}j</div>
        </div>
        <div style="text-align:right">
            <div style="font-family:var(--font-num);font-size:32px;color:var(--orange);line-height:1">{{ $actives->count() }}</div>
            <div class="text-sm text-muted">actifs</div>
        </div>
    </div>

    @if($actives->count()>0)
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($actives->sortBy('end_date') as $sub)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;background:var(--bg3);border-radius:8px">
            <div style="display:flex;align-items:center;gap:8px">
                <div class="avatar" style="width:28px;height:28px;font-size:10px">
                    {{ strtoupper(substr($sub->member->first_name,0,1).substr($sub->member->last_name,0,1)) }}
                </div>
                <div>
                    <div class="fw-500" style="font-size:13px">{{ $sub->member->full_name }}</div>
                    <div class="text-sm text-muted">{{ $sub->member->phone }}</div>
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0;margin-left:8px">
                <span class="badge {{ $sub->days_remaining<=7?'badge-warning':'badge-active' }}">{{ $sub->days_remaining }}j</span>
                <div class="text-sm text-muted" style="margin-top:2px">{{ $sub->end_date->format('d/m/Y') }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-sm text-muted">Aucun membre actif sur ce plan</div>
    @endif
</div>
@endforeach

@if($expiringSoon->count()>0)
<div class="card" style="border-color:rgba(239,68,68,0.3)">
    <div class="card-title" style="color:var(--red)">⚠ À renouveler dans 7 jours ({{ $expiringSoon->count() }})</div>
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($expiringSoon as $sub)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--bg3);border-radius:8px;border-left:3px solid var(--red)">
            <div>
                <div class="fw-500" style="font-size:13px">{{ $sub->member->full_name }}</div>
                <div class="text-sm text-muted">{{ $sub->member->phone }} · {{ $sub->plan?->name ?? 'Plan supprimé' }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;margin-left:8px">
                <div class="text-red fw-600">{{ $sub->end_date->format('d/m/Y') }}</div>
                <div class="text-sm text-muted">{{ $sub->days_remaining }}j</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
