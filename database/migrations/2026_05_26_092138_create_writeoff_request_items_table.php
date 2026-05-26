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
        Schema::create('writeoff_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('writeoff_request_id')->constrained('writeoff_requests')->onDelete('cascade');
            $table->foreignId('inventory_id')->nullable()->constrained('room_inventory')->nullOnDelete();
            $table->string('item_name');
            $table->string('unit')->default('шт');
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writeoff_request_items');
    }
};
