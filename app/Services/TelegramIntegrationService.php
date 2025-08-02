<?php
// app/Services/TelegramIntegrationService.php
namespace App\Services;

use App\Models\UserState;
use App\Models\Admin;
use App\Models\User;
use App\Models\RepairRequest;
use App\Models\CartridgeReplacement;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TelegramIntegrationService
{
    /**
     * Проверить является ли пользователь администратором
     */
    public function isAdmin(int $telegramId): bool
    {
        return Admin::where('telegram_id', $telegramId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Получить информацию о пользователе
     */
    public function getUserInfo(int $telegramId): array
    {
        $admin = Admin::where('telegram_id', $telegramId)
            ->where('is_active', true)
            ->first();

        $webUser = User::where('telegram_id', $telegramId)
            ->where('is_active', true)
            ->first();

        return [
            'telegram_id' => $telegramId,
            'is_admin' => (bool) $admin,
            'has_web_access' => (bool) $webUser,
            'admin_info' => $admin ? [
                'id' => $admin->id,
                'name' => $admin->name,
                'is_active' => $admin->is_active,
                'created_at' => $admin->created_at
            ] : null,
            'web_user_info' => $webUser ? [
                'id' => $webUser->id,
                'name' => $webUser->name,
                'email' => $webUser->email,
                'role' => $webUser->role
            ] : null
        ];
    }

    /**
     * Получить или создать состояние пользователя
     */
    public function getUserState(int $telegramId): ?UserState
    {
        return UserState::find($telegramId);
    }

    /**
     * Установить состояние пользователя
     */
    public function setUserState(int $telegramId, string $state, array $tempData = []): UserState
    {
        return UserState::updateOrCreate(
            ['telegram_id' => $telegramId],
            [
                'current_state' => $state,
                'temp_data' => $tempData,
                'updated_at' => now()
            ]
        );
    }

    /**
     * Очистить состояние пользователя
     */
    public function clearUserState(int $telegramId): bool
    {
        return UserState::where('telegram_id', $telegramId)->delete() > 0;
    }

    /**
     * Создать заявку на ремонт
     */
    public function createRepairRequest(array $data): RepairRequest
    {
        $repair = RepairRequest::create([
            'user_telegram_id' => $data['user_telegram_id'],
            'username' => $data['username'] ?? null,
            'branch_id' => $data['branch_id'],
            'room_number' => $data['room_number'],
            'description' => $data['description'],
            'phone' => $data['phone'] ?? null,
            'status' => 'нова'
        ]);

        // Логируем создание заявки
        Log::info('Repair request created via Telegram', [
            'repair_id' => $repair->id,
            'user_id' => $data['user_telegram_id'],
            'branch_id' => $data['branch_id']
        ]);

        // Уведомляем администраторов
        $this->notifyAdminsAboutRepair($repair);

        return $repair;
    }

    /**
     * Создать запись о замене картриджа
     */
    public function createCartridgeReplacement(array $data): CartridgeReplacement
    {
        $cartridge = CartridgeReplacement::create([
            'user_telegram_id' => $data['user_telegram_id'],
            'username' => $data['username'] ?? null,
            'branch_id' => $data['branch_id'],
            'room_number' => $data['room_number'],
            'printer_info' => $data['printer_info'],
            'cartridge_type' => $data['cartridge_type'],
            'replacement_date' => $data['replacement_date'] ?? now()->toDateString(),
            'notes' => $data['notes'] ?? null
        ]);

        Log::info('Cartridge replacement created via Telegram', [
            'cartridge_id' => $cartridge->id,
            'user_id' => $data['user_telegram_id'],
            'branch_id' => $data['branch_id']
        ]);

        $this->notifyAdminsAboutCartridge($cartridge);

        return $cartridge;
    }

    /**
     * Обновить статус заявки на ремонт
     */
    public function updateRepairStatus(int $repairId, string $status, int $adminTelegramId): bool
    {
        // Проверяем права администратора
        if (!$this->isAdmin($adminTelegramId)) {
            return false;
        }

        $repair = RepairRequest::find($repairId);
        if (!$repair) {
            return false;
        }

        $oldStatus = $repair->status;
        $repair->update(['status' => $status]);

        Log::info('Repair status updated via Telegram', [
            'repair_id' => $repairId,
            'old_status' => $oldStatus,
            'new_status' => $status,
            'admin_telegram_id' => $adminTelegramId
        ]);

        return true;
    }

    /**
     * Получить статистику
     */
    public function getStats(): array
    {
        return [
            'repairs' => [
                'total' => RepairRequest::count(),
                'new' => RepairRequest::where('status', 'нова')->count(),
                'in_progress' => RepairRequest::where('status', 'в_роботі')->count(),
                'completed' => RepairRequest::where('status', 'виконана')->count()
            ],
            'cartridges' => [
                'total' => CartridgeReplacement::count(),
                'this_month' => CartridgeReplacement::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count()
            ],
            'branches' => Branch::where('is_active', true)->count()
        ];
    }

    /**
     * Получить активные филиалы
     */
    public function getActiveBranches(): array
    {
        return Branch::where('is_active', true)
            ->withCount(['repairRequests', 'cartridgeReplacements'])
            ->orderBy('name')
            ->get()
            ->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'repair_requests_count' => $branch->repair_requests_count ?? 0,
                    'cartridge_replacements_count' => $branch->cartridge_replacements_count ?? 0
                ];
            })
            ->toArray();
    }

    /**
     * Получить последние заявки
     */
    public function getRecentRepairs(int $limit = 10, int $branchId = null, int $userId = null): array
    {
        $query = RepairRequest::with('branch')->orderBy('created_at', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($userId) {
            $query->where('user_telegram_id', $userId);
        }

        return $query->limit($limit)
            ->get()
            ->map(function ($repair) {
                return [
                    'id' => $repair->id,
                    'branch' => $repair->branch->name,
                    'room_number' => $repair->room_number,
                    'description' => $repair->description,
                    'status' => $repair->status,
                    'username' => $repair->username,
                    'created_at' => $repair->created_at,
                    'updated_at' => $repair->updated_at
                ];
            })
            ->toArray();
    }

    /**
     * Уведомить администраторов о новой заявке
     */
    private function notifyAdminsAboutRepair(RepairRequest $repair): void
    {
        try {
            $admins = Admin::where('is_active', true)->get();
            
            foreach ($admins as $admin) {
                // Здесь можно добавить отправку уведомлений через различные каналы
                // Например, email, push-уведомления и т.д.
                Log::info('Admin notification about repair', [
                    'admin_id' => $admin->id,
                    'repair_id' => $repair->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error notifying admins about repair: ' . $e->getMessage());
        }
    }

    /**
     * Уведомить администраторов о замене картриджа
     */
    private function notifyAdminsAboutCartridge(CartridgeReplacement $cartridge): void
    {
        try {
            $admins = Admin::where('is_active', true)->get();
            
            foreach ($admins as $admin) {
                Log::info('Admin notification about cartridge', [
                    'admin_id' => $admin->id,
                    'cartridge_id' => $cartridge->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error notifying admins about cartridge: ' . $e->getMessage());
        }
    }

    /**
     * Очистить старые состояния пользователей
     */
    public function cleanupOldStates(int $daysOld = 7): int
    {
        return UserState::where('updated_at', '<', now()->subDays($daysOld))->delete();
    }

    /**
     * Получить конфигурацию для бота
     */
    public function getBotConfig(): array
    {
        return [
            'web_panel_url' => config('app.url'),
            'api_version' => '1.0',
            'features' => [
                'repairs' => true,
                'cartridges' => true,
                'inventory' => true,
                'reports' => true
            ],
            'limits' => [
                'max_description_length' => 1000,
                'max_room_number_length' => 50,
                'max_cartridge_type_length' => 255
            ],
            'statuses' => [
                'нова' => 'Новая',
                'в_роботі' => 'В работе',
                'виконана' => 'Выполнена'
            ]
        ];
    }
}