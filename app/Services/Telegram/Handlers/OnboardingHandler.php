<?php

namespace App\Services\Telegram\Handlers;

use App\Models\Branch;
use App\Models\TelegramProfile;
use App\Services\Telegram\KeyboardService;
use App\Services\Telegram\ReplyKeyboardService;
use App\Services\Telegram\StateManager;
use App\Services\Telegram\TelegramProfileService;
use App\Services\Telegram\TelegramService;

class OnboardingHandler
{
    public function __construct(
        private TelegramService $telegram,
        private StateManager $stateManager,
        private KeyboardService $keyboard,
        private ReplyKeyboardService $replyKeyboard,
        private TelegramProfileService $profileService
    ) {}

    public function maybeStartOnboarding(int $chatId, int $userId): void
    {
        $profile = TelegramProfile::where('telegram_user_id', $userId)->first();

        if (! $profile) {
            return;
        }

        if ($profile->contact_consent === false && $profile->default_branch_id === null) {
            $this->stateManager->setUserState($userId, 'onboarding_awaiting_contact');

            $this->telegram->sendMessage(
                $chatId,
                "👋 <b>Кілька кроків для зручності</b>\n\nПоділіться контактом, щоб ми могли зв'язатися з вами щодо заявок. Це необов'язково.",
                $this->replyKeyboard->getContactKeyboard()
            );
        }
    }

    public function handleContactShared(array $message): void
    {
        $chatId = $message['chat']['id'];
        $userId = $message['from']['id'];
        $phone = $message['contact']['phone_number'] ?? null;

        if (! $phone) {
            return;
        }

        TelegramProfile::where('telegram_user_id', $userId)->update([
            'phone' => $phone,
            'contact_consent' => true,
        ]);

        $this->stateManager->setUserState($userId, 'onboarding_awaiting_workplace_choice');

        $this->telegram->sendMessage(
            $chatId,
            '✅ Дякуємо! Контакт збережено.',
            $this->replyKeyboard->getMainMenuKeyboard()
        );

        $this->telegram->sendMessage(
            $chatId,
            "🏢 <b>Налаштувати робоче місце?</b>\n\nОберіть філіал і кабінет за замовчуванням, щоб не вводити їх щоразу.",
            $this->keyboard->getWorkplaceChoiceKeyboard()
        );
    }

    public function handleSkipContact(int $chatId, int $userId): void
    {
        $this->stateManager->setUserState($userId, 'onboarding_awaiting_workplace_choice');

        $this->telegram->sendMessage(
            $chatId,
            '👌 Гаразд.',
            $this->replyKeyboard->getMainMenuKeyboard()
        );

        $this->telegram->sendMessage(
            $chatId,
            "🏢 <b>Налаштувати робоче місце?</b>\n\nОберіть філіал і кабінет за замовчуванням, щоб не вводити їх щоразу.",
            $this->keyboard->getWorkplaceChoiceKeyboard()
        );
    }

    public function handleCallback(array $callbackQuery): void
    {
        $data = $callbackQuery['data'] ?? '';

        if ($data === 'onboarding_setup_workplace') {
            $this->handleWorkplaceChoiceCallback($callbackQuery, true);
        } elseif ($data === 'onboarding_skip_workplace') {
            $this->handleWorkplaceChoiceCallback($callbackQuery, false);
        }
    }

    public function handleWorkplaceChoiceCallback(array $callbackQuery, bool $setupNow): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $messageId = $callbackQuery['message']['message_id'];

        if (! $setupNow) {
            $this->stateManager->clearUserState($userId);

            $this->telegram->editMessage(
                $chatId,
                $messageId,
                '👌 Гаразд, налаштуєте пізніше.'
            );

            $this->telegram->sendMessage(
                $chatId,
                'Оберіть дію з головного меню:',
                $this->replyKeyboard->getMainMenuKeyboard()
            );

            return;
        }

        $branches = Branch::where('is_active', true)->get();

        if ($branches->isEmpty()) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Філіали недоступні. Зв\'яжіться з адміністратором.');
            $this->stateManager->clearUserState($userId);

            return;
        }

        $this->stateManager->setUserState($userId, 'onboarding_awaiting_branch');

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            '🏢 <b>Оберіть філіал</b>',
            $this->keyboard->getBranchesKeyboard($branches, 'onboarding')
        );
    }

    public function handleBranchSelection(array $callbackQuery, int $branchId): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $userId = $callbackQuery['from']['id'];
        $messageId = $callbackQuery['message']['message_id'];

        $branch = Branch::find($branchId);
        if (! $branch) {
            $this->telegram->editMessage($chatId, $messageId, '❌ Філіал не знайдено.');

            return;
        }

        $this->stateManager->setUserState($userId, 'onboarding_awaiting_room', [
            'branch_id' => $branchId,
            'branch_name' => $branch->name,
        ]);

        $this->telegram->editMessage(
            $chatId,
            $messageId,
            '🚪 <b>Номер кабінету?</b>',
            $this->keyboard->getCancelKeyboard()
        );
    }

    public function handleRoomInput(int $chatId, int $userId, string $room): void
    {
        $userState = $this->stateManager->getUserState($userId);
        $tempData = $userState['temp_data'] ?? [];

        if (empty(trim($room)) || strlen($room) > 50) {
            $this->telegram->sendMessage($chatId, '❌ Номер кабінету не довший за 50 символів. Спробуйте ще раз:');

            return;
        }

        if (! isset($tempData['branch_id'])) {
            $this->telegram->sendMessage($chatId, '❌ Дані не збереглися. Спробуйте ще раз:', $this->keyboard->getMainMenuKeyboard($userId));
            $this->stateManager->clearUserState($userId);

            return;
        }

        $this->profileService->rememberWorkplace($userId, $tempData['branch_id'], trim($room));
        $this->stateManager->clearUserState($userId);

        $this->telegram->sendMessage(
            $chatId,
            "✅ <b>Робоче місце збережено</b>\n\n🏢 Філіал: {$tempData['branch_name']}\n🚪 Кабінет: ".trim($room),
            $this->keyboard->getMainMenuKeyboard($userId)
        );
    }
}
