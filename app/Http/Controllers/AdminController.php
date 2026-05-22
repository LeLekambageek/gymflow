<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Affiche la page de gestion admin
     */
    public function index()
    {
        $users = User::all();
        return view('admin.dashboard', compact('users'));
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur (propriétaire notamment)
     */
    public function resetPassword(User $user, Request $request)
    {
        // Vérifier que seul un admin peut faire ça
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Valider la nouvelle donnée
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Réinitialiser le mot de passe
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès pour ' . $user->name
        ]);
    }

    /**
     * Modifie les identifiants d'un utilisateur
     */
    public function updateCredentials(User $user, Request $request)
    {
        // Vérifier les droits admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Non autorisé');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:owner,manager,admin',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        return back()->with('success', 'Identifiants de ' . $user->name . ' mis à jour');
    }

    /**
     * Affiche les détails d'un utilisateur
     */
    public function show(User $user)
    {
        return view('admin.user-detail', compact('user'));
    }
}
