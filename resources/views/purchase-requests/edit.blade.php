{{-- resources/views/purchase-requests/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редагувати заявку ' . $purchaseRequest->request_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Редагувати заявку {{ $purchaseRequest->request_number }}</h4>
                <p class="text-muted">
                    Створена {{ $purchaseRequest->created_at->format('d.m.Y в H:i') }} |
                    Статус: {!! $purchaseRequest->status_badge !!}
                </p>
            </div>

            <form method="POST" action="{{ route('purchase-requests.update', $purchaseRequest) }}" id="purchaseForm">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="requested_date" class="form-label">Дата потреби <span class="text-danger">*</span></label>
                            <input type="date" name="requested_date" id="requested_date"
                                   class="form-control @error('requested_date') is-invalid @enderror"
                                   value="{{ old('requested_date', $purchaseRequest->requested_date->format('Y-m-d')) }}"
                                   required min="{{ date('Y-m-d') }}">
                            @error('requested_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="description" class="form-label">Опис заявки</label>
                            <input type="text" name="description" id="description"
                                   class="form-control @error('description') is-invalid @enderror"
                                   value="{{ old('description', $purchaseRequest->description) }}"
                                   placeholder="Короткий опис заявки">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Товари -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Товари для закупівлі</h5>
                            <div>
                                <button type="button" class="btn btn-success btn-sm" onclick="addItemRow()">
                                    <i class="bi bi-plus"></i> Додати товар
                                </button>
                                <button type="button" class="btn btn-warning btn-sm ms-2" id="splitBtn" onclick="showSplitModal()" style="display:none;">
                                    <i class="bi bi-diagram-3"></i> Розділити вибрані
                                </button>
                                @if(in_array(Auth::user()->role, ['admin', 'warehouse_keeper']))
                                    <button type="button" class="btn btn-success btn-sm ms-2" id="receiveEditBtn" onclick="showReceiveModal()" style="display:none;">
                                        <i class="bi bi-inbox"></i> Прийняти на склад
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="4%"><input type="checkbox" id="selectAllCheckbox" onchange="toggleAllCheckboxes()"></th>
                                        <th width="28%">Назва товару *</th>
                                        <th width="10%">Код</th>
                                        <th width="8%">Кількість *</th>
                                        <th width="8%">Одиниця *</th>
                                        <th width="12%">Очікувана ціна</th>
                                        <th width="14%">Категорія</th>
                                        <th width="10%">Сума</th>
                                        <th width="6%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @foreach($purchaseRequest->items as $index => $item)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="item-checkbox" data-item-index="{{ $index }}" data-item-id="{{ $item->id }}" data-item-name="{{ $item->item_name }}" onchange="updateSplitButtonVisibility()">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                    <input type="text" name="items[{{ $index }}][item_name]"
                                                           class="form-control form-control-sm item-name-input"
                                                           value="{{ $item->item_name }}"
                                                           placeholder="Назва товару" required>
                                                    <button type="button" class="btn btn-outline-secondary item-select-btn"
                                                            onclick="showItemSelect({{ $index }})" title="Вибрати зі складу">
                                                        <i class="bi bi-box-seam"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm item-code"
                                                       value="{{ $item->item_code }}" readonly>
                                                <input type="hidden" name="items[{{ $index }}][item_code]" value="{{ $item->item_code }}">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][quantity]"
                                                       class="form-control form-control-sm quantity-input"
                                                       value="{{ $item->quantity }}"
                                                       min="1" required
                                                       onchange="calculateRowTotal(this)">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][unit]"
                                                       class="form-control form-control-sm"
                                                       value="{{ $item->unit }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][estimated_price]"
                                                       class="form-control form-control-sm price-input"
                                                       value="{{ $item->estimated_price }}"
                                                       step="0.01" min="0" placeholder="0.00"
                                                       onchange="calculateRowTotal(this)">
                                            </td>
                                            <td>
                                                <select name="items[{{ $index }}][category]" class="form-select form-select-sm category-select">
                                                    <option value="">Без категорії</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category }}" {{ $item->category === $category ? 'selected' : '' }}>{{ $category }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <span class="row-total fw-bold">{{ number_format($item->total, 2) }} грн</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItemRow(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th colspan="7" class="text-end">Загальна сума:</th>
                                        <th id="totalAmount">{{ number_format($purchaseRequest->total_amount, 2) }} грн</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="addItemRow()">
                                <i class="bi bi-plus"></i> Додати товар
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="notes" class="form-label">Додаткові примітки</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                  rows="3" placeholder="Технічні вимоги, особливості постачання тощо">{{ old('notes', $purchaseRequest->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Скасувати
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Зберегти зміни
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal для вибору товару -->
<div class="modal fade" id="itemSelectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Вибрати товар</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="itemSearch" class="form-control" placeholder="Пошук товару...">
                </div>
                <div id="itemsList" style="max-height: 400px; overflow-y: auto;">
                    <!-- Items будут добавляться здесь -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal для розділення заявки -->
<div class="modal fade" id="splitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Розділити заявку</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="splitForm">
                    <div class="mb-3">
                        <label for="newDescription" class="form-label">Опис нової заявки</label>
                        <input type="text" id="newDescription" class="form-control" placeholder="Наприклад: Друга партія" value="">
                    </div>
                    <div class="mb-3">
                        <label for="newRequestedDate" class="form-label">Дата потреби для нової заявки</label>
                        <input type="date" id="newRequestedDate" class="form-control" value="">
                    </div>
                </form>
                <div class="alert alert-info">
                    <p class="mb-0">
                        <i class="bi bi-info-circle"></i>
                        Вибрані товари буде перенесено в нову заявку. Вони будуть видалені з поточної заявки.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="button" class="btn btn-warning" onclick="executeSplit()">
                    <i class="bi bi-diagram-3"></i> Розділити
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal для оприходування товарів -->
<div class="modal fade" id="receiveModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Прийняти товари на склад</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Позиція</th>
                                <th>Замовлено</th>
                                <th>Факт (шт)</th>
                                <th>Склад</th>
                            </tr>
                        </thead>
                        <tbody id="receiveTableBody">
                            <!-- Items будуть добавляться здесь -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="button" class="btn btn-success" onclick="executeReceive()">
                    <i class="bi bi-check-lg"></i> Прийняти на склад
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let itemCounter = {{ $purchaseRequest->items->count() }};
let currentRowIndex = -1;

const warehouseItems = {!! json_encode($warehouseItems->map(function($item) {
    return [
        'equipment_type' => $item->equipment_type,
        'full_name' => $item->full_name,
        'inventory_number' => $item->inventory_number,
        'unit' => $item->unit,
        'price' => $item->price,
        'category' => $item->category,
        'total_quantity' => $item->total_quantity,
        'min_quantity' => $item->min_quantity
    ];
})) !!};

const availableCategories = {!! json_encode($categories ?? []) !!};

// Функція для екранування HTML спецсимволів
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function addItemRow(itemData = null) {
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="text-center">
            <input type="checkbox" class="item-checkbox" data-item-index="${itemCounter}" onchange="updateSplitButtonVisibility()">
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="text" name="items[${itemCounter}][item_name]"
                       class="form-control form-control-sm item-name-input"
                       value="${escapeHtml(itemData?.equipment_type || '')}"
                       placeholder="Назва товару" required>
                <button type="button" class="btn btn-outline-secondary item-select-btn"
                        onclick="showItemSelect(${itemCounter})" title="Вибрати зі складу">
                    <i class="bi bi-box-seam"></i>
                </button>
            </div>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-code"
                   value="${itemData?.inventory_number || ''}" readonly>
            <input type="hidden" name="items[${itemCounter}][item_code]" value="${itemData?.inventory_number || ''}">
        </td>
        <td>
            <input type="number" name="items[${itemCounter}][quantity]"
                   class="form-control form-control-sm quantity-input"
                   value="${itemData?.quantity || 1}"
                   min="1" required
                   onchange="calculateRowTotal(this)">
        </td>
        <td>
            <input type="text" name="items[${itemCounter}][unit]"
                   class="form-control form-control-sm"
                   value="${itemData?.unit || 'шт'}" required>
        </td>
        <td>
            <input type="number" name="items[${itemCounter}][estimated_price]"
                   class="form-control form-control-sm price-input"
                   value="${itemData?.price || ''}"
                   step="0.01" min="0" placeholder="0.00"
                   onchange="calculateRowTotal(this)">
        </td>
        <td>
            <select name="items[${itemCounter}][category]" class="form-select form-select-sm category-select">
                <option value="">Без категорії</option>
                ${availableCategories.map(cat => `<option value="${escapeHtml(cat)}">${escapeHtml(cat)}</option>`).join('')}
            </select>
        </td>
        <td>
            <span class="row-total fw-bold">0.00 грн</span>
        </td>
        <td>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItemRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tbody.appendChild(row);
    itemCounter++;

    if (itemData) {
        calculateRowTotal(row.querySelector('.price-input'));
    }
}

function showItemSelect(index) {
    currentRowIndex = index;
    const itemsList = document.getElementById('itemsList');
    itemsList.innerHTML = warehouseItems.map((item, itemIndex) => `
        <div class="card mb-2">
            <div class="card-body p-2">
                <button type="button" class="btn btn-light w-100 text-start select-item-btn"
                        data-row-index="${index}"
                        data-item-index="${itemIndex}">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${escapeHtml(item.equipment_type)}</strong>
                            ${item.full_name ? '<br><small class="text-success">✓ Повна назва</small>' : ''}
                            <br><small class="text-muted">Код: ${escapeHtml(item.inventory_number)}</small>
                        </div>
                        <div class="text-end">
                            <small>На складі: <span class="badge bg-info">${item.total_quantity}</span></small>
                            <br><small class="text-muted">Мін: ${item.min_quantity}</small>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    `).join('');

    // Додаємо обробники для всіх кнопок
    document.querySelectorAll('.select-item-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const rowIndex = parseInt(this.dataset.rowIndex);
            const itemIndex = parseInt(this.dataset.itemIndex);
            const item = warehouseItems[itemIndex];
            selectItem(rowIndex, item.equipment_type, item.full_name, item.inventory_number, item.unit, item.price);
        });
    });

    const modal = new bootstrap.Modal(document.getElementById('itemSelectModal'));
    modal.show();

    document.getElementById('itemSearch').onchange = () => filterItems();
    document.getElementById('itemSearch').oninput = () => filterItems();
}

