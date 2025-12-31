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

            @if(!in_array($purchaseRequest->status, ['draft', 'submitted']))
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Увага!</strong> Заявка в статусі "{{ $purchaseRequest->status }}" не може бути відредагована.
                </div>
            @else
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
                            <button type="button" class="btn btn-success btn-sm" onclick="addItemRow()">
                                <i class="bi bi-plus"></i> Додати товар
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Назва товару *</th>
                                        <th width="12%">Код</th>
                                        <th width="10%">Кількість *</th>
                                        <th width="10%">Одиниця *</th>
                                        <th width="15%">Очікувана ціна</th>
                                        <th width="12%">Сума</th>
                                        <th width="6%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @foreach($purchaseRequest->items as $index => $item)
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start item-select-btn"
                                                        onclick="showItemSelect({{ $index }})">
                                                    <span class="item-name">{{ $item->item_name }}</span>
                                                    <input type="hidden" name="items[{{ $index }}][item_name]" class="item-name-hidden" value="{{ $item->item_name }}">
                                                </button>
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
                                        <th colspan="5" class="text-end">Загальна сума:</th>
                                        <th id="totalAmount">{{ number_format($purchaseRequest->total_amount, 2) }} грн</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
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
            @endif
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
        'total_quantity' => $item->total_quantity,
        'min_quantity' => $item->min_quantity
    ];
})) !!};

function addItemRow(itemData = null) {
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start item-select-btn"
                    onclick="showItemSelect(${itemCounter})">
                <span class="item-name">${itemData?.equipment_type || 'Вибрати товар...'}</span>
                <input type="hidden" name="items[${itemCounter}][item_name]" class="item-name-hidden" value="${itemData?.equipment_type || ''}">
            </button>
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
    itemsList.innerHTML = warehouseItems.map(item => `
        <div class="card mb-2">
            <div class="card-body p-2">
                <button type="button" class="btn btn-light w-100 text-start" onclick="selectItem(${index}, '${item.equipment_type.replace(/'/g, "\\'")}', '${(item.full_name || '').replace(/'/g, "\\'")}', '${item.inventory_number}', '${item.unit}', ${item.price})">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${item.equipment_type}</strong>
                            ${item.full_name ? '<br><small class="text-success">✓ Повна назва</small>' : ''}
                            <br><small class="text-muted">Код: ${item.inventory_number}</small>
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
    itemsList.innerHTML = items.map(item => `
        <div class="card mb-2">
            <div class="card-body p-2">
                <button type="button" class="btn btn-light w-100 text-start" onclick="selectItem(${currentRowIndex}, '${item.equipment_type.replace(/'/g, "\\'")}', '${(item.full_name || '').replace(/'/g, "\\'")}', '${item.inventory_number}', '${item.unit}', ${item.price})">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${item.equipment_type}</strong>
                            ${item.full_name ? '<br><small class="text-success">✓ Повна назва</small>' : ''}
                            <br><small class="text-muted">Код: ${item.inventory_number}</small>
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

    if (items.length === 0) {
        itemsList.innerHTML = '<div class="text-center text-muted p-3">Товари не знайдені</div>';
    }
}

function selectItem(index, name, fullName, code, unit, price) {
    const row = document.getElementById('itemsTableBody').children[index];
    if (!row) return;

    // Використовуємо повну назву якщо є, інакше коротку
    const displayName = fullName || name;

    row.querySelector('.item-name').textContent = displayName;
    row.querySelector('.item-name-hidden').value = displayName;
    row.querySelector('.item-code').value = code;
    row.querySelector('input[name="items[' + index + '][item_code]"]').value = code;
    row.querySelector('input[name="items[' + index + '][unit]"]').value = unit;
    row.querySelector('.price-input').value = price || '';

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

// Ініціалізація калькуляцій при завантаженні
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
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
