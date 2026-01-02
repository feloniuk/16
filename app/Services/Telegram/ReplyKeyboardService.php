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
                [
                    ['text' => '📋 Керування інвентарем'],
                ],
                [
                    ['text' => '⚙️ Панель адміністратора'],
                ],
                [
                    ['text' => '/help'],
                    ['text' => '/cancel'],
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
                    ['text' => '❌ Скасування'],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
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
