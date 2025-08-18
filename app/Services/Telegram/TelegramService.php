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
    private MessageCacheService $messageCache;

    public function __construct(MessageCacheService $messageCache)
    {
        $this->botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
        $this->messageCache = $messageCache;
        
        Log::info('TelegramService initialized', [
            'bot_token_exists' => !empty($this->botToken),
            'bot_token_length' => $this->botToken ? strlen($this->botToken) : 0
        ]);
    }

    public function sendMessage(int $chatId, string $text, ?array $replyMarkup = null, ?string $parseMode = 'HTML')
    {
        // Очищаем и валидируем текст
        $text = $this->sanitizeText($text);
        
        $data = [
            'chat_id' => $chatId,
            'text' => $text
        ];

        // Проверяем HTML и устанавливаем parse_mode только если HTML валиден
        if ($parseMode === 'HTML' && $this->isValidHTML($text)) {
            $data['parse_mode'] = $parseMode;
        } elseif ($parseMode === 'HTML') {
            // Если HTML невалиден, отправляем как plain text
            Log::warning('Invalid HTML detected, sending as plain text', [
                'chat_id' => $chatId,
                'text_preview' => substr($text, 0, 100)
            ]);
        }

        if ($replyMarkup) {
            $data['reply_markup'] = $replyMarkup;
        }

        return $this->makeRequest('sendMessage', $data);
    }

    public function editMessage(int $chatId, int $messageId, string $text, ?array $replyMarkup = null, ?string $parseMode = 'HTML')
    {
        // Очищаем и валидируем текст
        $text = $this->sanitizeText($text);
        
        // Проверяем, отличается ли новое сообщение от предыдущего
        if (!$this->messageCache->isDifferent($chatId, $messageId, $text, $replyMarkup)) {
            Log::info('Message content is identical, skipping edit', [
                'chat_id' => $chatId,
                'message_id' => $messageId
            ]);
            return ['ok' => true, 'result' => 'identical_content'];
        }

        $data = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text
        ];

        // Проверяем HTML и устанавливаем parse_mode только если HTML валиден
        if ($parseMode === 'HTML' && $this->isValidHTML($text)) {
            $data['parse_mode'] = $parseMode;
        } elseif ($parseMode === 'HTML') {
            Log::warning('Invalid HTML detected in edit, sending as plain text', [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text_preview' => substr($text, 0, 100)
            ]);
        }

        if ($replyMarkup) {
            $data['reply_markup'] = $replyMarkup;
        }

        $result = $this->makeRequest('editMessageText', $data);
        
        // Если редактирование прошло успешно, сохраняем в кеш
        if ($result) {
            $this->messageCache->store($chatId, $messageId, $text, $replyMarkup);
        }
        
        return $result;
    }

    public function editMessageSafe(int $chatId, int $messageId, string $text, ?array $replyMarkup = null, ?string $parseMode = 'HTML')
    {
        // Безопасное редактирование - удаляем старое сообщение и отправляем новое при ошибке
        $result = $this->editMessage($chatId, $messageId, $text, $replyMarkup, $parseMode);
        
        if (!$result || (isset($result['ok']) && !$result['ok'])) {
            // Если редактирование не удалось, отправляем новое сообщение
            Log::warning("Failed to edit message, sending new one", [
                'chat_id' => $chatId,
                'message_id' => $messageId
            ]);
            
            return $this->sendMessage($chatId, $text, $replyMarkup, $parseMode);
        }
        
        return $result;
    }

    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null)
    {
        $data = ['callback_query_id' => $callbackQueryId];
        
        if ($text) {
            $data['text'] = $this->sanitizeText($text);
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

    /**
     * Проверка валидности HTML для Telegram
     */
    private function isValidHTML(string $text): bool
    {
        // Список разрешенных HTML тегов в Telegram
        $allowedTags = ['b', 'strong', 'i', 'em', 'u', 'ins', 's', 'strike', 'del', 'span', 'tg-spoiler', 'a', 'tg-emoji', 'code', 'pre'];
        
        // Проверяем, есть ли HTML теги
        if (strpos($text, '<') === false) {
            return true; // Нет HTML тегов - это нормально
        }

        // Находим все теги
        preg_match_all('/<\/?([a-zA-Z-]+)(?:\s[^>]*)?>/i', $text, $matches);
        
        if (empty($matches[1])) {
            return true; // Нет тегов
        }

        // Проверяем, что все теги разрешены
        foreach ($matches[1] as $tag) {
            if (!in_array(strtolower($tag), $allowedTags)) {
                Log::warning('Invalid HTML tag detected', ['tag' => $tag, 'text' => substr($text, 0, 100)]);
                return false;
            }
        }

        // Проверяем парность тегов (упрощенная проверка)
        $openTags = [];
        foreach ($matches[0] as $index => $fullTag) {
            $tag = $matches[1][$index];
            
            if (strpos($fullTag, '</') === 0) {
                // Закрывающий тег
                if (empty($openTags) || end($openTags) !== strtolower($tag)) {
                    Log::warning('Mismatched HTML tags', ['tag' => $tag, 'text' => substr($text, 0, 100)]);
                    return false;
                }
                array_pop($openTags);
            } else {
                // Открывающий тег
                $openTags[] = strtolower($tag);
            }
        }

        // Проверяем, что все теги закрыты
        if (!empty($openTags)) {
            Log::warning('Unclosed HTML tags', ['tags' => $openTags, 'text' => substr($text, 0, 100)]);
            return false;
        }

        return true;
    }

    /**
     * Очистка и валидация текста для предотвращения ошибок UTF-8
     */
    private function sanitizeText(string $text): string
    {
        // Удаляем или заменяем проблематичные символы
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        
        // Удаляем null bytes и другие проблематичные символы
        $text = str_replace(["\0", "\x00"], '', $text);
        
        // Исправляем неправильно экранированные HTML теги
        $text = $this->fixHTMLEntities($text);
        
        // Ограничиваем длину (Telegram лимит 4096 символов)
        if (mb_strlen($text) > 4096) {
            $text = mb_substr($text, 0, 4093) . '...';
        }
        
        return trim($text);
    }

    /**
     * Исправление HTML entities
     */
    private function fixHTMLEntities(string $text): string
    {
        // Исправляем двойное экранирование
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Заменяем неправильные символы
        $replacements = [
            '&lt;b&gt;' => '<b>',
            '&lt;/b&gt;' => '</b>',
            '&lt;i&gt;' => '<i>',
            '&lt;/i&gt;' => '</i>',
            '&lt;u&gt;' => '<u>',
            '&lt;/u&gt;' => '</u>',
            '&lt;code&gt;' => '<code>',
            '&lt;/code&gt;' => '</code>',
            '&lt;pre&gt;' => '<pre>',
            '&lt;/pre&gt;' => '</pre>',
            '&amp;' => '&'
        ];
        
        $text = str_replace(array_keys($replacements), array_values($replacements), $text);
        
        return $text;
    }

    /**
     * Валидация данных перед отправкой
     */
    private function validateRequestData(array $data): array
    {
        // Рекурсивно очищаем все строковые значения
        array_walk_recursive($data, function (&$value) {
            if (is_string($value)) {
                $value = $this->sanitizeText($value);
            }
        });
        
        return $data;
    }

    private function makeRequest(string $method, array $data)
    {
        try {
            if (empty($this->botToken)) {
                Log::error("Bot token is empty in makeRequest");
                return false;
            }
            
            // Валидируем данные
            $data = $this->validateRequestData($data);
            
            $url = $this->apiUrl . $method;
            
            Log::info("Making Telegram API request", [
                'method' => $method,
                'chat_id' => $data['chat_id'] ?? 'N/A',
                'data_keys' => array_keys($data),
                'has_parse_mode' => isset($data['parse_mode'])
            ]);
            
            // Используем более надежный способ отправки запроса
            $response = Http::timeout(30)
                ->retry(3, 1000, function ($exception, $request) {
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                })
                ->withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json'
                ])
                ->post($url, $data);
            
            $responseBody = $response->body();
            $statusCode = $response->status();
            
            Log::info("Telegram API response", [
                'method' => $method,
                'status' => $statusCode,
                'response_length' => strlen($responseBody),
                'chat_id' => $data['chat_id'] ?? 'N/A'
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['ok']) && $result['ok']) {
                    return $result;
                } else {
                    // Специальная обработка ошибки "message is not modified"
                    if (isset($result['error_code']) && $result['error_code'] === 400 && 
                        strpos($result['description'] ?? '', 'message is not modified') !== false) {
                        Log::info("Message not modified - treating as success", [
                            'method' => $method,
                            'chat_id' => $data['chat_id'] ?? 'N/A'
                        ]);
                        return ['ok' => true, 'result' => 'not_modified'];
                    }
                    
                    // Обработка ошибки "Bad Request: can't parse entities"
                    if (isset($result['error_code']) && $result['error_code'] === 400 && 
                        strpos($result['description'] ?? '', "can't parse entities") !== false) {
                        Log::warning("Parse entities error, retrying without parse_mode", [
                            'method' => $method,
                            'chat_id' => $data['chat_id'] ?? 'N/A',
                            'original_text' => substr($data['text'] ?? '', 0, 100)
                        ]);
                        
                        // Повторяем запрос без parse_mode
                        if (isset($data['parse_mode'])) {
                            unset($data['parse_mode']);
                            return $this->makeRequest($method, $data);
                        }
                    }
                    
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
            
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("HTTP Client error in Telegram API request", [
                'method' => $method,
                'error' => $e->getMessage(),
                'chat_id' => $data['chat_id'] ?? 'N/A'
            ]);
            return false;
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