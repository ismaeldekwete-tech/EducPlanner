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
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('timetable_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('subject_teacher_id')->constrained('subject_teacher')->onDelete('cascade');
            $table->foreignUuid('room_id')->constrained()->onDelete('cascade');

            // Paramètres temporels
            $table->enum('day_of_week', ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi']);
            $table->integer('slot_number'); // 1 à 6 (en fonction des tranches horaires fixes de l'IUC)

            // Workflow de confirmation du professeur
            $table->enum('teacher_status', ['en_attente', 'confirme', 'refuse'])->default('en_attente');
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            // CONTRAINTES STRICTES POUR ÉVITER LES DOUBLONS (L'intelligence de la BDD)
            // 1. Une salle ne peut pas prendre deux cours au même moment le même jour
            $table->unique(['room_id', 'day_of_week', 'slot_number', 'timetable_id'], 'room_slot_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
