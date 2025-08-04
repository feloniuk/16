<?php

namespace App\Services\Telegram\Handlers;

use App\Models\Branch;
use App\Models\RepairRequest;
use App\Models\Admin;
use App\Services\Telegram\TelegramService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\KeyboardService;
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
        if (!$branch) {
            $this->telegram->editMessage($chatId, $messageId, "Ошибка: филиал не найден.");
            return;
        }

        $this->stateManager->setUserState($userId, 'repair_awaiting_room', [
            'branch_id' => $branchId,
            'branch_name' => $branch->name
        ]);

        $this->telegram->editMessage(
            $chatId, 
            $messageId, 
            "🔧 <b>Вызов IT мастера</b>\nФилиал: <b>{$branch->name}</b>\n\nВведите номер кабинета:", 
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
            $this->telegram->editMessage($chatId, $messageId, "К сожалению, филиалы недоступны. Обратитесь к администратору.");
            return;
        }

        $this->stateManager->setUserState($userId, 'repair_awaiting_branch');
        
        $this->telegram->editMessage(
            $chatId, 
            $messageId, 
            "🔧 <b>Вызов IT мастера</b>\n\nВыберите филиал:", 
            $this->keyboard->getBranchesKeyboard($branches, 'repair')
        );
    }

    public function handleRoomInput(int $chatId, int $userId, string $room): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($room)) || strlen($room) > 50) {
            $this->telegram->sendMessage($chatId, "❌ Некорректный номер кабинета. Введите номер кабинета (до 50 символов):");
            return;
        }

        $tempData['room_number'] = trim($room);
        $this->stateManager->setUserState($userId, 'repair_awaiting_description', $tempData);
        
        $this->telegram->sendMessage(
            $chatId, 
            "🔧 <b>Вызов IT мастера</b>\n" .
            "Филиал: <b>{$tempData['branch_name']}</b>\n" .
            "Кабинет: <b>" . trim($room) . "</b>\n\n" .
            "Опишите проблему (от 10 до 1000 символов):", 
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handleDescriptionInput(int $chatId, int $userId, string $description): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($description)) || strlen($description) < 10 || strlen($description) > 1000) {
            $this->telegram->sendMessage($chatId, "❌ Описание должно содержать от 10 до 1000 символов. Попробуйте еще раз:");
            return;
        }

        $tempData['description'] = trim($description);
        $this->stateManager->setUserState($userId, 'repair_awaiting_phone', $tempData);
        
        $this->telegram->sendMessage(
            $chatId, 
            "🔧 <b>Вызов IT мастера</b>\n" .
            "Филиал: <b>{$tempData['branch_name']}</b>\n" .
            "Кабинет: <b>{$tempData['room_number']}</b>\n" .
            "Проблема: <b>" . substr($description, 0, 100) . "...</b>\n\n" .
            "Введите номер телефона для связи или нажмите 'Пропустить':", 
            $this->keyboard->getPhoneKeyboard()
        );
    }

    public function handlePhoneInput(int $chatId, int $userId, ?string $username, string $phone): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        $phone = trim($phone);
        if (!empty($phone) && !preg_match('/^\+?3?8?0\d{9}$/', $phone)) {
            $this->telegram->sendMessage($chatId, "❌ Некорректный формат телефона. Введите номер в формате +380XXXXXXXXX или нажмите 'Пропустить':");
            return;
        }

        $this->createRepairRequest($chatId, $userId, $username, $phone, $tempData);
    }

    private function createRepairRequest(int $chatId, int $userId, ?string $username, string $phone, array $tempData): void
    {
        try {
            if (!isset($tempData['branch_id'], $tempData['room_number'], $tempData['description'])) {
                $this->telegram->sendMessage($chatId, "❌ Ошибка: не все данные сохранены. Попробуйте еще раз:", $this->keyboard->getMainMenuKeyboard($userId));
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
                'status' => 'нова'
            ]);

            $this->stateManager->clearUserState($userId);

            $message = "✅ <b>Заявка создана успешно!</b>\n\n" .
                      "📋 <b>Детали заявки № {$repair->id}:</b>\n" .
                      "🏢 Филиал: {$tempData['branch_name']}\n" .
                      "🚪 Кабинет: {$tempData['room_number']}\n" .
                      "📝 Проблема: " . htmlspecialchars($tempData['description']) . "\n";
            
            if (!empty($phone)) {
                $message .= "📞 Телефон: $phone\n";
            }
            
            $message .= "\n📧 Администраторы получили уведомление о вашей заявке.\n" .
                       "⏰ Ожидайте связи от IT мастера.";

            $this->telegram->sendMessage($chatId, $message, $this->keyboard->getMainMenuKeyboard($userId));

            // Уведомляем администраторов
            $this->notifyAdminsAboutRepair($repair, $tempData['branch_name']);

        } catch (\Exception $e) {
            Log::error('Error creating repair request: ' . $e->getMessage());
            $this->telegram->sendMessage($chatId, "❌ Произошла ошибка. Попробуйте позже или обратитесь к администратору.");
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

            $message = "🔧 <b>Новая заявка на ремонт № {$repair->id}!</b>\n\n";
            $message .= "📍 Филиал: <b>$branchName</b>\n";
            $message .= "🏢 Кабинет: <b>{$repair->room_number}</b>\n";
            $message .= "📝 Проблема: " . htmlspecialchars($repair->description) . "\n";
            $message .= "👤 Пользователь: $username\n";
            
            if (!empty($repair->phone)) {
                $message .= "📞 Телефон: {$repair->phone}\n";
            }
            
            $message .= "\n⏰ " . $repair->created_at->format('d.m.Y H:i');

            foreach ($admins as $admin) {
                try {
                    $this->telegram->sendMessage($admin->telegram_id, $message);
                } catch (\Exception $e) {
                    Log::error("Failed to notify admin {$admin->telegram_id}: " . $e->getMessage());
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error notifying admins about repair: ' . $e->getMessage());
        }
    }
}