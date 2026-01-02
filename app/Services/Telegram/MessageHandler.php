<?php

namespace App\Services\Telegram;

use App\Models\Branch;
use App\Services\Telegram\Handlers\AdminHandler;
use App\Services\Telegram\Handlers\CartridgeHandler;
use App\Services\Telegram\Handlers\InventoryHandler;
use App\Services\Telegram\Handlers\RepairHandler;
use Illuminate\Support\Facades\Log;

class MessageHandler
{
    private TelegramService $telegram;

    private StateManager $stateManager;

    private KeyboardService $keyboard;

    private ReplyKeyboardService $replyKeyboard;

    private RepairHandler $repairHandler;

    private CartridgeHandler $cartridgeHandler;

    private InventoryHandler $inventoryHandler;

    private AdminHandler $adminHandler;

    public function __construct(
        TelegramService $telegram,
        StateManager $stateManager,
        KeyboardService $keyboard,
        ReplyKeyboardService $replyKeyboard,
        RepairHandler $repairHandler,
        CartridgeHandler $cartridgeHandler,
        InventoryHandler $inventoryHandler,
        AdminHandler $adminHandler
    ) {
        $this->telegram = $telegram;
        $this->stateManager = $stateManager;
        $this->keyboard = $keyboard;
        $this->replyKeyboard = $replyKeyboard;
        $this->repairHandler = $repairHandler;
        $this->cartridgeHandler = $cartridgeHandler;
        $this->inventoryHandler = $inventoryHandler;
        $this->adminHandler = $adminHandler;
    }

    public function handle(array $message): void
    {
        $chatId = $message['chat']['id'];
        $userId = $message['from']['id'];
        $username = $message['from']['username'] ?? null;
        $text = $message['text'] ?? '';

        Log::info("Processing message from user {$userId}: {$text}");

        // Обработка команд
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $userId, $username, $text);

