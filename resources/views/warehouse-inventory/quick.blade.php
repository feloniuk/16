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
                                        <th>Поточний залишок</th>
                                        <th>Фактична кількість</th>
                                        <th>Різниця</th>
                                        <th>Примітка</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @foreach($items as $item)
                                    <tr class="item-row" data-item-name="{{ strtolower($item->name) }}" data-item-code="{{ strtolower($item->code) }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input item-checkbox" 
                                                   value="{{ $item->id }}" onchange="toggleItem(this, {{ $item->id }})">
                                        </td>
                                        <td><code>{{ $item->code }}</code></td>
                                        <td>
                                            <strong>{{ $item->name }}</strong>
                                            @if($item->category)
                                                <br><small class="text-muted">{{ $item->category }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info system-quantity">{{ $item->quantity }}</span>
                                            <span class="small text-muted">{{ $item->unit }}</span>
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
        
        // Добавляем скрытые поля в форму
        addHiddenField('items[' + selectedItems.size + '][id]', itemId);
        quantityInput.name = 'items[' + Array.from(selectedItems).indexOf(itemId) + '][actual_quantity]';
        noteInput.name = 'items[' + Array.from(selectedItems).indexOf(itemId) + '][note]';
    } else {
        selectedItems.delete(itemId);
        quantityInput.disabled = true;
        noteInput.disabled = true;
        quantityInput.name = '';
        noteInput.name = '';
        row.classList.remove('table-primary');
        
        // Удаляем скрытое поле
        removeHiddenField(itemId);
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

// Добавление скрытого поля
function addHiddenField(name, value) {
    const hiddenField = document.createElement('input');
    hiddenField.type = 'hidden';
    hiddenField.name = name;
    hiddenField.value = value;
    hiddenField.dataset.itemId = value;
    document.getElementById('inventoryForm').appendChild(hiddenField);
}

// Удаление скрытого поля
function removeHiddenField(itemId) {
    const hiddenField = document.querySelector(`input[data-item-id="${itemId}"]`);
    if (hiddenField) {
        hiddenField.remove();
    }
}

// Пересчет имен полей после изменений
function recalculateFieldNames() {
    const selectedItemsArray = Array.from(selectedItems);
    
    selectedItemsArray.forEach((itemId, index) => {
        const checkbox = document.querySelector(`.item-checkbox[value="${itemId}"]`);
        const row = checkbox.closest('tr');
        const quantityInput = row.querySelector('.actual-quantity');
        const noteInput = row.querySelector('.item-note');
        
        quantityInput.name = `items[${index}][actual_quantity]`;
        noteInput.name = `items[${index}][note]`;
        
        // Обновляем скрытое поле ID
        const hiddenField = document.querySelector(`input[data-item-id="${itemId}"]`);
        if (hiddenField) {
            hiddenField.name = `items[${index}][id]`;
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

// Автосохранение в localStorage для восстановления данных
function saveToLocalStorage() {
    const formData = {
        selectedItems: Array.from(selectedItems),
        timestamp: Date.now()
    };
    
    selectedItems.forEach(itemId => {
        const checkbox = document.querySelector(`.item-checkbox[value="${itemId}"]`);
        const row = checkbox.closest('tr');
        const quantityInput = row.querySelector('.actual-quantity');
        const noteInput = row.querySelector('.item-note');
        
        formData[`quantity_${itemId}`] = quantityInput.value;
        formData[`note_${itemId}`] = noteInput.value;
    });
    
    localStorage.setItem('inventoryFormData', JSON.stringify(formData));
}

// Восстановление данных из localStorage
function restoreFromLocalStorage() {
    const savedData = localStorage.getItem('inventoryFormData');
    if (savedData) {
        try {
            const formData = JSON.parse(savedData);
            
            // Проверяем, что данные не старше 1 часа
            if (Date.now() - formData.timestamp < 3600000) {
                const shouldRestore = confirm('Знайдені збережені дані інвентаризації. Відновити їх?');
                
                if (shouldRestore) {
                    formData.selectedItems.forEach(itemId => {
                        const checkbox = document.querySelector(`.item-checkbox[value="${itemId}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                            toggleItem(checkbox, itemId);
                            
                            const row = checkbox.closest('tr');
                            const quantityInput = row.querySelector('.actual-quantity');
                            const noteInput = row.querySelector('.item-note');
                            
                            quantityInput.value = formData[`quantity_${itemId}`] || quantityInput.dataset.system;
                            noteInput.value = formData[`note_${itemId}`] || '';
                            
                            calculateDifference(quantityInput);
                        }
                    });
                }
            }
        } catch (e) {
            console.error('Помилка відновлення данних:', e);
        }
    }
}

// Очистка localStorage при успешной отправке
document.getElementById('inventoryForm').addEventListener('submit', function() {
    localStorage.removeItem('inventoryFormData');
});

// Автосохранение каждые 30 секунд
setInterval(function() {
    if (selectedItems.size > 0) {
        saveToLocalStorage();
    }
}, 30000);

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    restoreFromLocalStorage();
    
    // Добавляем обработчики для автоматического расчета разности
    document.querySelectorAll('.actual-quantity').forEach(input => {
        input.addEventListener('input', function() {
            calculateDifference(this);
        });
    });
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

#selectedCount {
    font-size: 1.5rem;
    font-weight: bold;
    color: #0d6efd;
}

.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Адаптивность для мобильных устройств */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .form-control-sm {
        padding: 0.125rem 0.25rem;
        font-size: 0.75rem;
    }
}

/* Анимация для выбранных строк */
.item-row {
    transition: background-color 0.3s ease;
}

.item-row.table-primary {
    animation: highlightRow 0.5s ease-in-out;
}

@keyframes highlightRow {
    0% { background-color: transparent; }
    50% { background-color: rgba(13, 110, 253, 0.3); }
    100% { background-color: rgba(13, 110, 253, 0.1); }
}

/* Стилизация для количества товаров с расхождениями */
.badge.bg-success::before {
    content: '📈 ';
}

.badge.bg-danger::before {
    content: '📉 ';
}

.badge.bg-light::before {
    content: '✅ ';
}
</style>
@endpush