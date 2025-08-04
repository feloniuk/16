<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TelegramBotController;

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
    
    // Основной webhook для бота (без токена - публичный)
    Route::post('/webhook', [TelegramBotController::class, 'webhook'])->name('webhook');
    
    // Отладочные методы
    Route::get('/test-api', [TelegramBotController::class, 'testApi'])->name('test.api');
    Route::get('/webhook-info', [TelegramBotController::class, 'getWebhookInfo'])->name('webhook.info');
    Route::post('/set-webhook', [TelegramBotController::class, 'setWebhook'])->name('webhook.set');
    
    // API методы для бота
    Route::post('/user-info', [TelegramBotController::class, 'getUserInfo'])->name('user.info');
    Route::get('/stats', [TelegramBotController::class, 'getStats'])->name('stats');
    Route::get('/branches', [TelegramBotController::class, 'getBranches'])->name('branches');
    
    // Очистка webhook (для отладки)
    Route::post('/clear-webhook', function() {
        $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/deleteWebhook", [
            'drop_pending_updates' => true
        ]);
        
        return response()->json([
            'success' => $response->successful(),
            'response' => $response->json()
        ]);
    })->name('webhook.clear');
    
    // Совместимость со старыми маршрутами
    Route::post('/repair-notification', function(\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Log::info('Repair notification received', $request->all());
        return response()->json(['status' => 'ok']);
    });
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