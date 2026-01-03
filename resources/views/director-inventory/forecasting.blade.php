@extends('layouts.app')

@section('title', 'Прогнозування витрат')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3">Прогнозування витрат на основі історичних даних</h1>
    </div>
</div>

<!-- Вибір періоду -->
<div class="stats-card p-4 mb-4">
    <h5 class="card-title mb-3">Період аналізу</h5>
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Аналізувати минулих:</label>
            <div class="btn-group w-100" role="group">
                <a href="{{ route('director-inventory.forecasting', ['period' => 1]) }}"
                   class="btn btn-outline-primary {{ $period == 1 ? 'active' : '' }}">
                    1 місяць
                </a>
                <a href="{{ route('director-inventory.forecasting', ['period' => 3]) }}"
                   class="btn btn-outline-primary {{ $period == 3 ? 'active' : '' }}">
                    3 місяці
                </a>
                <a href="{{ route('director-inventory.forecasting', ['period' => 6]) }}"
                   class="btn btn-outline-primary {{ $period == 6 ? 'active' : '' }}">
                    6 місяців
                </a>
                <a href="{{ route('director-inventory.forecasting', ['period' => 12]) }}"
                   class="btn btn-outline-primary {{ $period == 12 ? 'active' : '' }}">
                    1 рік
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <small class="text-muted">
                <i class="bi bi-info-circle"></i>
                Період: {{ $dateFrom->format('d.m.Y') }} — {{ $dateTo->format('d.m.Y') }}
            </small>
        </div>
    </form>
</div>

<!-- Ключові показники -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Всього замін за період</h6>
                    <h3 class="mb-0">{{ $stats['total_cartridges'] }}</h3>
                    <small class="text-muted">картриджів</small>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded">
                    <i class="bi bi-printer text-primary fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Середня на місяц</h6>
                    <h3 class="mb-0">{{ $stats['avg_monthly_cartridges'] }}</h3>
                    <small class="text-muted">картриджів/місяц</small>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded">
                    <i class="bi bi-graph-up text-info fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Тренд</h6>
                    <h3 class="mb-0 {{ $forecast['trend'] >= 0 ? 'text-danger' : 'text-success' }}">
                        {{ $forecast['trend'] > 0 ? '+' : '' }}{{ $forecast['trend'] }}
                    </h3>
                    <small class="text-muted">
                        {{ $forecast['trend'] > 0 ? 'зростання' : ($forecast['trend'] < 0 ? 'зниження' : 'стабільно') }}
                    </small>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <i class="bi bi-arrow-{{ $forecast['trend'] > 0 ? 'up' : 'down' }} text-warning fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Прогноз на місяц</h6>
                    <h3 class="mb-0">{{ $forecast['projection'] }}</h3>
                    <small class="text-muted">очікуваних замін</small>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <i class="bi bi-eye text-success fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Графік історії та прогнозу -->
<div class="row g-4 mb-4">
    <div class="col-lg-12">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Історія витрат та прогноз на майбутнє</h5>
            <canvas id="forecastChart" height="80"></canvas>
        </div>
    </div>
</div>

<!-- Детальна інформація -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Статистика інвентарю</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Всього товарів на складі</strong></td>
                        <td class="text-end"><span class="badge bg-primary">{{ $stats['warehouse_items_count'] }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Одиниць обладнання в кабінетах</strong></td>
                        <td class="text-end"><span class="badge bg-info">{{ $stats['equipment_items_count'] }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Загальна вартість</strong></td>
                        <td class="text-end"><strong>{{ number_format($stats['total_inventory_value'], 2, ',', ' ') }} ₴</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Висновки та рекомендації</h5>
            <div class="alert alert-info mb-3">
                <i class="bi bi-lightbulb"></i> <strong>Аналіз тренду:</strong>
                <br>
                @if($forecast['trend'] > 0)
                    Витрати картриджів мають тенденцію до <strong>зростання</strong>.
                    Розглядайте збільшення обсягів закупівлі на {{ abs(round($forecast['trend'])) }} {{ abs($forecast['trend']) > 1 ? 'картриджів' : 'картриджа' }} в місяц.
                @elseif($forecast['trend'] < 0)
                    Витрати картриджів мають тенденцію до <strong>зниження</strong>.
                    Можливо оптимізувати запаси та зменшити закупівлі.
                @else
                    Витрати картриджів <strong>стабільні</strong>. Витрати залишаються однаковими з місяця в місяц.
                @endif
            </div>

            <div class="alert alert-{{ $stats['warehouse_items_count'] < 50 ? 'warning' : 'success' }}">
                <i class="bi bi-exclamation-triangle"></i> <strong>Статус складу:</strong>
                <br>
                @if($stats['warehouse_items_count'] < 50)
                    На складі мало товарів. Рекомендується поповнення запасів.
                @elseif($stats['warehouse_items_count'] < 100)
                    На складі помірна кількість товарів. Стежте за витратами.
                @else
                    На складі достатня кількість товарів для нормальної роботи.
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Перевіряємо чи є дані для графіка
const chartElement = document.getElementById('forecastChart');
if (chartElement) {
    const forecastCtx = chartElement.getContext('2d');

    const historicalLabels = {!! json_encode($chartLabels ?? []) !!} || [];
    const historicalData = {!! json_encode($chartData ?? []) !!} || [];
    const forecastedLabels = {!! json_encode($forecastedMonths ?? []) !!} || [];
    const forecastedData = {!! json_encode($forecastedData ?? []) !!} || [];

    // Об'єднуємо дані
    const allLabels = [...historicalLabels, ...forecastedLabels];
    const allHistoricalData = [...historicalData, ...Array(forecastedData.length).fill(null)];
    const allForecastedData = [...Array(historicalData.length).fill(null), ...forecastedData];

    const forecastChart = new Chart(forecastCtx, {
        type: 'line',
        data: {
            labels: allLabels && allLabels.length > 0 ? allLabels : ['Немає даних'],
            datasets: [
                {
                    label: 'Історія витрат',
                    data: allHistoricalData && allHistoricalData.length > 0 ? allHistoricalData : [0],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    borderWidth: 3
                },
                {
                    label: 'Прогноз',
                    data: allForecastedData && allForecastedData.length > 0 ? allForecastedData : [0],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    borderWidth: 3,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2.5,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                filler: {
                    propagate: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}
</script>
@endpush
