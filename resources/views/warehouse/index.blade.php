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
                           placeholder="Назва, код або опис товару..." value="{{ request('search') }}">
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

@if($lowStockCount > 0)
<div class="row mb-4">
    <div class="col">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong>Увага!</strong> {{ $lowStockCount }} товарів з низькими залишками.
            <a href="{{ route('warehouse.index', ['low_stock' => 1]) }}" class="alert-link">Переглянути</a>
        </div>
    </div>
</div>
@endif

<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Товари на складі ({{ $items->total() }})</h2>
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
                            <th>Код</th>
                            <th>Назва</th>
                            <th>Категорія</th>
                            <th>Залишок</th>
                            <th>Одиниця</th>
                            <th>Мін. кількість</th>
                            <th>Ціна</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr class="{{ $item->isLowStock() ? 'table-warning' : '' }}">
                            <td><code>{{ $item->code }}</code></td>
                            <td>
                                <div>
                                    <strong>{{ $item->name }}</strong>
                                    @if($item->description)
                                        <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $item->category ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $item->isLowStock() ? 'bg-warning' : 'bg-success' }}">
                                    {{ $item->quantity }}
                                </span>
                                @if($item->isLowStock())
                                    <i class="bi bi-exclamation-triangle text-warning" title="Низький залишок"></i>
                                @endif
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->min_quantity }}</td>
                            <td>
                                @if($item->price)
                                    {{ number_format($item->price, 2) }} грн
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success">Активний</span>
                                @else
                                    <span class="badge bg-secondary">Неактивний</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('warehouse.show', $item) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Переглянути">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('warehouse.edit', $item) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($item->quantity > 0)
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            onclick="showIssueModal({{ $item->id }}, '{{ $item->name }}', {{ $item->quantity }})"
                                            title="Видати">
                                        <i class="bi bi-box-arrow-right"></i>
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-success"
                                            onclick="showReceiptModal({{ $item->id }}, '{{ $item->name }}')"
                                            title="Надходження">
                                        <i class="bi bi-box-arrow-in-left"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white">
                {{ $items->withQueryString()->links() }}
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

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="receiptForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Надходження товару</h5>
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
                    <button type="submit" class="btn btn-success">Зафіксувати надходження</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Issue Modal -->
<div class="modal fade" id="issueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="issueForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Видача товару</h5>
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
                    <button type="submit" class="btn btn-warning">Видати</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showReceiptModal(itemId, itemName) {
    document.getElementById('receiptItemName').value = itemName;
    document.getElementById('receiptForm').action = `/warehouse/${itemId}/receipt`;
    
    // Очищаем форму
    document.getElementById('receiptQuantity').value = '';
    document.getElementById('receiptDocument').value = '';
    document.getElementById('receiptNote').value = '';
    
    new bootstrap.Modal(document.getElementById('receiptModal')).show();
}

function showIssueModal(itemId, itemName, available) {
    document.getElementById('issueItemName').value = itemName;
    document.getElementById('issueAvailable').value = available + ' шт';
    document.getElementById('issueQuantity').max = available;
    document.getElementById('issueForm').action = `/warehouse/${itemId}/issue`;
    
    // Очищаем форму
    document.getElementById('issueQuantity').value = '';
    document.getElementById('issuedTo').value = '';
    document.getElementById('issueNote').value = '';
    
    new bootstrap.Modal(document.getElementById('issueModal')).show();
}
</script>
@endpush