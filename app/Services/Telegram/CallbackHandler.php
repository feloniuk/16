<?php

namespace App\Services\Telegram;

use App\Services\Telegram\Handlers\AdminHandler;
use App\Services\Telegram\Handlers\CartridgeHandler;
use App\Services\Telegram\Handlers\InventoryHandler;
use App\Services\Telegram\Handlers\RepairHandler;
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
        try {
            $chatId = $callbackQuery['message']['chat']['id'] ?? null;
            $userId = $callbackQuery['from']['id'] ?? null;
            $username = $callbackQuery['from']['username'] ?? null;
            $data = $callbackQuery['data'] ?? '';
            $messageId = $callbackQuery['message']['message_id'] ?? null;
            $callbackId = $callbackQuery['id'] ?? '';

            // Валидация обязательных полей
            if (! $chatId || ! $userId || ! $messageId || ! $callbackId) {
                Log::error('Invalid callback query structure', [
                    'chat_id' => $chatId,
                    'user_id' => $userId,
                    'message_id' => $messageId,
                    'callback_id' => $callbackId,
                ]);

                return;
            }

            Log::info("Processing callback from user {$userId}: {$data}");

            // Подтверждение получения callback (очень важно!)
            $this->telegram->answerCallbackQuery($callbackId);

            // Парсим данные callback'а
            $parts = explode(':', $data);
            $action = $parts[0] ?? '';

            if (empty($action)) {
                Log::warning("Empty callback action from user {$userId}");
                $this->handleUnknownCallback($chatId, $userId, $messageId, $data);

                return;
            }

            // Маршрутизация по типу действия
            try {
                if ($action === 'main_menu') {
                    $this->handleMainMenu($chatId, $userId, $messageId);
                } elseif (str_starts_with($data, 'repair_')) {
                    $this->repairHandler->handleCallback($callbackQuery);
                } elseif (str_starts_with($data, 'cartridge_')) {
                    $this->cartridgeHandler->handleCallback($callbackQuery);
                } elseif (str_starts_with($data, 'inventory_')) {
                    $this->inventoryHandler->handleCallback($callbackQuery);
                } elseif (str_starts_with($data, 'admin_')) {
                    $this->adminHandler->handleCallback($callbackQuery);
                } elseif (str_starts_with($data, 'branch_select:')) {
                    $this->handleBranchSelection($callbackQuery);
                } elseif ($action === 'skip_phone') {
                    $this->repairHandler->handleSkipPhone($callbackQuery);
                } elseif ($action === 'cancel') {
                    $this->handleCancel($chatId, $userId, $messageId);
                } else {
                    $this->handleUnknownCallback($chatId, $userId, $messageId, $data);
                }
            } catch (\Exception $e) {
                Log::error("Error in callback routing: {$data}", [
                    'error' => $e->getMessage(),
                    'user_id' => $userId,
                    'chat_id' => $chatId,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                $this->handleError($chatId, $userId, $messageId);
            }

        } catch (\Exception $e) {
            Log::error('Critical error in callback handler', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackQuery,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function handleMainMenu(int $chatId, int $userId, int $messageId): void
    {
        try {
            $this->stateManager->clearUserState($userId);
            $this->telegram->editMessageSafe(
                $chatId,
                $messageId,
                "🏠 <b>Головне меню</b>\n\nОберіть дію:",
                $this->keyboard->getMainMenuKeyboard($userId)
            );
        } catch (\Exception $e) {
            Log::error('Error in handleMainMenu', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);
        }
    }

    private function handleBranchSelection(array $callbackQuery): void
    {
        try {
            $chatId = $callbackQuery['message']['chat']['id'];
            $userId = $callbackQuery['from']['id'];
            $messageId = $callbackQuery['message']['message_id'];
            $data = $callbackQuery['data'];

            $parts = explode(':', $data);
            if (! isset($parts[1]) || ! is_numeric($parts[1])) {
                Log::error("Invalid branch selection data: {$data}");
                $this->handleError($chatId, $userId, $messageId);

                return;
            }

            $branchId = (int) $parts[1];
            $userState = $this->stateManager->getUserState($userId);

            if (! $userState || ! isset($userState['state'])) {
                Log::error('No user state found for branch selection', ['user_id' => $userId]);
                $this->handleMainMenu($chatId, $userId, $messageId);

                return;
            }

            // Делегируем обработку в соответствующий handler
            switch ($userState['state']) {
                case 'repair_awaiting_branch':
                    $this->repairHandler->handleBranchSelection($callbackQuery, $branchId);
                    break;
                case 'cartridge_awaiting_branch':
                    $this->cartridgeHandler->handleBranchSelection($callbackQuery, $branchId);
                    break;
                case 'inventory_branch_selection':
                    $this->inventoryHandler->handleBranchSelection($callbackQuery, $branchId);
                    break;
                default:
                    Log::warning("Unknown state for branch selection: {$userState['state']}");
                    $this->handleMainMenu($chatId, $userId, $messageId);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error in handleBranchSelection', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackQuery,
            ]);
        }
    }

    private function handleCancel(int $chatId, int $userId, int $messageId): void
    {
        try {
            $this->stateManager->clearUserState($userId);
            $this->telegram->editMessageSafe(
                $chatId,
                $messageId,
                "✅ <b>Дія скасована.</b>\n\nОберіть нову дію:",
                $this->keyboard->getMainMenuKeyboard($userId)
            );
        } catch (\Exception $e) {
            Log::error('Error in handleCancel', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);
        }
    }

    private function handleError(int $chatId, int $userId, int $messageId): void
    {
        try {
            $this->stateManager->clearUserState($userId);
            $this->telegram->editMessageSafe(
                $chatId,
                $messageId,
                "❌ <b>Сталася помилка.</b> Спробуйте ще раз.\n\nПовертаємося в головне меню:",
                $this->keyboard->getMainMenuKeyboard($userId)
            );
        } catch (\Exception $e) {
            Log::error('Error in handleError', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);

            // Последняя попытка - отправить новое сообщение
            try {
                $this->telegram->sendMessage(
                    $chatId,
                    '❌ Сталася помилка. Натисніть /start для перезапуску.'
                );
            } catch (\Exception $e2) {
                Log::critical('Failed to send error message', [
                    'error' => $e2->getMessage(),
                    'chat_id' => $chatId,
                ]);
            }
        }
    }

    private function handleUnknownCallback(int $chatId, int $userId, int $messageId, string $data): void
    {
        Log::warning("Unknown callback action: {$data} from user {$userId}");

        try {
            $this->telegram->editMessageSafe(
                $chatId,
                $messageId,
                "❓ <b>Невідома дія:</b> {$data}\n\nПовертаємося в головне меню:",
                $this->keyboard->getMainMenuKeyboard($userId)
            );
        } catch (\Exception $e) {
            Log::error('Error in handleUnknownCallback', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
                'user_id' => $userId,
                'data' => $data,
            ]);

            $this->handleError($chatId, $userId, $messageId);
        }
    }
}