function filterItems() {
    const searchValue = document.getElementById('itemSearch').value.toLowerCase();
    const items = warehouseItems.filter(item =>
        item.equipment_type.toLowerCase().includes(searchValue) ||
        item.inventory_number.toLowerCase().includes(searchValue)
    );

    const itemsList = document.getElementById('itemsList');
    itemsList.innerHTML = items.map((item, itemIndex) => {
        const originalIndex = warehouseItems.indexOf(item);
        return `
        <div class="card mb-2">
            <div class="card-body p-2">
                <button type="button" class="btn btn-light w-100 text-start select-item-btn"
                        data-row-index="${currentRowIndex}"
                        data-item-index="${originalIndex}">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${escapeHtml(item.equipment_type)}</strong>
                            ${item.full_name ? '<br><small class="text-success">✓ Повна назва</small>' : ''}
                            <br><small class="text-muted">Код: ${escapeHtml(item.inventory_number)}</small>
                        </div>
                        <div class="text-end">
                            <small>На складі: <span class="badge bg-info">${item.total_quantity}</span></small>
                            <br><small class="text-muted">Мін: ${item.min_quantity}</small>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    `;
    }).join('');

    // Додаємо обробники для відфільтрованих кнопок
    document.querySelectorAll('.select-item-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const rowIndex = parseInt(this.dataset.rowIndex);
            const itemIndex = parseInt(this.dataset.itemIndex);
            const item = warehouseItems[itemIndex];
            selectItem(rowIndex, item.equipment_type, item.full_name, item.inventory_number, item.unit, item.price, item.category);
        });
    });

    if (items.length === 0) {
        itemsList.innerHTML = '<div class="text-center text-muted p-3">Товари не знайдені</div>';
    }
}

