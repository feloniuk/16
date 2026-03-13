{{-- resources/views/purchase-requests/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Заявка ' . $purchaseRequest->request_number)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Заявка {{ $purchaseRequest->request_number }}</h4>
                    <p class="text-muted mb-0">
                        Створена {{ $purchaseRequest->created_at->format('d.m.Y в H:i') }}
                        користувачем {{ $purchaseRequest->user->name }}
                    </p>
                </div>
                <div>
                    {!! $purchaseRequest->status_badge !!}
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Дата потреби</h6>
                    <p class="mb-0">{{ $purchaseRequest->requested_date->format('d.m.Y') }}</p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Загальна сума</h6>
                    <p class="mb-0">
                        @if($purchaseRequest->total_amount > 0)
                            <span class="fs-5 fw-bold text-success">{{ number_format($purchaseRequest->total_amount, 2) }} грн</span>
                        @else
                            <span class="text-muted">Не вказано</span>
                        @endif
                    </p>
                </div>
                
                @if($purchaseRequest->description)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Опис заявки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $purchaseRequest->description }}</p>
                    </div>
                </div>
                @endif
                
                @if($purchaseRequest->notes)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Примітки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $purchaseRequest->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
            
            <h5 class="mb-3">Товари для закупівлі</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            @if(in_array(Auth::user()->role, ['admin', 'warehouse_keeper']))
                                <th width="4%"><input type="checkbox" id="selectAllCheckbox" onchange="toggleAllCheckboxes()"></th>
                            @endif
                            <th width="5%">№</th>
                            <th>Назва товару</th>
                            <th>Код</th>
                            <th>Кількість</th>
                            <th>Одиниця</th>
                            <th>Очікувана ціна</th>
                            <th>Сума</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseRequest->items as $index => $item)
                        <tr>
                            @if(in_array(Auth::user()->role, ['admin', 'warehouse_keeper']))
                                <td>
                                    <input type="checkbox" class="item-checkbox" data-item-index="{{ $index }}" data-item-id="{{ $item->id }}" data-item-name="{{ $item->item_name }}" onchange="updateReceiveButtonVisibility()">
                                </td>
                            @endif
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->item_name }}</strong>
                                @if($item->specifications)
                                    <br><small class="text-muted">{{ $item->specifications }}</small>
                                @endif
                            </td>
                            <td>
                                @if($item->item_code)
                                    <code>{{ $item->item_code }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $item->quantity }}</span></td>
                            <td>{{ $item->unit }}</td>
                            <td>
                                @if($item->estimated_price)
                                    {{ number_format($item->estimated_price, 2) }} грн
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->estimated_price)
                                    <strong>{{ number_format($item->total, 2) }} грн</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="6" class="text-end">Загальна сума:</th>
                            <th>
                                @if($purchaseRequest->total_amount > 0)
                                    <strong class="text-success">{{ number_format($purchaseRequest->total_amount, 2) }} грн</strong>
                                @else
                                    <span class="text-muted">Не визначено</span>
                                @endif
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="stats-card p-4">
            <h5 class="mb-3">Інформація про заявку</h5>
            
            <div class="row g-3">
                <div class="col-12">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="fs-4 fw-bold">{{ $purchaseRequest->items->count() }}</div>
                        <small class="text-muted">Позицій у заявці</small>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                        <div class="fs-6 fw-bold text-info">{{ $purchaseRequest->items->sum('quantity') }}</div>
                        <small class="text-muted">Загальна кількість</small>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                        <div class="fs-6 fw-bold text-warning">{{ $purchaseRequest->items->whereNotNull('estimated_price')->count() }}</div>
                        <small class="text-muted">З цінами</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Дії</h5>

            <div class="d-grid gap-2">
                @if($purchaseRequest->status === 'draft' && ($purchaseRequest->user_id === Auth::id() || in_array(Auth::user()->role, ['admin', 'warehouse_keeper'])))
                    <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Редагувати заявку
                    </a>
                @endif

                @if($purchaseRequest->status === 'draft' && ($purchaseRequest->user_id === Auth::id() || in_array(Auth::user()->role, ['admin', 'warehouse_keeper'])))
                    <form method="POST" action="{{ route('purchase-requests.submit', $purchaseRequest) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Подати заявку на розгляд?')">
                            <i class="bi bi-send"></i> Подати заявку
                        </button>
                    </form>
                @endif

                @if($purchaseRequest->status === 'submitted' && in_array(Auth::user()->role, ['admin', 'director']))
                    <form method="POST" action="{{ route('purchase-requests.approve', $purchaseRequest) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Затвердити заявку?')">
                            <i class="bi bi-check-circle"></i> Затвердити
                        </button>
                    </form>
                    <form method="POST" action="{{ route('purchase-requests.reject', $purchaseRequest) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('Відхилити заявку?')">
                            <i class="bi bi-x-circle"></i> Відхилити
                        </button>
                    </form>
                @endif

                @if(in_array(Auth::user()->role, ['admin', 'warehouse_keeper']))
                    <button type="button" class="btn btn-success" id="receiveBtn" onclick="showReceiveModal()" style="display:none;">
                        <i class="bi bi-inbox"></i> Прийняти на склад
                    </button>
                @endif

                <a href="{{ route('purchase-requests.print', $purchaseRequest) }}"
                   class="btn btn-outline-success" target="_blank">
                    <i class="bi bi-printer"></i> Друкувати заявку
                </a>

                <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад до списку
                </a>
            </div>
        </div>
        
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Історія змін</h5>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Створено</h6>
                        <p class="timeline-text">{{ $purchaseRequest->created_at->format('d.m.Y H:i') }}</p>
                        <small class="text-muted">{{ $purchaseRequest->user->name }}</small>
                    </div>
                </div>
                
                @if($purchaseRequest->status !== 'draft')
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Статус змінено</h6>
                        <p class="timeline-text">{{ $purchaseRequest->updated_at->format('d.m.Y H:i') }}</p>
                        <small class="text-muted">
                            @switch($purchaseRequest->status)
                                @case('submitted') Подана на розгляд @break
                                @case('approved') Затверджена @break
                                @case('rejected') Відхилена @break
                                @case('completed') Виконана @break
                                @default {{ ucfirst($purchaseRequest->status) }}
                            @endswitch
                        </small>
                    </div>
                </div>
                @endif
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
                <div class="alert alert-info mt-3" id="receiveInfo">
                    <p class="mb-0">
                        <i class="bi bi-info-circle"></i>
                        Виберіть дію для кожного товару та введіть фактичну кількість
                    </p>
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

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 0.875rem;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 2px;
    font-size: 0.875rem;
}

