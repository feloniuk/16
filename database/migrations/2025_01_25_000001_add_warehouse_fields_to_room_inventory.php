<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('room_inventory', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->after('inventory_number');
            $table->string('unit', 20)->default('шт')->after('quantity');
            $table->decimal('price', 10, 2)->nullable()->after('unit');
            $table->integer('min_quantity')->default(0)->after('price');
            $table->string('category', 100)->nullable()->after('equipment_type');
            
            $table->index('category');
            $table->index('quantity');
        });
    }

    public function down()
    {
        Schema::table('room_inventory', function (Blueprint $table) {
            $table->dropColumn([
                'quantity', 
                'unit', 
                'price', 
                'min_quantity', 
                'category'
            ]);
        });
    }
};