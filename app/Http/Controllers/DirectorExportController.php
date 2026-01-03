<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CartridgeReplacement;
use App\Models\RepairRequest;
use App\Models\RoomInventory;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DirectorExportController extends Controller
{
    public function exportDashboardPdf()
    {
        $data = $this->getDashboardData();

        $pdf = PDF::loadView('exports.director-dashboard-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'director_dashboard_'.date('Y-m-d_H-i').'.pdf';

        return $pdf->download($filename);
    }

    public function exportDashboardExcel()
    {
        $data = $this->getDashboardData();

        $spreadsheet = new Spreadsheet;

        // Аркуш 1: Загальна статистика
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Статистика');

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
        $sheet->setCellValue('A1', 'Дашборд директора');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->applyFromArray($headerStyle);
        $sheet->getStyle('A1')->getFont()->setSize(14);

        $sheet->setCellValue('A2', 'Дата генерації: '.date('d.m.Y H:i'));
        $sheet->mergeCells('A2:B2');

        // Загальна статистика
        $row = 4;
        $sheet->setCellValue('A'.$row, 'Показник');
        $sheet->setCellValue('B'.$row, 'Значення');
        $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray($headerStyle);

        $row++;
        $generalStats = [
            ['Філіали', $data['totalStats']['branches']],
            ['Всього заявок', $data['totalStats']['total_repairs']],
            ['Картриджі', $data['totalStats']['total_cartridges']],
            ['Інвентар', $data['totalStats']['total_inventory']],
        ];

        foreach ($generalStats as $stat) {
            $sheet->setCellValue('A'.$row, $stat[0]);
            $sheet->setCellValue('B'.$row, $stat[1]);
            $sheet->getStyle('A'.$row.':B'.$row)->applyFromArray($dataStyle);
            $row++;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        // Аркуш 2: SLA метрики
        $sheet2 = $spreadsheet->createSheet(1);
        $sheet2->setTitle('SLA метрики');

        $sheet2->setCellValue('A1', 'SLA Метрики');
        $sheet2->mergeCells('A1:B1');
        $sheet2->getStyle('A1')->applyFromArray($headerStyle);
        $sheet2->getStyle('A1')->getFont()->setSize(14);

        $row = 3;
        $sheet2->setCellValue('A'.$row, 'Показник');
        $sheet2->setCellValue('B'.$row, 'Значення');
        $sheet2->getStyle('A'.$row.':B'.$row)->applyFromArray($headerStyle);

        $row++;
        $slaStats = [
            ['SLA Дотримання (%)', $data['slaMetrics']['sla_compliance'].'%'],
            ['Кількість в межах SLA', $data['slaMetrics']['within_sla_count']],
            ['Всього завершено', $data['slaMetrics']['total_completed']],
            ['Середній час відгуку (годин)', $data['slaMetrics']['avg_response_hours']],
            ['Середній час відгуку (днів)', $data['slaMetrics']['avg_response_days']],
        ];

        foreach ($slaStats as $stat) {
            $sheet2->setCellValue('A'.$row, $stat[0]);
            $sheet2->setCellValue('B'.$row, $stat[1]);
            $sheet2->getStyle('A'.$row.':B'.$row)->applyFromArray($dataStyle);
            $row++;
        }

        $sheet2->getColumnDimension('A')->setAutoSize(true);
        $sheet2->getColumnDimension('B')->setAutoSize(true);

        // Аркуш 3: Якість обслуговування
        $sheet3 = $spreadsheet->createSheet(2);
        $sheet3->setTitle('Якість обслуговування');

        $sheet3->setCellValue('A1', 'Показники якості');
        $sheet3->mergeCells('A1:B1');
        $sheet3->getStyle('A1')->applyFromArray($headerStyle);
        $sheet3->getStyle('A1')->getFont()->setSize(14);

        $row = 3;
        $sheet3->setCellValue('A'.$row, 'Показник');
        $sheet3->setCellValue('B'.$row, 'Значення');
        $sheet3->getStyle('A'.$row.':B'.$row)->applyFromArray($headerStyle);

        $row++;
        $qualityStats = [
            ['Коефіцієнт завершеності (%)', $data['qualityMetrics']['completion_rate'].'%'],
            ['Коефіцієнт активних (%)', $data['qualityMetrics']['active_rate'].'%'],
            ['Середнє заявок на філію', $data['qualityMetrics']['avg_repairs_per_branch']],
            ['Ефективність картриджів', $data['qualityMetrics']['cartridge_efficiency']],
            ['Завершено', $data['qualityMetrics']['completed_repairs']],
            ['В роботі', $data['qualityMetrics']['active_repairs']],
        ];

        foreach ($qualityStats as $stat) {
            $sheet3->setCellValue('A'.$row, $stat[0]);
            $sheet3->setCellValue('B'.$row, $stat[1]);
            $sheet3->getStyle('A'.$row.':B'.$row)->applyFromArray($dataStyle);
            $row++;
        }

        $sheet3->getColumnDimension('A')->setAutoSize(true);
        $sheet3->getColumnDimension('B')->setAutoSize(true);

        // Аркуш 4: Топ філіали
        $sheet4 = $spreadsheet->createSheet(3);
        $sheet4->setTitle('Топ філіали');

        $sheet4->setCellValue('A1', 'Топ-5 філіалів по активності');
        $sheet4->mergeCells('A1:D1');
        $sheet4->getStyle('A1')->applyFromArray($headerStyle);
        $sheet4->getStyle('A1')->getFont()->setSize(14);

        $row = 3;
        $headers = ['Філія', 'Заявок', 'Картриджів', 'Рейтинг'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col).$row;
            $sheet4->setCellValue($cell, $header);
            $sheet4->getStyle($cell)->applyFromArray($headerStyle);
        }

        $row++;
        $rank = 1;
        foreach ($data['topBranches'] as $branch) {
            $sheet4->setCellValue('A'.$row, $branch->name);
            $sheet4->setCellValue('B'.$row, $branch->repair_requests_count);
            $sheet4->setCellValue('C'.$row, $branch->cartridge_replacements_count);
            $sheet4->setCellValue('D'.$row, '#'.$rank);
            $sheet4->getStyle('A'.$row.':D'.$row)->applyFromArray($dataStyle);
            $row++;
            $rank++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet4->getColumnDimension($col)->setAutoSize(true);
        }

        // Генерація файлу
        $filename = 'director_dashboard_'.date('Y-m-d_H-i').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function getDashboardData(): array
    {
        // Загальна статистика
        $totalStats = [
            'branches' => Branch::where('is_active', true)->count(),
            'total_repairs' => RepairRequest::count(),
            'total_cartridges' => CartridgeReplacement::count(),
            'total_inventory' => RoomInventory::count(),
        ];

        // SLA метрики
        $slaMetrics = $this->calculateSlaMetrics();

        // Якість метрики
        $qualityMetrics = $this->calculateQualityMetrics();

        // Топ філіали
        $topBranches = Branch::withCount(['repairRequests', 'cartridgeReplacements'])
            ->orderBy('repair_requests_count', 'desc')
            ->limit(5)
            ->get();

        return compact('totalStats', 'slaMetrics', 'qualityMetrics', 'topBranches');
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
}
