<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Добавляем balance_code только если его нет
        if (!Schema::hasColumn('room_inventory', 'balance_code')) {
            Schema::table('room_inventory', function (Blueprint $table) {
                $table->string('balance_code', 100)->nullable()->after('category');
                $table->index('balance_code');
            });
        }
        
        // Проверяем и создаём таблицу inventory_transfers если её нет
        if (!Schema::hasTable('inventory_transfers')) {
            Schema::create('inventory_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_id')->constrained('room_inventory')->onDelete('cascade');
                $table->foreignId('from_branch_id')->nullable()->constrained('branches')->onDelete('set null');
                $table->string('from_room_number', 50)->nullable();
                $table->foreignId('to_branch_id')->constrained('branches')->onDelete('cascade');
                $table->string('to_room_number', 50);
                $table->integer('quantity');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->date('transfer_date');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
        
        // Проверяем и создаём таблицу inventory_logs если её нет
        if (!Schema::hasTable('inventory_logs')) {
            Schema::create('inventory_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('inventory_id')->constrained('room_inventory')->onDelete('cascade');
                $table->string('action');
                $table->json('old_data')->nullable();
                $table->json('new_data')->nullable();
                $table->string('from_location')->nullable();
                $table->string('to_location')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
        
        // Проверяем и создаём таблицу contractors если её нет
        if (!Schema::hasTable('contractors')) {
            Schema::create('contractors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('company_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->enum('type', ['repair', 'supply', 'service']);
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
        
        // Проверяем и создаём таблицу contractor_operations если её нет
        if (!Schema::hasTable('contractor_operations')) {
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
        }
        
        // Проверяем и создаём таблицу inventory_audits если её нет
        if (!Schema::hasTable('inventory_audits')) {
            Schema::create('inventory_audits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
                $table->string('audit_number');
                $table->date('audit_date');
                $table->enum('status', ['planned', 'in_progress', 'completed'])->default('planned');
                $table->integer('total_items')->default(0);
                $table->integer('checked_items')->default(0);
                $table->integer('missing_items')->default(0);
                $table->integer('extra_items')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
        
        // Проверяем и создаём таблицу inventory_audit_items если её нет
        if (!Schema::hasTable('inventory_audit_items')) {
            Schema::create('inventory_audit_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_id')->nullable()->constrained('room_inventory')->onDelete('set null');
                $table->string('inventory_number');
                $table->string('equipment_type');
                $table->string('location');
                $table->enum('status', ['found', 'missing', 'extra', 'damaged']);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // Удаляем таблицы в обратном порядке
        Schema::dropIfExists('inventory_audit_items');
        Schema::dropIfExists('inventory_audits');
        Schema::dropIfExists('contractor_operations');
        Schema::dropIfExists('contractors');
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('inventory_transfers');    
        
        // Удаляем колонку balance_code если она была добавлена этой миграцией
        if (Schema::hasColumn('room_inventory', 'balance_code')) {
            Schema::table('room_inventory', function (Blueprint $table) {
                $table->dropIndex(['balance_code']);
                $table->dropColumn('balance_code');
            });
        }
    }
};