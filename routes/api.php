<?php

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

// Telegram Bot Routes
Route::prefix('telegram')->group(function () {
    // Webhook для получения обновлений от Telegram
    Route::post('/webhook', [TelegramBotController::class, 'webhook']);
    
    // API методы для работы с ботом
    Route::post('/user-info', [TelegramBotController::class, 'getUserInfo']);
    Route::get('/stats', [TelegramBotController::class, 'getStats']);
    Route::get('/branches', [TelegramBotController::class, 'getBranches']);
    
    // Методы для управления webhook
    Route::post('/set-webhook', [TelegramBotController::class, 'setWebhook']);
    Route::get('/webhook-info', [TelegramBotController::class, 'getWebhookInfo']);
});

// Internal API routes for web panel
Route::middleware(['auth:sanctum'])->group(function () {
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
    });
    
    // API для получения данных для чартов
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
    });
    
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
    });
});