@extends('layouts.app')

@section('title', 'Інвентаризації складу')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('warehouse-inventory.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Всі статуси</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>В процесі</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Завершена</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Дата від</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Дата до</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Знайти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Інвентаризації ({{ $inventories->total() }})</h2>
            <div>
                <a href="{{ route('warehouse-inventory.quick') }}" class="btn btn-warning me-2">
                    <i class="bi bi-lightning"></i> Швидка інвентаризація
                </a>
                <a href="{{ route('warehouse-inventory.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Нова інвентаризація
                </a>
            </div>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-body p-0">
        @if($inventories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>№ інвентаризації</th>
                            <th>Дата проведення</th>
                            <th>Ініціатор</th>
                            <th>Кількість товарів</th>
                            <th>Статус</th>
                            <th>Створено</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inventory)
                        <tr>
                            <td><strong>{{ $inventory->inventory_number }}</strong></td>
                            <td>{{ $inventory->inventory_date->format('d.m.Y') }}</td>
                            <td>{{ $inventory->user->name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $inventory->items_count }} поз.</span>
                                @if($inventory->status === 'completed' && $inventory->total_discrepancies > 0)
                                    <br><small class="text-warning">{{ $inventory->total_discrepancies }} розбіжностей</small>
                                @endif
                            </td>
                            <td>{!! $inventory->status_badge !!}</td>
                            <td>
                                <div>{{ $inventory->created_at->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $inventory->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('warehouse-inventory.show', $inventory) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Переглянути">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if($inventory->status === 'in_progress')
                                    <a href="{{ route('warehouse-inventory.edit', $inventory) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Продовжити">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <form method="POST" action="{{ route('warehouse-inventory.complete', $inventory) }}" 
                                          class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" 
                                                title="Завершити інвентаризацію"
                                                onclick="return confirm('Завершити інвентаризацію? Залишки товарів будуть оновлені.')">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-clipboard-check fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Інвентаризацій не знайдено</h5>
                <p class="text-muted">Створіть нову інвентаризацію для контролю залишків</p>
                <div>
                    <a href="{{ route('warehouse-inventory.quick') }}" class="btn btn-warning me-2">
                        <i class="bi bi-lightning"></i> Швидка інвентаризація
                    </a>
                    <a href="{{ route('warehouse-inventory.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Нова інвентаризація
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($inventories->total() > 0)
<div class="stats-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Показано {{ $inventories->firstItem() }} - {{ $inventories->lastItem() }}
            з {{ $inventories->total() }} записів
        </div>
        <div>
            {{ $inventories->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
.pagination {
    margin: 0;
}
.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
@endpush
@endsection