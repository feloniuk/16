{{-- resources/views/warehouse-inventory/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Інвентаризація ')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Інвентаризація {{ $inventory->inventory_number }}</h4>
                    <p class="text-muted mb-0">
                        Дата: {{ $inventory->inventory_date ? $inventory->inventory_date->format('d.m.Y') : 'Не вказано' }} | 
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
            
            @if($inventory->notes)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Примітки до інвентаризації</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $inventory->notes }}</p>
                    </div>
                </div>
            @endif
            
            <!-- Статистика -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="fs-4 fw-bold">{{ $inventory->items->count() }}</div>
                        <small class="text-muted">Всього товарів</small>
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
                        <div class="fs-4 fw-bold text-info" id="progressPercent">
                            {{ $inventory->items->where('actual_quantity', '!=', 'system_quantity')->count() > 0 
                               ? round(($inventory->items->where('actual_quantity', '!=', 'system_quantity')->count() / $inventory->items->count()) * 100) 
                               : 0 }}%
                        </div>
                        <small class="text-muted">Прогрес</small>
                    </div>
                </div>
            </div>
            
            <!-- Фильтры и поиск -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="searchItems" class="form-label">Пошук товарів</label>
                    <input type="text" id="searchItems" class="form-control" 
                           placeholder="Назва або код товару...">
                </div>
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Статус</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">Всі товари</option>
                        <option value="unchanged">Без змін</option>
                        <option value="changed">Зі змінами</option>
                        <option value="discrepancy">З розбіжностями</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Швидкі дії</label>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="fillAllFromSystem()">
                            <i class="bi bi-arrow-repeat"></i> Заповнити з системи
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Збереження</label>
                    <div>
                        <button type="button" class="btn btn-sm btn-success" onclick="saveProgress()" id="saveBtn">
                            <i class="bi bi-save"></i> Зберегти
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Таблица товаров -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="25%">Товар</th>
                            <th width="10%">В системі</th>
                            <th width="15%">Фактично</th>
                            <th width="10%">Різниця</th>
                            <th width="25%">Примітка</th>
                            <th width="15%">Дії</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        @foreach($inventory->items as $item)
                        <tr class="inventory-row" data-item-id="{{ $item->id }}" 
                            data-item-name="{{ strtolower($item->warehouseItem->name) }}"
                            data-item-code="{{ strtolower($item->warehouseItem->code) }}">
                            <td>
                                <div>
                                    <strong>{{ $item->warehouseItem->name }}</strong>
                                    <br><small class="text-muted">{{ $item->warehouseItem->code }}</small>
                                    @if($item->warehouseItem->category)
                                        <br><span class="badge bg-light text-dark">{{ $item->warehouseItem->category }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info system-quantity">{{ $item->system_quantity }}</span>
                                <small class="text-muted d-block">{{ $item->warehouseItem->unit }}</small>
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
                                    <span class="badge bg-secondary">{{ $item->actual_quantity }} {{ $item->warehouseItem->unit }}</span>
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
                                           placeholder="Примітка..."
                                           style="min-width: 200px;">
                                @else
                                    {{ $item->note ?: '-' }}
                                @endif
                            </td>
                            <td>
                                @if($inventory->status === 'in_progress')
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                onclick="setFromSystem(this)" title="Взяти з системи">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="setZero(this)" title="Встановити 0">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="saveItem(this)" title="Зберегти">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </div>
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
                        <i class="bi bi-arrow-left"></i> Назад до списку
                    </a>
                    <div>
                        <button type="button" class="btn btn-warning me-2" onclick="saveProgress()">
                            <i class="bi bi-save"></i> Зберегти прогрес
                        </button>
                        <button type="button" class="btn btn-success" onclick="completeInventory()">
                            <i class="bi bi-check-circle"></i> Завершити інвентаризацію
                        </button>
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
// Поиск по товарам
document.getElementById('searchItems').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.inventory-row');
    
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

// Фильтр по статусу
document.getElementById('filterStatus').addEventListener('change', function() {
    const filterValue = this.value;
    const rows = document.querySelectorAll('.inventory-row');
    
    rows.forEach(row => {
        const difference = parseInt(row.querySelector('.difference-badge').dataset.difference);
        let show = true;
        
        switch(filterValue) {
            case 'unchanged':
                show = difference === 0;
                break;
            case 'changed':
                show = difference !== 0;
                break;
            case 'discrepancy':
                show = Math.abs(difference) > 0;
                break;
            default:
                show = true;
        }
        
        row.style.display = show ? '' : 'none';
    });
});

// Расчет разности
function calculateDifference(input) {
    const systemQuantity = parseInt(input.dataset.system);
    const actualQuantity = parseInt(input.value) || 0;
    const difference = actualQuantity - systemQuantity;
    
    const badge = input.closest('tr').querySelector('.difference-badge');
    badge.dataset.difference = difference;
    
    if (difference > 0) {
        badge.innerHTML = '<span class="badge bg-success">+' + difference + '</span>';
    } else if (difference < 0) {
        badge.innerHTML = '<span class="badge bg-danger">' + difference + '</span>';
    } else {
        badge.innerHTML = '<span class="badge bg-light text-dark">0</span>';
    }
    
    updateStatistics();
    markAsChanged(input.closest('tr'));
}

// Установить значение из системы
function setFromSystem(button) {
    const row = button.closest('tr');
    const systemQuantity = row.querySelector('.system-quantity').textContent;
    const actualInput = row.querySelector('.actual-quantity');
    
    actualInput.value = systemQuantity;
    calculateDifference(actualInput);
}

// Установить ноль
function setZero(button) {
    const row = button.closest('tr');
    const actualInput = row.querySelector('.actual-quantity');
    
    actualInput.value = 0;
    calculateDifference(actualInput);
}

// Заполнить все из системы
function fillAllFromSystem() {
    if (confirm('Заповнити всі фактичні кількості з системних залишків?')) {
        document.querySelectorAll('.actual-quantity').forEach(input => {
            const systemQuantity = input.dataset.system;
            input.value = systemQuantity;
            calculateDifference(input);
        });
    }
}

// Сохранить отдельный товар
function saveItem(button) {
    const row = button.closest('tr');
    const itemId = row.dataset.itemId;
    const actualQuantity = row.querySelector('.actual-quantity').value;
    const note = row.querySelector('.item-note').value;
    
    const inventoryId = {{ $inventory->id }};
    const url = `/warehouse-inventory/${inventoryId}/items/${itemId}`;
    
    fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            actual_quantity: parseInt(actualQuantity),
            note: note
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Товар збережено', 'success');
            row.classList.add('table-success');
            setTimeout(() => row.classList.remove('table-success'), 2000);
        } else {
            showToast('Помилка збереження', 'error');
        }
    })
    .catch(error => {
        showToast('Помилка мережі', 'error');
        console.error('Error:', error);
    });
}

