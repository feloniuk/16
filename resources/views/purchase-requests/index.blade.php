
@extends('layouts.app')

@section('title', 'Заявки на закупівлю')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('purchase-requests.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Всі статуси</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Чернетка</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Подана</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Затверджена</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Відхилена</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Виконана</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Дата від</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Дата до</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Знайти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Заявки на закупівлю ({{ $requests->total() }})</h2>
            <div>
                <a href="{{ route('purchase-requests.archiveIndex') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-archive"></i> Архів
                </a>
                <button type="button" class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#printSettingsModal" title="Налаштування документу">
                    <i class="bi bi-gear"></i>
                </button>
                <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Створити заявку
                </a>
            </div>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-body p-0">
        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>№ заявки</th>
                            <th>Ініціатор</th>
                            <th>Кількість позицій</th>
                            <th>Сума</th>
                            <th>Дата потреби</th>
                            <th>Статус</th>
                            <th>Створено</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>
                                <strong>{{ $request->request_number }}</strong>
                                @if($request->items->count() > 0)
                                    @php
                                        $itemsHtml = '<ul class="mb-0 ps-3">' . $request->items->map(fn($item) => '<li>' . e($item->item_name) . ' — <strong>' . $item->quantity . '</strong> ' . e($item->unit) . '</li>')->implode('') . '</ul>';
                                    @endphp
                                    <button type="button"
                                            class="btn btn-link p-0 ms-1 align-baseline text-info"
                                            data-bs-toggle="popover"
                                            data-bs-trigger="hover focus"
                                            data-bs-placement="right"
                                            data-bs-html="true"
                                            data-bs-title="Товари заявки"
                                            data-bs-content="{{ $itemsHtml }}"
                                            style="font-size:0.85rem;">
                                        <i class="bi bi-list-ul"></i>
                                    </button>
                                @endif
                            </td>
                            <td>{{ $request->user->name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $request->items_count }} поз.</span>
                            </td>
                            <td>
                                @if($request->total_amount > 0)
                                    <strong>{{ number_format($request->total_amount, 2) }} грн</strong>
                                @else
                                    <span class="text-muted">Не вказано</span>
                                @endif
                            </td>
                            <td>{{ $request->requested_date->format('d.m.Y') }}</td>
                            <td>{!! $request->status_badge !!}</td>
                            <td>
                                <div>{{ $request->created_at->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('purchase-requests.show', $request) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Переглянути">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('purchase-requests.edit', $request) }}"
                                       class="btn btn-sm btn-outline-warning" title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <a href="{{ route('purchase-requests.print', $request) }}"
                                       class="btn btn-sm btn-outline-success" title="Друк" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>

                                    @if(in_array(Auth::user()->role, ['admin', 'warehouse_keeper']) && $request->items->count() > 0)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-info"
                                            title="Прийняти на склад"
                                            onclick="openReceiveModalFromIndex({{ $request->id }}, '{{ $request->request_number }}')">
                                        <i class="bi bi-inbox"></i>
                                    </button>
                                    @endif

                                    @if($request->status === 'draft' && ($request->user_id === Auth::id() || in_array(Auth::user()->role, ['admin', 'warehouse_keeper'])))
                                    <form method="POST" action="{{ route('purchase-requests.submit', $request) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success"
                                                title="Подати заявку"
                                                onclick="return confirm('Подати заявку на розгляд?')">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if(in_array(Auth::user()->role, ['admin', 'director']))
                                    <form method="POST" action="{{ route('purchase-requests.archive', $request) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                title="Архівувати"
                                                onclick="return confirm('Архівувати заявку {{ $request->request_number }}?')">
                                            <i class="bi bi-archive"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-clipboard-data fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Заявки не знайдені</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку або створіть нову заявку</p>
                <a href="{{ route('purchase-requests.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Створити заявку
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($requests->total() > 0)
<div class="stats-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Показано {{ $requests->firstItem() }} - {{ $requests->lastItem() }}
            з {{ $requests->total() }} записів
        </div>
        <div>
            {{ $requests->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endif

<!-- Modal налаштувань документу -->
<div class="modal fade" id="printSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-gear me-2"></i>Налаштування документу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Директор (підпис)</label>
                    <input type="text" id="settingDirector" class="form-control"
                           value="{{ \App\Models\AppSetting::get('print_director', 'Ганна ПАВЛЕГА') }}">
                    <div class="form-text">Відображається у правому верхньому куті документу</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Заст. директора з АГР</label>
                    <input type="text" id="settingDeputy" class="form-control"
                           value="{{ \App\Models\AppSetting::get('print_deputy_director', 'Олександр ХРОМЧЕНКО') }}">
                    <div class="form-text">Відображається у рядку підпису внизу документу</div>
                </div>
                <div id="printSettingsAlert" class="alert d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="button" class="btn btn-primary" onclick="savePrintSettings()">
                    <i class="bi bi-check-lg"></i> Зберегти
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal для оприходування товарів -->
<div class="modal fade" id="receiveModalIndex" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Прийняти товари на склад - <span id="receiveModalTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="selectAllCheckboxIndex" onchange="toggleAllCheckboxesIndex()">
                                </th>
                                <th>Позиція</th>
                                <th>Замовлено</th>
                                <th>Факт (шт)</th>
                                <th>Склад</th>
                            </tr>
                        </thead>
                        <tbody id="receiveTableBodyIndex">
                            <!-- Items будуть добавляться здесь -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="button" class="btn btn-success" onclick="executeReceiveFromIndex()">
                    <i class="bi bi-check-lg"></i> Прийняти на склад
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.pagination {
    margin: 0;
}
.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
@endpush

@push('scripts')
<script>
function savePrintSettings() {
    const deputy = document.getElementById('settingDeputy').value.trim();
    const director = document.getElementById('settingDirector').value.trim();
    const alertEl = document.getElementById('printSettingsAlert');

    if (!deputy || !director) {
        alertEl.className = 'alert alert-danger';
        alertEl.textContent = 'Заповніть обидва поля';
        return;
    }

    fetch('{{ route('purchase-requests.print-settings') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ deputy_director: deputy, director: director }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alertEl.className = 'alert alert-success';
            alertEl.textContent = 'Збережено';
            setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('printSettingsModal')).hide(), 800);
        } else {
            alertEl.className = 'alert alert-danger';
            alertEl.textContent = 'Помилка збереження';
        }
    })
    .catch(() => {
        alertEl.className = 'alert alert-danger';
        alertEl.textContent = 'Помилка з\'єднання';
    });
}

// Дані про заявки для оприходування
const purchaseRequestsData = {!! json_encode($requests->map(function($request) {
    return [
        'id' => $request->id,
        'request_number' => $request->request_number,
        'items' => $request->items->map(function($item) {
            return [
                'id' => $item->id,
                'item_name' => $item->item_name,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
            ];
        })->toArray()
    ];
})) !!};

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
        new bootstrap.Popover(el);
    });
});