.receive-row {
    display: grid;
    grid-template-columns: 1fr 0.8fr 1fr 1.2fr;
    gap: 10px;
    align-items: center;
}
</style>

@push('scripts')
<script>
const warehouseItemsData = {!! json_encode($purchaseRequest->items->map(function($item) {
    return [
        'id' => $item->id,
        'item_name' => $item->item_name,
        'quantity' => $item->quantity,
        'unit' => $item->unit,
        'warehouse_item_id' => $item->warehouse_item_id,
        'estimated_price' => $item->estimated_price,
    ];
})) !!};

function getSelectedItemIds() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    return Array.from(checkboxes).map(cb => ({
        index: parseInt(cb.dataset.itemIndex),
        id: parseInt(cb.dataset.itemId),
        name: cb.dataset.itemName
    }));
}

function updateReceiveButtonVisibility() {
    const selected = getSelectedItemIds();
    const receiveBtn = document.getElementById('receiveBtn');

    if (selected.length > 0) {
        receiveBtn.style.display = 'inline-block';
    } else {
        receiveBtn.style.display = 'none';
    }
}

function toggleAllCheckboxes() {
    const allCheckbox = document.getElementById('selectAllCheckbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(cb => cb.checked = allCheckbox.checked);
    updateReceiveButtonVisibility();
}

function showReceiveModal() {
    const selected = getSelectedItemIds();
    if (selected.length === 0) {
        alert('Будь ласка, виберіть хоча б один товар');
        return;
    }

    const tableBody = document.getElementById('receiveTableBody');
    tableBody.innerHTML = '';

    // Запит для пошуку товарів на складі
    const searchPromises = selected.map(itemSelected => {
        // Знаходимо дані товару з warehouseItemsData
        const itemData = warehouseItemsData.find(item => item.id === itemSelected.id);

        return fetch(`{{ route('api.warehouse-items.search') }}?q=${encodeURIComponent(itemSelected.name)}`)
            .then(r => r.json())
            .then(results => ({
                ...itemSelected,
                quantity: itemData ? itemData.quantity : 1,
                unit: itemData ? itemData.unit : 'шт',
                foundItems: results
            }))
    );

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
                    <input type="number" class="form-control form-control-sm receive-quantity" value="${itemData.quantity}" min="1" data-item-id="${itemData.id}">
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
    const selected = getSelectedItemIds();
    if (selected.length === 0) {
        alert('Будь ласка, виберіть товари');
        return;
    }

    const items = [];
    const rows = document.querySelectorAll('#receiveTableBody tr');

    rows.forEach((row, idx) => {
        const quantityInput = row.querySelector('.receive-quantity');
        const actionInput = row.querySelector('.receive-action');
        const inventoryInput = row.querySelector('.receive-inventory-id');
        const itemId = parseInt(quantityInput.dataset.itemId);

        const actualQuantity = parseInt(quantityInput.value);
        const action = actionInput.value;
        const existingInventoryId = inventoryInput.value;

        if (!actualQuantity || actualQuantity < 1) {
            alert('Вкажіть коректну кількість для всіх товарів');
            return;
        }

        if (!action) {
            alert('Оберіть дію для всіх товарів');
            return;
        }

        if (action === 'link_to_existing' && !existingInventoryId) {
            alert('Оберіть товар на складі для прив\'язання');
            return;
        }

        items.push({
            purchase_request_item_id: itemId,
            actual_quantity: actualQuantity,
            action: action,
            existing_inventory_id: existingInventoryId || null
        });
    });

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

// Ініціалізація при завантаженні
document.addEventListener('DOMContentLoaded', function() {
    updateReceiveButtonVisibility();
});
</script>
@endpush

@endsection