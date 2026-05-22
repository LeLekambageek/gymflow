<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Crée un compte administrateur pour accéder à la gestion des mots de passe';

    public function handle()
    {
        $this->info('🔐 Création d\'un compte administrateur');
        $this->newLine();

        // Vérifier s'il existe déjà un admin
        if (User::where('role', 'admin')->exists()) {
            $this->warn('⚠️  Un compte admin existe déjà!');
            return;
        }

        $name = $this->ask('Nom de l\'administrateur', 'Admin Gym');
        $email = $this->ask('Email de l\'administrateur');

        // Valider l'email
        while (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email invalide!');
            $email = $this->ask('Email de l\'administrateur');
        }

        // Vérifier que l'email n'existe pas
        if (User::where('email', $email)->exists()) {
            $this->error('Cet email existe déjà!');
            return;
        }

        $password = $this->secret('Mot de passe (minimum 8 caractères)');

        while (strlen($password) < 8) {
            $this->error('Le mot de passe doit contenir au moins 8 caractères!');
            $password = $this->secret('Mot de passe');
        }

        // Créer l'utilisateur admin
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info('✅ Compte administrateur créé avec succès!');
        $this->info('Email: ' . $email);
        $this->newLine();
        $this->info('Vous pouvez maintenant vous connecter et gérer les comptes utilisateurs.');
    }
}