// Сохранить весь прогресс
function saveProgress() {
    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-spinner spin"></i> Збереження...';
    
    const inventoryId = {{ $inventory->id }};
    const promises = [];
    
    document.querySelectorAll('.inventory-row').forEach(row => {
        const itemId = row.dataset.itemId;
        const actualQuantity = row.querySelector('.actual-quantity').value;
        const note = row.querySelector('.item-note').value;
        
        const url = `/warehouse-inventory/${inventoryId}/items/${itemId}`;
        
        const promise = fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                actual_quantity: parseInt(actualQuantity),
                note: note
            })
        });
        
        promises.push(promise);
    });
    
    Promise.all(promises)
        .then(() => {
            showToast('Прогрес збережено', 'success');
            hasUnsavedChanges = false;
        })
        .catch(error => {
            showToast('Помилка збереження', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-save"></i> Зберегти';
        });
}

// Завершить инвентаризацию
function completeInventory() {
    if (confirm('Завершити інвентаризацію? Залишки товарів будуть оновлені згідно з фактичними даними. Ця дія незворотна!')) {
        // Сначала сохраняем прогресс
        saveProgress();
        
        // Затем завершаем инвентаризацию
        setTimeout(() => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/warehouse-inventory/{{ $inventory->id }}/complete`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }, 1000);
    }
}

// Обновление статистики
function updateStatistics() {
    const allRows = document.querySelectorAll('.inventory-row');
    let noDiscrepancy = 0;
    let withDiscrepancy = 0;
    
    allRows.forEach(row => {
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

// Отметить строку как измененную
function markAsChanged(row) {
    row.classList.add('table-warning');
    setTimeout(() => row.classList.remove('table-warning'), 1000);
}

// Показать уведомление
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

// Создать контейнер для уведомлений
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1050';
    document.body.appendChild(container);
    return container;
}

// Автосохранение каждые 2 минуты
setInterval(function() {
    if (document.querySelectorAll('.inventory-row').length > 0) {
        saveProgress();
    }
}, 120000);

// Предупреждение перед закрытием страницы
let hasUnsavedChanges = false;

document.addEventListener('change', function(e) {
    if (e.target.matches('.actual-quantity, .item-note')) {
        hasUnsavedChanges = true;
    }
});

window.addEventListener('beforeunload', function(e) {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Горячие клавиши
document.addEventListener('keydown', function(e) {
    // Ctrl+S - сохранить прогресс
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        saveProgress();
    }
    
    // Ctrl+Enter - завершить инвентаризацию
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        completeInventory();
    }
    
    // Escape - очистить поиск
    if (e.key === 'Escape') {
        document.getElementById('searchItems').value = '';
        document.getElementById('searchItems').dispatchEvent(new Event('input'));
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterStatus').dispatchEvent(new Event('change'));
    }
});

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    // Добавляем мета-тег для CSRF токена если его нет
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.head.appendChild(meta);
    }
    
    // Добавляем подсказки по горячим клавишам
    const helpText = document.createElement('div');
    helpText.className = 'mt-3 text-muted small';
    helpText.innerHTML = `
        <div class="d-flex flex-wrap gap-3">
            <span><kbd>Ctrl+S</kbd> - зберегти прогрес</span>
            <span><kbd>Ctrl+Enter</kbd> - завершити інвентаризацію</span>
            <span><kbd>Esc</kbd> - очистити фільтри</span>
        </div>
    `;
    
    document.querySelector('.table-responsive').parentNode.appendChild(helpText);
    
    // Фокус на первое поле ввода
    const firstInput = document.querySelector('.actual-quantity');
    if (firstInput) {
        firstInput.focus();
    }
    
    updateStatistics();
});

// Функция для копирования значений между полями (полезно для похожих товаров)
function copyValue(sourceInput, targetSelector) {
    const value = sourceInput.value;
    const note = sourceInput.closest('tr').querySelector('.item-note').value;
    
    document.querySelectorAll(targetSelector).forEach(target => {
        if (target !== sourceInput) {
            target.value = value;
            calculateDifference(target);
            
            const targetNote = target.closest('tr').querySelector('.item-note');
            if (targetNote && !targetNote.value) {
                targetNote.value = note;
            }
        }
    });
}

// Функция для массового обновления по фильтру
function bulkUpdate(filterFn, updateFn) {
    document.querySelectorAll('.inventory-row').forEach(row => {
        if (filterFn(row)) {
            updateFn(row);
        }
    });
    updateStatistics();
}

// Примеры использования массового обновления:
function setBulkZeroForCategory(category) {
    bulkUpdate(
        row => row.textContent.toLowerCase().includes(category.toLowerCase()),
        row => {
            const input = row.querySelector('.actual-quantity');
            input.value = 0;
            calculateDifference(input);
        })
    }

    function setBulkSystemForLowStock() {
        bulkUpdate(
            row => row.querySelector('.badge.bg-warning') !== null,
            row => setFromSystem(row.querySelector('button'))
        );
    }
</script>

<style>
.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-group .btn-sm {
    padding: 0.25rem 0.5rem;
}

.inventory-row {
    transition: background-color 0.3s ease;
}

.inventory-row.table-warning {
    background-color: rgba(255, 193, 7, 0.25) !important;
}

.inventory-row.table-success {
    background-color: rgba(25, 135, 84, 0.25) !important;
}

/* Стили для анимации загрузки */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spin {
    animation: spin 1s linear infinite;
}

/* Улучшенные стили для badge */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.5rem;
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

/* Стили для прогресс-индикатора */
.progress-indicator {
    height: 4px;
    background: linear-gradient(to right, #28a745 var(--progress, 0%), #f8f9fa var(--progress, 0%));
    border-radius: 2px;
    margin-bottom: 1rem;
}

/* Адаптивность */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        padding: 0.125rem 0.25rem;
    }
    
    .form-control-sm {
        min-width: 60px;
    }
}

/* Стили для выделения строк с расхождениями */
.inventory-row:has(.badge.bg-danger) {
    border-left: 4px solid #dc3545;
}

.inventory-row:has(.badge.bg-success) {
    border-left: 4px solid #198754;
}

/* Hover эффекты */
.inventory-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Стили для toast контейнера */
.toast-container {
    z-index: 1050;
}

.toast {
    margin-bottom: 0.5rem;
}
</style>
@endpush

