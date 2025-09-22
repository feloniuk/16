{{-- resources/views/dashboard/warehouse-keeper.blade.php --}}
@extends('layouts.app')

@section('title', 'Головна - Складовщик')

@section('content')
<!-- Статистика товаров -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stats-card p-4 text-center">
            <div class="display-6 text-primary mb-2">
                <i class="bi bi-box-seam"></i>
            </div>
            <h3 class="mb-1">{{ $warehouseStats['total_items'] }}</h3>
            <p class="text-muted mb-0">Найменувань товарів</p>
            <small class="text-primary">На складі</small>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="stats-card p-4 text-center">
            <div class="display-6 text-warning mb-2">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h3 class="mb-1">{{ $warehouseStats['low_stock_items'] }}</h3>
            <p class="text-muted mb-0">Мало на складі</p>
            @if($warehouseStats['low_stock_items'] > 0)
                <small class="text-warning">Потребує уваги</small>
            @else
                <small class="text-success">Все в нормі</small>
            @endif
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="stats-card p-4 text-center">
            <div class="display-6 text-danger mb-2">
                <i class="bi bi-x-circle"></i>
            </div>
            <h3 class="mb-1">{{ $warehouseStats['out_of_stock'] }}</h3>
            <p class="text-muted mb-0">Немає на складі</p>
            @if($warehouseStats['out_of_stock'] > 0)
                <small class="text-danger">Потрібне поповнення</small>
            @else
                <small class="text-success">Все в наявності</small>
            @endif
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="stats-card p-4 text-center">
            <div class="display-6 text-success mb-2">
                <i class="bi bi-currency-exchange"></i>
            </div>
            <h3 class="mb-1">{{ number_format($warehouseStats['total_value'], 0, ',', ' ') }}</h3>
            <p class="text-muted mb-0">грн</p>
            <small class="text-success">Загальна вартість</small>
        </div>
    </div>
</div>

