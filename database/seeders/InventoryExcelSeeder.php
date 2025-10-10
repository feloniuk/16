<?php
// database/seeders/InventoryExcelSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryExcelSeeder extends Seeder
{
    public function run()
    {
        // Очищаем таблицу перед импортом (опционально)
        // DB::table('room_inventory')->truncate();
        
        $data = [
            // 021 Забалансовий облік (ОЗ)
            ['balance_code' => '021 Забалансовий облік (ОЗ)', 'equipment_type' => 'Бензиновий електрогенератор "Pramac" РХ8000', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '5524', 'price' => 31591.80],
            ['balance_code' => '021 Забалансовий облік (ОЗ)', 'equipment_type' => 'Бензиновий електрогенератор "Pramac" РХ8000', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '5525', 'price' => 31591.80],
            
            // 103 Б/о Будівлі та споруди отримані безоплатно
            ['balance_code' => '103 Б/о Будівлі та споруди отримані безоплатно', 'equipment_type' => 'Поликлиника 16', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10137002', 'price' => 280179.00],
            ['balance_code' => '103 Б/о Будівлі та споруди отримані безоплатно', 'equipment_type' => 'Пристройка поликлиники 3 эт.', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101317001', 'price' => 360874.00],
            
            // 103/Д Будівлі та споруди дооцінені
            ['balance_code' => '103/Д Будівлі та споруди дооцінені', 'equipment_type' => 'Поликлиника 16', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101317002', 'price' => 14261111.10],
            ['balance_code' => '103/Д Будівлі та споруди дооцінені', 'equipment_type' => 'Пристройка к поликлинике 3эт', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101317001_2', 'price' => 25010750.00],
            
            // 104 Машини та обладнання (перша частина)
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
            
            // 152/НСЗУ
            ['balance_code' => '152/НСЗУ Придбання (виготовлення) основних засобів', 'equipment_type' => 'Дверь металопластикова 1910*2450', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400305', 'price' => 29684.32],
            
            // 104 Машини та обладнання - Дивани (11 шт)
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
            
            // Дивани без підлокітників (10 шт)
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
            
            // Компьютери Qbox (15 шт)
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
            
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комплексна система відеоспостереження(камера-10 шт; видеореест.-16 каналов)', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467115', 'price' => 43130.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Комплексна система відеоспостереження(камера-4шт;видеор.-8 каналов)', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467116', 'price' => 21565.00],
            
            // Компьютери персональні
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Компьютер персональний', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467031', 'price' => 7462.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Компьютер персональний', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467039', 'price' => 9800.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Компьютер персональний', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467040', 'price' => 9800.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Компьютер персональний', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467041', 'price' => 9800.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Компьютер персональний', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467042', 'price' => 9800.00],
            
            // Кондиціонери Мідея (4 шт)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиціонер Мідея', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400077', 'price' => 10910.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиціонер Мідея', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400078', 'price' => 10910.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиціонер Мідея', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400079', 'price' => 10910.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиціонер Мідея', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400080', 'price' => 10910.00],
            
            // Кондиціонери (різні)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497034', 'price' => 3268.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497035', 'price' => 3267.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497036', 'price' => 1291.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497043', 'price' => 2500.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497044', 'price' => 2500.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497045', 'price' => 2500.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101487046', 'price' => 10000.00],
            
            // Кондиціонери MIDEA (10 шт)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149746', 'price' => 12790.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149747', 'price' => 12790.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149748', 'price' => 12790.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149749', 'price' => 12790.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149750', 'price' => 12790.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149751', 'price' => 12790.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149752', 'price' => 12790.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149753', 'price' => 12790.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149754', 'price' => 12790.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Кондиционер "MIDEA"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149755', 'price' => 12790.00],
            
            // Монітори
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Монитор', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101487017', 'price' => 1512.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Монитор', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101487022', 'price' => 1058.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Монитор', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101487023', 'price' => 1059.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Монитор', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467037', 'price' => 3975.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Монитор', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467038', 'price' => 3975.00],
            
            // Монітори 27 LG (3 шт)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Монитор 27 LG', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400042', 'price' => 7488.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Монитор 27 LG', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400043', 'price' => 7488.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Монитор 27 LG', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400044', 'price' => 7488.00],
            
            // Ноутбуки
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'НОУТ- БУК', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101487019', 'price' => 2667.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'НОУТ- БУК', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101468045', 'price' => 5399.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'НОУТ- БУК', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467032', 'price' => 6480.00],
            
            // Ноутбуки ASUS (3 шт)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Ноутбук ASUS X515EA-BQ1175', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400275', 'price' => 27197.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Ноутбук ASUS X515EA-BQ1175', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400276', 'price' => 27197.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Ноутбук ASUS X515EA-BQ1175', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400277', 'price' => 27197.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Ноутбук Lenovo IdeaPad + П3 WIN 10H 64BIT', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '1046057', 'price' => 20638.80],
            
            // Системні блоки
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Системный блок', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101487025', 'price' => 2731.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Системный блок', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101487020', 'price' => 2882.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Системный блок', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467034', 'price' => 7350.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Системный блок', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467035', 'price' => 7350.00],
            
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Стенд Амбулатории 1-5 из 3 частей', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400260', 'price' => 20481.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Установка ПВ1 витяжна тип МС в компл.з автоматикой', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10149760', 'price' => 146670.00],
            
            // Холодильники Норд (3 шт)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Холодильник Норд', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497040', 'price' => 2327.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Холодильник Норд', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497041', 'price' => 2327.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Холодильник Норд', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497042', 'price' => 2328.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Холодильник Індезит', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497037', 'price' => 1383.00],
            
            // Шафи господарські (5 шт)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шафа господарська', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400037', 'price' => 6700.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шафа господарська', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400038', 'price' => 6700.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шафа господарська', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400039', 'price' => 6700.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шафа господарська', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400040', 'price' => 6700.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шафа господарська', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400041', 'price' => 6700.00],
            
            // Шафи для одягу металеві (4 шт)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шафа для одягу металева', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400033', 'price' => 5850.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шафа для одягу металева', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400034', 'price' => 5850.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шафа для одягу металева', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400035', 'price' => 5850.00],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шафа для одягу металева', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400036', 'price' => 5850.00],
            
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шкаф ВРУ', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101447004', 'price' => 151.00],
            
            // Шкафи для одежды (5 шт)
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шкаф для одежды', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400022', 'price' => 6049.45],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шкаф для одежды', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400023', 'price' => 6049.45],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шкаф для одежды', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400024', 'price' => 6049.45],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шкаф для одежды', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400025', 'price' => 6049.45],
            ['balance_code' => '104 Машини та обладнання', 'equipment_type' => 'Шкаф для одежды', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400026', 'price' => 6049.45],
            
            // 104 Б/о Машини та обладнання отримані по благ. доп.
            // БФП Epson (30 шт)
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467075', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467076', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467078', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467079', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467080', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467081', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467082', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467083', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467085', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467086', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467087', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467088', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467089', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467090', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467091', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467092', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467093', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467094', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467095', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467096', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467097', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467098', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467101', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467102', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467103', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467105', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467106', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467107', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467108', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467109', 'price' => 9324.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Багатофункціональний пристрій Epson', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467110', 'price' => 9324.00],
            
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Генератор CGM 9000SPTE 7,2 kW', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400264', 'price' => 64377.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Джерело безперебійного живлення APC SMC1500I-2U', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467113', 'price' => 36440.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Джерело безперебійного живлення APC SMC10000I-2U', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467114', 'price' => 28800.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Екран міжмережевий FortiGate-50E Hardware plus 3 Year8*5', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497053', 'price' => 56903.75],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Екран міжмережевий FortiGate-50E Protection FG-50E-BDL', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497054', 'price' => 56903.75],
            
            // Компьютери VT Computers (28 шт)
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467045', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467046', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467047', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467048', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467049', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467050', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467052', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467053', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467057', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467054', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467055', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467056', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467058', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467059', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467060', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467061', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467062', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467064', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467065', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467066', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467067', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467068', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467069', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467070', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467071', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467072', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467073', 'price' => 14327.50],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Компьютер VT Computers', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467074', 'price' => 14327.50],
            
            // Комутатори
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Комутатор HP 2530-8G', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497057', 'price' => 16816.52],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Комутатор HP 2530-48G-PoE+48*GE+GE L2 WarrantlyJ9772A', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497055', 'price' => 55966.52],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Комутатор HP 2530-48G', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497056', 'price' => 55966.52],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Комутатор НР 2530-8G 8xGE+2xGE-T/SFP,L2,LTWarrantlyJ9777A', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497058', 'price' => 16816.52],
            
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Персональный компьютер', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467043', 'price' => 8000.00],
            
            // Шафи монтажні (2 шт)
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Шафа монтажна 2РА5 19 24U 600*600', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467111', 'price' => 14300.00],
            ['balance_code' => '104 Б/о Машини та обладнання отримані по благ. доп.', 'equipment_type' => 'Шафа монтажна 2РА5 19 24U 600*600', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101467112', 'price' => 14300.00],
            
            // 104 Ц/ф Машини і обладнання цільове фінансування
            ['balance_code' => '104 Ц/ф Машини і обладнання цільове фінансування', 'equipment_type' => 'Електронне інформаційне табло LFD LG 49" 49SM5KE-B', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497051', 'price' => 54900.00],
            ['balance_code' => '104 Ц/ф Машини і обладнання цільове фінансування', 'equipment_type' => 'Електронне інформаційне табл. LFD LG 49', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '101497052', 'price' => 54900.00],
            
            // 104/Бюдж Машини і обладнання по бюджету розвитку
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'MK-DL200WY хірургічний пересувний LED світильник Medik', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400298', 'price' => 124869.00],
            
            // NEO ECG S120 (7 шт)
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'NEO ECG S120 елктрокардіограф Carewell', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400286', 'price' => 68970.00],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'NEO ECG S120 елктрокардіограф Carewell', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400287', 'price' => 68970.00],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'NEO ECG S120 елктрокардіограф Carewell', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400288', 'price' => 68970.00],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'NEO ECG S120 елктрокардіограф Carewell', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400289', 'price' => 68970.00],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'NEO ECG S120 елктрокардіограф Carewell', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400290', 'price' => 68970.00],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'NEO ECG S120 елктрокардіограф Carewell', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400291', 'price' => 68970.00],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'NEO ECG S120 елктрокардіограф Carewell', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400292', 'price' => 68970.00],
            
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'NEO ECG T180 елктрокардіограф Carewell', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400285', 'price' => 85580.00],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'YA-PS06 медичні ноші гідравлічні Medik', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400303', 'price' => 89499.08],
            
            // Дефібрилятори (2 шт)
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'і6 універсальний дефібрилятор монітор Amoul', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400299', 'price' => 189999.90],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'і6 універсальний дефібрилятор монітор Amoul', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400300', 'price' => 189999.90],
            
            // Монітори пацієнта (2 шт)
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'К12 base приліжковий монітор пацієнта "12 Creative Medical"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400301', 'price' => 53570.62],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'К12 base приліжковий монітор пацієнта "12 Creative Medical"', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400302', 'price' => 53570.62],
            
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'Набір F.O. ларингоскоп дорослий + педіатричний LED2.5B + 2 Miller та 3 Macintosh клинка Luxamed', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400304', 'price' => 29370.43],
            ['balance_code' => '104/Бюдж Машини і обладнання по бюджету розвитку', 'equipment_type' => 'Система рентгеннівська діагностична мобільна Jumong mobile', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400296', 'price' => 2334579.44],
            
            // 106 Ц/ф Машини і обладнання цільове фінансування
            ['balance_code' => '106 Ц/ф Машини і обладнання цільове фінансування', 'equipment_type' => 'Візок для прибирання професійний', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '1063224', 'price' => 24112.00],
            
            // 122 Б/о Право користування майном безоплатно отримано
            ['balance_code' => '122 Б/о Право користування майном безоплатно отримано', 'equipment_type' => 'Право постійного користування на зелельну ділянку за адресою Івана і Юрія Лип,1', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '12200001', 'price' => 5688911.00],
            
            // 122/НСЗУ Право користування майном
            ['balance_code' => '122/НСЗУ Право користування майном', 'equipment_type' => 'Право постійного користування на зелельну ділянку за адресою Івана і Юрія Лип,1', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '12200001_2', 'price' => 10000.00],
            
            // 127 Інші нематеріальні активи (7 шт)
            ['balance_code' => '127 Інші нематеріальні активи', 'equipment_type' => 'Програмне забезпечення Microsoft Windows', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '12700003', 'price' => 6809.00],
            ['balance_code' => '127 Інші нематеріальні активи', 'equipment_type' => 'Програмне забезпечення Microsoft Windows', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '12700004', 'price' => 6809.00],
            ['balance_code' => '127 Інші нематеріальні активи', 'equipment_type' => 'Програмне забезпечення Microsoft Windows', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '12700005', 'price' => 6809.00],
            ['balance_code' => '127 Інші нематеріальні активи', 'equipment_type' => 'Програмне забезпечення Microsoft Windows', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '12700006', 'price' => 6809.00],
            ['balance_code' => '127 Інші нематеріальні активи', 'equipment_type' => 'Програмне забезпечення Microsoft Windows', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '12700007', 'price' => 6809.00],
            ['balance_code' => '127 Інші нематеріальні активи', 'equipment_type' => 'Програмне забезпечення Microsoft Windows', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400008_2', 'price' => 6809.00],
            ['balance_code' => '127 Інші нематеріальні активи', 'equipment_type' => 'Програмний продукт Office', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '12700009', 'price' => 6630.00],
            
            // 152/бюдж Придбання (виготовлення) основних засобів по бюджету
            ['balance_code' => '152/бюдж Придбання (виготовлення) основних засобів по бюджету', 'equipment_type' => 'Автоматичний коагулометр С3100', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400284', 'price' => 1199999.65],
            ['balance_code' => '152/бюдж Придбання (виготовлення) основних засобів по бюджету', 'equipment_type' => 'Аналізатор гематологічний(3-компонентний автоматичний HRJ-H350)', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400295', 'price' => 446000.00],
            ['balance_code' => '152/бюдж Придбання (виготовлення) основних засобів по бюджету', 'equipment_type' => 'Аналізатор імунофлюрисцентний HRJ-F100', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400293', 'price' => 112149.53],
            ['balance_code' => '152/бюдж Придбання (виготовлення) основних засобів по бюджету', 'equipment_type' => 'Біохімічний аналізатор (автоматичний HRJ-C100)', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400294', 'price' => 965420.56],
            ['balance_code' => '152/бюдж Придбання (виготовлення) основних засобів по бюджету', 'equipment_type' => 'Система радіографічна цифрова Beatle-06P', 'unit' => 'шт', 'quantity' => 1, 'inventory_number' => '10400297', 'price' => 3177570.09],
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
            ]);
        }

        $this->command->info('Імпортовано ' . count($data) . ' позицій інвентарю');
    }
}