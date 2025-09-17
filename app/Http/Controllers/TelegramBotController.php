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
        
        // Логируем для отладки
        Log::info('TelegramBotController initialized', [
            'bot_token_exists' => !empty($this->botToken),
            'bot_token_first_chars' => substr($this->botToken, 0, 10) . '...',
            'api_url' => $this->apiUrl
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

            // Проверяем наличие обязательных полей
            if (!isset($update['update_id'])) {
                Log::warning('Invalid update format - missing update_id', $update);
                return response()->json(['status' => 'ok']);
            }

            if (isset($update['message'])) {
                $this->handleMessage($update['message']);
            } elseif (isset($update['callback_query'])) {
                $this->handleCallbackQuery($update['callback_query']);
            } else {
                Log::info('Unsupported update type', $update);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
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
        // Проверяем обязательные поля
        if (!isset($message['chat']['id'], $message['from']['id'])) {
            Log::warning('Invalid message format', $message);
            return;
        }

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
     * Обработка callback запросов (кнопки)
     */
    private function handleCallbackQuery($callbackQuery)
    {
        // Проверяем обязательные поля
        if (!isset($callbackQuery['message']['chat']['id'], $callbackQuery['from']['id'], $callbackQuery['id'])) {
            Log::warning('Invalid callback query format', $callbackQuery);
            return;
        }

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

    // =============== TELEGRAM API METHODS ===============

    private function sendMessage($chatId, $text, $replyMarkup = null, $parseMode = 'HTML')
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode
        ];

        if ($replyMarkup) {
            $data['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->makeRequest('sendMessage', $data);
    }

    private function editMessage($chatId, $messageId, $text, $replyMarkup = null, $parseMode = 'HTML')
    {
        $data = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => $parseMode
        ];

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
                'url' => $url,
                'data_keys' => array_keys($data),
                'bot_token_length' => strlen($this->botToken)
            ]);
            
            // Проверяем текст на длину (максимум 4096 символов для сообщений)
            if (isset($data['text']) && strlen($data['text']) > 4096) {
                $data['text'] = substr($data['text'], 0, 4093) . '...';
                Log::warning('Message truncated due to length limit');
            }
            
            // Используем POST для всех запросов к Telegram API
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->asForm() // Добавляем отправку как form data
                ->post($url, $data);
            
            Log::info("Telegram API response", [
                'method' => $method,
                'status' => $response->status(),
                'response_body' => $response->body()
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['ok']) && $result['ok']) {
                    return $result;
                } else {
                    Log::error("Telegram API returned error", [
                        'method' => $method,
                        'error' => $result,
                        'request_data' => $data
                    ]);
                    return false;
                }
            } else {
                Log::error("HTTP error in Telegram API request", [
                    'method' => $method,
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'request_data' => $data
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error("Exception in Telegram API request", [
                'method' => $method,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $data
            ]);
            return false;
        }
    }

    // =============== ОСТАЛЬНЫЕ МЕТОДЫ (без изменений) ===============
    
    // Все остальные методы остаются такими же, как в исходном коде
    // Для краткости я не буду их повторять, но они должны оставаться без изменений
    
    private function handleCommand($chatId, $userId, $username, $command)
    {
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

            default:
                $this->sendMessage($chatId, "Неизвестная команда. Используйте /help для справки.");
        }
    }

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
        $text = "📋 Справка по боту:\n\n" .
               "🔧 <b>Вызов IT мастера</b> - подать заявку на ремонт оборудования\n" .
               "🖨️ <b>Замена картриджа</b> - запрос на замену картриджа\n\n" .
               "📞 Команды:\n" .
               "/start - Главное меню\n" .
               "/help - Эта справка\n" .
               "/cancel - Отменить текущее действие\n" .
               "/admin - Админ-панель (только для администраторов)\n\n" .
               "❓ Если у вас возникли вопросы, обратитесь к администратору.";
        
        $this->sendMessage($chatId, $text);
    }

    private function sendMainMenu($chatId)
    {
        $this->sendMessage($chatId, "Выберите действие из главного меню:", $this->getMainMenuKeyboard());
    }

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

    // Тестовый метод для проверки API
    public function testApi()
    {
        $response = $this->makeRequest('getMe', []);
        
        return response()->json([
            'bot_token_exists' => !empty($this->botToken),
            'api_response' => $response
        ]);
    }

    // Получить информацию о webhook
    public function getWebhookInfo()
    {
        $response = $this->makeRequest('getWebhookInfo', []);

        return response()->json([
            'success' => true,
            'webhook_info' => $response
        ]);
    }

    // Установить webhook
    public function setWebhook(Request $request)
    {
        $webhookUrl = config('app.url') . '/api/telegram/webhook';
        
        $response = $this->makeRequest('setWebhook', [
            'url' => $webhookUrl,
            'drop_pending_updates' => true // Очищаем накопившиеся обновления
        ]);

        if ($response && $response['ok']) {
            return response()->json([
                'success' => true,
                'message' => 'Webhook установлен успешно',
                'url' => $webhookUrl,
                'response' => $response
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка установки webhook',
                'response' => $response
            ], 500);
        }
    }
}