<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TelegramIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TelegramIntegrationController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramIntegrationService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Получить информацию о пользователе по Telegram ID
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'telegram_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $userInfo = $this->telegramService->getUserInfo($request->telegram_id);

        return response()->json([
            'success' => true,
            'data' => $userInfo
        ]);
    }

    /**
     * Создать заявку на ремонт
     */
    public function createRepair(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_telegram_id' => 'required|integer',
            'username' => 'nullable|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'room_number' => 'required|string|max:50',
            'description' => 'required|string|max:1000',
            'phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $repair = $this->telegramService->createRepairRequest($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Repair request created successfully',
                'data' => [
                    'id' => $repair->id,
                    'status' => $repair->status,
                    'created_at' => $repair->created_at
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating repair request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Создать запись о замене картриджа
     */
    public function createCartridge(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_telegram_id' => 'required|integer',
            'username' => 'nullable|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'room_number' => 'required|string|max:50',
            'printer_info' => 'required|string|max:500',
            'cartridge_type' => 'required|string|max:255',
            'replacement_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $cartridge = $this->telegramService->createCartridgeReplacement($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Cartridge replacement created successfully',
                'data' => [
                    'id' => $cartridge->id,
                    'replacement_date' => $cartridge->replacement_date,
                    'created_at' => $cartridge->created_at
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating cartridge replacement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить статистику
     */
    public function getStats(): JsonResponse
    {
        $stats = $this->telegramService->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
            'generated_at' => now()
        ]);
    }

    /**
     * Получить филиалы
     */
    public function getBranches(): JsonResponse
    {
        $branches = $this->telegramService->getActiveBranches();

        return response()->json([
            'success' => true,
            'data' => $branches
        ]);
    }

    /**
     * Получить последние заявки
     */
    public function getRecentRepairs(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $branchId = $request->get('branch_id');
        $userId = $request->get('user_id');

        $repairs = $this->telegramService->getRecentRepairs($limit, $branchId, $userId);

        return response()->json([
            'success' => true,
            'data' => $repairs
        ]);
    }

    /**
     * Обновить статус заявки
     */
    public function updateRepairStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'repair_id' => 'required|integer|exists:repair_requests,id',
            'status' => 'required|in:нова,в_роботі,виконана',
            'admin_telegram_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $success = $this->telegramService->updateRepairStatus(
            $request->repair_id,
            $request->status,
            $request->admin_telegram_id
        );

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update repair status'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * Получить конфигурацию для бота
     */
    public function getConfig(): JsonResponse
    {
        $config = $this->telegramService->getBotConfig();

        return response()->json([
            'success' => true,
            'config' => $config
        ]);
    }

    /**
     * Управление состоянием пользователя
     */
    public function getUserState(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'telegram_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $state = $this->telegramService->getUserState($request->telegram_id);

        return response()->json([
            'success' => true,
            'data' => $state ? [
                'telegram_id' => $state->telegram_id,
                'current_state' => $state->current_state,
                'temp_data' => $state->temp_data,
                'updated_at' => $state->updated_at
            ] : null
        ]);
    }

    /**
     * Установить состояние пользователя
     */
    public function setUserState(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'telegram_id' => 'required|integer',
            'state' => 'required|string|max:100',
            'temp_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $userState = $this->telegramService->setUserState(
            $request->telegram_id,
            $request->state,
            $request->temp_data ?? []
        );

        return response()->json([
            'success' => true,
            'data' => [
                'telegram_id' => $userState->telegram_id,
                'current_state' => $userState->current_state,
                'temp_data' => $userState->temp_data,
                'updated_at' => $userState->updated_at
            ]
        ]);
    }

    /**
     * Очистить состояние пользователя
     */
    public function clearUserState(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'telegram_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $cleared = $this->telegramService->clearUserState($request->telegram_id);

        return response()->json([
            'success' => true,
            'cleared' => $cleared
        ]);
    }

    /**
     * Webhook для уведомлений
     */
    public function webhook(Request $request): JsonResponse
    {
        // Простая проверка безопасности
        $expectedToken = config('app.telegram_webhook_token', 'default_token');
        $providedToken = $request->header('X-Telegram-Token');

        if ($providedToken !== $expectedToken) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $action = $request->get('action');
        $data = $request->get('data', []);

        try {
            switch ($action) {
                case 'repair_created':
                    $this->handleRepairCreated($data);
                    break;
                case 'repair_updated':
                    $this->handleRepairUpdated($data);
                    break;
                case 'cartridge_created':
                    $this->handleCartridgeCreated($data);
                    break;
                default:
                    \Log::warning('Unknown webhook action', ['action' => $action]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Webhook processing error', [
                'action' => $action,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Обработка создания заявки
     */
    private function handleRepairCreated(array $data): void
    {
        \Log::info('Repair created webhook', $data);
        // Здесь можно добавить дополнительную логику
    }

    /**
     * Обработка обновления заявки
     */
    private function handleRepairUpdated(array $data): void
    {
        \Log::info('Repair updated webhook', $data);
    }

    /**
     * Обработка создания записи о картридже
     */
    private function handleCartridgeCreated(array $data): void
    {
        \Log::info('Cartridge created webhook', $data);
    }
}