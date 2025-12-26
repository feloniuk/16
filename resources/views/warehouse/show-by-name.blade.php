{{-- resources/views/warehouse/show-by-name.blade.php --}}
@extends('layouts.app')

@section('title', $equipmentType . ' - Склад')

@section('content')
<div class="row mb-4">
    <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('warehouse.index') }}">Склад</a></li>
                <li class="breadcrumb-item active">{{ $equipmentType }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>{{ $equipmentType }}</h2>
                <p class="text-muted mb-0">
                    Загальна кількість: <span class="badge bg-primary fs-6">{{ $totalQuantity }}</span>
                    | Записів в БД: <span class="badge bg-secondary">{{ $items->count() }}</span>
                </p>
            </div>
            <div>
                @if($totalQuantity > 0)
                <button type="button" class="btn btn-info me-2"
                        onclick="showIssueModal('{{ addslashes($equipmentType) }}', {{ $totalQuantity }}, '{{ addslashes($items->first()->unit) }}')">
                    <i class="bi bi-box-arrow-right"></i> Видати
                </button>
                @endif
                <button type="button" class="btn btn-success me-2"
                        onclick="showReceiptModal('{{ addslashes($equipmentType) }}')">
                    <i class="bi bi-box-arrow-in-left"></i> Прихід
                </button>
                <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="stats-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Код (Інв. номер)</th>
                        <th>Категорія</th>
                        <th>Кабінет</th>
                        <th>Залишок</th>
                        <th>Одиниця</th>
                        <th>Мін. к-сть</th>
                        <th>Ціна</th>
                        <th>Примітки</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr class="{{ $item->isLowStock() ? 'table-warning' : '' }}">
                        <td><code>{{ $item->inventory_number }}</code></td>
                        <td>{{ $item->category ?? '-' }}</td>
                        <td>{{ $item->room_number }}</td>
                        <td>
                            <span class="badge {{ $item->isLowStock() ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ $item->quantity }}
                            </span>
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
                        <td>{{ Str::limit($item->notes, 30) }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('warehouse.show', $item) }}"
                                   class="btn btn-outline-primary" title="Переглянути">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('warehouse.edit', $item) }}"
                                   class="btn btn-outline-warning" title="Редагувати">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

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
