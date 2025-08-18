<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramService;
use App\Services\Telegram\CallbackHandler;
use App\Services\Telegram\MessageHandler;
use App\Services\Telegram\StateManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    private TelegramService $telegramService;
    private CallbackHandler $callbackHandler;
    private MessageHandler $messageHandler;
    private StateManager $stateManager;

    public function __construct(
        TelegramService $telegramService,
        CallbackHandler $callbackHandler, 
        MessageHandler $messageHandler,
        StateManager $stateManager
    ) {
        $this->telegramService = $telegramService;
        $this->callbackHandler = $callbackHandler;
        $this->messageHandler = $messageHandler;
        $this->stateManager = $stateManager;
    }

    /**
     * Webhook для обработки сообщений от Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $update = $request->all();
            
            // Логируем только основную информацию, избегая больших объемов данных
            Log::info('Telegram webhook received', [
                'update_id' => $update['update_id'] ?? 'unknown',
                'type' => $this->getUpdateType($update),
                'chat_id' => $this->extractChatId($update),
                'user_id' => $this->extractUserId($update)
            ]);

            if (empty($update)) {
                Log::warning('Empty webhook update received');
                return response()->json(['status' => 'ok']);
            }

            // Валидируем структуру update
            if (!$this->validateUpdate($update)) {
                Log::warning('Invalid update structure', ['update_id' => $update['update_id'] ?? 'unknown']);
                return response()->json(['status' => 'ok']);
            }

            if (isset($update['message'])) {
                $this->messageHandler->handle($update['message']);
            } elseif (isset($update['callback_query'])) {
                $this->callbackHandler->handle($update['callback_query']);
            } else {
                Log::info('Unhandled update type', [
                    'update_id' => $update['update_id'] ?? 'unknown',
                    'available_keys' => array_keys($update)
                ]);
            }

            return response()->json(['status' => 'ok']);
            
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data_keys' => array_keys($request->all())
            ]);
            
            // Всегда возвращаем 200 OK, чтобы Telegram не повторял запрос
            return response()->json(['error' => 'Internal error'], 200);
        }
    }

    /**
     * Определяет тип обновления
     */
    private function getUpdateType(array $update): string
    {
        if (isset($update['message'])) return 'message';
        if (isset($update['callback_query'])) return 'callback_query';
        if (isset($update['inline_query'])) return 'inline_query';
        if (isset($update['chosen_inline_result'])) return 'chosen_inline_result';
        if (isset($update['edited_message'])) return 'edited_message';
        
        return 'unknown';
    }

    /**
     * Извлекает chat_id из update
     */
    private function extractChatId(array $update): ?int
    {
        if (isset($update['message']['chat']['id'])) {
            return $update['message']['chat']['id'];
        }
        if (isset($update['callback_query']['message']['chat']['id'])) {
            return $update['callback_query']['message']['chat']['id'];
        }
        
        return null;
    }

    /**
     * Извлекает user_id из update
     */
    private function extractUserId(array $update): ?int
    {
        if (isset($update['message']['from']['id'])) {
            return $update['message']['from']['id'];
        }
        if (isset($update['callback_query']['from']['id'])) {
            return $update['callback_query']['from']['id'];
        }
        
        return null;
    }

    /**
     * Валидирует структуру update
     */
    private function validateUpdate(array $update): bool
    {
        // Проверяем наличие update_id
        if (!isset($update['update_id']) || !is_numeric($update['update_id'])) {
            return false;
        }

        // Проверяем структуру message
        if (isset($update['message'])) {
            $message = $update['message'];
            if (!isset($message['message_id'], $message['date'], $message['chat'], $message['from'])) {
                return false;
            }
        }

        // Проверяем структуру callback_query
        if (isset($update['callback_query'])) {
            $callback = $update['callback_query'];
            if (!isset($callback['id'], $callback['from'], $callback['data'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * API методы для внешнего использования
     */
    public function getUserInfo(Request $request)
    {
        try {
            $request->validate([
                'telegram_id' => 'required|numeric'
            ]);

            return $this->telegramService->getUserInfo($request->telegram_id);
            
        } catch (\Exception $e) {
            Log::error('Error in getUserInfo: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    public function sendNotification(Request $request)
    {
        try {
            $request->validate([
                'telegram_id' => 'required|numeric',
                'message' => 'required|string|max:4096'
            ]);

            $result = $this->telegramService->sendMessage(
                $request->telegram_id, 
                $request->message
            );

            return response()->json([
                'success' => (bool) $result,
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in sendNotification: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Тестовые методы для отладки
     */
    public function testApi()
    {
        try {
            $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
            
            if (!$botToken) {
                return response()->json(['error' => 'Bot token not configured'], 500);
            }

            $response = \Illuminate\Support\Facades\Http::get("https://api.telegram.org/bot{$botToken}/getMe");
            $result = $response->json();

            return response()->json([
                'success' => $result['ok'] ?? false,
                'bot_info' => $result['result'] ?? null,
                'response_status' => $response->status()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in testApi: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    public function getWebhookInfo()
    {
        try {
            $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
            
            if (!$botToken) {
                return response()->json(['error' => 'Bot token not configured'], 500);
            }

            $response = \Illuminate\Support\Facades\Http::get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");
            $result = $response->json();

            return response()->json([
                'success' => $result['ok'] ?? false,
                'webhook_info' => $result['result'] ?? null,
                'response_status' => $response->status()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getWebhookInfo: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    public function setWebhook(Request $request)
    {
        try {
            $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
            
            if (!$botToken) {
                return response()->json(['error' => 'Bot token not configured'], 500);
            }

            $webhookUrl = $request->input('url', config('app.url') . '/api/telegram/webhook');

            $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
                'url' => $webhookUrl,
                'allowed_updates' => ['message', 'callback_query']
            ]);
            
            $result = $response->json();

            return response()->json([
                'success' => $result['ok'] ?? false,
                'description' => $result['description'] ?? null,
                'webhook_url' => $webhookUrl,
                'response_status' => $response->status()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in setWebhook: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Получить статистику для API
     */
    public function getStats()
    {
        try {
            $stats = [
                'repairs' => [
                    'total' => \App\Models\RepairRequest::count(),
                    'new' => \App\Models\RepairRequest::where('status', 'нова')->count(),
                    'in_progress' => \App\Models\RepairRequest::where('status', 'в_роботі')->count(),
                    'completed' => \App\Models\RepairRequest::where('status', 'виконана')->count()
                ],
                'cartridges' => [
                    'total' => \App\Models\CartridgeReplacement::count(),
                    'this_month' => \App\Models\CartridgeReplacement::whereMonth('created_at', now()->month)->count()
                ],
                'branches' => \App\Models\Branch::where('is_active', true)->count(),
                'generated_at' => now()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getStats: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Получить список филиалов
     */
    public function getBranches()
    {
        try {
            $branches = \App\Models\Branch::where('is_active', true)
                ->withCount(['repairRequests', 'cartridgeReplacements'])
                ->orderBy('name')
                ->get()
                ->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'repair_requests_count' => $branch->repair_requests_count ?? 0,
                        'cartridge_replacements_count' => $branch->cartridge_replacements_count ?? 0,
                        'created_at' => $branch->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'branches' => $branches
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getBranches: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}