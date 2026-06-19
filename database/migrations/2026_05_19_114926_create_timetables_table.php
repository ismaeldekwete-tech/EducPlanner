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
        Schema::create('timetables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('classe_id')->constrained()->onDelete('cascade');
            $table->integer('week_number');
            $table->year('academic_year');
            $table->enum('status', ['brouillon', 'en_attente_validation', 'publie'])->default('brouillon');
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();

            // Une classe ne peut avoir qu'un seul calendrier par semaine donnée
            $table->unique(['classe_id', 'week_number', 'academic_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
