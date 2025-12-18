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
        Schema::create('repair_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->enum('status', [
                'draft',
                'pending_approval',
                'approved',
                'rejected',
                'sent',
                'in_repair',
                'completed',
                'cancelled',
            ])->default('draft');
            $table->foreignId('repair_master_id')->nullable()->constrained('repair_masters')->onDelete('set null');
            $table->string('invoice_number')->nullable();
            $table->date('sent_date')->nullable();
            $table->date('returned_date')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('user_id');
            $table->index('repair_master_id');
        });

        Schema::create('repair_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_order_id')->constrained('repair_orders')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('room_inventory')->onDelete('restrict');
            $table->text('repair_description');
            $table->text('repair_notes')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->timestamps();

            $table->index('repair_order_id');
            $table->index('equipment_id');
            $table->unique(['repair_order_id', 'equipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_order_items');
        Schema::dropIfExists('repair_orders');
    }
};
