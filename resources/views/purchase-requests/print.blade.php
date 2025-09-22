{{-- resources/views/purchase-requests/print.blade.php --}}
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Заявка {{ $purchaseRequest->request_number }}</title>
    
    <style>
        @media print {
            @page {
                margin: 2cm 1.5cm;
                size: A4;
            }
            
            body {
                font-family: 'Times New Roman', serif;
                font-size: 12pt;
                line-height: 1.4;
                color: #000;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .document-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .document-number {
            font-size: 12pt;
            margin-bottom: 10px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 8px;
            border: 1px solid #000;
            vertical-align: top;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 30%;
            background-color: #f5f5f5;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        .items-table th,
        .items-table td {
            padding: 8px;
            border: 1px solid #000;
            text-align: left;
            vertical-align: top;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table .number {
            width: 5%;
            text-align: center;
        }
        
        .items-table .name {
            width: 35%;
        }
        
        .items-table .code {
            width: 15%;
            text-align: center;
        }
        
        .items-table .quantity {
            width: 10%;
            text-align: center;
        }
        
        .items-table .unit {
            width: 10%;
            text-align: center;
        }
        
        .items-table .price {
            width: 12.5%;
            text-align: right;
        }
        
        .items-table .total {
            width: 12.5%;
            text-align: right;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-block {
            width: 45%;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 30px;
            margin-bottom: 5px;
        }
        
        .signature-label {
            font-size: 10pt;
            text-align: center;
        }
        
        .notes {
            margin-top: 25px;
            border: 1px solid #000;
            padding: 10px;
            min-height: 60px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
        
        @media screen {
            body {
                background: #f8f9fa;
                padding: 40px;
            }
            
            .document-container {
                max-width: 210mm;
                margin: 0 auto;
                background: white;
                padding: 20mm;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Друкувати
    </button>
    
    <div class="document-container">
        <div class="header">
            <div class="company-name">
                {{ config('app.name', 'Медичний заклад') }}
            </div>
            <div class="document-title">
                ЗАЯВКА НА ЗАКУПІВЛЮ ТОВАРНО-МАТЕРІАЛЬНИХ ЦІННОСТЕЙ
            </div>
            <div class="document-number">
                № {{ $purchaseRequest->request_number }} від {{ $purchaseRequest->created_at->format('d.m.Y') }}
            </div>
        </div>
        
        <table class="info-table">
            <tr>
                <td class="label">Ініціатор заявки:</td>
                <td>{{ $purchaseRequest->user->name }}</td>
            </tr>
            <tr>
                <td class="label">Дата створення:</td>
                <td>{{ $purchaseRequest->created_at->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Дата потреби:</td>
                <td>{{ $purchaseRequest->requested_date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td class="label">Статус:</td>
                <td>
                    @switch($purchaseRequest->status)
                        @case('draft') Чернетка @break
                        @case('submitted') Подана на розгляд @break
                        @case('approved') Затверджена @break
                        @case('rejected') Відхилена @break
                        @case('completed') Виконана @break
                        @default {{ ucfirst($purchaseRequest->status) }}
                    @endswitch
                </td>
            </tr>
            @if($purchaseRequest->description)
            <tr>
                <td class="label">Опис заявки:</td>
                <td>{{ $purchaseRequest->description }}</td>
            </tr>
            @endif
        </table>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th class="number">№</th>
                    <th class="name">Найменування товару</th>
                    <th class="code">Код/Артикул</th>
                    <th class="quantity">Кількість</th>
                    <th class="unit">Од. вим.</th>
                    <th class="price">Ціна за од., грн</th>
                    <th class="total">Сума, грн</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseRequest->items as $index => $item)
                <tr>
                    <td class="number">{{ $index + 1 }}</td>
                    <td class="name">
                        {{ $item->item_name }}
                        @if($item->specifications)
                            <br><small style="font-style: italic;">{{ $item->specifications }}</small>
                        @endif
                    </td>
                    <td class="code">{{ $item->item_code ?: '-' }}</td>
                    <td class="quantity">{{ $item->quantity }}</td>
                    <td class="unit">{{ $item->unit }}</td>
                    <td class="price">
                        @if($item->estimated_price)
                            {{ number_format($item->estimated_price, 2, ',', ' ') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="total">
                        @if($item->estimated_price)
                            {{ number_format($item->total, 2, ',', ' ') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" style="text-align: right; font-weight: bold;">ВСЬОГО:</td>
                    <td style="text-align: right; font-weight: bold;">
                        @if($purchaseRequest->total_amount > 0)
                            {{ number_format($purchaseRequest->total_amount, 2, ',', ' ') }} грн
                        @else
                            Не визначено
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
        
        @if($purchaseRequest->notes)
        <div class="notes">
            <div class="notes-title">Додаткові примітки та технічні вимоги:</div>
            <div>{{ $purchaseRequest->notes }}</div>
        </div>
        @endif
        
        <div class="signatures">
            <div class="signature-block">
                <div style="margin-bottom: 15px;">Заявку склав:</div>
                <div class="signature-line"></div>
                <div class="signature-label">
                    (підпис) {{ $purchaseRequest->user->name }}
                </div>
                <div style="margin-top: 10px;">
                    Дата: {{ $purchaseRequest->created_at->format('d.m.Y') }}
                </div>
            </div>
            
            <div class="signature-block">
                <div style="margin-bottom: 15px;">Погоджено:</div>
                <div class="signature-line"></div>
                <div class="signature-label">
                    (підпис) Керівник
                </div>
                <div style="margin-top: 10px;">
                    Дата: ________________
                </div>
            </div>
        </div>
        
        <div style="margin-top: 40px; font-size: 10pt; color: #666;">
            <div style="border-top: 1px solid #ccc; padding-top: 10px;">
                <div style="display: flex; justify-content: space-between;">
                    <div>Заявка створена системою IT Support Panel</div>
                    <div>Сторінка 1 з 1</div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Автоматически открыть диалог печати при загрузке (опционально)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // });
        
        // Закрыть окно после печати
        window.addEventListener('afterprint', function() {
            // window.close(); // Раскомментируйте если нужно
        });
    </script>
</body>
</html>