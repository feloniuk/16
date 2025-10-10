<?php
// database/migrations/2025_01_26_000001_fix_warehouse_inventory_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Отримати список існуючих foreign keys для таблиці
     */
    private function getForeignKeys($table)
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        
        $foreignKeys = DB::select(
            "SELECT CONSTRAINT_NAME 
             FROM information_schema.TABLE_CONSTRAINTS 
             WHERE CONSTRAINT_SCHEMA = ? 
             AND TABLE_NAME = ? 
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$dbName, $table]
        );
        
        return collect($foreignKeys)->pluck('CONSTRAINT_NAME')->toArray();
    }

    public function up()
    {
        $foreignKeys = $this->getForeignKeys('warehouse_inventory_items');
        
        // Перевіряємо та видаляємо існуючі зайві foreign keys
        Schema::table('warehouse_inventory_items', function (Blueprint $table) use ($foreignKeys) {
            // Видаляємо warehouse_item_id якщо він є
            if (in_array('warehouse_inventory_items_warehouse_item_id_foreign', $foreignKeys)) {
                $table->dropForeign(['warehouse_item_id']);
            }
            if (Schema::hasColumn('warehouse_inventory_items', 'warehouse_item_id')) {
                $table->dropColumn('warehouse_item_id');
            }
        });
        
        // Додаємо правильні колонки якщо їх немає
        Schema::table('warehouse_inventory_items', function (Blueprint $table) use ($foreignKeys) {
            // Додаємо warehouse_inventory_id якщо його немає
            if (!Schema::hasColumn('warehouse_inventory_items', 'warehouse_inventory_id')) {
                $table->foreignId('warehouse_inventory_id')
                      ->after('id')
                      ->constrained('warehouse_inventories')
                      ->onDelete('cascade');
            }
            
            // Додаємо inventory_id якщо його немає (це посилання на room_inventory)
            if (!Schema::hasColumn('warehouse_inventory_items', 'inventory_id')) {
                $table->foreignId('inventory_id')
                      ->after('warehouse_inventory_id')
                      ->constrained('room_inventory')
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        $foreignKeys = $this->getForeignKeys('warehouse_inventory_items');
        
        Schema::table('warehouse_inventory_items', function (Blueprint $table) use ($foreignKeys) {
            // Видаляємо inventory_id якщо він є
            if (in_array('warehouse_inventory_items_inventory_id_foreign', $foreignKeys)) {
                $table->dropForeign(['inventory_id']);
            }
            if (Schema::hasColumn('warehouse_inventory_items', 'inventory_id')) {
                $table->dropColumn('inventory_id');
            }
            
            // Видаляємо warehouse_inventory_id якщо він є
            if (in_array('warehouse_inventory_items_warehouse_inventory_id_foreign', $foreignKeys)) {
                $table->dropForeign(['warehouse_inventory_id']);
            }
            if (Schema::hasColumn('warehouse_inventory_items', 'warehouse_inventory_id')) {
                $table->dropColumn('warehouse_inventory_id');
            }
            
            // Повертаємо стару структуру
            if (!Schema::hasColumn('warehouse_inventory_items', 'warehouse_item_id')) {
                $table->foreignId('warehouse_item_id')
                      ->after('warehouse_inventory_id')
                      ->constrained('warehouse_items')
                      ->onDelete('cascade');
            }
        });
    }
};