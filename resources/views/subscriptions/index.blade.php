@extends('layouts.owner')
@section('title', 'Abonnements')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">ABONNEMENTS</div>
        <div class="page-subtitle">{{ $subscriptions->total() }} abonnements</div>
    </div>
    <button onclick="openModal('addSubModal')" class="btn btn-primary btn-sm">+ Nouveau</button>
</div>

<form method="GET" style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        <select name="status">
            <option value="">Tous statuts</option>
            <option value="active" {{ request('status')==='active'?'selected':'' }}>Actif</option>
            <option value="expired" {{ request('status')==='expired'?'selected':'' }}>Expiré</option>
            <option value="cancelled" {{ request('status')==='cancelled'?'selected':'' }}>Annulé</option>
        </select>
        <label style="display:flex;align-items:center;gap:6px;font-size:13px;text-transform:none;letter-spacing:0;margin:0;cursor:pointer;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:0 12px">
            <input type="checkbox" name="expiring" value="1" {{ request('expiring')?'checked':'' }} style="width:auto">
            <span style="font-size:12px">Expirent 7j</span>
        </label>
    </div>
    <button type="submit" class="btn btn-secondary btn-full">Filtrer</button>
</form>

<div style="display:flex;flex-direction:column;gap:10px">
    @forelse($subscriptions as $sub)
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:12px 14px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px">
            <div style="display:flex;align-items:center;gap:9px">
                <div class="avatar" style="width:30px;height:30px;font-size:11px">
                    {{ strtoupper(substr($sub->member->first_name,0,1).substr($sub->member->last_name,0,1)) }}
                </div>
                <div>
                    <div class="fw-500" style="font-size:13px">{{ $sub->member->full_name }}</div>
                    <div class="text-sm text-muted">{{ $sub->plan?->name ?? 'Plan supprimé' }}</div>
                </div>
            </div>
            <span class="badge badge-{{ $sub->status }}">{{ $sub->status }}</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:12px;margin-bottom:8px">
            <div><span class="text-muted">Début : </span>{{ $sub->start_date->format('d/m/Y') }}</div>
            <div><span class="text-muted">Fin : </span><span class="{{ $sub->status==='active'&&$sub->days_remaining<=7?'text-orange fw-500':'' }}">{{ $sub->end_date->format('d/m/Y') }}</span></div>
            <div><span class="text-muted">Montant : </span>{{ number_format($sub->amount_paid,0,',',' ') }} F</div>
            @if($sub->status==='active')
            <div><span class="text-muted">Reste : </span>{{ $sub->days_remaining }}j</div>
            @endif
        </div>
        @if($sub->status==='active')
        @if($sub->plan)
        <div class="progress" style="margin-bottom:8px"><div class="progress-bar {{ $sub->days_remaining<=3?'red':($sub->days_remaining<=7?'orange':'') }}" style="width:{{ min(100,max(0,($sub->days_remaining/$sub->plan->duration_days)*100)) }}%"></div></div>
        @endif
        <form method="POST" action="{{ route('subscriptions.cancel',$sub) }}" onsubmit="return confirm('Annuler ?')">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm btn-full" style="font-size:12px">Annuler l'abonnement</button>
        </form>
        @endif
    </div>
    @empty
    <div class="empty-state">Aucun abonnement trouvé</div>
    @endforelse
</div>
<div style="margin-top:14px">{{ $subscriptions->links() }}</div>

<div id="addSubModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">NOUVEL ABONNEMENT</div>
            <button class="modal-close" onclick="closeModal('addSubModal')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('subscriptions.store') }}">
                @csrf
                <div class="form-group"><label>Membre *</label>
                    <select name="member_id" required>
                        <option value="">Choisir…</option>
                        @foreach(\App\Models\Member::orderBy('last_name')->get() as $m)
                        <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>Plan *</label>
                    <select name="subscription_plan_id" required>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }} — {{ number_format($plan->price,0,',',' ') }} FCFA</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>Date de début *</label><input type="date" name="start_date" value="{{ date('Y-m-d') }}" required></div>
                <div class="form-group"><label>Mode de paiement *</label>
                    <select name="payment_method" required>
                        <option value="cash">💵 Espèces</option>
                        <option value="mobile">📱 Mobile Money</option>
                        <option value="card">💳 Carte</option>
                        <option value="transfer">🏦 Virement</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Créer l'abonnement</button>
            </form>
        </div>
    </div>
</div>
@endsection
