@extends('layouts.owner')
@section('title', 'Staff')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">GÉRANTS & STAFF</div>
        <div class="page-subtitle">{{ $managers->count() }} compte(s) gérant</div>
    </div>
    <button onclick="openModal('addManagerModal')" class="btn btn-primary btn-sm">+ Ajouter</button>
</div>

<div style="display:flex;flex-direction:column;gap:10px">
    @forelse($managers as $manager)
    <div class="card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
            <div style="display:flex;align-items:center;gap:10px">
                <div class="avatar" style="width:40px;height:40px;font-size:14px;color:var(--green)">
                    {{ strtoupper(substr($manager->name,0,2)) }}
                </div>
                <div>
                    <div class="fw-600" style="font-size:14px">{{ $manager->name }}</div>
                    <div class="text-sm text-muted mono">{{ $manager->email }}</div>
                    <div class="text-sm text-muted">Inscrit le {{ $manager->created_at->format('d/m/Y') }}</div>
                </div>
            </div>
            <span class="badge badge-active">Actif</span>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <button onclick="openEditModal({{ $manager->id }},'{{ addslashes($manager->name) }}','{{ $manager->email }}')"
                class="btn btn-secondary btn-sm" style="flex:1">Modifier</button>
            <button onclick="openPasswordModal({{ $manager->id }},'{{ addslashes($manager->name) }}')"
                class="btn btn-secondary btn-sm" style="flex:1;color:var(--orange)">Mot de passe</button>
            <form method="POST" action="{{ route('owner.staff.destroy',$manager) }}"
                onsubmit="return confirm('Supprimer {{ $manager->name }} ?')" style="flex:1">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm btn-full">Supprimer</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="empty-state">Aucun gérant. Ajoutez-en un pour commencer.</div>
    </div>
    @endforelse
</div>

{{-- MODAL: Ajouter gérant --}}
<div id="addManagerModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">NOUVEAU GÉRANT</div>
            <button class="modal-close" onclick="closeModal('addManagerModal')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('owner.staff.store') }}">
                @csrf
                <div class="form-group"><label>Nom complet *</label><input type="text" name="name" required placeholder="Prénom Nom"></div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" required placeholder="gerant@gymflow.sn"></div>
                <div class="form-group"><label>Mot de passe * (min. 8 car.)</label><input type="password" name="password" required minlength="8"></div>
                <div class="form-group"><label>Confirmer le mot de passe *</label><input type="password" name="password_confirmation" required minlength="8"></div>
                <button type="submit" class="btn btn-primary btn-full" style="margin-top:4px">Créer le compte</button>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: Modifier gérant --}}
<div id="editManagerModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">MODIFIER GÉRANT</div>
            <button class="modal-close" onclick="closeModal('editManagerModal')">✕</button>
        </div>
        <div class="modal-body">
            <form id="editManagerForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group"><label>Nom complet *</label><input type="text" name="name" id="edit_manager_name" required></div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" id="edit_manager_email" required></div>
                <button type="submit" class="btn btn-primary btn-full">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: Mot de passe --}}
<div id="passwordModal" class="modal-backdrop">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">MOT DE PASSE</div>
            <button class="modal-close" onclick="closeModal('passwordModal')">✕</button>
        </div>
        <div class="modal-body">
            <div id="passwordModalName" class="text-sm text-muted" style="margin-bottom:14px"></div>
            <form id="passwordForm" method="POST">
                @csrf @method('PUT')
                <div class="form-group"><label>Nouveau mot de passe *</label><input type="password" name="password" required minlength="8"></div>
                <div class="form-group"><label>Confirmer *</label><input type="password" name="password_confirmation" required minlength="8"></div>
                <button type="submit" class="btn btn-full" style="background:var(--orange);color:white">Changer le mot de passe</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openEditModal(id,name,email){
    document.getElementById('editManagerForm').action='/owner/staff/'+id;
    document.getElementById('edit_manager_name').value=name;
    document.getElementById('edit_manager_email').value=email;
    openModal('editManagerModal');
}
function openPasswordModal(id,name){
    document.getElementById('passwordForm').action='/owner/staff/'+id+'/password';
    document.getElementById('passwordModalName').textContent='Modifier le mot de passe de : '+name;
    openModal('passwordModal');
}
</script>
@endpush
@endsection
