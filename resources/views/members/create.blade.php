@extends('layouts.owner')
@section('title', 'Nouveau membre')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">NOUVEAU MEMBRE</div>
        <div class="page-subtitle">Enregistrement d'un adhérent</div>
    </div>
    <a href="{{ route('members.index') }}" class="btn btn-secondary btn-sm">← Retour</a>
</div>

<form method="POST" action="{{ route('members.store') }}">
@csrf
<div class="card">
    <div class="card-title">Informations personnelles</div>
    <div class="form-grid">
        <div class="form-group"><label>Prénom *</label><input type="text" name="first_name" value="{{ old('first_name') }}" required></div>
        <div class="form-group"><label>Nom *</label><input type="text" name="last_name" value="{{ old('last_name') }}" required></div>
    </div>
    <div class="form-group"><label>Email *</label><input type="email" name="email" value="{{ old('email') }}" required></div>
    <div class="form-group"><label>Téléphone</label><input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+221 77 000 00 00"></div>
    <div class="form-grid">
        <div class="form-group"><label>Date de naissance</label><input type="date" name="birth_date" value="{{ old('birth_date') }}"></div>
        <div class="form-group"><label>Statut *</label>
            <select name="status" required>
                <option value="active" {{ old('status','active')==='active'?'selected':'' }}>Actif</option>
                <option value="inactive" {{ old('status')==='inactive'?'selected':'' }}>Inactif</option>
                <option value="suspended" {{ old('status')==='suspended'?'selected':'' }}>Suspendu</option>
            </select>
        </div>
    </div>
    <div class="form-group"><label>Adresse</label><input type="text" name="address" value="{{ old('address') }}"></div>
</div>

<div class="card">
    <div class="card-title">Contact d'urgence</div>
    <div class="form-grid">
        <div class="form-group"><label>Nom du contact</label><input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}"></div>
        <div class="form-group"><label>Téléphone</label><input type="tel" name="emergency_phone" value="{{ old('emergency_phone') }}"></div>
    </div>
</div>

<div class="card">
    <div class="card-title">Abonnement initial</div>
    <div class="form-group">
        <label>Plan</label>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:6px" id="planGrid">
            <label style="padding:10px 12px;background:var(--bg3);border:2px solid var(--border);border-radius:10px;cursor:pointer" class="plan-lbl">
                <input type="radio" name="plan_id" value="" style="display:none" checked>
                <div style="font-size:13px;font-weight:500;color:var(--muted)">Sans abonnement</div>
            </label>
            @foreach($plans as $plan)
            <label style="padding:10px 12px;background:var(--bg3);border:2px solid var(--border);border-radius:10px;cursor:pointer" class="plan-lbl">
                <input type="radio" name="plan_id" value="{{ $plan->id }}" style="display:none">
                <div style="font-size:18px;font-weight:700;color:var(--orange);line-height:1">{{ number_format($plan->price,0,',',' ') }}<span style="font-size:10px;color:var(--muted);font-weight:400"> F</span></div>
                <div style="font-size:11px;font-weight:500;margin-top:2px">{{ $plan->name }}</div>
                <div style="font-size:10px;color:var(--muted)">{{ $plan->duration_days }}j</div>
            </label>
            @endforeach
        </div>
    </div>
    <div class="form-group" id="paymentGroup" style="display:none">
        <label>Mode de paiement</label>
        <select name="payment_method">
            <option value="cash">💵 Espèces</option>
            <option value="mobile">📱 Mobile Money</option>
            <option value="card">💳 Carte</option>
            <option value="transfer">🏦 Virement</option>
        </select>
    </div>
</div>

<button type="submit" class="btn btn-primary btn-full" style="font-size:15px;padding:13px;margin-bottom:20px">
    Enregistrer le membre
</button>
</form>

@push('scripts')
<script>
document.querySelectorAll('.plan-lbl').forEach(function(lbl){
    lbl.addEventListener('click',function(){
        document.querySelectorAll('.plan-lbl').forEach(function(l){l.style.borderColor='var(--border)';l.style.background='var(--bg3)';});
        this.style.borderColor='var(--orange)';this.style.background='rgba(249,115,22,0.06)';
        var hasVal=this.querySelector('input').value!=='';
        document.getElementById('paymentGroup').style.display=hasVal?'block':'none';
    });
});
</script>
@endpush
@endsection
