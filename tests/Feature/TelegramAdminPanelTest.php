<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CartridgeReplacement;
use App\Models\RepairRequest;
use App\Models\User;
use App\Services\Telegram\CallbackHandler;
use App\Services\Telegram\Handlers\AdminPanelHandler;
use App\Services\Telegram\KeyboardService;
use App\Services\Telegram\MessageHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramAdminPanelTest extends TestCase
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

    private function makeRepair(string $status, int $branchId): RepairRequest
    {
        return RepairRequest::create([
            'user_telegram_id' => 12345,
            'branch_id' => $branchId,
            'room_number' => '101',
            'description' => 'Тестова заявка '.$status,
            'status' => $status,
        ]);
    }

    // === Main menu button visibility ===

    public function test_main_menu_shows_admin_panel_button_for_recognized_admin(): void
    {
        User::factory()->create(['role' => 'admin', 'telegram_id' => 501]);

        $keyboard = app(KeyboardService::class)->getMainMenuKeyboard(501);

        $buttons = collect($keyboard['inline_keyboard'])->flatten(1);
        $this->assertTrue($buttons->contains(fn ($button) => $button['callback_data'] === 'adminpanel_menu'));
    }

    public function test_main_menu_hides_admin_panel_button_for_regular_user(): void
    {
        $keyboard = app(KeyboardService::class)->getMainMenuKeyboard(502);

        $buttons = collect($keyboard['inline_keyboard'])->flatten(1);
        $this->assertFalse($buttons->contains(fn ($button) => $button['callback_data'] === 'adminpanel_menu'));
    }

    public function test_main_menu_hides_admin_panel_button_for_non_admin_role_user(): void
    {
        User::factory()->create(['role' => 'warehouse_keeper', 'telegram_id' => 503]);

        $keyboard = app(KeyboardService::class)->getMainMenuKeyboard(503);

        $buttons = collect($keyboard['inline_keyboard'])->flatten(1);
        $this->assertFalse($buttons->contains(fn ($button) => $button['callback_data'] === 'adminpanel_menu'));
    }

    // === Gate re-check inside the handler ===

    public function test_unlinked_telegram_user_gets_friendly_message_not_exception(): void
    {
        // No matching User row for this telegram id at all.
        app(CallbackHandler::class)->handle($this->callbackQuery(504, 604, 1, 'adminpanel_menu'));

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], "не пов'язаний");
        });
    }

    public function test_admin_user_whose_telegram_id_does_not_match_gets_friendly_message(): void
    {
        // Admin exists in DB but under a different telegram_id than the caller.
        User::factory()->create(['role' => 'admin', 'telegram_id' => 999999]);

        app(CallbackHandler::class)->handle($this->callbackQuery(505, 605, 1, 'adminpanel_menu'));

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], "не пов'язаний");
        });
    }

    public function test_recognized_admin_can_open_panel_menu(): void
    {
        User::factory()->create(['role' => 'director', 'telegram_id' => 506]);

        app(CallbackHandler::class)->handle($this->callbackQuery(506, 606, 1, 'adminpanel_menu'));

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], 'Адмін-панель');
        });
    }

    // === Status filter ===

    public function test_status_filter_shows_only_matching_repairs(): void
    {
        User::factory()->create(['role' => 'admin', 'telegram_id' => 507]);
        $branch = Branch::factory()->create(['name' => 'Філія Тест']);

        $new = $this->makeRepair('нова', $branch->id);
        $this->makeRepair('в_роботі', $branch->id);
        $this->makeRepair('виконана', $branch->id);

        app(CallbackHandler::class)->handle($this->callbackQuery(507, 607, 1, 'adminpanel_repairs_filter:нова'));

        Http::assertSent(function ($request) use ($new) {
            $body = $request->data();

            return isset($body['text'])
                && str_contains($body['text'], "#{$new->id}")
                && ! str_contains($body['text'], 'Тестова заявка в_роботі')
                && ! str_contains($body['text'], 'Тестова заявка виконана');
        });
    }

    public function test_admin_command_opens_new_admin_panel_for_recognized_admin(): void
    {
        User::factory()->create(['role' => 'admin', 'telegram_id' => 512]);

        app(MessageHandler::class)->handle([
            'chat' => ['id' => 612],
            'from' => ['id' => 512, 'username' => 'olena'],
            'text' => '/admin',
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], 'Адмін-панель');
        });
    }

    public function test_status_command_shows_new_panel_stats_for_recognized_admin(): void
    {
        User::factory()->create(['role' => 'admin', 'telegram_id' => 516]);
        $branch = Branch::factory()->create();
        $this->makeRepair('нова', $branch->id);

        app(MessageHandler::class)->handle([
            'chat' => ['id' => 616],
            'from' => ['id' => 516, 'username' => 'olena'],
            'text' => '/status',
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], 'Статистика Telegram-заявок');
        });
    }

    public function test_admin_panel_stats_callback_shows_stats(): void
    {
        User::factory()->create(['role' => 'admin', 'telegram_id' => 517]);

        app(CallbackHandler::class)->handle($this->callbackQuery(517, 617, 1, 'adminpanel_stats'));

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], 'Статистика Telegram-заявок');
        });
    }

    public function test_status_command_is_rejected_for_regular_user(): void
    {
        app(MessageHandler::class)->handle([
            'chat' => ['id' => 613],
            'from' => ['id' => 513, 'username' => 'petro'],
            'text' => '/status',
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], 'Недостатньо прав');
        });
    }
    // === Status update ===

    public function test_status_update_changes_repair_status_in_database(): void
    {
        User::factory()->create(['role' => 'admin', 'telegram_id' => 508]);
        $branch = Branch::factory()->create();
        $repair = $this->makeRepair('нова', $branch->id);

        app(CallbackHandler::class)->handle($this->callbackQuery(508, 608, 1, "adminpanel_status_update:{$repair->id}:в_роботі"));

        $this->assertSame('в_роботі', $repair->fresh()->status);

        $expectedCallbackId = "cb-adminpanel_status_update:{$repair->id}:в_роботі";

        Http::assertSent(function ($request) use ($expectedCallbackId) {
            $body = $request->data();

            return ($body['callback_query_id'] ?? null) === $expectedCallbackId
                && isset($body['text'])
                && str_contains($body['text'], 'Статус змінено');
        });
    }

    public function test_admin_cannot_skip_repair_status_sequence(): void
    {
        User::factory()->create(['role' => 'admin', 'telegram_id' => 514]);
        $branch = Branch::factory()->create();
        $repair = $this->makeRepair('нова', $branch->id);

        app(CallbackHandler::class)->handle($this->callbackQuery(514, 614, 1, "adminpanel_status_update:{$repair->id}:виконана"));

        $this->assertSame('нова', $repair->fresh()->status);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], 'перехід статусу недоступний');
        });
    }

    // === Cartridges: view-only, no status UI ===

    public function test_cartridge_panel_lists_items_without_status_update_buttons(): void
    {
        User::factory()->create(['role' => 'admin', 'telegram_id' => 509]);
        $branch = Branch::factory()->create(['name' => 'Філія Картридж']);

        CartridgeReplacement::create([
            'user_telegram_id' => 12345,
            'branch_id' => $branch->id,
            'room_number' => '202',
            'printer_info' => 'HP LaserJet',
            'cartridge_type' => 'HP CF230A',
            'replacement_date' => now(),
        ]);

        app(CallbackHandler::class)->handle($this->callbackQuery(509, 609, 1, 'adminpanel_cartridges'));

        Http::assertSent(function ($request) {
            $body = $request->data();
            $keyboard = collect($body['reply_markup']['inline_keyboard'] ?? [])->flatten(1);

            $hasText = isset($body['text']) && str_contains($body['text'], 'HP CF230A');
            $noStatusButton = ! $keyboard->contains(fn ($button) => str_starts_with($button['callback_data'] ?? '', 'adminpanel_status_update'));

            return $hasText && $noStatusButton;
        });
    }

    public function test_cartridge_panel_keyboard_never_contains_status_update_callback(): void
    {
        $branch = Branch::factory()->create();
        CartridgeReplacement::create([
            'user_telegram_id' => 12345,
            'branch_id' => $branch->id,
            'room_number' => '202',
            'printer_info' => 'HP LaserJet',
            'cartridge_type' => 'HP CF230A',
            'replacement_date' => now(),
        ]);

        $keyboard = app(KeyboardService::class)->getBackKeyboard('adminpanel_menu');

        $buttons = collect($keyboard['inline_keyboard'])->flatten(1);
        $this->assertFalse($buttons->contains(fn ($button) => str_starts_with($button['callback_data'], 'adminpanel_status_update')));
    }

    public function test_legacy_admin_callbacks_are_no_longer_routed(): void
    {
        User::factory()->create(['role' => 'admin', 'telegram_id' => 515]);

        app(CallbackHandler::class)->handle($this->callbackQuery(515, 615, 1, 'admin_inventory'));

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], 'Невідома дія');
        });
    }

    public function test_direct_handler_call_also_enforces_gate(): void
    {
        $handler = app(AdminPanelHandler::class);

        $handler->handleCallback($this->callbackQuery(510, 610, 1, 'adminpanel_repairs'));

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], "не пов'язаний");
        });
    }

    public function test_non_admin_cannot_force_a_status_update_via_forged_callback_data(): void
    {
        $branch = Branch::factory()->create();
        $repair = $this->makeRepair('нова', $branch->id);

        // No matching admin/director User row for this telegram id — a non-admin
        // guessing the adminpanel_status_update callback_data format directly.
        app(CallbackHandler::class)->handle(
            $this->callbackQuery(511, 611, 1, "adminpanel_status_update:{$repair->id}:виконана")
        );

        $this->assertSame('нова', $repair->fresh()->status);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return isset($body['text']) && str_contains($body['text'], "не пов'язаний");
        });
    }
}
