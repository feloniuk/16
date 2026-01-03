@extends('layouts.app')

@section('title', 'Інвентар складу')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3">Інвентар складу</h1>
        <div class="d-flex gap-2">
            <span class="badge bg-primary">{{ $items->total() }} товарів</span>
            <span class="badge bg-warning">{{ $lowStockItems }} низький запас</span>
        </div>
    </div>
</div>

<!-- Фільтри -->
<div class="stats-card p-4 mb-4">
    <h5 class="card-title mb-3">Фільтри</h5>
    <form method="GET" class="row g-3">
        <div class="col-md-4">
            <input type="text" class="form-control" name="search" placeholder="Пошук по назві, коду, марці..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" name="category" placeholder="Категорія"
                   value="{{ request('category') }}">
        </div>
        <div class="col-md-2">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="low_stock" id="low_stock"
                       {{ request('low_stock') ? 'checked' : '' }}>
                <label class="form-check-label" for="low_stock">
                    Тільки низький запас
                </label>
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel"></i> Пошук
            </button>
        </div>
    </form>
</div>

<!-- Статистика -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">Загальна вартість</h6>
                <h3 class="mb-0">{{ number_format($totalValue, 2, ',', ' ') }} ₴</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">Товарів в наявності</h6>
                <h3 class="mb-0">{{ $items->total() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">Низький запас</h6>
                <h3 class="mb-0 text-warning">{{ $lowStockItems }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">Сторінка</h6>
                <h3 class="mb-0">{{ $items->currentPage() }} / {{ $items->lastPage() }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Таблиця -->
<div class="stats-card p-4">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Назва</th>
                    <th>Код</th>
                    <th>Категорія</th>
                    <th>Кількість</th>
                    <th>Ціна</th>
                    <th>Сумма</th>
                    <th>Мін. запас</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->equipment_type }}</strong>
                            @if($item->brand)
                                <br><small class="text-muted">{{ $item->brand }} {{ $item->model }}</small>
                            @endif
                        </td>
                        <td>
                            <code>{{ $item->inventory_number }}</code>
                        </td>
                        <td>{{ $item->category ?? '-' }}</td>
                        <td>
                            <strong>{{ $item->quantity }}</strong> {{ $item->unit }}
                        </td>
                        <td>{{ number_format($item->price ?? 0, 2, ',', ' ') }} ₴</td>
                        <td>
                            <strong>{{ number_format(($item->quantity * ($item->price ?? 0)), 2, ',', ' ') }} ₴</strong>
                        </td>
                        <td>{{ $item->min_quantity ?? '-' }} {{ $item->unit }}</td>
                        <td>
                            @if($item->isLowStock())
                                <span class="badge bg-danger">Критично низький</span>
                            @elseif($item->quantity < ($item->min_quantity ?? 0) * 1.5)
                                <span class="badge bg-warning">Низький</span>
                            @else
                                <span class="badge bg-success">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox"></i> Товарів не знайдено
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Пагінація -->
    <div class="d-flex justify-content-center mt-4">
        {{ $items->appends(request()->query())->links() }}
    </div>
</div>
@endsection
