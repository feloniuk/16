@extends('layouts.app')

@section('title', 'Создать перемещение')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Создать новое перемещение</h4>
                <p class="text-muted">Выберите инвентарь для перемещения между филиалами</p>
            </div>
            
            <form method="POST" action="{{ route('inventory-transfers.store') }}" id="transferForm">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="from_branch_id" class="form-label">Филиал-источник <span class="text-danger">*</span></label>
                        <select name="from_branch_id" id="from_branch_id" class="form-select @error('from_branch_id') is-invalid @enderror" required>
                            <option value="">Выберите филиал</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('from_branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('from_branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="to_branch_id" class="form-label">Филиал-получатель <span class="text-danger">*</span></label>
                        <select name="to_branch_id" id="to_branch_id" class="form-select @error('to_branch_id') is-invalid @enderror" required>
                            <option value="">Выберите филиал</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="from_room" class="form-label">Кабинет-источник</label>
                        <input type="text" name="from_room" id="from_room" 
                               class="form-control @error('from_room') is-invalid @enderror" 
                               value="{{ old('from_room') }}" placeholder="Номер кабинета">
                        @error('from_room')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="to_room" class="form-label">Кабинет-получатель</label>
                        <input type="text" name="to_room" id="to_room" 
                               class="form-control @error('to_room') is-invalid @enderror" 
                               value="{{ old('to_room') }}" placeholder="Номер кабинета">
                        @error('to_room')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="transfer_date" class="form-label">Дата перемещения <span class="text-danger">*</span></label>
                        <input type="date" name="transfer_date" id="transfer_date" 
                               class="form-control @error('transfer_date') is-invalid @enderror" 
                               value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                        @error('transfer_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="reason" class="form-label">Причина перемещения <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" rows="3" 
                                  class="form-control @error('reason') is-invalid @enderror" 
                                  placeholder="Укажите причину перемещения инвентаря" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="notes" class="form-label">Дополнительные заметки</label>
                        <textarea name="notes" id="notes" rows="2" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Дополнительная информация">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Доступный инвентарь</h5>
                        <div class="border rounded p-3" style="height: 400px; overflow-y: auto;">
                            <div id="available-inventory">
                                <div class="text-muted text-center">
                                    <i class="bi bi-arrow-up"></i>
                                    <br>Выберите филиал-источник
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>К перемещению <span id="selected-count" class="badge bg-primary">0</span></h5>
                        <div class="border rounded p-3" style="height: 400px; overflow-y: auto;">
                            <div id="selected-inventory">
                                <div class="text-muted text-center">
                                    <i class="bi bi-inbox"></i>
                                    <br>Выберите инвентарь для перемещения
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('inventory-transfers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Отмена
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="bi bi-save"></i> Создать перемещение
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedItems = new Set();

document.getElementById('from_branch_id').addEventListener('change', function() {
    const branchId = this.value;
    if (branchId) {
        loadAvailableInventory(branchId);
    } else {
        document.getElementById('available-inventory').innerHTML = 
            '<div class="text-muted text-center"><i class="bi bi-arrow-up"></i><br>Выберите филиал-источник</div>';
    }
});

function loadAvailableInventory(branchId) {
    const container = document.getElementById('available-inventory');
    container.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Загрузка...</div>';
    
    fetch(`/api/inventory/branch/${branchId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.inventory.length > 0) {
                container.innerHTML = data.inventory.map(item => `
                    <div class="inventory-item p-2 mb-2 border rounded" data-id="${item.id}" style="cursor: pointer;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${item.inventory_number}</strong>
                                <div class="text-muted small">${item.equipment_type}</div>
                                ${item.brand || item.model ? `<div class="text-muted small">${item.brand || ''} ${item.model || ''}</div>` : ''}
                                <div class="text-muted small">Кабинет: ${item.room_number}</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary select-btn">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
                
                // Добавляем обработчики событий
                container.querySelectorAll('.inventory-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectItem(this);
                    });
                });
            } else {
                container.innerHTML = '<div class="text-muted text-center"><i class="bi bi-inbox"></i><br>Нет доступного инвентаря</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<div class="text-danger text-center">Ошибка загрузки данных</div>';
        });
}

function selectItem(element) {
    const itemId = element.dataset.id;
    
    if (!selectedItems.has(itemId)) {
        selectedItems.add(itemId);
        
        // Клонируем элемент в selected area
        const clone = element.cloneNode(true);
        clone.classList.add('bg-light');
        clone.querySelector('.select-btn').innerHTML = '<i class="bi bi-arrow-left"></i>';
        clone.querySelector('.select-btn').classList.remove('btn-outline-primary');
        clone.querySelector('.select-btn').classList.add('btn-outline-danger');
        
        clone.addEventListener('click', function() {
            unselectItem(this, itemId);
        });
        
        const selectedContainer = document.getElementById('selected-inventory');
        if (selectedContainer.querySelector('.text-muted')) {
            selectedContainer.innerHTML = '';
        }
        selectedContainer.appendChild(clone);
        
        // Скрываем в available area
        element.style.display = 'none';
        
        // Добавляем hidden input
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'inventory_ids[]';
        hiddenInput.value = itemId;
        hiddenInput.id = `selected-${itemId}`;
        document.getElementById('transferForm').appendChild(hiddenInput);
        
        updateSelectedCount();
    }
}

function unselectItem(element, itemId) {
    selectedItems.delete(itemId);
    element.remove();
    
    // Показываем обратно в available area
    document.querySelector(`[data-id="${itemId}"]`).style.display = 'block';
    
    // Удаляем hidden input
    document.getElementById(`selected-${itemId}`).remove();
    
    updateSelectedCount();
    
    // Если не осталось выбранных элементов
    if (selectedItems.size === 0) {
        document.getElementById('selected-inventory').innerHTML = 
            '<div class="text-muted text-center"><i class="bi bi-inbox"></i><br>Выберите инвентарь для перемещения</div>';
    }
}

function updateSelectedCount() {
    document.getElementById('selected-count').textContent = selectedItems.size;
    document.getElementById('submitBtn').disabled = selectedItems.size === 0;
}
</script>
@endpush