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
        Schema::create('cabinets', function (Blueprint $table) {
            $table->id();
            // No FK constraint: branches.id is a legacy `int(11)` (signed) column;
            // this matches the existing unconstrained-reference pattern used for
            // branch_id elsewhere in the app (see work_logs.branch_id).
            $table->unsignedInteger('branch_id');
            $table->index('branch_id');
            $table->string('number');
            $table->foreignId('ambulatoriya_id')->nullable()->constrained('ambulatorii')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabinets');
    }
};
