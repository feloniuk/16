<?php
// database/migrations/2025_01_01_120000_create_repair_trackings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Таблица мастеров по ремонту
        Schema::create('repair_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Таблица для учета отправок на ремонт
        Schema::create('repair_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('room_inventory')->onDelete('cascade');
            $table->foreignId('repair_master_id')->nullable()->constrained('repair_masters')->onDelete('set null');
            $table->date('sent_date');
            $table->date('returned_date')->nullable();
            $table->string('invoice_number')->nullable();
            $table->text('our_description'); // описание поломки от нас
            $table->text('repair_description')->nullable(); // описание ремонта от мастера
            $table->decimal('cost', 10, 2)->nullable();
            $table->enum('status', ['sent', 'in_repair', 'completed', 'cancelled'])->default('sent');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('repair_trackings');
        Schema::dropIfExists('repair_masters');
    }
};