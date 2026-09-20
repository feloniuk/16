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
        Schema::create('medical_staff', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('position');
            $table->string('employment_status')->nullable();
            $table->string('position_status')->nullable();
            $table->foreignId('ambulatoriya_id')->nullable()->constrained('ambulatorii')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_staff');
    }
};
