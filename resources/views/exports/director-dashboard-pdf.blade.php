<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Дашборд директора</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4472C4;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            color: #4472C4;
            font-size: 28px;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #999;
            font-size: 12px;
        }

        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #4472C4;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th {
            background-color: #E7F0F7;
            border: 1px solid #CCCCCC;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }

        table td {
            border: 1px solid #CCCCCC;
            padding: 10px;
            font-size: 12px;
        }

        table tr:nth-child(even) {
            background-color: #F9F9F9;
        }

        .metric-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .metric-cell {
            display: table-cell;
            width: 48%;
            border: 1px solid #CCCCCC;
            padding: 15px;
            vertical-align: top;
        }

        .metric-label {
            font-weight: bold;
            color: #4472C4;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .metric-sub {
            font-size: 11px;
            color: #999;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #CCCCCC;
            font-size: 10px;
            color: #999;
        }

        .page-break {
            page-break-after: always;
        }

        .stat-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            border: 1px solid #CCCCCC;
            background-color: #F9F9F9;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #4472C4;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Заголовок -->
    <div class="header">
        <h1>Дашборд директора</h1>
        <p>Звіт був згенерований: {{ date('d.m.Y H:i') }}</p>
    </div>

    <!-- SECTION 1: Загальна статистика -->
    <div class="section">
        <div class="section-title">Загальна статистика</div>

        <div class="stat-grid">
            <div class="stat-item">
                <div class="stat-number">{{ $totalStats['branches'] }}</div>
                <div class="stat-label">Філіалів</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $totalStats['total_repairs'] }}</div>
                <div class="stat-label">Всього заявок</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $totalStats['total_cartridges'] }}</div>
                <div class="stat-label">Картриджів</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $totalStats['total_inventory'] }}</div>
                <div class="stat-label">Інвентарю</div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: SLA Метрики -->
    <div class="section">
        <div class="section-title">SLA Метрики (Цільовий час: 72 години)</div>

        <table>
            <tr>
                <th>Показник</th>
                <th>Значення</th>
            </tr>
            <tr>
                <td>SLA Дотримання</td>
                <td><strong>{{ $slaMetrics['sla_compliance'] }}%</strong> ({{ $slaMetrics['within_sla_count'] }} з {{ $slaMetrics['total_completed'] }} завершено)</td>
            </tr>
            <tr>
                <td>Середній час відгуку (годин)</td>
                <td><strong>{{ $slaMetrics['avg_response_hours'] }}</strong> годин</td>
            </tr>
            <tr>
                <td>Середній час відгуку (днів)</td>
                <td><strong>{{ $slaMetrics['avg_response_days'] }}</strong> днів</td>
            </tr>
            <tr>
                <td>Завершено в межах SLA</td>
                <td><strong>{{ $slaMetrics['within_sla_count'] }}</strong> заявок</td>
            </tr>
            <tr>
                <td>Всього завершено</td>
                <td><strong>{{ $slaMetrics['total_completed'] }}</strong> заявок</td>
            </tr>
        </table>
    </div>

    <!-- SECTION 3: Якість обслуговування -->
    <div class="section">
        <div class="section-title">Якість обслуговування</div>

        <table>
            <tr>
                <th>Показник</th>
                <th>Значення</th>
            </tr>
            <tr>
                <td>Коефіцієнт завершеності</td>
                <td><strong>{{ $qualityMetrics['completion_rate'] }}%</strong> ({{ $qualityMetrics['completed_repairs'] }} завершено)</td>
            </tr>
            <tr>
                <td>Коефіцієнт активних заявок</td>
                <td><strong>{{ $qualityMetrics['active_rate'] }}%</strong> ({{ $qualityMetrics['active_repairs'] }} в роботі)</td>
            </tr>
            <tr>
                <td>Середнє заявок на філію</td>
                <td><strong>{{ $qualityMetrics['avg_repairs_per_branch'] }}</strong> заявок/філію</td>
            </tr>
            <tr>
                <td>Ефективність картриджів</td>
                <td><strong>{{ $qualityMetrics['cartridge_efficiency'] }}</strong> картриджів/заявку</td>
            </tr>
            <tr>
                <td>Всього в системі</td>
                <td>{{ $qualityMetrics['total_repairs'] }} заявок</td>
            </tr>
        </table>
    </div>

    <!-- SECTION 4: Топ філіали -->
    <div class="section">
        <div class="section-title">Топ-5 філіалів по активності</div>

        <table>
            <tr>
                <th>#</th>
                <th>Філія</th>
                <th>Заявок</th>
                <th>Картриджів</th>
            </tr>
            @forelse($topBranches as $index => $branch)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $branch->name }}</td>
                <td>{{ $branch->repair_requests_count }}</td>
                <td>{{ $branch->cartridge_replacements_count }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Немає даних</td>
            </tr>
            @endforelse
        </table>
    </div>

    <!-- Футер -->
    <div class="footer">
        <p>Цей звіт був автоматично згенерований системою управління.</p>
        <p>Конфіденційно | {{ date('d.m.Y H:i:s') }}</p>
    </div>
</body>
</html>
