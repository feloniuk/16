<?php

namespace App\Http\Controllers;

use App\Models\CartridgeReplacement;
use App\Models\RoomInventory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DirectorInventoryController extends Controller
{
    public function warehouse(Request $request)
    {
        $query = RoomInventory::warehouse();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('equipment_type', 'like', "%{$search}%")
                    ->orWhere('inventory_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('low_stock')) {
            $query->lowStock();
        }

        $items = $query->paginate(50);

        $totalValue = $items->sum(function ($item) {
            return $item->quantity * ($item->price ?? 0);
        });

        $lowStockItems = RoomInventory::warehouse()->lowStock()->count();

        return view('director-inventory.warehouse', compact('items', 'totalValue', 'lowStockItems'));
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

        $items = $query->with('branch')->paginate(50);

        $branches = \App\Models\Branch::where('is_active', true)->get();

        $totalValue = $items->sum(function ($item) {
            return $item->quantity * ($item->price ?? 0);
        });

        return view('director-inventory.equipment', compact('items', 'branches', 'totalValue'));
    }

    public function forecasting(Request $request)
    {
        $period = $request->get('period', '3');
        $months = (int) $period;

        $dateFrom = Carbon::now()->subMonths($months)->startOfMonth();
        $dateTo = Carbon::now()->endOfMonth();

        // Витрати картриджів по місяцям
        $cartridgeData = collect();

        try {
            if (config('database.default') === 'sqlite') {
                $cartridgeData = CartridgeReplacement::whereBetween('replacement_date', [$dateFrom, $dateTo])
                    ->selectRaw('strftime(\'%Y-%m-01\', replacement_date) as month, COUNT(*) as count')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
            } else {
                $cartridgeData = CartridgeReplacement::whereBetween('replacement_date', [$dateFrom, $dateTo])
                    ->selectRaw('DATE_TRUNC(\'month\', replacement_date)::date as month, COUNT(*) as count')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
            }
        } catch (\Exception $e) {
            // Fallback на простий агрегат якщо запит не працює
            $allData = CartridgeReplacement::whereBetween('replacement_date', [$dateFrom, $dateTo])->get();
            $cartridgeData = $allData->groupBy(function ($item) {
                return $item->replacement_date->format('Y-m-01');
            })->map(function ($group) {
                return (object) ['month' => $group[0]->replacement_date->format('Y-m-01'), 'count' => $group->count()];
            })->values();
        }

        // Розраховуємо витрати інвентарю через RoomInventory
        $inventoryCosts = RoomInventory::whereNotNull('price')
            ->where('price', '>', 0)
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m');
            })
            ->map(function ($group) {
                return $group->sum(function ($item) {
                    return $item->quantity * $item->price;
                });
            });

        // Прогноз на основі тренду
        $forecast = $this->calculateForecast($cartridgeData, $months);

        // Статистика
        $stats = [
            'total_cartridges' => CartridgeReplacement::whereBetween('replacement_date', [$dateFrom, $dateTo])->count(),
            'avg_monthly_cartridges' => ceil(CartridgeReplacement::whereBetween('replacement_date', [$dateFrom, $dateTo])->count() / max(1, $months)),
            'total_inventory_value' => RoomInventory::sum('price'),
            'warehouse_items_count' => RoomInventory::warehouse()->count(),
            'equipment_items_count' => RoomInventory::equipment()->count(),
        ];

        // Дані для графіка
        $chartLabels = [];
        $chartData = [];
        $currentMonth = $dateFrom->copy();

        while ($currentMonth->lte($dateTo)) {
            $monthKey = $currentMonth->format('Y-m');
            $chartLabels[] = $currentMonth->format('M Y');

            $monthToFind = $currentMonth->format('Y-m-01');
            $count = 0;

            // Пошук у даних (обробка обох форматів)
            foreach ($cartridgeData as $data) {
                if ($data->month === $monthToFind || str_contains($data->month, $monthToFind)) {
                    $count = $data->count;
                    break;
                }
            }

            $chartData[] = (int) $count;
            $currentMonth->addMonth();
        }

        // Прогноз на наступні 6 місяців
        $forecastedMonths = [];
        $forecastedData = [];
        $forecastStart = Carbon::now()->addMonth()->startOfMonth();

        for ($i = 0; $i < 6; $i++) {
            $forecastMonth = $forecastStart->copy()->addMonths($i);
            $forecastedMonths[] = $forecastMonth->format('M Y');
            $forecastedData[] = (int) $forecast['avg'];
        }

        // Гарантуємо що масиви не пусті
        if (empty($chartLabels)) {
            $chartLabels = ['Немає даних'];
            $chartData = [0];
        }

        if (empty($forecastedMonths)) {
            $forecastedMonths = ['Немає даних'];
            $forecastedData = [0];
        }

        return view('director-inventory.forecasting', compact(
            'stats',
            'period',
            'months',
            'chartLabels',
            'chartData',
            'forecastedMonths',
            'forecastedData',
            'forecast',
            'dateFrom',
            'dateTo'
        ));
    }

    private function calculateForecast($data, $months)
    {
        $values = $data->pluck('count')->toArray();

        if (empty($values)) {
            return [
                'avg' => 0,
                'trend' => 0,
                'projection' => 0,
            ];
        }

        $avg = array_sum($values) / count($values);

        // Простий тренд: порівняння першої половини з другою половиною
        $midpoint = count($values) / 2;
        $firstHalf = array_slice($values, 0, (int) $midpoint);
        $secondHalf = array_slice($values, (int) $midpoint);

        $firstAvg = ! empty($firstHalf) ? array_sum($firstHalf) / count($firstHalf) : 0;
        $secondAvg = ! empty($secondHalf) ? array_sum($secondHalf) / count($secondHalf) : 0;

        $trend = $secondAvg - $firstAvg;
        $projection = $avg + ($trend / 2); // Прогноз з 50% масштабом тренду

        return [
            'avg' => round($avg, 1),
            'trend' => round($trend, 1),
            'projection' => round(max(0, $projection), 1),
        ];
    }
}
