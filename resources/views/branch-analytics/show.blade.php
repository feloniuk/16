@extends('layouts.app')

@section('title', 'Аналітика філії: ' . $branch->name)

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-2">{{ $branch->name }}</h1>
            <small class="text-muted">Аналітика філії | Адреса: {{ $branch->address ?? 'не вказана' }}</small>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('branch-analytics.export', ['branch' => $branch, 'format' => 'pdf']) }}"
               class="btn btn-outline-danger btn-sm" title="Експорт у PDF">
                <i class="bi bi-file-pdf"></i> PDF
            </a>
            <a href="{{ route('branch-analytics.export', ['branch' => $branch, 'format' => 'excel']) }}"
               class="btn btn-outline-success btn-sm" title="Експорт у Excel">
                <i class="bi bi-file-excel"></i> Excel
            </a>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="stats-card p-4 mb-4">
    <h5 class="card-title mb-3">Фільтри</h5>
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <label for="date_from" class="form-label">Дата від</label>
            <input type="date" class="form-control" id="date_from" name="date_from"
                   value="{{ $dateFrom->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
            <label for="date_to" class="form-label">Дата до</label>
            <input type="date" class="form-control" id="date_to" name="date_to"
                   value="{{ $dateTo->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
            <label for="compare_period" class="form-label">Порівняння</label>
            <select class="form-select" id="compare_period" name="compare_period">
                <option value="none" {{ $comparePeriod === 'none' ? 'selected' : '' }}>Без порівняння</option>
                <option value="previous" {{ $comparePeriod === 'previous' ? 'selected' : '' }}>Попередній період</option>
                <option value="year_ago" {{ $comparePeriod === 'year_ago' ? 'selected' : '' }}>Той самий період минулого року</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel"></i> Застосувати
            </button>
        </div>
    </form>
</div>

<!-- Main Metrics -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Всього заявок</h6>
                    <h3 class="mb-0">{{ $metrics['total_repairs'] }}</h3>
                    @if($changes)
                        <small class="{{ $changes['repairs_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="bi bi-arrow-{{ $changes['repairs_change'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ abs($changes['repairs_change']) }}%
                        </small>
                    @endif
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded">
                    <i class="bi bi-tools text-primary fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">SLA Дотримання</h6>
                    <h3 class="mb-0">{{ $metrics['sla_compliance'] }}%</h3>
                    @if($changes)
                        <small class="{{ $changes['sla_compliance_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="bi bi-arrow-{{ $changes['sla_compliance_change'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ abs($changes['sla_compliance_change']) }}%
                        </small>
                    @endif
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Коефіцієнт завершеності</h6>
                    <h3 class="mb-0">{{ $metrics['completion_rate'] }}%</h3>
                    @if($changes)
                        <small class="{{ $changes['completion_rate_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="bi bi-arrow-{{ $changes['completion_rate_change'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ abs($changes['completion_rate_change']) }}%
                        </small>
                    @endif
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded">
                    <i class="bi bi-percent text-info fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Середній час відгуку</h6>
                    <h3 class="mb-0">{{ $metrics['avg_response_days'] }}д</h3>
                    <small class="text-muted">{{ $metrics['avg_response_hours'] }}ч</small>
                    @if($changes)
                        <div class="mt-1">
                            <small class="{{ $changes['response_time_change'] <= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="bi bi-arrow-{{ $changes['response_time_change'] <= 0 ? 'down' : 'up' }}"></i>
                                {{ abs($changes['response_time_change']) }}ч
                            </small>
                        </div>
                    @endif
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <i class="bi bi-clock text-warning fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Metrics -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">Завершено</h6>
                <h3 class="mb-0">{{ $metrics['completed_repairs'] }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">В роботі</h6>
                <h3 class="mb-0">{{ $metrics['active_repairs'] }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">Активні (%)</h6>
                <h3 class="mb-0">{{ $metrics['active_rate'] }}%</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">Картриджів замінено</h6>
                <h3 class="mb-0">{{ $metrics['total_cartridges'] }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Динаміка заявок</h5>
            <canvas id="dailyChart" height="100"></canvas>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <h5 class="card-title mb-3">Розподіл по статусам</h5>
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Топ-10 кабінетів</h5>
            @if($topRooms->isEmpty())
                <p class="text-muted">Немає даних</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Кабінет</th>
                                <th class="text-end">Заявок</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topRooms as $room => $count)
                                <tr>
                                    <td>{{ $room }}</td>
                                    <td class="text-end"><span class="badge bg-primary">{{ $count }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Останні заявки</h5>
            @if($recentRepairs->isEmpty())
                <p class="text-muted">Немає даних</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Кабінет</th>
                                <th>Статус</th>
                                <th class="text-end">Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentRepairs as $repair)
                                <tr>
                                    <td>{{ $repair->room_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $repair->status === 'виконана' ? 'success' : ($repair->status === 'в_роботі' ? 'info' : 'warning') }}">
                                            {{ $repair->status }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $repair->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Daily Repairs Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
const dailyChart = new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyRepairs->keys()->map(fn($date) => \Carbon\Carbon::parse($date)->format('d.m'))) !!},
        datasets: [{
            label: 'Заявки на ремонт',
            data: {!! json_encode($dailyRepairs->values()) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
            legend: {
                display: false
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

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Нова', 'В роботі', 'Виконана'],
        datasets: [{
            data: [
                {{ $statusDistribution['нова'] ?? 0 }},
                {{ $statusDistribution['в_роботі'] ?? 0 }},
                {{ $statusDistribution['виконана'] ?? 0 }}
            ],
            backgroundColor: [
                'rgb(245, 158, 11)',
                'rgb(59, 130, 246)',
                'rgb(34, 197, 94)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush
