<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        // Обновляем таблицу cartridge_replacements
        Schema::table('cartridge_replacements', function (Blueprint $table) {
            $table->string('printer_info', 500)->change();
        });

        // Обновляем таблицу room_inventory
        Schema::table('room_inventory', function (Blueprint $table) {
            $table->bigInteger('admin_telegram_id')->change();
        });

        // Обновляем таблицу inventory_templates
        Schema::table('inventory_templates', function (Blueprint $table) {
        });
    }

    public function down()
    {
        Schema::table('cartridge_replacements', function (Blueprint $table) {
            $table->text('printer_info')->change();
        });

        Schema::table('room_inventory', function (Blueprint $table) {
            $table->bigInteger('admin_telegram_id')->default(0)->change();
        });

    }
};