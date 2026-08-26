<?php

namespace App\Services\Telegram;

class ReplyKeyboardService
{
    public function getMainMenuKeyboard(): array
    {
        return [
            'keyboard' => [
                [
                    ['text' => '🔧 Виклик IT майстра'],
                    ['text' => '🖨️ Заміна картриджа'],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
            'selective' => false,
        ];
    }

    public function getCancelKeyboard(): array
    {
        return [
            'keyboard' => [
                [
                    ['text' => '❌ Скасувати'],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
            'selective' => false,
        ];
    }

    public function getContactKeyboard(): array
    {
        return [
            'keyboard' => [
                [
                    ['text' => '📞 Поділитися контактом', 'request_contact' => true],
                ],
                [
                    ['text' => '⏭️ Пропустити'],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'selective' => false,
        ];
    }

    public function getHiddenKeyboard(): array
    {
        return [
            'remove_keyboard' => true,
            'selective' => false,
        ];
    }
}
