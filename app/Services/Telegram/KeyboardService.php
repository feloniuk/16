<?php

namespace App\Services\Telegram;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;

class KeyboardService
{
    private TelegramService $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function getMainMenuKeyboard(int $userId): array
    {
        $keyboard = [
            [
                ['text' => '🔧 Вызов IT мастера', 'callback_data' => 'repair_request']
            ],
            [
                ['text' => '🖨️ Замена картриджа', 'callback_data' => 'cartridge_request']
            ]
        ];

        // Добавляем админ кнопки только для администраторов
        if ($this->telegram->isAdmin($userId)) {
            $keyboard[] = [
                ['text' => '📋 Управление инвентарем', 'callback_data' => 'inventory_management']
            ];
            $keyboard[] = [
                ['text' => '⚙️ Админ-панель', 'callback_data' => 'admin_menu']
            ];
        }

        return ['inline_keyboard' => $keyboard];
    }

    public function getCancelKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '❌ Отмена', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    public function getPhoneKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '⏭️ Пропустить', 'callback_data' => 'skip_phone']
                ],
                [
                    ['text' => '❌ Отмена', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    public function getBranchesKeyboard(Collection $branches, string $type = 'repair'): array
    {
        $keyboard = [];
        
        foreach ($branches as $branch) {
            $keyboard[] = [
                ['text' => $branch->name, 'callback_data' => "branch_select:{$branch->id}"]
            ];
        }
        
        $keyboard[] = [
            ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
        ];
        
        return ['inline_keyboard' => $keyboard];
    }

    // === INVENTORY KEYBOARDS ===

    public function getInventoryBranchesKeyboard(Collection $branches): array
    {
        $keyboard = [];
        
        foreach ($branches as $branch) {
            $keyboard[] = [
                ['text' => $branch->name, 'callback_data' => "inventory_branch_select:{$branch->id}"]
            ];
        }
        
        $keyboard[] = [
            ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
        ];
        
        return ['inline_keyboard' => $keyboard];
    }

    public function getInventoryMenuKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '➕ Добавить оборудование', 'callback_data' => 'inventory_add_equipment']
                ],
                [
                    ['text' => '📝 Показать все в кабинете', 'callback_data' => 'inventory_show_room']
                ],
                [
                    ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    public function getAddEquipmentKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '⚡ Быстрое добавление', 'callback_data' => 'inventory_quick_add']
                ],
                [
                    ['text' => '✏️ Ручной ввод', 'callback_data' => 'inventory_manual_add']
                ],
                [
                    ['text' => '◀️ Назад к кабинету', 'callback_data' => 'inventory_show_room']
                ]
            ]
        ];
    }

    public function getQuickAddKeyboard(): array
    {
        $commonTypes = [
            'Компьютер' => '💻',
            'Монитор' => '🖥️',
            'Принтер' => '🖨️',
            'Клавиатура' => '⌨️',
            'Мышь' => '🖱️',
            'Телефон' => '📞',
            'Сканер' => '📠',
            'УПС' => '🔋'
        ];

        $keyboard = [];
        foreach ($commonTypes as $type => $emoji) {
            $keyboard[] = [
                ['text' => "$emoji $type", 'callback_data' => "inventory_quick_type:$type"]
            ];
        }

        $keyboard[] = [
            ['text' => '✏️ Другой тип', 'callback_data' => 'inventory_manual_add']
        ];
        $keyboard[] = [
            ['text' => '◀️ Назад', 'callback_data' => 'inventory_add_equipment']
        ];

        return ['inline_keyboard' => $keyboard];
    }

    public function getPopularBrandsKeyboard(string $equipmentType): array
    {
        $brands = $this->getPopularBrands($equipmentType);
        
        $keyboard = [];
        foreach ($brands as $brand) {
            $keyboard[] = [
                ['text' => $brand, 'callback_data' => "inventory_brand_select:$brand"]
            ];
        }

        $keyboard[] = [
            ['text' => '✏️ Ввести вручную', 'callback_data' => 'inventory_manual_brand']
        ];
        $keyboard[] = [
            ['text' => '⏭️ Пропустить', 'callback_data' => 'inventory_skip_brand']
        ];

        return ['inline_keyboard' => $keyboard];
    }

    public function getRoomInventoryKeyboard(bool $hasItems = false): array
    {
        $keyboard = [
            [
                ['text' => '➕ Добавить оборудование', 'callback_data' => 'inventory_add_equipment']
            ]
        ];

        if ($hasItems) {
            $keyboard[] = [
                ['text' => '📝 Редактировать', 'callback_data' => 'inventory_edit_list'],
                ['text' => '🗑️ Удалить', 'callback_data' => 'inventory_delete_list']
            ];
            $keyboard[] = [
                ['text' => '📊 Экспорт QR', 'callback_data' => 'inventory_export_qr'],
                ['text' => '📋 Отчет', 'callback_data' => 'inventory_report']
            ];
        }

        $keyboard[] = [
            ['text' => '🔄 Обновить', 'callback_data' => 'inventory_show_room']
        ];
        $keyboard[] = [
            ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
        ];

        return ['inline_keyboard' => $keyboard];
    }

    public function getBackToRoomKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '◀️ Назад к списку', 'callback_data' => 'inventory_show_room']
                ]
            ]
        ];
    }

    public function getEditListKeyboard($inventory): array
    {
        $keyboard = [];
        
        foreach ($inventory as $item) {
            $emoji = $this->getEquipmentEmoji($item->equipment_type);
            $info = $item->brand && $item->model ? " ({$item->brand} {$item->model})" : "";
            $text = "$emoji {$item->equipment_type}$info - {$item->inventory_number}";
            
            if (strlen($text) > 45) {
                $text = substr($text, 0, 42) . '...';
            }
            
            $keyboard[] = [
                ['text' => $text, 'callback_data' => "inventory_edit_item:{$item->id}"]
            ];
        }

        $keyboard[] = [
            ['text' => '◀️ Назад к списку', 'callback_data' => 'inventory_show_room']
        ];

        return ['inline_keyboard' => $keyboard];
    }

    public function getDeleteListKeyboard($inventory): array
    {
        $keyboard = [];
        
        foreach ($inventory as $item) {
            $emoji = $this->getEquipmentEmoji($item->equipment_type);
            $info = $item->brand && $item->model ? " ({$item->brand} {$item->model})" : "";
            $text = "$emoji {$item->equipment_type}$info - {$item->inventory_number}";
            
            if (strlen($text) > 45) {
                $text = substr($text, 0, 42) . '...';
            }
            
            $keyboard[] = [
                ['text' => $text, 'callback_data' => "inventory_delete_item:{$item->id}"]
            ];
        }

        $keyboard[] = [
            ['text' => '◀️ Назад к списку', 'callback_data' => 'inventory_show_room']
        ];

        return ['inline_keyboard' => $keyboard];
    }

    public function getEditItemKeyboard(int $itemId): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '✏️ Изменить тип', 'callback_data' => "inventory_edit_field:{$itemId}:type"],
                    ['text' => '🏭 Изменить бренд', 'callback_data' => "inventory_edit_field:{$itemId}:brand"]
                ],
                [
                    ['text' => '📱 Изменить модель', 'callback_data' => "inventory_edit_field:{$itemId}:model"],
                    ['text' => '🔢 Изменить S/N', 'callback_data' => "inventory_edit_field:{$itemId}:serial"]
                ],
                [
                    ['text' => '🏷️ Изменить инв. №', 'callback_data' => "inventory_edit_field:{$itemId}:inventory"]
                ],
                [
                    ['text' => '🗑️ Удалить', 'callback_data' => "inventory_delete_item:{$itemId}"],
                    ['text' => '◀️ Назад', 'callback_data' => 'inventory_show_room']
                ]
            ]
        ];
    }

    public function getConfirmDeleteKeyboard(int $itemId): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Да, удалить', 'callback_data' => "inventory_confirm_delete:{$itemId}"],
                    ['text' => '❌ Отмена', 'callback_data' => "inventory_edit_item:{$itemId}"]
                ]
            ]
        ];
    }

    // === ADMIN KEYBOARDS ===

    public function getAdminMenuKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '📊 Заявки на ремонт', 'callback_data' => 'admin_repairs'],
                    ['text' => '🖨️ История картриджей', 'callback_data' => 'admin_cartridges']
                ],
                [
                    ['text' => '📦 Управление инвентарем', 'callback_data' => 'admin_inventory']
                ],
                [
                    ['text' => '📈 Статистика', 'callback_data' => 'admin_stats']
                ],
                [
                    ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    public function getRepairsListKeyboard($repairs): array
    {
        $keyboard = [];
        
        foreach ($repairs->take(5) as $repair) {
            $status = $this->getStatusEmoji($repair->status);
            $text = "#{$repair->id} $status {$repair->branch->name}";
            if (strlen($text) > 40) {
                $text = substr($text, 0, 37) . '...';
            }
            $keyboard[] = [
                ['text' => $text, 'callback_data' => "repair_details:{$repair->id}"]
            ];
        }
        
        $keyboard[] = [
            ['text' => '🔄 Обновить', 'callback_data' => 'admin_repairs'],
            ['text' => '◀️ Админ-панель', 'callback_data' => 'admin_menu']
        ];
        
        return ['inline_keyboard' => $keyboard];
    }

    public function getRepairDetailsKeyboard($repair): array
    {
        $keyboard = [];
        
        // Кнопки смены статуса
        if ($repair->status === 'нова') {
            $keyboard[] = [
                ['text' => '▶️ Взять в работу', 'callback_data' => "status_update:{$repair->id}:в_роботі"]
            ];
        } elseif ($repair->status === 'в_роботі') {
            $keyboard[] = [
                ['text' => '✅ Выполнено', 'callback_data' => "status_update:{$repair->id}:виконана"]
            ];
        }
        
        $keyboard[] = [
            ['text' => '◀️ К списку', 'callback_data' => 'admin_repairs'],
            ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
        ];
        
        return ['inline_keyboard' => $keyboard];
    }

    public function getBackKeyboard(string $backAction): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '◀️ Назад', 'callback_data' => $backAction]
                ],
                [
                    ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    // === HELPER METHODS ===

    private function getPopularBrands(string $equipmentType): array
    {
        $brands = [
            'Компьютер' => ['HP', 'Dell', 'Lenovo', 'ASUS', 'Acer'],
            'Монитор' => ['Samsung', 'LG', 'ASUS', 'Dell', 'HP'],
            'Принтер' => ['HP', 'Canon', 'Epson', 'Brother', 'Xerox'],
            'Клавиатура' => ['Logitech', 'Microsoft', 'A4Tech', 'HP', 'Dell'],
            'Мышь' => ['Logitech', 'Microsoft', 'A4Tech', 'HP', 'Dell'],
            'Телефон' => ['Cisco', 'Panasonic', 'Yealink', 'Gigaset'],
            'Сканер' => ['Canon', 'Epson', 'HP', 'Brother'],
            'УПС' => ['APC', 'CyberPower', 'Eaton', 'Powercom']
        ];

        return $brands[$equipmentType] ?? ['HP', 'Dell', 'Canon', 'Другой'];
    }

    private function getEquipmentEmoji(string $type): string
    {
        $emojis = [
            'Компьютер' => '💻',
            'Монитор' => '🖥️', 
            'Принтер' => '🖨️',
            'Клавиатура' => '⌨️',
            'Мышь' => '🖱️',
            'Телефон' => '📞',
            'Сканер' => '📠',
            'УПС' => '🔋',
        ];

        return $emojis[$type] ?? '📦';
    }

    private function getStatusEmoji(string $status): string
    {
        return match($status) {
            'нова' => '🆕',
            'в_роботі' => '⚙️',
            'виконана' => '✅',
            default => '❓'
        };
    }
}