<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Обновляем таблицу admins для соответствия существующей структуре
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });

        // Обновляем таблицу branches
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });

        // Обновляем таблицу cartridge_replacements
        Schema::table('cartridge_replacements', function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->string('printer_info', 500)->change();
        });

        // Обновляем таблицу room_inventory
        Schema::table('room_inventory', function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->bigInteger('admin_telegram_id')->change();
        });

        // Обновляем таблицу inventory_templates
        Schema::table('inventory_templates', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }

    public function down()
    {
        // Возвращаем обратно при необходимости
        Schema::table('admins', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });

        Schema::table('cartridge_replacements', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
            $table->text('printer_info')->change();
        });

        Schema::table('room_inventory', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
            $table->bigInteger('admin_telegram_id')->default(0)->change();
        });

        Schema::table('inventory_templates', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });
    }
};