@extends('layouts.app')

@section('title', 'Додати обладнання')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Додати обладнання в інвентар</h4>
                <p class="text-muted">Оберіть філію та кабінет, потім додайте обладнання</p>
            </div>
            
            <form method="POST" action="{{ route('inventory.store-bulk') }}" id="inventoryForm">
                @csrf
                
                <!-- Основна інформація про локацію -->
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Локація</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="branch_id" class="form-label">Філія <span class="text-danger">*</span></label>
                                <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                    <option value="">Оберіть філію</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="room_number" class="form-label">Номер кабінету <span class="text-danger">*</span></label>
                                <input type="text" name="room_number" id="room_number" 
                                       class="form-control @error('room_number') is-invalid @enderror" 
                                       value="{{ old('room_number') }}" 
                                       placeholder="Наприклад: 101, Кабінет директора"
                                       required>
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Список обладнання -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-laptop"></i> Обладнання в кабінеті</h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-success" onclick="addItemFromTemplate()">
                                    <i class="bi bi-list-ul"></i> З шаблону
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addItemRow()">
                                    <i class="bi bi-plus-lg"></i> Додати обладнання
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Тип обладнання *</th>
                                        <th width="15%">Бренд</th>
                                        <th width="15%">Модель</th>
                                        <th width="15%">Серійний номер</th>
                                        <th width="20%">Інвентарний номер *</th>
                                        <th width="5%">Дії</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <!-- Рядки додаються динамічно -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="p-3 bg-light" id="emptyState">
                            <div class="text-center text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2">Немає доданого обладнання. Натисніть "Додати обладнання" щоб почати.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Додано: <strong id="itemsCount">0</strong> од.
                            </small>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllItems()">
                                    <i class="bi bi-trash"></i> Очистити все
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="duplicateLastItem()">
                                    <i class="bi bi-files"></i> Дублювати останнє
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Загальні примітки -->
                <div class="card mb-4">
                    <div class="card-body">
                        <label for="general_notes" class="form-label">
                            <i class="bi bi-sticky"></i> Загальні примітки (застосуються до всього обладнання)
                        </label>
                        <textarea name="general_notes" id="general_notes" class="form-control" rows="2" 
                                  placeholder="Наприклад: Нове обладнання, встановлено 15.01.2025"></textarea>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                        <i class="bi bi-save"></i> Зберегти обладнання (<span id="submitCount">0</span>)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модалка вибору шаблону -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-list-ul"></i> Обрати з шаблону</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="templateSearch" 
                           placeholder="Пошук шаблону...">
                </div>
                <div class="list-group" id="templateList">
                    @foreach($templates as $template)
                    <button type="button" class="list-group-item list-group-item-action template-item" 
                            data-template="{{ json_encode([
                                'equipment_type' => $template->equipment_type,
                                'brand' => $template->brand,
                                'model' => $template->model,
                                'requires_serial' => $template->requires_serial,
                                'requires_inventory' => $template->requires_inventory
                            ]) }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $template->equipment_type }}</strong>
                                @if($template->brand || $template->model)
                                    <br><small class="text-muted">{{ $template->brand }} {{ $template->model }}</small>
                                @endif
                            </div>
                            <span class="badge bg-primary">Обрати</span>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let itemCounter = 0;

