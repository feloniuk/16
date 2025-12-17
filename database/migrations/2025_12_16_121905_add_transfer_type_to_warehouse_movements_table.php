<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'transfer' to the enum values for the 'type' column
        DB::statement("ALTER TABLE warehouse_movements MODIFY COLUMN type ENUM('receipt', 'issue', 'writeoff', 'inventory', 'transfer') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'transfer' from the enum values
        DB::statement("ALTER TABLE warehouse_movements MODIFY COLUMN type ENUM('receipt', 'issue', 'writeoff', 'inventory') NOT NULL");
    }
};
