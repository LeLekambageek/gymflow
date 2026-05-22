@extends('layouts.owner')
@section('title', 'Plans tarifaires')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">PLANS TARIFAIRES</div>
        <div class="page-subtitle">{{ $plans->count() }} plans configurés</div>
    </div>
    <button onclick="openModal('addPlanModal')" class="btn btn-primary btn-sm">+ Nouveau plan</button>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
    @forelse($plans as $plan)
    <div style="background:var(--bg2);border:2px solid {{ $plan->is_active?'var(--orange)':'var(--border)' }};border-radius:12px;padding:14px;opacity:{{ $plan->is_active?1:0.6 }}">
        <div style="font-family:var(--font-num);font-size:28px;color:var(--orange);line-height:1;margin-bottom:4px">
            {{ number_format($plan->price,0,',',' ') }}<span style="font-size:13px;color:var(--muted);font-family:var(--font)"> F</span>
        </div>
        <div class="fw-600" style="font-size:13px;margin-bottom:3px">{{ $plan->name }}</div>
        <div class="text-sm text-muted">{{ $plan->duration_days }} jour(s)</div>
        <div class="divider" style="margin:10px 0"></div>
        <div style="display:flex;justify-content:space-between;font-size:12px">
            <span class="text-muted">{{ $plan->subscriptions_count }} membres</span>
            <span class="badge {{ $plan->is_active?'badge-active':'badge-inactive' }}">{{ $plan->is_active?'Actif':'Inactif' }}</span>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1">
        <div class="empty-state">Aucun plan créé.</div>
    </div>
    @endforelse
</div>

<div id="addPlanModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">NOUVEAU PLAN</div>
            <button class="modal-close" onclick="closeModal('addPlanModal')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('subscriptions.plans.store') }}">
                @csrf
                <div class="form-group"><label>Nom *</label><input type="text" name="name" placeholder="Ex: Mensuel Standard" required></div>
                <div class="form-grid">
                    <div class="form-group"><label>Prix (FCFA) *</label><input type="number" name="price" min="0" step="500" required></div>
                    <div class="form-group"><label>Durée (jours) *</label><input type="number" name="duration_days" min="1" value="30" required></div>
                </div>
                <div class="form-group"><label>Description</label><textarea name="description" rows="2"></textarea></div>
                <button type="submit" class="btn btn-primary btn-full">Créer le plan</button>
            </form>
        </div>
    </div>
</div>
@endsection
