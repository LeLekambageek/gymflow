@extends('layouts.owner')
@section('title', 'Paiements')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">PAIEMENTS</div>
        <div class="page-subtitle">Suivi des encaissements</div>
    </div>
    <button onclick="openModal('addPaymentModal')" class="btn btn-primary btn-sm">+ Ajouter</button>
</div>

<div class="stats-grid" style="grid-template-columns:1fr 1fr;margin-bottom:14px">
    <div class="stat-card" style="--accent:var(--green)">
        <div class="stat-label">Ce mois</div>
        <div class="stat-value" style="font-size:22px">{{ number_format($monthlyRevenue,0,',',' ') }}</div>
        <div class="stat-note">FCFA</div>
    </div>
    <div class="stat-card" style="--accent:var(--orange)">
        <div class="stat-label">Filtre actuel</div>
        <div class="stat-value" style="font-size:22px">{{ number_format($totalPaid,0,',',' ') }}</div>
        <div class="stat-note">FCFA affiché</div>
    </div>
</div>

<form method="GET" style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        <input type="month" name="month" value="{{ request('month',now()->format('Y-m')) }}">
        <select name="method">
            <option value="">Toutes méthodes</option>
            <option value="cash" {{ request('method')==='cash'?'selected':'' }}>💵 Espèces</option>
            <option value="mobile" {{ request('method')==='mobile'?'selected':'' }}>📱 Mobile</option>
            <option value="card" {{ request('method')==='card'?'selected':'' }}>💳 Carte</option>
            <option value="transfer" {{ request('method')==='transfer'?'selected':'' }}>🏦 Virement</option>
        </select>
    </div>
    <button type="submit" class="btn btn-secondary btn-full">Filtrer</button>
</form>

<div style="display:flex;flex-direction:column;gap:8px">
    @forelse($payments as $p)
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:12px 14px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:6px">
            <div style="display:flex;align-items:center;gap:9px">
                <div class="avatar" style="width:30px;height:30px;font-size:11px">
                    {{ strtoupper(substr($p->member?->first_name??'?',0,1).substr($p->member?->last_name??'?',0,1)) }}
                </div>
                <div>
                    <div class="fw-500" style="font-size:13px">{{ $p->member?->full_name??'N/A' }}</div>
                    <div class="text-sm text-muted">{{ $p->payment_date->format('d/m/Y') }}</div>
                </div>
            </div>
            <div class="fw-600 text-green mono" style="font-size:15px;flex-shrink:0;margin-left:8px">
                +{{ number_format($p->amount,0,',',' ') }} F
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:11px">
            <span class="tag">{{ $p->type }}</span>
            <span class="text-muted">{{ ['cash'=>'💵 Espèces','mobile'=>'📱 Mobile','card'=>'💳 Carte','transfer'=>'🏦 Virement'][$p->method]??$p->method }}</span>
            <span class="badge badge-{{ $p->status }}">{{ $p->status }}</span>
        </div>
    </div>
    @empty
    <div class="empty-state">Aucun paiement trouvé</div>
    @endforelse
</div>
<div style="margin-top:14px">{{ $payments->links() }}</div>

<div id="addPaymentModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">NOUVEAU PAIEMENT</div>
            <button class="modal-close" onclick="closeModal('addPaymentModal')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('payments.store') }}">
                @csrf
                <div class="form-group"><label>Membre *</label>
                    <select name="member_id" required>
                        <option value="">Choisir…</option>
                        @foreach(\App\Models\Member::orderBy('last_name')->get() as $m)
                        <option value="{{ $m->id }}">{{ $m->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label>Montant (FCFA) *</label><input type="number" name="amount" min="0" step="500" required></div>
                    <div class="form-group"><label>Date *</label><input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label>Type *</label>
                        <select name="type" required>
                            <option value="subscription">Abonnement</option>
                            <option value="course">Cours</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Méthode *</label>
                        <select name="method" required>
                            <option value="cash">💵 Espèces</option>
                            <option value="mobile">📱 Mobile</option>
                            <option value="card">💳 Carte</option>
                            <option value="transfer">🏦 Virement</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Enregistrer</button>
            </form>
        </div>
    </div>
</div>
@endsection
