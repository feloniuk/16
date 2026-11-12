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
        Schema::create('inventory_walk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_audit_room_id')->constrained('inventory_walk_rooms')->cascadeOnDelete();
            // No FK constraint: room_inventory is a legacy table and a record may be
            // removed while the audit that referenced it is kept as history.
            $table->unsignedBigInteger('room_inventory_id')->nullable();
            $table->index('room_inventory_id');
            $table->string('equipment_type');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number')->nullable();
            $table->string('inventory_number')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->string('resolution')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_walk_items');
    }
};