// Додавання нового рядка обладнання
function addItemRow(templateData = null) {
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.dataset.index = itemCounter;
    
    row.innerHTML = `
        <td class="text-center">${itemCounter + 1}</td>
        <td>
            <input type="text" name="items[${itemCounter}][equipment_type]" 
                   class="form-control form-control-sm" 
                   value="${templateData?.equipment_type || ''}"
                   placeholder="Комп'ютер, Принтер..." 
                   required list="equipmentTypeList">
            <datalist id="equipmentTypeList">
                <option value="Комп'ютер">
                <option value="Ноутбук">
                <option value="Принтер">
                <option value="МФУ">
                <option value="Сканер">
                <option value="Монітор">
                <option value="Клавіатура">
                <option value="Миша">
                <option value="ДБЖ">
                <option value="Телефон">
                <option value="Телевізор">
                <option value="Проектор">
            </datalist>
        </td>
        <td>
            <input type="text" name="items[${itemCounter}][brand]" 
                   class="form-control form-control-sm" 
                   value="${templateData?.brand || ''}"
                   placeholder="HP, Dell..." list="brandList">
            <datalist id="brandList">
                <option value="HP">
                <option value="Dell">
                <option value="Lenovo">
                <option value="Asus">
                <option value="Acer">
                <option value="Canon">
                <option value="Epson">
                <option value="Samsung">
                <option value="LG">
            </datalist>
        </td>
        <td>
            <input type="text" name="items[${itemCounter}][model]" 
                   class="form-control form-control-sm" 
                   value="${templateData?.model || ''}"
                   placeholder="Model">
        </td>
        <td>
            <input type="text" name="items[${itemCounter}][serial_number]" 
                   class="form-control form-control-sm" 
                   placeholder="S/N"
                   ${templateData?.requires_serial ? 'required' : ''}>
        </td>
        <td>
            <input type="text" name="items[${itemCounter}][inventory_number]" 
                   class="form-control form-control-sm" 
                   placeholder="INV-00${itemCounter + 1}" 
                   required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" 
                    onclick="removeItemRow(this)" title="Видалити">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    itemCounter++;
    
    updateUI();
    hideEmptyState();
    
    // Фокус на перше поле нового рядка
    row.querySelector('input').focus();
}

// Видалення рядка
function removeItemRow(button) {
    const row = button.closest('tr');
    row.remove();
    
    updateUI();
    updateRowNumbers();
    
    if (document.querySelectorAll('.item-row').length === 0) {
        showEmptyState();
    }
}

// Додавання з шаблону
function addItemFromTemplate() {
    const modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();
}

// Обробка кліку по шаблону
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.template-item').forEach(item => {
        item.addEventListener('click', function() {
            const templateData = JSON.parse(this.dataset.template);
            addItemRow(templateData);
            bootstrap.Modal.getInstance(document.getElementById('templateModal')).hide();
        });
    });
    
    // Пошук по шаблонах
    document.getElementById('templateSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.template-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});

// Дублювання останнього рядка
function duplicateLastItem() {
    const lastRow = document.querySelector('.item-row:last-child');
    if (!lastRow) {
        addItemRow();
        return;
    }
    
    const inputs = lastRow.querySelectorAll('input');
    const data = {
        equipment_type: inputs[0].value,
        brand: inputs[1].value,
        model: inputs[2].value,
    };
    
    addItemRow(data);
}

// Очистити все
function clearAllItems() {
    if (!confirm('Видалити всі додані позиції?')) return;
    
    document.getElementById('itemsTableBody').innerHTML = '';
    itemCounter = 0;
    updateUI();
    showEmptyState();
}

// Оновити нумерацію рядків
function updateRowNumbers() {
    document.querySelectorAll('.item-row').forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1;
    });
}

// Оновити UI
function updateUI() {
    const count = document.querySelectorAll('.item-row').length;
    document.getElementById('itemsCount').textContent = count;
    document.getElementById('submitCount').textContent = count;
    document.getElementById('submitBtn').disabled = count === 0;
}

// Показати/сховати порожній стан
function showEmptyState() {
    document.getElementById('emptyState').style.display = 'block';
    document.getElementById('itemsTable').style.display = 'none';
}

function hideEmptyState() {
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('itemsTable').style.display = 'table';
}

// Ініціалізація
document.addEventListener('DOMContentLoaded', function() {
    // Показуємо порожній стан на старті
    showEmptyState();
    
    // Автозаповнення інвентарних номерів
    document.getElementById('itemsTableBody').addEventListener('blur', function(e) {
        if (e.target.name && e.target.name.includes('[equipment_type]')) {
            const row = e.target.closest('tr');
            const invInput = row.querySelector('[name*="[inventory_number]"]');
            
            if (!invInput.value) {
                const index = row.dataset.index;
                const branchId = document.getElementById('branch_id').value;
                const roomNum = document.getElementById('room_number').value;
                
                if (branchId && roomNum) {
                    invInput.value = `INV-${branchId}-${roomNum}-${String(parseInt(index) + 1).padStart(3, '0')}`;
                }
            }
        }
    }, true);
    
    // Попередження перед виходом
    let formChanged = false;
    document.getElementById('inventoryForm').addEventListener('input', function() {
        formChanged = true;
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formChanged && document.querySelectorAll('.item-row').length > 0) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // Гарячі клавіші
    document.addEventListener('keydown', function(e) {
        // Ctrl + Enter - додати новий рядок
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            addItemRow();
        }
        
        // Ctrl + D - дублювати останнє
        if (e.ctrlKey && e.key === 'd') {
            e.preventDefault();
            duplicateLastItem();
        }
    });
    
    // AJAX валідація інвентарних номерів при втраті фокусу
    document.getElementById('itemsTableBody').addEventListener('blur', function(e) {
        if (e.target.name && e.target.name.includes('[inventory_number]')) {
            validateInventoryNumber(e.target);
        }
    }, true);
});

// Валідація інвентарного номера
async function validateInventoryNumber(input) {
    const value = input.value.trim();
    if (!value) return;
    
    try {
        const response = await fetch('{{ route("inventory.validate-numbers") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                numbers: [value]
            })
        });
        
        const data = await response.json();
        
        if (!data.valid && data.duplicates.includes(value)) {
            input.classList.add('is-invalid');
            
            // Показуємо повідомлення
            let feedback = input.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                input.parentNode.appendChild(feedback);
            }
            feedback.textContent = 'Цей інвентарний номер вже існує!';
            feedback.style.display = 'block';
        } else {
            input.classList.remove('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Помилка валідації:', error);
    }
}

// Швидке введення (Enter для переходу до наступного поля)
document.getElementById('itemsTableBody').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
        e.preventDefault();
        
        const currentRow = e.target.closest('tr');
        const inputs = Array.from(currentRow.querySelectorAll('input'));
        const currentIndex = inputs.indexOf(e.target);
        
        // Якщо це останнє поле в рядку - додаємо новий рядок
        if (currentIndex === inputs.length - 1) {
            addItemRow();
        } else {
            // Переходимо до наступного поля
            inputs[currentIndex + 1]?.focus();
        }
    }
}, true);
</script>

<style>
.item-row {
    transition: background-color 0.2s;
}

.item-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.form-control-sm {
    font-size: 0.875rem;
}

#emptyState {
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.template-item {
    cursor: pointer;
    transition: all 0.2s;
}

.template-item:hover {
    background-color: rgba(0, 123, 255, 0.1);
    transform: translateX(5px);
}

.card-header {
    font-weight: 500;
}

/* Анімація додавання рядка */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.item-row {
    animation: slideIn 0.3s ease-out;
}
</style>
@endpush