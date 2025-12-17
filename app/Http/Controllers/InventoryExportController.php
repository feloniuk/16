<?php

// app/Http/Controllers/InventoryExportController.php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\RoomInventory;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
            $query->where('room_number', 'like', '%'.$request->room_number.'%');
        }

        $printers = $query->orderBy('branch_id')
            ->orderBy('room_number')
            ->orderBy('equipment_type')
            ->get();

        return $this->generateExcel($printers, 'Принтери');
    }

    public function exportGroupedTotals(Request $request)
    {
        $query = RoomInventory::with('branch');

        // Збираємо всі branch_id для фільтрації (основний + advanced)
        $branchIds = [];
        $notBranchIds = [];

        // Основний фільтр branch_id
        if ($request->filled('branch_id')) {
            $branchIds[] = $request->branch_id;
        }

        if ($request->filled('room_number')) {
            $query->where('room_number', 'like', '%'.$request->room_number.'%');
        }

        if ($request->filled('balance_code')) {
            $query->where('balance_code', $request->balance_code);
        }

        if ($request->filled('equipment_type')) {
            $query->where('equipment_type', 'like', '%'.$request->equipment_type.'%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inventory_number', 'like', "%{$search}%")
                    ->orWhere('equipment_type', 'like', "%{$search}%")
                    ->orWhere('balance_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        // Розширений пошук (Advanced Search)
        if ($request->filled('advanced_filters')) {
            $notConditions = [];

            foreach ($request->advanced_filters as $filter) {
                if (empty($filter['field']) || empty($filter['value'])) {
                    continue;
                }

                $field = $filter['field'];
                $value = $filter['value'];
                $operator = $filter['operator'] ?? 'and';

                $allowedFields = ['branch_id', 'balance_code', 'equipment_type', 'room_number', 'brand', 'model', 'serial_number', 'inventory_number'];
                if (! in_array($field, $allowedFields)) {
                    continue;
                }

                if ($operator === 'not') {
                    if ($field === 'branch_id') {
                        $notBranchIds[] = $value;
                    } else {
                        $notConditions[] = [$field, $value];
                    }
                } else {
                    if ($field === 'branch_id') {
                        $branchIds[] = $value;
                    } else {
                        $query->where($field, 'like', "%{$value}%");
                    }
                }
            }

            // Обработка NOT условий для других полей
            foreach ($notConditions as $condition) {
                [$field, $value] = $condition;
                $query->where($field, 'not like', "%{$value}%");
            }
        }

        // Применяемо фільтр branch_id - объединяємо основний + advanced
        if (! empty($branchIds)) {
            $query->whereIn('branch_id', $branchIds);
        }

        // Применяемо NOT branch_id
        if (! empty($notBranchIds)) {
            $query->whereNotIn('branch_id', $notBranchIds);
        }

        $inventory = $query->get();

        // Группировка
        $grouped = $inventory->groupBy('equipment_type')
            ->map(function ($items) {
                return [
                    'name' => $items->first()->equipment_type,
                    'count' => $items->count(),
                    'total_quantity' => $items->sum('quantity'),
                    'balance_code' => $items->first()->balance_code,
                    'total_price' => $items->sum(function ($item) {
                        return $item->quantity * ($item->price ?? 0);
                    }),
                    'items' => $items,
                ];
            });

        // Создаем Excel файл с итогами
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Итоги инвентаря');

        // Стили
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
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Общая статистика
        $sheet->setCellValue('A1', 'Общая статистика');
        $sheet->setCellValue('B1', 'Значение');
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        $totalStats = [
            ['Уникальных наименований', $grouped->count()],
            ['Позиций', $grouped->sum('count')],
            ['Общее количество', $grouped->sum('total_quantity')],
            ['Групп баланса', $grouped->pluck('balance_code')->unique()->count()],
            ['Общая стоимость', number_format($grouped->sum('total_price'), 2).' грн'],
        ];

        // Заполнение общей статистики
        foreach ($totalStats as $index => $stat) {
            $sheet->setCellValue('A'.($index + 2), $stat[0]);
            $sheet->setCellValue('B'.($index + 2), $stat[1]);
        }
        $sheet->getStyle('A2:B'.(count($totalStats) + 1))->applyFromArray($dataStyle);

        // Группы баланса
        $balanceDetails = $grouped->groupBy('balance_code')
            ->map(function ($group) {
                return [
                    'balance_code' => $group->first()->balance_code,
                    'equipment_types_count' => $group->count(),
                    'positions_count' => $group->sum('count'),
                    'total_quantity' => $group->sum('total_quantity'),
                    'total_price' => $group->sum('total_price'),
                ];
            });

        // Заголовки групп баланса
        $sheet->setCellValue('A'.(count($totalStats) + 3), 'Группы баланса');
        $sheet->mergeCells('A'.(count($totalStats) + 3).':E'.(count($totalStats) + 3));
        $sheet->getStyle('A'.(count($totalStats) + 3))->applyFromArray($headerStyle);

        // Заголовки таблицы групп баланса
        $balanceHeaders = [
            'Код баланса',
            'Уникальных наименований',
            'Позиций',
            'Общее количество',
            'Общая стоимость (грн)',
        ];
        $startRow = count($totalStats) + 4;
        foreach ($balanceHeaders as $col => $header) {
            $sheet->setCellValue(chr(65 + $col).$startRow, $header);
        }
        $sheet->getStyle('A'.$startRow.':E'.$startRow)->applyFromArray($headerStyle);

        // Заполнение данных групп баланса
        $row = $startRow + 1;
        foreach ($balanceDetails as $detail) {
            $sheet->setCellValue('A'.$row, $detail['balance_code']);
            $sheet->setCellValue('B'.$row, $detail['equipment_types_count']);
            $sheet->setCellValue('C'.$row, $detail['positions_count']);
            $sheet->setCellValue('D'.$row, $detail['total_quantity']);
            $sheet->setCellValue('E'.$row, number_format($detail['total_price'], 2));
            $row++;
        }
        $sheet->getStyle('A'.($startRow + 1).':E'.($row - 1))->applyFromArray($dataStyle);

        // Детальная информация о наименованиях
        $sheet->setCellValue('A'.$row, 'Детали наименований');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->getStyle('A'.$row)->applyFromArray($headerStyle);

        // Заголовки для детальной информации
        $detailHeaders = [
            'Наименование',
            'Код баланса',
            'Позиций',
            'Общее количество',
            'Ед. измерения',
            'Общая стоимость (грн)',
        ];
        $row++;
        foreach ($detailHeaders as $col => $header) {
            $sheet->setCellValue(chr(65 + $col).$row, $header);
        }
        $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray($headerStyle);

        // Заполнение детальной информации
        $row++;
        foreach ($grouped->sortByDesc('total_quantity') as $detail) {
            $sheet->setCellValue('A'.$row, $detail['name']);
            $sheet->setCellValue('B'.$row, $detail['balance_code']);
            $sheet->setCellValue('C'.$row, $detail['count']);
            $sheet->setCellValue('D'.$row, $detail['total_quantity']);
            $sheet->setCellValue('E'.$row, $detail['items']->first()->unit);
            $sheet->setCellValue('F'.$row, number_format($detail['total_price'], 2));
            $row++;
        }
        $sheet->getStyle('A'.($row - $grouped->count()).':F'.($row - 1))->applyFromArray($dataStyle);

        // Автоширина колонок
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Сохраняем файл
        $filename = 'inventory_totals_'.date('Y-m-d_H-i').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportByBranch(Request $request)
    {
        $branchId = $request->get('branch_id');
        $branch = Branch::find($branchId);

        if (! $branch) {
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
        if (! $branch) {
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
        $spreadsheet = new Spreadsheet;
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
            'J1' => 'Дата додавання',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Стилізація заголовків
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Дані
        $row = 2;
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $item->branch->name);
            $sheet->setCellValue('C'.$row, $item->room_number);
            $sheet->setCellValue('D'.$row, $item->equipment_type);
            $sheet->setCellValue('E'.$row, $item->brand ?? '');
            $sheet->setCellValue('F'.$row, $item->model ?? '');
            $sheet->setCellValue('G'.$row, $item->serial_number ?? '');
            $sheet->setCellValue('H'.$row, $item->inventory_number);
            $sheet->setCellValue('I'.$row, $item->notes ?? '');
            $sheet->setCellValue('J'.$row, $item->created_at->format('d.m.Y H:i'));
            $row++;
        }

        // Автоширина колонок
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Стилізація таблиці
        $tableRange = 'A1:J'.($row - 1);
        $sheet->getStyle($tableRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Заморозити заголовок
        $sheet->freezePane('A2');

        // Генерація файлу
        $filename = $filename.'_'.date('Y-m-d_H-i-s').'.xlsx';

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
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
            'inventory as printers_count' => function ($query) {
                $query->where('equipment_type', 'like', '%принтер%')
                    ->orWhere('equipment_type', 'like', '%МФУ%')
                    ->orWhere('equipment_type', 'like', '%сканер%');
            },
        ])->where('is_active', true)->get();

        $balanceCodes = RoomInventory::whereNotNull('balance_code')
            ->distinct()
            ->pluck('balance_code')
            ->sort();

        return view('inventory.export', compact('branches', 'branchStats', 'balanceCodes'));
    }

    public function exportGroupedDetailed(Request $request)
    {
        $query = RoomInventory::with('branch');

        // Збираємо всі branch_id для фільтрації (основний + advanced)
        $branchIds = [];
        $notBranchIds = [];

        // Основний фільтр branch_id
        if ($request->filled('branch_id')) {
            $branchIds[] = $request->branch_id;
        }

        if ($request->filled('balance_code')) {
            $query->where('balance_code', $request->balance_code);
        }

        if ($request->filled('room_number')) {
            $query->where('room_number', 'like', '%'.$request->room_number.'%');
        }

        if ($request->filled('equipment_type')) {
            $query->where('equipment_type', 'like', '%'.$request->equipment_type.'%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('inventory_number', 'like', "%{$search}%")
                    ->orWhere('equipment_type', 'like', "%{$search}%")
                    ->orWhere('balance_code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%");
            });
        }

        // Розширений пошук
        if ($request->filled('advanced_filters')) {
            $notConditions = [];

            foreach ($request->advanced_filters as $filter) {
                if (empty($filter['field']) || empty($filter['value'])) {
                    continue;
                }

                $field = $filter['field'];
                $value = $filter['value'];
                $operator = $filter['operator'] ?? 'and';

                $allowedFields = ['branch_id', 'balance_code', 'equipment_type', 'room_number', 'brand', 'model', 'serial_number', 'inventory_number'];
                if (! in_array($field, $allowedFields)) {
                    continue;
                }

                if ($operator === 'not') {
                    if ($field === 'branch_id') {
                        $notBranchIds[] = $value;
                    } else {
                        $notConditions[] = [$field, $value];
                    }
                } else {
                    if ($field === 'branch_id') {
                        $branchIds[] = $value;
                    } else {
                        $query->where($field, 'like', "%{$value}%");
                    }
                }
            }

            // Обработка NOT условий для других полей
            foreach ($notConditions as $condition) {
                [$field, $value] = $condition;
                $query->where($field, 'not like', "%{$value}%");
            }
        }

        // Применяемо фільтр branch_id - объединяємо основний + advanced
        if (! empty($branchIds)) {
            $query->whereIn('branch_id', $branchIds);
        }

        // Применяемо NOT branch_id
        if (! empty($notBranchIds)) {
            $query->whereNotIn('branch_id', $notBranchIds);
        }

        $inventory = $query->get();

        // Группировка по наименованиям
        $grouped = $inventory->groupBy('equipment_type')
            ->map(function ($items) {
                return [
                    'name' => $items->first()->equipment_type,
                    'balance_code' => $items->first()->balance_code,
                    'count' => $items->count(),
                    'total_quantity' => $items->sum('quantity'),
                    'items' => $items,
                ];
            })
            ->sortBy('name');

        // Создаем Excel файл
        $spreadsheet = new Spreadsheet;

        // Стили
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

        $groupHeaderStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD'],
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

        // Первый лист - Сводка
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Зведення');

        // Заголовок сводки
        $sheet->setCellValue('A1', 'Зведення по наименуваннях');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray($headerStyle);

        // Заголовки таблицы
        $headers = ['Найменування', 'Код балансу', 'Позицій', 'Загальна кількість', 'Од. вим.', 'Примітка'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col).'2';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->applyFromArray($headerStyle);
        }

        // Заполнение сводки
        $row = 3;
        foreach ($grouped as $group) {
            $sheet->setCellValue('A'.$row, $group['name']);
            $sheet->setCellValue('B'.$row, $group['balance_code']);
            $sheet->setCellValue('C'.$row, $group['count']);
            $sheet->setCellValue('D'.$row, $group['total_quantity']);
            $sheet->setCellValue('E'.$row, $group['items']->first()->unit ?? 'шт');
            $sheet->setCellValue('F'.$row, 'Деталі на окремому аркуші');
            $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray($dataStyle);
            $row++;
        }

        // Автоширина
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->freezePane('A3');

        // Итоги
        $row++;
        $sheet->setCellValue('A'.$row, 'ВСЬОГО:');
        $sheet->setCellValue('C'.$row, $grouped->sum('count'));
        $sheet->setCellValue('D'.$row, $grouped->sum('total_quantity'));
        $sheet->getStyle('A'.$row.':F'.$row)->applyFromArray($groupHeaderStyle);

        // Создаем листы для каждой группы
        $sheetIndex = 1;
        foreach ($grouped as $group) {
            $sheetName = mb_substr($group['name'], 0, 30); // Ограничение 31 символ для названия листа
            $newSheet = $spreadsheet->createSheet($sheetIndex);
            $newSheet->setTitle($sheetName);

            // Заголовок группы
            $newSheet->setCellValue('A1', $group['name']);
            $newSheet->mergeCells('A1:J1');
            $newSheet->getStyle('A1')->applyFromArray($groupHeaderStyle);

            $newSheet->setCellValue('A2', 'Код балансу: '.$group['balance_code']);
            $newSheet->setCellValue('D2', 'Всього позицій: '.$group['count']);
            $newSheet->setCellValue('G2', 'Загальна кількість: '.$group['total_quantity']);

            // Заголовки таблицы
            $detailHeaders = ['№', 'Філія', 'Кабінет', 'Бренд', 'Модель', 'Серійний №', 'Інв. №', 'Кількість', 'Од.', 'Примітки'];
            foreach ($detailHeaders as $col => $header) {
                $cell = chr(65 + $col).'3';
                $newSheet->setCellValue($cell, $header);
                $newSheet->getStyle($cell)->applyFromArray($headerStyle);
            }

            // Заполнение данных группы
            $row = 4;
            $itemIndex = 1;
            foreach ($group['items'] as $item) {
                $newSheet->setCellValue('A'.$row, $itemIndex++);
                $newSheet->setCellValue('B'.$row, $item->branch->name);
                $newSheet->setCellValue('C'.$row, $item->room_number);
                $newSheet->setCellValue('D'.$row, $item->brand ?? '');
                $newSheet->setCellValue('E'.$row, $item->model ?? '');
                $newSheet->setCellValue('F'.$row, $item->serial_number ?? '');
                $newSheet->setCellValue('G'.$row, $item->inventory_number);
                $newSheet->setCellValue('H'.$row, $item->quantity);
                $newSheet->setCellValue('I'.$row, $item->unit);
                $newSheet->setCellValue('J'.$row, $item->notes ?? '');
                $newSheet->getStyle('A'.$row.':J'.$row)->applyFromArray($dataStyle);
                $row++;
            }

            // Автоширина для листа группы
            foreach (range('A', 'J') as $column) {
                $newSheet->getColumnDimension($column)->setAutoSize(true);
            }
            $newSheet->freezePane('A4');

            $sheetIndex++;
        }

        // Генерация файла
        $filename = 'Інвентар_групований_'.date('Y-m-d_H-i').'.xlsx';
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
