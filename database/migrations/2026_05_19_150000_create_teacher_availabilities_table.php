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
        Schema::create('teacher_availabilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('teacher_id')->constrained('users')->onDelete('cascade');
            
            // Plage de disponibilité
            $table->enum('day_of_week', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']);
            $table->string('slot_number', 50); // Mappe avec les créneaux fixes (ex: '08:00 - 09:50', etc.)
            
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Eviter les doublons de déclaration de disponibilité pour un prof sur un même créneau
            $table->unique(['teacher_id', 'day_of_week', 'slot_number'], 'teacher_avail_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_availabilities');
    }
};