<!-- Швидкі дії -->
<div class="row g-4 mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <h5 class="mb-3">
                <i class="bi bi-lightning"></i> Швидкі дії
            </h5>
            <div class="row g-2">
                <div class="col-md-3">
                    <a href="{{ route('warehouse-inventory.quick') }}" class="btn btn-warning w-100">
                        <i class="bi bi-clipboard-check"></i> Швидка інвентаризація
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('warehouse.create') }}" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i> Додати товар
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('purchase-requests.create') }}" class="btn btn-success w-100">
                        <i class="bi bi-cart-plus"></i> Заявка на закупівлю
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('warehouse.movements') }}" class="btn btn-info w-100">
                        <i class="bi bi-arrow-left-right"></i> Рух товарів
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Товары с низкими остатками -->
    <div class="col-lg-6">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>
                    <i class="bi bi-exclamation-triangle text-warning"></i> 
                    Товари з низькими залишками
                </h5>
                <a href="{{ route('warehouse.index', ['low_stock' => 1]) }}" class="btn btn-sm btn-outline-warning">
                    Переглянути всі
                </a>
            </div>
            
            @if($lowStockItems->count() > 0)
                <div class="list-group">
                    @foreach($lowStockItems as $item)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $item->name }}</strong>
                            <br><small class="text-muted">{{ $item->code }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-warning">{{ $item->quantity }} {{ $item->unit }}</span>
                            <br><small class="text-muted">мін: {{ $item->min_quantity }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-check-circle fs-1"></i>
                    <p>Все товари в достатній кількості</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Статистика заявок на закупку -->
    <div class="col-lg-6">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>
                    <i class="bi bi-cart-plus text-primary"></i> 
                    Заявки на закупівлю
                </h5>
                <a href="{{ route('purchase-requests.index') }}" class="btn btn-sm btn-outline-primary">
                    Всі заявки
                </a>
            </div>
            
            <div class="row g-3">
                <div class="col-6">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="fs-4 text-secondary">{{ $purchaseRequestsStats['draft'] }}</div>
                        <small>Чернетки</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                        <div class="fs-4 text-warning">{{ $purchaseRequestsStats['submitted'] }}</div>
                        <small>На розгляді</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                        <div class="fs-4 text-success">{{ $purchaseRequestsStats['approved'] }}</div>
                        <small>Затверджені</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                        <div class="fs-4 text-info">{{ $purchaseRequestsStats['my_requests'] }}</div>
                        <small>Мої заявки</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Последние движения товаров -->
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>
                    <i class="bi bi-arrow-left-right text-info"></i> 
                    Останні операції
                </h5>
                <a href="{{ route('warehouse.movements') }}" class="btn btn-sm btn-outline-info">
                    Всі операції
                </a>
            </div>
            
            @if($recentMovements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Дата</th>
                                <th>Товар</th>
                                <th>Операція</th>
                                <th>Кількість</th>
                                <th>Залишок</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentMovements as $movement)
                            <tr>
                                <td>
                                    <small>{{ $movement->operation_date->format('d.m.Y') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $movement->warehouseItem->name }}</strong>
                                </td>
                                <td>{!! $movement->type_badge !!}</td>
                                <td>
                                    @if($movement->quantity > 0)
                                        <span class="text-success">+{{ $movement->quantity }}</span>
                                    @else
                                        <span class="text-danger">{{ $movement->quantity }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $movement->balance_after }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p>Немає операцій</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Недавние инвентаризации -->
    <div class="col-lg-4">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>
                    <i class="bi bi-clipboard-check text-success"></i> 
                    Інвентаризації
                </h5>
                <a href="{{ route('warehouse-inventory.index') }}" class="btn btn-sm btn-outline-success">
                    Всі
                </a>
            </div>
            
            @if($recentInventories->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentInventories as $inventory)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $inventory->inventory_number }}</strong>
                                <br><small class="text-muted">{{ $inventory->user->name }}</small>
                            </div>
                            <div class="text-end">
                                {!! $inventory->status_badge !!}
                                <br><small class="text-muted">{{ $inventory->inventory_date->format('d.m.Y') }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-clipboard fs-3"></i>
                    <p class="mb-2">Немає інвентаризацій</p>
                    <a href="{{ route('warehouse-inventory.create') }}" class="btn btn-sm btn-success">
                        Створити інвентаризацію
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- График активности за неделю -->
@if($dailyActivity->count() > 0)
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="stats-card p-4">
            <h5 class="mb-3">
                <i class="bi bi-bar-chart text-primary"></i> 
                Активність за тиждень
            </h5>
            
            <div class="row">
                <div class="col-lg-8">
                    <canvas id="activityChart" height="100"></canvas>
                </div>
                <div class="col-lg-4">
                    <div class="row g-3">
                        @if($topActiveItems->count() > 0)
                            <div class="col-12">
                                <h6>Найактивніші товари цього місяця:</h6>
                                <div class="list-group list-group-flush">
                                    @foreach($topActiveItems as $item)
                                    <div class="list-group-item px-0 py-2">
                                        <div class="d-flex justify-content-between">
                                            <small>{{ $item->warehouseItem->name }}</small>
                                            <span class="badge bg-primary">{{ $item->total_movements }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// График активности
@if($dailyActivity->count() > 0)
const ctx = document.getElementById('activityChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! $dailyActivity->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d.m'); })->toJson() !!},
        datasets: [{
            label: 'Операцій за день',
            data: {!! $dailyActivity->pluck('movements_count')->toJson() !!},
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
@endif

// Автообновление каждые 5 минут
setInterval(function() {
    location.reload();
}, 300000);

// Уведомления о низких остатках
@if($warehouseStats['low_stock_items'] > 0)
document.addEventListener('DOMContentLoaded', function() {
    // Показываем уведомление только раз в сессию
    if (!sessionStorage.getItem('lowStockNotified')) {
        setTimeout(function() {
            const toast = document.createElement('div');
            toast.className = 'toast-container position-fixed top-0 end-0 p-3';
            toast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-header">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        <strong class="me-auto">Увага!</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        {{ $warehouseStats['low_stock_items'] }} товарів з низькими залишками. 
                        <a href="{{ route('warehouse.index', ['low_stock' => 1]) }}" class="alert-link">Переглянути</a>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(function() {
                toast.remove();
            }, 10000);
        }, 2000);
        
        sessionStorage.setItem('lowStockNotified', 'true');
    }
});
@endif
</script>
@endpush