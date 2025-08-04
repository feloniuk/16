<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\RepairRequest;
use App\Models\CartridgeReplacement;
use App\Models\Branch;
use App\Models\RoomInventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TelegramBotController extends Controller
{
    private $botToken;
    private $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
        
        Log::info('TelegramBotController initialized', [
            'bot_token_exists' => !empty($this->botToken),
            'bot_token_length' => $this->botToken ? strlen($this->botToken) : 0
        ]);
    }

    /**
     * Webhook для обработки сообщений от Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $update = $request->all();
            Log::info('Telegram webhook received', $update);

            if (empty($update)) {
                Log::warning('Empty webhook update received');
                return response()->json(['status' => 'ok']);
            }

            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
            } elseif (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Обработка текстовых сообщений
     */
    private function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $userId = $message['from']['id'];
        $username = $message['from']['username'] ?? null;
        $text = $message['text'] ?? '';

        Log::info("Processing message from user {$userId}: {$text}");

        // Обработка команд
        if (strpos($text, '/') === 0) {
            $this->handleCommand($chatId, $userId, $username, $text);
            return;
        }

        // Обработка по состоянию пользователя
        $userState = $this->getUserState($userId);
        
        if ($userState && isset($userState['state'])) {
            $this->handleStateMessage($chatId, $userId, $username, $text, $userState);
        } else {
            $this->sendMainMenu($chatId);
        }
    }

    /**
     * Обработка команд
     */
    private function handleCommand($chatId, $userId, $username, $command)
    {
        Log::info("Handling command: {$command} for user: {$userId}");
        
        switch ($command) {
            case '/start':
                $this->clearUserState($userId);
                $this->sendWelcomeMessage($chatId, $username);
                break;

            case '/help':
                $this->sendHelpMessage($chatId);
                break;

            case '/cancel':
                $this->clearUserState($userId);
                $this->sendMessage($chatId, "Действие отменено. Выберите новое действие:", $this->getMainMenuKeyboard());
                break;

            case '/admin':
                if ($this->isAdmin($userId)) {
                    $this->sendAdminMenu($chatId);
                } else {
                    $this->sendMessage($chatId, "У вас нет прав администратора.");
                }
                break;

            case '/status':
                $this->sendSystemStatus($chatId);
                break;

            default:
                $this->sendMessage($chatId, "Неизвестная команда. Используйте /help для справки.", $this->getMainMenuKeyboard());
        }
    }

    /**
     * Отправка системного статуса
     */
    private function sendSystemStatus($chatId)
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
            $message .= "\n⏰ Обновлено: " . now()->format('d.m.Y H:i');

            $this->sendMessage($chatId, $message, null, 'HTML');
        } catch (\Exception $e) {
            Log::error('Error getting system status: ' . $e->getMessage());
            $this->sendMessage($chatId, "❌ Ошибка получения статистики системы");
        }
    }

    private function getSystemStats()
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
            'branches' => Branch::where('is_active', true)->count()
        ];
    }

    /**
     * Обработка callback запросов (кнопки)
     */
    private function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $username = $callbackQuery['from']['username'] ?? null;
        $data = $callbackQuery['data'];
        $messageId = $callbackQuery['message']['message_id'];

        Log::info("Processing callback from user {$userId}: {$data}");

        // Подтверждение получения callback
        $this->answerCallbackQuery($callbackQuery['id']);

        $parts = explode(':', $data);
        $action = $parts[0];

        switch ($action) {
            case 'main_menu':
                $this->clearUserState($userId);
                $this->editMessage($chatId, $messageId, "Выберите действие:", $this->getMainMenuKeyboard());
                break;

            case 'repair_request':
                $this->startRepairRequest($chatId, $userId, $messageId);
                break;

            case 'cartridge_request':
                $this->startCartridgeRequest($chatId, $userId, $messageId);
                break;

            case 'branch_select':
                if (isset($parts[1])) {
                    $this->handleBranchSelection($chatId, $userId, $messageId, $parts[1]);
                }
                break;

            case 'skip_phone':
                $this->handleSkipPhone($chatId, $userId, $username, $messageId);
                break;

            case 'admin_menu':
                if ($this->isAdmin($userId)) {
                    $this->sendAdminMenu($chatId, $messageId);
                } else {
                    $this->editMessage($chatId, $messageId, "У вас нет прав администратора.");
                }
                break;

            case 'admin_repairs':
                if ($this->isAdmin($userId)) {
                    $this->showRepairsList($chatId, $messageId);
                }
                break;

            case 'admin_cartridges':
                if ($this->isAdmin($userId)) {
                    $this->showCartridgesList($chatId, $messageId);
                }
                break;

            case 'repair_details':
                if ($this->isAdmin($userId) && isset($parts[1])) {
                    $this->showRepairDetails($chatId, $messageId, $parts[1]);
                }
                break;

            case 'status_update':
                if ($this->isAdmin($userId) && isset($parts[1], $parts[2])) {
                    $this->updateRepairStatus($chatId, $messageId, $parts[1], $parts[2]);
                }
                break;

            default:
                $this->editMessage($chatId, $messageId, "Неизвестное действие.", $this->getMainMenuKeyboard());
        }
    }

    // =============== REPAIR REQUEST METHODS ===============

    private function startRepairRequest($chatId, $userId, $messageId)
    {
        $branches = Branch::where('is_active', true)->get();
        
        if ($branches->isEmpty()) {
            $this->editMessage($chatId, $messageId, "К сожалению, филиалы недоступны. Обратитесь к администратору.");
            return;
        }

        $this->setUserState($userId, 'repair_awaiting_branch');
        $keyboard = $this->getBranchesKeyboard($branches);
        $this->editMessage($chatId, $messageId, "🔧 <b>Вызов IT мастера</b>\n\nВыберите филиал:", $keyboard, 'HTML');
    }

    private function handleBranchSelection($chatId, $userId, $messageId, $branchId)
    {
        $branch = Branch::find($branchId);
        if (!$branch) {
            $this->editMessage($chatId, $messageId, "Ошибка: филиал не найден.");
            return;
        }

        $userState = $this->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];
        $tempData['branch_id'] = $branchId;
        $tempData['branch_name'] = $branch->name;

        if ($userState['state'] === 'repair_awaiting_branch') {
            $this->setUserState($userId, 'repair_awaiting_room', $tempData);
            $this->editMessage($chatId, $messageId, 
                "🔧 <b>Вызов IT мастера</b>\nФилиал: <b>{$branch->name}</b>\n\nВведите номер кабинета:", 
                $this->getCancelKeyboard(), 'HTML');
        } elseif ($userState['state'] === 'cartridge_awaiting_branch') {
            $this->setUserState($userId, 'cartridge_awaiting_room', $tempData);
            $this->editMessage($chatId, $messageId, 
                "🖨️ <b>Замена картриджа</b>\nФилиал: <b>{$branch->name}</b>\n\nВведите номер кабинета:", 
                $this->getCancelKeyboard(), 'HTML');
        }
    }

    private function handleStateMessage($chatId, $userId, $username, $text, $userState)
    {
        $state = $userState['state'];
        $tempData = $userState['temp_data'] ?? [];

        switch ($state) {
            case 'repair_awaiting_room':
                $this->handleRepairRoomInput($chatId, $userId, $text, $tempData);
                break;

            case 'repair_awaiting_description':
                $this->handleRepairDescriptionInput($chatId, $userId, $text, $tempData);
                break;

            case 'repair_awaiting_phone':
                $this->handleRepairPhoneInput($chatId, $userId, $username, $text, $tempData);
                break;

            case 'cartridge_awaiting_room':
                $this->handleCartridgeRoomInput($chatId, $userId, $text, $tempData);
                break;

            case 'cartridge_awaiting_printer':
                $this->handleCartridgePrinterInput($chatId, $userId, $text, $tempData);
                break;

            case 'cartridge_awaiting_type':
                $this->handleCartridgeTypeInput($chatId, $userId, $username, $text, $tempData);
                break;

            default:
                $this->sendMessage($chatId, "Неизвестное состояние. Возвращаемся в главное меню.", $this->getMainMenuKeyboard());
                $this->clearUserState($userId);
        }
    }

    private function handleRepairRoomInput($chatId, $userId, $room, $tempData)
    {
        if (empty(trim($room)) || strlen($room) > 50) {
            $this->sendMessage($chatId, "❌ Некорректный номер кабинета. Введите номер кабинета (до 50 символов):");
            return;
        }

        $tempData['room_number'] = trim($room);
        $this->setUserState($userId, 'repair_awaiting_description', $tempData);
        
        $this->sendMessage($chatId, 
            "🔧 <b>Вызов IT мастера</b>\n".
            "Филиал: <b>{$tempData['branch_name']}</b>\n".
            "Кабинет: <b>".trim($room)."</b>\n\n".
            "Опишите проблему (от 10 до 1000 символов):", 
            $this->getCancelKeyboard(), 'HTML');
    }

    private function handleRepairDescriptionInput($chatId, $userId, $description, $tempData)
    {
        if (empty(trim($description)) || strlen($description) < 10 || strlen($description) > 1000) {
            $this->sendMessage($chatId, "❌ Описание должно содержать от 10 до 1000 символов. Попробуйте еще раз:");
            return;
        }

        $tempData['description'] = trim($description);
        $this->setUserState($userId, 'repair_awaiting_phone', $tempData);
        
        $this->sendMessage($chatId, 
            "🔧 <b>Вызов IT мастера</b>\n".
            "Филиал: <b>{$tempData['branch_name']}</b>\n".
            "Кабинет: <b>{$tempData['room_number']}</b>\n".
            "Проблема: <b>".substr($description, 0, 100)."...</b>\n\n".
            "Введите номер телефона для связи или нажмите 'Пропустить':", 
            $this->getPhoneKeyboard(), 'HTML');
    }

    private function handleRepairPhoneInput($chatId, $userId, $username, $phone, $tempData)
    {
        $phone = trim($phone);
        if (!empty($phone) && !preg_match('/^\+?3?8?0\d{9}$/', $phone)) {
            $this->sendMessage($chatId, "❌ Некорректный формат телефона. Введите номер в формате +380XXXXXXXXX или нажмите 'Пропустить':");
            return;
        }

        $this->createRepairRequest($chatId, $userId, $username, $phone, $tempData);
    }

    private function handleSkipPhone($chatId, $userId, $username, $messageId)
    {
        $userState = $this->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];
        $this->createRepairRequest($chatId, $userId, $username, '', $tempData);
    }

    private function createRepairRequest($chatId, $userId, $username, $phone, $tempData)
    {
        try {
            if (!isset($tempData['branch_id'], $tempData['room_number'], $tempData['description'])) {
                $this->sendMessage($chatId, "❌ Ошибка: не все данные сохранены. Попробуйте еще раз:", $this->getMainMenuKeyboard());
                $this->clearUserState($userId);
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

            $this->clearUserState($userId);

            $message = "✅ <b>Заявка создана успешно!</b>\n\n".
                      "📋 <b>Детали заявки № {$repair->id}:</b>\n".
                      "🏢 Филиал: {$tempData['branch_name']}\n".
                      "🚪 Кабинет: {$tempData['room_number']}\n".
                      "📝 Проблема: ".htmlspecialchars($tempData['description'])."\n";
            
            if (!empty($phone)) {
                $message .= "📞 Телефон: $phone\n";
            }
            
            $message .= "\n📧 Администраторы получили уведомление о вашей заявке.\n".
                       "⏰ Ожидайте связи от IT мастера.";

            $this->sendMessage($chatId, $message, $this->getMainMenuKeyboard(), 'HTML');

            // Уведомляем администраторов
            $this->notifyAdminsAboutRepair($repair, $tempData['branch_name']);

        } catch (\Exception $e) {
            Log::error('Error creating repair request: ' . $e->getMessage());
            $this->sendMessage($chatId, "❌ Произошла ошибка. Попробуйте позже или обратитесь к администратору.");
            $this->clearUserState($userId);
        }
    }

    // =============== CARTRIDGE METHODS ===============

    private function startCartridgeRequest($chatId, $userId, $messageId)
    {
        $branches = Branch::where('is_active', true)->get();
        
        if ($branches->isEmpty()) {
            $this->editMessage($chatId, $messageId, "К сожалению, филиалы недоступны. Обратитесь к администратору.");
            return;
        }

        $this->setUserState($userId, 'cartridge_awaiting_branch');
        $keyboard = $this->getBranchesKeyboard($branches);
        $this->editMessage($chatId, $messageId, "🖨️ <b>Замена картриджа</b>\n\nВыберите филиал:", $keyboard, 'HTML');
    }

    private function handleCartridgeRoomInput($chatId, $userId, $room, $tempData)
    {
        if (empty(trim($room)) || strlen($room) > 50) {
            $this->sendMessage($chatId, "❌ Некорректный номер кабинета. Введите номер кабинета (до 50 символов):");
            return;
        }

        $tempData['room_number'] = trim($room);
        $this->setUserState($userId, 'cartridge_awaiting_printer', $tempData);
        
        $this->sendMessage($chatId, 
            "🖨️ <b>Замена картриджа</b>\n".
            "Филиал: <b>{$tempData['branch_name']}</b>\n".
            "Кабинет: <b>".trim($room)."</b>\n\n".
            "Введите информацию о принтере (модель, инвентарный номер):", 
            $this->getCancelKeyboard(), 'HTML');
    }

    private function handleCartridgePrinterInput($chatId, $userId, $printer, $tempData)
    {
        if (empty(trim($printer))) {
            $this->sendMessage($chatId, "❌ Введите информацию о принтере:");
            return;
        }

        $tempData['printer_info'] = trim($printer);
        $this->setUserState($userId, 'cartridge_awaiting_type', $tempData);
        
        $this->sendMessage($chatId, 
            "🖨️ <b>Замена картриджа</b>\n".
            "Филиал: <b>{$tempData['branch_name']}</b>\n".
            "Кабинет: <b>{$tempData['room_number']}</b>\n".
            "Принтер: <b>".trim($printer)."</b>\n\n".
            "Введите тип картриджа (например, HP CF217A):", 
            $this->getCancelKeyboard(), 'HTML');
    }

    private function handleCartridgeTypeInput($chatId, $userId, $username, $cartridgeType, $tempData)
    {
        if (empty(trim($cartridgeType))) {
            $this->sendMessage($chatId, "❌ Введите тип картриджа:");
            return;
        }

        $this->createCartridgeRequest($chatId, $userId, $username, trim($cartridgeType), $tempData);
    }

    private function createCartridgeRequest($chatId, $userId, $username, $cartridgeType, $tempData)
    {
        try {
            if (!isset($tempData['branch_id'], $tempData['room_number'], $tempData['printer_info'])) {
                $this->sendMessage($chatId, "❌ Ошибка: не все данные сохранены. Попробуйте еще раз:", $this->getMainMenuKeyboard());
                $this->clearUserState($userId);
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

            $this->clearUserState($userId);

            $message = "✅ <b>Запрос на замену картриджа создан!</b>\n\n".
                      "📋 <b>Детали запроса № {$cartridge->id}:</b>\n".
                      "🏢 Филиал: {$tempData['branch_name']}\n".
                      "🚪 Кабинет: {$tempData['room_number']}\n".
                      "🖨️ Принтер: {$tempData['printer_info']}\n".
                      "🛒 Картридж: ".htmlspecialchars($cartridgeType)."\n".
                      "\n📧 Администраторы получили уведомление о вашем запросе.";

            $this->sendMessage($chatId, $message, $this->getMainMenuKeyboard(), 'HTML');

            // Уведомляем администраторов
            $this->notifyAdminsAboutCartridge($cartridge, $tempData['branch_name']);

        } catch (\Exception $e) {
            Log::error('Error creating cartridge request: ' . $e->getMessage());
            $this->sendMessage($chatId, "❌ Произошла ошибка. Попробуйте позже или обратитесь к администратору.");
            $this->clearUserState($userId);
        }
    }

    // =============== ADMIN METHODS ===============

    private function sendAdminMenu($chatId, $messageId = null)
    {
        $keyboard = $this->getAdminMenuKeyboard();
        $text = "⚙️ <b>Админ-панель:</b>\n\nВыберите действие:";
        
        if ($messageId) {
            $this->editMessage($chatId, $messageId, $text, $keyboard, 'HTML');
        } else {
            $this->sendMessage($chatId, $text, $keyboard, 'HTML');
        }
    }

    private function showRepairsList($chatId, $messageId)
    {
        $repairs = RepairRequest::with('branch')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($repairs->isEmpty()) {
            $this->editMessage($chatId, $messageId, "📋 <b>Заявки на ремонт</b>\n\nЗаявок не найдено.", $this->getBackKeyboard('admin_menu'), 'HTML');
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

        $keyboard = $this->getRepairsListKeyboard($repairs);
        $this->editMessage($chatId, $messageId, $message, $keyboard, 'HTML');
    }

    private function showRepairDetails($chatId, $messageId, $repairId)
    {
        $repair = RepairRequest::with('branch')->find($repairId);
        
        if (!$repair) {
            $this->editMessage($chatId, $messageId, "❌ Заявка не найдена.", $this->getBackKeyboard('admin_repairs'));
            return;
        }

        $status = $this->getStatusEmoji($repair->status);
        $date = $repair->created_at->format('d.m.Y H:i');
        $updated = $repair->updated_at->format('d.m.Y H:i');
        $username = $repair->username ? "@{$repair->username}" : "ID: {$repair->user_telegram_id}";

        $message = "🔧 <b>Заявка № {$repair->id}</b> $status\n\n";
        $message .= "📍 <b>Филиал:</b> {$repair->branch->name}\n";
        $message .= "🚪 <b>Кабинет:</b> {$repair->room_number}\n";
        $message .= "📝 <b>Проблема:</b>\n" . htmlspecialchars($repair->description) . "\n\n";
        $message .= "👤 <b>Пользователь:</b> $username\n";
        
        if (!empty($repair->phone)) {
            $message .= "📞 <b>Телефон:</b> {$repair->phone}\n";
        }
        
        $message .= "⏰ <b>Создано:</b> $date\n";
        $message .= "🔄 <b>Обновлено:</b> $updated\n";

        $keyboard = $this->getStatusKeyboard($repairId);
        $this->editMessage($chatId, $messageId, $message, $keyboard, 'HTML');
    }

    private function updateRepairStatus($chatId, $messageId, $repairId, $newStatus)
    {
        try {
            $repair = RepairRequest::find($repairId);
            if (!$repair) {
                $this->editMessage($chatId, $messageId, "❌ Заявка не найдена.", $this->getBackKeyboard('admin_repairs'));
                return;
            }

            $repair->update(['status' => $newStatus]);

            $statusText = $this->getStatusText($newStatus);
            $this->showRepairDetails($chatId, $messageId, $repairId);
            $this->answerCallbackQuery($messageId, "✅ Статус изменен на: $statusText");

        } catch (\Exception $e) {
            Log::error('Error updating repair status: ' . $e->getMessage());
            $this->editMessage($chatId, $messageId, "❌ Ошибка обновления статуса.", $this->getBackKeyboard('admin_repairs'));
        }
    }

    private function showCartridgesList($chatId, $messageId)
    {
        $cartridges = CartridgeReplacement::with('branch')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($cartridges->isEmpty()) {
            $this->editMessage($chatId, $messageId, "🖨️ <b>История картриджей</b>\n\nЗаписей не найдено.", $this->getBackKeyboard('admin_menu'), 'HTML');
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

        $keyboard = $this->getBackKeyboard('admin_menu');
        $this->editMessage($chatId, $messageId, $message, $keyboard, 'HTML');
    }

    // =============== NOTIFICATION METHODS ===============

    private function notifyAdminsAboutRepair($repair, $branchName)
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

            $notifiedCount = 0;
            foreach ($admins as $admin) {
                try {
                    $result = $this->sendMessage($admin->telegram_id, $message, null, 'HTML');
                    if ($result) {
                        $notifiedCount++;
                        Log::info("Admin notified successfully", ['admin_id' => $admin->id, 'telegram_id' => $admin->telegram_id]);
                    } else {
                        Log::warning("Failed to notify admin", ['admin_id' => $admin->id, 'telegram_id' => $admin->telegram_id]);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to notify admin {$admin->telegram_id}: " . $e->getMessage());
                }
            }
            
            Log::info("Repair notification sent", [
                'repair_id' => $repair->id,
                'total_admins' => $admins->count(),
                'notified_count' => $notifiedCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error notifying admins about repair: ' . $e->getMessage());
        }
    }

    private function notifyAdminsAboutCartridge($cartridge, $branchName)
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

            $notifiedCount = 0;
            foreach ($admins as $admin) {
                try {
                    $result = $this->sendMessage($admin->telegram_id, $message, null, 'HTML');
                    if ($result) {
                        $notifiedCount++;
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to notify admin {$admin->telegram_id}: " . $e->getMessage());
                }
            }
            
            Log::info("Cartridge notification sent", [
                'cartridge_id' => $cartridge->id,
                'total_admins' => $admins->count(),
                'notified_count' => $notifiedCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error notifying admins about cartridge: ' . $e->getMessage());
        }
    }

    // =============== HELPER METHODS ===============

    private function isAdmin($userId)
    {
        return Admin::where('telegram_id', $userId)->where('is_active', true)->exists();
    }

    private function getUserState($userId)
    {
        return Cache::get("telegram_user_state_{$userId}");
    }

    private function setUserState($userId, $state, $tempData = [])
    {
        Cache::put("telegram_user_state_{$userId}", [
            'state' => $state,
            'temp_data' => $tempData,
            'updated_at' => now()
        ], now()->addHours(24));
    }

    private function clearUserState($userId)
    {
        Cache::forget("telegram_user_state_{$userId}");
    }

    private function getStatusEmoji($status)
    {
        return match($status) {
            'нова' => '🆕',
            'в_роботі' => '⚙️',
            'виконана' => '✅',
            default => '❓'
        };
    }

    private function getStatusText($status)
    {
        return match($status) {
            'нова' => 'Новая',
            'в_роботі' => 'В работе',
            'виконана' => 'Выполнена',
            default => 'Неизвестный'
        };
    }

    private function truncateText($text, $length)
    {
        return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
    }

    // =============== KEYBOARD METHODS ===============

    private function getMainMenuKeyboard()
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '🔧 Вызов IT мастера', 'callback_data' => 'repair_request']
                ],
                [
                    ['text' => '🖨️ Замена картриджа', 'callback_data' => 'cartridge_request']
                ],
                [
                    ['text' => '⚙️ Админ-панель', 'callback_data' => 'admin_menu']
                ]
            ]
        ];
    }

    private function getBranchesKeyboard($branches)
    {
        $keyboard = [];
        
        foreach ($branches as $branch) {
            $keyboard[] = [
                ['text' => $branch->name, 'callback_data' => "branch_select:{$branch->id}"]
            ];
        }
        
        $keyboard[] = [
            ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
        ];
        
        return ['inline_keyboard' => $keyboard];
    }

    private function getCancelKeyboard()
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '❌ Отмена', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    private function getPhoneKeyboard()
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '⏭️ Пропустить', 'callback_data' => 'skip_phone']
                ],
                [
                    ['text' => '❌ Отмена', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    private function getAdminMenuKeyboard()
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '📊 Заявки на ремонт', 'callback_data' => 'admin_repairs'],
                    ['text' => '🖨️ История картриджей', 'callback_data' => 'admin_cartridges']
                ],
                [
                    ['text' => '📈 Статистика', 'callback_data' => 'admin_stats']
                ],
                [
                    ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    private function getRepairsListKeyboard($repairs)
    {
        $keyboard = [];
        
        foreach ($repairs->take(5) as $repair) {
            $status = $this->getStatusEmoji($repair->status);
            $text = "#{$repair->id} $status {$repair->branch->name}";
            if (strlen($text) > 40) {
                $text = substr($text, 0, 37) . '...';
            }
            $keyboard[] = [
                ['text' => $text, 'callback_data' => "repair_details:{$repair->id}"]
            ];
        }
        
        $keyboard[] = [
            ['text' => '🔄 Обновить', 'callback_data' => 'admin_repairs'],
            ['text' => '◀️ Админ-панель', 'callback_data' => 'admin_menu']
        ];
        
        return ['inline_keyboard' => $keyboard];
    }

    private function getStatusKeyboard($repairId)
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '🔄 В работу', 'callback_data' => "status_update:$repairId:в_роботі"]
                ],
                [
                    ['text' => '✅ Выполнена', 'callback_data' => "status_update:$repairId:виконана"]
                ],
                [
                    ['text' => '🔙 Новая', 'callback_data' => "status_update:$repairId:нова"]
                ],
                [
                    ['text' => '◀️ К списку', 'callback_data' => 'admin_repairs']
                ]
            ]
        ];
    }

    private function getBackKeyboard($backAction)
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '◀️ Назад', 'callback_data' => $backAction]
                ],
                [
                    ['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']
                ]
            ]
        ];
    }

    // =============== MESSAGE METHODS ===============

    private function sendWelcomeMessage($chatId, $username)
    {
        $name = $username ? "@$username" : "Пользователь";
        $text = "🤖 Добро пожаловать, $name!\n\n" .
               "Я бот для подачи заявок на ремонт оборудования и замены картриджей.\n\n" .
               "Что вы хотите сделать?";
        
        $this->sendMessage($chatId, $text, $this->getMainMenuKeyboard());
    }

    private function sendHelpMessage($chatId)
    {
        $text = "📋 <b>Справка по боту:</b>\n\n" .
               "🔧 <b>Вызов IT мастера</b> - подать заявку на ремонт оборудования\n" .
               "🖨️ <b>Замена картриджа</b> - запрос на замену картриджа\n\n" .
               "📞 <b>Команды:</b>\n" .
               "/start - Главное меню\n" .
               "/help - Эта справка\n" .
               "/cancel - Отменить текущее действие\n" .
               "/admin - Админ-панель (только для администраторов)\n" .
               "/status - Статистика системы\n\n" .
               "❓ Если у вас возникли вопросы, обратитесь к администратору.";
        
        $this->sendMessage($chatId, $text, $this->getMainMenuKeyboard(), 'HTML');
    }

    private function sendMainMenu($chatId)
    {
        $this->sendMessage($chatId, "Выберите действие из главного меню:", $this->getMainMenuKeyboard());
    }

    // =============== TELEGRAM API METHODS ===============

    private function sendMessage($chatId, $text, $replyMarkup = null, $parseMode = null)
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text
        ];

        if ($parseMode) {
            $data['parse_mode'] = $parseMode;
        }

        if ($replyMarkup) {
            $data['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->makeRequest('sendMessage', $data);
    }

    private function editMessage($chatId, $messageId, $text, $replyMarkup = null, $parseMode = null)
    {
        $data = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text
        ];

        if ($parseMode) {
            $data['parse_mode'] = $parseMode;
        }

        if ($replyMarkup) {
            $data['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->makeRequest('editMessageText', $data);
    }

    private function answerCallbackQuery($callbackQueryId, $text = null)
    {
        $data = ['callback_query_id' => $callbackQueryId];
        
        if ($text) {
            $data['text'] = $text;
        }

        return $this->makeRequest('answerCallbackQuery', $data);
    }

    private function makeRequest($method, $data)
    {
        try {
            // Проверяем, что токен установлен
            if (empty($this->botToken)) {
                Log::error("Bot token is empty in makeRequest");
                return false;
            }
            
            $url = $this->apiUrl . $method;
            
            Log::info("Making Telegram API request", [
                'method' => $method,
                'chat_id' => $data['chat_id'] ?? 'N/A',
                'data_keys' => array_keys($data)
            ]);
            
            // Используем POST для всех запросов к Telegram API
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $data);
            
            $responseBody = $response->body();
            $statusCode = $response->status();
            
            Log::info("Telegram API response", [
                'method' => $method,
                'status' => $statusCode,
                'response_length' => strlen($responseBody)
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['ok']) && $result['ok']) {
                    return $result;
                } else {
                    Log::error("Telegram API returned error", [
                        'method' => $method,
                        'error_code' => $result['error_code'] ?? 'unknown',
                        'description' => $result['description'] ?? 'unknown',
                        'chat_id' => $data['chat_id'] ?? 'N/A'
                    ]);
                    return false;
                }
            } else {
                Log::error("HTTP error in Telegram API request", [
                    'method' => $method,
                    'status' => $statusCode,
                    'response' => $responseBody,
                    'chat_id' => $data['chat_id'] ?? 'N/A'
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error("Exception in Telegram API request", [
                'method' => $method,
                'error' => $e->getMessage(),
                'chat_id' => $data['chat_id'] ?? 'N/A',
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return false;
        }
    }

    // =============== PUBLIC API METHODS ===============

    /**
     * Получить информацию о пользователе по Telegram ID
     */
    public function getUserInfo(Request $request)
    {
        $request->validate([
            'telegram_id' => 'required|numeric'
        ]);

        $telegramId = $request->telegram_id;
        
        $admin = Admin::where('telegram_id', $telegramId)
            ->where('is_active', true)
            ->first();

        $webUser = User::where('telegram_id', $telegramId)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'telegram_id' => $telegramId,
                'is_admin' => (bool) $admin,
                'has_web_access' => (bool) $webUser,
                'admin_info' => $admin ? [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'is_active' => $admin->is_active,
                    'created_at' => $admin->created_at
                ] : null,
                'web_user_info' => $webUser ? [
                    'id' => $webUser->id,
                    'name' => $webUser->name,
                    'email' => $webUser->email,
                    'role' => $webUser->role
                ] : null
            ]
        ]);
    }

    /**
     * Получить статистику для бота
     */
    public function getStats(Request $request)
    {
        $stats = $this->getSystemStats();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'generated_at' => now()
        ]);
    }

    /**
     * Получить активные филиалы
     */
    public function getBranches(Request $request)
    {
        $branches = Branch::where('is_active', true)
            ->withCount(['repairRequests', 'cartridgeReplacements'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'branches' => $branches->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'repair_requests_count' => $branch->repair_requests_count,
                    'cartridge_replacements_count' => $branch->cartridge_replacements_count,
                    'created_at' => $branch->created_at
                ];
            })
        ]);
    }

    /**
     * Установить webhook
     */
    public function setWebhook(Request $request)
    {
        $webhookUrl = config('app.url') . '/api/telegram/webhook';
        
        $response = $this->makeRequest('setWebhook', [
            'url' => $webhookUrl
        ]);

        if ($response && $response['ok']) {
            return response()->json([
                'success' => true,
                'message' => 'Webhook установлен успешно',
                'url' => $webhookUrl
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка установки webhook',
                'response' => $response
            ], 500);
        }
    }

    /**
     * Получить информацию о webhook
     */
    public function getWebhookInfo()
    {
        $response = $this->makeRequest('getWebhookInfo', []);

        return response()->json([
            'success' => true,
            'webhook_info' => $response
        ]);
    }
}