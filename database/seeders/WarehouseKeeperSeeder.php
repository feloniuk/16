<?php
// database/seeders/WarehouseKeeperSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WarehouseItem;
use Illuminate\Support\Facades\Hash;

class WarehouseKeeperSeeder extends Seeder
{
    public function run()
    {
        // Создаем пользователя-складовщика
        $warehouseKeeper = User::firstOrCreate(
            ['email' => 'warehouse@' . parse_url(config('app.url'), PHP_URL_HOST)],
            [
                'name' => 'Складовщик',
                'password' => Hash::make('password'),
                'role' => 'warehouse_keeper',
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );
        
        $this->command->info("Складовщик создан/обновлен: {$warehouseKeeper->email}");
        
        // Создаем несколько тестовых товаров на складе
        $sampleItems = [
            [
                'name' => 'Папір офісний А4',
                'code' => 'PAPER-A4-80',
                'description' => 'Папір офісний білий А4, щільність 80 г/м²',
                'unit' => 'пачка',
                'quantity' => 25,
                'min_quantity' => 10,
                'price' => 125.50,
                'category' => 'Канцелярські товари'
            ],
            [
                'name' => 'Ручки кулькові сині',
                'code' => 'PEN-BLUE-1MM',
                'description' => 'Ручка кулькова синя, товщина стержня 1мм',
                'unit' => 'шт',
                'quantity' => 150,
                'min_quantity' => 50,
                'price' => 12.00,
                'category' => 'Канцелярські товари'
            ],
            [
                'name' => 'Картридж HP LaserJet',
                'code' => 'CART-HP-85A',
                'description' => 'Оригінальний картридж HP CE285A для LaserJet',
                'unit' => 'шт',
                'quantity' => 5,
                'min_quantity' => 3,
                'price' => 2850.00,
                'category' => 'Картриджі'
            ],
            [
                'name' => 'Флешка USB 32GB',
                'code' => 'USB-32GB-KINGSTON',
                'description' => 'USB флеш-накопичувач Kingston 32GB USB 3.0',
                'unit' => 'шт',
                'quantity' => 12,
                'min_quantity' => 5,
                'price' => 450.00,
                'category' => 'Носії інформації'
            ],
            [
                'name' => 'Батарейки AA',
                'code' => 'BATTERY-AA-DURACELL',
                'description' => 'Батарейки пальчикові AA Duracell',
                'unit' => 'упак',
                'quantity' => 8,
                'min_quantity' => 15, // Сделаем этот товар с низким остатком
                'price' => 89.50,
                'category' => 'Елементи живлення'
            ],
            [
                'name' => 'Скотч канцелярський',
                'code' => 'TAPE-CLEAR-19MM',
                'description' => 'Скотч прозорий канцелярський 19мм х 33м',
                'unit' => 'шт',
                'quantity' => 0, // Сделаем этот товар закончившимся
                'min_quantity' => 10,
                'price' => 25.00,
                'category' => 'Канцелярські товари'
            ],
            [
                'name' => 'Диск CD-R',
                'code' => 'CD-R-VERBATIM-700MB',
                'description' => 'CD-R диск Verbatim 700MB 80min 52x',
                'unit' => 'шт',
                'quantity' => 35,
                'min_quantity' => 20,
                'price' => 18.00,
                'category' => 'Носії інформації'
            ],
            [
                'name' => 'Миша комп\'ютерна',
                'code' => 'MOUSE-OPTICAL-USB',
                'description' => 'Миша комп\'ютерна оптична USB',
                'unit' => 'шт',
                'quantity' => 7,
                'min_quantity' => 5,
                'price' => 280.00,
                'category' => 'Комп\'ютерна техніка'
            ]
        ];
        
        foreach ($sampleItems as $itemData) {
            $item = WarehouseItem::firstOrCreate(
                ['code' => $itemData['code']],
                $itemData
            );
            
            $this->command->info("Товар создан/обновлен: {$item->name}");
        }
        
        $this->command->info("Создано товаров: " . count($sampleItems));
        
        // Выводим информацию для входа
        $this->command->info("\n=== Данные для входа складовщика ===");
        $this->command->info("Email: warehouse@" . parse_url(config('app.url'), PHP_URL_HOST));
        $this->command->info("Пароль: password");
        $this->command->info("Роль: Складовщик");
    }
}