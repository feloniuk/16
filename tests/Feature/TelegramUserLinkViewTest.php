<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class TelegramUserLinkViewTest extends TestCase
{
    public function test_username_is_rendered_as_clickable_telegram_link(): void
    {
        $html = Blade::render("@include('partials.telegram-user-link', ['username' => 'olena', 'telegramId' => 123456])");

        $this->assertStringContainsString('href="https://t.me/olena"', $html);
        $this->assertStringContainsString('@olena', $html);
    }

    public function test_username_with_at_prefix_is_normalized(): void
    {
        $html = Blade::render("@include('partials.telegram-user-link', ['username' => '@olena', 'telegramId' => 123456])");

        $this->assertStringContainsString('href="https://t.me/olena"', $html);
        $this->assertStringNotContainsString('https://t.me/@olena', $html);
    }

    public function test_missing_username_falls_back_to_clickable_telegram_id(): void
    {
        $html = Blade::render("@include('partials.telegram-user-link', ['username' => null, 'telegramId' => 123456])");

        $this->assertStringContainsString('href="tg://user?id=123456"', $html);
        $this->assertStringContainsString('ID: 123456', $html);
    }
}
