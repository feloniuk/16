{{-- resources/views/inventory-transfers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Перемещения инвентаря')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('inventory-transfers.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Все статусы</option>
                        <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>Запланировано</option>
                        <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>В пути</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Завершено</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Отменено</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="from_branch_id" class="form-label">Откуда</label>
                    <select name="from_branch_id" id="from_branch_id" class="form-select">
                        <option value="">Все филиалы</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('from_branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="to_branch_id" class="form-label">Куда</label>
                    <select name="to_branch_id" id="to_branch_id" class="form-select">
                        <option value="">Все филиалы</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Дата от</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Дата до</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Найти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Перемещения ({{ $transfers->total() }})</h2>
            <a href="{{ route('inventory-transfers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Создать перемещение
            </a>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-body p-0">
        @if($transfers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Номер</th>
                            <th>Откуда</th>
                            <th>Куда</th>
                            <th>Количество</th>
                            <th>Дата перемещения</th>
                            <th>Инициатор</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                        <tr>
                            <td><strong>{{ $transfer->transfer_number }}</strong></td>
                            <td>
                                <div>{{ $transfer->fromBranch->name }}</div>
                                @if($transfer->from_room)
                                    <small class="text-muted">{{ $transfer->from_room }}</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ $transfer->toBranch->name }}</div>
                                @if($transfer->to_room)
                                    <small class="text-muted">{{ $transfer->to_room }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $transfer->items_count ?? $transfer->items->count() }} ед.</span>
                            </td>
                            <td>{{ $transfer->transfer_date->format('d.m.Y') }}</td>
                            <td>{{ $transfer->user->name }}</td>
                            <td>{!! $transfer->status_badge !!}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('inventory-transfers.show', $transfer) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Просмотр">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if($transfer->status === 'in_transit')
                                    <form method="POST" action="{{ route('inventory-transfers.complete', $transfer) }}" 
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" 
                                                title="Завершить перемещение"
                                                onclick="return confirm('Завершить перемещение?')">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if(in_array($transfer->status, ['planned', 'in_transit']))
                                    <form method="POST" action="{{ route('inventory-transfers.cancel', $transfer) }}" 
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                title="Отменить перемещение"
                                                onclick="return confirm('Отменить перемещение?')">
                                            <i class="bi bi-x-circle"></i>
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
            
            <div class="card-footer bg-white">
                {{ $transfers->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-arrow-left-right fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Перемещений не найдено</h5>
                <p class="text-muted">Попробуйте изменить параметры поиска</p>
            </div>
        @endif
    </div>
</div>
@endsection