{{-- resources/views/warehouse-inventory/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Створити інвентаризацію')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Створити нову інвентаризацію</h4>
                <p class="text-muted">Оберіть товари та обладнання для інвентаризації</p>
            </div>
            
            <form method="POST" action="{{ route('warehouse-inventory.store') }}" id="inventoryForm">
                @csrf
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="inventory_date" class="form-label">Дата інвентаризації <span class="text-danger">*</span></label>
                        <input type="date" name="inventory_date" id="inventory_date" 
                               class="form-control @error('inventory_date') is-invalid @enderror" 
                               value="{{ old('inventory_date', date('Y-m-d')) }}" required>
                        @error('inventory_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="searchItems" class="form-label">Пошук</label>
                        <input type="text" id="searchItems" class="form-control" 
                               placeholder="Введіть назву, код або філію для пошуку...">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label">Примітки до інвентаризації</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                              rows="3" placeholder="Загальні примітки">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Оберіть позиції для інвентаризації</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllVisible()">
                                <i class="bi bi-check-all"></i> Обрати всі видимі
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllSelected()">
                                <i class="bi bi-x"></i> Очистити вибір
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="selectWarehouseOnly()">
                                <i class="bi bi-box-seam"></i> Тільки склад
                            </button>
                            <span class="ms-3 badge bg-info" id="selectedCount">0 обрано</span>
                        </div>
                    </div>
                    
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onchange="toggleAllVisible()">
                                    </th>
                                    <th>Код</th>
                                    <th>Назва</th>
                                    <th>Філія</th>
                                    <th>Кабінет/Категорія</th>
                                    <th>Поточний залишок</th>
                                    <th>Тип</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                @foreach($inventoryItems as $item)
                                <tr class="item-row" 
                                    data-item-name="{{ strtolower($item->equipment_type) }}" 
                                    data-item-code="{{ strtolower($item->inventory_number) }}"
                                    data-branch="{{ strtolower($item->branch->name) }}"
                                    data-is-warehouse="{{ $item->isWarehouseItem() ? '1' : '0' }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input item-checkbox" 
                                               name="items[]" value="{{ $item->id }}" 
                                               onchange="updateSelectedCount()">
                                    </td>
                                    <td><code>{{ $item->inventory_number }}</code></td>
                                    <td>
                                        <strong>{{ $item->equipment_type }}</strong>
                                        @if($item->brand || $item->model)
                                            <br><small class="text-muted">{{ $item->brand }} {{ $item->model }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->isWarehouseItem() ? 'bg-warning' : 'bg-primary' }}">
                                            {{ $item->branch->name }}
                                        </span>
                                    </td>
                                    <td>{{ $item->room_number }}</td>
                                    <td>
                                        @if($item->isWarehouseItem())
                                            <span class="badge {{ $item->isLowStock() ? 'bg-danger' : 'bg-success' }}">
                                                {{ $item->quantity }} {{ $item->unit }}
                                            </span>
                                            @if($item->isLowStock())
                                                <i class="bi bi-exclamation-triangle text-warning ms-1" title="Низький залишок"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">Обладнання</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->isWarehouseItem())
                                            <span class="badge bg-warning">Склад</span>
                                        @else
                                            <span class="badge bg-info">Обладнання</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @error('items')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('warehouse-inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="bi bi-save"></i> Створити інвентаризацію
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Пошук по позиціях
document.getElementById('searchItems').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.item-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const itemName = row.dataset.itemName;
        const itemCode = row.dataset.itemCode;
        const branch = row.dataset.branch;
        
        if (itemName.includes(searchTerm) || 
            itemCode.includes(searchTerm) || 
            branch.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
            const checkbox = row.querySelector('.item-checkbox');
            if (checkbox.checked) {
                checkbox.checked = false;
            }
        }
    });
    
    updateSelectedCount();
    updateMainCheckbox();
});

// Вибір всіх видимих
function selectAllVisible() {
    const visibleCheckboxes = document.querySelectorAll('.item-row:not([style*="display: none"]) .item-checkbox');
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
    updateMainCheckbox();
}

// Вибір тільки складу
function selectWarehouseOnly() {
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    document.querySelectorAll('.item-row[data-is-warehouse="1"]:not([style*="display: none"]) .item-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    
    updateSelectedCount();
    updateMainCheckbox();
}

// Очистка всіх виборів
function clearAllSelected() {
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedCount();
    updateMainCheckbox();
}

// Переключення всіх через головний чекбокс
function toggleAllVisible() {
    const mainCheckbox = document.getElementById('selectAllCheckbox');
    const visibleCheckboxes = document.querySelectorAll('.item-row:not([style*="display: none"]) .item-checkbox');
    
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = mainCheckbox.checked;
    });
    
    updateSelectedCount();
}

// Оновлення лічильника обраних
function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = selectedCount + ' обрано';
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = selectedCount === 0;
    
    updateMainCheckbox();
}

// Оновлення стану головного чекбокса
function updateMainCheckbox() {
    const mainCheckbox = document.getElementById('selectAllCheckbox');
    const visibleCheckboxes = document.querySelectorAll('.item-row:not([style*="display: none"]) .item-checkbox');
    const checkedVisible = document.querySelectorAll('.item-row:not([style*="display: none"]) .item-checkbox:checked');
    
    if (visibleCheckboxes.length === 0) {
        mainCheckbox.indeterminate = false;
        mainCheckbox.checked = false;
    } else if (checkedVisible.length === 0) {
        mainCheckbox.indeterminate = false;
        mainCheckbox.checked = false;
    } else if (checkedVisible.length === visibleCheckboxes.length) {
        mainCheckbox.indeterminate = false;
        mainCheckbox.checked = true;
    } else {
        mainCheckbox.indeterminate = true;
    }
}

// Попередження перед відходом
let formChanged = false;
document.getElementById('inventoryForm').addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    const selectedCount = document.querySelectorAll('.item-checkbox:checked').length;
    if (formChanged && selectedCount > 0) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Ініціалізація
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});

// Гарячі клавіші
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        selectAllVisible();
    }
    if (e.key === 'Escape') {
        document.getElementById('searchItems').value = '';
        document.getElementById('searchItems').dispatchEvent(new Event('input'));
    }
});
</script>

<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
    background: white;
}

.item-row {
    transition: background-color 0.2s ease;
}

.item-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>
@endpush