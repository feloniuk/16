<?php
// routes/web.php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseInventoryController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\CartridgeReplacementController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryExportController;
use App\Http\Controllers\RepairTrackingController;
use App\Http\Controllers\RepairMasterController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Аутентификация (Laravel Breeze)
require __DIR__.'/auth.php';

// Защищенные маршруты
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Главная страница - дашборд
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Профиль пользователя
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Заявки на ремонт
    Route::resource('repairs', RepairRequestController::class)->only(['index', 'show', 'update']);
    
    // Замены картриджей
    Route::resource('cartridges', CartridgeReplacementController::class)->only(['index', 'show']);
    
    // Облік ремонтів (доступно всем авторизованным пользователям)
    Route::resource('repair-tracking', RepairTrackingController::class);
    
    // Отчеты
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/repairs', [ReportsController::class, 'repairs'])->name('repairs');
        Route::get('/cartridges', [ReportsController::class, 'cartridges'])->name('cartridges');
        Route::get('/inventory', [ReportsController::class, 'inventory'])->name('inventory');
        Route::get('/export', [ReportsController::class, 'export'])->name('export');
    });
    
    // Только для администраторов
    Route::middleware('role:admin')->group(function () {
        // Филиалы
        Route::resource('branches', BranchController::class);
        
        // Мастеры по ремонту
        Route::resource('repair-masters', RepairMasterController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Инвентарь
        Route::resource('inventory', InventoryController::class);
        Route::get('/inventory-export', [InventoryController::class, 'export'])->name('inventory.export');
        
        // Експорт інвентарю
        Route::get('/inventory/export-form', [InventoryExportController::class, 'exportForm'])->name('inventory.export.form');
        Route::get('/inventory/export-printers', [InventoryExportController::class, 'exportPrinters'])->name('inventory.export.printers');
        Route::get('/inventory/export-branch', [InventoryExportController::class, 'exportByBranch'])->name('inventory.export.branch');
        Route::get('/inventory/export-room', [InventoryExportController::class, 'exportByRoom'])->name('inventory.export.room');
    });

    // API маршруты для AJAX запросов
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/stats/monthly', [DashboardController::class, 'monthlyStats'])->name('stats.monthly');
        Route::get('/repairs/chart-data', [RepairRequestController::class, 'chartData'])->name('repairs.chart');
        Route::get('/branches/stats', [BranchController::class, 'stats'])->name('branches.stats');
    });
});

Route::middleware('role:admin,warehouse_keeper')->group(function () {

    // Склад - товары
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.create');
    Route::post('/warehouse', [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::get('/warehouse/{item}', [WarehouseController::class, 'show'])->name('warehouse.show');
    Route::get('/warehouse/{item}/edit', [WarehouseController::class, 'edit'])->name('warehouse.edit');
    Route::patch('/warehouse/{item}', [WarehouseController::class, 'update'])->name('warehouse.update');

    // Операции с товарами
    Route::post('/warehouse/{item}/receipt', [WarehouseController::class, 'receipt'])->name('warehouse.receipt');
    Route::post('/warehouse/{item}/issue', [WarehouseController::class, 'issue'])->name('warehouse.issue');

    // Движения товаров
    Route::get('/warehouse-movements', [WarehouseController::class, 'movements'])->name('warehouse.movements');

    // Инвентаризация склада
    Route::prefix('warehouse-inventory')->name('warehouse-inventory.')->group(function () {
        Route::get('/', [WarehouseInventoryController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseInventoryController::class, 'create'])->name('create');
        Route::post('/', [WarehouseInventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}', [WarehouseInventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [WarehouseInventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [WarehouseInventoryController::class, 'update'])->name('update');
        
        // Дополнительные действия с инвентаризацией
        Route::patch('/{inventory}/complete', [WarehouseInventoryController::class, 'complete'])->name('complete');
        Route::patch('/{inventory}/items/{item}', [WarehouseInventoryController::class, 'updateItem'])->name('update-item');
        
        // Быстрая инвентаризация
        Route::get('/quick/start', [WarehouseInventoryController::class, 'quickInventory'])->name('quick');
        Route::post('/quick/process', [WarehouseInventoryController::class, 'processQuickInventory'])->name('process-quick');
    });
    
    // Заявки на закупку
    Route::resource('purchase-requests', PurchaseRequestController::class)->names([
        'index' => 'purchase-requests.index',
        'create' => 'purchase-requests.create',
        'store' => 'purchase-requests.store',
        'show' => 'purchase-requests.show',
        'edit' => 'purchase-requests.edit',
        'update' => 'purchase-requests.update',
    ]);

    // Дополнительные действия с заявками
    Route::post('/purchase-requests/{purchaseRequest}/submit', [PurchaseRequestController::class, 'submit'])->name('purchase-requests.submit');
    Route::get('/purchase-requests/{purchaseRequest}/print', [PurchaseRequestController::class, 'print'])->name('purchase-requests.print');

    // API для автозаполнения
    Route::get('/api/warehouse-items/search', function(Request $request) {
        $query = $request->get('q', '');
        $items = \App\Models\WarehouseItem::active()
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'code', 'unit', 'price']);

        return response()->json($items);
    })->name('api.warehouse-items.search');
});

// === МАРШРУТЫ ДЛЯ ВСЕХ (включая складовщика) ===
Route::middleware('role:admin,warehouse_keeper,warehouse_manager,director')->group(function () {

    // Филиалы (просмотр для всех)
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::get('/branches/{branch}', [BranchController::class, 'show'])->name('branches.show');

    // Инвентарь (просмотр для всех)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
});

// Middleware для регистрации
Route::middleware('guest')->group(function () {
    // Дополнительные маршруты для гостей при необходимости
});