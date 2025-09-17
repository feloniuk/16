<?php

namespace App\Services\Telegram\Handlers;

use App\Models\Branch;
use App\Models\CartridgeReplacement;
use App\Models\Admin;
use App\Services\Telegram\TelegramService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\KeyboardService;
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
            $this->startCartridgeRequest($chatId, $userId, $messageId);
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

        $this->stateManager->setUserState($userId, 'cartridge_awaiting_room', [
            'branch_id' => $branchId,
            'branch_name' => $branch->name
        ]);

        $this->telegram->editMessage(
            $chatId, 
            $messageId, 
            "🖨️ <b>Замена картриджа</b>\nФилиал: <b>{$branch->name}</b>\n\nВведите номер кабинета:", 
            $this->keyboard->getCancelKeyboard()
        );
    }

    private function startCartridgeRequest(int $chatId, int $userId, int $messageId): void
    {
        $branches = Branch::where('is_active', true)->get();
        
        if ($branches->isEmpty()) {
            $this->telegram->editMessage($chatId, $messageId, "К сожалению, филиалы недоступны. Обратитесь к администратору.");
            return;
        }

        $this->stateManager->setUserState($userId, 'cartridge_awaiting_branch');
        
        $this->telegram->editMessage(
            $chatId, 
            $messageId, 
            "🖨️ <b>Замена картриджа</b>\n\nВыберите филиал:", 
            $this->keyboard->getBranchesKeyboard($branches, 'cartridge')
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
        $this->stateManager->setUserState($userId, 'cartridge_awaiting_printer', $tempData);
        
        $this->telegram->sendMessage(
            $chatId, 
            "🖨️ <b>Замена картриджа</b>\n" .
            "Филиал: <b>{$tempData['branch_name']}</b>\n" .
            "Кабинет: <b>" . trim($room) . "</b>\n\n" .
            "Введите информацию о принтере (любая полезная информация):", 
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handlePrinterInput(int $chatId, int $userId, string $printer): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($printer))) {
            $this->telegram->sendMessage($chatId, "❌ Введите информацию о принтере:");
            return;
        }

        $tempData['printer_info'] = trim($printer);
        $this->stateManager->setUserState($userId, 'cartridge_awaiting_type', $tempData);
        
        $this->telegram->sendMessage(
            $chatId, 
            "🖨️ <b>Замена картриджа</b>\n" .
            "Филиал: <b>{$tempData['branch_name']}</b>\n" .
            "Кабинет: <b>{$tempData['room_number']}</b>\n" .
            "Принтер: <b>" . trim($printer) . "</b>\n\n" .
            "Введите тип картриджа (чернильный, отработаных чернил):", 
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handleTypeInput(int $chatId, int $userId, ?string $username, string $cartridgeType): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($cartridgeType))) {
            $this->telegram->sendMessage($chatId, "❌ Введите тип картриджа:");
            return;
        }

        $this->createCartridgeRequest($chatId, $userId, $username, trim($cartridgeType), $tempData);
    }

    private function createCartridgeRequest(int $chatId, int $userId, ?string $username, string $cartridgeType, array $tempData): void
    {
        try {
            if (!isset($tempData['branch_id'], $tempData['room_number'], $tempData['printer_info'])) {
                $this->telegram->sendMessage($chatId, "❌ Ошибка: не все данные сохранены. Попробуйте еще раз:", $this->keyboard->getMainMenuKeyboard($userId));
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

            $this->stateManager->clearUserState($userId);

            $message = "✅ <b>Запрос на замену картриджа создан!</b>\n\n" .
                      "📋 <b>Детали запроса № {$cartridge->id}:</b>\n" .
                      "🏢 Филиал: {$tempData['branch_name']}\n" .
                      "🚪 Кабинет: {$tempData['room_number']}\n" .
                      "🖨️ Принтер: {$tempData['printer_info']}\n" .
                      "🛒 Картридж: " . htmlspecialchars($cartridgeType) . "\n" .
                      "\n📧 Администраторы получили уведомление о вашем запросе.";

            $this->telegram->sendMessage($chatId, $message, $this->keyboard->getMainMenuKeyboard($userId));

            // Уведомляем администраторов
            $this->notifyAdminsAboutCartridge($cartridge, $tempData['branch_name']);

        } catch (\Exception $e) {
            Log::error('Error creating cartridge request: ' . $e->getMessage());
            $this->telegram->sendMessage($chatId, "❌ Произошла ошибка. Попробуйте позже или обратитесь к администратору.");
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

            $message = "🖨️ <b>Запрос на замену картриджа № {$cartridge->id}!</b>\n\n";
            $message .= "📍 Филиал: <b>$branchName</b>\n";
            $message .= "🏢 Кабинет: <b>{$cartridge->room_number}</b>\n";
            $message .= "🖨️ Принтер: " . htmlspecialchars($cartridge->printer_info) . "\n";
            $message .= "🛒 Картридж: " . htmlspecialchars($cartridge->cartridge_type) . "\n";
            $message .= "👤 Пользователь: $username\n";
            $message .= "\n⏰ " . $cartridge->created_at->format('d.m.Y H:i');

            foreach ($admins as $admin) {
                try {
                    $this->telegram->sendMessage($admin->telegram_id, $message);
                } catch (\Exception $e) {
                    Log::error("Failed to notify admin {$admin->telegram_id}: " . $e->getMessage());
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error notifying admins about cartridge: ' . $e->getMessage());
        }
    }
}