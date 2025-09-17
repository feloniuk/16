<?php
namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\CartridgeReplacement;
use App\Models\Branch;
use App\Models\RoomInventory;
use App\Models\InventoryLog;
use App\Models\Contractor;
use App\Models\ContractorOperation;
use App\Models\InventoryAudit;
use App\Models\InventoryTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        return match($user->role) {
            'admin' => $this->adminDashboard(),
            'warehouse_manager' => $this->warehouseManagerDashboard(),
            'director' => $this->directorDashboard(),
            default => $this->adminDashboard()
        };
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

        // Статистика картриджей
        $cartridgeCount = CartridgeReplacement::where('created_at', '>=', Carbon::now()->subMonth())->count();

        // Статистика инвентаря
        $inventoryStats = [
            'total' => RoomInventory::count(),
            'recent_moves' => InventoryLog::where('action', 'moved')
                ->where('created_at', '>=', Carbon::now()->subWeek())
                ->count(),
        ];

        // Последние действия в журнале
        $recentLogs = InventoryLog::with(['user', 'inventory'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Статистика подрядчиков
        $contractorStats = [
            'total' => Contractor::where('is_active', true)->count(),
            'active_operations' => ContractorOperation::where('status', 'in_progress')->count(),
        ];

        return view('dashboard.admin', compact(
            'repairStats', 
            'recentRepairs', 
            'cartridgeCount', 
            'inventoryStats',
            'recentLogs',
            'contractorStats'
        ));
    }

    private function warehouseManagerDashboard()
    {
        // Статистика инвентаря
        $inventoryStats = [
            'total' => RoomInventory::count(),
            'by_type' => RoomInventory::select('equipment_type')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('equipment_type')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            'recent_additions' => RoomInventory::where('created_at', '>=', Carbon::now()->subWeek())->count(),
        ];

        // Активные инвентаризации
        $activeAudits = InventoryAudit::with('branch')
            ->whereIn('status', ['planned', 'in_progress'])
            ->orderBy('audit_date', 'desc')
            ->get();

        // Статистика перемещений
        $transferStats = [
            'pending' => InventoryTransfer::where('status', 'planned')->count(),
            'in_transit' => InventoryTransfer::where('status', 'in_transit')->count(),
            'completed_this_month' => InventoryTransfer::where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count(),
        ];

        // Последние операции с подрядчиками
        $recentOperations = ContractorOperation::with(['contractor', 'inventory', 'user'])
            ->orderBy('operation_date', 'desc')
            ->limit(10)
            ->get();

        // Статистика подрядчиков
        $contractorStats = [
            'total' => Contractor::where('is_active', true)->count(),
            'by_type' => Contractor::where('is_active', true)
                ->select('type')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'active_operations' => ContractorOperation::where('status', 'in_progress')->count(),
        ];

        // Журнал действий (только по инвентарю)
        $recentLogs = InventoryLog::with(['user', 'inventory.branch'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        return view('dashboard.warehouse-manager', compact(
            'inventoryStats',
            'activeAudits',
            'transferStats',
            'recentOperations',
            'contractorStats',
            'recentLogs'
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
            'total_operations_cost' => ContractorOperation::where('status', 'completed')
                ->sum('cost'),
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
        $topBranches = Branch::withCount(['repairRequests', 'cartridgeReplacements', 'inventory'])
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

        // Финансовая статистика
        $financialStats = [
            'total_repair_costs' => ContractorOperation::where('type', 'send_for_repair')
                ->where('status', 'completed')
                ->sum('cost'),
            'total_purchase_costs' => ContractorOperation::where('type', 'purchase')
                ->where('status', 'completed')
                ->sum('cost'),
            'monthly_costs' => ContractorOperation::where('status', 'completed')
                ->where('operation_date', '>=', Carbon::now()->startOfMonth())
                ->sum('cost'),
        ];

        // KPI метрики
        $kpiMetrics = [
            'avg_repair_time' => RepairRequest::where('status', 'виконана')
                ->whereNotNull('updated_at')
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                ->value('avg_days') ?? 0,
            'inventory_utilization' => $this->calculateInventoryUtilization(),
            'contractor_efficiency' => $this->calculateContractorEfficiency(),
        ];

        return view('dashboard.director', compact(
            'totalStats',
            'monthlyStats', 
            'statusStats',
            'topBranches',
            'monthlyRepairs',
            'financialStats',
            'kpiMetrics'
        ));
    }

    private function getMonthlyStats()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'repairs_this_month' => RepairRequest::where('created_at', '>=', $currentMonth)->count(),
            'repairs_last_month' => RepairRequest::whereBetween('created_at', [
                $lastMonth, 
                $lastMonth->copy()->endOfMonth()
            ])->count(),
            'cartridges_this_month' => CartridgeReplacement::where('created_at', '>=', $currentMonth)->count(),
            'cartridges_last_month' => CartridgeReplacement::whereBetween('created_at', [
                $lastMonth,
                $lastMonth->copy()->endOfMonth()
            ])->count(),
            'inventory_moves_this_month' => InventoryLog::where('action', 'moved')
                ->where('created_at', '>=', $currentMonth)->count(),
            'costs_this_month' => ContractorOperation::where('status', 'completed')
                ->where('operation_date', '>=', $currentMonth)
                ->sum('cost'),
        ];
    }

    private function calculateInventoryUtilization()
    {
        // Процент инвентаря, который использовался в последние 3 месяца
        $totalInventory = RoomInventory::count();
        $activeInventory = RoomInventory::whereHas('cartridgeReplacements', function($query) {
            $query->where('created_at', '>=', Carbon::now()->subMonths(3));
        })->count();

        return $totalInventory > 0 ? round(($activeInventory / $totalInventory) * 100, 1) : 0;
    }

    private function calculateContractorEfficiency()
    {
        // Процент завершенных операций в срок
        $totalOperations = ContractorOperation::where('status', 'completed')->count();
        $onTimeOperations = ContractorOperation::where('status', 'completed')
            ->whereColumn('updated_at', '<=', 'operation_date')
            ->count();

        return $totalOperations > 0 ? round(($onTimeOperations / $totalOperations) * 100, 1) : 0;
    }
}