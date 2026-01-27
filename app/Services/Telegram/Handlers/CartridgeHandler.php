<?php

namespace App\Services\Telegram\Handlers;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\CartridgeReplacement;
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
            $this->startCartridgeRequest($chatId, $userId, $messageId);
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

        $this->stateManager->setUserState($userId, 'cartridge_awaiting_room', [
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

    private function startCartridgeRequest(int $chatId, int $userId, int $messageId): void
    {
        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            $this->telegram->editMessage($chatId, $messageId, '❌ На жаль, філіали недоступні. Зв\'яжіться з адміністратором.');

            return;
        }

        $this->stateManager->setUserState($userId, 'cartridge_awaiting_branch');

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            "🖨️ <b>Заміна картриджа</b>\n\nОберіть філіал:",
            $this->keyboard->getBranchesKeyboard($branches, 'cartridge')
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
        $this->stateManager->setUserState($userId, 'cartridge_awaiting_printer', $tempData);

        $this->telegram->sendMessage(
            $chatId,
            "🖨️ <b>Вкажіть принтер:</b>\n(модель або інвентарний номер)",
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
            "🛒 <b>Вкажіть тип картриджа:</b>\n(наприклад, HP CF217A)",
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
                $this->telegram->sendMessage($chatId, '❌ Помилка: не всі дані збережені. Спробуйте ще раз:', $this->keyboard->getMainMenuKeyboard($userId));
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

            $message = "✅ <b>Запит на заміну картриджа створено!</b>\n\n".
                      "📋 <b>Деталі запиту № {$cartridge->id}:</b>\n".
                      "🏢 Філіал: {$tempData['branch_name']}\n".
                      "🚪 Кабінет: {$tempData['room_number']}\n".
                      "🖨️ Принтер: {$tempData['printer_info']}\n".
                      '🛒 Картридж: '.htmlspecialchars($cartridgeType)."\n".
                      "\n📧 Адміністратори отримали сповіщення про ваш запит.";

            $this->telegram->sendMessage($chatId, $message, $this->keyboard->getMainMenuKeyboard($userId));

            // Уведомляем администраторов
            $this->notifyAdminsAboutCartridge($cartridge, $tempData['branch_name']);

        } catch (\Exception $e) {
            Log::error('Error creating cartridge request: '.$e->getMessage());
            $this->telegram->sendMessage($chatId, '❌ Сталася помилка. Спробуйте пізніше або зв\'яжіться з адміністратором.');
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

            $message = "🖨️ <b>Запит на заміну картриджа № {$cartridge->id}!</b>\n\n";
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
