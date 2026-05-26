<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Акт списання {{ $writeoffRequest->request_number }}</title>
    <style>
        @page { margin: 20mm 17mm 25mm 20mm; }
        * { margin: 0; padding: 0; }
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.4; color: #000; }

        .document-container { padding: 0; }
        .approval { text-align: right; margin-bottom: 20px; font-size: 12pt; }
        .director-signature { text-align: right; margin-bottom: 30px; font-size: 12pt; line-height: 1.8; }

        .title { text-align: center; font-size: 13pt; margin-bottom: 15px; font-weight: bold; }
        .subtitle { text-align: center; font-size: 11pt; margin-bottom: 20px; }

        .info-row { margin-bottom: 8px; font-size: 12pt; }
        .spacer { height: 15px; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt; }
        .items-table th, .items-table td {
            border: 1px solid #000; padding: 6px 4px;
            vertical-align: top; word-break: break-word; white-space: normal;
        }
        .items-table th { background-color: #f5f5f5; font-weight: bold; text-align: center; padding: 8px 4px; }
        .items-table .col-number { width: 8mm; text-align: center; white-space: nowrap; }
        .items-table .col-name { width: 110mm; }
        .items-table .col-unit { width: 18mm; text-align: center; }
        .items-table .col-quantity { width: 20mm; text-align: center; }
        .items-table .col-notes { width: 40mm; }

        .signatures { margin-top: 40px; font-size: 11pt; }
        .signature-row { margin-bottom: 25px; line-height: 2; }
        .signature-line { border-bottom: 1px solid #000; width: 200px; display: inline-block; }

        .print-button {
            position: fixed; top: 20px; right: 20px; padding: 10px 20px;
            background: #007bff; color: white; border: none; border-radius: 5px;
            cursor: pointer; font-size: 14px; z-index: 1000;
        }
        .print-button:hover { background: #0056b3; }

        @media screen {
            body { background: #f8f9fa; padding: 40px; }
            .document-container { max-width: 210mm; margin: 0 auto; background: white; padding: 20mm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            .footer { display: none; }
        }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; padding-bottom: 25mm; }
            .document-container { max-width: 100%; padding: 0; margin: 0; }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Друкувати
    </button>

    <div class="document-container">
        <div class="approval">« ЗАТВЕРДЖУЮ »</div>
        <div class="director-signature">
            Директор КНП «ЦПМСД № 16» ОМР<br>
            {{ $director }}
        </div>

        <div class="title">АКТ СПИСАННЯ</div>
        <div class="subtitle">
            № {{ $writeoffRequest->request_number }} від {{ $writeoffRequest->writeoff_date->format('«d» ') }}{{ $writeoffRequest->writeoff_date->isoFormat('MMMM YYYY') }} року
        </div>

        @if($writeoffRequest->description)
        <div class="info-row">
            <strong>Підстава:</strong> {{ $writeoffRequest->description }}
        </div>
        <div class="spacer"></div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-number">№ з/п</th>
                    <th class="col-name">Найменування матеріальних цінностей</th>
                    <th class="col-unit">Одиниця виміру</th>
                    <th class="col-quantity">Кількість</th>
                    <th class="col-notes">Примітка</th>
                </tr>
            </thead>
            <tbody>
                @forelse($writeoffRequest->items as $index => $item)
                <tr>
                    <td class="col-number">{{ $index + 1 }}</td>
                    <td class="col-name">{{ $item->item_name }}</td>
                    <td class="col-unit">{{ $item->unit ?? 'шт' }}</td>
                    <td class="col-quantity">{{ $item->quantity }}</td>
                    <td class="col-notes">{{ $item->notes }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px;">Немає позицій</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="spacer"></div>

        @if($writeoffRequest->notes)
        <div class="info-row" style="margin-bottom: 20px;">
            <strong>Примітки:</strong> {{ $writeoffRequest->notes }}
        </div>
        @endif

        <div class="signatures">
            <div class="signature-row">
                Заст. директора з АГР ________________ {{ $deputyDirector }}
            </div>
            <div class="signature-row">
                Матеріально-відповідальна особа ________________ ____________________
            </div>
            <div class="signature-row">
                Члени комісії: ________________ ____________________
            </div>
        </div>
    </div>

    <script>
        // Українська локаль для дат
    </script>
</body>
</html>
