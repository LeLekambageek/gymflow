@extends('layouts.owner')
@section('title', 'Coachs')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">COACHS</div>
        <div class="page-subtitle">{{ $coaches->count() }} membres du staff</div>
    </div>
    <button onclick="openModal('addCoachModal')" class="btn btn-primary btn-sm">+ Ajouter</button>
</div>

<div style="display:flex;flex-direction:column;gap:10px">
    @forelse($coaches as $coach)
    <div class="card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
            <div style="display:flex;align-items:center;gap:12px">
                <div class="avatar" style="width:44px;height:44px;font-size:15px">
                    {{ strtoupper(substr($coach->first_name,0,1).substr($coach->last_name,0,1)) }}
                </div>
                <div>
                    <div class="fw-600" style="font-size:14px">{{ $coach->full_name }}</div>
                    <div class="text-sm text-muted">{{ $coach->speciality??'Spécialité non définie' }}</div>
                </div>
            </div>
            <span class="badge badge-{{ $coach->status }}">{{ $coach->status }}</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:12px;margin-bottom:12px">
            <div><span class="text-muted">Tél : </span>{{ $coach->phone??'—' }}</div>
            <div><span class="text-muted">Cours : </span>{{ $coach->courses_count }}</div>
            @if($coach->hourly_rate)
            <div style="grid-column:1/-1"><span class="text-muted">Taux : </span><span class="fw-500 text-orange">{{ number_format($coach->hourly_rate,0,',',' ') }} FCFA/h</span></div>
            @endif
        </div>
        <div style="display:flex;gap:8px">
            <button onclick="openEditModal({{ $coach->id }},'{{ addslashes($coach->first_name) }}','{{ addslashes($coach->last_name) }}','{{ $coach->email }}','{{ $coach->phone }}','{{ $coach->speciality }}','{{ $coach->hourly_rate }}','{{ $coach->status }}')"
                class="btn btn-secondary btn-sm" style="flex:1">Modifier</button>
            <form method="POST" action="{{ route('coaches.destroy',$coach) }}" onsubmit="return confirm('Supprimer ?')" style="flex:0">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Supp.</button>
            </form>
        </div>
    </div>
    @empty
    <div class="empty-state">Aucun coach enregistré</div>
    @endforelse
</div>

<div id="addCoachModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">NOUVEAU COACH</div>
            <button class="modal-close" onclick="closeModal('addCoachModal')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('coaches.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group"><label>Prénom *</label><input type="text" name="first_name" required></div>
                    <div class="form-group"><label>Nom *</label><input type="text" name="last_name" required></div>
                </div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
                <div class="form-grid">
                    <div class="form-group"><label>Téléphone</label><input type="tel" name="phone"></div>
                    <div class="form-group"><label>Spécialité</label><input type="text" name="speciality"></div>
                </div>
                <div class="form-group"><label>Taux horaire (FCFA)</label><input type="number" name="hourly_rate" min="0"></div>
                <div class="form-group"><label>Bio</label><textarea name="bio" rows="2"></textarea></div>
                <button type="submit" class="btn btn-primary btn-full">Ajouter le coach</button>
            </form>
        </div>
    </div>
</div>

<div id="editCoachModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">MODIFIER COACH</div>
            <button class="modal-close" onclick="closeModal('editCoachModal')">✕</button>
        </div>
        <div class="modal-body">
            <form id="editCoachForm" method="POST">
                @csrf @method('PUT')
                <div class="form-grid">
                    <div class="form-group"><label>Prénom *</label><input type="text" name="first_name" id="e_fn" required></div>
                    <div class="form-group"><label>Nom *</label><input type="text" name="last_name" id="e_ln" required></div>
                </div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" id="e_email" required></div>
                <div class="form-grid">
                    <div class="form-group"><label>Téléphone</label><input type="tel" name="phone" id="e_phone"></div>
                    <div class="form-group"><label>Spécialité</label><input type="text" name="speciality" id="e_spec"></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label>Taux horaire</label><input type="number" name="hourly_rate" id="e_rate" min="0"></div>
                    <div class="form-group"><label>Statut</label>
                        <select name="status" id="e_status">
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openEditModal(id,fn,ln,email,phone,spec,rate,status){
    document.getElementById('editCoachForm').action='/coaches/'+id;
    document.getElementById('e_fn').value=fn;
    document.getElementById('e_ln').value=ln;
    document.getElementById('e_email').value=email;
    document.getElementById('e_phone').value=phone;
    document.getElementById('e_spec').value=spec;
    document.getElementById('e_rate').value=rate;
    document.getElementById('e_status').value=status;
    openModal('editCoachModal');
}
</script>
@endpush
@endsection
