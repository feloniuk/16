<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\CartridgeReplacement;
use App\Models\RoomInventory;
use App\Models\ContractorOperation;
use App\Models\InventoryAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KpiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:director']);
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // month, quarter, year
        $dateRange = $this->getDateRange($period);

        // KPI Метрики
        $kpis = [
            'repair_efficiency' => $this->calculateRepairEfficiency($dateRange),
            'inventory_turnover' => $this->calculateInventoryTurnover($dateRange),
            'cost_optimization' => $this->calculateCostOptimization($dateRange),
            'contractor_performance' => $this->calculateContractorPerformance($dateRange),
            'audit_compliance' => $this->calculateAuditCompliance($dateRange),
            'equipment_utilization' => $this->calculateEquipmentUtilization($dateRange),
        ];

        // Тренды
        $trends = [
            'repair_volume' => $this->getRepairVolumeTrend($dateRange),
            'cost_trend' => $this->getCostTrend($dateRange),
            'efficiency_trend' => $this->getEfficiencyTrend($dateRange),
        ];

        return view('kpi.index', compact('kpis', 'trends', 'period'));
    }

    public function charts(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $chartData = [
            'repair_status_distribution' => $this->getRepairStatusDistribution($dateRange),
            'monthly_costs' => $this->getMonthlyCosts($dateRange),
            'contractor_comparison' => $this->getContractorComparison($dateRange),
            'branch_performance' => $this->getBranchPerformance($dateRange),
        ];

        return response()->json($chartData);
    }

    private function getDateRange($period)
    {
        return match($period) {
            'quarter' => [Carbon::now()->startOfQuarter(), Carbon::now()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()]
        };
    }

    private function calculateRepairEfficiency($dateRange)
    {
        $totalRepairs = RepairRequest::whereBetween('created_at', $dateRange)->count();
        $completedRepairs = RepairRequest::whereBetween('created_at', $dateRange)
            ->where('status', 'виконана')
            ->count();
        
        $avgTime = RepairRequest::whereBetween('created_at', $dateRange)
            ->where('status', 'виконана')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days') ?? 0;

        return [
            'completion_rate' => $totalRepairs > 0 ? round(($completedRepairs / $totalRepairs) * 100, 1) : 0,
            'avg_resolution_time' => round($avgTime, 1),
            'total_repairs' => $totalRepairs,
            'completed_repairs' => $completedRepairs,
        ];
    }

    private function calculateInventoryTurnover($dateRange)
    {
        $totalInventory = RoomInventory::count();
        $movedItems = DB::table('inventory_logs')
            ->whereBetween('created_at', $dateRange)
            ->where('action', 'moved')
            ->distinct('inventory_id')
            ->count('inventory_id');

        return [
            'turnover_rate' => $totalInventory > 0 ? round(($movedItems / $totalInventory) * 100, 1) : 0,
            'total_items' => $totalInventory,
            'moved_items' => $movedItems,
        ];
    }

    private function calculateCostOptimization($dateRange)
    {
        $currentPeriodCosts = ContractorOperation::whereBetween('operation_date', $dateRange)
            ->where('status', 'completed')
            ->sum('cost');

        $previousPeriod = match(count($dateRange)) {
            2 => [
                $dateRange[0]->copy()->subMonth(),
                $dateRange[1]->copy()->subMonth()
            ],
            default => [$dateRange[0], $dateRange[1]]
        };

        $previousPeriodCosts = ContractorOperation::whereBetween('operation_date', $previousPeriod)
            ->where('status', 'completed')
            ->sum('cost');

        $costChange = $previousPeriodCosts > 0 
            ? (($currentPeriodCosts - $previousPeriodCosts) / $previousPeriodCosts) * 100 
            : 0;

        return [
            'current_costs' => $currentPeriodCosts,
            'previous_costs' => $previousPeriodCosts,
            'cost_change' => round($costChange, 1),
            'optimization_score' => max(0, 100 - abs($costChange)),
        ];
    }

    private function calculateContractorPerformance($dateRange)
    {
        $contractors = DB::table('contractor_operations')
            ->join('contractors', 'contractor_operations.contractor_id', '=', 'contractors.id')
            ->whereBetween('operation_date', $dateRange)
            ->select(
                'contractors.name',
                DB::raw('COUNT(*) as operations_count'),
                DB::raw('AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completion_rate'),
                DB::raw('SUM(cost) as total_cost')
            )
            ->groupBy('contractors.id', 'contractors.name')
            ->orderBy('completion_rate', 'desc')
            ->get();

        $avgPerformance = $contractors->avg('completion_rate') * 100;

        return [
            'avg_performance' => round($avgPerformance, 1),
            'top_performers' => $contractors->take(3),
            'total_contractors' => $contractors->count(),
        ];
    }

    private function calculateAuditCompliance($dateRange)
    {
        $totalAudits = InventoryAudit::whereBetween('audit_date', $dateRange)->count();
        $completedAudits = InventoryAudit::whereBetween('audit_date', $dateRange)
            ->where('status', 'completed')
            ->count();

        $avgAccuracy = InventoryAudit::whereBetween('audit_date', $dateRange)
            ->where('status', 'completed')
            ->selectRaw('AVG((total_items - missing_items - extra_items) / total_items * 100) as accuracy')
            ->value('accuracy') ?? 100;

        return [
            'completion_rate' => $totalAudits > 0 ? round(($completedAudits / $totalAudits) * 100, 1) : 0,
            'avg_accuracy' => round($avgAccuracy, 1),
            'total_audits' => $totalAudits,
            'completed_audits' => $completedAudits,
        ];
    }

    private function calculateEquipmentUtilization($dateRange)
    {
        $totalEquipment = RoomInventory::count();
        $utilizationData = DB::table('cartridge_replacements')
            ->whereBetween('created_at', $dateRange)
            ->join('room_inventory', 'cartridge_replacements.printer_inventory_id', '=', 'room_inventory.id')
            ->select('room_inventory.equipment_type')
            ->groupBy('room_inventory.equipment_type')
            ->selectRaw('COUNT(*) as usage_count')
            ->get();

        return [
            'total_equipment' => $totalEquipment,
            'utilization_by_type' => $utilizationData,
            'avg_utilization' => $totalEquipment > 0 ? round(($utilizationData->sum('usage_count') / $totalEquipment) * 100, 1) : 0,
        ];
    }

    private function getRepairVolumeTrend($dateRange)
    {
        return RepairRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', $dateRange)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getCostTrend($dateRange)
    {
        return ContractorOperation::selectRaw('DATE(operation_date) as date, SUM(cost) as total_cost')
            ->whereBetween('operation_date', $dateRange)
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getEfficiencyTrend($dateRange)
    {
        return RepairRequest::selectRaw('DATE(created_at) as date, AVG(DATEDIFF(updated_at, created_at)) as avg_time')
            ->whereBetween('created_at', $dateRange)
            ->where('status', 'виконана')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRepairStatusDistribution($dateRange)
    {
        return RepairRequest::selectRaw('status, COUNT(*) as count')
            ->whereBetween('created_at', $dateRange)
            ->groupBy('status')
            ->get();
    }

    private function getMonthlyCosts($dateRange)
    {
        return ContractorOperation::selectRaw('YEAR(operation_date) as year, MONTH(operation_date) as month, SUM(cost) as total_cost')
            ->whereBetween('operation_date', $dateRange)
            ->where('status', 'completed')
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get();
    }

    private function getContractorComparison($dateRange)
    {
        return DB::table('contractor_operations')
            ->join('contractors', 'contractor_operations.contractor_id', '=', 'contractors.id')
            ->whereBetween('operation_date', $dateRange)
            ->select(
                'contractors.name',
                DB::raw('COUNT(*) as operations'),
                DB::raw('SUM(cost) as total_cost'),
                DB::raw('AVG(CASE WHEN status = "completed" THEN 1 ELSE 0 END) * 100 as success_rate')
            )
            ->groupBy('contractors.id', 'contractors.name')
            ->get();
    }

    private function getBranchPerformance($dateRange)
    {
        return DB::table('repair_requests')
            ->join('branches', 'repair_requests.branch_id', '=', 'branches.id')
            ->whereBetween('repair_requests.created_at', $dateRange)
            ->select(
                'branches.name',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(CASE WHEN status = "виконана" THEN 1 ELSE 0 END) as completed'),
                DB::raw('AVG(CASE WHEN status = "виконана" THEN DATEDIFF(updated_at, created_at) END) as avg_resolution_time')
            )
            ->groupBy('branches.id', 'branches.name')
            ->get();
    }
}