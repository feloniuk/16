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
        Schema::create('work_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('work_type', [
                'inventory_transfer',
                'cartridge_replacement',
                'repair_sent',
                'repair_returned',
                'manual',
            ]);
            $table->text('description');
            $table->unsignedInteger('branch_id')->nullable();
            $table->string('room_number', 50)->nullable();
            $table->date('performed_at');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Polymorphic relationship
            $table->string('loggable_type', 100)->nullable();
            $table->unsignedBigInteger('loggable_id')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('work_type');
            $table->index('branch_id');
            $table->index('performed_at');
            $table->index('user_id');
            $table->index(['loggable_type', 'loggable_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_logs');
    }
};
