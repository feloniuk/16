<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CartridgeReplacement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BranchAnalyticsController extends Controller
{
    public function index()
    {
        $branches = Branch::where('is_active', true)
            ->with(['repairRequests', 'cartridgeReplacements'])
            ->get();

        // Додати метрики до кожної філії
        $branchesWithMetrics = $branches->map(function ($branch) {
            $allRepairs = $branch->repairRequests;
            $completedRepairs = $allRepairs->where('status', 'виконана');

            return [
                'branch' => $branch,
                'total_repairs' => $allRepairs->count(),
                'completed_repairs' => $completedRepairs->count(),
                'completion_rate' => $allRepairs->count() > 0
                    ? round(($completedRepairs->count() / $allRepairs->count()) * 100, 1)
                    : 0,
                'avg_response_time' => $this->calculateAvgResponseTime($completedRepairs),
                'sla_compliance' => $this->calculateSlaPerfentage($completedRepairs),
                'cartridges' => $branch->cartridgeReplacements->count(),
            ];
        })->sortByDesc('total_repairs');

        return view('branch-analytics.index', compact('branchesWithMetrics'));
    }

    public function show(Branch $branch, Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $comparePeriod = $request->get('compare_period', 'previous');

        // Основний період
        $repairs = $branch->repairRequests()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        $cartridges = CartridgeReplacement::where('branch_id', $branch->id)
            ->whereBetween('replacement_date', [$dateFrom, $dateTo])
            ->get();

        // Період для порівняння
        $compareRepairs = collect();
        $compareCartridges = collect();
        $compareDateFrom = null;
        $compareDateTo = null;

        if ($comparePeriod === 'previous') {
            $periodLength = $dateFrom->diffInDays($dateTo);
            $compareDateFrom = $dateFrom->copy()->subDays($periodLength)->startOfDay();
            $compareDateTo = $dateFrom->copy()->subDay()->endOfDay();
        } elseif ($comparePeriod === 'year_ago') {
            $compareDateFrom = $dateFrom->copy()->subYear()->startOfDay();
            $compareDateTo = $dateTo->copy()->subYear()->endOfDay();
        }

        if ($compareDateFrom && $compareDateTo) {
            $compareRepairs = $branch->repairRequests()
                ->whereBetween('created_at', [$compareDateFrom, $compareDateTo])
                ->get();

            $compareCartridges = CartridgeReplacement::where('branch_id', $branch->id)
                ->whereBetween('replacement_date', [$compareDateFrom, $compareDateTo])
                ->get();
        }

        // Розрахунок метрик для основного періоду
        $metrics = $this->calculateBranchMetrics($repairs, $cartridges);

        // Розрахунок метрик для періоду порівняння
        $compareMetrics = $compareDateFrom && $compareDateTo
            ? $this->calculateBranchMetrics($compareRepairs, $compareCartridges)
            : null;

        // Розрахунок змін
        $changes = $compareMetrics ? $this->calculateChanges($metrics, $compareMetrics) : null;

        // Щоденні дані для графіків
        $dailyRepairs = $repairs->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function ($group) {
            return $group->count();
        });

        // Розподіл по статусам
        $statusDistribution = $repairs->groupBy('status')->map(function ($group) {
            return $group->count();
        })->toArray();

        // Топ кабінети
        $topRooms = $repairs->groupBy('room_number')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(10);

        // Останні заявки
        $recentRepairs = $repairs->sortByDesc('created_at')->take(10);

        return view('branch-analytics.show', compact(
            'branch',
            'dateFrom',
            'dateTo',
            'comparePeriod',
            'metrics',
            'compareMetrics',
            'changes',
            'dailyRepairs',
            'statusDistribution',
            'topRooms',
            'recentRepairs'
        ));
    }

    public function export(Branch $branch, $format, Request $request)
    {
        if ($format === 'pdf') {
            return $this->exportPdf($branch, $request);
        } elseif ($format === 'excel') {
            return $this->exportExcel($branch, $request);
        }

        abort(404);
    }

    private function exportPdf(Branch $branch, Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)
            : Carbon::now()->subMonth();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)
            : Carbon::now();

        $repairs = $branch->repairRequests()
            ->whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->get();

        $cartridges = CartridgeReplacement::where('branch_id', $branch->id)
            ->whereBetween('replacement_date', [$dateFrom, $dateTo])
            ->get();

        $metrics = $this->calculateBranchMetrics($repairs, $cartridges);

        $data = compact('branch', 'dateFrom', 'dateTo', 'metrics', 'repairs', 'cartridges');

        $pdf = PDF::loadView('exports.branch-analytics-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'branch_analytics_'.$branch->id.'_'.date('Y-m-d_H-i').'.pdf';

        return $pdf->download($filename);
    }

    private function exportExcel(Branch $branch, Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)
            : Carbon::now()->subMonth();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)
            : Carbon::now();

        $repairs = $branch->repairRequests()
            ->whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->orderBy('created_at', 'desc')
            ->get();

        $cartridges = CartridgeReplacement::where('branch_id', $branch->id)
            ->whereBetween('replacement_date', [$dateFrom, $dateTo])
            ->get();

        $metrics = $this->calculateBranchMetrics($repairs, $cartridges);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Аналітика');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $dataStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        // Заголовок
        $sheet->setCellValue('A1', 'Аналітика філії: '.$branch->name);
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray($headerStyle);
        $sheet->getStyle('A1')->getFont()->setSize(14);

        $sheet->setCellValue('A2', 'Період: '.$dateFrom->format('d.m.Y').' - '.$dateTo->format('d.m.Y'));
        $sheet->mergeCells('A2:B2');

        // Метрики
        $row = 4;
        $sheet->setCellValue('A'.$row, 'Метрика');
        $sheet->setCellValue('B'.$row, 'Значення');
        $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray($headerStyle);

        $row++;
        $metricsData = [
            ['Всього заявок', $metrics['total_repairs']],
            ['Завершено', $metrics['completed_repairs']],
            ['Коефіцієнт завершеності', $metrics['completion_rate'].'%'],
            ['SLA Дотримання', $metrics['sla_compliance'].'%'],
            ['Середній час відгуку', $metrics['avg_response_days'].' днів'],
            ['Картриджів замінено', $metrics['total_cartridges']],
        ];

        foreach ($metricsData as $metric) {
            $sheet->setCellValue('A'.$row, $metric[0]);
            $sheet->setCellValue('B'.$row, $metric[1]);
            $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray($dataStyle);
            $row++;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        // Заявки
        $sheet2 = $spreadsheet->createSheet(1);
        $sheet2->setTitle('Заявки');

        $sheet2->setCellValue('A1', 'Список заявок');
        $sheet2->mergeCells('A1:E1');
        $sheet2->getStyle('A1')->applyFromArray($headerStyle);

        $row = 2;
        $headers = ['№', 'Кабінет', 'Опис', 'Статус', 'Дата'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col).$row;
            $sheet2->setCellValue($cell, $header);
            $sheet2->getStyle($cell)->applyFromArray($headerStyle);
        }

        $row++;
        $index = 1;
        foreach ($repairs as $repair) {
            $sheet2->setCellValue('A'.$row, $index++);
            $sheet2->setCellValue('B'.$row, $repair->room_number);
            $sheet2->setCellValue('C'.$row, mb_substr($repair->description, 0, 30));
            $sheet2->setCellValue('D'.$row, $repair->status);
            $sheet2->setCellValue('E'.$row, $repair->created_at->format('d.m.Y'));
            $sheet2->getStyle('A'.$row.':E'.$row)->applyFromArray($dataStyle);
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'branch_analytics_'.$branch->id.'_'.date('Y-m-d_H-i').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function calculateBranchMetrics($repairs, $cartridges)
    {
        $completedRepairs = $repairs->where('status', 'виконана');

        return [
            'total_repairs' => $repairs->count(),
            'completed_repairs' => $completedRepairs->count(),
            'completion_rate' => $repairs->count() > 0
                ? round(($completedRepairs->count() / $repairs->count()) * 100, 1)
                : 0,
            'active_repairs' => $repairs->whereIn('status', ['нова', 'в_роботі'])->count(),
            'active_rate' => $repairs->count() > 0
                ? round(($repairs->whereIn('status', ['нова', 'в_роботі'])->count() / $repairs->count()) * 100, 1)
                : 0,
            'avg_response_hours' => $this->calculateAvgResponseTime($completedRepairs),
            'avg_response_days' => $this->calculateAvgResponseTime($completedRepairs, 'days'),
            'sla_compliance' => $this->calculateSlaPerfentage($completedRepairs),
            'total_cartridges' => $cartridges->count(),
            'cartridge_efficiency' => $repairs->count() > 0
                ? round($cartridges->count() / $repairs->count(), 2)
                : 0,
        ];
    }

    private function calculateAvgResponseTime($repairs, $unit = 'hours')
    {
        if ($repairs->isEmpty()) {
            return 0;
        }

        $avgHours = $repairs->avg(function ($repair) {
            return $repair->created_at->diffInHours($repair->updated_at);
        });

        if ($unit === 'days') {
            return round($avgHours / 24, 1);
        }

        return round($avgHours, 1);
    }

    private function calculateSlaPerfentage($repairs)
    {
        if ($repairs->isEmpty()) {
            return 0;
        }

        $slaHours = 72;
        $withinSla = $repairs->filter(function ($repair) use ($slaHours) {
            return $repair->created_at->diffInHours($repair->updated_at) <= $slaHours;
        })->count();

        return round(($withinSla / $repairs->count()) * 100, 1);
    }

    private function calculateChanges($metrics, $compareMetrics)
    {
        return [
            'repairs_change' => $this->calculatePercentChange(
                $compareMetrics['total_repairs'],
                $metrics['total_repairs']
            ),
            'completion_rate_change' => $metrics['completion_rate'] - $compareMetrics['completion_rate'],
            'sla_compliance_change' => $metrics['sla_compliance'] - $compareMetrics['sla_compliance'],
            'response_time_change' => $metrics['avg_response_hours'] - $compareMetrics['avg_response_hours'],
        ];
    }

    private function calculatePercentChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
    }
}
