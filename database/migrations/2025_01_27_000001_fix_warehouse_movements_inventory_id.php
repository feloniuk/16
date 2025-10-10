<?php
// database/migrations/2025_01_27_000001_fix_warehouse_movements_inventory_id.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Перевіряємо чи існує колонка warehouse_item_id
        if (Schema::hasColumn('warehouse_movements', 'warehouse_item_id')) {
            Schema::table('warehouse_movements', function (Blueprint $table) {
                // Видаляємо foreign key якщо він є
                $foreignKeys = DB::select(
                    "SELECT CONSTRAINT_NAME 
                     FROM information_schema.TABLE_CONSTRAINTS 
                     WHERE CONSTRAINT_SCHEMA = ? 
                     AND TABLE_NAME = 'warehouse_movements' 
                     AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
                    [DB::getDatabaseName()]
                );
                
                foreach ($foreignKeys as $fk) {
                    if (strpos($fk->CONSTRAINT_NAME, 'warehouse_item') !== false) {
                        $table->dropForeign([$fk->CONSTRAINT_NAME]);
                    }
                }
                
                // Видаляємо колонку
                $table->dropColumn('warehouse_item_id');
            });
        }
        
        // Додаємо правильну колонку якщо її немає
        if (!Schema::hasColumn('warehouse_movements', 'inventory_id')) {
            Schema::table('warehouse_movements', function (Blueprint $table) {
                $table->foreignId('inventory_id')
                      ->after('user_id')
                      ->constrained('room_inventory')
                      ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('warehouse_movements', 'inventory_id')) {
            Schema::table('warehouse_movements', function (Blueprint $table) {
                $table->dropForeign(['inventory_id']);
                $table->dropColumn('inventory_id');
            });
        }
    }
};