function openReceiveModalFromIndex(requestId, requestNumber) {
    const requestData = purchaseRequestsData.find(r => r.id === requestId);
    if (!requestData || !requestData.items.length) {
        alert('Товари не знайдені');
        return;
    }

    document.getElementById('receiveModalTitle').textContent = requestNumber;
    const tableBody = document.getElementById('receiveTableBodyIndex');

    // Показуємо модал з лоадером одразу
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <div class="mt-2"><strong>Завантажуємо товари зі складу...</strong></div>
            </td>
        </tr>
    `;

    const modal = new bootstrap.Modal(document.getElementById('receiveModalIndex'));
    modal.show();

    // Батч-запит для пошуку товарів на складі
    const queries = requestData.items.map(item => item.item_name);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                      document.querySelector('input[name="_token"]')?.value || '';

    fetch(`{{ route('api.warehouse-items.search-batch') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ queries })
    })
        .then(r => r.json())
        .then(batchResults => {
            const itemsWithResults = requestData.items.map(item => ({
                ...item,
                foundItems: batchResults[item.item_name] || []
            }));
            renderReceiveRowsIndex(itemsWithResults, tableBody);
        })
        .catch(err => {
            console.error('Batch search error:', err);
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Помилка завантаження товарів</td></tr>';
        });
}