            return;
        }

        // Обработка кнопок reply keyboard (главное меню)
        if ($this->handleMenuButton($chatId, $userId, $username, $text)) {
            return;
        }

        // Обработка по состоянию пользователя
        $userState = $this->stateManager->getUserState($userId);

        if ($userState && isset($userState['state'])) {
            $this->handleStateMessage($chatId, $userId, $username, $text, $userState);
        } else {
            $this->sendMainMenu($chatId, $userId);
        }
    }

    private function handleMenuButton(int $chatId, int $userId, ?string $username, string $text): bool
    {
        Log::info("Checking menu button: {$text} for user: {$userId}");

        match ($text) {
            '🔧 Виклик IT майстра' => $this->handleRepairButton($chatId, $userId),
            '🖨️ Заміна картриджа' => $this->handleCartridgeButton($chatId, $userId),
            '📋 Керування інвентарем' => $this->handleInventoryButton($chatId, $userId),
            '⚙️ Панель адміністратора' => $this->adminHandler->sendAdminMenu($chatId),
            default => false,
        };

        return in_array($text, [
            '🔧 Виклик IT майстра',
            '🖨️ Заміна картриджа',
            '📋 Керування інвентарем',
            '⚙️ Панель адміністратора',
        ]);
    }

    private function handleRepairButton(int $chatId, int $userId): void
    {
        $this->stateManager->clearUserState($userId);
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            $this->telegram->sendMessage($chatId, '❌ На жаль, філіали недоступні. Зв\'яжіться з адміністратором.');

            return;
        }

        $this->stateManager->setUserState($userId, 'repair_awaiting_branch');

        $this->telegram->sendMessage(
            $chatId,
            "🔧 <b>Виклик IT майстра</b>\n\nОберіть філіал:",
            $this->keyboard->getBranchesKeyboard($branches, 'repair')
        );
    }

    private function handleCartridgeButton(int $chatId, int $userId): void
    {
        $this->stateManager->clearUserState($userId);
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            $this->telegram->sendMessage($chatId, '❌ На жаль, філіали недоступні. Зв\'яжіться з адміністратором.');

            return;
        }

        $this->stateManager->setUserState($userId, 'cartridge_awaiting_branch');

        $this->telegram->sendMessage(
            $chatId,
            "🖨️ <b>Заміна картриджа</b>\n\nОберіть філіал:",
            $this->keyboard->getBranchesKeyboard($branches, 'cartridge')
        );
    }

    private function handleInventoryButton(int $chatId, int $userId): void
    {
        $this->stateManager->clearUserState($userId);
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            $this->telegram->sendMessage($chatId, '❌ На жаль, філіали недоступні. Зв\'яжіться з адміністратором.');

            return;
        }

        $this->stateManager->setUserState($userId, 'inventory_branch_selection');

        $this->telegram->sendMessage(
            $chatId,
            "📋 <b>Керування інвентарем</b>\n\nОберіть філіал:",
            $this->keyboard->getInventoryBranchesKeyboard($branches)
        );
    }

    private function handleCommand(int $chatId, int $userId, ?string $username, string $command): void
    {
        Log::info("Handling command: {$command} for user: {$userId}");

        switch ($command) {
            case '/start':
                $this->handleStartCommand($chatId, $userId, $username);
                break;
            case '/help':
                $this->handleHelpCommand($chatId, $userId);
                break;
            case '/cancel':
                $this->handleCancelCommand($chatId, $userId);
                break;
            case '/admin':
                $this->handleAdminCommand($chatId, $userId);
                break;
            case '/status':
                $this->handleStatusCommand($chatId);
                break;
            default:
                $this->handleUnknownCommand($chatId, $userId, $command);
                break;
        }
    }

    private function handleStartCommand(int $chatId, int $userId, ?string $username): void
    {
        $this->stateManager->clearUserState($userId);
        $this->sendWelcomeMessage($chatId, $userId, $username);
    }

    private function handleHelpCommand(int $chatId, int $userId): void
    {
        $text = "📋 <b>Довідка:</b>\n\n".
               "🔧 <b>Виклик IT майстра</b> - подати заявку на ремонт обладнання\n".
               "🖨️ <b>Заміна картриджа</b> - запит на заміну картриджа\n\n".
               "📞 <b>Команди:</b>\n".
               "/start - Головне меню\n".
               "/help - Ця довідка\n".
               "/cancel - Скасувати поточну дію\n".
               "/admin - Панель адміністратора (тільки для адміністраторів)\n".
               "/status - Статистика системи\n\n".
               "❓ Якщо у вас виникли питання, зв\'яжіться з адміністратором.";

        $this->telegram->sendMessage($chatId, $text, $this->keyboard->getMainMenuKeyboard($userId));
    }

    private function handleCancelCommand(int $chatId, int $userId): void
    {
        $this->stateManager->clearUserState($userId);
        $this->telegram->sendMessage(
            $chatId,
            '✅ Дія скасована. Оберіть нову дію:',
            $this->keyboard->getMainMenuKeyboard($userId)
        );
    }

    private function handleAdminCommand(int $chatId, int $userId): void
    {
        if ($this->telegram->isAdmin($userId)) {
            $this->adminHandler->sendAdminMenu($chatId);
        } else {
            $this->telegram->sendMessage($chatId, '❌ У вас немає прав адміністратора.');
        }
    }

    private function handleStatusCommand(int $chatId): void
    {
        $this->adminHandler->sendSystemStatus($chatId);
    }

    private function handleUnknownCommand(int $chatId, int $userId, string $command): void
    {
        $this->telegram->sendMessage(
            $chatId,
            "❓ Невідома команда: {$command}. Використовуйте /help для довідки.",
            $this->keyboard->getMainMenuKeyboard($userId)
        );
    }

    private function handleStateMessage(int $chatId, int $userId, ?string $username, string $text, array $userState): void
    {
        $state = $userState['state'];
        $tempData = $userState['temp_data'] ?? [];

        Log::info('Handling state message', ['state' => $state, 'user_id' => $userId]);

        switch ($state) {
            // Repair states
            case 'repair_awaiting_room':
                $this->repairHandler->handleRoomInput($chatId, $userId, $text);
                break;
            case 'repair_awaiting_description':
                $this->repairHandler->handleDescriptionInput($chatId, $userId, $text);
                break;
            case 'repair_awaiting_phone':
                $this->repairHandler->handlePhoneInput($chatId, $userId, $username, $text);
                break;

                // Cartridge states
            case 'cartridge_awaiting_room':
                $this->cartridgeHandler->handleRoomInput($chatId, $userId, $text);
                break;
            case 'cartridge_awaiting_printer':
                $this->cartridgeHandler->handlePrinterInput($chatId, $userId, $text);
                break;
            case 'cartridge_awaiting_type':
                $this->cartridgeHandler->handleTypeInput($chatId, $userId, $username, $text);
                break;

                // Inventory states
            case 'inventory_room_input':
                $this->inventoryHandler->handleRoomInput($chatId, $userId, $text);
                break;
            case 'inventory_equipment_type':
                $this->inventoryHandler->handleEquipmentType($chatId, $userId, $text);
                break;
            case 'inventory_brand':
            case 'inventory_quick_brand':
                $this->inventoryHandler->handleBrand($chatId, $userId, $text);
                break;
            case 'inventory_model':
            case 'inventory_quick_model':
                $this->inventoryHandler->handleModel($chatId, $userId, $text);
                break;
            case 'inventory_serial':
            case 'inventory_quick_serial':
                $this->inventoryHandler->handleSerial($chatId, $userId, $text);
                break;
            case 'inventory_number':
                $this->inventoryHandler->handleInventoryNumber($chatId, $userId, $username, $text);
                break;

            default:
                $this->handleUnknownState($chatId, $userId, $state);
                break;
        }
    }

    private function handleUnknownState(int $chatId, int $userId, string $state): void
    {
        Log::warning("Unknown user state: {$state} for user: {$userId}");
        $this->telegram->sendMessage(
            $chatId,
            '❓ Невідомий стан. Повертаємося в головне меню.',
            $this->keyboard->getMainMenuKeyboard($userId)
        );
        $this->stateManager->clearUserState($userId);
    }

    private function sendWelcomeMessage(int $chatId, int $userId, ?string $username): void
    {
        $name = $username ? "@$username" : 'Користувач';
        $text = "🤖 Ласкаво просимо, $name!\n\n".
               "Я бот для подачі заявок на ремонт обладнання та заміни картриджів.\n\n".
               'Що ви хочете зробити?';

        $this->telegram->sendMessage($chatId, $text, $this->replyKeyboard->getMainMenuKeyboard());
    }

    private function sendMainMenu(int $chatId, int $userId): void
    {
        $this->telegram->sendMessage(
            $chatId,
            'Оберіть дію з головного меню:',
            $this->replyKeyboard->getMainMenuKeyboard()
        );
    }
}
