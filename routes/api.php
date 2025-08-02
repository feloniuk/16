<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TelegramIntegrationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Telegram Bot Integration Routes
Route::prefix('telegram')->name('telegram.')->group(function () {
    
    // Основная интеграция с ботом
    Route::post('/webhook', [TelegramIntegrationController::class, 'webhook'])->name('webhook');
    
    // Информация о пользователях
    Route::post('/user-info', [TelegramIntegrationController::class, 'getUserInfo'])->name('user.info');
    
    // Управление состояниями пользователей
    Route::get('/user-state', [TelegramIntegrationController::class, 'getUserState'])->name('user.state.get');
    Route::post('/user-state', [TelegramIntegrationController::class, 'setUserState'])->name('user.state.set');
    Route::delete('/user-state', [TelegramIntegrationController::class, 'clearUserState'])->name('user.state.clear');
    
    // Заявки на ремонт
    Route::post('/repairs', [TelegramIntegrationController::class, 'createRepair'])->name('repairs.create');
    Route::get('/repairs/recent', [TelegramIntegrationController::class, 'getRecentRepairs'])->name('repairs.recent');
    Route::patch('/repairs/status', [TelegramIntegrationController::class, 'updateRepairStatus'])->name('repairs.status');
    
    // Замены картриджей
    Route::post('/cartridges', [TelegramIntegrationController::class, 'createCartridge'])->name('cartridges.create');
    
    // Статистика и справочная информация
    Route::get('/stats', [TelegramIntegrationController::class, 'getStats'])->name('stats');
    Route::get('/branches', [TelegramIntegrationController::class, 'getBranches'])->name('branches');
    Route::get('/config', [TelegramIntegrationController::class, 'getConfig'])->name('config');
});

// Внутренние API маршруты для веб-панели
Route::middleware(['auth:sanctum'])->prefix('internal')->name('internal.')->group(function () {
    
    // Статистика для дашборда
    Route::get('/dashboard-stats', function() {
        return response()->json([
            'repairs' => [
                'total' => \App\Models\RepairRequest::count(),
                'new' => \App\Models\RepairRequest::where('status', 'нова')->count(),
                'in_progress' => \App\Models\RepairRequest::where('status', 'в_роботі')->count(),
                'completed' => \App\Models\RepairRequest::where('status', 'виконана')->count(),
            ],
            'cartridges' => [
                'total' => \App\Models\CartridgeReplacement::count(),
                'this_month' => \App\Models\CartridgeReplacement::whereMonth('created_at', now()->month)->count(),
            ],
            'branches' => \App\Models\Branch::where('is_active', true)->count(),
        ]);
    })->name('dashboard.stats');
    
    // Данные для графиков
    Route::get('/repairs/monthly', function() {
        $monthlyData = \App\Models\RepairRequest::select(
                \DB::raw('YEAR(created_at) as year'),
                \DB::raw('MONTH(created_at) as month'),
                \DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get();
            
        return response()->json($monthlyData);
    })->name('repairs.monthly');
    
    Route::get('/cartridges/monthly', function() {
        $monthlyData = \App\Models\CartridgeReplacement::select(
                \DB::raw('YEAR(created_at) as year'),
                \DB::raw('MONTH(created_at) as month'),
                \DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get();
            
        return response()->json($monthlyData);
    })->name('cartridges.monthly');
    
    // Статистика по филиалам
    Route::get('/branches/stats', function() {
        $branchStats = \App\Models\Branch::withCount([
                'repairRequests',
                'cartridgeReplacements'
            ])
            ->where('is_active', true)
            ->orderBy('repair_requests_count', 'desc')
            ->get();
            
        return response()->json($branchStats);
    })->name('branches.stats');
});

// Публичные API маршруты (для внешних интеграций)
Route::prefix('public')->name('public.')->group(function () {
    
    // Проверка статуса системы
    Route::get('/health', function() {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'version' => config('app.version', '1.0.0')
        ]);
    })->name('health');
    
    // Получение информации о филиалах (публично)
    Route::get('/branches', function() {
        $branches = \App\Models\Branch::where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $branches
        ]);
    })->name('branches');
});

// Совместимость со старыми маршрутами
Route::prefix('api/telegram')->group(function () {
    Route::post('/user-info', function(\Illuminate\Http\Request $request) {
        // Получение информации о пользователе по Telegram ID (старый формат)
        $telegramId = $request->input('telegram_id');
        
        if (!$telegramId) {
            return response()->json([
                'is_admin' => false,
                'user_info' => null
            ]);
        }
        
        $admin = \App\Models\Admin::where('telegram_id', $telegramId)->first();
        
        return response()->json([
            'is_admin' => (bool) $admin,
            'user_info' => $admin ? [
                'id' => $admin->id,
                'name' => $admin->name,
                'is_active' => $admin->is_active
            ] : null
        ]);
    });
    
    Route::post('/repair-notification', function(\Illuminate\Http\Request $request) {
        // Webhook для уведомлений о новых заявках (старый формат)
        \Illuminate\Support\Facades\Log::info('Repair notification received', $request->all());
        return response()->json(['status' => 'ok']);
    });
});