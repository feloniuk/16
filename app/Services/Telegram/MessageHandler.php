<?php

namespace App\Services\Telegram;

use App\Models\Branch;
use App\Services\Telegram\Handlers\AdminPanelHandler;
use App\Services\Telegram\Handlers\CartridgeHandler;
use App\Services\Telegram\Handlers\InventoryHandler;
use App\Services\Telegram\Handlers\OnboardingHandler;
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

    private OnboardingHandler $onboardingHandler;

    private AdminPanelHandler $adminPanelHandler;

    private TelegramProfileService $profileService;

    public function __construct(
        TelegramService $telegram,
        StateManager $stateManager,
        KeyboardService $keyboard,
        ReplyKeyboardService $replyKeyboard,
        RepairHandler $repairHandler,
        CartridgeHandler $cartridgeHandler,
        InventoryHandler $inventoryHandler,
        OnboardingHandler $onboardingHandler,
        AdminPanelHandler $adminPanelHandler,
        TelegramProfileService $profileService
    ) {
        $this->telegram = $telegram;
        $this->stateManager = $stateManager;
        $this->keyboard = $keyboard;
        $this->replyKeyboard = $replyKeyboard;
        $this->repairHandler = $repairHandler;
        $this->cartridgeHandler = $cartridgeHandler;
        $this->inventoryHandler = $inventoryHandler;
        $this->onboardingHandler = $onboardingHandler;
        $this->adminPanelHandler = $adminPanelHandler;
        $this->profileService = $profileService;
    }

    public function handle(array $message): void
    {
        $chatId = $message['chat']['id'];
        $userId = $message['from']['id'];
        $username = $message['from']['username'] ?? null;
        $text = $message['text'] ?? '';

        Log::info("Processing message from user {$userId}: {$text}");

        // Поділ контакту (кнопка request_contact)
        if (isset($message['contact'])) {
            $this->onboardingHandler->handleContactShared($message);

            return;
        }

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

        // "⏭️ Пропустити" тут — це кнопка reply-клавіатури запиту контакту (онбординг),
        // а не inline-кнопка "skip_phone" з клавіатури репair-флоу (інший механізм доставки).
        if ($text === '⏭️ Пропустити') {
            $userState = $this->stateManager->getUserState($userId);
            if ($userState && ($userState['state'] ?? null) === 'onboarding_awaiting_contact') {
                $this->onboardingHandler->handleSkipContact($chatId, $userId);

                return true;
            }
        }

        match ($text) {
            '🔧 Виклик IT майстра' => $this->handleRepairButton($chatId, $userId),
            '🖨️ Заміна картриджа' => $this->handleCartridgeButton($chatId, $userId),
            default => false,
        };

        return in_array($text, [
            '🔧 Виклик IT майстра',
            '🖨️ Заміна картриджа',
        ]);
    }

    private function handleRepairButton(int $chatId, int $userId): void
    {
        $this->stateManager->clearUserState($userId);
        $this->repairHandler->startFromMenu($chatId, $userId);
    }

    private function handleCartridgeButton(int $chatId, int $userId): void
    {
        $this->stateManager->clearUserState($userId);
        $this->cartridgeHandler->startFromMenu($chatId, $userId);
    }

    private function handleInventoryButton(int $chatId, int $userId): void
    {
        $this->stateManager->clearUserState($userId);
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            $this->telegram->sendMessage($chatId, '❌ Філіали недоступні. Зв\'яжіться з адміністратором.');

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
                $this->handleStatusCommand($chatId, $userId);
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
        $this->onboardingHandler->maybeStartOnboarding($chatId, $userId);
    }

    private function handleHelpCommand(int $chatId, int $userId): void
    {
        $text = "📋 <b>Довідка</b>\n\n".
               "🔧 <b>Виклик IT майстра</b> — заявка на ремонт обладнання\n".
               "🖨️ <b>Заміна картриджа</b> — запит на заміну картриджа\n\n".
               "📞 <b>Команди:</b>\n".
               "/start - Головне меню\n".
               "/help - Ця довідка\n".
               "/cancel - Скасувати поточну дію\n".
               "/admin - Панель адміністратора (тільки для адміністраторів)\n".
               "/status - Статистика системи\n\n".
               '❓ Питання? Зв\'яжіться з адміністратором.';

        $this->telegram->sendMessage($chatId, $text, $this->keyboard->getMainMenuKeyboard($userId));
    }

    private function handleCancelCommand(int $chatId, int $userId): void
    {
        $this->stateManager->clearUserState($userId);
        $this->telegram->sendMessage(
            $chatId,
            '✅ Скасовано. Оберіть дію:',
            $this->keyboard->getMainMenuKeyboard($userId)
        );
    }

    private function handleAdminCommand(int $chatId, int $userId): void
    {
        if ($this->profileService->isRecognizedAdmin($userId)) {
            $this->adminPanelHandler->sendPanelMenu($chatId);
        } else {
            $this->telegram->sendMessage($chatId, '❌ Недостатньо прав.');
        }
    }

    private function handleStatusCommand(int $chatId, int $userId): void
    {
        if ($this->profileService->isRecognizedAdmin($userId)) {
            $this->adminPanelHandler->sendStats($chatId);
        } else {
            $this->telegram->sendMessage($chatId, '❌ Недостатньо прав.');
        }
    }

    private function handleUnknownCommand(int $chatId, int $userId, string $command): void
    {
        $this->telegram->sendMessage(
            $chatId,
            "❓ Невідома команда: {$command}. Наберіть /help для довідки.",
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

                // Onboarding states
            case 'onboarding_awaiting_room':
                $this->onboardingHandler->handleRoomInput($chatId, $userId, $text);
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
        $text = "🤖 Вітаємо, $name!\n\n".
               "Цей бот приймає заявки на ремонт обладнання та заміну картриджів.\n\n".
               'Оберіть дію:';

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
