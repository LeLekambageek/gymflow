<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modifier l'enum pour ajouter le rôle 'admin'
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'owner', 'manager'])->default('manager')->change();
        });
    }

    public function down(): void
    {
        // Retirer le rôle admin
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['owner', 'manager'])->default('manager')->change();
        });
    }
};
