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
        // Додаємо категорію "Миючі засоби" до перших товарів на складі
        // Спочатку перевіряємо чи вже існує елемент з цією категорією
        DB::table('room_inventory')
            ->where('category', 'миючі засоби')
            ->orWhere('category', 'Миючі засоби')
            ->limit(1)
            ->exists();

        // Якщо не існує - оновлюємо перший елемент зі складу
        if (! DB::table('room_inventory')
            ->whereRaw('LOWER(category) = ?', ['миючі засоби'])
            ->exists()) {
            // Створюємо один тестовий елемент з цією категорією
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
