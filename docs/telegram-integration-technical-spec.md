# Telegram Integration Technical Specification

Date: 2026-08-26
Project: Internal IT asset and operations management system
Framework: Laravel 12 using the Laravel 10-style application structure

## Investigated Runtime Architecture

The live Telegram webhook path is:

1. `routes/api.php`
2. `App\Http\Controllers\Api\TelegramBotController`
3. `App\Services\Telegram\MessageHandler`
4. `App\Services\Telegram\CallbackHandler`
5. Concrete flow handlers under `App\Services\Telegram\Handlers`

The service wiring is defined in `App\Providers\TelegramServiceProvider`.

The codebase also contains older Telegram-related services and API controllers. During the audit, only the classes directly wired through `TelegramServiceProvider` and `TelegramBotController` were treated as live bot code.

## Key Findings From The Audit

Before the changes, Telegram identity and authorization were fragmented across multiple sources:

- `users.telegram_id`
- `admins.telegram_id`
- request-level `user_telegram_id` fields

The visible Telegram menu also exposed too many actions for current operational needs. Inventory and legacy admin workflows created avoidable risk because they were incomplete and not clearly part of the validated Telegram use case.

The callback routing also had legacy gaps:

- Old `admin_*` callbacks routed to a legacy admin handler.
- Some status update callbacks could fail because `answerCallbackQuery` was called with a message ID instead of the Telegram callback query ID.
- Status transitions were not strictly constrained to the intended sequence.

## Implemented Identity And Profile Accounting

A persistent Telegram profile model was introduced to track bot users independently of repair and cartridge records.

Main files:

- `app/Models/TelegramProfile.php`
- `database/migrations/2026_08_26_105354_create_telegram_profiles_table.php`
- `app/Services/Telegram/TelegramProfileService.php`

The profile service syncs Telegram metadata from incoming messages and callbacks. It also remembers a user's default workplace after successful repair or cartridge flows.

Blocked profiles remain blocked after later syncs.

## Webhook Security

The Telegram webhook now requires a configured secret token.

Main behavior:

- Incoming webhook requests must include `X-Telegram-Bot-Api-Secret-Token` matching `services.telegram.webhook_secret`.
- Production fails closed when the secret is missing.
- Webhook registration sends the configured `secret_token` to Telegram.
- Public debug and management Telegram routes were removed from the public route list.

Configuration source:

- `config/services.php`
- `TELEGRAM_WEBHOOK_SECRET`

## Current Live User Menu

The reply keyboard is intentionally limited to:

- IT repair request.
- Cartridge replacement.

The inline main menu is also focused on those two actions for ordinary users.

Recognized admins additionally receive:

- Admin panel.

## Current Admin Authorization Rule

A Telegram caller is recognized as an admin if a web `User` exists with:

- Matching `telegram_id`.
- Role in `admin` or `director`.

This is implemented in `TelegramProfileService::isRecognizedAdmin()` and is reused by the menu and admin panel handler.

## Admin Panel Implementation

The current admin panel is implemented in:

- `app/Services/Telegram/Handlers/AdminPanelHandler.php`
- `app/Services/Telegram/KeyboardService.php`
- `app/Services/Telegram/MessageHandler.php`
- `app/Services/Telegram/CallbackHandler.php`
- `app/Providers/TelegramServiceProvider.php`

Callback namespace:

- `adminpanel_menu`
- `adminpanel_repairs`
- `adminpanel_repairs_filter:{status}`
- `adminpanel_repair_details:{repairId}`
- `adminpanel_status_update:{repairId}:{status}`
- `adminpanel_cartridges`
- `adminpanel_stats`

The admin panel handler authorizes every callback before processing it.

## Repair Status Transition Rules

Repair status changes from Telegram are limited to this sequence:

- `нова` to `в_роботі`
- `в_роботі` to `виконана`

Any other transition is rejected and the request detail view is re-rendered.

The status update callback now sends Telegram feedback using the actual callback query ID.

## Legacy Admin Removal

The legacy Telegram admin handler was removed from the live service graph and then deleted:

- `app/Services/Telegram/Handlers/AdminHandler.php`

The old `admin_*` callback route was removed from `CallbackHandler`.

The old admin keyboard methods were removed from `KeyboardService`:

- `getAdminMenuKeyboard()`
- `getRepairsListKeyboard()`
- `getRepairDetailsKeyboard()`

The `/admin` command now opens `AdminPanelHandler`.

The `/status` command now uses `AdminPanelHandler::sendStats()` and requires the same recognized admin rule.

## Tests Added Or Updated

Main test files:

- `tests/Feature/TelegramWebhookTest.php`
- `tests/Feature/TelegramAdminPanelTest.php`

Covered behavior includes:

- Webhook rejects requests without the configured secret.
- Webhook accepts the configured secret.
- Production fails closed when the webhook secret is missing.
- Webhook registration sends the configured secret token.
- Public Telegram debug and management routes are not registered.
- Telegram profile sync upserts identity and workplace fields.
- Profile sync does not unblock explicitly blocked profiles.
- Main menu callback data remains valid.
- Admin panel button is visible only for recognized admin users.
- Non-admin and mismatched Telegram users cannot open the admin panel.
- `/admin` opens the new admin panel for recognized admins.
- `/status` shows new panel statistics only for recognized admins.
- Admin panel statistics callback works.
- Repair filters show only matching statuses.
- Repair status update persists to the database.
- Repair status updates use the correct callback query ID.
- Admins cannot skip the repair status sequence.
- Cartridge history is read-only and does not expose repair status callbacks.
- Legacy `admin_inventory` callbacks are no longer routed.
- Forged admin panel status callbacks are rejected for non-admin users.

## Verification Commands

The following checks were run successfully after the latest changes:

```bash
php artisan test tests\\Feature\\TelegramAdminPanelTest.php
php artisan test tests\\Feature\\TelegramWebhookTest.php
vendor\\bin\\pint --dirty
git diff --check
```

Latest observed targeted results:

- `TelegramAdminPanelTest`: 18 tests passed, 21 assertions.
- `TelegramWebhookTest`: 8 tests passed, 16 assertions.

## Operational Notes

Before using the webhook in production, make sure `TELEGRAM_WEBHOOK_SECRET` is set and register the webhook with the project command:

```bash
php artisan telegram:set-webhook
```

Admins must have their Telegram ID stored on their web `users` record. The old `admins` table is not the source of truth for the current Telegram admin panel.

## Recommended Next Implementation Steps

1. Surface the saved workplace suggestion at the beginning of repair and cartridge flows.
2. Build web-admin controls for Telegram broadcasts using `telegram_profiles` as the recipient source.
3. Build a Telegram profile admin screen to inspect linked users, profile completeness, defaults, and block status.
4. Decide whether Telegram admins should be able to add comments, assign requests, or only change status.
5. Consider archiving or migrating the old `admins` table if it is no longer used outside legacy APIs.