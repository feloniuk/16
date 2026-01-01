<?php

namespace App\Services\Telegram;

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
                ['text' => '🔧 Виклик IT майстра', 'callback_data' => 'repair_request'],
            ],
            [
                ['text' => '🖨️ Заміна картриджа', 'callback_data' => 'cartridge_request'],
            ],
        ];

        // Додаємо кнопки адміністратора тільки для адміністраторів
        if ($this->telegram->isAdmin($userId)) {
            $keyboard[] = [
                ['text' => '📋 Керування інвентарем', 'callback_data' => 'inventory_management'],
            ];
            $keyboard[] = [
                ['text' => '⚙️ Панель адміністратора', 'callback_data' => 'admin_menu'],
            ];
        }

        return $this->createInlineKeyboard($keyboard);
    }

    public function getCancelKeyboard(): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '❌ Скасування', 'callback_data' => 'main_menu'],
            ],
        ]);
    }

    public function getPhoneKeyboard(): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '⏭️ Пропустити', 'callback_data' => 'skip_phone'],
            ],
            [
                ['text' => '❌ Скасування', 'callback_data' => 'main_menu'],
            ],
        ]);
    }

    public function getBranchesKeyboard(Collection $branches, string $type = 'repair'): array
    {
        $keyboard = [];

        foreach ($branches as $branch) {
            // Обмежуємо довжину назви кнопки
            $buttonText = $this->truncateText($branch->name, 30);
            $keyboard[] = [
                ['text' => $buttonText, 'callback_data' => "branch_select:{$branch->id}"],
            ];
        }

        $keyboard[] = [
            ['text' => '🏠 Головне меню', 'callback_data' => 'main_menu'],
        ];

        return $this->createInlineKeyboard($keyboard);
    }

    // === INVENTORY KEYBOARDS ===

    public function getInventoryBranchesKeyboard(Collection $branches): array
    {
        $keyboard = [];

        foreach ($branches as $branch) {
            $buttonText = $this->truncateText($branch->name, 30);
            $keyboard[] = [
                ['text' => $buttonText, 'callback_data' => "inventory_branch_select:{$branch->id}"],
            ];
        }

        $keyboard[] = [
            ['text' => '🏠 Головне меню', 'callback_data' => 'main_menu'],
        ];

        return $this->createInlineKeyboard($keyboard);
    }

    public function getInventoryMenuKeyboard(): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '➕ Додати обладнання', 'callback_data' => 'inventory_add_equipment'],
            ],
            [
                ['text' => '📝 Показати все в кабінету', 'callback_data' => 'inventory_show_room'],
            ],
            [
                ['text' => '🏠 Головне меню', 'callback_data' => 'main_menu'],
            ],
        ]);
    }

    public function getAddEquipmentKeyboard(): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '⚡ Швидке додавання', 'callback_data' => 'inventory_quick_add'],
            ],
            [
                ['text' => '✏️ Ручний ввід', 'callback_data' => 'inventory_manual_add'],
            ],
            [
                ['text' => '◀️ Назад до кабінету', 'callback_data' => 'inventory_show_room'],
            ],
        ]);
    }

    public function getQuickAddKeyboard(): array
    {
        $commonTypes = [
            'Комп\'ютер' => '💻',
            'Монітор' => '🖥️',
            'Принтер' => '🖨️',
            'Клавіатура' => '⌨️',
            'Миша' => '🖱️',
            'Телефон' => '📞',
            'Сканер' => '📠',
            'ДБЖ' => '🔋',
        ];

        $keyboard = [];
        foreach ($commonTypes as $type => $emoji) {
            $keyboard[] = [
                ['text' => "$emoji $type", 'callback_data' => 'inventory_quick_type:'.urlencode($type)],
            ];
        }

        $keyboard[] = [
            ['text' => '✏️ Інший тип', 'callback_data' => 'inventory_manual_add'],
        ];
        $keyboard[] = [
            ['text' => '◀️ Назад', 'callback_data' => 'inventory_add_equipment'],
        ];

        return $this->createInlineKeyboard($keyboard);
    }

    public function getPopularBrandsKeyboard(string $equipmentType): array
    {
        $brands = $this->getPopularBrands($equipmentType);

        $keyboard = [];
        foreach ($brands as $brand) {
            $keyboard[] = [
                ['text' => $brand, 'callback_data' => 'inventory_brand_select:'.urlencode($brand)],
            ];
        }

        $keyboard[] = [
            ['text' => '✏️ Ввести вручну', 'callback_data' => 'inventory_manual_brand'],
        ];
        $keyboard[] = [
            ['text' => '⏭️ Пропустити', 'callback_data' => 'inventory_skip_brand'],
        ];

        return $this->createInlineKeyboard($keyboard);
    }

    public function getRoomInventoryKeyboard(bool $hasItems = false): array
    {
        $keyboard = [
            [
                ['text' => '➕ Додати обладнання', 'callback_data' => 'inventory_add_equipment'],
            ],
        ];

        if ($hasItems) {
            $keyboard[] = [
                ['text' => '📝 Редагувати', 'callback_data' => 'inventory_edit_list'],
                ['text' => '🗑️ Видалити', 'callback_data' => 'inventory_delete_list'],
            ];
        }

        $keyboard[] = [
            ['text' => '🔄 Оновити', 'callback_data' => 'inventory_show_room'],
        ];
        $keyboard[] = [
            ['text' => '🏠 Головне меню', 'callback_data' => 'main_menu'],
        ];

        return $this->createInlineKeyboard($keyboard);
    }

    public function getBackToRoomKeyboard(): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '◀️ Назад до списку', 'callback_data' => 'inventory_show_room'],
            ],
        ]);
    }

    public function getEditListKeyboard($inventory): array
    {
        $keyboard = [];

        foreach ($inventory as $item) {
            $emoji = $this->getEquipmentEmoji($item->equipment_type);
            $info = $item->brand && $item->model ? " ({$item->brand} {$item->model})" : '';
            $text = "$emoji {$item->equipment_type}$info - {$item->inventory_number}";

            // Обмежуємо довжину тексту кнопки
            $text = $this->truncateText($text, 45);

            $keyboard[] = [
                ['text' => $text, 'callback_data' => "inventory_edit_item:{$item->id}"],
            ];
        }

        $keyboard[] = [
            ['text' => '◀️ Назад до списку', 'callback_data' => 'inventory_show_room'],
        ];

        return $this->createInlineKeyboard($keyboard);
    }

    public function getDeleteListKeyboard($inventory): array
    {
        $keyboard = [];

        foreach ($inventory as $item) {
            $emoji = $this->getEquipmentEmoji($item->equipment_type);
            $info = $item->brand && $item->model ? " ({$item->brand} {$item->model})" : '';
            $text = "$emoji {$item->equipment_type}$info - {$item->inventory_number}";

            $text = $this->truncateText($text, 45);

            $keyboard[] = [
                ['text' => $text, 'callback_data' => "inventory_delete_item:{$item->id}"],
            ];
        }

        $keyboard[] = [
            ['text' => '◀️ Назад до списку', 'callback_data' => 'inventory_show_room'],
        ];

        return $this->createInlineKeyboard($keyboard);
    }

    public function getEditItemKeyboard(int $itemId): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '✏️ Змінити тип', 'callback_data' => "inventory_edit_field:{$itemId}:type"],
                ['text' => '🏭 Змінити бренд', 'callback_data' => "inventory_edit_field:{$itemId}:brand"],
            ],
            [
                ['text' => '📱 Змінити модель', 'callback_data' => "inventory_edit_field:{$itemId}:model"],
                ['text' => '🔢 Змінити S/N', 'callback_data' => "inventory_edit_field:{$itemId}:serial"],
            ],
            [
                ['text' => '🏷️ Змінити інв. №', 'callback_data' => "inventory_edit_field:{$itemId}:inventory"],
            ],
            [
                ['text' => '🗑️ Видалити', 'callback_data' => "inventory_delete_item:{$itemId}"],
                ['text' => '◀️ Назад', 'callback_data' => 'inventory_show_room'],
            ],
        ]);
    }

    public function getConfirmDeleteKeyboard(int $itemId): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '✅ Так, видалити', 'callback_data' => "inventory_confirm_delete:{$itemId}"],
                ['text' => '❌ Скасування', 'callback_data' => "inventory_edit_item:{$itemId}"],
            ],
        ]);
    }

    // === ADMIN KEYBOARDS ===

    public function getAdminMenuKeyboard(): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '📊 Заявки на ремонт', 'callback_data' => 'admin_repairs'],
                ['text' => '🖨️ Історія картриджів', 'callback_data' => 'admin_cartridges'],
            ],
            [
                ['text' => '📦 Керування інвентарем', 'callback_data' => 'admin_inventory'],
            ],
            [
                ['text' => '📈 Статистика', 'callback_data' => 'admin_stats'],
            ],
            [
                ['text' => '🏠 Головне меню', 'callback_data' => 'main_menu'],
            ],
        ]);
    }

    public function getRepairsListKeyboard($repairs): array
    {
        $keyboard = [];

        foreach ($repairs->take(5) as $repair) {
            $status = $this->getStatusEmoji($repair->status);
            $text = "#{$repair->id} $status ".$this->truncateText($repair->branch->name, 25);

            $keyboard[] = [
                ['text' => $text, 'callback_data' => "repair_details:{$repair->id}"],
            ];
        }

        $keyboard[] = [
            ['text' => '🔄 Оновити', 'callback_data' => 'admin_repairs'],
            ['text' => '◀️ Панель адміністратора', 'callback_data' => 'admin_menu'],
        ];

        return $this->createInlineKeyboard($keyboard);
    }

    public function getRepairDetailsKeyboard($repair): array
    {
        $keyboard = [];

        // Кнопки зміни статусу
        if ($repair->status === 'нова') {
            $keyboard[] = [
                ['text' => '▶️ Взяти в роботу', 'callback_data' => "status_update:{$repair->id}:в_роботі"],
            ];
        } elseif ($repair->status === 'в_роботі') {
            $keyboard[] = [
                ['text' => '✅ Виконано', 'callback_data' => "status_update:{$repair->id}:виконана"],
            ];
        }

        $keyboard[] = [
            ['text' => '◀️ До списку', 'callback_data' => 'admin_repairs'],
            ['text' => '🏠 Головне меню', 'callback_data' => 'main_menu'],
        ];

        return $this->createInlineKeyboard($keyboard);
    }

    public function getBackKeyboard(string $backAction): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '◀️ Назад', 'callback_data' => $backAction],
            ],
            [
                ['text' => '🏠 Головне меню', 'callback_data' => 'main_menu'],
            ],
        ]);
    }

    public function hoHomeKeyboard(string $backAction): array
    {
        return $this->createInlineKeyboard([
            [
                ['text' => '◀️ Назад', 'callback_data' => $backAction],
            ],
            [
                ['text' => '🏠 Головне меню', 'callback_data' => 'main_menu'],
            ],
        ]);
    }

    // === HELPER METHODS ===

    /**
     * Створює структуру inline клавіатури з валідацією
     */
    private function createInlineKeyboard(array $keyboard): array
    {
        // Валідуємо кожну кнопку
        foreach ($keyboard as &$row) {
            foreach ($row as &$button) {
                // Перевіряємо обов'язкові поля
                if (! isset($button['text']) || ! isset($button['callback_data'])) {
                    \Log::error('Invalid button structure', ['button' => $button]);

                    continue;
                }

                // Очищуємо текст кнопки
                $button['text'] = $this->sanitizeButtonText($button['text']);

                // Обмежуємо довжину callback_data (максимум 64 байти в Telegram)
                if (strlen($button['callback_data']) > 64) {
                    $button['callback_data'] = substr($button['callback_data'], 0, 64);
                    \Log::warning('Callback data truncated', ['original' => $button['callback_data']]);
                }
            }
        }

        return ['inline_keyboard' => $keyboard];
    }

    /**
     * Очистка тексту кнопки
     */
    private function sanitizeButtonText(string $text): string
    {
        // Видаляємо проблематичні символи
        $text = str_replace(["\0", "\r", "\n", "\t"], '', $text);

        // Конвертуємо в UTF-8
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        return trim($text);
    }

    /**
     * Обрізає текст до вказаної довжини
     */
    private function truncateText(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length - 3).'...';
    }

    private function getPopularBrands(string $equipmentType): array
    {
        $brands = [
            'Комп\'ютер' => ['HP', 'Dell', 'Lenovo', 'ASUS', 'Acer'],
            'Монітор' => ['Samsung', 'LG', 'ASUS', 'Dell', 'HP'],
            'Принтер' => ['HP', 'Canon', 'Epson', 'Brother', 'Xerox'],
            'Клавіатура' => ['Logitech', 'Microsoft', 'A4Tech', 'HP', 'Dell'],
            'Миша' => ['Logitech', 'Microsoft', 'A4Tech', 'HP', 'Dell'],
            'Телефон' => ['Cisco', 'Panasonic', 'Yealink', 'Gigaset'],
            'Сканер' => ['Canon', 'Epson', 'HP', 'Brother'],
            'ДБЖ' => ['APC', 'CyberPower', 'Eaton', 'Powercom'],
        ];

        return $brands[$equipmentType] ?? ['HP', 'Dell', 'Canon', 'Інше'];
    }

    private function getEquipmentEmoji(string $type): string
    {
        $emojis = [
            'Комп\'ютер' => '💻',
            'Монітор' => '🖥️',
            'Принтер' => '🖨️',
            'Клавіатура' => '⌨️',
            'Миша' => '🖱️',
            'Телефон' => '📞',
            'Сканер' => '📠',
            'ДБЖ' => '🔋',
        ];

        return $emojis[$type] ?? '📦';
    }

    private function getStatusEmoji(string $status): string
    {
        switch ($status) {
            case 'нова':
                return '🆕';
            case 'в_роботі':
                return '⚙️';
            case 'виконана':
                return '✅';
            default:
                return '❓';
        }
    }
}
