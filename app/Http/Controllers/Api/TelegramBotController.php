<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramService;
use App\Services\Telegram\CallbackHandler;
use App\Services\Telegram\MessageHandler;
use App\Services\Telegram\StateManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    private TelegramService $telegramService;
    private CallbackHandler $callbackHandler;
    private MessageHandler $messageHandler;
    private StateManager $stateManager;

    public function __construct(
        TelegramService $telegramService,
        CallbackHandler $callbackHandler, 
        MessageHandler $messageHandler,
        StateManager $stateManager
    ) {
        $this->telegramService = $telegramService;
        $this->callbackHandler = $callbackHandler;
        $this->messageHandler = $messageHandler;
        $this->stateManager = $stateManager;
    }

    /**
     * Webhook для обработки сообщений от Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $update = $request->all();
            Log::info('Telegram webhook received', $update);

            if (empty($update)) {
                Log::warning('Empty webhook update received');
                return response()->json(['status' => 'ok']);
            }

            if (isset($update['message'])) {
                $this->messageHandler->handle($update['message']);
            } elseif (isset($update['callback_query'])) {
                $this->callbackHandler->handle($update['callback_query']);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * API методы для внешнего использования
     */
    public function getUserInfo(Request $request)
    {
        $request->validate([
            'telegram_id' => 'required|numeric'
        ]);

        return $this->telegramService->getUserInfo($request->telegram_id);
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'telegram_id' => 'required|numeric',
            'message' => 'required|string|max:4096'
        ]);

        $result = $this->telegramService->sendMessage(
            $request->telegram_id, 
            $request->message
        );

        return response()->json([
            'success' => (bool) $result,
            'data' => $result
        ]);
    }
}