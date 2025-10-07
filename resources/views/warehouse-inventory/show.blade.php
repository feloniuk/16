{{-- resources/views/warehouse-inventory/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Інвентаризація ' . $inventory->inventory_number)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Інвентаризація {{ $inventory->inventory_number }}</h4>
                    <p class="text-muted mb-0">
                        Створена {{ $inventory->created_at->format('d.m.Y в H:i') }}
                        користувачем {{ $inventory->user->name }}
                    </p>
                </div>
                <div>
                    {!! $inventory->status_badge !!}
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Дата проведення</h6>
                    <p class="mb-0">{{ $inventory->inventory_date->format('d.m.Y') }}</p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Кількість позицій</h6>
                    <p class="mb-0">
                        <span class="badge bg-info fs-6">{{ $inventory->items->count() }} позицій</span>
                    </p>
                </div>
                
                @if($inventory->notes)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Примітки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $inventory->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
            
            <h5 class="mb-3">Результати інвентаризації</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Позиція</th>
                            <th>Філія</th>
                            <th>В системі</th>
                            <th>Фактично</th>
                            <th>Різниця</th>
                            <th>Примітка</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory->items as $item)
                        <tr class="{{ $item->difference != 0 ? 'table-warning' : '' }}">
                            <td>
                                <div>
                                    <strong>{{ $item->inventoryItem->equipment_type }}</strong>
                                    <br><small class="text-muted">{{ $item->inventoryItem->inventory_number }}</small>
                                    @if($item->inventoryItem->brand || $item->inventoryItem->model)
                                        <br><small class="text-muted">{{ $item->inventoryItem->brand }} {{ $item->inventoryItem->model }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $item->inventoryItem->isWarehouseItem() ? 'bg-warning' : 'bg-primary' }}">
                                    {{ $item->inventoryItem->branch->name }}
                                </span>
                            </td>
                            <td>
                                {{ $item->system_quantity }}
                                @if($item->inventoryItem->isWarehouseItem())
                                    <small class="text-muted">{{ $item->inventoryItem->unit }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $item->actual_quantity }}
                                @if($item->inventoryItem->isWarehouseItem())
                                    <small class="text-muted">{{ $item->inventoryItem->unit }}</small>
                                @endif
                            </td>
                            <td>{!! $item->difference_status !!}</td>
                            <td>{{ $item->note ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="stats-card p-4">
            <h5 class="mb-3">Статистика</h5>
            
            <div class="row g-3">
                <div class="col-12">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="fs-4 fw-bold">{{ $inventory->items->count() }}</div>
                        <small class="text-muted">Всього позицій</small>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                        <div class="fs-5 fw-bold text-success">{{ $inventory->items->where('difference', 0)->count() }}</div>
                        <small class="text-muted">Без розбіжностей</small>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                        <div class="fs-5 fw-bold text-warning">{{ $inventory->items->where('difference', '!=', 0)->count() }}</div>
                        <small class="text-muted">З розбіжностями</small>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                        <div class="fs-6 fw-bold text-info">{{ $inventory->items->where('difference', '>', 0)->count() }}</div>
                        <small class="text-muted">Надлишки</small>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="text-center p-2 bg-danger bg-opacity-10 rounded">
                        <div class="fs-6 fw-bold text-danger">{{ $inventory->items->where('difference', '<', 0)->count() }}</div>
                        <small class="text-muted">Нестачі</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Дії</h5>
            
            <div class="d-grid gap-2">
                @if($inventory->status === 'in_progress')
                    <a href="{{ route('warehouse-inventory.edit', $inventory) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Продовжити інвентаризацію
                    </a>
                    
                    <form method="POST" action="{{ route('warehouse-inventory.complete', $inventory) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success w-100" 
                                onclick="return confirm('Завершити інвентаризацію? Залишки будуть оновлені.')">
                            <i class="bi bi-check-circle"></i> Завершити інвентаризацію
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('warehouse-inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад до списку
                </a>
            </div>
        </div>
    </div>
</div>
@endsection