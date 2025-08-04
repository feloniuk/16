<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Cache;

class MessageCacheService
{
    private const CACHE_PREFIX = 'telegram_message_';
    private const CACHE_TTL = 3600; // 1 час

    /**
     * Проверяет, отличается ли новое сообщение от последнего отправленного
     */
    public function isDifferent(int $chatId, int $messageId, string $text, ?array $replyMarkup = null): bool
    {
        $cacheKey = $this->getCacheKey($chatId, $messageId);
        $newHash = $this->generateHash($text, $replyMarkup);
        $cachedHash = Cache::get($cacheKey);
        
        return $cachedHash !== $newHash;
    }

    /**
     * Сохраняет хеш сообщения в кеш
     */
    public function store(int $chatId, int $messageId, string $text, ?array $replyMarkup = null): void
    {
        $cacheKey = $this->getCacheKey($chatId, $messageId);
        $hash = $this->generateHash($text, $replyMarkup);
        
        Cache::put($cacheKey, $hash, now()->addSeconds(self::CACHE_TTL));
    }

    /**
     * Очищает кеш сообщения
     */
    public function forget(int $chatId, int $messageId): void
    {
        $cacheKey = $this->getCacheKey($chatId, $messageId);
        Cache::forget($cacheKey);
    }

    /**
     * Генерирует хеш для сообщения и клавиатуры
     */
    private function generateHash(string $text, ?array $replyMarkup = null): string
    {
        // Убираем временные метки из текста для корректного сравнения
        $cleanText = preg_replace('/🕐 \d{2}:\d{2}:\d{2}$/', '', trim($text));
        
        $data = [
            'text' => $cleanText,
            'keyboard' => $replyMarkup
        ];
        
        return md5(serialize($data));
    }

    /**
     * Генерирует ключ кеша
     */
    private function getCacheKey(int $chatId, int $messageId): string
    {
        return self::CACHE_PREFIX . $chatId . '_' . $messageId;
    }
}