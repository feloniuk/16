<?php

namespace App\Services\Telegram;

use App\Models\TelegramProfile;
use App\Models\User;

class TelegramProfileService
{
    /**
     * @param  array<string, mixed>  $from
     */
    public function sync(array $from, int $chatId): TelegramProfile
    {
        return TelegramProfile::query()->updateOrCreate(
            ['telegram_user_id' => $from['id']],
            [
                'telegram_chat_id' => $chatId,
                'username' => $from['username'] ?? null,
                'first_name' => $from['first_name'] ?? null,
                'last_name' => $from['last_name'] ?? null,
                'language_code' => $from['language_code'] ?? null,
                'is_bot' => $from['is_bot'] ?? false,
                'last_interaction_at' => now(),
            ],
        );
    }

    public function rememberWorkplace(int $telegramUserId, int $branchId, string $roomNumber): void
    {
        TelegramProfile::query()->where('telegram_user_id', $telegramUserId)->update([
            'default_branch_id' => $branchId,
            'default_room_number' => $roomNumber,
            'is_profile_complete' => true,
        ]);
    }

    public function isBlocked(int $telegramUserId): bool
    {
        return TelegramProfile::query()
            ->where('telegram_user_id', $telegramUserId)
            ->where('is_blocked', true)
            ->exists();
    }

    public function restoreAfterContactShare(int $telegramUserId): void
    {
        TelegramProfile::query()->where('telegram_user_id', $telegramUserId)->update([
            'is_blocked' => false,
            'notifications_enabled' => true,
            'last_interaction_at' => now(),
        ]);
    }

    public function isRecognizedAdmin(int $telegramUserId): bool
    {
        return User::where('telegram_id', $telegramUserId)->whereIn('role', ['admin', 'director'])->exists();
    }
}
