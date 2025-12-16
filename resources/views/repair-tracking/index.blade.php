{{-- resources/views/repair-tracking/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Облік ремонтів')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('repair-tracking.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Всі статуси</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Відправлено</option>
                        <option value="in_repair" {{ request('status') === 'in_repair' ? 'selected' : '' }}>На ремонті</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Завершено</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Скасовано</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="branch_id" class="form-label">Філія</label>
                    <select name="branch_id" id="branch_id" class="form-select">
                        <option value="">Всі філії</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="master_id" class="form-label">Майстер</label>
                    <select name="master_id" id="master_id" class="form-select">
                        <option value="">Всі майстри</option>
                        @foreach($masters as $master)
                            <option value="{{ $master->id }}" {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                {{ $master->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Пошук</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Пошук по накладній, опису..." value="{{ request('search') }}">
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
            <h2>Облік ремонтів ({{ $trackings->total() }})</h2>
            <div>
                <a href="{{ route('repair-masters.index') }}" class="btn btn-outline-info me-2">
                    <i class="bi bi-people"></i> Майстри
                </a>
                <a href="{{ route('repair-tracking.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати запис
                </a>
            </div>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-body p-0">
        @if($trackings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Обладнання</th>
                            <th>Філія/Кімната</th>
                            <th>Майстер</th>
                            <th>Дата відправки</th>
                            <th>Номер накладної</th>
                            <th>Статус</th>
                            <th>Вартість</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trackings as $tracking)
                        <tr>
                            <td><strong>#{{ $tracking->id }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $tracking->equipment->equipment_type }}</strong>
                                    @if($tracking->equipment->brand || $tracking->equipment->model)
                                        <br><small class="text-muted">{{ $tracking->equipment->brand }} {{ $tracking->equipment->model }}</small>
                                    @endif
                                    <br><small class="text-muted">Інв. №: {{ $tracking->equipment->inventory_number }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $tracking->equipment->branch->name }}</span>
                                <br>кімн. {{ $tracking->equipment->room_number }}
                            </td>
                            <td>
                                @if($tracking->repairMaster)
                                    {{ $tracking->repairMaster->name }}
                                    @if($tracking->repairMaster->phone)
                                        <br><small class="text-muted">{{ $tracking->repairMaster->phone }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">Не вказано</span>
                                @endif
                            </td>
                            <td>{{ $tracking->sent_date->format('d.m.Y') }}</td>
                            <td>{{ $tracking->invoice_number ?? '-' }}</td>
                            <td>{!! $tracking->status_badge !!}</td>
                            <td>
                                @if($tracking->cost)
                                    {{ number_format($tracking->cost, 2) }} грн
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('repair-tracking.show', $tracking) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Перегляд">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('repair-tracking.edit', $tracking) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('repair-tracking.destroy', $tracking) }}" 
                                          class="d-inline" onsubmit="return confirm('Видалити цей запис?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Видалити">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tools fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Записи не знайдені</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку або додайте новий запис</p>
                <a href="{{ route('repair-tracking.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати запис
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($trackings->total() > 0)
<div class="stats-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Показано {{ $trackings->firstItem() }} - {{ $trackings->lastItem() }}
            з {{ $trackings->total() }} записів
        </div>
        <div>
            {{ $trackings->withQueryString()->links('vendor.pagination.bootstrap-5') }}
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





