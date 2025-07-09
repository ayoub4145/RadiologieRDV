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
        Schema::create('visiteur', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Assuming name is optional
            $table->string('email')->unique()->nullable();
            $table->string('telephone', 20)->nullable(); // Assuming telephone is a string
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visiteur');
    }
};
