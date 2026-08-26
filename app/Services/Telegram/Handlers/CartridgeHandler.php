<?php

namespace App\Services\Telegram\Handlers;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\CartridgeReplacement;
use App\Models\TelegramProfile;
use App\Services\Telegram\KeyboardService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;

class CartridgeHandler
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

        if ($data === 'cartridge_request') {
            $this->startFromMenu($chatId, $userId, $messageId);
        } elseif ($data === 'cartridge_workplace_use') {
            $this->handleWorkplaceConfirmation($callbackQuery, true);
        } elseif ($data === 'cartridge_workplace_change') {
            $this->handleWorkplaceConfirmation($callbackQuery, false);
        }
    }

    /**
     * Single source of truth for the "returning user with a saved workplace"
     * branching, shared by the inline-button path (handleCallback) and the
     * reply-keyboard path (MessageHandler::handleCartridgeButton).
     */
    public function startFromMenu(int $chatId, int $userId, ?int $messageId = null): void
    {
        $profile = TelegramProfile::where('telegram_user_id', $userId)->first();

        if ($profile && $profile->is_profile_complete) {
            $this->showWorkplaceConfirmation($chatId, $userId, $messageId, $profile);

            return;
        }

        $this->startPicker($chatId, $userId, $messageId);
    }

    public function handleBranchSelection(array $callbackQuery, int $branchId): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $messageId = $callbackQuery['message']['message_id'];

        $branch = Branch::find($branchId);
        if (! $branch) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Філіал не знайдено.');

            return;
        }

        $this->stateManager->setUserState($userId, 'cartridge_awaiting_room', [
            'branch_id' => $branchId,
            'branch_name' => $branch->name,
        ]);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            '🚪 <b>Номер кабінету?</b>',
            $this->keyboard->getCancelKeyboard()
        );
    }

    /**
     * Shows the from-scratch branch picker. Edits the given message when
     * $messageId is provided (inline callback path), otherwise sends a new
     * message (reply-keyboard path).
     */
    public function startPicker(int $chatId, int $userId, ?int $messageId = null, string $prefix = ''): void
    {
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            $text = '❌ Філіали недоступні. Зв\'яжіться з адміністратором.';
            if ($messageId) {
                $this->telegram->editMessage($chatId, $messageId, $text);
            } else {
                $this->telegram->sendMessage($chatId, $text);
            }

            return;
        }

        $this->stateManager->setUserState($userId, 'cartridge_awaiting_branch');

        $text = $prefix."🖨️ <b>Заміна картриджа</b>\n\nОберіть філіал:";
        $keyboard = $this->keyboard->getBranchesKeyboard($branches, 'cartridge');

        if ($messageId) {
            $this->telegram->editMessage($chatId, $messageId, $text, $keyboard);
        } else {
            $this->telegram->sendMessage($chatId, $text, $keyboard);
        }
    }

    private function showWorkplaceConfirmation(int $chatId, int $userId, ?int $messageId, TelegramProfile $profile): void
    {
        $branch = Branch::find($profile->default_branch_id);

        if (! $branch || ! $branch->is_active) {
            $this->startPicker($chatId, $userId, $messageId, "⚠️ Збережений філіал більше недоступний.\n\n");

            return;
        }

        $this->stateManager->setUserState($userId, 'cartridge_awaiting_workplace_confirmation', [
            'branch_id' => $profile->default_branch_id,
            'branch_name' => $branch->name,
            'room_number' => $profile->default_room_number,
        ]);

        $text = "🏠 <b>Ваше робоче місце</b>\n\n".
            "Філія: {$branch->name}\n".
            "Кабінет: {$profile->default_room_number}";
        $keyboard = $this->keyboard->getWorkplaceConfirmationKeyboard('cartridge');

        if ($messageId) {
            $this->telegram->editMessage($chatId, $messageId, $text, $keyboard);
        } else {
            $this->telegram->sendMessage($chatId, $text, $keyboard);
        }
    }

    public function handleWorkplaceConfirmation(array $callbackQuery, bool $useSaved): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $messageId = $callbackQuery['message']['message_id'];

        if (! $useSaved) {
            $this->startPicker($chatId, $userId, $messageId);

            return;
        }

        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (! isset($tempData['branch_id'], $tempData['branch_name'], $tempData['room_number'])) {
            $this->startPicker($chatId, $userId, $messageId);

            return;
        }

        $this->stateManager->setUserState($userId, 'cartridge_awaiting_printer', $tempData);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "🖨️ <b>Який принтер?</b>\nМодель або інвентарний номер.",
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handleRoomInput(int $chatId, int $userId, string $room): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($room)) || strlen($room) > 50) {
            $this->telegram->sendMessage($chatId, '❌ Номер кабінету не довший за 50 символів. Спробуйте ще раз:');

            return;
        }

        $tempData['room_number'] = trim($room);
        $this->stateManager->setUserState($userId, 'cartridge_awaiting_printer', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "🖨️ <b>Який принтер?</b>\nМодель або інвентарний номер.",
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handlePrinterInput(int $chatId, int $userId, string $printer): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($printer))) {
            $this->telegram->sendMessage($chatId, '❌ Вкажіть принтер:');

            return;
        }

        $tempData['printer_info'] = trim($printer);
        $this->stateManager->setUserState($userId, 'cartridge_awaiting_type', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "🛒 <b>Який тип картриджа?</b>\nНаприклад, HP CF217A.",
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handleTypeInput(int $chatId, int $userId, ?string $username, string $cartridgeType): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($cartridgeType))) {
            $this->telegram->sendMessage($chatId, '❌ Вкажіть тип картриджа:');

            return;
        }

        $this->createCartridgeRequest($chatId, $userId, $username, trim($cartridgeType), $tempData);
    }

    private function createCartridgeRequest(int $chatId, int $userId, ?string $username, string $cartridgeType, array $tempData): void
    {
        try {
            if (! isset($tempData['branch_id'], $tempData['room_number'], $tempData['printer_info'])) {
                $this->telegram->sendMessage($chatId, '❌ Дані не збереглися. Спробуйте ще раз:', $this->keyboard->getMainMenuKeyboard($userId));
                $this->stateManager->clearUserState($userId);

                return;
            }

            $cartridge = CartridgeReplacement::create([
                'user_telegram_id' => $userId,
                'username' => $username,
                'branch_id' => $tempData['branch_id'],
                'room_number' => $tempData['room_number'],
                'printer_info' => $tempData['printer_info'],
                'cartridge_type' => $cartridgeType,
                'replacement_date' => now()->toDateString(),
            ]);

            app(\App\Services\Telegram\TelegramProfileService::class)->rememberWorkplace($userId, $tempData['branch_id'], $tempData['room_number']);

            // Додаємо запис у журнал робіт
            \App\Models\WorkLog::create([
                'work_type' => 'cartridge_replacement',
                'description' => "Заміна картриджа {$cartridgeType} на {$tempData['printer_info']}",
                'branch_id' => $tempData['branch_id'],
                'room_number' => $tempData['room_number'],
                'performed_at' => now()->toDateString(),
                'user_id' => \App\Models\User::where('telegram_id', $userId)->first()?->id ?? 1,
                'loggable_type' => \App\Models\CartridgeReplacement::class,
                'loggable_id' => $cartridge->id,
                'notes' => 'Запит створено через Telegram',
            ]);

            $this->stateManager->clearUserState($userId);

            $message = "✅ <b>Запит № {$cartridge->id} створено</b>\n\n".
                      "🏢 Філіал: {$tempData['branch_name']}\n".
                      "🚪 Кабінет: {$tempData['room_number']}\n".
                      "🖨️ Принтер: {$tempData['printer_info']}\n".
                      '🛒 Картридж: '.htmlspecialchars($cartridgeType)."\n".
                      "\n📧 Адміністраторів сповіщено.";

            $this->telegram->sendMessage($chatId, $message, $this->keyboard->getMainMenuKeyboard($userId));

            // Уведомляем администраторов
            $this->notifyAdminsAboutCartridge($cartridge, $tempData['branch_name']);

        } catch (\Exception $e) {
            Log::error('Error creating cartridge request: '.$e->getMessage());
            $this->telegram->sendMessage($chatId, '❌ Сталася помилка. Спробуйте пізніше.');
            $this->stateManager->clearUserState($userId);
        }
    }

    private function notifyAdminsAboutCartridge(CartridgeReplacement $cartridge, string $branchName): void
    {
        try {
            $admins = Admin::where('is_active', true)->get();

            if ($admins->isEmpty()) {
                Log::warning('No active admins found for cartridge notification');

                return;
            }

            $username = $cartridge->username ? "@{$cartridge->username}" : "ID: {$cartridge->user_telegram_id}";

            $message = "🖨️ <b>Запит на заміну картриджа № {$cartridge->id}</b>\n\n";
            $message .= "📍 Філіал: <b>$branchName</b>\n";
            $message .= "🏢 Кабінет: <b>{$cartridge->room_number}</b>\n";
            $message .= '🖨️ Принтер: '.htmlspecialchars($cartridge->printer_info)."\n";
            $message .= '🛒 Картридж: '.htmlspecialchars($cartridge->cartridge_type)."\n";
            $message .= "👤 Користувач: $username\n";
            $message .= "\n⏰ ".$cartridge->created_at->format('d.m.Y H:i');

            foreach ($admins as $admin) {
                try {
                    $this->telegram->sendMessage($admin->telegram_id, $message);
                } catch (\Exception $e) {
                    Log::error("Failed to notify admin {$admin->telegram_id}: ".$e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('Error notifying admins about cartridge: '.$e->getMessage());
        }
    }
}
