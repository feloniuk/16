@extends('layouts.app')

@section('title', 'Журнал робіт')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <!-- Filters -->
            <form method="GET" action="{{ route('work-logs.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="branch_id" class="form-label">Філіал</label>
                    <select name="branch_id" id="branch_id" class="form-select">
                        <option value="">Всі філіали</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="work_type" class="form-label">Тип роботи</label>
                    <select name="work_type" id="work_type" class="form-select">
                        <option value="">Всі типи</option>
                        <option value="inventory_transfer" {{ request('work_type') == 'inventory_transfer' ? 'selected' : '' }}>Переміщення інвентарю</option>
                        <option value="cartridge_replacement" {{ request('work_type') == 'cartridge_replacement' ? 'selected' : '' }}>Заміна картриджа</option>
                        <option value="repair_sent" {{ request('work_type') == 'repair_sent' ? 'selected' : '' }}>Відправка на ремонт</option>
                        <option value="repair_returned" {{ request('work_type') == 'repair_returned' ? 'selected' : '' }}>Повернення з ремонту</option>
                        <option value="manual" {{ request('work_type') == 'manual' ? 'selected' : '' }}>Інше</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date_from" class="form-label">Дата від</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_to" class="form-label">Дата до</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-3">
                    <label for="search" class="form-label">Пошук</label>
                    <input type="text" name="search" id="search" class="form-control"
                           placeholder="Пошук по описанню, кабінету..." value="{{ request('search') }}">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Знайти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Журнал робіт ({{ $workLogs->total() }})</h5>
        <div>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('work-logs.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Додати запис
                </a>
            @endif
            <a href="{{ route('work-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise"></i> Оновити
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        @if($workLogs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Тип роботи</th>
                            <th>Опис</th>
                            <th>Філіал/Кабінет</th>
                            <th>Дата виконання</th>
                            <th>Користувач</th>
                            <th>Дата створення</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workLogs as $log)
                        <tr>
                            <td><strong>#{{ $log->id }}</strong></td>
                            <td>
                                @switch($log->work_type)
                                    @case('inventory_transfer')
                                        <span class="badge bg-primary">Переміщення</span>
                                        @break
                                    @case('cartridge_replacement')
                                        <span class="badge bg-info">Картридж</span>
                                        @break
                                    @case('repair_sent')
                                        <span class="badge bg-warning">Відправка</span>
                                        @break
                                    @case('repair_returned')
                                        <span class="badge bg-success">Повернення</span>
                                        @break
                                    @case('manual')
                                        <span class="badge bg-secondary">Інше</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div style="max-width: 300px;">
                                    {{ Str::limit($log->description, 50) }}
                                </div>
                            </td>
                            <td>
                                @if($log->branch)
                                    <span class="badge bg-light text-dark">{{ $log->branch->name }}</span>
                                @endif
                                @if($log->room_number)
                                    <br><small class="text-muted">{{ $log->room_number }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $log->performed_at->format('d.m.Y') }}
                            </td>
                            <td>
                                @if($log->user)
                                    <i class="bi bi-person"></i> {{ $log->user->name }}
                                @endif
                            </td>
                            <td>
                                <div>{{ $log->created_at->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('work-logs.show', $log) }}"
                                   class="btn btn-sm btn-outline-primary" title="Переглянути">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('work-logs.edit', $log) }}"
                                       class="btn btn-sm btn-outline-secondary" title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-journal-x fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Записи не знайдені</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку</p>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($workLogs->total() > 0)
<div class="stats-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Показано {{ $workLogs->firstItem() }} - {{ $workLogs->lastItem() }}
            з {{ $workLogs->total() }} записів
        </div>
        <div>
            {{ $workLogs->withQueryString()->links('vendor.pagination.bootstrap-5') }}
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
