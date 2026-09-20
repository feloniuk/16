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
        Schema::create('cabinet_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabinet_id')->constrained('cabinets')->cascadeOnDelete();
            $table->unsignedTinyInteger('shift');
            $table->foreignId('doctor_id')->nullable()->constrained('medical_staff')->nullOnDelete();
            $table->foreignId('nurse_id')->nullable()->constrained('medical_staff')->nullOnDelete();
            $table->timestamps();

            $table->unique(['cabinet_id', 'shift']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabinet_shifts');
    }
};
