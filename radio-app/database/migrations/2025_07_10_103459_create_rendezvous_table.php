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
        Schema::create('rendez_vouses', function (Blueprint $table) {
            $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->foreignId('service_id')->constrained('services')->onDelete('cascade');

                // Clé étrangère vers visiteurs(id)
                $table->foreignId('visiteur_id')->nullable()->constrained('visiteur')->onDelete('cascade');
                $table->dateTime('date_heure');
                $table->boolean('is_urgent')->default(false);
                $table->text('resultat')->nullable();
                $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vouses');
    }
};
