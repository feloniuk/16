@extends('layouts.app')

@section('title', 'Створити заявку на ремонт')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Створити заявку на ремонт</h4>
                <p class="text-muted">Вкажіть обладнання, що потребує ремонту, та опишіть проблеми</p>
            </div>

            <form method="POST" action="{{ route('repair-orders.store') }}" id="repairForm">
                @csrf
                <input type="hidden" name="status" id="formStatus" value="draft">

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="description" class="form-label">Опис заявки</label>
                        <input type="text" name="description" id="description"
                               class="form-control @error('description') is-invalid @enderror"
                               value="{{ old('description') }}" placeholder="Короткий опис заявки">
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="repair_master_id" class="form-label">Майстер з ремонту</label>
                        <select name="repair_master_id" id="repair_master_id"
                                class="form-select @error('repair_master_id') is-invalid @enderror">
                            <option value="">-- Виберіть майстра --</option>
                            @foreach($masters as $master)
                                <option value="{{ $master->id }}" {{ old('repair_master_id') == $master->id ? 'selected' : '' }}>
                                    {{ $master->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('repair_master_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Обладнання -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Обладнання для ремонту</h5>
                        <button type="button" class="btn btn-success btn-sm" onclick="addItemRow()">
                            <i class="bi bi-plus"></i> Додати обладнання
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="25%">Обладнання *</th>
                                    <th width="50%">Описання проблеми *</th>
                                    <th width="15%">Вартість</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Строки будут добавляться динамически -->
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="2" class="text-end">Загальна вартість:</th>
                                    <th id="totalCost">0.00 грн</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @error('items')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                    @error('items.*')
                        <div class="alert alert-danger mt-2">Перевірте дані в таблиці обладнання</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label">Додаткові примітки</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                              rows="3" placeholder="Додаткова інформація про ремонт">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('repair-orders.index') }}" class="btn btn-outline-secondary">
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

<!-- Modal для вибору обладнання -->
<div class="modal fade" id="equipmentSelectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Вибрати обладнання</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" id="equipmentSearch" class="form-control" placeholder="Пошук обладнання...">
                </div>
                <div id="equipmentList" style="max-height: 400px; overflow-y: auto;">
                    <!-- Items будут добавляться здесь -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let itemCounter = 0;
let currentRowIndex = -1;

const equipmentList = {!! json_encode($equipment->map(function($item) {
    return [
        'id' => $item->id,
        'equipment_type' => $item->equipment_type,
        'inventory_number' => $item->inventory_number,
        'branch_name' => $item->branch->name ?? '',
    ];
})) !!};

function addItemRow(itemData = null) {
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <button type="button" class="btn btn-outline-primary btn-sm w-100 text-start item-select-btn"
                    onclick="showEquipmentSelect(${itemCounter})">
                <span class="equipment-name">${itemData?.equipment_type || 'Вибрати обладнання...'}</span>
                <input type="hidden" name="items[${itemCounter}][equipment_id]" class="equipment-id" value="${itemData?.id || ''}">
            </button>
        </td>
        <td>
            <textarea name="items[${itemCounter}][repair_description]"
                      class="form-control form-control-sm repair-description" rows="2" required
                      placeholder="Опишіть проблему">${itemData?.repair_description || ''}</textarea>
        </td>
        <td>
            <input type="number" name="items[${itemCounter}][cost]"
                   class="form-control form-control-sm cost-input"
                   value="${itemData?.cost || ''}" step="0.01" min="0"
                   onchange="calculateTotal()" placeholder="0.00">
        </td>
        <td>
            <button type="button" class="btn btn-outline-danger btn-sm"
                    onclick="removeItemRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

    tbody.appendChild(row);
    itemCounter++;
    calculateTotal();
}

function removeItemRow(button) {
    if (document.querySelectorAll('#itemsTableBody tr').length > 1) {
        button.closest('tr').remove();
        calculateTotal();
    } else {
        alert('Має бути принаймні один предмет у заявці');
    }
}

function showEquipmentSelect(index) {
    currentRowIndex = index;
    const equipmentListDiv = document.getElementById('equipmentList');
    equipmentListDiv.innerHTML = equipmentList.map(item => `
        <div class="card mb-2">
            <div class="card-body p-2">
                <button type="button" class="btn btn-light w-100 text-start"
                        onclick="selectEquipment(${index}, ${item.id}, '${item.equipment_type.replace(/'/g, "\\'")}', '${item.branch_name}')">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${item.equipment_type}</strong>
                            <br><small class="text-muted">Інвентар: ${item.inventory_number}</small>
                        </div>
                        <div class="text-end">
                            <small class="badge bg-info">${item.branch_name}</small>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    `).join('');

    const modal = new bootstrap.Modal(document.getElementById('equipmentSelectModal'));
    modal.show();

    // Фільтрація при вводі
    document.getElementById('equipmentSearch').onchange = () => filterEquipment();
    document.getElementById('equipmentSearch').oninput = () => filterEquipment();
    document.getElementById('equipmentSearch').value = '';
}

function filterEquipment() {
    const searchValue = document.getElementById('equipmentSearch').value.toLowerCase();
    const items = equipmentList.filter(item =>
        item.equipment_type.toLowerCase().includes(searchValue) ||
        item.inventory_number.toLowerCase().includes(searchValue)
    );

    const equipmentListDiv = document.getElementById('equipmentList');
    equipmentListDiv.innerHTML = items.map(item => `
        <div class="card mb-2">
            <div class="card-body p-2">
                <button type="button" class="btn btn-light w-100 text-start"
                        onclick="selectEquipment(${currentRowIndex}, ${item.id}, '${item.equipment_type.replace(/'/g, "\\'")}', '${item.branch_name}')">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${item.equipment_type}</strong>
                            <br><small class="text-muted">Інвентар: ${item.inventory_number}</small>
                        </div>
                        <div class="text-end">
                            <small class="badge bg-info">${item.branch_name}</small>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    `).join('');
}

function selectEquipment(index, equipmentId, equipmentType, branchName) {
    const rows = document.querySelectorAll('#itemsTableBody tr');
    const row = rows[index];

    if (row) {
        row.querySelector('.equipment-name').textContent = equipmentType;
        row.querySelector('.equipment-id').value = equipmentId;
    }

    const modal = bootstrap.Modal.getInstance(document.getElementById('equipmentSelectModal'));
    modal.hide();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.cost-input').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalCost').textContent = total.toFixed(2) + ' грн';
}

document.addEventListener('DOMContentLoaded', () => {
    addItemRow();

    // Обработка действия формы
    document.querySelectorAll('[type="submit"][name="action"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const action = this.value;
            const statusInput = document.getElementById('formStatus');

            if (action === 'submit') {
                statusInput.value = 'pending_approval';
            } else if (action === 'save_draft') {
                statusInput.value = 'draft';
            }
        });
    });
});
</script>
@endpush
