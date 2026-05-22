@extends('layouts.owner')
@section('title', $member->full_name)

@section('content')
{{-- HEADER MEMBRE --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;flex-wrap:wrap">
    <div class="avatar" style="width:52px;height:52px;font-size:18px;flex-shrink:0">
        {{ strtoupper(substr($member->first_name,0,1).substr($member->last_name,0,1)) }}
    </div>
    <div style="flex:1;min-width:0">
        <div class="page-title" style="font-size:22px">{{ strtoupper($member->full_name) }}</div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:4px">
            <span class="badge badge-{{ $member->status }}">{{ $member->status }}</span>
            @if($member->age)<span class="text-muted text-sm">{{ $member->age }} ans</span>@endif
            <span class="text-muted text-sm">Inscrit {{ $member->created_at->format('d/m/Y') }}</span>
        </div>
    </div>
    <div style="display:flex;gap:8px;width:100%">
        <a href="{{ route('members.edit',$member) }}" class="btn btn-secondary btn-sm" style="flex:1;text-align:center">Modifier</a>
        <form method="POST" action="{{ route('members.destroy',$member) }}" onsubmit="return confirm('Supprimer ?')" style="flex:1">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm btn-full">Supprimer</button>
        </form>
    </div>
</div>

{{-- INFOS CONTACT --}}
<div class="card">
    <div class="card-title">Contact</div>
    <div style="display:flex;flex-direction:column;gap:8px;font-size:13px">
        <div style="display:flex;justify-content:space-between"><span class="text-muted">Email</span><span class="mono" style="font-size:12px">{{ $member->email }}</span></div>
        <div style="display:flex;justify-content:space-between"><span class="text-muted">Téléphone</span><span>{{ $member->phone??'—' }}</span></div>
        <div style="display:flex;justify-content:space-between"><span class="text-muted">Adresse</span><span>{{ $member->address??'—' }}</span></div>
        @if($member->emergency_contact)
        <div class="divider" style="margin:4px 0"></div>
        <div style="display:flex;justify-content:space-between"><span class="text-muted">Urgence</span><span>{{ $member->emergency_contact }}</span></div>
        <div style="display:flex;justify-content:space-between"><span class="text-muted">Tél. urgence</span><span>{{ $member->emergency_phone }}</span></div>
        @endif
    </div>
</div>

{{-- ABONNEMENT ACTUEL --}}
@php $activeSub=$member->subscriptions->where('status','active')->first(); @endphp
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <div class="card-title" style="margin:0">Abonnement actuel</div>
        <button onclick="openModal('addSubModal')" class="btn btn-secondary btn-sm">+ Ajouter</button>
    </div>
    @if($activeSub && $activeSub->plan)
    <div style="padding:14px;background:rgba(249,115,22,0.08);border:1px solid rgba(249,115,22,0.2);border-radius:8px">
        <div class="fw-600 text-orange" style="font-size:15px">{{ $activeSub->plan->name }}</div>
        <div class="text-sm text-muted" style="margin-top:3px">Du {{ $activeSub->start_date->format('d/m/Y') }} au {{ $activeSub->end_date->format('d/m/Y') }}</div>
        <div style="margin-top:10px">
            @if($activeSub->plan)
            <div class="progress"><div class="progress-bar" style="width:{{ min(100,max(0,(1-$activeSub->days_remaining/$activeSub->plan->duration_days)*100)) }}%"></div></div>
            @endif
            <div class="text-sm text-muted mt-4">{{ (int)$activeSub->days_remaining }} jours restants</div>
        </div>
    </div>
    @else
    <div class="text-sm text-muted">Aucun abonnement actif</div>
    @endif
</div>

{{-- HISTORIQUE ABONNEMENTS --}}
<div class="card">
    <div class="card-title">Historique abonnements</div>
    @forelse($member->subscriptions->sortByDesc('start_date') as $sub)
    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border)">
        <div>
            <div class="fw-500" style="font-size:13px">{{ $sub->plan?->name ?? 'Plan supprimé' }}</div>
            <div class="text-sm text-muted">{{ $sub->start_date->format('d/m') }} → {{ $sub->end_date->format('d/m/Y') }}</div>
        </div>
        <div style="text-align:right;flex-shrink:0;margin-left:8px">
            <div class="fw-500 mono" style="font-size:13px">{{ number_format($sub->amount_paid,0,',',' ') }} F</div>
            <span class="badge badge-{{ $sub->status }}">{{ $sub->status }}</span>
        </div>
    </div>
    @empty
    <div class="text-sm text-muted">Aucun historique</div>
    @endforelse
</div>

{{-- HISTORIQUE PAIEMENTS --}}
<div class="card">
    <div class="card-title">Paiements</div>
    @forelse($member->payments->sortByDesc('payment_date') as $payment)
    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border)">
        <div>
            <div style="font-size:13px">{{ $payment->payment_date->format('d/m/Y') }} · {{ $payment->method }}</div>
            <span class="tag">{{ $payment->type }}</span>
        </div>
        <div style="text-align:right;flex-shrink:0;margin-left:8px">
            <div class="fw-500 text-green mono">{{ number_format($payment->amount,0,',',' ') }} F</div>
            <span class="badge badge-{{ $payment->status }}">{{ $payment->status }}</span>
        </div>
    </div>
    @empty
    <div class="text-sm text-muted">Aucun paiement</div>
    @endforelse
</div>

{{-- MODAL: Ajouter abonnement --}}
@php $plans=\App\Models\SubscriptionPlan::where('is_active',true)->get(); @endphp
<div id="addSubModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">NOUVEL ABONNEMENT</div>
            <button class="modal-close" onclick="closeModal('addSubModal')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('subscriptions.store') }}">
                @csrf
                <input type="hidden" name="member_id" value="{{ $member->id }}">
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
