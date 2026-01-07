<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CartridgeReplacement;
use App\Models\RepairRequest;
use App\Models\RoomInventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            // 'warehouse_manager' => $this->warehouseManagerDashboard(),
            'warehouse_keeper' => $this->warehouseKeeperDashboard(),
            'director' => $this->directorDashboard(),
            default => $this->adminDashboard()
        };
    }

    private function warehouseKeeperDashboard()
    {
        // Статистика товаров на складе
        $warehouseStats = [
            'total_items' => \App\Models\WarehouseItem::active()->count(),
            'low_stock_items' => \App\Models\WarehouseItem::lowStock()->active()->count(),
            'out_of_stock' => \App\Models\WarehouseItem::where('quantity', 0)->active()->count(),
            'total_value' => \App\Models\WarehouseItem::active()->sum(DB::raw('quantity * COALESCE(price, 0)')),
        ];

        // Последние движения товаров
        $recentMovements = \App\Models\WarehouseMovement::with(['warehouseItem', 'user'])
            ->whereHas('inventoryItem')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Товары с низкими остатками
        $lowStockItems = \App\Models\WarehouseItem::lowStock()
            ->active()
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        // Статистика заявок на закупку
        $purchaseRequestsStats = [
            'total' => \App\Models\PurchaseRequest::count(),
            'draft' => \App\Models\PurchaseRequest::where('status', 'draft')->count(),
            'submitted' => \App\Models\PurchaseRequest::where('status', 'submitted')->count(),
            'approved' => \App\Models\PurchaseRequest::where('status', 'approved')->count(),
            'my_requests' => \App\Models\PurchaseRequest::where('user_id', Auth::id())->count(),
        ];

        // Последние инвентаризации
        $recentInventories = \App\Models\WarehouseInventory::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Активность по дням (последние 7 дней)
        $dailyActivity = \App\Models\WarehouseMovement::select(
            DB::raw('DATE(operation_date) as date'),
            DB::raw('COUNT(*) as movements_count')
        )
            ->where('operation_date', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Топ-5 наиболее активных товаров за месяц
        $topActiveItems = \App\Models\WarehouseMovement::select(
            'inventory_id',
            DB::raw('SUM(ABS(quantity)) as total_movements')
        )
            ->with('warehouseItem')
            ->whereHas('inventoryItem')
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->groupBy('inventory_id')
            ->orderBy('total_movements', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.warehouse-keeper', compact(
            'warehouseStats',
            'recentMovements',
            'lowStockItems',
            'purchaseRequestsStats',
            'recentInventories',
            'dailyActivity',
            'topActiveItems'
        ));
    }

    private function adminDashboard()
    {
        // Статистика заявок на ремонт
        $repairStats = [
            'total' => RepairRequest::count(),
            'new' => RepairRequest::where('status', 'нова')->count(),
            'in_progress' => RepairRequest::where('status', 'в_роботі')->count(),
            'completed' => RepairRequest::where('status', 'виконана')->count(),
        ];

        // Последние заявки
        $recentRepairs = RepairRequest::with('branch')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Статистика картриджей за последний месяц
        $cartridgeCount = CartridgeReplacement::where('created_at', '>=', Carbon::now()->subMonth())->count();

        // Статистика по филиалам
        $branchStats = Branch::withCount(['repairRequests', 'cartridgeReplacements'])
            ->orderBy('repair_requests_count', 'desc')
            ->get();

        // Общий инвентарь
        $inventoryCount = RoomInventory::count();

        return view('dashboard.admin', compact(
            'repairStats',
            'recentRepairs',
            'cartridgeCount',
            'branchStats',
            'inventoryCount'
        ));
    }

    private function directorDashboard()
    {
        // Общая статистика
        $totalStats = [
            'branches' => Branch::where('is_active', true)->count(),
            'total_repairs' => RepairRequest::count(),
            'total_cartridges' => CartridgeReplacement::count(),
            'total_inventory' => RoomInventory::count(),
        ];

        // Статистика за периоды
        $monthlyStats = $this->getMonthlyStats();

        // Статистика по статусам заявок
        $statusStats = RepairRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Топ филиалы по активности
        $topBranches = Branch::withCount(['repairRequests', 'cartridgeReplacements'])
            ->orderBy('repair_requests_count', 'desc')
            ->limit(5)
            ->get();

        // Динамика заявок по месяцам (последние 6 месяцев)
        $monthlyRepairs = RepairRequest::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // SLA та якість метрики
        $slaMetrics = $this->calculateSlaMetrics();
        $qualityMetrics = $this->calculateQualityMetrics();

        return view('dashboard.director', compact(
            'totalStats',
            'monthlyStats',
            'statusStats',
            'topBranches',
            'monthlyRepairs',
            'slaMetrics',
            'qualityMetrics'
        ));
    }

    private function calculateSlaMetrics(): array
    {
        $slaHours = 72; // 3 дні

        $completedRepairs = RepairRequest::where('status', 'виконана')->get();

        if ($completedRepairs->isEmpty()) {
            return [
                'sla_compliance' => 0,
                'within_sla_count' => 0,
                'total_completed' => 0,
                'avg_response_hours' => 0,
                'avg_response_days' => 0,
            ];
        }

        $withinSla = $completedRepairs->filter(function ($repair) use ($slaHours) {
            return $repair->created_at->diffInHours($repair->updated_at) <= $slaHours;
        })->count();

        $avgResponseHours = $completedRepairs->avg(function ($repair) {
            return $repair->created_at->diffInHours($repair->updated_at);
        });

        return [
            'sla_compliance' => round(($withinSla / $completedRepairs->count()) * 100, 1),
            'within_sla_count' => $withinSla,
            'total_completed' => $completedRepairs->count(),
            'avg_response_hours' => round($avgResponseHours, 1),
            'avg_response_days' => round($avgResponseHours / 24, 1),
        ];
    }

    private function calculateQualityMetrics(): array
    {
        $totalRepairs = RepairRequest::count();
        $completedRepairs = RepairRequest::where('status', 'виконана')->count();
        $activeRepairs = RepairRequest::whereIn('status', ['нова', 'в_роботі'])->count();
        $activeBranches = Branch::where('is_active', true)->count();
        $totalCartridges = CartridgeReplacement::count();

        return [
            'completion_rate' => $totalRepairs > 0 ? round(($completedRepairs / $totalRepairs) * 100, 1) : 0,
            'active_rate' => $totalRepairs > 0 ? round(($activeRepairs / $totalRepairs) * 100, 1) : 0,
            'avg_repairs_per_branch' => $activeBranches > 0 ? round($totalRepairs / $activeBranches, 1) : 0,
            'cartridge_efficiency' => $totalRepairs > 0 ? round($totalCartridges / $totalRepairs, 2) : 0,
            'total_repairs' => $totalRepairs,
            'completed_repairs' => $completedRepairs,
            'active_repairs' => $activeRepairs,
        ];
    }

    private function getMonthlyStats()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'repairs_this_month' => RepairRequest::where('created_at', '>=', $currentMonth)->count(),
            'repairs_last_month' => RepairRequest::whereBetween('created_at', [
                $lastMonth,
                $lastMonth->copy()->endOfMonth(),
            ])->count(),
            'cartridges_this_month' => CartridgeReplacement::where('created_at', '>=', $currentMonth)->count(),
            'cartridges_last_month' => CartridgeReplacement::whereBetween('created_at', [
                $lastMonth,
                $lastMonth->copy()->endOfMonth(),
            ])->count(),
        ];
    }
}
