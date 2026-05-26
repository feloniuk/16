@extends('layouts.app')

@section('title', 'Редагування ' . $writeoffRequest->request_number)

@section('content')
<div class="row mb-3">
    <div class="col">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('writeoff-requests.show', $writeoffRequest) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Назад
            </a>
            <h2 class="mb-0">Редагування {{ $writeoffRequest->request_number }}</h2>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('writeoff-requests.update', $writeoffRequest) }}" id="writeoffForm">
    @csrf @method('PUT')

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="stats-card p-4 h-100">
                <h5 class="mb-3">Параметри заявки</h5>

                <div class="mb-3">
                    <label for="writeoff_date" class="form-label fw-semibold">Дата списання <span class="text-danger">*</span></label>
                    <input type="date" name="writeoff_date" id="writeoff_date" class="form-control @error('writeoff_date') is-invalid @enderror"
                        value="{{ old('writeoff_date', $writeoffRequest->writeoff_date->toDateString()) }}" required>
                    @error('writeoff_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Опис</label>
                    <input type="text" name="description" id="description" class="form-control"
                        value="{{ old('description', $writeoffRequest->description) }}">
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label fw-semibold">Примітки</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $writeoffRequest->notes) }}</textarea>
                </div>

                <hr>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Зберегти
                    </button>
                    <a href="{{ route('writeoff-requests.show', $writeoffRequest) }}" class="btn btn-outline-secondary">
                        Скасувати
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            {{-- Добавити з рухів --}}
            <div class="stats-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-box-arrow-up text-warning me-1"></i>
                        Додати з виданих товарів
                    </h5>
                    <form method="GET" action="{{ route('writeoff-requests.edit', $writeoffRequest) }}" class="d-flex gap-2 align-items-center">
                        <input type="date" name="date_from" class="form-control form-control-sm" style="width:140px" value="{{ $dateFrom }}">
                        <span class="text-muted">—</span>
                        <input type="date" name="date_to" class="form-control form-control-sm" style="width:140px" value="{{ $dateTo }}">
                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel"></i></button>
                    </form>
                </div>

                @if($groupedMovements->isEmpty())
                <p class="text-muted text-center py-3">Немає виданих товарів за обраний період</p>
                @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:36px"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                <th>Найменування</th>
                                <th style="width:80px">Од.</th>
                                <th style="width:100px">Видано</th>
                                <th style="width:120px">К-сть для списання</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedMovements as $row)
                            <tr class="movement-row" data-inventory-id="{{ $row['inventory_id'] }}"
                                data-item-name="{{ $row['item_name'] }}"
                                data-unit="{{ $row['unit'] }}"
                                data-quantity="{{ $row['total_quantity'] }}">
                                <td><input type="checkbox" class="form-check-input movement-checkbox"
                                    data-inventory-id="{{ $row['inventory_id'] }}"
                                    data-item-name="{{ $row['item_name'] }}"
                                    data-unit="{{ $row['unit'] }}"
                                    data-quantity="{{ $row['total_quantity'] }}"></td>
                                <td>{{ $row['item_name'] }}</td>
                                <td class="text-muted">{{ $row['unit'] }}</td>
                                <td>{{ $row['total_quantity'] }}</td>
                                <td><input type="number" class="form-control form-control-sm writeoff-qty" min="1" value="{{ $row['total_quantity'] }}" style="width:90px"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-end">
                    <button type="button" class="btn btn-sm btn-primary" id="addSelectedBtn">
                        <i class="bi bi-plus-circle"></i> Додати вибрані
                    </button>
                </div>
                @endif
            </div>

            {{-- Поточні позиції --}}
            <div class="stats-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-list-ul text-primary me-1"></i> Позиції заявки</h5>
                    <button type="button" class="btn btn-sm btn-outline-success" id="addCustomBtn">
                        <i class="bi bi-plus"></i> Додати вручну
                    </button>
                </div>

                @if($errors->has('items'))
                <div class="alert alert-danger py-2">{{ $errors->first('items') }}</div>
                @endif

                <div id="itemsContainer"></div>
                <div id="emptyNotice" class="text-center text-muted py-4" style="display:none">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    Немає позицій
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const itemsContainer = document.getElementById('itemsContainer');
const emptyNotice = document.getElementById('emptyNotice');
let itemIndex = 0;

function updateEmptyNotice() {
    emptyNotice.style.display = itemsContainer.querySelectorAll('.item-row').length === 0 ? 'block' : 'none';
}

function addItem(inventoryId, itemName, unit, quantity, existingId, notes) {
    if (inventoryId) {
        const existing = itemsContainer.querySelector(`.item-row[data-inventory-id="${inventoryId}"]`);
        if (existing && !existingId) {
            const qtyInput = existing.querySelector('.item-qty');
            qtyInput.value = parseInt(qtyInput.value) + parseInt(quantity);
            return;
        }
    }

    const idx = itemIndex++;
    const row = document.createElement('div');
    row.className = 'item-row border rounded p-3 mb-2 bg-white';
    row.dataset.inventoryId = inventoryId || '';
    row.innerHTML = `
        <input type="hidden" name="items[${idx}][id]" value="${existingId || ''}">
        <input type="hidden" name="items[${idx}][inventory_id]" value="${inventoryId || ''}">
        <div class="row g-2 align-items-start">
            <div class="col-md-5">
                <label class="form-label small text-muted mb-1">Найменування <span class="text-danger">*</span></label>
                <input type="text" name="items[${idx}][item_name]" class="form-control form-control-sm"
                    value="${itemName}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Од. виміру</label>
                <input type="text" name="items[${idx}][unit]" class="form-control form-control-sm" value="${unit}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Кількість <span class="text-danger">*</span></label>
                <input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm item-qty"
                    value="${quantity}" min="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Примітка</label>
                <input type="text" name="items[${idx}][notes]" class="form-control form-control-sm" value="${notes || ''}">
            </div>
            <div class="col-md-1 d-flex align-items-end justify-content-end">
                <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>`;
    row.querySelector('.remove-item').addEventListener('click', () => { row.remove(); updateEmptyNotice(); });
    itemsContainer.appendChild(row);
    updateEmptyNotice();
}

document.getElementById('checkAll')?.addEventListener('change', function () {
    document.querySelectorAll('.movement-checkbox').forEach(cb => cb.checked = this.checked);
});

document.getElementById('addSelectedBtn')?.addEventListener('click', function () {
    const checked = document.querySelectorAll('.movement-checkbox:checked');
    if (!checked.length) { alert('Оберіть хоча б один товар'); return; }
    checked.forEach(cb => {
        const row = cb.closest('.movement-row');
        const qty = parseInt(row.querySelector('.writeoff-qty').value) || 1;
        addItem(cb.dataset.inventoryId, cb.dataset.itemName, cb.dataset.unit, qty, null, '');
        cb.checked = false;
    });
    document.getElementById('checkAll').checked = false;
});

document.getElementById('addCustomBtn').addEventListener('click', () => {
    addItem('', '', 'шт', 1, null, '');
});

// Завантажити поточні позиції заявки
@foreach($writeoffRequest->items as $item)
addItem(
    '{{ $item->inventory_id ?? '' }}',
    '{{ addslashes($item->item_name) }}',
    '{{ $item->unit }}',
    {{ $item->quantity }},
    {{ $item->id }},
    '{{ addslashes($item->notes ?? '') }}'
);
@endforeach

updateEmptyNotice();
</script>
@endpush
