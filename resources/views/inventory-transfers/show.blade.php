@extends('layouts.app')

@section('title', 'Перемещение ' . $inventoryTransfer->transfer_number)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Перемещение {{ $inventoryTransfer->transfer_number }}</h4>
                    <p class="text-muted mb-0">
                        Создано {{ $inventoryTransfer->created_at->format('d.m.Y в H:i') }}
                        пользователем {{ $inventoryTransfer->user->name }}
                    </p>
                </div>
                <div>
                    {!! $inventoryTransfer->status_badge !!}
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Откуда</h6>
                    <div class="bg-light p-3 rounded">
                        <div><strong>{{ $inventoryTransfer->fromBranch->name }}</strong></div>
                        @if($inventoryTransfer->from_room)
                            <div class="text-muted">{{ $inventoryTransfer->from_room }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Куда</h6>
                    <div class="bg-light p-3 rounded">
                        <div><strong>{{ $inventoryTransfer->toBranch->name }}</strong></div>
                        @if($inventoryTransfer->to_room)
                            <div class="text-muted">{{ $inventoryTransfer->to_room }}</div>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Дата перемещения</h6>
                    <p class="mb-0">{{ $inventoryTransfer->transfer_date->format('d.m.Y') }}</p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Количество единиц</h6>
                    <p class="mb-0">
                        <span class="badge bg-info fs-6">{{ $inventoryTransfer->items->count() }} ед.</span>
                    </p>
                </div>
                
                <div class="col-12">
                    <h6 class="text-muted mb-2">Причина перемещения</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $inventoryTransfer->reason }}</p>
                    </div>
                </div>
                
                @if($inventoryTransfer->notes)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Заметки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $inventoryTransfer->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
            
            <h5 class="mb-3">Перемещаемое оборудование</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Инв. номер</th>
                            <th>Тип оборудования</th>
                            <th>Бренд/Модель</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryTransfer->items as $item)
                        <tr>
                            <td>{{ $item->inventory->inventory_number }}</td>
                            <td>{{ $item->inventory->equipment_type }}</td>
                            <td>
                                @if($item->inventory->brand || $item->inventory->model)
                                    {{ $item->inventory->brand }} {{ $item->inventory->model }}
                                @else
                                    <span class="text-muted">Не указано</span>
                                @endif
                            </td>
                            <td>
                                @switch($item->status ?? $inventoryTransfer->status)
                                    @case('planned')
                                        <span class="badge bg-secondary">Запланировано</span>
                                        @break
                                    @case('in_transit')
                                        <span class="badge bg-warning">В пути</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Завершено</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">{{ $item->status ?? $inventoryTransfer->status }}</span>
                                @endswitch
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="stats-card p-4">
            <h5 class="mb-3">Действия</h5>
            
            <div class="d-grid gap-2">
                @if($inventoryTransfer->status === 'planned')
                    <form method="POST" action="{{ route('inventory-transfers.start', $inventoryTransfer) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-truck"></i> Отправить в путь
                        </button>
                    </form>
                @endif
                
                @if($inventoryTransfer->status === 'in_transit')
                    <form method="POST" action="{{ route('inventory-transfers.complete', $inventoryTransfer) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" 
                                onclick="return confirm('Завершить перемещение? Инвентарь будет перемещен в указанные локации.')">
                            <i class="bi bi-check-circle"></i> Завершить перемещение
                        </button>
                    </form>
                @endif
                
                @if(in_array($inventoryTransfer->status, ['planned', 'in_transit']))
                    <form method="POST" action="{{ route('inventory-transfers.cancel', $inventoryTransfer) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100"
                                onclick="return confirm('Отменить перемещение?')">
                            <i class="bi bi-x-circle"></i> Отменить
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('inventory-transfers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад к списку
                </a>
            </div>
        </div>
        
        <!-- История -->
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">История изменений</h5>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Перемещение создано</h6>
                        <small class="text-muted">
                            {{ $inventoryTransfer->created_at->format('d.m.Y H:i') }}
                            <br>{{ $inventoryTransfer->user->name }}
                        </small>
                    </div>
                </div>
                
                @if($inventoryTransfer->status !== 'planned')
                <div class="timeline-item">
                    <div class="timeline-marker bg-warning"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Отправлено в путь</h6>
                        <small class="text-muted">{{ $inventoryTransfer->updated_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
                @endif
                
                @if($inventoryTransfer->status === 'completed')
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Перемещение завершено</h6>
                        <small class="text-muted">{{ $inventoryTransfer->updated_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
                @endif
                
                @if($inventoryTransfer->status === 'cancelled')
                <div class="timeline-item">
                    <div class="timeline-marker bg-danger"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Перемещение отменено</h6>
                        <small class="text-muted">{{ $inventoryTransfer->updated_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    padding-left: 15px;
}
</style>
@endpush