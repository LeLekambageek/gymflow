<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    
    public function index()
    {
        $users = User::all();
        return view('admin.dashboard', compact('users'));
    }

    
    public function resetPassword(User $user, Request $request)
    {
        
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

    
        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès pour ' . $user->name
        ]);
    }

    
    public function updateCredentials(User $user, Request $request)
    {
        // Sécurité: Seul un admin peut modifier les identifiants
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Non autorisé');
        }

        // Valider que les données sont correctes
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,  // unique sauf pour cet utilisateur
            'role' => 'required|in:owner,manager,admin',  // Rôle doit être l'un de ces trois
        ]);

       
        $user->update($request->only(['name', 'email', 'role']));

     
        return back()->with('success', 'Identifiants de ' . $user->name . ' mis à jour');
    }

    /**
     * Affiche les détails complets d'un utilisateur spécifique
     * (pas encore implémenté mais prêt pour plus tard)
     */
    public function show(User $user)
    {
        return view('admin.user-detail', compact('user'));
    }
}
