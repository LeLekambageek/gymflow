<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Les colonnes qu'on peut remplir directement lors de la création/modification
     * (par sécurité, on liste explicitement ce qui peut être modifié)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',             
        'email',        
        'password',         
        'role',             
    ];

    /**
     * Les colonnes sensibles à ne jamais envoyer en réponse
     * (on les cache pour éviter de leaker des données sensibles)
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',      
        'remember_token', 
    ];

    /**
     * Définir comment les colonnes doivent être traitées lors des requêtes
     * (exemple: convertir les dates en objets DateTime)
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',      
        ];
    }
}
