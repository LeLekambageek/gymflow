@extends('layouts.owner')
@section('title', 'Modifier membre')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">MODIFIER</div>
        <div class="page-subtitle">{{ $member->full_name }}</div>
    </div>
    <a href="{{ route('members.show',$member) }}" class="btn btn-secondary btn-sm">← Retour</a>
</div>

<form method="POST" action="{{ route('members.update',$member) }}">
@csrf @method('PUT')

<div class="card">
    <div class="card-title">Informations personnelles</div>
    <div class="form-grid">
        <div class="form-group"><label>Prénom *</label><input type="text" name="first_name" value="{{ old('first_name',$member->first_name) }}" required></div>
        <div class="form-group"><label>Nom *</label><input type="text" name="last_name" value="{{ old('last_name',$member->last_name) }}" required></div>
    </div>
    <div class="form-group"><label>Email *</label><input type="email" name="email" value="{{ old('email',$member->email) }}" required></div>
    <div class="form-group"><label>Téléphone</label><input type="tel" name="phone" value="{{ old('phone',$member->phone) }}"></div>
    <div class="form-grid">
        <div class="form-group"><label>Date de naissance</label><input type="date" name="birth_date" value="{{ old('birth_date',$member->birth_date?->format('Y-m-d')) }}"></div>
        <div class="form-group"><label>Statut *</label>
            <select name="status" required>
                <option value="active" {{ old('status',$member->status)==='active'?'selected':'' }}>Actif</option>
                <option value="inactive" {{ old('status',$member->status)==='inactive'?'selected':'' }}>Inactif</option>
                <option value="suspended" {{ old('status',$member->status)==='suspended'?'selected':'' }}>Suspendu</option>
            </select>
        </div>
    </div>
    <div class="form-group"><label>Adresse</label><input type="text" name="address" value="{{ old('address',$member->address) }}"></div>
</div>

<div class="card">
    <div class="card-title">Contact d'urgence</div>
    <div class="form-grid">
        <div class="form-group"><label>Nom</label><input type="text" name="emergency_contact" value="{{ old('emergency_contact',$member->emergency_contact) }}"></div>
        <div class="form-group"><label>Téléphone</label><input type="tel" name="emergency_phone" value="{{ old('emergency_phone',$member->emergency_phone) }}"></div>
    </div>
</div>

<div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px">
    <button type="submit" class="btn btn-primary btn-full" style="font-size:15px;padding:13px">Enregistrer les modifications</button>
    <a href="{{ route('members.show',$member) }}" class="btn btn-secondary btn-full" style="text-align:center">Annuler</a>
</div>
</form>
@endsection
