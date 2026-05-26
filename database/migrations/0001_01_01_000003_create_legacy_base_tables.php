<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('telegram_id')->unique();
                $table->string('name');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('inventory_templates')) {
            Schema::create('inventory_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('equipment_type');
                $table->string('brand')->nullable();
                $table->string('model')->nullable();
                $table->boolean('requires_serial')->default(false);
                $table->boolean('requires_inventory')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('room_inventory')) {
            Schema::create('room_inventory', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('admin_telegram_id')->default(0);
                $table->unsignedInteger('branch_id');
                $table->string('room_number', 50)->nullable();
                $table->unsignedInteger('template_id')->nullable();
                $table->string('equipment_type', 100);
                $table->string('brand', 100)->nullable();
                $table->string('model', 100)->nullable();
                $table->string('serial_number', 100)->nullable();
                $table->string('inventory_number', 100)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('repair_requests')) {
            Schema::create('repair_requests', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('user_telegram_id');
                $table->string('username')->nullable();
                $table->unsignedInteger('branch_id');
                $table->string('room_number', 50)->nullable();
                $table->text('description');
                $table->string('phone', 20)->nullable();
                $table->string('status', 30)->default('нова');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('cartridge_replacements')) {
            Schema::create('cartridge_replacements', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('user_telegram_id');
                $table->string('username')->nullable();
                $table->unsignedInteger('branch_id');
                $table->string('room_number', 50)->nullable();
                $table->unsignedBigInteger('printer_inventory_id')->nullable();
                $table->text('printer_info')->nullable();
                $table->string('cartridge_type', 100)->nullable();
                $table->date('replacement_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cartridge_replacements');
        Schema::dropIfExists('repair_requests');
        Schema::dropIfExists('room_inventory');
        Schema::dropIfExists('inventory_templates');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('admins');
    }
};
