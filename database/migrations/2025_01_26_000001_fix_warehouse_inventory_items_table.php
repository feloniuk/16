<?php
// database/migrations/2025_01_26_000001_fix_warehouse_inventory_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Перевіряємо чи існує стара колонка і перейменовуємо
        if (Schema::hasColumn('warehouse_inventory_items', 'warehouse_inventory_id')) {
            Schema::table('warehouse_inventory_items', function (Blueprint $table) {
                // Якщо є warehouse_item_id - видаляємо його
                if (Schema::hasColumn('warehouse_inventory_items', 'warehouse_item_id')) {
                    $table->dropForeign(['warehouse_item_id']);
                    $table->dropColumn('warehouse_item_id');
                }
            });
        } else {
            // Якщо немає правильної колонки - додаємо
            Schema::table('warehouse_inventory_items', function (Blueprint $table) {
                // Видаляємо стару колонку якщо є
                if (Schema::hasColumn('warehouse_inventory_items', 'warehouse_item_id')) {
                    $table->dropForeign(['warehouse_item_id']);
                    $table->dropColumn('warehouse_item_id');
                }
                
                // Додаємо правильну колонку
                if (!Schema::hasColumn('warehouse_inventory_items', 'warehouse_inventory_id')) {
                    $table->foreignId('warehouse_inventory_id')
                          ->after('id')
                          ->constrained('warehouse_inventories')
                          ->onDelete('cascade');
                }
            });
        }
        
        // Перевіряємо inventory_id
        if (!Schema::hasColumn('warehouse_inventory_items', 'inventory_id')) {
            Schema::table('warehouse_inventory_items', function (Blueprint $table) {
                $table->foreignId('inventory_id')
                      ->after('warehouse_inventory_id')
                      ->constrained('room_inventory')
                      ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::table('warehouse_inventory_items', function (Blueprint $table) {
            if (Schema::hasColumn('warehouse_inventory_items', 'inventory_id')) {
                $table->dropForeign(['inventory_id']);
                $table->dropColumn('inventory_id');
            }
        });
    }
};