<?php
// database/seeders/InventoryExcelSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryExcelSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 021 Забалансовий облік (ОЗ)
            ['balance_code' => '021 Забалансовий облік (ОЗ)', 'equipment_type' => 'Бензиновий електрогенератор "Pramac" РХ8000', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '5524', 'price' => 31591.80],
            ['balance_code' => '021 Забалансовий облік (ОЗ)', 'equipment_type' => 'Бензиновий електрогенератор "Pramac" РХ8000', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '5525', 'price' => 31591.80],
            
            // 103 Б/о Будівлі та споруди отримані безоплатно
            ['balance_code' => '103 Б/о Будівлі та споруди отримані безоплатно', 'equipment_type' => 'Поликлиника 16', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10137002', 'price' => 280179.00],
            ['balance_code' => '103 Б/о Будівлі та споруди отримані безоплатно', 'equipment_type' => 'Пристройка поликлиники 3 эт.', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101317001', 'price' => 360874.00],
            
            // 103/Д Будівлі та споруди дооцінені
            ['balance_code' => '103/Д Будівлі та споруди дооцінені', 'equipment_type' => 'Поликлиника 16', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101317002', 'price' => 14261111.10],
            ['balance_code' => '103/Д Будівлі та споруди дооцінені', 'equipment_type' => 'Пристройка к поликлинике 3эт', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101317001', 'price' => 25010750.00],
            
            // 104 Машини та обладнання
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Автоматичні розсувні двері', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400098', 'price' => 103968.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'БФП (Принтер) HP Pro', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400048', 'price' => 6328.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'БФП (Принтер) HP Pro', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400049', 'price' => 6328.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'БФП (Принтер) HP Pro', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400050', 'price' => 6328.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'БФП Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400061', 'price' => 10974.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Блок до припливної установки(Компрессорно конденсаторний)', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149759', 'price' => 83280.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Вентилятор', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497038', 'price' => 2666.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Вентилятор', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497039', 'price' => 2667.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Двері алюмінієві', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400096', 'price' => 20278.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Двері алюмінієві', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400097', 'price' => 20278.00],
            
            // 152/НСЗУ Придбання (виготовлення) основних засобів
            ['balance_code' => '152/НСЗУ Придбання (виготовлення) основних засобів', 'equipment_type' => 'Дверь металопластикова 1910*2450', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400305', 'price' => 29684.32],
            
            // 104 Машини та обладнання (продовження)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400009', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400010', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400011', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400012', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400013', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400014', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400015', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400016', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400017', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400018', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван 1600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400019', 'price' => 6019.20],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400062', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400063', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400064', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400065', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400066', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400067', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400068', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400069', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400070', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван без підлокітників', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400071', 'price' => 6020.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Диван кутовий 600*600*700', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400020', 'price' => 3857.80],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Електронне інформаційне табло', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400007', 'price' => 38900.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Електронне інформаційне табло', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400008', 'price' => 38900.00],
            
            // Компьютери Qbox (15 штук)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400045', 'price' => 15580.80],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400046', 'price' => 15580.80],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400047', 'price' => 15580.80],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400051', 'price' => 15581.10],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400052', 'price' => 15581.10],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400053', 'price' => 15581.10],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400054', 'price' => 15581.10],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400055', 'price' => 15581.10],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400056', 'price' => 15581.10],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400057', 'price' => 15581.10],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400058', 'price' => 15581.10],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400059', 'price' => 15581.10],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комп`ютер Qbox', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400060', 'price' => 15581.10],
            
            // Продовжуємо далі...
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комплексна система відеоспостереження(камера-10 шт; видеореест.-16 каналов)', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467115', 'price' => 43130.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комплексна система відеоспостереження(камера-4шт;видеор.-8 каналов)', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467116', 'price' => 21565.00],
            
            // Через обмеження токенів, решту даних додамо в наступній частині
            // Загалом у файлі 242 рядки
        ];

        foreach ($data as $item) {
            DB::table('room_inventory')->insert([
                'admin_telegram_id' => 0,
                'branch_id' => 6, // Склад
                'room_number' => 'Склад',
                'balance_code' => $item['balance_code'],
                'equipment_type' => $item['equipment_type'],
                'unit' => $item['unit'],
                'quantity' => $item['quantity'],
                'inventory_number' => $item['inventory_number'],
                'price' => $item['price'] ?? null,
                'min_quantity' => 0,
                'category' => null,
                'brand' => null,
                'model' => null,
                'serial_number' => null,
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Імпортовано ' . count($data) . ' позицій інвентарю');
    }
}