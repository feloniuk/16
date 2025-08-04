<?php

namespace App\Services\Telegram;

use App\Services\Telegram\TelegramService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\KeyboardService;
use App\Services\Telegram\Handlers\RepairHandler;
use App\Services\Telegram\Handlers\CartridgeHandler;
use App\Services\Telegram\Handlers\InventoryHandler;
use App\Services\Telegram\Handlers\AdminHandler;
use Illuminate\Support\Facades\Log;

class MessageHandler
{
    private TelegramService $telegram;
    private StateManager $stateManager;
    private KeyboardService $keyboard;
    private RepairHandler $repairHandler;
    private CartridgeHandler $cartridgeHandler;
    private InventoryHandler $inventoryHandler;
    private AdminHandler $adminHandler;

    public function __construct(
        TelegramService $telegram,
        StateManager $stateManager,
        KeyboardService $keyboard,
        RepairHandler $repairHandler,
        CartridgeHandler $cartridgeHandler,
        InventoryHandler $inventoryHandler,
        AdminHandler $adminHandler
    ) {
        $this->telegram = $telegram;
        $this->stateManager = $stateManager;
        $this->keyboard = $keyboard;
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

        // Обработка по состоянию пользователя
        $userState = $this->stateManager->getUserState($userId);
        
        if ($userState && isset($userState['state'])) {
            $this->handleStateMessage($chatId, $userId, $username, $text, $userState);
        } else {
            $this->sendMainMenu($chatId, $userId);
        }
    }

    private function handleCommand(int $chatId, int $userId, ?string $username, string $command): void
    {
        Log::info("Handling command: {$command} for user: {$userId}");
        
        match ($command) {
            '/start' => $this->handleStartCommand($chatId, $userId, $username),
            '/help' => $this->handleHelpCommand($chatId, $userId),
            '/cancel' => $this->handleCancelCommand($chatId, $userId),
            '/admin' => $this->handleAdminCommand($chatId, $userId),
            '/status' => $this->handleStatusCommand($chatId),
            default => $this->handleUnknownCommand($chatId, $userId, $command)
        };
    }

    private function handleStartCommand(int $chatId, int $userId, ?string $username): void
    {
        $this->stateManager->clearUserState($userId);
        $this->sendWelcomeMessage($chatId, $userId, $username);
    }

    private function handleHelpCommand(int $chatId, int $userId): void
    {
        $text = "📋 <b>Справка по боту:</b>\n\n" .
               "🔧 <b>Вызов IT мастера</b> - подать заявку на ремонт оборудования\n" .
               "🖨️ <b>Замена картриджа</b> - запрос на замену картриджа\n\n" .
               "📞 <b>Команды:</b>\n" .
               "/start - Главное меню\n" .
               "/help - Эта справка\n" .
               "/cancel - Отменить текущее действие\n" .
               "/admin - Админ-панель (только для администраторов)\n" .
               "/status - Статистика системы\n\n" .
               "❓ Если у вас возникли вопросы, обратитесь к администратору.";
        
        $this->telegram->sendMessage($chatId, $text, $this->keyboard->getMainMenuKeyboard($userId));
    }

    private function handleCancelCommand(int $chatId, int $userId): void
    {
        $this->stateManager->clearUserState($userId);
        $this->telegram->sendMessage(
            $chatId, 
            "Действие отменено. Выберите новое действие:", 
            $this->keyboard->getMainMenuKeyboard($userId)
        );
    }

    private function handleAdminCommand(int $chatId, int $userId): void
    {
        if ($this->telegram->isAdmin($userId)) {
            $this->adminHandler->sendAdminMenu($chatId);
        } else {
            $this->telegram->sendMessage($chatId, "У вас нет прав администратора.");
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
            "Неизвестная команда: {$command}. Используйте /help для справки.", 
            $this->keyboard->getMainMenuKeyboard($userId)
        );
    }

    private function handleStateMessage(int $chatId, int $userId, ?string $username, string $text, array $userState): void
    {
        $state = $userState['state'];
        $tempData = $userState['temp_data'] ?? [];

        Log::info("Handling state message", ['state' => $state, 'user_id' => $userId]);

        match ($state) {
            // Repair states
            'repair_awaiting_room' => $this->repairHandler->handleRoomInput($chatId, $userId, $text),
            'repair_awaiting_description' => $this->repairHandler->handleDescriptionInput($chatId, $userId, $text),
            'repair_awaiting_phone' => $this->repairHandler->handlePhoneInput($chatId, $userId, $username, $text),

            // Cartridge states
            'cartridge_awaiting_room' => $this->cartridgeHandler->handleRoomInput($chatId, $userId, $text),
            'cartridge_awaiting_printer' => $this->cartridgeHandler->handlePrinterInput($chatId, $userId, $text),
            'cartridge_awaiting_type' => $this->cartridgeHandler->handleTypeInput($chatId, $userId, $username, $text),

            // Inventory states
            'inventory_room_input' => $this->inventoryHandler->handleRoomInput($chatId, $userId, $text),
            'inventory_equipment_type' => $this->inventoryHandler->handleEquipmentType($chatId, $userId, $text),
            'inventory_brand', 'inventory_quick_brand' => $this->inventoryHandler->handleBrand($chatId, $userId, $text),
            'inventory_model', 'inventory_quick_model' => $this->inventoryHandler->handleModel($chatId, $userId, $text),
            'inventory_serial', 'inventory_quick_serial' => $this->inventoryHandler->handleSerial($chatId, $userId, $text),
            'inventory_number' => $this->inventoryHandler->handleInventoryNumber($chatId, $userId, $username, $text),

            default => $this->handleUnknownState($chatId, $userId, $state)
        };
    }

    private function handleUnknownState(int $chatId, int $userId, string $state): void
    {
        Log::warning("Unknown user state: {$state} for user: {$userId}");
        $this->telegram->sendMessage(
            $chatId, 
            "Неизвестное состояние. Возвращаемся в главное меню.", 
            $this->keyboard->getMainMenuKeyboard($userId)
        );
        $this->stateManager->clearUserState($userId);
    }

    private function sendWelcomeMessage(int $chatId, int $userId, ?string $username): void
    {
        $name = $username ? "@$username" : "Пользователь";
        $text = "🤖 Добро пожаловать, $name!\n\n" .
               "Я бот для подачи заявок на ремонт оборудования и замены картриджей.\n\n" .
               "Что вы хотите сделать?";
        
        $this->telegram->sendMessage($chatId, $text, $this->keyboard->getMainMenuKeyboard($userId));
    }

    private function sendMainMenu(int $chatId, int $userId): void
    {
        $this->telegram->sendMessage(
            $chatId, 
            "Выберите действие из главного меню:", 
            $this->keyboard->getMainMenuKeyboard($userId)
        );
    }
}