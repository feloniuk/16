<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 100%;
            padding: 20px;
        }

        .header {
            border-bottom: 3px solid #4472C4;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            color: #4472C4;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #4472C4;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        table.metrics-table {
            font-size: 12px;
        }

        table.metrics-table td {
            padding: 10px;
        }

        table.metrics-table tr:first-child {
            background-color: #E8EFFF;
            font-weight: bold;
        }

        table.metrics-table tr:nth-child(even) {
            background-color: #F5F5F5;
        }

        th {
            background-color: #4472C4;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .metric-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .metric-label {
            font-weight: bold;
            color: #333;
        }

        .metric-value {
            text-align: right;
            color: #4472C4;
            font-weight: bold;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        .metric-card {
            background-color: #f5f5f5;
            padding: 12px;
            border-left: 4px solid #4472C4;
        }

        .metric-card-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .metric-card-value {
            font-size: 18px;
            font-weight: bold;
            color: #4472C4;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-right: 5px;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>Аналітика філії: {{ $branch->name }}</h1>
        <div class="header-info">
            <div>
                <strong>Період:</strong> {{ $dateFrom->format('d.m.Y') }} — {{ $dateTo->format('d.m.Y') }}
            </div>
            <div>
                <strong>Дата звіту:</strong> {{ date('d.m.Y H:i') }}
            </div>
        </div>
    </div>

    <!-- Section: Key Metrics -->
    <div class="section">
        <div class="section-title">Ключові показники</div>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-card-label">Всього заявок</div>
                <div class="metric-card-value">{{ $metrics['total_repairs'] }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-card-label">Завершено</div>
                <div class="metric-card-value">{{ $metrics['completed_repairs'] }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-card-label">SLA Дотримання</div>
                <div class="metric-card-value">{{ $metrics['sla_compliance'] }}%</div>
            </div>
            <div class="metric-card">
                <div class="metric-card-label">Коефіцієнт завершеності</div>
                <div class="metric-card-value">{{ $metrics['completion_rate'] }}%</div>
            </div>
        </div>

        <table class="metrics-table">
            <tr>
                <td class="metric-label">Середній час відгуку (години)</td>
                <td class="metric-value">{{ $metrics['avg_response_hours'] }}</td>
            </tr>
            <tr>
                <td class="metric-label">Середній час відгуку (дні)</td>
                <td class="metric-value">{{ $metrics['avg_response_days'] }}</td>
            </tr>
            <tr>
                <td class="metric-label">В роботі</td>
                <td class="metric-value">{{ $metrics['active_repairs'] }} ({{ $metrics['active_rate'] }}%)</td>
            </tr>
            <tr>
                <td class="metric-label">Картриджів замінено</td>
                <td class="metric-value">{{ $metrics['total_cartridges'] }}</td>
            </tr>
            <tr>
                <td class="metric-label">Ефективність картриджів</td>
                <td class="metric-value">{{ $metrics['cartridge_efficiency'] }}</td>
            </tr>
        </table>
    </div>

    <!-- Section: Recent Repairs -->
    <div class="section">
        <div class="section-title">Останні заявки (топ-10)</div>
        @if($repairs->isEmpty())
            <p style="color: #999; font-size: 12px;">Немає даних</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="width: 15%;">Кабінет</th>
                        <th style="width: 40%;">Опис</th>
                        <th style="width: 20%;">Статус</th>
                        <th style="width: 15%; text-align: right;">Дата</th>
                    </tr>
                </thead>
                <tbody>
                    @php $count = 1; @endphp
                    @foreach($repairs->take(10) as $repair)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>{{ $repair->room_number }}</td>
                            <td>{{ mb_substr($repair->description ?? '', 0, 50) }}...</td>
                            <td>
                                @if($repair->status === 'виконана')
                                    <span class="badge badge-success">{{ $repair->status }}</span>
                                @elseif($repair->status === 'в_роботі')
                                    <span class="badge badge-info">{{ $repair->status }}</span>
                                @else
                                    <span class="badge badge-warning">{{ $repair->status }}</span>
                                @endif
                            </td>
                            <td style="text-align: right;">{{ $repair->created_at->format('d.m.Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Section: Cartridge Replacements -->
    <div class="section">
        <div class="section-title">Заміни картриджів</div>
        @if($cartridges->isEmpty())
            <p style="color: #999; font-size: 12px;">Немає даних</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="width: 30%;">Тип картриджу</th>
                        <th style="width: 30%;">Кабінет</th>
                        <th style="width: 20%;">Кількість</th>
                        <th style="width: 20%; text-align: right;">Дата</th>
                    </tr>
                </thead>
                <tbody>
                    @php $count = 1; @endphp
                    @foreach($cartridges->take(20) as $cartridge)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>{{ $cartridge->cartridge_type ?? 'не вказано' }}</td>
                            <td>{{ $cartridge->room_number ?? '-' }}</td>
                            <td>{{ $cartridge->quantity ?? 1 }}</td>
                            <td style="text-align: right;">{{ $cartridge->replacement_date->format('d.m.Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Звіт був автоматично згенерований системою управління ремонтами</p>
        <p>{{ $branch->name }} | Період: {{ $dateFrom->format('d.m.Y') }} — {{ $dateTo->format('d.m.Y') }}</p>
    </div>
</div>
</body>
</html>
