<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('room_inventory')) {
            return;
        }

        $missing = collect(['quantity', 'unit', 'price', 'min_quantity', 'category'])
            ->filter(fn ($col) => ! Schema::hasColumn('room_inventory', $col));

        if ($missing->isEmpty()) {
            return;
        }

        Schema::table('room_inventory', function (Blueprint $table) use ($missing) {
            if ($missing->contains('quantity')) {
                $table->integer('quantity')->default(0);
                $table->index('quantity');
            }
            if ($missing->contains('unit')) {
                $table->string('unit', 20)->default('шт');
            }
            if ($missing->contains('price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
            if ($missing->contains('min_quantity')) {
                $table->integer('min_quantity')->default(0);
            }
            if ($missing->contains('category')) {
                $table->string('category', 100)->nullable();
                $table->index('category');
            }
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
                'category',
            ]);
        });
    }
};
