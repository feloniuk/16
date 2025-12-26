{{-- resources/views/purchase-requests/print.blade.php --}}
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Заявка {{ $purchaseRequest->request_number }}</title>

    <style>
        @page {
            margin: 20mm 17mm 25mm 20mm;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20mm;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            font-size: 10pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }

        .document-container {
            padding: 0;
        }

        .approval {
            text-align: right;
            margin-bottom: 20px;
            font-size: 12pt;
        }

        .director-signature {
            text-align: right;
            margin-bottom: 30px;
            font-size: 12pt;
            line-height: 1.8;
        }

        .title {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .info-row {
            margin-bottom: 8px;
            font-size: 12pt;
        }

        .info-label {
            font-weight: bold;
        }

        .spacer {
            height: 20px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12pt;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .items-table .col-number {
            width: 7%;
            text-align: center;
        }

        .items-table .col-name {
            width: 60%;
        }

        .items-table .col-unit {
            width: 12%;
            text-align: center;
        }

        .items-table .col-quantity {
            width: 21%;
            text-align: center;
        }

        .signatures {
            margin-top: 50px;
            font-size: 11pt;
        }

        .signature-row {
            margin-bottom: 30px;
            line-height: 2;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            display: inline-block;
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
            .no-print {
                display: block !important;
            }

            .footer {
                display: none;
            }

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

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 0;
                padding-bottom: 25mm;
            }

            .document-container {
                max-width: 100%;
                padding: 0;
                margin: 0;
            }

            .footer {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Друкувати
    </button>

    <div class="document-container">
        <div class="approval">
            « ЗАТВЕРДЖУЮ »
        </div>

        <div class="director-signature">
            Директор КНП «ЦПМСД № 16» ОМР<br>
            _____________
        </div>

        <div class="title">
            ЗАЯВКА на ________________ року
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-number">№ з/п</th>
                    <th class="col-name">Найменування</th>
                    <th class="col-unit">Одиниця виміру</th>
                    <th class="col-quantity">Кількість товарів</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseRequest->items as $index => $item)
                <tr>
                    <td class="col-number">{{ $index + 1 }}</td>
                    <td class="col-name">
                        {{ $item->item_name }}
                        @if($item->item_code)
                            ({{ $item->item_code }})
                        @endif
                        @if($item->specifications)
                            <br><small>{{ $item->specifications }}</small>
                        @endif
                    </td>
                    <td class="col-unit">{{ $item->unit ?? 'шт.' }}</td>
                    <td class="col-quantity">{{ $item->quantity }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">
                        Немає товарів у заявці
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="spacer"></div>
        <div class="spacer"></div>

        <div class="signatures">
            <div class="signature-row">
                Заст. директора з АГР _________________
            </div>
        </div>
    </div>

    <div class="footer">
        Створено автоматизованою системою
    </div>

    <script>
        window.addEventListener('afterprint', function() {
            // window.close();
        });
    </script>
</body>
</html>