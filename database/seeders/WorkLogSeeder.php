<?php

namespace Database\Seeders;

use App\Models\WorkLog;
use Illuminate\Database\Seeder;

class WorkLogSeeder extends Seeder
{
    public function run(): void
    {
        WorkLog::factory()
            ->count(50)
            ->create();

        // Создаем специфические типы
        WorkLog::factory()
            ->inventoryTransfer()
            ->count(10)
            ->create();

        WorkLog::factory()
            ->cartridgeReplacement()
            ->count(15)
            ->create();
    }
}
