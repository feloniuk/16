@extends('layouts.app')

@section('title', 'Інвентар кабінетів')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3">Інвентар кабінетів та обладнання</h1>
        <span class="badge bg-primary">{{ $items->total() }} одиниць</span>
    </div>
</div>

<!-- Фільтри -->
<div class="stats-card p-4 mb-4">
    <h5 class="card-title mb-3">Фільтри</h5>
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <input type="text" class="form-control" name="search" placeholder="Пошук по назві, кабінету, серійному номеру..."
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select class="form-select" name="branch_id">
                <option value="">Усі філії</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" name="category" placeholder="Категорія"
                   value="{{ request('category') }}">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel"></i> Пошук
            </button>
        </div>
    </form>
</div>

<!-- Статистика -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">Загальна вартість обладнання</h6>
                <h3 class="mb-0">{{ number_format($totalValue, 2, ',', ' ') }} ₴</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stats-card p-4 h-100">
            <div>
                <h6 class="text-muted mb-2">Одиниць обладнання</h6>
                <h3 class="mb-0">{{ $items->total() }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
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
                    <th>Обладнання</th>
                    <th>Філія</th>
                    <th>Кабінет</th>
                    <th>Серійний номер</th>
                    <th>Код інвентаря</th>
                    <th>Категорія</th>
                    <th>Ціна</th>
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
                            @if($item->branch)
                                <span class="badge bg-light text-dark">{{ $item->branch->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->room_number)
                                <strong>{{ $item->room_number }}</strong>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <code>{{ $item->serial_number ?? '-' }}</code>
                        </td>
                        <td>
                            <code>{{ $item->inventory_number ?? '-' }}</code>
                        </td>
                        <td>{{ $item->category ?? '-' }}</td>
                        <td>
                            @if($item->price)
                                <strong>{{ number_format($item->price, 2, ',', ' ') }} ₴</strong>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->inventory_number)
                                <span class="badge bg-success">Активне</span>
                            @else
                                <span class="badge bg-secondary">Неактивне</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox"></i> Обладнання не знайдено
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
