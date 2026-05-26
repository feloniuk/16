@extends('layouts.app')

@section('title', 'Прогнозування витрат')

@section('content')
<div class="mb-4">
    <h1 class="h3">Прогнозування витрат</h1>
</div>

<!-- Вибір періоду -->
<div class="stats-card p-4 mb-4">
    <h5 class="card-title mb-2">Період аналізу</h5>
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="btn-group" role="group">
            <a href="{{ route('director-inventory.forecasting', ['period' => 1]) }}"
               class="btn btn-outline-primary {{ $period == 1 ? 'active' : '' }}">1 місяць</a>
            <a href="{{ route('director-inventory.forecasting', ['period' => 3]) }}"
               class="btn btn-outline-primary {{ $period == 3 ? 'active' : '' }}">3 місяці</a>
            <a href="{{ route('director-inventory.forecasting', ['period' => 6]) }}"
               class="btn btn-outline-primary {{ $period == 6 ? 'active' : '' }}">6 місяців</a>
            <a href="{{ route('director-inventory.forecasting', ['period' => 12]) }}"
               class="btn btn-outline-primary {{ $period == 12 ? 'active' : '' }}">1 рік</a>
        </div>
        <small class="text-muted">
            <i class="bi bi-calendar3"></i>
            {{ $dateFrom->format('d.m.Y') }} — {{ $dateTo->format('d.m.Y') }}
        </small>
    </div>
</div>

<!-- Ключові показники -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stats-card p-3 h-100">
            <h6 class="text-muted mb-1 small">Замін картриджів</h6>
            <h3 class="mb-0">{{ $stats['total_cartridges'] }}</h3>
            <small class="text-muted">за {{ $months }} {{ $months == 1 ? 'місяць' : 'місяців' }}</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stats-card p-3 h-100">
            <h6 class="text-muted mb-1 small">Середня/місяць</h6>
            <h3 class="mb-0">{{ $stats['avg_monthly_cartridges'] }}</h3>
            <small class="text-muted">
                @if($stats['cartridge_trend'] > 0)
                    <span class="text-danger"><i class="bi bi-arrow-up-short"></i>+{{ $stats['cartridge_trend'] }} тренд</span>
                @elseif($stats['cartridge_trend'] < 0)
                    <span class="text-success"><i class="bi bi-arrow-down-short"></i>{{ $stats['cartridge_trend'] }} тренд</span>
                @else
                    стабільно
                @endif
            </small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stats-card p-3 h-100 {{ $stats['critical_items'] > 0 ? 'border border-danger' : '' }}">
            <h6 class="text-muted mb-1 small">Критичних позицій</h6>
            <h3 class="mb-0 {{ $stats['critical_items'] > 0 ? 'text-danger' : 'text-success' }}">
                {{ $stats['critical_items'] }}
            </h3>
            <small class="text-muted">залишок ≤14 днів або 0</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stats-card p-3 h-100 {{ $stats['warning_items'] > 0 ? 'border border-warning' : '' }}">
            <h6 class="text-muted mb-1 small">Попереджень</h6>
            <h3 class="mb-0 {{ $stats['warning_items'] > 0 ? 'text-warning' : 'text-success' }}">
                {{ $stats['warning_items'] }}
            </h3>
            <small class="text-muted">залишок 15–30 днів</small>
        </div>
    </div>
</div>

<!-- Графіки -->
<div class="row g-4 mb-4">
    <!-- Картриджі: історія + прогноз -->
    <div class="col-lg-6">
        <div class="stats-card p-4 h-100">
            <h5 class="card-title mb-1">Динаміка замін картриджів</h5>
            <small class="text-muted d-block mb-3">Прогноз на 6 місяців вперед</small>
            <canvas id="cartridgeChart" height="140"></canvas>
        </div>
    </div>

    <!-- Динаміка видачі по категоріях -->
    <div class="col-lg-6">
        <div class="stats-card p-4 h-100">
            <h5 class="card-title mb-1">Динаміка видачі зі складу</h5>
            <small class="text-muted d-block mb-3">За категоріями товарів</small>
            <canvas id="categoryChart" height="140"></canvas>
        </div>
    </div>
</div>

