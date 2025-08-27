@extends('layouts.app')

@section('title', 'KPI и Аналитика')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>KPI и Аналитика</h2>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    Период: 
                    @switch($period)
                        @case('quarter') Квартал @break
                        @case('year') Год @break
                        @default Месяц
                    @endswitch
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('kpi.index', ['period' => 'month']) }}">Месяц</a></li>
                    <li><a class="dropdown-item" href="{{ route('kpi.index', ['period' => 'quarter']) }}">Квартал</a></li>
                    <li><a class="dropdown-item" href="{{ route('kpi.index', ['period' => 'year']) }}">Год</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Основные KPI карточки -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stats-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Эффективность ремонтов</h6>
                    <h3 class="mb-1 text-primary">{{ $kpis['repair_efficiency']['completion_rate'] }}%</h3>
                    <small class="text-muted">
                        Среднее время: {{ $kpis['repair_efficiency']['avg_resolution_time'] }} дн.
                    </small>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded">
                    <i class="bi bi-speedometer2 text-primary fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Оборачиваемость инвентаря</h6>
                    <h3 class="mb-1 text-success">{{ $kpis['inventory_turnover']['turnover_rate'] }}%</h3>
                    <small class="text-muted">
                        Перемещено: {{ $kpis['inventory_turnover']['moved_items'] }} ед.
                    </small>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <i class="bi bi-arrow-repeat text-success fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Оптимизация затрат</h6>
                    <h3 class="mb-1 {{ $kpis['cost_optimization']['cost_change'] > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $kpis['cost_optimization']['cost_change'] > 0 ? '+' : '' }}{{ $kpis['cost_optimization']['cost_change'] }}%
                    </h3>
                    <small class="text-muted">
                        Текущий период: {{ number_format($kpis['cost_optimization']['current_costs'], 0) }} грн
                    </small>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <i class="bi bi-cash-coin text-warning fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-2">Качество аудитов</h6>
                    <h3 class="mb-1 text-info">{{ $kpis['audit_compliance']['avg_accuracy'] }}%</h3>
                    <small class="text-muted">
                        Завершено: {{ $kpis['audit_compliance']['completed_audits'] }} из {{ $kpis['audit_compliance']['total_audits'] }}
                    </small>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded">
                    <i class="bi bi-clipboard-check text-info fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Графики и тренды -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Динамика объема заявок</h5>
            <canvas id="repairVolumeChart" height="100"></canvas>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Производительность подрядчиков</h5>
            <canvas id="contractorPerformanceChart"></canvas>
            
            <div class="mt-3">
                <h6 class="mb-2">Средняя производительность: {{ $kpis['contractor_performance']['avg_performance'] }}%</h6>
                <div class="list-group list-group-flush">
                    @foreach($kpis['contractor_performance']['top_performers'] as $contractor)
                    <div class="list-group-item px-0 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="fw-medium">{{ $contractor->name }}</small>
                                <div class="text-muted small">{{ $contractor->operations_count }} операций</div>
                            </div>
                            <span class="badge bg-success">{{ number_format($contractor->completion_rate * 100, 1) }}%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Детальная аналитика -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Тренд затрат</h5>
            <canvas id="costTrendChart" height="80"></canvas>
            
            <div class="row mt-3">
                <div class="col-6">
                    <div class="text-center p-3 bg-light rounded">
                        <h6 class="text-primary mb-1">{{ number_format($kpis['cost_optimization']['current_costs'], 0) }}</h6>
                        <small class="text-muted">Текущий период</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-3 bg-light rounded">
                        <h6 class="text-muted mb-1">{{ number_format($kpis['cost_optimization']['previous_costs'], 0) }}</h6>
                        <small class="text-muted">Предыдущий период</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Эффективность по времени</h5>
            <canvas id="efficiencyTrendChart" height="80"></canvas>
            
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Утилизация оборудования:</span>
                    <span class="badge bg-primary">{{ $kpis['equipment_utilization']['avg_utilization'] }}%</span>
                </div>
                
                <div class="progress mb-2">
                    <div class="progress-bar" style="width: {{ $kpis['equipment_utilization']['avg_utilization'] }}%"></div>
                </div>
                
                <small class="text-muted">
                    Всего единиц: {{ $kpis['equipment_utilization']['total_equipment'] }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Экспорт данных -->
<div class="row mt-4">
    <div class="col">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Экспорт отчетов</h5>
            
            <div class="row g-3">
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-success w-100" onclick="exportKPI('excel')">
                        <i class="bi bi-file-earmark-excel"></i> Excel отчет
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-danger w-100" onclick="exportKPI('pdf')">
                        <i class="bi bi-file-earmark-pdf"></i> PDF отчет  
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="downloadCharts()">
                        <i class="bi bi-graph-up"></i> Скачать графики
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#customReportModal">
                        <i class="bi bi-gear"></i> Настроить отчет
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для настройки отчета -->
<div class="modal fade" id="customReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Настроить пользовательский отчет</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="customReportForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="report_period" class="form-label">Период</label>
                            <select class="form-select" id="report_period" name="period">
                                <option value="last_month">Последний месяц</option>
                                <option value="last_quarter">Последний квартал</option>
                                <option value="last_year">Последний год</option>
                                <option value="custom">Произвольный период</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="report_format" class="form-label">Формат</label>
                            <select class="form-select" id="report_format" name="format">
                                <option value="excel">Excel</option>
                                <option value="pdf">PDF</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                        
                        <div class="col-12" id="custom_period" style="display: none;">
                            <div class="row">
                                <div class="col-6">
                                    <label for="date_from" class="form-label">Дата от</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from">
                                </div>
                                <div class="col-6">
                                    <label for="date_to" class="form-label">Дата до</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Включить в отчет:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_contractors" name="include[]" value="contractors">
                                        <label class="form-check-label" for="include_contractors">Подрядчики</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_costs" name="include[]" value="costs">
                                        <label class="form-check-label" for="include_costs">Финансовые данные</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_charts" name="include[]" value="charts">
                                        <label class="form-check-label" for="include_charts">Графики и диаграммы</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label for="report_title" class="form-label">Заголовок отчета</label>
                            <input type="text" class="form-control" id="report_title" name="title" 
                                   value="KPI отчет - {{ date('d.m.Y') }}" placeholder="Введите заголовок отчета">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сгенерировать отчет</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Данные для графиков
const repairVolumeData = @json($trends['repair_volume']);
const costTrendData = @json($trends['cost_trend']);
const efficiencyTrendData = @json($trends['efficiency_trend']);

// График объема заявок
const repairVolumeCtx = document.getElementById('repairVolumeChart').getContext('2d');
const repairVolumeChart = new Chart(repairVolumeCtx, {
    type: 'line',
    data: {
        labels: repairVolumeData.map(item => item.date),
        datasets: [{
            label: 'Количество заявок',
            data: repairVolumeData.map(item => item.count),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// График производительности подрядчиков
const contractorPerformanceCtx = document.getElementById('contractorPerformanceChart').getContext('2d');
const contractorPerformanceChart = new Chart(contractorPerformanceCtx, {
    type: 'doughnut',
    data: {
        labels: ['Высокая', 'Средняя', 'Низкая'],
        datasets: [{
            data: [70, 20, 10], // Примерные данные
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(245, 158, 11)',
                'rgb(239, 68, 68)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// График тренда затрат
const costTrendCtx = document.getElementById('costTrendChart').getContext('2d');
const costTrendChart = new Chart(costTrendCtx, {
    type: 'bar',
    data: {
        labels: costTrendData.map(item => item.date),
        datasets: [{
            label: 'Затраты, грн',
            data: costTrendData.map(item => item.total_cost),
            backgroundColor: 'rgba(245, 158, 11, 0.8)',
            borderColor: 'rgb(245, 158, 11)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// График эффективности
const efficiencyTrendCtx = document.getElementById('efficiencyTrendChart').getContext('2d');
const efficiencyTrendChart = new Chart(efficiencyTrendCtx, {
    type: 'line',
    data: {
        labels: efficiencyTrendData.map(item => item.date),
        datasets: [{
            label: 'Среднее время (дни)',
            data: efficiencyTrendData.map(item => item.avg_time),
            borderColor: 'rgb(168, 85, 247)',
            backgroundColor: 'rgba(168, 85, 247, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Функции экспорта
function exportKPI(format) {
    const period = '{{ $period }}';
    const url = `{{ route('kpi.export') }}?format=${format}&period=${period}`;
    window.open(url, '_blank');
}

function downloadCharts() {
    // Создаем Canvas для комбинирования графиков
    const canvas = document.createElement('canvas');
    canvas.width = 1200;
    canvas.height = 800;
    const ctx = canvas.getContext('2d');
    
    // Белый фон
    ctx.fillStyle = 'white';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    // Заголовок
    ctx.fillStyle = 'black';
    ctx.font = '24px Arial';
    ctx.fillText('KPI Dashboard - {{ date("d.m.Y") }}', 50, 40);
    
    // Экспортируем каждый график
    const charts = [repairVolumeChart, contractorPerformanceChart, costTrendChart, efficiencyTrendChart];
    const positions = [
        {x: 50, y: 80, w: 500, h: 300},
        {x: 650, y: 80, w: 300, h: 300},
        {x: 50, y: 450, w: 500, h: 300},
        {x: 650, y: 450, w: 300, h: 300}
    ];
    
    charts.forEach((chart, index) => {
        const pos = positions[index];
        const chartCanvas = chart.canvas;
        ctx.drawImage(chartCanvas, pos.x, pos.y, pos.w, pos.h);
    });
    
    // Скачиваем
    const link = document.createElement('a');
    link.download = `KPI_Charts_{{ date('Y-m-d') }}.png`;
    link.href = canvas.toDataURL();
    link.click();
}

// Обработка формы пользовательского отчета
document.getElementById('report_period').addEventListener('change', function() {
    const customPeriod = document.getElementById('custom_period');
    if (this.value === 'custom') {
        customPeriod.style.display = 'block';
    } else {
        customPeriod.style.display = 'none';
    }
});

document.getElementById('customReportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams();
    
    for (let [key, value] of formData.entries()) {
        params.append(key, value);
    }
    
    const url = `{{ route('kpi.custom-export') }}?${params.toString()}`;
    window.open(url, '_blank');
    
    // Закрываем модальное окно
    const modal = bootstrap.Modal.getInstance(document.getElementById('customReportModal'));
    modal.hide();
});
</script>
@endpush