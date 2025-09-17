<?php
// routes/web.php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\RepairMasterController;
use App\Http\Controllers\RepairTrackingController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\CartridgeReplacementController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryExportController;
use App\Http\Controllers\InventoryLogController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\ContractorOperationController;
use App\Http\Controllers\InventoryAuditController;
use App\Http\Controllers\InventoryTransferController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Role-based Access Control
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

// Защищенные маршруты
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Главная страница - дашборд (для всех ролей)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Профиль пользователя (для всех ролей)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // === МАРШРУТЫ ДЛЯ ВСЕХ РОЛЕЙ ===
    
    // Заявки на ремонт (просмотр для всех)
    Route::get('/repairs', [RepairRequestController::class, 'index'])->name('repairs.index');
    Route::get('/repairs/{repair}', [RepairRequestController::class, 'show'])->name('repairs.show');
    
    // Облік ремонтів (доступно всем авторизованным пользователям)
    Route::resource('repair-tracking', RepairTrackingController::class);
    
    // === МАРШРУТЫ ДЛЯ АДМИНИСТРАТОРОВ ===
    Route::middleware('role:admin')->group(function () {
        
        // Полное управление заявками
        Route::patch('/repairs/{repair}', [RepairRequestController::class, 'update'])->name('repairs.update');
        
        // Филиалы
        Route::resource('branches', BranchController::class);
        
        // Мастеры по ремонту
        Route::resource('repair-masters', RepairMasterController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Инвентарь
        Route::resource('inventory', InventoryController::class);
        Route::get('/inventory-export', [InventoryController::class, 'export'])->name('inventory.export');
        
        // Экспорт инвентаря
        Route::get('/inventory/export-form', [InventoryExportController::class, 'exportForm'])->name('inventory.export.form');
        Route::get('/inventory/export-printers', [InventoryExportController::class, 'exportPrinters'])->name('inventory.export.printers');
        Route::get('/inventory/export-branch', [InventoryExportController::class, 'exportByBranch'])->name('inventory.export.branch');
        Route::get('/inventory/export-room', [InventoryExportController::class, 'exportByRoom'])->name('inventory.export.room');
        
        // Подрядчики
        Route::resource('contractors', ContractorController::class);
        
        // Операции с подрядчиками
        Route::resource('contractor-operations', ContractorOperationController::class);
        
        // Инвентаризация
        Route::resource('inventory-audits', InventoryAuditController::class);
        Route::patch('/inventory-audits/{inventoryAudit}/items/{item}', [InventoryAuditController::class, 'updateItem'])->name('inventory-audits.update-item');
        Route::get('/inventory-audits/{inventoryAudit}/export', [InventoryAuditController::class, 'export'])->name('inventory-audits.export');
        
        // Перемещения инвентаря
        Route::resource('inventory-transfers', InventoryTransferController::class);
        Route::patch('/inventory-transfers/{inventoryTransfer}/complete', [InventoryTransferController::class, 'complete'])->name('inventory-transfers.complete');
        Route::patch('/inventory-transfers/{inventoryTransfer}/cancel', [InventoryTransferController::class, 'cancel'])->name('inventory-transfers.cancel');
        
        // Журнал операций (просмотр)
        Route::get('/inventory-logs', [InventoryLogController::class, 'index'])->name('inventory-logs.index');
        Route::get('/inventory-logs/{inventoryLog}', [InventoryLogController::class, 'show'])->name('inventory-logs.show');
    });
    
    // === МАРШРУТЫ ДЛЯ ДИРЕКТОРА ===
    Route::middleware('role:director')->group(function () {
        
        // Отчеты и аналитика
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::get('/repairs', [ReportsController::class, 'repairs'])->name('repairs');
            Route::get('/cartridges', [ReportsController::class, 'cartridges'])->name('cartridges');
            Route::get('/inventory', [ReportsController::class, 'inventory'])->name('inventory');
            Route::get('/financial', [ReportsController::class, 'financial'])->name('financial');
            Route::get('/contractors', [ReportsController::class, 'contractors'])->name('contractors');
            Route::get('/efficiency', [ReportsController::class, 'efficiency'])->name('efficiency');
        });
        
        // Экспорт отчетов
        Route::get('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
        Route::post('/reports/generate-pdf', [ReportsController::class, 'generatePdf'])->name('reports.generate-pdf');
        Route::post('/reports/generate-excel', [ReportsController::class, 'generateExcel'])->name('reports.generate-excel');
        
        // KPI и метрики
        Route::get('/kpi', [KpiController::class, 'index'])->name('kpi.index');
        Route::get('/kpi/charts', [KpiController::class, 'charts'])->name('kpi.charts');
    });

    // === МАРШРУТЫ ДЛЯ АДМИНИСТРАТОРОВ И ДИРЕКТОРОВ ===
    Route::middleware('role:admin,director')->group(function () {
        
        // Просмотр отчетов (базовый уровень)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::get('/summary', [ReportsController::class, 'summary'])->name('summary');
            Route::get('/export-basic', [ReportsController::class, 'exportBasic'])->name('export-basic');
        });
    });

    // === МАРШРУТЫ ДЛЯ ЗАВЕДУЮЩЕГО СКЛАДОМ И ДИРЕКТОРА ===
    Route::middleware('role:warehouse_manager,director')->group(function () {
        
        // Просмотр инвентаря (только просмотр для директора)
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
        
        // Просмотр журнала (только чтение для директора)
        Route::get('/inventory-logs', [InventoryLogController::class, 'index'])->name('inventory-logs.index');
        Route::get('/inventory-logs/{inventoryLog}', [InventoryLogController::class, 'show'])->name('inventory-logs.show');
        
        // Просмотр аудитов
        Route::get('/inventory-audits', [InventoryAuditController::class, 'index'])->name('inventory-audits.index');
        Route::get('/inventory-audits/{inventoryAudit}', [InventoryAuditController::class, 'show'])->name('inventory-audits.show');
    });

    // API маршруты для AJAX запросов (для всех авторизованных)
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/stats/monthly', [DashboardController::class, 'monthlyStats'])->name('stats.monthly');
        Route::get('/repairs/chart-data', [RepairRequestController::class, 'chartData'])->name('repairs.chart');
        Route::get('/branches/stats', [BranchController::class, 'stats'])->name('branches.stats');
        Route::get('/inventory/quick-search', [InventoryController::class, 'quickSearch'])->name('inventory.quick-search');
    });
});