<!-- Прогноз витратних матеріалів -->
<div class="stats-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title mb-0">Прогноз витратних матеріалів</h5>
        <small class="text-muted">Розраховано на основі {{ $months }} {{ $months == 1 ? 'місяця' : 'місяців' }} витрат</small>
    </div>

    @if($consumablesForecast->isEmpty())
        <div class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            Немає даних про витрати за вибраний період
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Найменування</th>
                        <th>Категорія</th>
                        <th class="text-center">Залишок</th>
                        <th class="text-center">Видано за<br>{{ $months }} міс.</th>
                        <th class="text-center">Сер./місяць</th>
                        <th class="text-center">Залишилось</th>
                        <th class="text-center">Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consumablesForecast->sortByDesc(fn($i) => match($i->urgency) { 'depleted' => 4, 'critical' => 3, 'warning' => 2, default => 1 }) as $item)
                    <tr class="{{ in_array($item->urgency, ['depleted','critical']) ? 'table-danger' : ($item->urgency === 'warning' ? 'table-warning' : '') }}">
                        <td>
                            <strong>{{ $item->full_name ?: $item->equipment_type }}</strong>
                            @if($item->full_name && $item->equipment_type !== $item->full_name)
                                <br><small class="text-muted">{{ $item->equipment_type }}</small>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                        <td class="text-center">
                            <strong class="{{ $item->current_stock <= 0 ? 'text-danger' : '' }}">
                                {{ $item->current_stock }}
                            </strong> {{ $item->unit }}
                        </td>
                        <td class="text-center">{{ $item->total_issued }} {{ $item->unit }}</td>
                        <td class="text-center">{{ $item->avg_monthly }} {{ $item->unit }}</td>
                        <td class="text-center">
                            @if($item->days_left === null)
                                <span class="text-muted">—</span>
                            @elseif($item->days_left <= 0)
                                <span class="text-danger fw-bold">вичерпано</span>
                            @else
                                <strong>{{ $item->days_left }}</strong> дн.
                                @if($item->months_forecast !== null)
                                    <br><small class="text-muted">({{ $item->months_forecast }} міс.)</small>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            @switch($item->urgency)
                                @case('depleted')
                                    <span class="badge bg-danger">Вичерпано</span>
                                    @break
                                @case('critical')
                                    <span class="badge bg-danger">Критично</span>
                                    @break
                                @case('warning')
                                    <span class="badge bg-warning text-dark">Увага</span>
                                    @break
                                @default
                                    <span class="badge bg-success">OK</span>
                            @endswitch
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Типи картриджів та топ видачі -->
<div class="row g-4 mb-4">
    <!-- Картриджі по типу -->
    <div class="col-md-5">
        <div class="stats-card p-4 h-100">
            <h5 class="card-title mb-3">Картриджі за типом</h5>
            @if($cartridgeByType->isEmpty())
                <div class="text-center text-muted py-3">Немає даних</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Тип картриджа</th>
                                <th class="text-center">Замін</th>
                                <th class="text-end">Частка</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $cartridgeTotal = $cartridgeByType->sum('count'); @endphp
                            @foreach($cartridgeByType as $type)
                            <tr>
                                <td>{{ $type->cartridge_type ?: '(не вказано)' }}</td>
                                <td class="text-center"><strong>{{ $type->count }}</strong></td>
                                <td class="text-end">
                                    @if($cartridgeTotal > 0)
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width:60px; height:6px;">
                                                <div class="progress-bar bg-primary" style="width:{{ round($type->count/$cartridgeTotal*100) }}%"></div>
                                            </div>
                                            <small>{{ round($type->count/$cartridgeTotal*100) }}%</small>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Топ-10 видача -->
    <div class="col-md-7">
        <div class="stats-card p-4 h-100">
            <h5 class="card-title mb-3">Топ-10 позицій за видачею</h5>
            @if($topIssuedItems->isEmpty())
                <div class="text-center text-muted py-3">Немає даних</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Назва</th>
                                <th class="text-center">Видано</th>
                                <th class="text-center">Сер./міс.</th>
                                <th class="text-center">Залишилось</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topIssuedItems as $i => $item)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <strong>{{ $item->full_name ?: $item->equipment_type }}</strong>
                                    <br><small class="text-muted">{{ $item->category }}</small>
                                </td>
                                <td class="text-center">{{ $item->total_issued }} {{ $item->unit }}</td>
                                <td class="text-center">{{ $item->avg_monthly }}</td>
                                <td class="text-center">
                                    @if($item->days_left === null)
                                        <span class="text-muted">—</span>
                                    @elseif($item->days_left <= 0)
                                        <span class="badge bg-danger">вичерпано</span>
                                    @elseif($item->days_left <= 14)
                                        <span class="badge bg-danger">{{ $item->days_left }} дн.</span>
                                    @elseif($item->days_left <= 30)
                                        <span class="badge bg-warning text-dark">{{ $item->days_left }} дн.</span>
                                    @else
                                        <span class="text-muted">{{ $item->days_left }} дн.</span>
                                    @endif
                                </td>
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
// Картриджі: історія + прогноз
(function () {
    const ctx = document.getElementById('cartridgeChart');
    if (!ctx) { return; }

    const historicalLabels = {!! json_encode($cartridgeChartLabels) !!};
    const historicalData   = {!! json_encode($cartridgeChartData) !!};
    const forecastLabels   = {!! json_encode($forecastMonths) !!};
    const forecastValues   = {!! json_encode($forecastData) !!};

    const allLabels = [...historicalLabels, ...forecastLabels];
    const histSeries = [...historicalData, ...Array(forecastValues.length).fill(null)];
    const forecastSeries = [...Array(historicalData.length).fill(null), ...forecastValues];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: allLabels.length ? allLabels : ['Немає даних'],
            datasets: [
                {
                    label: 'Фактично',
                    data: histSeries,
                    borderColor: 'rgb(59,130,246)',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    borderWidth: 2,
                },
                {
                    label: 'Прогноз',
                    data: forecastSeries,
                    borderColor: 'rgb(34,197,94)',
                    backgroundColor: 'rgba(34,197,94,0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    borderWidth: 2,
                    borderDash: [5,5],
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}());

// Категорії: стековий бар
(function () {
    const ctx = document.getElementById('categoryChart');
    if (!ctx) { return; }

    const labels   = {!! json_encode($chartMonthLabels) !!};
    const datasets = {!! json_encode($categoryDatasets) !!};

    if (!labels.length || !datasets.length) {
        ctx.parentElement.innerHTML += '<p class="text-muted text-center mt-3">Немає даних про видачі за цей період</p>';
        return;
    }

    new Chart(ctx, {
        type: 'bar',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { position: 'top' } },
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true }
            }
        }
    });
}());
</script>
@endpush
