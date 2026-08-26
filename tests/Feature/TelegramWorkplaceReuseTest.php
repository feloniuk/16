<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CartridgeReplacement;
use App\Models\RepairRequest;
use App\Models\TelegramProfile;
use App\Services\Telegram\CallbackHandler;
use App\Services\Telegram\MessageHandler;
use App\Services\Telegram\StateManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramWorkplaceReuseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);
    }

    private function callbackQuery(int $userId, int $chatId, int $messageId, string $data): array
    {
        return [
            'id' => 'cb-'.$data,
            'from' => ['id' => $userId, 'username' => 'olena'],
            'message' => ['chat' => ['id' => $chatId], 'message_id' => $messageId],
            'data' => $data,
        ];
    }

    private function message(int $userId, int $chatId, string $text = ''): array
    {
        return [
            'chat' => ['id' => $chatId],
            'from' => ['id' => $userId, 'username' => 'olena'],
            'text' => $text,
        ];
    }

    // === Repair side ===

    public function test_repair_request_with_complete_profile_shows_workplace_confirmation(): void
    {
        $branch = Branch::factory()->create(['name' => 'Філія Центр', 'is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 301,
            'telegram_chat_id' => 401,
            'default_branch_id' => $branch->id,
            'default_room_number' => '204',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(301, 401, 1, 'repair_request'));

        $state = app(StateManager::class)->getUserState(301);
        $this->assertSame('repair_awaiting_workplace_confirmation', $state['state'] ?? null);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text'])
                && str_contains($body['text'], 'Філія Центр')
                && str_contains($body['text'], '204');
        });
    }

    public function test_repair_workplace_use_transitions_directly_to_description_state(): void
    {
        $branch = Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 302,
            'telegram_chat_id' => 402,
            'default_branch_id' => $branch->id,
            'default_room_number' => '105',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(302, 402, 1, 'repair_request'));
        app(CallbackHandler::class)->handle($this->callbackQuery(302, 402, 1, 'repair_workplace_use'));

        $state = app(StateManager::class)->getUserState(302);
        $this->assertSame('repair_awaiting_description', $state['state'] ?? null);
        $this->assertSame($branch->id, $state['temp_data']['branch_id'] ?? null);
        $this->assertSame($branch->name, $state['temp_data']['branch_name'] ?? null);
        $this->assertSame('105', $state['temp_data']['room_number'] ?? null);
    }

    public function test_repair_workplace_change_goes_through_full_branch_picker(): void
    {
        $branch = Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 303,
            'telegram_chat_id' => 403,
            'default_branch_id' => $branch->id,
            'default_room_number' => '105',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(303, 403, 1, 'repair_request'));
        app(CallbackHandler::class)->handle($this->callbackQuery(303, 403, 1, 'repair_workplace_change'));

        $state = app(StateManager::class)->getUserState(303);
        $this->assertSame('repair_awaiting_branch', $state['state'] ?? null);
    }

    public function test_repair_request_falls_back_to_picker_when_saved_branch_is_inactive(): void
    {
        $branch = Branch::factory()->create(['is_active' => false]);
        Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 304,
            'telegram_chat_id' => 404,
            'default_branch_id' => $branch->id,
            'default_room_number' => '105',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(304, 404, 1, 'repair_request'));

        $state = app(StateManager::class)->getUserState(304);
        $this->assertSame('repair_awaiting_branch', $state['state'] ?? null);
        $this->assertNotSame('repair_awaiting_workplace_confirmation', $state['state'] ?? null);
    }

    public function test_repair_request_falls_back_to_picker_when_saved_branch_is_deleted(): void
    {
        Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 305,
            'telegram_chat_id' => 405,
            'default_branch_id' => 999999,
            'default_room_number' => '105',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(305, 405, 1, 'repair_request'));

        $state = app(StateManager::class)->getUserState(305);
        $this->assertSame('repair_awaiting_branch', $state['state'] ?? null);
    }

    public function test_repair_request_with_incomplete_profile_goes_straight_to_picker(): void
    {
        Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 306,
            'telegram_chat_id' => 406,
            'default_branch_id' => null,
            'default_room_number' => null,
            'is_profile_complete' => false,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(306, 406, 1, 'repair_request'));

        $state = app(StateManager::class)->getUserState(306);
        $this->assertSame('repair_awaiting_branch', $state['state'] ?? null);
    }

    public function test_repair_request_with_no_profile_at_all_goes_straight_to_picker(): void
    {
        Branch::factory()->create(['is_active' => true]);

        app(CallbackHandler::class)->handle($this->callbackQuery(307, 407, 1, 'repair_request'));

        $state = app(StateManager::class)->getUserState(307);
        $this->assertSame('repair_awaiting_branch', $state['state'] ?? null);
    }

    // === Cartridge side ===

    public function test_cartridge_request_with_complete_profile_shows_workplace_confirmation(): void
    {
        $branch = Branch::factory()->create(['name' => 'Філія Північ', 'is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 311,
            'telegram_chat_id' => 411,
            'default_branch_id' => $branch->id,
            'default_room_number' => '312',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(311, 411, 1, 'cartridge_request'));

        $state = app(StateManager::class)->getUserState(311);
        $this->assertSame('cartridge_awaiting_workplace_confirmation', $state['state'] ?? null);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text'])
                && str_contains($body['text'], 'Філія Північ')
                && str_contains($body['text'], '312');
        });
    }

    public function test_cartridge_workplace_use_transitions_directly_to_printer_state(): void
    {
        $branch = Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 312,
            'telegram_chat_id' => 412,
            'default_branch_id' => $branch->id,
            'default_room_number' => '77',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(312, 412, 1, 'cartridge_request'));
        app(CallbackHandler::class)->handle($this->callbackQuery(312, 412, 1, 'cartridge_workplace_use'));

        $state = app(StateManager::class)->getUserState(312);
        $this->assertSame('cartridge_awaiting_printer', $state['state'] ?? null);
        $this->assertSame($branch->id, $state['temp_data']['branch_id'] ?? null);
        $this->assertSame($branch->name, $state['temp_data']['branch_name'] ?? null);
        $this->assertSame('77', $state['temp_data']['room_number'] ?? null);
    }

    public function test_cartridge_workplace_change_goes_through_full_branch_picker(): void
    {
        $branch = Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 313,
            'telegram_chat_id' => 413,
            'default_branch_id' => $branch->id,
            'default_room_number' => '77',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(313, 413, 1, 'cartridge_request'));
        app(CallbackHandler::class)->handle($this->callbackQuery(313, 413, 1, 'cartridge_workplace_change'));

        $state = app(StateManager::class)->getUserState(313);
        $this->assertSame('cartridge_awaiting_branch', $state['state'] ?? null);
    }

    // === Reply-keyboard entry point parity ===

    public function test_reply_keyboard_repair_button_shows_workplace_confirmation_for_returning_user(): void
    {
        $branch = Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 321,
            'telegram_chat_id' => 421,
            'default_branch_id' => $branch->id,
            'default_room_number' => '9',
            'is_profile_complete' => true,
        ]);

        app(MessageHandler::class)->handle($this->message(321, 421, '🔧 Виклик IT майстра'));

        $state = app(StateManager::class)->getUserState(321);
        $this->assertSame('repair_awaiting_workplace_confirmation', $state['state'] ?? null);
    }

    public function test_reply_keyboard_cartridge_button_shows_workplace_confirmation_for_returning_user(): void
    {
        $branch = Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 322,
            'telegram_chat_id' => 422,
            'default_branch_id' => $branch->id,
            'default_room_number' => '9',
            'is_profile_complete' => true,
        ]);

        app(MessageHandler::class)->handle($this->message(322, 422, '🖨️ Заміна картриджа'));

        $state = app(StateManager::class)->getUserState(322);
        $this->assertSame('cartridge_awaiting_workplace_confirmation', $state['state'] ?? null);
    }

    // === Full completion via saved workplace ===

    public function test_repair_flow_completes_and_creates_request_via_saved_workplace(): void
    {
        $branch = Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 331,
            'telegram_chat_id' => 431,
            'default_branch_id' => $branch->id,
            'default_room_number' => '55',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(331, 431, 1, 'repair_request'));
        app(CallbackHandler::class)->handle($this->callbackQuery(331, 431, 1, 'repair_workplace_use'));
        app(MessageHandler::class)->handle($this->message(331, 431, 'Не працює монітор, чорний екран.'));
        app(MessageHandler::class)->handle($this->message(331, 431, '+380501112233'));

        $this->assertDatabaseHas(RepairRequest::class, [
            'user_telegram_id' => 331,
            'branch_id' => $branch->id,
            'room_number' => '55',
            'phone' => '+380501112233',
        ]);

        $state = app(StateManager::class)->getUserState(331);
        $this->assertNull($state);
    }

    public function test_cartridge_flow_completes_and_creates_request_via_saved_workplace(): void
    {
        $branch = Branch::factory()->create(['is_active' => true]);

        TelegramProfile::query()->create([
            'telegram_user_id' => 332,
            'telegram_chat_id' => 432,
            'default_branch_id' => $branch->id,
            'default_room_number' => '66',
            'is_profile_complete' => true,
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(332, 432, 1, 'cartridge_request'));
        app(CallbackHandler::class)->handle($this->callbackQuery(332, 432, 1, 'cartridge_workplace_use'));
        app(MessageHandler::class)->handle($this->message(332, 432, 'HP LaserJet M404'));
        app(MessageHandler::class)->handle($this->message(332, 432, 'HP CF230A'));

        $this->assertDatabaseHas(CartridgeReplacement::class, [
            'user_telegram_id' => 332,
            'branch_id' => $branch->id,
            'room_number' => '66',
            'printer_info' => 'HP LaserJet M404',
            'cartridge_type' => 'HP CF230A',
        ]);

        $state = app(StateManager::class)->getUserState(332);
        $this->assertNull($state);
    }
}
