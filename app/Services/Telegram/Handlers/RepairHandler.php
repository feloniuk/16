<?php

namespace App\Services\Telegram\Handlers;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\RepairRequest;
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
            $this->startRepairRequest($chatId, $userId, $messageId);
        }
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

        $this->stateManager->setUserState($userId, 'repair_awaiting_room', [
            'branch_id' => $branchId,
            'branch_name' => $branch->name,
        ]);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            '🚪 <b>Введіть номер кабінету:</b>',
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

    private function startRepairRequest(int $chatId, int $userId, int $messageId): void
    {
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            $this->telegram->editMessage($chatId, $messageId, '❌ На жаль, філіали недоступні. Зв\'яжіться з адміністратором.');

            return;
        }

        $this->stateManager->setUserState($userId, 'repair_awaiting_branch');

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "🔧 <b>Виклик IT майстра</b>\n\nОберіть філіал:",
            $this->keyboard->getBranchesKeyboard($branches, 'repair')
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
        $this->stateManager->setUserState($userId, 'repair_awaiting_description', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "📝 <b>Опишіть проблему:</b>\n(від 10 до 1000 символів)",
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handleDescriptionInput(int $chatId, int $userId, string $description): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($description)) || strlen($description) < 10 || strlen($description) > 1000) {
            $this->telegram->sendMessage($chatId, '❌ Опис має містити від 10 до 1000 символів:');

            return;
        }

        $tempData['description'] = trim($description);
        $this->stateManager->setUserState($userId, 'repair_awaiting_phone', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "📞 <b>Введіть номер телефону:</b>\nабо натисніть «Пропустити»",
            $this->keyboard->getPhoneKeyboard()
        );
    }

    public function handlePhoneInput(int $chatId, int $userId, ?string $username, string $phone): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        $phone = trim($phone);
        if (! empty($phone) && ! preg_match('/^\+?3?8?0\d{9}$/', $phone)) {
            $this->telegram->sendMessage($chatId, '❌ Невірний формат. Введіть номер у форматі +380XXXXXXXXX:');

            return;
        }

        $this->createRepairRequest($chatId, $userId, $username, $phone, $tempData);
    }

    private function createRepairRequest(int $chatId, int $userId, ?string $username, string $phone, array $tempData): void
    {
        try {
            if (! isset($tempData['branch_id'], $tempData['room_number'], $tempData['description'])) {
                $this->telegram->sendMessage($chatId, '❌ Помилка: не всі дані збережені. Спробуйте ще раз:', $this->keyboard->getMainMenuKeyboard($userId));
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

            $this->stateManager->clearUserState($userId);

            $message = "✅ <b>Заявку створено успішно!</b>\n\n".
                      "📋 <b>Деталі заявки № {$repair->id}:</b>\n".
                      "🏢 Філіал: {$tempData['branch_name']}\n".
                      "🚪 Кабінет: {$tempData['room_number']}\n".
                      '📝 Проблема: '.htmlspecialchars($tempData['description'])."\n";

            if (! empty($phone)) {
                $message .= "📞 Телефон: $phone\n";
            }

            $message .= "\n📧 Адміністратори отримали сповіщення про вашу заявку.\n".
                       '⏰ Очікуйте зв\'язку від IT майстра.';

            $this->telegram->sendMessage($chatId, $message, $this->keyboard->getMainMenuKeyboard($userId));

            // Уведомляем администраторов
            $this->notifyAdminsAboutRepair($repair, $tempData['branch_name']);

        } catch (\Exception $e) {
            Log::error('Error creating repair request: '.$e->getMessage());
            $this->telegram->sendMessage($chatId, '❌ Сталася помилка. Спробуйте пізніше або зв\'яжіться з адміністратором.');
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

            $message = "🔧 <b>Нова заявка на ремонт № {$repair->id}!</b>\n\n";
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
