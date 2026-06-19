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
        Schema::create('subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('semester');

            // Quotas stockés en MINUTES pour éviter les approximations (ex: 30h = 1800 minutes)
            $table->integer('quota_cm_minutes')->default(0);
            $table->integer('quota_td_minutes')->default(0);
            $table->integer('quota_tp_minutes')->default(0);
            $table->integer('quota_total_remaining_minutes')->default(0);

            $table->foreignUuid('department_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
