<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Cache;

class StateManager
{
    public function getUserState(int $userId): ?array
    {
        return Cache::get("telegram_user_state_{$userId}");
    }

    public function setUserState(int $userId, string $state, array $tempData = []): void
    {
        Cache::put("telegram_user_state_{$userId}", [
            'state' => $state,
            'temp_data' => $tempData,
            'updated_at' => now()
        ], now()->addHours(24));
    }

    public function clearUserState(int $userId): void
    {
        Cache::forget("telegram_user_state_{$userId}");
    }

    public function updateTempData(int $userId, array $data): void
    {
        $userState = $this->getUserState($userId);
        if ($userState) {
            $userState['temp_data'] = array_merge($userState['temp_data'] ?? [], $data);
            $userState['updated_at'] = now();
            Cache::put("telegram_user_state_{$userId}", $userState, now()->addHours(24));
        }
    }
}