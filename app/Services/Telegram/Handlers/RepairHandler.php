<?php

namespace App\Services\Telegram\Handlers;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\RepairRequest;
use App\Models\TelegramProfile;
use App\Services\Telegram\KeyboardService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;

class RepairHandler
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

        if ($data === 'repair_request') {
            $this->startFromMenu($chatId, $userId, $messageId);
        } elseif ($data === 'repair_workplace_use') {
            $this->handleWorkplaceConfirmation($callbackQuery, true);
        } elseif ($data === 'repair_workplace_change') {
            $this->handleWorkplaceConfirmation($callbackQuery, false);
        }
    }

    /**
     * Single source of truth for the "returning user with a saved workplace"
     * branching, shared by the inline-button path (handleCallback) and the
     * reply-keyboard path (MessageHandler::handleRepairButton).
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

        $this->stateManager->setUserState($userId, 'repair_awaiting_room', [
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

    public function handleSkipPhone(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $username = $callbackQuery['from']['username'] ?? null;

        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        $this->createRepairRequest($chatId, $userId, $username, '', $tempData);
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

        $this->stateManager->setUserState($userId, 'repair_awaiting_branch');

        $text = $prefix."🔧 <b>Виклик IT майстра</b>\n\nОберіть філіал:";
        $keyboard = $this->keyboard->getBranchesKeyboard($branches, 'repair');

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

        $this->stateManager->setUserState($userId, 'repair_awaiting_workplace_confirmation', [
            'branch_id' => $profile->default_branch_id,
            'branch_name' => $branch->name,
            'room_number' => $profile->default_room_number,
        ]);

        $text = "🏠 <b>Ваше робоче місце</b>\n\n".
            "Філія: {$branch->name}\n".
            "Кабінет: {$profile->default_room_number}";
        $keyboard = $this->keyboard->getWorkplaceConfirmationKeyboard('repair');

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

        $this->stateManager->setUserState($userId, 'repair_awaiting_description', $tempData);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "📝 <b>Опишіть проблему</b>\n10–1000 символів.",
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
        $this->stateManager->setUserState($userId, 'repair_awaiting_description', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "📝 <b>Опишіть проблему</b>\n10–1000 символів.",
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handleDescriptionInput(int $chatId, int $userId, string $description): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($description)) || strlen($description) < 10 || strlen($description) > 1000) {
            $this->telegram->sendMessage($chatId, '❌ Опис має бути від 10 до 1000 символів. Спробуйте ще раз:');

            return;
        }

        $tempData['description'] = trim($description);
        $this->stateManager->setUserState($userId, 'repair_awaiting_phone', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "📞 <b>Номер телефону?</b>\nАбо натисніть «Пропустити».",
            $this->keyboard->getPhoneKeyboard()
        );
    }

    public function handlePhoneInput(int $chatId, int $userId, ?string $username, string $phone): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        $phone = trim($phone);
        if (! empty($phone) && ! preg_match('/^\+?3?8?0\d{9}$/', $phone)) {
            $this->telegram->sendMessage($chatId, '❌ Невірний формат. Приклад: +380XXXXXXXXX:');

            return;
        }

        $this->createRepairRequest($chatId, $userId, $username, $phone, $tempData);
    }

    private function createRepairRequest(int $chatId, int $userId, ?string $username, string $phone, array $tempData): void
    {
        try {
            if (! isset($tempData['branch_id'], $tempData['room_number'], $tempData['description'])) {
                $this->telegram->sendMessage($chatId, '❌ Дані не збереглися. Спробуйте ще раз:', $this->keyboard->getMainMenuKeyboard($userId));
                $this->stateManager->clearUserState($userId);

                return;
            }

            $repair = RepairRequest::create([
                'user_telegram_id' => $userId,
                'username' => $username,
                'branch_id' => $tempData['branch_id'],
                'room_number' => $tempData['room_number'],
                'description' => $tempData['description'],
                'phone' => $phone ?: null,
                'status' => 'нова',
            ]);

            app(\App\Services\Telegram\TelegramProfileService::class)->rememberWorkplace($userId, $tempData['branch_id'], $tempData['room_number']);

            $this->stateManager->clearUserState($userId);

            $message = "✅ <b>Заявку № {$repair->id} створено</b>\n\n".
                      "🏢 Філіал: {$tempData['branch_name']}\n".
                      "🚪 Кабінет: {$tempData['room_number']}\n".
                      '📝 Проблема: '.htmlspecialchars($tempData['description'])."\n";

            if (! empty($phone)) {
                $message .= "📞 Телефон: $phone\n";
            }

            $message .= "\n📧 Адміністраторів сповіщено.\n".
                       '⏰ Очікуйте на зв\'язок від IT майстра.';

            $this->telegram->sendMessage($chatId, $message, $this->keyboard->getMainMenuKeyboard($userId));

            // Уведомляем администраторов
            $this->notifyAdminsAboutRepair($repair, $tempData['branch_name']);

        } catch (\Exception $e) {
            Log::error('Error creating repair request: '.$e->getMessage());
            $this->telegram->sendMessage($chatId, '❌ Сталася помилка. Спробуйте пізніше.');
            $this->stateManager->clearUserState($userId);
        }
    }

    private function notifyAdminsAboutRepair(RepairRequest $repair, string $branchName): void
    {
        try {
            $admins = Admin::where('is_active', true)->get();

            if ($admins->isEmpty()) {
                Log::warning('No active admins found for repair notification');

                return;
            }

            $username = $repair->username ? "@{$repair->username}" : "ID: {$repair->user_telegram_id}";

            $message = "🔧 <b>Нова заявка на ремонт № {$repair->id}</b>\n\n";
            $message .= "📍 Філіал: <b>$branchName</b>\n";
            $message .= "🏢 Кабінет: <b>{$repair->room_number}</b>\n";
            $message .= '📝 Проблема: '.htmlspecialchars($repair->description)."\n";
            $message .= "👤 Користувач: $username\n";

            if (! empty($repair->phone)) {
                $message .= "📞 Телефон: {$repair->phone}\n";
            }

            $message .= "\n⏰ ".$repair->created_at->format('d.m.Y H:i');

            foreach ($admins as $admin) {
                try {
                    $this->telegram->sendMessage($admin->telegram_id, $message);
                } catch (\Exception $e) {
                    Log::error("Failed to notify admin {$admin->telegram_id}: ".$e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('Error notifying admins about repair: '.$e->getMessage());
        }
    }
}
