<?php

namespace App\Services\Telegram\Handlers;

use App\Models\RepairRequest;
use App\Models\CartridgeReplacement;
use App\Models\RoomInventory;
use App\Models\Branch;
use App\Services\Telegram\TelegramService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\KeyboardService;
use Illuminate\Support\Facades\Log;

class AdminHandler
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

        // Проверяем права администратора
        if (!$this->telegram->isAdmin($userId)) {
            $this->telegram->editMessage($chatId, $messageId, "❌ У вас нет прав администратора.");
            return;
        }

        $parts = explode(':', $data);
        $action = $parts[0];

        match ($action) {
            'admin_menu' => $this->sendAdminMenu($chatId, $messageId),
            'admin_repairs' => $this->showRepairsList($chatId, $messageId),
            'admin_cartridges' => $this->showCartridgesList($chatId, $messageId),
            'admin_inventory' => $this->showInventoryMenu($chatId, $messageId),
            'admin_stats' => $this->sendSystemStatus($chatId, $messageId),
            'repair_details' => $this->showRepairDetails($chatId, $messageId, (int) ($parts[1] ?? 0)),
            'status_update' => $this->updateRepairStatus($chatId, $messageId, (int) ($parts[1] ?? 0), $parts[2] ?? ''),
            default => Log::warning("Unknown admin action: {$action}")
        };
    }

    public function sendAdminMenu(int $chatId, ?int $messageId = null): void
    {
        $text = "⚙️ <b>Админ-панель:</b>\n\nВыберите действие:";
        $keyboard = $this->keyboard->getAdminMenuKeyboard();
        
        if ($messageId) {
            $this->telegram->editMessage($chatId, $messageId, $text, $keyboard);
        } else {
            $this->telegram->sendMessage($chatId, $text, $keyboard);
        }
    }

    public function sendSystemStatus(int $chatId, ?int $messageId = null): void
    {
        try {
            $stats = $this->getSystemStats();
            
            $message = "📊 <b>Статистика системы:</b>\n\n";
            $message .= "🔧 Заявки на ремонт:\n";
            $message .= "   • Всего: {$stats['repairs']['total']}\n";
            $message .= "   • Новые: {$stats['repairs']['new']}\n";
            $message .= "   • В работе: {$stats['repairs']['in_progress']}\n";
            $message .= "   • Выполнено: {$stats['repairs']['completed']}\n\n";
            $message .= "🖨️ Картриджи: {$stats['cartridges']['total']}\n";
            $message .= "🏢 Филиалы: {$stats['branches']}\n";
            $message .= "📦 Инвентарь: {$stats['inventory']}\n";
            $message .= "\n⏰ Обновлено: " . now()->format('d.m.Y H:i');

            $keyboard = $messageId ? $this->keyboard->getBackKeyboard('admin_menu') : null;

            if ($messageId) {
                $this->telegram->editMessage($chatId, $messageId, $message, $keyboard);
            } else {
                $this->telegram->sendMessage($chatId, $message, $keyboard);
            }
        } catch (\Exception $e) {
            Log::error('Error getting system status: ' . $e->getMessage());
            $errorMessage = "❌ Ошибка получения статистики системы";
            
            if ($messageId) {
                $this->telegram->editMessage($chatId, $messageId, $errorMessage);
            } else {
                $this->telegram->sendMessage($chatId, $errorMessage);
            }
        }
    }

    private function showRepairsList(int $chatId, int $messageId): void
    {
        $repairs = RepairRequest::with('branch')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($repairs->isEmpty()) {
            $this->telegram->editMessage(
                $chatId, 
                $messageId, 
                "📋 <b>Заявки на ремонт</b>\n\nЗаявок не найдено.", 
                $this->keyboard->getBackKeyboard('admin_menu')
            );
            return;
        }

        $message = "📋 <b>Последние заявки на ремонт:</b>\n\n";
        
        foreach ($repairs as $repair) {
            $status = $this->getStatusEmoji($repair->status);
            $date = $repair->created_at->format('d.m.Y H:i');
            $username = $repair->username ? "@{$repair->username}" : "ID: {$repair->user_telegram_id}";
            
            $message .= "🔧 <b>#{$repair->id}</b> $status\n";
            $message .= "📍 {$repair->branch->name} - каб. {$repair->room_number}\n";
            $message .= "📝 " . $this->truncateText($repair->description, 50) . "\n";
            $message .= "👤 $username | ⏰ $date\n\n";
        }

        $this->telegram->editMessage($chatId, $messageId, $message, $this->keyboard->getRepairsListKeyboard($repairs));
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
                "🖨️ <b>История картриджей</b>\n\nЗаписей не найдено.", 
                $this->keyboard->getBackKeyboard('admin_menu')
            );
            return;
        }

        $message = "🖨️ <b>Последние замены картриджей:</b>\n\n";
        
        foreach ($cartridges as $cartridge) {
            $date = $cartridge->replacement_date->format('d.m.Y');
            $username = $cartridge->username ? "@{$cartridge->username}" : "ID: {$cartridge->user_telegram_id}";
            
            $message .= "🖨️ <b>#{$cartridge->id}</b>\n";
            $message .= "📍 {$cartridge->branch->name} - каб. {$cartridge->room_number}\n";
            $message .= "🛒 {$cartridge->cartridge_type}\n";
            $message .= "👤 $username | 📅 $date\n\n";
        }

        $this->telegram->editMessage($chatId, $messageId, $message, $this->keyboard->getBackKeyboard('admin_menu'));
    }

    private function showInventoryMenu(int $chatId, int $messageId): void
    {
        $stats = RoomInventory::selectRaw('equipment_type, COUNT(*) as count')
            ->groupBy('equipment_type')
            ->orderBy('count', 'desc')
            ->get();

        $message = "📦 <b>Управление инвентарем</b>\n\n";
        $message .= "📊 <b>Статистика по типам:</b>\n";
        
        foreach ($stats->take(10) as $stat) {
            $message .= "• {$stat->equipment_type}: {$stat->count}\n";
        }
        
        $message .= "\nВсего единиц: " . RoomInventory::count();

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📋 Добавить оборудование', 'callback_data' => 'inventory_management']
                ],
                [
                    ['text' => '📊 Экспорт отчета', 'callback_data' => 'inventory_export']
                ],
                [
                    ['text' => '◀️ Админ-панель', 'callback_data' => 'admin_menu']
                ]
            ]
        ];

        $this->telegram->editMessage($chatId, $messageId, $message, $keyboard);
    }

    private function showRepairDetails(int $chatId, int $messageId, int $repairId): void
    {
        $repair = RepairRequest::with('branch')->find($repairId);
        
        if (!$repair) {
            $this->telegram->editMessage($chatId, $messageId, "Заявка не найдена.");
            return;
        }

        $status = $this->getStatusEmoji($repair->status);
        $username = $repair->username ? "@{$repair->username}" : "ID: {$repair->user_telegram_id}";
        
        $message = "🔧 <b>Заявка #{$repair->id}</b> $status\n\n";
        $message .= "📍 <b>Филиал:</b> {$repair->branch->name}\n";
        $message .= "🚪 <b>Кабинет:</b> {$repair->room_number}\n";
        $message .= "📝 <b>Проблема:</b>\n" . htmlspecialchars($repair->description) . "\n\n";
        $message .= "👤 <b>Пользователь:</b> $username\n";
        
        if ($repair->phone) {
            $message .= "📞 <b>Телефон:</b> {$repair->phone}\n";
        }
        
        $message .= "⏰ <b>Создана:</b> " . $repair->created_at->format('d.m.Y H:i');
        
        if ($repair->updated_at != $repair->created_at) {
            $message .= "\n🔄 <b>Обновлена:</b> " . $repair->updated_at->format('d.m.Y H:i');
        }

        $this->telegram->editMessage($chatId, $messageId, $message, $this->keyboard->getRepairDetailsKeyboard($repair));
    }

    private function updateRepairStatus(int $chatId, int $messageId, int $repairId, string $newStatus): void
    {
        $repair = RepairRequest::find($repairId);
        
        if (!$repair) {
            $this->telegram->editMessage($chatId, $messageId, "Заявка не найдена.");
            return;
        }

        $repair->status = $newStatus;
        $repair->save();

        $statusText = [
            'нова' => 'Новая',
            'в_роботі' => 'В работе', 
            'виконана' => 'Выполнена'
        ];

        $this->telegram->answerCallbackQuery($messageId, "Статус изменен на: " . $statusText[$newStatus]);
        $this->showRepairDetails($chatId, $messageId, $repairId);
    }

    private function getSystemStats(): array
    {
        return [
            'repairs' => [
                'total' => RepairRequest::count(),
                'new' => RepairRequest::where('status', 'нова')->count(),
                'in_progress' => RepairRequest::where('status', 'в_роботі')->count(),
                'completed' => RepairRequest::where('status', 'виконана')->count()
            ],
            'cartridges' => [
                'total' => CartridgeReplacement::count(),
                'this_month' => CartridgeReplacement::whereMonth('created_at', now()->month)->count()
            ],
            'inventory' => RoomInventory::count(),
            'branches' => Branch::where('is_active', true)->count()
        ];
    }

    private function getStatusEmoji(string $status): string
    {
        return match($status) {
            'нова' => '🆕',
            'в_роботі' => '⚙️',
            'виконана' => '✅',
            default => '❓'
        };
    }

    private function truncateText(string $text, int $length): string
    {
        return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
    }
}