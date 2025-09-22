{{-- resources/views/warehouse-inventory/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Створити інвентаризацію')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Створити нову інвентаризацію складу</h4>
                <p class="text-muted">Оберіть товари для інвентаризації та встановіть дату проведення</p>
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
                        <label for="searchItems" class="form-label">Пошук товарів</label>
                        <input type="text" id="searchItems" class="form-control" 
                               placeholder="Введіть назву або код товару для пошуку...">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label">Примітки до інвентаризації</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                              rows="3" placeholder="Загальні примітки до проведення інвентаризації">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Оберіть товари для інвентаризації</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllVisible()">
                                <i class="bi bi-check-all"></i> Обрати всі видимі
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllSelected()">
                                <i class="bi bi-x"></i> Очистити вибір
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
                                    <th>Назва товару</th>
                                    <th>Категорія</th>
                                    <th>Поточний залишок</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                @foreach($warehouseItems as $item)
                                <tr class="item-row" 
                                    data-item-name="{{ strtolower($item->name) }}" 
                                    data-item-code="{{ strtolower($item->code) }}"
                                    data-item-category="{{ strtolower($item->category ?? '') }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input item-checkbox" 
                                               name="items[]" value="{{ $item->id }}" 
                                               onchange="updateSelectedCount()">
                                    </td>
                                    <td><code>{{ $item->code }}</code></td>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        @if($item->description)
                                            <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->category)
                                            <span class="badge bg-light text-dark">{{ $item->category }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->isLowStock() ? 'bg-warning' : 'bg-success' }}">
                                            {{ $item->quantity }} {{ $item->unit }}
                                        </span>
                                        @if($item->isLowStock())
                                            <i class="bi bi-exclamation-triangle text-warning ms-1" title="Низький залишок"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->is_active)
                                            <span class="badge bg-success">Активний</span>
                                        @else
                                            <span class="badge bg-secondary">Неактивний</span>
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
// Поиск по товарам
document.getElementById('searchItems').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.item-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const itemName = row.dataset.itemName;
        const itemCode = row.dataset.itemCode;
        const itemCategory = row.dataset.itemCategory;
        
        if (itemName.includes(searchTerm) || 
            itemCode.includes(searchTerm) || 
            itemCategory.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
            // Снимаем выделение с скрытых элементов
            const checkbox = row.querySelector('.item-checkbox');
            if (checkbox.checked) {
                checkbox.checked = false;
            }
        }
    });
    
    updateSelectedCount();
    updateMainCheckbox();
});

// Выбор всех видимых товаров
function selectAllVisible() {
    const visibleCheckboxes = document.querySelectorAll('.item-row:not([style*="display: none"]) .item-checkbox');
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
    updateMainCheckbox();
}

// Очистка всех выборов
function clearAllSelected() {
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedCount();
    updateMainCheckbox();
}

// Переключение всех видимых через главный чекбокс
function toggleAllVisible() {
    const mainCheckbox = document.getElementById('selectAllCheckbox');
    const visibleCheckboxes = document.querySelectorAll('.item-row:not([style*="display: none"]) .item-checkbox');
    
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = mainCheckbox.checked;
    });
    
    updateSelectedCount();
}

// Обновление счетчика выбранных товаров
function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('.item-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = selectedCount + ' обрано';
    
    // Активируем кнопку отправки только если выбран хотя бы один товар
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = selectedCount === 0;
    
    updateMainCheckbox();
}

// Обновление состояния главного чекбокса
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

// Быстрый выбор товаров с низкими остатками
function selectLowStockItems() {
    document.querySelectorAll('.item-row').forEach(row => {
        const warningBadge = row.querySelector('.badge.bg-warning');
        if (warningBadge) {
            const checkbox = row.querySelector('.item-checkbox');
            checkbox.checked = true;
        }
    });
    updateSelectedCount();
}

// Быстрый выбор по категории
function selectByCategory(category) {
    const searchInput = document.getElementById('searchItems');
    searchInput.value = category;
    searchInput.dispatchEvent(new Event('input'));
    
    setTimeout(() => {
        selectAllVisible();
    }, 100);
}

// Горячие клавиши
document.addEventListener('keydown', function(e) {
    // Ctrl+A - выбрать все видимые товары
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        selectAllVisible();
    }
    
    // Escape - очистить поиск
    if (e.key === 'Escape') {
        document.getElementById('searchItems').value = '';
        document.getElementById('searchItems').dispatchEvent(new Event('input'));
    }
    
    // Ctrl+L - выбрать товары с низкими остатками
    if (e.ctrlKey && e.key === 'l') {
        e.preventDefault();
        selectLowStockItems();
    }
});

// Предупреждение перед уходом со страницы
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

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    
    // Добавляем подсказки для быстрых действий
    const helpText = document.createElement('div');
    helpText.className = 'mt-3 text-muted small';
    helpText.innerHTML = `
        <div class="d-flex flex-wrap gap-3">
            <span><kbd>Ctrl+A</kbd> - обрати всі видимі</span>
            <span><kbd>Ctrl+L</kbd> - обрати товари з низькими залишками</span>
            <span><kbd>Esc</kbd> - очистити пошук</span>
        </div>
    `;
    
    document.querySelector('.table-responsive').parentNode.appendChild(helpText);
});

// Добавляем функцию для быстрого создания инвентаризации с низкими остатками
function createLowStockInventory() {
    if (confirm('Створити інвентаризацію тільки для товарів з низькими залишками?')) {
        clearAllSelected();
        selectLowStockItems();
        
        if (document.querySelectorAll('.item-checkbox:checked').length === 0) {
            alert('Товарів з низькими залишками не знайдено');
        }
    }
}
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

.form-check-input:checked + * {
    font-weight: 600;
}

/* Стили для индикатора выбора */
#selectedCount {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
}

/* Адаптивность для мобильных устройств */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
}

/* Выделение строк с низкими остатками */
.item-row:has(.badge.bg-warning) {
    background-color: rgba(255, 193, 7, 0.1);
}

/* Анимация для счетчика */
#selectedCount {
    transition: all 0.3s ease;
}

#selectedCount:not(:empty) {
    animation: pulse 0.5s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Стили для клавиатурных подсказок */
kbd {
    background-color: #e9ecef;
    border: 1px solid #adb5bd;
    border-radius: 0.25rem;
    color: #495057;
    font-size: 0.75rem;
    padding: 0.125rem 0.25rem;
}
</style>
@endpus