function selectItem(index, name, fullName, code, unit, price, category = null) {
    const row = document.getElementById('itemsTableBody').children[index];
    if (!row) return;

    // Використовуємо повну назву якщо є та не порожня, інакше коротку
    const displayName = (fullName && fullName.trim() !== '') ? fullName : name;

    row.querySelector('.item-name-input').value = displayName;
    row.querySelector('.item-code').value = code;
    row.querySelector('input[name="items[' + index + '][item_code]"]').value = code;
    row.querySelector('input[name="items[' + index + '][unit]"]').value = unit;
    row.querySelector('.price-input').value = price || '';

    // Встановлюємо категорію якщо вона є
    const categorySelect = row.querySelector('.category-select');
    if (categorySelect && category) {
        categorySelect.value = category;
    }

    calculateRowTotal(row.querySelector('.price-input'));

    bootstrap.Modal.getInstance(document.getElementById('itemSelectModal')).hide();
}

function removeItemRow(button) {
    if (document.querySelectorAll('#itemsTableBody tr').length > 1) {
        button.closest('tr').remove();
        calculateTotal();
    } else {
        alert('Має бути принаймні один товар у заявці');
    }
}

function calculateRowTotal(input) {
    const row = input.closest('tr');
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const total = quantity * price;

    row.querySelector('.row-total').textContent = total.toFixed(2) + ' грн';

    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.row-total').forEach(element => {
        const value = parseFloat(element.textContent.replace(' грн', '')) || 0;
        total += value;
    });

    document.getElementById('totalAmount').textContent = total.toFixed(2) + ' грн';
}

