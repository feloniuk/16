<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('room_inventory')) {
            return;
        }

        if (DB::table('room_inventory')
            ->whereRaw('LOWER(category) = ?', ['миючі засоби'])
            ->exists()) {
            return;
        }

        DB::table('room_inventory')->insert([
            'branch_id' => 6,
            'room_number' => 'Загальний',
            'equipment_type' => 'Миючі засоби',
            'inventory_number' => 'CLEAN-'.now()->format('YmdHis'),
            'quantity' => 0,
            'unit' => 'шт',
            'price' => 0,
            'category' => 'миючі засоби',
            'admin_telegram_id' => 0,
            'created_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Видаляємо елементи з категорією "Миючі засоби"
        DB::table('room_inventory')
            ->whereRaw('LOWER(category) = ?', ['миючі засоби'])
            ->where('equipment_type', 'Миючі засоби')
            ->delete();
    }
};
