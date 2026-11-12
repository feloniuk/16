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
        Schema::create('inventory_walk_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_audit_id')->constrained('inventory_walks')->cascadeOnDelete();
            // No FK constraint: branches.id is a legacy `int(11)` (signed) column;
            // this matches the unconstrained-reference pattern used by cabinets.branch_id.
            $table->unsignedInteger('branch_id');
            $table->index('branch_id');
            $table->string('room_number', 50);
            $table->foreignId('cabinet_id')->nullable()->constrained('cabinets')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['inventory_audit_id', 'branch_id', 'room_number'], 'inventory_walk_rooms_location_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_walk_rooms');
    }
};
