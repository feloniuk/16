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

class CallbackHandler
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

    public function handle(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $username = $callbackQuery['from']['username'] ?? null;
        $data = $callbackQuery['data'];
        $messageId = $callbackQuery['message']['message_id'];

        Log::info("Processing callback from user {$userId}: {$data}");

        // Подтверждение получения callback
        $this->telegram->answerCallbackQuery($callbackQuery['id']);

        // Парсим данные callback'а
        $parts = explode(':', $data);
        $action = $parts[0];

        // Маршрутизация по типу действия
        try {
            match (true) {
                $action === 'main_menu' => $this->handleMainMenu($chatId, $userId, $messageId),
                
                // Ремонтные заявки
                str_starts_with($data, 'repair_') => $this->repairHandler->handleCallback($callbackQuery),
                
                // Картриджи
                str_starts_with($data, 'cartridge_') => $this->cartridgeHandler->handleCallback($callbackQuery),
                
                // Инвентаризация
                str_starts_with($data, 'inventory_') => $this->inventoryHandler->handleCallback($callbackQuery),
                
                // Админ панель
                str_starts_with($data, 'admin_') => $this->adminHandler->handleCallback($callbackQuery),
                
                // Выбор филиала (общий)
                str_starts_with($data, 'branch_select:') => $this->handleBranchSelection($callbackQuery),
                
                // Пропуск телефона
                $action === 'skip_phone' => $this->repairHandler->handleSkipPhone($callbackQuery),
                
                default => $this->handleUnknownCallback($chatId, $userId, $messageId, $data)
            };
        } catch (\Exception $e) {
            Log::error("Error handling callback: {$data}", [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'chat_id' => $chatId
            ]);
            
            $this->telegram->editMessage(
                $chatId, 
                $messageId, 
                "❌ Произошла ошибка. Попробуйте еще раз.", 
                $this->keyboard->getMainMenuKeyboard($userId)
            );
        }
    }

    private function handleMainMenu(int $chatId, int $userId, int $messageId): void
    {
        $this->stateManager->clearUserState($userId);
        $this->telegram->editMessage(
            $chatId, 
            $messageId, 
            "Выберите действие:", 
            $this->keyboard->getMainMenuKeyboard($userId)
        );
    }

    private function handleBranchSelection(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $data = $callbackQuery['data'];
        
        $parts = explode(':', $data);
        if (!isset($parts[1])) {
            Log::error("Invalid branch selection data: {$data}");
            return;
        }
        
        $branchId = (int) $parts[1];
        $userState = $this->stateManager->getUserState($userId);
        
        if (!$userState || !isset($userState['state'])) {
            Log::error("No user state found for branch selection", ['user_id' => $userId]);
            return;
        }

        // Делегируем обработку в соответствующий handler
        match ($userState['state']) {
            'repair_awaiting_branch' => $this->repairHandler->handleBranchSelection($callbackQuery, $branchId),
            'cartridge_awaiting_branch' => $this->cartridgeHandler->handleBranchSelection($callbackQuery, $branchId),
            default => Log::warning("Unknown state for branch selection: {$userState['state']}")
        };
    }

    private function handleUnknownCallback(int $chatId, int $userId, int $messageId, string $data): void
    {
        Log::warning("Unknown callback action: {$data} from user {$userId}");
        $this->telegram->editMessage(
            $chatId, 
            $messageId, 
            "Неизвестное действие. Возвращаемся в главное меню.", 
            $this->keyboard->getMainMenuKeyboard($userId)
        );
    }
}