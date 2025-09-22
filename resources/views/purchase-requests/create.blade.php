@extends('layouts.app')

@section('title', 'Створити заявку на закупівлю')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Створити заявку на закупівлю</h4>
                <p class="text-muted">Вкажіть необхідні товари та їх кількість</p>
            </div>
            
            @if($lowStockItems->count() > 0)
            <div class="alert alert-info">
                <h6><i class="bi bi-info-circle"></i> Товари з низькими залишками:</h6>
                <div class="row g-2">
                    @foreach($lowStockItems as $item)
                    <div class="col-md-4">
                        <button type="button" class="btn btn-sm btn-outline-warning w-100"
                                onclick="addLowStockItem('{{ $item->id }}', '{{ $item->name }}', '{{ $item->code }}', {{ $item->min_quantity - $item->quantity }}, '{{ $item->unit }}', {{ $item->price ?? 0 }})">
                            {{ $item->name }} ({{ $item->quantity }}/{{ $item->min_quantity }})
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <form method="POST" action="{{ route('purchase-requests.store') }}" id="purchaseForm">
                @csrf
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="requested_date" class="form-label">Дата потреби <span class="text-danger">*</span></label>
                        <input type="date" name="requested_date" id="requested_date" 
                               class="form-control @error('requested_date') is-invalid @enderror" 
                               value="{{ old('requested_date') }}" required min="{{ date('Y-m-d') }}">
                        @error('requested_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="description" class="form-label">Опис заявки</label>
                        <input type="text" name="description" id="description" 
                               class="form-control @error('description') is-invalid @enderror" 
                               value="{{ old('description') }}" placeholder="Короткий опис заявки">
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
                                <!-- Строки будут добавляться динамически -->
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="5" class="text-end">Загальна сума:</th>
                                    <th id="totalAmount">0.00 грн</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label">Додаткові примітки</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                              rows="3" placeholder="Технічні вимоги, особливості постачання тощо">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <div>
                        <button type="submit" name="action" value="save_draft" class="btn btn-outline-primary me-2">
                            <i class="bi bi-save"></i> Зберегти як чернетку
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Створити і подати
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemCounter = 0;

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

function addLowStockItem(id, name, code, suggestedQty, unit, price) {
    addItemRow({
        name: name,
        code: code,
        quantity: suggestedQty,
        unit: unit,
        price: price
    });
}

// Добавляем первую строку при загрузке
document.addEventListener('DOMContentLoaded', function() {
    addItemRow();
});
</script>
@endpush