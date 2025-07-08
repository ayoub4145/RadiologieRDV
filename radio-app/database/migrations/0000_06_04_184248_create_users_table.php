<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ Assure-toi que la table `roles` est créée avant cette migration
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
             $table->string('two_factor_code')->nullable(); // Pour 2FA
            $table->timestamp('two_factor_expires_at')->nullable(); // Pour l'expiration de 2FA
             // Clé secrète Google 2FA (TOTP)
            $table->string('google2fa_secret')->nullable();

            // Statut activatiaon 2FA (true = activé)
            $table->boolean('two_factor_enabled')->default(false);
            $table->rememberToken();
            $table->timestamps();


        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ⚠️ Supprimer d'abord les tables dépendantes
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
