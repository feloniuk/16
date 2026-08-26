<?php

namespace App\Services\Telegram\Handlers;

use App\Models\CartridgeReplacement;
use App\Models\RepairRequest;
use App\Services\Telegram\KeyboardService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\TelegramProfileService;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;

class AdminPanelHandler
{
    public function __construct(
        private TelegramService $telegram,
        private StateManager $stateManager,
        private KeyboardService $keyboard,
        private TelegramProfileService $profileService
    ) {}

    public function handleCallback(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $data = $callbackQuery['data'];

        if (! $this->profileService->isRecognizedAdmin($userId)) {
            $this->telegram->editMessage(
                $chatId,
                $messageId,
                "❌ Ваш Telegram-акаунт ще не пов'язаний з обліковим записом адміністратора.\n\nЗверніться до директора або прив'яжіть Telegram у своєму профілі на сайті (Профіль → Прив'язати Telegram)."
            );

            return;
        }

        $parts = explode(':', $data);
        $action = $parts[0];

        match ($action) {
            'adminpanel_menu' => $this->sendPanelMenu($chatId, $messageId),
            'adminpanel_repairs' => $this->showRepairsList($chatId, $messageId, null),
            'adminpanel_repairs_filter' => $this->showRepairsList($chatId, $messageId, $parts[1] ?? null),
            'adminpanel_repair_details' => $this->showRepairDetails($chatId, $messageId, (int) ($parts[1] ?? 0)),
            'adminpanel_status_update' => $this->updateRepairStatus($chatId, $messageId, (int) ($parts[1] ?? 0), $parts[2] ?? ''),
            'adminpanel_cartridges' => $this->showCartridgesList($chatId, $messageId),
            default => Log::warning("Unknown admin panel action: {$action}")
        };
    }

    public function sendPanelMenu(int $chatId, ?int $messageId = null): void
    {
        $text = "👑 <b>Адмін-панель:</b>\n\nОберіть дію:";
        $keyboard = $this->keyboard->getAdminPanelMenuKeyboard();

        if ($messageId) {
            $this->telegram->editMessage($chatId, $messageId, $text, $keyboard);
        } else {
            $this->telegram->sendMessage($chatId, $text, $keyboard);
        }
    }

    private function showRepairsList(int $chatId, int $messageId, ?string $statusFilter): void
    {
        $repairs = RepairRequest::with('branch')
            ->when($statusFilter, fn ($query) => $query->where('status', $statusFilter))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($repairs->isEmpty()) {
            $this->telegram->editMessage(
                $chatId,
                $messageId,
                "📋 <b>Заявки на ремонт</b>\n\nЗаявок не знайдено.",
                $this->keyboard->getAdminPanelRepairsListKeyboard($repairs, $statusFilter)
            );

            return;
        }

        $message = "📋 <b>Останні заявки на ремонт:</b>\n\n";

        foreach ($repairs as $repair) {
            $status = $this->getStatusEmoji($repair->status);
            $date = $repair->created_at->format('d.m.Y H:i');
            $username = $repair->username ? "@{$repair->username}" : "ID: {$repair->user_telegram_id}";

            $message .= "🔧 <b>#{$repair->id}</b> $status\n";
            $message .= "📍 {$repair->branch->name} - каб. {$repair->room_number}\n";
            $message .= '📝 '.$this->truncateText($repair->description, 50)."\n";
            $message .= "👤 $username | ⏰ $date\n\n";
        }

        $this->telegram->editMessage($chatId, $messageId, $message, $this->keyboard->getAdminPanelRepairsListKeyboard($repairs, $statusFilter));
    }

    private function showRepairDetails(int $chatId, int $messageId, int $repairId): void
    {
        $repair = RepairRequest::with('branch')->find($repairId);

        if (! $repair) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Заявку не знайдено.');

            return;
        }

        $status = $this->getStatusEmoji($repair->status);
        $username = $repair->username ? "@{$repair->username}" : "ID: {$repair->user_telegram_id}";

        $message = "🔧 <b>Заявка #{$repair->id}</b> $status\n\n";
        $message .= "📍 <b>Філіал:</b> {$repair->branch->name}\n";
        $message .= "🚪 <b>Кабінет:</b> {$repair->room_number}\n";
        $message .= "📝 <b>Проблема:</b>\n".htmlspecialchars($repair->description)."\n\n";
        $message .= "👤 <b>Користувач:</b> $username\n";

        if ($repair->phone) {
            $message .= "📞 <b>Телефон:</b> {$repair->phone}\n";
        }

        $message .= '⏰ <b>Створена:</b> '.$repair->created_at->format('d.m.Y H:i');

        if ($repair->updated_at != $repair->created_at) {
            $message .= "\n🔄 <b>Оновлена:</b> ".$repair->updated_at->format('d.m.Y H:i');
        }

        $this->telegram->editMessage($chatId, $messageId, $message, $this->keyboard->getAdminPanelRepairDetailsKeyboard($repair));
    }

    private function updateRepairStatus(int $chatId, int $messageId, int $repairId, string $newStatus): void
    {
        $repair = RepairRequest::find($repairId);

        if (! $repair) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Заявку не знайдено.');

            return;
        }

        $repair->status = $newStatus;
        $repair->save();

        $statusText = [
            'нова' => 'Нова',
            'в_роботі' => 'В роботі',
            'виконана' => 'Виконана',
        ];

        $this->telegram->answerCallbackQuery($messageId, 'Статус змінено на: '.$statusText[$newStatus]);
        $this->showRepairDetails($chatId, $messageId, $repairId);
    }

    private function showCartridgesList(int $chatId, int $messageId): void
    {
        $cartridges = CartridgeReplacement::with('branch')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($cartridges->isEmpty()) {
            $this->telegram->editMessage(
                $chatId,
                $messageId,
                "🖨️ <b>Історія картриджів</b>\n\nЗаписів не знайдено.",
                $this->keyboard->getBackKeyboard('adminpanel_menu')
            );

            return;
        }

        $message = "🖨️ <b>Останні заміни картриджів:</b>\n\n";

        foreach ($cartridges as $cartridge) {
            $date = $cartridge->replacement_date->format('d.m.Y');
            $username = $cartridge->username ? "@{$cartridge->username}" : "ID: {$cartridge->user_telegram_id}";

            $message .= "🖨️ <b>#{$cartridge->id}</b>\n";
            $message .= "📍 {$cartridge->branch->name} - каб. {$cartridge->room_number}\n";
            $message .= "🛒 {$cartridge->cartridge_type}\n";
            $message .= "👤 $username | 📅 $date\n\n";
        }

        $this->telegram->editMessage($chatId, $messageId, $message, $this->keyboard->getBackKeyboard('adminpanel_menu'));
    }

    private function getStatusEmoji(string $status): string
    {
        return match ($status) {
            'нова' => '🆕',
            'в_роботі' => '⚙️',
            'виконана' => '✅',
            default => '❓'
        };
    }

    private function truncateText(string $text, int $length): string
    {
        return mb_strlen($text) > $length ? mb_substr($text, 0, $length).'...' : $text;
    }
}
