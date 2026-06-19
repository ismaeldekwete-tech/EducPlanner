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
        Schema::create('classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('filiere');
            $table->enum('regime', ['J', 'S']);
            $table->integer('niveau');
            $table->string('groupe');
            $table->string('code_unique')->unique();

            $table->foreignUuid('department_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('room_id')->nullable()->comment('Salle attitrée pour la semaine');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
