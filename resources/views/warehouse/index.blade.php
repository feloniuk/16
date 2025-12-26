{{-- resources/views/warehouse/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Склад')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('warehouse.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="category" class="form-label">Категорія</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">Всі категорії</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="search" class="form-label">Пошук</label>
                    <input type="text" name="search" id="search" class="form-control"
                           placeholder="Назва товару..." value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="low_stock" value="1"
                               id="low_stock" {{ request('low_stock') ? 'checked' : '' }}>
                        <label class="form-check-label" for="low_stock">
                            Мало на складі
                        </label>
                    </div>
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

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($lowStockCount > 0)
<div class="row mb-4">
    <div class="col">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Увага!</strong> {{ $lowStockCount }} найменувань з низькими залишками.
            <a href="{{ route('warehouse.index', ['low_stock' => 1]) }}" class="alert-link">Переглянути</a>
        </div>
    </div>
</div>
@endif

<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>
                Товари на складі ({{ $items->total() }} найменувань)
                <span class="badge bg-info">Згруповано</span>
            </h2>
            <div>
                <a href="{{ route('warehouse-inventory.quick') }}" class="btn btn-warning me-2">
                    <i class="bi bi-clipboard-check"></i> Швидка інвентаризація
                </a>
                <a href="{{ route('warehouse.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати товар
                </a>
            </div>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-body p-0">
        @if($items->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Найменування</th>
                            <th>Категорія</th>
                            <th width="120">Загальний залишок</th>
                            <th width="100">Одиниця</th>
                            <th width="100">Позицій в БД</th>
                            <th width="120">Сер. ціна</th>
                            <th width="200">Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        @php
                            $isLowStock = $item->total_quantity <= $item->min_quantity;
                        @endphp
                        <tr class="{{ $isLowStock ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $item->equipment_type }}</strong>
                            </td>
                            <td>{{ $item->category ?? '-' }}</td>
                            <td>
                                <span class="badge fs-6 {{ $isLowStock ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ $item->total_quantity }}
                                </span>
                                @if($isLowStock)
                                    <i class="bi bi-exclamation-triangle text-warning" title="Низький залишок"></i>
                                @endif
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->items_count }}</span>
                            </td>
                            <td>
                                @if($item->avg_price)
                                    {{ number_format($item->avg_price, 2) }} грн
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('warehouse.show-by-name', ['name' => $item->equipment_type]) }}"
                                       class="btn btn-sm btn-outline-primary" title="Детально">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($item->total_quantity > 0)
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                            onclick="showIssueModal('{{ addslashes($item->equipment_type) }}', {{ $item->total_quantity }}, '{{ addslashes($item->unit) }}')"
                                            title="Видати">
                                        <i class="bi bi-box-arrow-right"></i> Видати
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                            onclick="showReceiptModal('{{ addslashes($item->equipment_type) }}')"
                                            title="Надходження">
                                        <i class="bi bi-box-arrow-in-left"></i> Прихід
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-box fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Товари не знайдені</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку або додайте новий товар</p>
                <a href="{{ route('warehouse.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати товар
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($items->total() > 0)
<div class="stats-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Показано {{ $items->firstItem() }} - {{ $items->lastItem() }}
            з {{ $items->total() }} найменувань
        </div>
        <div>
            {{ $items->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endif

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('warehouse.receipt-by-name') }}">
                @csrf
                <input type="hidden" name="equipment_type" id="receiptEquipmentType">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-in-left text-success"></i> Надходження товару</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Товар</label>
                        <input type="text" class="form-control" id="receiptItemName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="receiptQuantity" class="form-label">Кількість <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="receiptQuantity" name="quantity" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="receiptDocument" class="form-label">Номер документа</label>
                        <input type="text" class="form-control" id="receiptDocument" name="document_number">
                    </div>
                    <div class="mb-3">
                        <label for="receiptNote" class="form-label">Примітка</label>
                        <textarea class="form-control" id="receiptNote" name="note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-box-arrow-in-left"></i> Зафіксувати надходження
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Issue Modal -->
<div class="modal fade" id="issueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('warehouse.issue-by-name') }}">
                @csrf
                <input type="hidden" name="equipment_type" id="issueEquipmentType">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-right text-info"></i> Видача товару</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Товар</label>
                        <input type="text" class="form-control" id="issueItemName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Доступно на складі</label>
                        <input type="text" class="form-control" id="issueAvailable" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="issueQuantity" class="form-label">Кількість <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="issueQuantity" name="quantity" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="issuedTo" class="form-label">Кому видано</label>
                        <input type="text" class="form-control" id="issuedTo" name="issued_to"
                               placeholder="ПІБ отримувача">
                    </div>
                    <div class="mb-3">
                        <label for="issueNote" class="form-label">Примітка</label>
                        <textarea class="form-control" id="issueNote" name="note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-box-arrow-right"></i> Видати
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showReceiptModal(equipmentType) {
    document.getElementById('receiptEquipmentType').value = equipmentType;
    document.getElementById('receiptItemName').value = equipmentType;
    document.getElementById('receiptQuantity').value = '';
    document.getElementById('receiptDocument').value = '';
    document.getElementById('receiptNote').value = '';
    new bootstrap.Modal(document.getElementById('receiptModal')).show();
}

function showIssueModal(equipmentType, available, unit) {
    document.getElementById('issueEquipmentType').value = equipmentType;
    document.getElementById('issueItemName').value = equipmentType;
    document.getElementById('issueAvailable').value = available + ' ' + unit;
    document.getElementById('issueQuantity').max = available;
    document.getElementById('issueQuantity').value = '';
    document.getElementById('issuedTo').value = '';
    document.getElementById('issueNote').value = '';
    new bootstrap.Modal(document.getElementById('issueModal')).show();
}
</script>
@endpush

@push('styles')
<style>
.pagination {
    margin: 0;
}
.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}
.table td {
    vertical-align: middle;
}
</style>
@endpush