// Функції для розділення заявки
function getSelectedItemIndices() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    return Array.from(checkboxes).map(cb => parseInt(cb.dataset.itemIndex));
}

function updateSplitButtonVisibility() {
    const selected = getSelectedItemIndices();
    const splitBtn = document.getElementById('splitBtn');
    const receiveBtn = document.getElementById('receiveEditBtn');
    const totalItems = document.querySelectorAll('.item-checkbox').length;

    if (selected.length > 0 && selected.length < totalItems) {
        splitBtn.style.display = 'inline-block';
    } else {
        splitBtn.style.display = 'none';
    }

    if (receiveBtn && selected.length > 0) {
        receiveBtn.style.display = 'inline-block';
    } else if (receiveBtn) {
        receiveBtn.style.display = 'none';
    }
}

function toggleAllCheckboxes() {
    const allCheckbox = document.getElementById('selectAllCheckbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(cb => cb.checked = allCheckbox.checked);
    updateSplitButtonVisibility();
}

function showSplitModal() {
    const selected = getSelectedItemIndices();
    if (selected.length === 0) {
        alert('Будь ласка, виберіть хоча б один товар');
        return;
    }

    const totalItems = document.querySelectorAll('.item-checkbox').length;
    if (selected.length === totalItems) {
        alert('Будь ласка, залиште хоча б один товар у поточній заявці');
        return;
    }

    // Встановлюємо дату за замовчуванням
    const dateInput = document.getElementById('newRequestedDate');
    dateInput.value = document.getElementById('requested_date').value;

    const modal = new bootstrap.Modal(document.getElementById('splitModal'));
    modal.show();
}

function executeSplit() {
    const selected = getSelectedItemIndices();
    if (selected.length === 0) {
        alert('Будь ласка, виберіть товари для розділення');
        return;
    }

    const newDescription = document.getElementById('newDescription').value;
    const newRequestedDate = document.getElementById('newRequestedDate').value;

    if (!newRequestedDate) {
        alert('Будь ласка, вкажіть дату потреби для нової заявки');
        return;
    }

    // Збираємо дані вибраних товарів
    const selectedItems = [];
    selected.forEach(index => {
        const row = document.querySelector(`input[name="items[${index}][item_name]"]`).closest('tr');
        selectedItems.push({
            item_name: row.querySelector('.item-name-input').value,
            item_code: row.querySelector('input[name="items[' + index + '][item_code]"]').value,
            quantity: row.querySelector('.quantity-input').value,
            unit: row.querySelector('input[name="items[' + index + '][unit]"]').value,
            estimated_price: row.querySelector('.price-input').value,
        });
    });

    // Відправляємо запит на сервер
    fetch(`{{ route('purchase-requests.split', $purchaseRequest) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            selected_indices: selected,
            new_description: newDescription,
            new_requested_date: newRequestedDate,
            selected_items: selectedItems
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = `{{ route('purchase-requests.index') }}?success=Заявка розділена. Створено нову заявку: ${data.new_request_number}`;
        } else {
            alert('Помилка: ' + (data.message || 'Не вдалося розділити заявку'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Помилка при розділенні заявки');
    });
}

// Ініціалізація калькуляцій при завантаженні
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
    updateSplitButtonVisibility();
});

// ===== ФУНКЦІЇ ДЛЯ ОПРИХОДУВАННЯ ТОВАРІВ =====
const warehouseItemsDataEdit = {!! json_encode($purchaseRequest->items->map(function($item) {
    return [
        'id' => $item->id,
        'item_name' => $item->item_name,
        'quantity' => $item->quantity,
        'unit' => $item->unit,
    ];
})) !!};

function getSelectedItemIdsForReceive() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    return Array.from(checkboxes).map(cb => ({
        index: parseInt(cb.dataset.itemIndex),
        id: parseInt(cb.dataset.itemId),
        name: cb.dataset.itemName
    }));
}

function getItemNameAtIndex(index) {
    const input = document.querySelector(`input[name="items[${index}][item_name]"]`);
    return input ? input.value : '';
}

function showReceiveModal() {
    const selected = getSelectedItemIdsForReceive();
    if (selected.length === 0) {
        alert('Будь ласка, виберіть хоча б один товар');
        return;
    }

    const tableBody = document.getElementById('receiveTableBody');
    tableBody.innerHTML = '';

    // Запит для пошуку товарів на складі
    const searchPromises = selected.map(itemSelected => {
        // Знаходимо дані товару з warehouseItemsDataEdit
        const itemData = warehouseItemsDataEdit.find(item => item.id === itemSelected.id);

        return fetch(`{{ route('api.warehouse-items.search') }}?q=${encodeURIComponent(itemSelected.name)}`)
            .then(r => r.json())
            .then(results => ({
                ...itemSelected,
                quantity: itemData ? itemData.quantity : 1,
                unit: itemData ? itemData.unit : 'шт',
                foundItems: results
            }))
    });

    Promise.all(searchPromises).then(itemsWithResults => {
        itemsWithResults.forEach((itemData, idx) => {
            const foundItem = itemData.foundItems.length > 0 ? itemData.foundItems[0] : null;

            const row = document.createElement('tr');
            let warehouseHTML = '';

            if (foundItem) {
                warehouseHTML = `
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-success text-white">✓ Знайдено</span>
                        <input type="text" class="form-control form-control-sm" value="${foundItem.name} (${foundItem.code})" readonly>
                        <input type="hidden" class="receive-action" value="update_existing">
                        <input type="hidden" class="receive-inventory-id" value="${foundItem.id}">
                    </div>
                `;
            } else {
                warehouseHTML = `
                    <select class="form-select form-select-sm receive-action-select" onchange="updateReceiveActionSelect(this)">
                        <option value="">Виберіть дію</option>
                        <option value="create_new">+ Створити новий</option>
                        <option value="link_to_existing">Прив'язати до існуючого</option>
                    </select>
                    <div class="receive-existing-select" style="display:none; margin-top: 5px;">
                        <input type="text" class="form-control form-control-sm receive-search" placeholder="Пошук товару..." onkeyup="searchWarehouseItems(this)">
                        <div class="receive-search-results" style="max-height: 150px; overflow-y: auto; margin-top: 5px;"></div>
                    </div>
                    <input type="hidden" class="receive-inventory-id" value="">
                    <input type="hidden" class="receive-action" value="">
                `;
            }

            row.innerHTML = `
                <td>
                    <strong>${itemData.name}</strong>
                </td>
                <td>
                    <span class="badge bg-info">${itemData.quantity} ${itemData.unit}</span>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm receive-quantity" value="${itemData.quantity}" min="1" data-item-index="${itemData.index}">
                </td>
                <td>
                    ${warehouseHTML}
                </td>
            `;

            tableBody.appendChild(row);
        });

        const modal = new bootstrap.Modal(document.getElementById('receiveModal'));
        modal.show();
    });
}

function updateReceiveActionSelect(selectElement) {
    const action = selectElement.value;
    const row = selectElement.closest('td');
    const existingSelect = row.querySelector('.receive-existing-select');
    const actionInput = row.querySelector('.receive-action');

    actionInput.value = action;

    if (action === 'link_to_existing') {
        existingSelect.style.display = 'block';
    } else if (action === 'create_new') {
        existingSelect.style.display = 'none';
    }
}

function searchWarehouseItems(inputElement) {
    const query = inputElement.value;
    const row = inputElement.closest('td');
    const resultsDiv = row.querySelector('.receive-search-results');

    if (query.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }

    fetch(`{{ route('api.warehouse-items.search') }}?q=${encodeURIComponent(query)}`)
        .then(r => r.json())
        .then(items => {
            resultsDiv.innerHTML = items.map(item => `
                <div class="card mb-1" style="cursor: pointer;" onclick="selectWarehouseItem(this, ${item.id}, '${item.name}')">
                    <div class="card-body p-2">
                        <div><strong>${item.name}</strong></div>
                        <small class="text-muted">Код: ${item.code || 'N/A'}</small>
                    </div>
                </div>
            `).join('');
        });
}

function selectWarehouseItem(element, itemId, itemName) {
    const row = element.closest('td');
    const actionInput = row.querySelector('.receive-action');
    const inventoryInput = row.querySelector('.receive-inventory-id');
    const existingSelect = row.querySelector('.receive-existing-select');

    actionInput.value = 'link_to_existing';
    inventoryInput.value = itemId;
    existingSelect.innerHTML = `
        <div class="input-group input-group-sm">
            <span class="input-group-text">Обрано:</span>
            <input type="text" class="form-control form-control-sm" value="${itemName}" readonly>
        </div>
    `;
}

function executeReceive() {
    const selected = getSelectedItemIdsForReceive();
    if (selected.length === 0) {
        alert('Будь ласка, виберіть товари');
        return;
    }

    const items = [];
    const rows = document.querySelectorAll('#receiveTableBody tr');

    let rowIndex = 0;
    rows.forEach((row, idx) => {
        const quantityInput = row.querySelector('.receive-quantity');
        const actionInput = row.querySelector('.receive-action');
        const inventoryInput = row.querySelector('.receive-inventory-id');

        const actualQuantity = parseInt(quantityInput.value);
        const action = actionInput.value;
        const existingInventoryId = inventoryInput.value;

        if (!actualQuantity || actualQuantity < 1) {
            alert('Вкажіть коректну кількість для всіх товарів');
            throw new Error('Invalid quantity');
        }

        if (!action) {
            alert('Оберіть дію для всіх товарів');
            throw new Error('No action selected');
        }

        if (action === 'link_to_existing' && !existingInventoryId) {
            alert('Оберіть товар на складі для прив\'язання');
            throw new Error('No inventory selected');
        }

        // Отримуємо ID товару з обраних
        const itemId = selected[rowIndex] ? selected[rowIndex].id : null;

        if (itemId) {
            items.push({
                purchase_request_item_id: itemId,
                actual_quantity: actualQuantity,
                action: action,
                existing_inventory_id: existingInventoryId || null
            });
        }

        rowIndex++;
    });

    if (items.length === 0) {
        alert('Помилка: не вдалося отримати ID товарів');
        return;
    }

    // Відправляємо запит на сервер
    fetch(`{{ route('purchase-requests.receive', $purchaseRequest) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ items })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Помилка: ' + (data.message || 'Не вдалося оприходувати товари'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Помилка при оприходуванні товарів');
    });
}
</script>
@endpush

@push('styles')
<style>
.item-select-btn {
    white-space: normal;
    line-height: 1.5;
}
</style>
@endpush
