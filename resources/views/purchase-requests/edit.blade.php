{{-- resources/views/purchase-requests/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редагувати заявку ' . $purchaseRequest->request_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
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
                                        <th width="30%">Назва товару *</th>
                                        <th width="15%">Код/Артикул</th>
                                        <th width="10%">Кількість *</th>
                                        <th width="10%">Одиниця *</th>
                                        <th width="15%">Очікувана ціна</th>
                                        <th width="15%">Сума</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @foreach($purchaseRequest->items as $index => $item)
                                        <tr>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][item_name]" 
                                                       class="form-control item-name" 
                                                       value="{{ $item->item_name }}" 
                                                       placeholder="Назва товару" required
                                                       onchange="fillItemData(this, {{ $index }})">
                                                <datalist id="itemsList{{ $index }}">
                                                    @foreach($warehouseItems as $warehouseItem)
                                                        <option value="{{ $warehouseItem->name }}" 
                                                                data-code="{{ $warehouseItem->code }}" 
                                                                data-unit="{{ $warehouseItem->unit }}" 
                                                                data-price="{{ $warehouseItem->price ?? 0 }}">
                                                            {{ $warehouseItem->code }}
                                                        </option>
                                                    @endforeach
                                                </datalist>
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][item_code]" 
                                                       class="form-control" 
                                                       value="{{ $item->item_code }}"
                                                       placeholder="Код">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][quantity]" 
                                                       class="form-control quantity-input" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" required
                                                       onchange="calculateRowTotal(this)">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $index }}][unit]" 
                                                       class="form-control" 
                                                       value="{{ $item->unit }}" 
                                                       placeholder="шт" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][estimated_price]" 
                                                       class="form-control price-input" 
                                                       value="{{ $item->estimated_price }}" 
                                                       step="0.01" min="0"
                                                       placeholder="0.00"
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
@endsection

@push('scripts')
<script>
let itemCounter = {{ $purchaseRequest->items->count() }};

// Автозаполнение из справочника товаров
const warehouseItems = {!! json_encode($warehouseItems->map(function($item) {
    return [
        'id' => $item->id,
        'name' => $item->name,
        'code' => $item->code,
        'unit' => $item->unit,
        'price' => $item->price
    ];
})) !!};

function addItemRow(itemData = null) {
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <input type="text" name="items[${itemCounter}][item_name]" 
                   class="form-control item-name" 
                   value="${itemData?.name || ''}" 
                   placeholder="Назва товару" required
                   onchange="fillItemData(this, ${itemCounter})">
            <datalist id="itemsList${itemCounter}">
                ${warehouseItems.map(item => 
                    `<option value="${item.name}" data-code="${item.code}" data-unit="${item.unit}" data-price="${item.price || 0}">${item.code}</option>`
                ).join('')}
            </datalist>
        </td>
        <td>
            <input type="text" name="items[${itemCounter}][item_code]" 
                   class="form-control" 
                   value="${itemData?.code || ''}"
                   placeholder="Код">
        </td>
        <td>
            <input type="number" name="items[${itemCounter}][quantity]" 
                   class="form-control quantity-input" 
                   value="${itemData?.quantity || 1}" 
                   min="1" required
                   onchange="calculateRowTotal(this)">
        </td>
        <td>
            <input type="text" name="items[${itemCounter}][unit]" 
                   class="form-control" 
                   value="${itemData?.unit || 'шт'}" 
                   placeholder="шт" required>
        </td>
        <td>
            <input type="number" name="items[${itemCounter}][estimated_price]" 
                   class="form-control price-input" 
                   value="${itemData?.price || ''}" 
                   step="0.01" min="0"
                   placeholder="0.00"
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
    
    // Добавляем датalist для автозаполнения
    const nameInput = row.querySelector('.item-name');
    nameInput.setAttribute('list', `itemsList${itemCounter}`);
    
    tbody.appendChild(row);
    itemCounter++;
    
    calculateTotal();
}

function fillItemData(nameInput, index) {
    const selectedItem = warehouseItems.find(item => item.name === nameInput.value);
    if (selectedItem) {
        const row = nameInput.closest('tr');
        row.querySelector(`input[name="items[${index}][item_code]"]`).value = selectedItem.code;
        row.querySelector(`input[name="items[${index}][unit]"]`).value = selectedItem.unit;
        row.querySelector(`input[name="items[${index}][estimated_price]"]`).value = selectedItem.price || '';
        
        calculateRowTotal(nameInput);
    }
}

function removeItemRow(button) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    if (rows.length > 1) {
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

// Инициализация расчетов при загрузке
document.addEventListener('DOMContentLoaded', function() {
    // Пересчитываем суммы для существующих строк
    document.querySelectorAll('.quantity-input, .price-input').forEach(input => {
        calculateRowTotal(input);
    });
});
</script>
@endpush