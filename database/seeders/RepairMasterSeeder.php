<?php
// database/seeders/RepairMasterSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RepairMaster;

class RepairMasterSeeder extends Seeder
{
    public function run()
    {
        $masters = [
            [
                'name' => 'Іван Петренко',
                'phone' => '+380501234567',
                'email' => 'ivan.petrenko@repair.com',
                'notes' => 'Спеціалізується на комп\'ютерах та серверах',
                'is_active' => true
            ],
            [
                'name' => 'Марія Коваленко',
                'phone' => '+380679876543',
                'email' => 'maria.kovalenko@repair.com',
                'notes' => 'Експерт з принтерів та МФУ',
                'is_active' => true
            ],
            [
                'name' => 'Олександр Сидоренко',
                'phone' => '+380631111111',
                'email' => null,
                'notes' => 'Ремонт мережевого обладнання',
                'is_active' => true
            ]
        ];

        foreach ($masters as $master) {
            RepairMaster::create($master);
        }
    }
}
