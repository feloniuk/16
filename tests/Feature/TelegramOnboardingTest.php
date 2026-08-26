<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\TelegramProfile;
use App\Services\Telegram\CallbackHandler;
use App\Services\Telegram\MessageHandler;
use App\Services\Telegram\StateManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);
    }

    private function message(int $userId, int $chatId, string $text = ''): array
    {
        return [
            'chat' => ['id' => $chatId],
            'from' => ['id' => $userId, 'username' => 'olena'],
            'text' => $text,
        ];
    }

    public function test_blocked_existing_profile_is_prompted_to_share_contact_before_old_button_actions(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 119,
            'telegram_chat_id' => 230,
            'contact_consent' => false,
            'is_blocked' => true,
        ]);

        app(MessageHandler::class)->handle($this->message(119, 230, '📋 Керування інвентарем'));

        $state = app(StateManager::class)->getUserState(119);
        $this->assertSame('onboarding_awaiting_contact', $state['state'] ?? null);

        Http::assertSent(function ($request) {
            $body = $request->data();
            $keyboard = collect($body['reply_markup']['keyboard'] ?? [])->flatten(1);

            return isset($body['text'])
                && str_contains($body['text'], 'Потрібно оновити дані профілю')
                && $keyboard->contains(fn ($button) => ($button['request_contact'] ?? false) === true);
        });
    }

    public function test_blocked_existing_profile_is_prompted_to_share_contact_before_old_inline_callbacks(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 121,
            'telegram_chat_id' => 232,
            'contact_consent' => false,
            'is_blocked' => true,
        ]);

        app(CallbackHandler::class)->handle([
            'id' => 'blocked-callback',
            'from' => ['id' => 121, 'username' => 'olena'],
            'message' => ['chat' => ['id' => 232], 'message_id' => 77],
            'data' => 'repair_request',
        ]);

        $state = app(StateManager::class)->getUserState(121);
        $this->assertSame('onboarding_awaiting_contact', $state['state'] ?? null);

        Http::assertSent(function ($request) {
            $body = $request->data();
            $keyboard = collect($body['reply_markup']['keyboard'] ?? [])->flatten(1);

            return isset($body['text'])
                && str_contains($body['text'], 'Потрібно оновити дані профілю')
                && $keyboard->contains(fn ($button) => ($button['request_contact'] ?? false) === true);
        });
    }

    public function test_sharing_contact_restores_blocked_profile(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 120,
            'telegram_chat_id' => 231,
            'contact_consent' => false,
            'is_blocked' => true,
        ]);

        app(StateManager::class)->setUserState(120, 'onboarding_awaiting_contact');

        $message = $this->message(120, 231);
        $message['contact'] = ['phone_number' => '+380991111111'];

        app(MessageHandler::class)->handle($message);

        $this->assertDatabaseHas('telegram_profiles', [
            'telegram_user_id' => 120,
            'contact_consent' => true,
            'phone' => '+380991111111',
            'is_blocked' => false,
        ]);
    }

    public function test_first_start_for_new_telegram_user_triggers_contact_prompt(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 111,
            'telegram_chat_id' => 222,
            'contact_consent' => false,
            'default_branch_id' => null,
        ]);

        app(MessageHandler::class)->handle($this->message(111, 222, '/start'));

        $state = app(StateManager::class)->getUserState(111);
        $this->assertSame('onboarding_awaiting_contact', $state['state'] ?? null);
    }

    public function test_already_onboarded_profile_via_contact_consent_is_not_reonboarded(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 112,
            'telegram_chat_id' => 223,
            'contact_consent' => true,
            'default_branch_id' => null,
        ]);

        app(MessageHandler::class)->handle($this->message(112, 223, '/start'));

        $state = app(StateManager::class)->getUserState(112);
        $this->assertNull($state);
    }

    public function test_already_onboarded_profile_via_default_branch_is_not_reonboarded(): void
    {
        $branch = Branch::factory()->create();

        TelegramProfile::query()->create([
            'telegram_user_id' => 113,
            'telegram_chat_id' => 224,
            'contact_consent' => false,
            'default_branch_id' => $branch->id,
        ]);

        app(MessageHandler::class)->handle($this->message(113, 224, '/start'));

        $state = app(StateManager::class)->getUserState(113);
        $this->assertNull($state);
    }

    public function test_sharing_contact_sets_consent_and_phone_and_advances_state(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 114,
            'telegram_chat_id' => 225,
            'contact_consent' => false,
        ]);

        app(StateManager::class)->setUserState(114, 'onboarding_awaiting_contact');

        $message = $this->message(114, 225);
        $message['contact'] = ['phone_number' => '+380991234567'];

        app(MessageHandler::class)->handle($message);

        $this->assertDatabaseHas('telegram_profiles', [
            'telegram_user_id' => 114,
            'contact_consent' => true,
            'phone' => '+380991234567',
        ]);

        $state = app(StateManager::class)->getUserState(114);
        $this->assertSame('onboarding_awaiting_workplace_choice', $state['state'] ?? null);
    }

    public function test_skipping_contact_advances_state_without_setting_consent(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 115,
            'telegram_chat_id' => 226,
            'contact_consent' => false,
        ]);

        app(StateManager::class)->setUserState(115, 'onboarding_awaiting_contact');

        app(MessageHandler::class)->handle($this->message(115, 226, '⏭️ Пропустити'));

        $state = app(StateManager::class)->getUserState(115);
        $this->assertSame('onboarding_awaiting_workplace_choice', $state['state'] ?? null);

        $this->assertDatabaseHas('telegram_profiles', [
            'telegram_user_id' => 115,
            'contact_consent' => false,
        ]);
    }

    public function test_declining_workplace_setup_returns_to_main_menu_and_clears_state(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 116,
            'telegram_chat_id' => 227,
        ]);

        app(StateManager::class)->setUserState(116, 'onboarding_awaiting_workplace_choice');

        $callbackQuery = [
            'id' => 'cb1',
            'from' => ['id' => 116, 'username' => 'olena'],
            'message' => ['chat' => ['id' => 227], 'message_id' => 55],
            'data' => 'onboarding_skip_workplace',
        ];

        app(CallbackHandler::class)->handle($callbackQuery);

        $state = app(StateManager::class)->getUserState(116);
        $this->assertNull($state);
    }

    public function test_completing_workplace_setup_sets_default_branch_and_room(): void
    {
        $branch = Branch::factory()->create();

        TelegramProfile::query()->create([
            'telegram_user_id' => 117,
            'telegram_chat_id' => 228,
        ]);

        $callbackQuery = [
            'id' => 'cb2',
            'from' => ['id' => 117, 'username' => 'olena'],
            'message' => ['chat' => ['id' => 228], 'message_id' => 56],
            'data' => "branch_select:{$branch->id}",
        ];

        app(StateManager::class)->setUserState(117, 'onboarding_awaiting_branch');
        app(CallbackHandler::class)->handle($callbackQuery);

        $state = app(StateManager::class)->getUserState(117);
        $this->assertSame('onboarding_awaiting_room', $state['state'] ?? null);

        app(MessageHandler::class)->handle($this->message(117, 228, '204'));

        $this->assertDatabaseHas('telegram_profiles', [
            'telegram_user_id' => 117,
            'default_branch_id' => $branch->id,
            'default_room_number' => '204',
            'is_profile_complete' => true,
        ]);

        $this->assertNull(app(StateManager::class)->getUserState(117));
    }

    public function test_main_menu_button_works_while_onboarding_state_is_active(): void
    {
        TelegramProfile::query()->create([
            'telegram_user_id' => 118,
            'telegram_chat_id' => 229,
        ]);

        Branch::factory()->create();

        app(StateManager::class)->setUserState(118, 'onboarding_awaiting_contact');

        app(MessageHandler::class)->handle($this->message(118, 229, '🔧 Виклик IT майстра'));

        $state = app(StateManager::class)->getUserState(118);
        $this->assertSame('repair_awaiting_branch', $state['state'] ?? null);
    }
}
