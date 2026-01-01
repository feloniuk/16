<?php

namespace App\Services\Telegram\Handlers;

use App\Models\Branch;
use App\Models\RoomInventory;
use App\Services\Telegram\KeyboardService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;

class InventoryHandler
{
    private TelegramService $telegram;

    private StateManager $stateManager;

    private KeyboardService $keyboard;

    public function __construct(
        TelegramService $telegram,
        StateManager $stateManager,
        KeyboardService $keyboard
    ) {
        $this->telegram = $telegram;
        $this->stateManager = $stateManager;
        $this->keyboard = $keyboard;
    }

    public function handleCallback(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $data = $callbackQuery['data'];

        $parts = explode(':', $data);
        $action = $parts[0];

        Log::info("Handling inventory callback: {$action}", ['user_id' => $userId]);

        // Проверяем права администратора для всех действий с инвентарем
        if (! $this->telegram->isAdmin($userId)) {
            $this->telegram->editMessage(
                $chatId,
                $messageId,
                '❌ У вас немає прав для керування інвентарем.'
            );

            return;
        }

        match ($action) {
            'inventory_management' => $this->startInventoryManagement($chatId, $userId, $messageId),
            'inventory_branch_select' => $this->handleBranchSelection($callbackQuery, (int) ($parts[1] ?? 0)),
            'inventory_add_equipment' => $this->startAddEquipment($chatId, $userId, $messageId),
            'inventory_show_room' => $this->showRoomInventory($chatId, $userId, $messageId),
            'inventory_quick_add' => $this->showQuickAddOptions($chatId, $userId, $messageId),
            'inventory_manual_add' => $this->startManualAdd($chatId, $userId, $messageId),
            'inventory_quick_type' => $this->handleQuickTypeSelection($chatId, $userId, $messageId, $parts[1] ?? ''),
            'inventory_brand_select' => $this->handleBrandSelection($chatId, $userId, $messageId, $parts[1] ?? ''),
            'inventory_skip_brand' => $this->handleBrandSelection($chatId, $userId, $messageId, ''),
            'inventory_manual_brand' => $this->setManualBrandInput($chatId, $userId, $messageId),
            'inventory_edit_list' => $this->showInventoryEditList($chatId, $userId, $messageId),
            'inventory_delete_list' => $this->showInventoryDeleteList($chatId, $userId, $messageId),
            'inventory_edit_item' => $this->showEditItemOptions($chatId, $messageId, (int) ($parts[1] ?? 0)),
            'inventory_delete_item' => $this->confirmDeleteItem($chatId, $messageId, (int) ($parts[1] ?? 0)),
            'inventory_confirm_delete' => $this->deleteInventoryItem($chatId, $messageId, (int) ($parts[1] ?? 0)),
            default => Log::warning("Unknown inventory action: {$action}")
        };
    }

    private function startInventoryManagement(int $chatId, int $userId, int $messageId): void
    {
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            $this->telegram->editMessage($chatId, $messageId, '❌ На жаль, філіали недоступні. Зв\'яжіться з адміністратором.');

            return;
        }