function renderReceiveRowsIndex(itemsWithResults, tableBody) {
    tableBody.innerHTML = '';

    itemsWithResults.forEach((itemData) => {
        const foundItem = itemData.foundItems.length > 0 ? itemData.foundItems[0] : null;

        const row = document.createElement('tr');
        let warehouseHTML = '';

        if (foundItem) {
            const displayName = (foundItem.full_name && foundItem.full_name.trim() !== '') ? foundItem.full_name : foundItem.name;
            warehouseHTML = `
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-success text-white">✓ Знайдено</span>
                    <input type="text" class="form-control form-control-sm" value="${displayName} (${foundItem.code})" readonly>
                    <input type="hidden" class="receive-action" value="update_existing">
                    <input type="hidden" class="receive-inventory-id" value="${foundItem.id}">
                </div>
            `;
        } else {
            warehouseHTML = `
                <div class="d-flex gap-2">
                    <div class="flex-grow-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-info text-white">+ Новий</span>
                            <input type="text" class="form-control form-control-sm" value="Буде створено новий товар" readonly>
                        </div>
                        <input type="hidden" class="receive-inventory-id" value="">
                        <input type="hidden" class="receive-action" value="create_new">
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="switchToExistingIndex(this)" title="Прив'язати до існуючого">
                        <i class="bi bi-link"></i>
                    </button>
                </div>
                <div class="receive-existing-select" style="display:none; margin-top: 8px;">
                    <input type="text" class="form-control form-control-sm receive-search" placeholder="Пошук товару..." onkeyup="searchWarehouseItemsIndex(this)">
                    <div class="receive-search-results" style="max-height: 150px; overflow-y: auto; margin-top: 5px;"></div>
                </div>
            `;
        }

        row.innerHTML = `
            <td>
                <input type="checkbox" class="item-checkbox-index" data-item-id="${itemData.id}" checked>
            </td>
            <td>
                <strong>${itemData.item_name}</strong>
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

    document.getElementById('selectAllCheckboxIndex').checked = true;
}

function toggleAllCheckboxesIndex() {
    const allCheckbox = document.getElementById('selectAllCheckboxIndex');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox-index');
    itemCheckboxes.forEach(cb => cb.checked = allCheckbox.checked);
}

function updateReceiveActionSelectIndex(selectElement) {
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

function switchToExistingIndex(button) {
    const td = button.closest('td');
    const actionInput = td.querySelector('.receive-action');
    const displayDiv = button.parentElement.querySelector('.input-group');
    const existingSelect = td.querySelector('.receive-existing-select');
    const searchInput = existingSelect.querySelector('.receive-search');

    actionInput.value = 'link_to_existing';
    displayDiv.style.display = 'none';
    button.style.display = 'none';
    existingSelect.style.display = 'block';
    searchInput.focus();
}

function searchWarehouseItemsIndex(inputElement) {
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
                <div class="card mb-1" style="cursor: pointer;" onclick="selectWarehouseItemIndex(this, ${item.id}, '${item.name}')">
                    <div class="card-body p-2">
                        <div><strong>${item.name}</strong></div>
                        <small class="text-muted">Код: ${item.code || 'N/A'}</small>
                    </div>
                </div>
            `).join('');
        });
}

function selectWarehouseItemIndex(element, itemId, itemName) {
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

function executeReceiveFromIndex() {
    const currentRequestNumber = document.getElementById('receiveModalTitle').textContent;
    const requestData = purchaseRequestsData.find(r => r.request_number === currentRequestNumber);

    if (!requestData) {
        alert('Помилка: не вдалося знайти заявку');
        return;
    }

    const items = [];
    const rows = document.querySelectorAll('#receiveTableBodyIndex tr');

    rows.forEach((row, idx) => {
        const checkbox = row.querySelector('.item-checkbox-index');

        if (!checkbox.checked) {
            return;
        }

        const quantityInput = row.querySelector('.receive-quantity');
        const actionInput = row.querySelector('.receive-action');
        const inventoryInput = row.querySelector('.receive-inventory-id');
        const itemId = parseInt(checkbox.dataset.itemId);

        const actualQuantity = parseInt(quantityInput.value);
        const action = actionInput.value;
        const existingInventoryId = inventoryInput.value;

        if (!actualQuantity || actualQuantity < 1) {
            alert('Вкажіть коректну кількість для всіх обраних товарів');
            throw new Error('Invalid quantity');
        }

        if (!action) {
            alert('Оберіть дію для всіх обраних товарів');
            throw new Error('No action selected');
        }

        if (action === 'link_to_existing' && !existingInventoryId) {
            alert('Оберіть товар на складі для прив\'язання');
            throw new Error('No inventory selected');
        }

        items.push({
            purchase_request_item_id: itemId,
            actual_quantity: actualQuantity,
            action: action,
            existing_inventory_id: existingInventoryId || null
        });
    });

    if (items.length === 0) {
        alert('Виберіть принаймні один товар для оприходування');
        return;
    }

    // Відправляємо запит на сервер
    fetch(`/purchase-requests/${requestData.id}/receive`, {
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

@endsection