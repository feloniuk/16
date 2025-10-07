{{-- resources/views/warehouse-inventory/quick.blade.php --}}
@extends('layouts.app')

@section('title', 'Швидка інвентаризація')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Швидка інвентаризація складу</h4>
                <p class="text-muted">Оберіть товари та вкажіть фактичну кількість для швидкої інвентаризації</p>
            </div>
            
            <form method="POST" action="{{ route('warehouse-inventory.process-quick') }}" id="inventoryForm">
                @csrf
                
                <!-- Поиск и фильтр -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="searchItems" class="form-label">Пошук товарів</label>
                        <input type="text" id="searchItems" class="form-control" placeholder="Введіть назву або код товару...">
                    </div>
                    <div class="col-md-3">
                        <label for="selectAll" class="form-label">Дії з усіма</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllItems()">
                                Обрати всі
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllItems()">
                                Очистити
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Обрано товарів</label>
                        <div class="fs-5 fw-bold text-primary" id="selectedCount">0</div>
                    </div>
                </div>

                <!-- Список товаров -->
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onchange="toggleAllItems()">
                                        </th>
                                        <th>Код</th>
                                        <th>Назва товару</th>
                                        <th>Філія</th>
                                        <th>Поточний залишок</th>
                                        <th>Фактична кількість</th>
                                        <th>Різниця</th>
                                        <th>Примітка</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @foreach($items as $item)
                                    <tr class="item-row" 
                                        data-item-name="{{ strtolower($item->equipment_type) }}" 
                                        data-item-code="{{ strtolower($item->inventory_number) }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input item-checkbox" 
                                                   value="{{ $item->id }}" onchange="toggleItem(this, {{ $item->id }})">
                                        </td>
                                        <td><code>{{ $item->inventory_number }}</code></td>
                                        <td>
                                            <strong>{{ $item->equipment_type }}</strong>
                                            @if($item->category)
                                                <br><small class="text-muted">{{ $item->category }}</small>
                                            @endif
                                            @if($item->brand || $item->model)
                                                <br><small class="text-muted">{{ $item->brand }} {{ $item->model }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->isWarehouseItem() ? 'bg-warning' : 'bg-primary' }}">
                                                {{ $item->branch->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info system-quantity">{{ $item->quantity }}</span>
                                            @if($item->unit)
                                                <span class="small text-muted">{{ $item->unit }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm actual-quantity" 
                                                   min="0" value="{{ $item->quantity }}" 
                                                   data-system="{{ $item->quantity }}"
                                                   onchange="calculateDifference(this)" disabled>
                                        </td>
                                        <td>
                                            <span class="badge difference-badge">0</span>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm item-note" 
                                                   placeholder="Примітка..." disabled>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Общие примечания -->
                <div class="mb-4">
                    <label for="notes" class="form-label">Загальні примітки до інвентаризації</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3" 
                              placeholder="Додаткові примітки до інвентаризації..."></textarea>
                </div>

                <!-- Кнопки -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                        <i class="bi bi-check-circle"></i> Завершити інвентаризацію
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedItems = new Set();

// Поиск по товарам
document.getElementById('searchItems').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.item-row');
    
    rows.forEach(row => {
        const itemName = row.dataset.itemName;
        const itemCode = row.dataset.itemCode;
        
        if (itemName.includes(searchTerm) || itemCode.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Выбор всех товаров
function selectAllItems() {
    const visibleCheckboxes = document.querySelectorAll('.item-row:not([style*="display: none"]) .item-checkbox');
    visibleCheckboxes.forEach(checkbox => {
        if (!checkbox.checked) {
            checkbox.checked = true;
            toggleItem(checkbox, checkbox.value);
        }
    });
}

// Очистка всех выборов
function clearAllItems() {
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        if (checkbox.checked) {
            checkbox.checked = false;
            toggleItem(checkbox, checkbox.value);
        }
    });
}

// Переключение всех через главный чекбокс
function toggleAllItems() {
    const mainCheckbox = document.getElementById('selectAllCheckbox');
    const visibleCheckboxes = document.querySelectorAll('.item-row:not([style*="display: none"]) .item-checkbox');
    
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = mainCheckbox.checked;
        toggleItem(checkbox, checkbox.value);
    });
}

// Переключение отдельного товара
function toggleItem(checkbox, itemId) {
    const row = checkbox.closest('tr');
    const quantityInput = row.querySelector('.actual-quantity');
    const noteInput = row.querySelector('.item-note');
    
    if (checkbox.checked) {
        selectedItems.add(itemId);
        quantityInput.disabled = false;
        noteInput.disabled = false;
        row.classList.add('table-primary');
        
        // Создаем скрытые поля для формы
        const index = Array.from(selectedItems).indexOf(itemId);
        quantityInput.name = `items[${index}][actual_quantity]`;
        noteInput.name = `items[${index}][note]`;
        
        // Добавляем скрытое поле ID
        if (!row.querySelector('input[name*="[id]"]')) {
            const hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = `items[${index}][id]`;
            hiddenId.value = itemId;
            hiddenId.className = 'hidden-id-field';
            row.appendChild(hiddenId);
        }
    } else {
        selectedItems.delete(itemId);
        quantityInput.disabled = true;
        noteInput.disabled = true;
        quantityInput.name = '';
        noteInput.name = '';
        row.classList.remove('table-primary');
        
        // Удаляем скрытое поле
        const hiddenField = row.querySelector('.hidden-id-field');
        if (hiddenField) {
            hiddenField.remove();
        }
    }
    
    updateSelectedCount();
    updateSubmitButton();
    recalculateFieldNames();
}

// Пересчет разности
function calculateDifference(input) {
    const systemQuantity = parseInt(input.dataset.system);
    const actualQuantity = parseInt(input.value) || 0;
    const difference = actualQuantity - systemQuantity;
    
    const badge = input.closest('tr').querySelector('.difference-badge');
    badge.textContent = difference;
    
    if (difference > 0) {
        badge.className = 'badge bg-success';
        badge.textContent = '+' + difference;
    } else if (difference < 0) {
        badge.className = 'badge bg-danger';
        badge.textContent = difference;
    } else {
        badge.className = 'badge bg-light text-dark';
        badge.textContent = '0';
    }
}

// Пересчет имен полей после изменений
function recalculateFieldNames() {
    const selectedItemsArray = Array.from(selectedItems);
    
    selectedItemsArray.forEach((itemId, index) => {
        const checkbox = document.querySelector(`.item-checkbox[value="${itemId}"]`);
        if (!checkbox) return;
        
        const row = checkbox.closest('tr');
        const quantityInput = row.querySelector('.actual-quantity');
        const noteInput = row.querySelector('.item-note');
        const hiddenId = row.querySelector('.hidden-id-field');
        
        if (quantityInput) quantityInput.name = `items[${index}][actual_quantity]`;
        if (noteInput) noteInput.name = `items[${index}][note]`;
        if (hiddenId) {
            hiddenId.name = `items[${index}][id]`;
        } else {
            // Создаем если нет
            const newHiddenId = document.createElement('input');
            newHiddenId.type = 'hidden';
            newHiddenId.name = `items[${index}][id]`;
            newHiddenId.value = itemId;
            newHiddenId.className = 'hidden-id-field';
            row.appendChild(newHiddenId);
        }
    });
}

// Обновление счетчика выбранных товаров
function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = selectedItems.size;
}

// Обновление состояния кнопки отправки
function updateSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = selectedItems.size === 0;
}

// Предупреждение перед уходом со страницы
let formChanged = false;

document.getElementById('inventoryForm').addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged && selectedItems.size > 0) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Горячие клавиши
document.addEventListener('keydown', function(e) {
    // Ctrl+A - выбрать все видимые товары
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        selectAllItems();
    }
    
    // Ctrl+S - сохранить (отправить форму)
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        if (selectedItems.size > 0) {
            document.getElementById('inventoryForm').submit();
        }
    }
    
    // Escape - очистить поиск
    if (e.key === 'Escape') {
        document.getElementById('searchItems').value = '';
        document.getElementById('searchItems').dispatchEvent(new Event('input'));
    }
});
</script>

<style>
.table-responsive {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

.item-row.table-primary .actual-quantity,
.item-row.table-primary .item-note {
    background-color: rgba(13, 110, 253, 0.1);
    border-color: #0d6efd;
}

.difference-badge {
    min-width: 40px;
    display: inline-block;
    text-align: center;
}
</style>
@endpush