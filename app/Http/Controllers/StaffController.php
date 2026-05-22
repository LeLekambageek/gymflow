<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    /**
     * Liste de tous les gérants
     */
    public function index()
    {
        $managers = User::where('role', 'manager')->latest()->get();
        return view('owner.staff', compact('managers'));
    }

    /**
     * Ajouter un nouveau gérant
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'manager',
        ]);

        return back()->with('success', "Compte gérant créé pour {$validated['name']}.");
    }

    /**
     * Modifier le mot de passe d'un gérant
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Mot de passe de {$user->name} modifié.");
    }

    /**
     * Modifier le nom / email d'un gérant
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => "required|email|unique:users,email,{$user->id}",
        ]);

        $user->update($validated);

        return back()->with('success', "Informations de {$user->name} mises à jour.");
    }

    /**
     * Supprimer un gérant
     */
    public function destroy(User $user)
    {
        if ($user->role === 'owner') {
            return back()->with('error', 'Impossible de supprimer le propriétaire.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Compte de {$name} supprimé.");
    }
}
