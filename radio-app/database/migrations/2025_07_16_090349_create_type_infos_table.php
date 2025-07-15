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
        Schema::create('type_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->text('titre')->nullable();
            $table->text('contenu')->nullable();
            $table->string('image')->nullable(); // image optionnelle
            $table->string('numero', 20)->nullable(); // numéro de téléphone
            $table->string('email')->nullable(); // email
            $table->string('lien')->nullable(); // lien externe
            $table->integer('ordre')->nullable(); // pour ordonner les éléments
            $table->boolean('is_active')->default(true); // actif ou non
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_infos');
    }
};
