@extends('layouts.admin')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">🔐 Gestion Administrateur</div>
        <div class="page-subtitle">Gestion des utilisateurs et réinitialisation de mots de passe</div>
    </div>
</div>

<div class="card">
    <div class="card-title">Liste des utilisateurs</div>

    @if($users->isEmpty())
        <p class="text-muted">Aucun utilisateur trouvé.</p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td class="text-sm">{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ strtolower($user->role) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <button onclick="showResetPasswordModal({{ $user->id }}, '{{ $user->name }}')"
                                        class="btn btn-sm btn-primary">
                                    🔑 Reset
                                </button>
                                <button onclick="showEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')"
                                        class="btn btn-sm btn-secondary">
                                    ✏️ Modifier
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal Réinitialiser Mot de Passe -->
<div id="resetPasswordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">🔐 Réinitialiser mot de passe</h2>
            <button type="button" onclick="closeResetPasswordModal()" class="modal-close">×</button>
        </div>
        <div class="modal-body">
            <p class="text-muted" style="margin-bottom: 14px;">
                Utilisateur: <strong id="resetUserName"></strong>
            </p>

            <form id="resetPasswordForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="new_password" required minlength="8"
                           placeholder="Minimum 8 caractères">
                </div>

                <div class="form-group">
                    <label>Confirmer mot de passe</label>
                    <input type="password" name="new_password_confirmation" required minlength="8"
                           placeholder="Répétez le mot de passe">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Réinitialiser
                    </button>
                    <button type="button" onclick="closeResetPasswordModal()"
                            class="btn btn-secondary" style="flex: 1;">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Identifiants -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">✏️ Modifier les identifiants</h2>
            <button type="button" onclick="closeEditModal()" class="modal-close">×</button>
        </div>
        <div class="modal-body">
            <form id="editForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="name" id="editName" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="editEmail" required>
                </div>

                <div class="form-group">
                    <label>Rôle</label>
                    <select name="role" id="editRole" required>
                        <option value="manager">Manager</option>
                        <option value="owner">Owner</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Mettre à jour
                    </button>
                    <button type="button" onclick="closeEditModal()"
                            class="btn btn-secondary" style="flex: 1;">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showResetPasswordModal(userId, userName) {
    document.getElementById('resetUserName').textContent = userName;
    document.getElementById('resetPasswordForm').action = `/admin/users/${userId}/reset-password`;
    document.getElementById('resetPasswordModal').classList.add('open');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.remove('open');
}

function showEditModal(userId, name, email, role) {
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = role;
    document.getElementById('editForm').action = `/admin/users/${userId}/credentials`;
    document.getElementById('editModal').classList.add('open');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}

// Fermer les modals en cliquant en dehors
document.addEventListener('click', function(event) {
    if (event.target.id === 'resetPasswordModal') {
        closeResetPasswordModal();
    }
    if (event.target.id === 'editModal') {
        closeEditModal();
    }
});

// Gérer la soumission du formulaire reset password
document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const response = await fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    });
    const data = await response.json();
    if (data.success) {
        alert(data.message);
        closeResetPasswordModal();
        location.reload();
    } else {
        alert('Erreur: ' + (data.error || 'Une erreur est survenue'));
    }
});

// Gérer la soumission du formulaire edit
document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const response = await fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    });
    if (response.ok) {
        alert('Utilisateur mis à jour avec succès');
        closeEditModal();
        location.reload();
    } else {
        alert('Erreur lors de la mise à jour');
    }
});
</script>
@endsection
