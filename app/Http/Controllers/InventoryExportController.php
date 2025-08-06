<?php
// app/Http/Controllers/InventoryExportController.php
namespace App\Http\Controllers;

use App\Models\RoomInventory;
use App\Models\Branch;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InventoryExportController extends Controller
{
    public function exportPrinters(Request $request)
    {
        $query = RoomInventory::with('branch')
            ->where('equipment_type', 'like', '%принтер%')
            ->orWhere('equipment_type', 'like', '%МФУ%')
            ->orWhere('equipment_type', 'like', '%сканер%');

        // Фільтрація
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('room_number')) {
            $query->where('room_number', 'like', '%' . $request->room_number . '%');
        }

        $printers = $query->orderBy('branch_id')
            ->orderBy('room_number')
            ->orderBy('equipment_type')
            ->get();

        return $this->generateExcel($printers, 'Принтери');
    }

    public function exportByBranch(Request $request)
    {
        $branchId = $request->get('branch_id');
        $branch = Branch::find($branchId);
        
        if (!$branch) {
            return redirect()->back()->withErrors(['Філію не знайдено']);
        }

        $inventory = RoomInventory::with('branch')
            ->where('branch_id', $branchId)
            ->orderBy('room_number')
            ->orderBy('equipment_type')
            ->get();

        return $this->generateExcel($inventory, "Інвентар_{$branch->name}");
    }

    public function exportByRoom(Request $request)
    {
        $branchId = $request->get('branch_id');
        $roomNumber = $request->get('room_number');
        
        $branch = Branch::find($branchId);
        if (!$branch) {
            return redirect()->back()->withErrors(['Філію не знайдено']);
        }

        $inventory = RoomInventory::with('branch')
            ->where('branch_id', $branchId)
            ->where('room_number', $roomNumber)
            ->orderBy('equipment_type')
            ->get();

        return $this->generateExcel($inventory, "Інвентар_{$branch->name}_кімната_{$roomNumber}");
    }

    private function generateExcel($data, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Заголовки
        $headers = [
            'A1' => '№',
            'B1' => 'Філія',
            'C1' => 'Кімната',
            'D1' => 'Тип обладнання',
            'E1' => 'Бренд',
            'F1' => 'Модель',
            'G1' => 'Серійний номер',
            'H1' => 'Інвентарний номер',
            'I1' => 'Примітки',
            'J1' => 'Дата додавання'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Стилізація заголовків
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Дані
        $row = 2;
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->branch->name);
            $sheet->setCellValue('C' . $row, $item->room_number);
            $sheet->setCellValue('D' . $row, $item->equipment_type);
            $sheet->setCellValue('E' . $row, $item->brand ?? '');
            $sheet->setCellValue('F' . $row, $item->model ?? '');
            $sheet->setCellValue('G' . $row, $item->serial_number ?? '');
            $sheet->setCellValue('H' . $row, $item->inventory_number);
            $sheet->setCellValue('I' . $row, $item->notes ?? '');
            $sheet->setCellValue('J' . $row, $item->created_at->format('d.m.Y H:i'));
            $row++;
        }

        // Автоширина колонок
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Стилізація таблиці
        $tableRange = 'A1:J' . ($row - 1);
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

        // Заморозити заголовок
        $sheet->freezePane('A2');

        // Генерація файлу
        $filename = $filename . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function exportForm()
    {
        $branches = Branch::where('is_active', true)->get();
        
        // Статистика по філіям
        $branchStats = Branch::withCount([
            'inventory',
            'inventory as printers_count' => function($query) {
                $query->where('equipment_type', 'like', '%принтер%')
                      ->orWhere('equipment_type', 'like', '%МФУ%')
                      ->orWhere('equipment_type', 'like', '%сканер%');
            }
        ])->where('is_active', true)->get();

        return view('inventory.export', compact('branches', 'branchStats'));
    }
}