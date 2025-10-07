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
                                           placeholder="Примітка...">
                                @else
                                    {{ $item->note ?: '-' }}
                                @endif
                            </td>
                            <td>
                                @if($inventory->status === 'in_progress')
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            onclick="saveItem({{ $item->id }})"
                                            title="Зберегти">
                                        <i class="bi bi-check"></i>
                                    </button>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($inventory->status === 'in_progress')
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('warehouse-inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
                <div>
                    <button type="button" class="btn btn-warning me-2" onclick="saveProgress()">
                        <i class="bi bi-save"></i> Зберегти прогрес
                    </button>
                    <form method="POST" action="{{ route('warehouse-inventory.complete', $inventory) }}" 
                          class="d-inline" onsubmit="return confirm('Завершити інвентаризацію? Залишки будуть оновлені.')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Завершити інвентаризацію
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('warehouse-inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад до списку
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Пошук по позиціях
document.getElementById('searchItems').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    filterRows();
});

// Фільтр по статусу
document.getElementById('filterStatus').addEventListener('change', function() {
    filterRows();
});

// Фільтр по філії
document.getElementById('filterBranch').addEventListener('change', function() {
    filterRows();
});

function filterRows() {
    const searchTerm = document.getElementById('searchItems').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const branchFilter = document.getElementById('filterBranch').value;
    const rows = document.querySelectorAll('.inventory-row');
    
    rows.forEach(row => {
        const itemName = row.dataset.itemName;
        const itemCode = row.dataset.itemCode;
        const branchName = row.dataset.branchName;
        const branchId = row.dataset.branchId;
        const difference = parseInt(row.querySelector('.difference-badge').dataset.difference);
        
        let showRow = true;
        
        // Пошук
        if (searchTerm && !(itemName.includes(searchTerm) || itemCode.includes(searchTerm) || branchName.includes(searchTerm))) {
            showRow = false;
        }
        
        // Фільтр по статусу
        if (statusFilter === 'unchanged' && difference !== 0) {
            showRow = false;
        } else if (statusFilter === 'changed' && difference === 0) {
            showRow = false;
        } else if (statusFilter === 'discrepancy' && difference === 0) {
            showRow = false;
        }
        
        // Фільтр по філії
        if (branchFilter && branchId !== branchFilter) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Розрахунок різниці
function calculateDifference(input) {
    const row = input.closest('tr');
    const systemQuantity = parseInt(input.dataset.system);
    const actualQuantity = parseInt(input.value) || 0;
    const difference = actualQuantity - systemQuantity;
    
    const badge = row.querySelector('.difference-badge');
    badge.dataset.difference = difference;
    
    if (difference > 0) {
        badge.className = 'badge bg-success difference-badge';
        badge.textContent = '+' + difference;
    } else if (difference < 0) {
        badge.className = 'badge bg-danger difference-badge';
        badge.textContent = difference;
    } else {
        badge.className = 'badge bg-light text-dark difference-badge';
        badge.textContent = '0';
    }
    
    updateStatistics();
}

// Збереження окремої позиції
function saveItem(itemId) {
    const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
    const actualQuantity = row.querySelector('.actual-quantity').value;
    const note = row.querySelector('.item-note').value;
    
    fetch(`{{ route('warehouse-inventory.index') }}/${{{ $inventory->id }}}/items/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            actual_quantity: actualQuantity,
            note: note
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', 'Позицію збережено');
            calculateDifference(row.querySelector('.actual-quantity'));
        } else {
            showNotification('error', 'Помилка збереження');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Помилка збереження');
    });
}

// Збереження прогресу (всі змінені позиції)
function saveProgress() {
    const rows = document.querySelectorAll('.inventory-row');
    const updates = [];
    
    rows.forEach(row => {
        const itemId = row.dataset.itemId;
        const actualQuantity = row.querySelector('.actual-quantity');
        const note = row.querySelector('.item-note');
        
        if (actualQuantity && note) {
            updates.push({
                id: itemId,
                actual_quantity: actualQuantity.value,
                note: note.value
            });
        }
    });
    
    if (updates.length === 0) {
        showNotification('warning', 'Немає змін для збереження');
        return;
    }
    
    // Показуємо прогрес
    const progressBtn = event.target;
    progressBtn.disabled = true;
    progressBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Збереження...';
    
    Promise.all(updates.map(item => 
        fetch(`{{ route('warehouse-inventory.index') }}/${{{ $inventory->id }}}/items/${item.id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                actual_quantity: item.actual_quantity,
                note: item.note
            })
        })
    ))
    .then(() => {
        showNotification('success', `Збережено ${updates.length} позицій`);
        progressBtn.disabled = false;
        progressBtn.innerHTML = '<i class="bi bi-save"></i> Зберегти прогрес';
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Помилка збереження');
        progressBtn.disabled = false;
        progressBtn.innerHTML = '<i class="bi bi-save"></i> Зберегти прогрес';
    });
}

// Заповнити всі з системних даних
function fillAllFromSystem() {
    if (!confirm('Заповнити всі позиції системними даними? Поточні зміни будуть втрачені.')) {
        return;
    }
    
    const rows = document.querySelectorAll('.inventory-row');
    rows.forEach(row => {
        const actualQuantity = row.querySelector('.actual-quantity');
        if (actualQuantity) {
            const systemQuantity = actualQuantity.dataset.system;
            actualQuantity.value = systemQuantity;
            calculateDifference(actualQuantity);
        }
    });
    
    updateStatistics();
    showNotification('success', 'Всі позиції заповнені системними даними');
}

// Оновлення статистики
function updateStatistics() {
    const rows = document.querySelectorAll('.inventory-row:not([style*="display: none"])');
    let noDiscrepancy = 0;
    let withDiscrepancy = 0;
    
    rows.forEach(row => {
        const difference = parseInt(row.querySelector('.difference-badge').dataset.difference);
        if (difference === 0) {
            noDiscrepancy++;
        } else {
            withDiscrepancy++;
        }
    });
    
    document.getElementById('noDiscrepancyCount').textContent = noDiscrepancy;
    document.getElementById('discrepancyCount').textContent = withDiscrepancy;
}

// Показ сповіщень
function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 
                      type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 3000);
}

// Автозбереження кожні 2 хвилини
let autoSaveInterval = setInterval(function() {
    const inProgress = '{{ $inventory->status }}' === 'in_progress';
    if (inProgress) {
        saveProgress();
    }
}, 120000); // 2 хвилини

// Очистка інтервалу при виході
window.addEventListener('beforeunload', function() {
    clearInterval(autoSaveInterval);
});
</script>
@endpush