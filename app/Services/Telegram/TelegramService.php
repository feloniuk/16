<?php

namespace App\Services\Telegram;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $botToken;
    private string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
        
        Log::info('TelegramService initialized', [
            'bot_token_exists' => !empty($this->botToken),
            'bot_token_length' => $this->botToken ? strlen($this->botToken) : 0
        ]);
    }

    public function sendMessage(int $chatId, string $text, ?array $replyMarkup = null, ?string $parseMode = 'HTML'): array|false
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text
        ];

        if ($parseMode) {
            $data['parse_mode'] = $parseMode;
        }

        if ($replyMarkup) {
            $data['reply_markup'] = $replyMarkup; // Убираем json_encode
        }

        return $this->makeRequest('sendMessage', $data);
    }

    public function editMessage(int $chatId, int $messageId, string $text, ?array $replyMarkup = null, ?string $parseMode = 'HTML'): array|false
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
            $data['reply_markup'] = $replyMarkup; // Убираем json_encode
        }

        return $this->makeRequest('editMessageText', $data);
    }

    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null): array|false
    {
        $data = ['callback_query_id' => $callbackQueryId];
        
        if ($text) {
            $data['text'] = $text;
        }

        return $this->makeRequest('answerCallbackQuery', $data);
    }

    public function getUserInfo(int $telegramId): array
    {
        $admin = Admin::where('telegram_id', $telegramId)
            ->where('is_active', true)
            ->first();

        $webUser = User::where('telegram_id', $telegramId)
            ->where('is_active', true)
            ->first();

        return [
            'success' => true,
            'data' => [
                'telegram_id' => $telegramId,
                'is_admin' => (bool) $admin,
                'has_web_access' => (bool) $webUser,
                'admin_info' => $admin ? [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'created_at' => $admin->created_at
                ] : null,
                'web_user_info' => $webUser ? [
                    'id' => $webUser->id,
                    'name' => $webUser->name,
                    'email' => $webUser->email,
                    'created_at' => $webUser->created_at
                ] : null
            ]
        ];
    }

    public function isAdmin(int $userId): bool
    {
        return Admin::where('telegram_id', $userId)->where('is_active', true)->exists();
    }

    private function makeRequest(string $method, array $data): array|false
    {
        try {
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
            
            // Правильная сериализация для Telegram API
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->asJson() // Автоматически установит Content-Type и сериализует данные
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
}