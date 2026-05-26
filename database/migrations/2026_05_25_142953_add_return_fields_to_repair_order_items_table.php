<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_order_items', function (Blueprint $table) {
            $table->date('returned_at')->nullable()->after('cost');
            $table->string('return_document_number')->nullable()->after('returned_at');
        });
    }

    public function down(): void
    {
        Schema::table('repair_order_items', function (Blueprint $table) {
            $table->dropColumn(['returned_at', 'return_document_number']);
        });
    }
};
