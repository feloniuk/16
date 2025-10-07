{{-- resources/views/warehouse-inventory/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Інвентаризація ' . $inventory->inventory_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Інвентаризація {{ $inventory->inventory_number }}</h4>
                    <p class="text-muted mb-0">
                        Дата: {{ $inventory->inventory_date->format('d.m.Y') }} | 
                        Створена: {{ $inventory->created_at->format('d.m.Y в H:i') }}
                    </p>
                </div>
                <div>
                    {!! $inventory->status_badge !!}
                </div>
            </div>
            
            @if($inventory->status === 'completed')
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Інформація:</strong> Ця інвентаризація вже завершена. Зміни не можливі.
                </div>
            @endif
            
            <!-- Статистика -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="fs-4 fw-bold">{{ $inventory->items->count() }}</div>
                        <small class="text-muted">Всього позицій</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                        <div class="fs-4 fw-bold text-success" id="noDiscrepancyCount">
                            {{ $inventory->items->where('difference', 0)->count() }}
                        </div>
                        <small class="text-muted">Без розбіжностей</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                        <div class="fs-4 fw-bold text-warning" id="discrepancyCount">
                            {{ $inventory->items->where('difference', '!=', 0)->count() }}
                        </div>
                        <small class="text-muted">З розбіжностями</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                        <button type="button" class="btn btn-sm btn-warning" onclick="saveProgress()">
                            <i class="bi bi-save"></i> Зберегти прогрес
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Фільтри та пошук -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="searchItems" class="form-label">Пошук</label>
                    <input type="text" id="searchItems" class="form-control" 
                           placeholder="Назва, код або філія...">
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Статус</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">Всі позиції</option>
                        <option value="unchanged">Без змін</option>
                        <option value="changed">Зі змінами</option>
                        <option value="discrepancy">З розбіжностями</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterBranch" class="form-label">Філія</label>
                    <select id="filterBranch" class="form-select">
                        <option value="">Всі філії</option>
                        <option value="6">Склад</option>
                        @foreach($inventory->items->pluck('inventoryItem.branch')->unique('id')->sortBy('name') as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Дії</label>
                    <button type="button" class="btn btn-sm btn-outline-warning w-100" onclick="fillAllFromSystem()">
                        <i class="bi bi-arrow-repeat"></i> З системи
                    </button>
                </div>
            </div>
            
            <!-- Таблиця товарів -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="25%">Позиція</th>
                            <th width="10%">Філія</th>
                            <th width="10%">В системі</th>
                            <th width="15%">Фактично</th>
                            <th width="10%">Різниця</th>
                            <th width="20%">Примітка</th>
                            <th width="10%">Дії</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        @foreach($inventory->items as $item)
                        <tr class="inventory-row" 
                            data-item-id="{{ $item->id }}" 
                            data-item-name="{{ strtolower($item->inventoryItem->equipment_type) }}"
                            data-item-code="{{ strtolower($item->inventoryItem->inventory_number) }}"
                            data-branch-id="{{ $item->inventoryItem->branch_id }}"
                            data-branch-name="{{ strtolower($item->inventoryItem->branch->name) }}">
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
                                <span class="badge bg-info system-quantity">{{ $item->system_quantity }}</span>
                                @if($item->inventoryItem->isWarehouseItem())
                                    <small class="text-muted d-block">{{ $item->inventoryItem->unit }}</small>
                                @endif
                            </td>
                            <td>
                                @if($inventory->status === 'in_progress')
                                    <input type="number" class="form-control form-control-sm actual-quantity" 
                                           value="{{ $item->actual_quantity }}" 
                                           min="0" 
                                           data-system="{{ $item->system_quantity }}"
                                           data-item-id="{{ $item->id }}"
                                           onchange="calculateDifference(this)"
                                           style="width: 100px;">
                                @else
                                    <span class="badge bg-secondary">{{ $item->actual_quantity }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="difference-badge" data-difference="{{ $item->difference }}">
                                    {!! $item->difference_status !!}
                                </span>
                            </td>
                            <td>
                                @if($inventory->status === 'in_progress')
                                    <input type="text" class="form-control form-control-sm item-note" 
                                           value="{{ $item->note }}" 
                                           data-item-id="{{ $item->id }}"