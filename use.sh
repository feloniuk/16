#!/bin/bash
# Скрипт для швидкого виправлення проблем

echo "🔧 Виправлення структури бази даних..."

# 1. Створюємо міграцію
php artisan make:migration fix_warehouse_inventory_items_table

# 2. Або виконуємо SQL напряму через tinker
php artisan tinker << 'EOF'

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Перевіряємо структуру
$columns = DB::select("DESCRIBE warehouse_inventory_items");
echo "Поточні колонки:\n";
foreach ($columns as $col) {
    echo "- {$col->Field}\n";
}

// Видаляємо стару колонку
try {
    DB::statement("ALTER TABLE warehouse_inventory_items DROP FOREIGN KEY IF EXISTS warehouse_inventory_items_warehouse_item_id_foreign");
    DB::statement("ALTER TABLE warehouse_inventory_items DROP COLUMN IF EXISTS warehouse_item_id");
    echo "✅ Стару колонку видалено\n";
} catch (\Exception $e) {
    echo "⚠️ Помилка видалення: " . $e->getMessage() . "\n";
}

// Додаємо правильні колонки
try {
    if (!Schema::hasColumn('warehouse_inventory_items', 'warehouse_inventory_id')) {
        DB::statement("
            ALTER TABLE warehouse_inventory_items 
            ADD COLUMN warehouse_inventory_id BIGINT UNSIGNED NOT NULL AFTER id,
            ADD CONSTRAINT warehouse_inventory_items_warehouse_inventory_id_foreign 
                FOREIGN KEY (warehouse_inventory_id) 
                REFERENCES warehouse_inventories(id) 
                ON DELETE CASCADE
        ");
        echo "✅ Додано warehouse_inventory_id\n";
    }
    
    if (!Schema::hasColumn('warehouse_inventory_items', 'inventory_id')) {
        DB::statement("
            ALTER TABLE warehouse_inventory_items 
            ADD COLUMN inventory_id BIGINT UNSIGNED NOT NULL AFTER warehouse_inventory_id,
            ADD CONSTRAINT warehouse_inventory_items_inventory_id_foreign 
                FOREIGN KEY (inventory_id) 
                REFERENCES room_inventory(id) 
                ON DELETE CASCADE
        ");
        echo "✅ Додано inventory_id\n";
    }
} catch (\Exception $e) {
    echo "❌ Помилка додавання колонок: " . $e->getMessage() . "\n";
}

// Перевіряємо фінальну структуру
$columnsAfter = DB::select("DESCRIBE warehouse_inventory_items");
echo "\n✅ Фінальна структура:\n";
foreach ($columnsAfter as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}

EOF

echo ""
echo "✅ Виправлення завершено!"
echo ""
echo "📋 Наступні кроки:"
echo "1. Перевірте routes/web.php - маршрути для швидкої інвентаризації"
echo "2. Очистіть кеш: php artisan cache:clear"
echo "3. Очистіть config: php artisan config:clear"
echo "4. Спробуйте створити інвентаризацію знову"