<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create';
    protected $description = '✨ Crée un compte administrateur pour accéder à la gestion des mots de passe et des utilisateurs';

    /**
     * Exécute la commande de création d'admin
     * 
     * Utilisation:
     * $ php artisan admin:create
     * 
     * Processus:
     * 1. Vérifier qu'il n'existe pas déjà un admin
     * 2. Demander le nom et email à l'utilisateur
     * 3. Valider que l'email est correct et unique
     * 4. Demander un mot de passe fort (min 8 caractères)
     * 5. Créer l'utilisateur dans la base de données
     */
    public function handle()
    {
        $this->info('🔐 === Création d\'un compte administrateur === ');
        $this->info('Cette personne pourra gérer les mots de passe de tous les utilisateurs');
        $this->newLine();

        
        if (User::where('role', 'admin')->exists()) {
            $this->warn('⚠️  Un compte admin existe déjà! Vous pouvez en créer d\'autres si vous voulez.');
            if (!$this->confirm('Continuer quand même?')) {
                return;
            }
        }

        
        $name = $this->ask('📝 Quel est le nom de l\'administrateur?', 'Admin Gym');
        
        
        $email = $this->ask('📧 Quel est son email?');
        while (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Cet email n\'est pas valide!');
            $email = $this->ask('📧 Essayez encore avec un email correct');
        }

       
        if (User::where('email', $email)->exists()) {
            $this->error('❌ Cet email existe déjà dans le système!');
            return;
        }

    
        $password = $this->secret('🔑 Choisissez un mot de passe fort (minimum 8 caractères)');

        while (strlen($password) < 8) {
            $this->error('❌ Le mot de passe doit faire au minimum 8 caractères!');
            $password = $this->secret('🔑 Réessayez avec un mot de passe plus long');
        }

        
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->info('✅ Compte administrateur créé avec succès!');
        $this->info('📧 Email: ' . $email);
        $this->newLine();
        $this->info('🎉 Vous pouvez maintenant vous connecter et gérer les comptes utilisateurs.');
    }
}
