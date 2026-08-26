<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\TelegramBotController;
use App\Models\TelegramProfile;
use App\Services\Telegram\KeyboardService;
use App\Services\Telegram\TelegramProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_requests_without_the_configured_secret(): void
    {
        config()->set('services.telegram.webhook_secret', 'test-secret');

        $this->postJson('/api/telegram/webhook', [])->assertForbidden();
    }

    public function test_webhook_accepts_the_configured_secret(): void
    {
        config()->set('services.telegram.webhook_secret', 'test-secret');

        $this->withHeader('X-Telegram-Bot-Api-Secret-Token', 'test-secret')
            ->postJson('/api/telegram/webhook', [])
            ->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    public function test_webhook_fails_closed_when_production_secret_is_missing(): void
    {
        config()->set('services.telegram.webhook_secret', null);
        $environment = $this->app['env'];
        $this->app['env'] = 'production';

        try {
            $this->postJson('/api/telegram/webhook', [])->assertStatus(503);
        } finally {
            $this->app['env'] = $environment;
        }
    }

    public function test_webhook_registration_sends_the_configured_secret_to_telegram(): void
    {
        config()->set('services.telegram.bot_token', 'bot-token');
        config()->set('services.telegram.webhook_secret', 'webhook-secret');
        Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);

        app(TelegramBotController::class)->setWebhook(Request::create('/api/telegram/set-webhook', 'POST'));

        Http::assertSent(fn ($request): bool => $request['secret_token'] === 'webhook-secret');
    }

    public function test_telegram_debug_and_management_routes_are_not_publicly_registered(): void
    {
        foreach ([
            '/api/telegram/test-api',
            '/api/telegram/webhook-info',
            '/api/telegram/set-webhook',
            '/api/telegram/clear-webhook',
            '/api/telegram/user-info',
            '/api/telegram/stats',
            '/api/telegram/branches',
        ] as $url) {
            $this->getJson($url)->assertNotFound();
        }
    }

    public function test_profile_service_upserts_telegram_identity_and_workplace(): void
    {
        $profileService = app(TelegramProfileService::class);
        $profileService->sync([
            'id' => 123456,
            'username' => 'olena',
            'first_name' => 'Olena',
            'last_name' => 'Test',
            'language_code' => 'uk',
        ], 987654);
        $profileService->rememberWorkplace(123456, 1, '204');

        $this->assertDatabaseHas('telegram_profiles', [
            'telegram_user_id' => 123456,
            'telegram_chat_id' => 987654,
            'username' => 'olena',
            'default_branch_id' => 1,
            'default_room_number' => '204',
            'is_profile_complete' => true,
        ]);
        $this->assertInstanceOf(TelegramProfile::class, TelegramProfile::first());
    }

    public function test_profile_sync_does_not_unblock_an_explicitly_blocked_profile(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 123456,
            'telegram_chat_id' => 987654,
            'is_blocked' => true,
        ]);

        $profileService = app(TelegramProfileService::class);
        $profileService->sync([
            'id' => 123456,
            'username' => 'olena',
            'first_name' => 'Olena',
            'last_name' => 'Test',
            'language_code' => 'uk',
        ], 987654);

        $this->assertTrue(TelegramProfile::where('telegram_user_id', 123456)->firstOrFail()->is_blocked);
    }

    public function test_main_menu_keyboard_callback_data_unchanged_after_copy_pass(): void
    {
        $keyboard = app(KeyboardService::class)->getMainMenuKeyboard(123456);

        $this->assertSame([
            [['callback_data' => 'repair_request']],
            [['callback_data' => 'cartridge_request']],
        ], array_map(
            fn (array $row) => array_map(fn (array $button) => ['callback_data' => $button['callback_data']], $row),
            $keyboard['inline_keyboard']
        ));
    }
}