        $this->stateManager->setUserState($userId, 'inventory_branch_selection');

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "📋 <b>Керування інвентарем</b>\n\nОберіть філіал для інвентаризації:",
            $this->keyboard->getInventoryBranchesKeyboard($branches)
        );
    }

    public function handleBranchSelection(array $callbackQuery, int $branchId): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $messageId = $callbackQuery['message']['message_id'];

        $branch = Branch::find($branchId);
        if (! $branch) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Помилка: філіал не знайдено.');

            return;
        }

        $this->stateManager->setUserState($userId, 'inventory_room_input', [
            'branch_id' => $branchId,
            'branch_name' => $branch->name,
        ]);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "📋 <b>Інвентаризація</b>\nФіліал: <b>{$branch->name}</b>\n\nВведіть номер кабінету для інвентаризації:",
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handleRoomInput(int $chatId, int $userId, string $room): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($room)) || strlen($room) > 50) {
            $this->telegram->sendMessage($chatId, '❌ Введіть номер кабінету (до 50 символів):');

            return;
        }

        $tempData['room_number'] = trim($room);

        // Получаем существующий инвентарь в кабинете
        $existingInventory = RoomInventory::where('branch_id', $tempData['branch_id'])
            ->where('room_number', $tempData['room_number'])
            ->get();

        $this->stateManager->setUserState($userId, 'inventory_menu', $tempData);

        $message = "📋 <b>Інвентаризація кабінету</b>\n";
        $message .= "Філіал: <b>{$tempData['branch_name']}</b>\n";
        $message .= "Кабінет: <b>{$tempData['room_number']}</b>\n\n";

        if ($existingInventory->count() > 0) {
            $message .= "🏷️ <b>Знайдене обладнання ({$existingInventory->count()}):</b>\n";
            foreach ($existingInventory->take(5) as $item) {
                $message .= "• {$item->equipment_type}";
                if ($item->brand || $item->model) {
                    $message .= " ({$item->brand} {$item->model})";
                }
                $message .= " - {$item->inventory_number}\n";
            }
            if ($existingInventory->count() > 5) {
                $message .= '... та ще '.($existingInventory->count() - 5)."\n";
            }
            $message .= "\n";
        } else {
            $message .= "ℹ️ У цьому кабінету поки немає зареєстрованого обладнання.\n\n";
        }

        $message .= 'Оберіть дію:';

        $this->telegram->sendMessage($chatId, $message, $this->keyboard->getInventoryMenuKeyboard());
    }

    private function startAddEquipment(int $chatId, int $userId, int $messageId): void
    {
        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "📋 <b>Додавання обладнання</b>\n\nОберіть спосіб додавання:",
            $this->keyboard->getAddEquipmentKeyboard()
        );
    }

    private function showQuickAddOptions(int $chatId, int $userId, int $messageId): void
    {
        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "⚡ <b>Швидке додавання</b>\n\nОберіть тип обладнання:",
            $this->keyboard->getQuickAddKeyboard()
        );
    }

    private function handleQuickTypeSelection(int $chatId, int $userId, int $messageId, string $type): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];
        $tempData['equipment_type'] = $type;
        $tempData['quick_mode'] = true;

        $this->stateManager->setUserState($userId, 'inventory_quick_brand', $tempData);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "📋 <b>Додавання: $type</b>\n\nОберіть бренд або введіть вручну:",
            $this->keyboard->getPopularBrandsKeyboard($type)
        );
    }

    private function handleBrandSelection(int $chatId, int $userId, int $messageId, string $brand): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];
        $tempData['brand'] = $brand;

        $this->stateManager->setUserState($userId, 'inventory_quick_model', $tempData);

        $message = "📋 <b>Додавання: {$tempData['equipment_type']}</b>\n";
        if (! empty($brand)) {
            $message .= "Бренд: <b>$brand</b>\n";
        }
        $message .= "\nВведіть модель або натисніть /skip для пропуску:";

        $this->telegram->editMessage($chatId, $messageId, $message, $this->keyboard->getCancelKeyboard());
    }

    private function showRoomInventory(int $chatId, int $userId, int $messageId): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (! isset($tempData['branch_id'], $tempData['room_number'])) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Помилка: дані кабінету не знайдені.');

            return;
        }

        $inventory = RoomInventory::where('branch_id', $tempData['branch_id'])
            ->where('room_number', $tempData['room_number'])
            ->orderBy('equipment_type')
            ->orderBy('created_at', 'desc')
            ->get();

        $message = $this->buildInventoryMessage($tempData, $inventory);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            $message,
            $this->keyboard->getRoomInventoryKeyboard($inventory->count() > 0)
        );
    }

    private function buildInventoryMessage(array $tempData, $inventory): string
    {
        $message = "📋 <b>Інвентар кабінету</b>\n";
        $message .= "🏢 Філіал: <b>{$tempData['branch_name']}</b>\n";
        $message .= "🚪 Кабінет: <b>{$tempData['room_number']}</b>\n\n";

        if ($inventory->count() > 0) {
            $grouped = $inventory->groupBy('equipment_type');

            foreach ($grouped as $type => $items) {
                $emoji = $this->getEquipmentEmoji($type);
                $message .= "$emoji <b>$type ({$items->count()})</b>\n";

                foreach ($items->take(3) as $item) {
                    $info = [];
                    if ($item->brand) {
                        $info[] = $item->brand;
                    }
                    if ($item->model) {
                        $info[] = $item->model;
                    }
                    $infoStr = $info ? ' ('.implode(' ', $info).')' : '';

                    $message .= "  • {$item->inventory_number}$infoStr\n";
                }

                if ($items->count() > 3) {
                    $message .= '  ... та ще '.($items->count() - 3)."\n";
                }
                $message .= "\n";
            }

            $message .= "📊 <b>Всього одиниць: {$inventory->count()}</b>";
        } else {
            $message .= 'ℹ️ У кабінету поки немає зареєстрованого обладнання.';
        }

        return $message;
    }

    public function createInventoryItem(int $chatId, int $userId, ?string $username, string $inventoryNumber, array $tempData): void
    {
        try {
            // Проверяем уникальность инвентарного номера
            $existing = RoomInventory::where('inventory_number', $inventoryNumber)->first();
            if ($existing) {
                $this->telegram->sendMessage($chatId, '❌ Інвентарний номер уже існує. Введіть інший номер:');

                return;
            }

            $inventory = RoomInventory::create([
                'admin_telegram_id' => $userId,
                'branch_id' => $tempData['branch_id'],
                'room_number' => $tempData['room_number'],
                'equipment_type' => $tempData['equipment_type'],
                'brand' => $tempData['brand'] ?: null,
                'model' => $tempData['model'] ?: null,
                'serial_number' => $tempData['serial_number'] ?: null,
                'inventory_number' => $inventoryNumber,
                'notes' => null,
            ]);

            $this->stateManager->clearUserState($userId);

            $message = "✅ <b>Обладнання додано!</b>\n\n";
            $message .= "📋 <b>Деталі:</b>\n";
            $message .= "🏢 Філіал: {$tempData['branch_name']}\n";
            $message .= "🚪 Кабінет: {$tempData['room_number']}\n";
            $message .= "🖥️ Тип: {$tempData['equipment_type']}\n";
            if ($tempData['brand']) {
                $message .= "🏭 Бренд: {$tempData['brand']}\n";
            }
            if ($tempData['model']) {
                $message .= "📱 Модель: {$tempData['model']}\n";
            }
            if ($tempData['serial_number']) {
                $message .= "🔢 S/N: {$tempData['serial_number']}\n";
            }
            $message .= "🏷️ Інв. №: {$inventoryNumber}\n";

            $this->telegram->sendMessage($chatId, $message, $this->keyboard->getMainMenuKeyboard($userId));

            Log::info('Inventory item created via Telegram', [
                'inventory_id' => $inventory->id,
                'user_id' => $userId,
                'branch_id' => $tempData['branch_id'],
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating inventory item: '.$e->getMessage());
            $this->telegram->sendMessage($chatId, '❌ Сталася помилка. Спробуйте пізніше або зв\'яжіться з адміністратором.');
            $this->stateManager->clearUserState($userId);
        }
    }

    private function startManualAdd(int $chatId, int $userId, int $messageId): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        $this->stateManager->setUserState($userId, 'inventory_equipment_type', $tempData);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "📋 <b>Ручне додавання обладнання</b>\n\nВведіть тип обладнання (наприклад: Компютер, Принтер, Монітор):",
            $this->keyboard->getCancelKeyboard()
        );
    }

    private function setManualBrandInput(int $chatId, int $userId, int $messageId): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        $this->stateManager->setUserState($userId, 'inventory_brand', $tempData);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "📋 <b>Додавання: {$tempData['equipment_type']}</b>\n\nВведіть бренд (виробника) або натисніть /skip для пропуску:",
            $this->keyboard->getCancelKeyboard()
        );
    }

    private function showInventoryEditList(int $chatId, int $userId, int $messageId): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (! isset($tempData['branch_id'], $tempData['room_number'])) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Помилка: дані кабінету не знайдені.');

            return;
        }

        $inventory = RoomInventory::where('branch_id', $tempData['branch_id'])
            ->where('room_number', $tempData['room_number'])
            ->orderBy('equipment_type')
            ->orderBy('inventory_number')
            ->get();

        if ($inventory->isEmpty()) {
            $this->telegram->editMessage(
                $chatId,
                $messageId,
                "📝 <b>Редагування інвентаря</b>\n\nУ кабінету немає обладнання для редагування.",
                $this->keyboard->getBackToRoomKeyboard()
            );

            return;
        }

        $message = "📝 <b>Оберіть обладнання для редагування:</b>\n\n";
        $message .= "🏢 Філіал: <b>{$tempData['branch_name']}</b>\n";
        $message .= "🚪 Кабінет: <b>{$tempData['room_number']}</b>\n\n";

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            $message,
            $this->keyboard->getEditListKeyboard($inventory)
        );
    }

    private function showInventoryDeleteList(int $chatId, int $userId, int $messageId): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (! isset($tempData['branch_id'], $tempData['room_number'])) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Помилка: дані кабінету не знайдені.');

            return;
        }

        $inventory = RoomInventory::where('branch_id', $tempData['branch_id'])
            ->where('room_number', $tempData['room_number'])
            ->orderBy('equipment_type')
            ->orderBy('inventory_number')
            ->get();

        if ($inventory->isEmpty()) {
            $this->telegram->editMessage(
                $chatId,
                $messageId,
                "🗑️ <b>Видалення інвентаря</b>\n\nУ кабінету немає обладнання для видалення.",
                $this->keyboard->getBackToRoomKeyboard()
            );

            return;
        }

        $message = "🗑️ <b>Оберіть обладнання для видалення:</b>\n\n";
        $message .= "🏢 Філіал: <b>{$tempData['branch_name']}</b>\n";
        $message .= "🚪 Кабінет: <b>{$tempData['room_number']}</b>\n\n";
        $message .= "⚠️ <b>Увага:</b> видалення неможливо скасувати!\n\n";

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            $message,
            $this->keyboard->getDeleteListKeyboard($inventory)
        );
    }

    private function showEditItemOptions(int $chatId, int $messageId, int $itemId): void
    {
        $item = RoomInventory::find($itemId);

        if (! $item) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Обладнання не знайдено.');

            return;
        }

        $message = "✏️ <b>Редагування обладнання</b>\n\n";
        $message .= "📦 <b>Тип:</b> {$item->equipment_type}\n";
        $message .= '🏭 <b>Бренд:</b> '.($item->brand ?: 'Не вказано')."\n";
        $message .= '📱 <b>Модель:</b> '.($item->model ?: 'Не вказана')."\n";
        $message .= '🔢 <b>S/N:</b> '.($item->serial_number ?: 'Не вказано')."\n";
        $message .= "🏷️ <b>Інв. №:</b> {$item->inventory_number}\n";

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            $message,
            $this->keyboard->getEditItemKeyboard($itemId)
        );
    }

    private function confirmDeleteItem(int $chatId, int $messageId, int $itemId): void
    {
        $item = RoomInventory::find($itemId);

        if (! $item) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Обладнання не знайдено.');

            return;
        }

        $message = "🗑️ <b>Видалення обладнання</b>\n\n";
        $message .= "❗ Ви впевнені, що хочете видалити:\n\n";
        $message .= "📦 <b>{$item->equipment_type}</b>\n";

        if ($item->brand || $item->model) {
            $info = [];
            if ($item->brand) {
                $info[] = $item->brand;
            }
            if ($item->model) {
                $info[] = $item->model;
            }
            $message .= '🏭 '.implode(' ', $info)."\n";
        }

        $message .= "🏷️ <b>{$item->inventory_number}</b>\n\n";
        $message .= '⚠️ <b>Це дію неможливо скасувати!</b>';

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            $message,
            $this->keyboard->getConfirmDeleteKeyboard($itemId)
        );
    }

    private function deleteInventoryItem(int $chatId, int $messageId, int $itemId): void
    {
        try {
            $item = RoomInventory::find($itemId);

            if (! $item) {
                $this->telegram->editMessage($chatId, $messageId, '❌ Обладнання не знайдено.');

                return;
            }

            $itemInfo = $item->equipment_type.' ('.$item->inventory_number.')';
            $item->delete();

            Log::info('Inventory item deleted via Telegram', [
                'item_id' => $itemId,
                'item_info' => $itemInfo,
                'deleted_by' => $chatId,
            ]);

            $this->telegram->editMessage(
                $chatId,
                $messageId,
                "✅ <b>Обладнання видалено</b>\n\n🗑️ Видалено: <b>$itemInfo</b>"
            );

        } catch (\Exception $e) {
            Log::error('Error deleting inventory item: '.$e->getMessage());
            $this->telegram->editMessage($chatId, $messageId, '❌ Сталася помилка при видаленні. Спробуйте пізніше.');
        }
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

    // Методы для обработки текстовых сообщений
    public function handleEquipmentType(int $chatId, int $userId, string $equipmentType): void
    {
        if (empty(trim($equipmentType)) || strlen($equipmentType) > 100) {
            $this->telegram->sendMessage($chatId, '❌ Невірний тип обладнання. Введіть тип (до 100 символів):');

            return;
        }

        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];
        $tempData['equipment_type'] = trim($equipmentType);

        $this->stateManager->setUserState($userId, 'inventory_brand', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "📋 <b>Додавання обладнання</b>\nТип: <b>{$tempData['equipment_type']}</b>\n\nВведіть бренд (виробника) або натисніть /skip для пропуску:"
        );
    }

    public function handleBrand(int $chatId, int $userId, string $brand): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if ($brand === '/skip') {
            $tempData['brand'] = '';
        } else {
            $tempData['brand'] = trim($brand);
        }

        $this->stateManager->setUserState($userId, 'inventory_model', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "📋 <b>Додавання обладнання</b>\n".
            "Тип: <b>{$tempData['equipment_type']}</b>\n".
            'Бренд: <b>'.($tempData['brand'] ?: 'Не вказано')."</b>\n\n".
            'Введіть модель або натисніть /skip для пропуску:'
        );
    }

    public function handleModel(int $chatId, int $userId, string $model): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if ($model === '/skip') {
            $tempData['model'] = '';
        } else {
            $tempData['model'] = trim($model);
        }

        $this->stateManager->setUserState($userId, 'inventory_serial', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "📋 <b>Додавання обладнання</b>\n".
            "Тип: <b>{$tempData['equipment_type']}</b>\n".
            'Бренд: <b>'.($tempData['brand'] ?: 'Не вказано')."</b>\n".
            'Модель: <b>'.($tempData['model'] ?: 'Не вказана')."</b>\n\n".
            'Введіть серійний номер або натисніть /skip для пропуску:'
        );
    }

    public function handleSerial(int $chatId, int $userId, string $serial): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if ($serial === '/skip') {
            $tempData['serial_number'] = '';
        } else {
            $tempData['serial_number'] = trim($serial);
        }

        $this->stateManager->setUserState($userId, 'inventory_number', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "📋 <b>Додавання обладнання</b>\n".
            "Тип: <b>{$tempData['equipment_type']}</b>\n".
            'Серійний номер: <b>'.($tempData['serial_number'] ?: 'Не вказано')."</b>\n\n".
            'Введіть інвентарний номер:'
        );
    }

    public function handleInventoryNumber(int $chatId, int $userId, ?string $username, string $inventoryNumber): void
    {
        $inventoryNumber = trim($inventoryNumber);

        if (empty($inventoryNumber)) {
            $this->telegram->sendMessage($chatId, '❌ Інвентарний номер обов\'язковий. Введіть інвентарний номер:');

            return;
        }

        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        $this->createInventoryItem($chatId, $userId, $username, $inventoryNumber, $tempData);
    }
}
