<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CartridgeReplacement;
use App\Models\RoomInventory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirectorInventoryController extends Controller
{
    private const WAREHOUSE_BRANCH_ID = 6;

    public function warehouse(Request $request)
    {
        $query = RoomInventory::warehouse();
        $categories = config('warehouse-categories');

        $activeCategory = $request->get('category', $categories[0] ?? null);

        if ($activeCategory) {
            $query->where('category', $activeCategory);
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('equipment_type', 'like', "%{$search}%")
                    ->orWhere('inventory_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('low_stock')) {
            $query->lowStock();
        }

        // Total value across ALL matching items (not just current page)
        $totalValue = (clone $query)->sum(DB::raw('quantity * COALESCE(price, 0)'));
        $lowStockItems = RoomInventory::warehouse()->where('category', $activeCategory)->lowStock()->count();

        $items = $query->orderBy('full_name')->paginate(50);

        // Top 5 issued this month for sidebar
        $topIssuedThisMonth = DB::table('warehouse_movements')
            ->join('room_inventory', 'warehouse_movements.inventory_id', '=', 'room_inventory.id')
            ->whereIn('warehouse_movements.type', ['issue', 'writeoff'])
            ->whereYear('warehouse_movements.operation_date', now()->year)
            ->whereMonth('warehouse_movements.operation_date', now()->month)
            ->where('room_inventory.category', $activeCategory)
            ->select(
                'room_inventory.equipment_type',
                'room_inventory.full_name',
                'room_inventory.unit',
                DB::raw('SUM(ABS(warehouse_movements.quantity)) as total_issued')
            )
            ->groupBy('room_inventory.equipment_type', 'room_inventory.full_name', 'room_inventory.unit')
            ->orderByDesc('total_issued')
            ->limit(5)
            ->get();

        return view('director-inventory.warehouse', compact(
            'items', 'totalValue', 'lowStockItems', 'categories', 'activeCategory', 'topIssuedThisMonth'
        ));
    }

    public function equipment(Request $request)
    {
        $query = RoomInventory::equipment();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->get('branch_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('equipment_type', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $totalValue = (clone $query)->sum(DB::raw('quantity * COALESCE(price, 0)'));
        $items = $query->orderBy('branch_id')->orderBy('equipment_type')->with('branch')->paginate(50);
        $branches = Branch::where('is_active', true)->get();

        return view('director-inventory.equipment', compact('items', 'branches', 'totalValue'));
    }

    public function forecasting(Request $request)
    {
        $period = $request->get('period', '3');
        $months = (int) $period;

        $dateFrom = Carbon::now()->subMonths($months)->startOfMonth();
        $dateTo = Carbon::now()->endOfMonth();

        // --- Картриджі ---
        $cartridgeMonthly = CartridgeReplacement::whereBetween('replacement_date', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(replacement_date, '%Y-%m-01') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $cartridgeByType = CartridgeReplacement::whereBetween('replacement_date', [$dateFrom, $dateTo])
            ->select('cartridge_type', DB::raw('COUNT(*) as count'))
            ->groupBy('cartridge_type')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // --- Динаміка видачі зі складу по категоріях ---
        $categoryMonthly = DB::table('warehouse_movements')
            ->join('room_inventory', 'warehouse_movements.inventory_id', '=', 'room_inventory.id')
            ->whereIn('warehouse_movements.type', ['issue', 'writeoff'])
            ->whereBetween('warehouse_movements.operation_date', [$dateFrom, $dateTo])
            ->selectRaw("DATE_FORMAT(warehouse_movements.operation_date, '%Y-%m-01') as month, room_inventory.category, SUM(ABS(warehouse_movements.quantity)) as total_issued")
            ->groupBy('month', 'room_inventory.category')
            ->orderBy('month')
            ->get();

        // --- Топ-10 позицій за кількістю видачі ---
        $topIssuedItems = DB::table('warehouse_movements')
            ->join('room_inventory', 'warehouse_movements.inventory_id', '=', 'room_inventory.id')
            ->whereIn('warehouse_movements.type', ['issue', 'writeoff'])
            ->whereBetween('warehouse_movements.operation_date', [$dateFrom, $dateTo])
            ->select(
                'room_inventory.id',
                'room_inventory.equipment_type',
                'room_inventory.full_name',
                'room_inventory.category',
                'room_inventory.unit',
                'room_inventory.quantity as current_stock',
                'room_inventory.min_quantity',
                DB::raw('SUM(ABS(warehouse_movements.quantity)) as total_issued'),
                DB::raw('COUNT(DISTINCT warehouse_movements.id) as issue_events')
            )
            ->groupBy('room_inventory.id', 'room_inventory.equipment_type', 'room_inventory.full_name',
                'room_inventory.category', 'room_inventory.unit', 'room_inventory.quantity', 'room_inventory.min_quantity')
            ->orderByDesc('total_issued')
            ->limit(10)
            ->get()
            ->map(function ($item) use ($months) {
                $item->avg_monthly = round($item->total_issued / max(1, $months), 1);
                $item->days_left = $item->avg_monthly > 0
                    ? (int) round($item->current_stock / ($item->avg_monthly / 30))
                    : null;

                return $item;
            });

        // --- Прогнозування по витратних матеріалах ---
        // Категорії що постійно витрачаються
        $consumableCategories = ['орг техніка', 'канцелярські товари', 'миючі засоби', 'господарчі товари'];

        $consumablesForecast = DB::table('warehouse_movements')
            ->join('room_inventory', 'warehouse_movements.inventory_id', '=', 'room_inventory.id')
            ->whereIn('warehouse_movements.type', ['issue', 'writeoff'])
            ->whereBetween('warehouse_movements.operation_date', [$dateFrom, $dateTo])
            ->where('room_inventory.branch_id', self::WAREHOUSE_BRANCH_ID)
            ->whereIn('room_inventory.category', $consumableCategories)
            ->select(
                'room_inventory.id',
                'room_inventory.equipment_type',
                'room_inventory.full_name',
                'room_inventory.category',
                'room_inventory.unit',
                'room_inventory.quantity as current_stock',
                'room_inventory.min_quantity',
                DB::raw('SUM(ABS(warehouse_movements.quantity)) as total_issued')
            )
            ->groupBy('room_inventory.id', 'room_inventory.equipment_type', 'room_inventory.full_name',
                'room_inventory.category', 'room_inventory.unit', 'room_inventory.quantity', 'room_inventory.min_quantity')
            ->having('total_issued', '>', 0)
            ->orderBy('room_inventory.category')
            ->orderByDesc('total_issued')
            ->get()
            ->map(function ($item) use ($months) {
                $item->avg_monthly = round($item->total_issued / max(1, $months), 1);
                $item->days_left = $item->avg_monthly > 0
                    ? (int) round($item->current_stock / ($item->avg_monthly / 30))
                    : null;
                $item->months_forecast = $item->avg_monthly > 0
                    ? round($item->current_stock / $item->avg_monthly, 1)
                    : null;
                // Urgency: critical ≤14 days, warning ≤30 days
                $item->urgency = match (true) {
                    $item->days_left === null => 'ok',
                    $item->days_left <= 0 => 'depleted',
                    $item->days_left <= 14 => 'critical',
                    $item->days_left <= 30 => 'warning',
                    default => 'ok',
                };

                return $item;
            });

        // --- Загальна статистика ---
        $totalCartridges = CartridgeReplacement::whereBetween('replacement_date', [$dateFrom, $dateTo])->count();
        $avgMonthlyCartridges = ceil($totalCartridges / max(1, $months));
        $cartridgeForecast = $this->calculateTrend($cartridgeMonthly->pluck('count')->toArray());

        $criticalItems = $consumablesForecast->whereIn('urgency', ['critical', 'depleted'])->count();
        $warningItems = $consumablesForecast->where('urgency', 'warning')->count();
        $totalIssued = $topIssuedItems->sum('total_issued');

        $stats = [
            'total_cartridges' => $totalCartridges,
            'avg_monthly_cartridges' => $avgMonthlyCartridges,
            'cartridge_trend' => $cartridgeForecast['trend'],
            'cartridge_projection' => $cartridgeForecast['projection'],
            'total_issued' => $totalIssued,
            'critical_items' => $criticalItems,
            'warning_items' => $warningItems,
            'warehouse_items_count' => RoomInventory::warehouse()->count(),
        ];

        // --- Дані для графіків ---
        [$cartridgeChartLabels, $cartridgeChartData] = $this->buildMonthlyChartData($cartridgeMonthly, $dateFrom, $dateTo);

        // Стековий графік по категоріях
        $allMonths = collect();
        $currentMonth = $dateFrom->copy();
        while ($currentMonth->lte($dateTo)) {
            $allMonths->push($currentMonth->format('Y-m-01'));
            $currentMonth->addMonth();
        }

        $chartMonthLabels = $allMonths->map(fn ($m) => Carbon::parse($m)->translatedFormat('M Y'))->toArray();

        $categoryColors = [
            'господарчі товари' => 'rgba(59,130,246,0.8)',
            'канцелярські товари' => 'rgba(16,185,129,0.8)',
            'миючі засоби' => 'rgba(245,158,11,0.8)',
            'орг техніка' => 'rgba(139,92,246,0.8)',
            'електрика' => 'rgba(239,68,68,0.8)',
            'сантехніка' => 'rgba(20,184,166,0.8)',
            'буд матеріали' => 'rgba(156,163,175,0.8)',
        ];

        $distinctCategories = $categoryMonthly->pluck('category')->unique()->values()->toArray();

        $categoryDatasets = collect($distinctCategories)->map(function ($cat) use ($categoryMonthly, $allMonths, $categoryColors) {
            $data = $allMonths->map(function ($month) use ($cat, $categoryMonthly) {
                return (int) ($categoryMonthly->where('category', $cat)->where('month', $month)->first()?->total_issued ?? 0);
            })->toArray();

            return [
                'label' => ucfirst($cat),
                'data' => $data,
                'backgroundColor' => $categoryColors[$cat] ?? 'rgba(107,114,128,0.8)',
                'borderWidth' => 0,
            ];
        })->values()->toArray();

        // Прогноз картриджів на 6 місяців вперед
        $forecastMonths = [];
        $forecastData = [];
        for ($i = 1; $i <= 6; $i++) {
            $forecastMonths[] = Carbon::now()->addMonths($i)->translatedFormat('M Y');
            $forecastData[] = (int) $cartridgeForecast['projection'];
        }

        return view('director-inventory.forecasting', compact(
            'stats', 'period', 'months', 'dateFrom', 'dateTo',
            'cartridgeChartLabels', 'cartridgeChartData',
            'cartridgeByType',
            'chartMonthLabels', 'categoryDatasets',
            'forecastMonths', 'forecastData',
            'cartridgeForecast',
            'topIssuedItems',
            'consumablesForecast',
            'consumableCategories'
        ));
    }

    private function buildMonthlyChartData($monthlyData, Carbon $from, Carbon $to): array
    {
        $labels = [];
        $data = [];
        $current = $from->copy();

        while ($current->lte($to)) {
            $monthKey = $current->format('Y-m-01');
            $labels[] = $current->translatedFormat('M Y');
            $found = $monthlyData->first(fn ($row) => str_starts_with($row->month, substr($monthKey, 0, 7)));
            $data[] = $found ? (int) $found->count : 0;
            $current->addMonth();
        }

        return [$labels ?: ['Немає даних'], $data ?: [0]];
    }

    private function calculateTrend(array $values): array
    {
        if (empty($values)) {
            return ['avg' => 0, 'trend' => 0, 'projection' => 0];
        }

        $avg = array_sum($values) / count($values);
        $mid = (int) (count($values) / 2);
        $firstAvg = $mid > 0 ? array_sum(array_slice($values, 0, $mid)) / $mid : $avg;
        $secondAvg = $mid > 0 ? array_sum(array_slice($values, $mid)) / max(1, count($values) - $mid) : $avg;
        $trend = $secondAvg - $firstAvg;

        return [
            'avg' => round($avg, 1),
            'trend' => round($trend, 1),
            'projection' => round(max(0, $avg + $trend * 0.5), 1),
        ];
    }
}
