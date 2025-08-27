{{-- resources/views/dashboard/warehouse-manager.blade.php --}}
{{-- Дашборд для заведующего складом с полным функционалом управления инвентарем --}}
@extends('layouts.app')

@section('title', 'Панель заведующего складом')

@section('content')
<div class="row g-4">
    <!-- Основная статистика по инвентарю -->
    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Всего инвентаря</h6>
                    <h3 class="mb-0">{{ $inventoryStats['total'] }}</h3>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i>
                        +{{ $inventoryStats['recent_additions'] }} за неделю
                    </small>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded">
                    <i class="bi bi-pc-display text-primary fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Запланированные перемещения</h6>
                    <h3 class="mb-0 text-warning">{{ $transferStats['pending'] }}</h3>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded">
                    <i class="bi bi-arrow-left-right text-warning fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">В пути</h6>
                    <h3 class="mb-0 text-info">{{ $transferStats['in_transit'] }}</h3>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded">
                    <i class="bi bi-truck text-info fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stats-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-muted mb-2">Завершено за месяц</h6>
                    <h3 class="mb-0 text-success">{{ $transferStats['completed_this_month'] }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Статистика по типам инвентаря -->
    <div class="col-lg-6">
        <div class="stats-card p-4">
            <h5 class="card-title mb-3">Инвентарь по типам</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Тип оборудования</th>
                            <th class="text-end">Количество</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryStats['by_type'] as $type)
                        <tr>
                            <td>{{ $type->equipment_type }}</td>
                            <td class="text-end">
                                <span class="badge bg-primary">{{ $type->count }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Активные инвентаризации -->
    <div class="col-lg-6">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Активные инвентаризации</h5>
                <a href="{{ route('inventory-audits.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Создать
                </a>
            </div>
            
            @if($activeAudits->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($activeAudits as $audit)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $audit->branch->name }}</h6>
                                <p class="mb-1 text-muted small">
                                    Номер: {{ $audit->audit_number }}
                                </p>
                                <small class="text-muted">
                                    Дата: {{ $audit->audit_date->format('d.m.Y') }}
                                </small>
                            </div>
                            <div class="text-end">
                                {!! $audit->status_badge !!}
                                <div class="mt-1">
                                    <small class="text-muted">
                                        {{ $audit->completion_percentage }}%
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-3">
                    <i class="bi bi-clipboard-check fs-2 text-muted"></i>
                    <p class="text-muted mt-2">Нет активных инвентаризаций</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Последние операции с подрядчиками -->
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Последние операции с подрядчиками</h5>
                <a href="{{ route('contractor-operations.index') }}" class="btn btn-sm btn-outline-primary">
                    Все операции <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            @if($recentOperations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Подрядчик</th>
                                <th>Операция</th>
                                <th>Дата</th>
                                <th>Стоимость</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOperations as $operation)
                            <tr>
                                <td>{{ $operation->contractor->name }}</td>
                                <td>{!! $operation->type_badge !!}</td>
                                <td>{{ $operation->operation_date->format('d.m.Y') }}</td>
                                <td>
                                    @if($operation->cost)
                                        {{ number_format($operation->cost, 2) }} грн
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{!! $operation->status_badge !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-briefcase fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Операций пока нет</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Статистика подрядчиков -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <h5 class="card-title mb-3">Подрядчики</h5>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Всего активных</span>
                    <span class="badge bg-primary">{{ $contractorStats['total'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Активных операций</span>
                    <span class="badge bg-warning">{{ $contractorStats['active_operations'] }}</span>
                </div>
            </div>
            
            <h6 class="mb-2">По типам:</h6>
            @foreach($contractorStats['by_type'] as $type)
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span>
                    @switch($type->type)
                        @case('repair') Ремонт @break
                        @case('supply') Поставки @break  
                        @case('service') Обслуживание @break
                        @default {{ ucfirst($type->type) }}
                    @endswitch
                </span>
                <span class="badge bg-light text-dark">{{ $type->count }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Журнал действий -->
    <div class="col-12">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Последние действия</h5>
                <a href="{{ route('inventory-logs.index') }}" class="btn btn-sm btn-outline-primary">
                    Полный журнал <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            @if($recentLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Время</th>
                                <th>Пользователь</th>
                                <th>Действие</th>
                                <th>Оборудование</th>
                                <th>Описание</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLogs as $log)
                            <tr>
                                <td>
                                    <div>{{ $log->created_at->format('d.m.Y') }}</div>
                                    <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                                </td>
                                <td>{{ $log->user->name }}</td>
                                <td>{!! $log->action_badge !!}</td>
                                <td>
                                    @if($log->inventory)
                                        <div>{{ $log->inventory->equipment_type }}</div>
                                        <small class="text-muted">
                                            {{ $log->inventory->branch->name }} - {{ $log->inventory->room_number }}
                                        </small>
                                    @else
                                        <span class="text-muted">Не указано</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($log->description, 50) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-journal-text fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Журнал пуст</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection