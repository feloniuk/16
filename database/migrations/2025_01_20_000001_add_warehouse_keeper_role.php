<?php
// database/migrations/2025_01_20_000001_add_warehouse_keeper_role.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Обновляем enum для ролей пользователей
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'warehouse_manager', 'warehouse_keeper', 'director') DEFAULT 'warehouse_keeper'");
        
        // Создаем таблицу для товарных позиций на складе
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название товара
            $table->string('code')->unique(); // Артикул/код товара
            $table->text('description')->nullable(); // Описание
            $table->string('unit')->default('шт'); // Единица измерения
            $table->integer('quantity')->default(0); // Текущее количество
            $table->integer('min_quantity')->default(0); // Минимальное количество для заказа
            $table->decimal('price', 10, 2)->nullable(); // Цена за единицу
            $table->string('category')->nullable(); // Категория товара
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Таблица для движения товаров
        Schema::create('warehouse_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('warehouse_item_id')->constrained('warehouse_items');
            $table->enum('type', ['receipt', 'issue', 'writeoff', 'inventory']); // приход, выдача, списание, инвентаризация
            $table->integer('quantity'); // количество (может быть отрицательным для выдачи)
            $table->integer('balance_after'); // остаток после операции
            $table->text('note')->nullable(); // примечание
            $table->string('document_number')->nullable(); // номер документа
            $table->foreignId('issued_to_user_id')->nullable()->constrained('users'); // кому выдано
            $table->date('operation_date');
            $table->timestamps();
        });
        
        // Таблица для заявок на заказ товара
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('user_id')->constrained('users'); // кто создал заявку
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'completed'])->default('draft');
            $table->text('description')->nullable(); // общее описание заявки
            $table->decimal('total_amount', 12, 2)->default(0); // общая сумма
            $table->date('requested_date'); // дата когда нужно
            $table->text('notes')->nullable(); // примечания
            $table->timestamps();
        });
        
        // Позиции в заявке на заказ
        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('warehouse_item_id')->nullable()->constrained('warehouse_items');
            $table->string('item_name'); // название (может быть новый товар)
            $table->string('item_code')->nullable(); // код товара
            $table->integer('quantity'); // требуемое количество
            $table->string('unit')->default('шт'); // единица измерения
            $table->decimal('estimated_price', 10, 2)->nullable(); // ожидаемая цена
            $table->text('specifications')->nullable(); // технические требования
            $table->timestamps();
        });
        
        // Инвентаризации (упрощенная версия)
        Schema::create('warehouse_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_number')->unique();
            $table->foreignId('user_id')->constrained('users'); // кто проводил
            $table->date('inventory_date');
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
        // Результаты инвентаризации
        Schema::create('warehouse_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('warehouse_inventories')->onDelete('cascade');
            $table->foreignId('warehouse_item_id')->constrained('warehouse_items');
            $table->integer('system_quantity'); // количество в системе
            $table->integer('actual_quantity'); // фактическое количество
            $table->integer('difference'); // разность
            $table->text('note')->nullable(); // примечание к позиции
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_inventory_items');
        Schema::dropIfExists('warehouse_inventories');
        Schema::dropIfExists('purchase_request_items');
        Schema::dropIfExists('purchase_requests');
        Schema::dropIfExists('warehouse_movements');
        Schema::dropIfExists('warehouse_items');
        
        // Возвращаем старые роли
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'warehouse_manager', 'director') DEFAULT 'admin'");
    }
};