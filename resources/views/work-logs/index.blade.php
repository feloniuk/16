@extends('layouts.app')

@section('title', 'Журнал робіт')

@section('content')
<!-- Кнопка показать/скрыть фильтры на мобильных -->
<button class="btn btn-outline-secondary d-md-none w-100 mb-3" type="button"
        data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
    <i class="bi bi-funnel"></i> Фільтри
</button>

<!-- Форма фильтров -->
<div class="collapse show" id="filtersCollapse">
    <div class="stats-card p-4 mb-4">
        <form method="GET" action="{{ route('work-logs.index') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-6 col-lg-2">
                <label for="work_type" class="form-label">Тип роботи</label>
                <select name="work_type" id="work_type" class="form-select">
                    <option value="">Усі типи</option>
                    <option value="inventory_transfer" {{ request('work_type') === 'inventory_transfer' ? 'selected' : '' }}>Перемішення інвентарю</option>
                    <option value="cartridge_replacement" {{ request('work_type') === 'cartridge_replacement' ? 'selected' : '' }}>Заміна картриджа</option>
                    <option value="repair_sent" {{ request('work_type') === 'repair_sent' ? 'selected' : '' }}>Відправка на ремонт</option>
                    <option value="repair_returned" {{ request('work_type') === 'repair_returned' ? 'selected' : '' }}>Повернення з ремонту</option>
                    <option value="manual" {{ request('work_type') === 'manual' ? 'selected' : '' }}>Ручний запис</option>
                </select>
            </div>

            <div class="col-12 col-md-6 col-lg-2">
                <label for="branch_id" class="form-label">Філія</label>
                <select name="branch_id" id="branch_id" class="form-select">
                    <option value="">Усі філії</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-6 col-lg-2">
                <label for="date_from" class="form-label">Дата від</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-12 col-md-6 col-lg-2">
                <label for="date_to" class="form-label">Дата до</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <label for="search" class="form-label">Пошук</label>
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Опис, кабінет..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-6 col-md-3 col-lg-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            @if(request()->hasAny(['work_type', 'branch_id', 'date_from', 'date_to', 'search']))
            <div class="col-6 col-md-3">
                <a href="{{ route('work-logs.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x"></i>
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Заголовок та кнопка додавання -->
<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Журнал робіт ({{ $workLogs->total() }})</h2>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('work-logs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Додати запис
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Таблиця записів -->
<div class="stats-card">
    <div class="card-body p-0">
        @if($workLogs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 85px;">Тип</th>
                            <th>Опис</th>
                            <th class="d-none d-md-table-cell">Філія</th>
                            <th class="d-none d-lg-table-cell">Кабінет</th>
                            <th class="text-center" style="width: 85px;">Дата</th>
                            <th class="d-none d-lg-table-cell">Користувач</th>
                            <th class="text-center" style="width: 75px;">Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workLogs as $log)
                        <tr>
                            <td>
                                @switch($log->work_type)
                                    @case('inventory_transfer')
                                        <span class="badge bg-info" style="font-size: 0.7rem;">Перем.</span>
                                        @break
                                    @case('cartridge_replacement')
                                        <span class="badge bg-warning" style="font-size: 0.7rem;">Карт.</span>
                                        @break
                                    @case('repair_sent')
                                        <span class="badge bg-danger" style="font-size: 0.7rem;">Ремонт ↗</span>
                                        @break
                                    @case('repair_returned')
                                        <span class="badge bg-success" style="font-size: 0.7rem;">Ремонт ↙</span>
                                        @break
                                    @case('manual')
                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">Ручн.</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div>
                                    <strong class="d-block">{{ Str::limit($log->description, 40) }}</strong>
                                    <small class="text-muted d-md-none">
                                        {{ $log->branch->name ?? '-' }}
                                        @if($log->room_number)
                                            , каб. {{ $log->room_number }}
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <small class="badge bg-light text-dark">{{ $log->branch->name ?? '-' }}</small>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small>{{ $log->room_number ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <small>{{ $log->performed_at->format('d.m') }}</small>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small><i class="bi bi-person"></i> {{ Str::limit($log->user->name ?? '-', 15) }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('work-logs.show', $log) }}"
                                       class="btn btn-outline-primary" title="Перегляд">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('work-logs.edit', $log) }}"
                                       class="btn btn-outline-warning" title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('work-logs.destroy', $log) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Ви впевнені?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Видалити">
                                            <i class="bi bi-trash"></i>
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
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Записів не знайдено</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку</p>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('work-logs.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати запис
                </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Пагінація -->
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
@endsection
