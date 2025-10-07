{{-- resources/views/warehouse/movements.blade.php --}}
@extends('layouts.app')

@section('title', 'Рух товарів на складі')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('warehouse.movements') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="type" class="form-label">Тип операції</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Всі операції</option>
                        <option value="receipt" {{ request('type') === 'receipt' ? 'selected' : '' }}>Надходження</option>
                        <option value="issue" {{ request('type') === 'issue' ? 'selected' : '' }}>Видача</option>
                        <option value="writeoff" {{ request('type') === 'writeoff' ? 'selected' : '' }}>Списання</option>
                        <option value="inventory" {{ request('type') === 'inventory' ? 'selected' : '' }}>Інвентаризація</option>
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
                
                <div class="col-md-2">
                    <a href="{{ route('warehouse.movements') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x"></i> Очистити
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Рух товарів ({{ $movements->total() }})</h2>
            <a href="{{ route('warehouse.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-box-seam"></i> До складу
            </a>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-body p-0">
        @if($movements->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Дата операції</th>
                            <th>Товар</th>
                            <th>Тип операції</th>
                            <th>Кількість</th>
                            <th>Залишок після</th>
                            <th>Користувач</th>
                            <th>Документ</th>
                            <th>Примітка</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movements as $movement)
                        <tr>
                            <td>
                                <div>{{ $movement->operation_date->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $movement->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $movement->inventoryItem->equipment_type }}</strong>
                                    <br><small class="text-muted">{{ $movement->inventoryItem->inventory_number }}</small>
                                </div>
                            </td>
                            <td>{!! $movement->type_badge !!}</td>
                            <td>
                                @if($movement->quantity > 0)
                                    <span class="text-success fw-bold">+{{ $movement->quantity }}</span>
                                @else
                                    <span class="text-danger fw-bold">{{ $movement->quantity }}</span>
                                @endif
                                <small class="text-muted d-block">{{ $movement->inventoryItem->unit }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $movement->balance_after }} {{ $movement->inventoryItem->unit }}</span>
                            </td>
                            <td>
                                <div>{{ $movement->user->name }}</div>
                                @if($movement->issuedToUser)
                                    <small class="text-muted">Видано: {{ $movement->issuedToUser->name }}</small>
                                @endif
                            </td>
                            <td>
                                @if($movement->document_number)
                                    <code>{{ $movement->document_number }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($movement->note)
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $movement->note }}">
                                        {{ $movement->note }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white">
                {{ $movements->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-arrow-left-right fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Операцій не знайдено</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку</p>
                <a href="{{ route('warehouse.index') }}" class="btn btn-primary">
                    <i class="bi bi-box-seam"></i> Перейти до складу
                </a>
            </div>
        @endif
    </div>
</div>

@if($movements->count() > 0)
<div class="row g-4 mt-4">
    <div class="col-md-3">
        <div class="stats-card p-3 text-center">
            <div class="text-success">
                <i class="bi bi-arrow-down-circle fs-3"></i>
                <div class="mt-2">
                    <h5>{{ $movements->where('type', 'receipt')->count() }}</h5>
                    <small class="text-muted">Надходжень</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card p-3 text-center">
            <div class="text-warning">
                <i class="bi bi-arrow-up-circle fs-3"></i>
                <div class="mt-2">
                    <h5>{{ $movements->where('type', 'issue')->count() }}</h5>
                    <small class="text-muted">Видач</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card p-3 text-center">
            <div class="text-info">
                <i class="bi bi-clipboard-check fs-3"></i>
                <div class="mt-2">
                    <h5>{{ $movements->where('type', 'inventory')->count() }}</h5>
                    <small class="text-muted">Інвентаризацій</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card p-3 text-center">
            <div class="text-danger">
                <i class="bi bi-x-circle fs-3"></i>
                <div class="mt-2">
                    <h5>{{ $movements->where('type', 'writeoff')->count() }}</h5>
                    <small class="text-muted">Списань</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection