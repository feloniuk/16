<?php
// database/migrations/2025_01_01_200000_update_roles_system.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Обновляем роли пользователей
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'warehouse_manager', 'director'])->default('admin');
        });

        // Создаем таблицу журнала операций
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('room_inventory')->onDelete('cascade');
            $table->string('action'); // moved, created, updated, deleted, assigned, returned
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->string('from_location')->nullable(); // филия:кабинет
            $table->string('to_location')->nullable(); // филия:кабинет
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Создаем таблицу для работы с подрядчиками
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->enum('type', ['repair', 'supply', 'service']); // ремонт, поставка, обслуживание
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Создаем таблицу для операций с подрядчиками
        Schema::create('contractor_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained('contractors')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('inventory_id')->nullable()->constrained('room_inventory')->onDelete('set null');
            $table->enum('type', ['send_for_repair', 'receive_from_repair', 'purchase', 'service']);
            $table->string('contract_number')->nullable();
            $table->date('operation_date');
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('description');
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Создаем таблицу для инвентаризации
        Schema::create('inventory_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('audit_number')->unique();
            $table->date('audit_date');
            $table->enum('status', ['planned', 'in_progress', 'completed'])->default('planned');
            $table->integer('total_items')->default(0);
            $table->integer('checked_items')->default(0);
            $table->integer('missing_items')->default(0);
            $table->integer('extra_items')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Создаем таблицу для результатов инвентаризации
        Schema::create('inventory_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('inventory_audits')->onDelete('cascade');
            $table->foreignId('inventory_id')->nullable()->constrained('room_inventory')->onDelete('set null');
            $table->string('inventory_number');
            $table->string('equipment_type');
            $table->string('location'); // филия:кабинет
            $table->enum('status', ['found', 'missing', 'extra', 'damaged']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Создаем таблицу для движения инвентаря между филиалами
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('from_branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('to_branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('from_room')->nullable();
            $table->string('to_room')->nullable();
            $table->date('transfer_date');
            $table->enum('status', ['planned', 'in_transit', 'completed', 'cancelled'])->default('planned');
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Создаем таблицу для элементов перемещения
        Schema::create('inventory_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('inventory_transfers')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('room_inventory')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_transfer_items');
        Schema::dropIfExists('inventory_transfers');
        Schema::dropIfExists('inventory_audit_items');
        Schema::dropIfExists('inventory_audits');
        Schema::dropIfExists('contractor_operations');
        Schema::dropIfExists('contractors');
        Schema::dropIfExists('inventory_logs');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'director', 'user'])->default('user');
        });
    }
};