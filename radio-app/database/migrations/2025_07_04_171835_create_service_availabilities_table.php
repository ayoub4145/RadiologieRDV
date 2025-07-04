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
        Schema::create('service_availabilities', function (Blueprint $table) {
            $table->id();
             $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
                $table->string('start_day'); // Exemple : "Monday"
                $table->string('end_day');   // Exemple : "Friday"
                $table->time('start_time');  // Exemple : 09:00
                $table->time('end_time');    // Exemple : 17:00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_availabilities');
    }
};
