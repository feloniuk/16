@extends('layouts.app')

@section('title', 'Додати обладнання')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="stats-card p-4">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4>Додати нове обладнання</h4>
                    <p class="text-muted mb-0">Заповніть форму для додавання обладнання в інвентар</p>
                </div>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="mode" id="mode-single" checked>
                    <label class="btn btn-outline-primary" for="mode-single" onclick="switchMode('single')">
                        <i class="bi bi-file-earmark-plus"></i> Одиночне
                    </label>
                    
                    <input type="radio" class="btn-check" name="mode" id="mode-bulk">
                    <label class="btn btn-outline-primary" for="mode-bulk" onclick="switchMode('bulk')">
                        <i class="bi bi-files"></i> Масове
                    </label>
                </div>
            </div>
            
            <!-- Одиночне додавання -->
            <form method="POST" action="{{ route('inventory.store') }}" id="single-form">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="branch_id" class="form-label">Філія <span class="text-danger">*</span></label>
                        <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                            <option value="">Оберіть філію</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="room_number" class="form-label">Номер кабінету <span class="text-danger">*</span></label>
                        <input type="text" name="room_number" id="room_number" 
                               class="form-control @error('room_number') is-invalid @enderror" 
                               value="{{ old('room_number') }}" required>
                        @error('room_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="equipment_type" class="form-label">Тип обладнання <span class="text-danger">*</span></label>
                        <input type="text" name="equipment_type" id="equipment_type" 
                               class="form-control @error('equipment_type') is-invalid @enderror" 
                               value="{{ old('equipment_type') }}" 
                               placeholder="Наприклад: Комп'ютер, Принтер, Монітор" required>
                        @error('equipment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="inventory_number" class="form-label">Інвентарний номер <span class="text-danger">*</span></label>
                        <input type="text" name="inventory_number" id="inventory_number" 
                               class="form-control @error('inventory_number') is-invalid @enderror" 
                               value="{{ old('inventory_number') }}" required>
                        @error('inventory_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="brand" class="form-label">Бренд</label>
                        <input type="text" name="brand" id="brand" 
                               class="form-control @error('brand') is-invalid @enderror" 
                               value="{{ old('brand') }}" placeholder="HP, Dell, Lenovo...">
                        @error('brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="model" class="form-label">Модель</label>
                        <input type="text" name="model" id="model" 
                               class="form-control @error('model') is-invalid @enderror" 
                               value="{{ old('model') }}">
                        @error('model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="serial_number" class="form-label">Серійний номер</label>
                        <input type="text" name="serial_number" id="serial_number" 
                               class="form-control @error('serial_number') is-invalid @enderror" 
                               value="{{ old('serial_number') }}">
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="notes" class="form-label">Примітки</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Додаткова інформація про обладнання">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Зберегти
                    </button>
                </div>
            </form>

            <!-- Масове додавання -->
            <form method="POST" action="{{ route('inventory.store-bulk') }}" id="bulk-form" style="display: none;">
                @csrf
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="bulk_branch_id" class="form-label">Філія <span class="text-danger">*</span></label>
                        <select name="branch_id" id="bulk_branch_id" class="form-select" required>
                            <option value="">Оберіть філію</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="bulk_room_number" class="form-label">Номер кабінету <span class="text-danger">*</span></label>
                        <input type="text" name="room_number" id="bulk_room_number" 
                               class="form-control" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Список обладнання</h5>
                    <button type="button" class="btn btn-sm btn-success" onclick="addBulkItem()">
                        <i class="bi bi-plus-circle"></i> Додати позицію
                    </button>
                </div>

                <div id="bulk-items-container">
                    <!-- Перша позиція за замовчуванням -->
                    <div class="bulk-item card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="mb-0">Позиція #1</h6>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeBulkItem(this)" style="display: none;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Тип обладнання <span class="text-danger">*</span></label>
                                    <input type="text" name="items[0][equipment_type]" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Інвентарний № <span class="text-danger">*</span></label>
                                    <input type="text" name="items[0][inventory_number]" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Бренд</label>
                                    <input type="text" name="items[0][brand]" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Модель</label>
                                    <input type="text" name="items[0][model]" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Серійний номер</label>
                                    <input type="text" name="items[0][serial_number]" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Примітки</label>
                                    <input type="text" name="items[0][notes]" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Зберегти всі
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let bulkItemCounter = 1;

function switchMode(mode) {
    const singleForm = document.getElementById('single-form');
    const bulkForm = document.getElementById('bulk-form');
    
    if (mode === 'single') {
        singleForm.style.display = 'block';
        bulkForm.style.display = 'none';
    } else {
        singleForm.style.display = 'none';
        bulkForm.style.display = 'block';
    }
}

function addBulkItem() {
    const container = document.getElementById('bulk-items-container');
    const itemCount = container.children.length;
    
    const newItem = document.createElement('div');
    newItem.className = 'bulk-item card mb-3';
    newItem.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h6 class="mb-0">Позиція #${itemCount + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeBulkItem(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Тип обладнання <span class="text-danger">*</span></label>
                    <input type="text" name="items[${itemCount}][equipment_type]" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Інвентарний № <span class="text-danger">*</span></label>
                    <input type="text" name="items[${itemCount}][inventory_number]" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Бренд</label>
                    <input type="text" name="items[${itemCount}][brand]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Модель</label>
                    <input type="text" name="items[${itemCount}][model]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Серійний номер</label>
                    <input type="text" name="items[${itemCount}][serial_number]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Примітки</label>
                    <input type="text" name="items[${itemCount}][notes]" class="form-control">
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(newItem);
    
    // Показуємо кнопку видалення для всіх позицій, якщо їх більше однієї
    if (container.children.length > 1) {
        container.querySelectorAll('.bulk-item .btn-danger').forEach(btn => {
            btn.style.display = 'inline-block';
        });
    }
}

function removeBulkItem(button) {
    const container = document.getElementById('bulk-items-container');
    const item = button.closest('.bulk-item');
    
    if (container.children.length > 1) {
        item.remove();
        
        // Оновлюємо номери позицій
        container.querySelectorAll('.bulk-item').forEach((item, index) => {
            item.querySelector('h6').textContent = `Позиція #${index + 1}`;
            
            // Оновлюємо індекси в name атрибутах
            item.querySelectorAll('input').forEach(input => {
                const name = input.name;
                input.name = name.replace(/items\[\d+\]/, `items[${index}]`);
            });
        });
        
        // Ховаємо кнопку видалення, якщо залишилась одна позиція
        if (container.children.length === 1) {
            container.querySelector('.btn-danger').style.display = 'none';
        }
    }
}
</script>
@endpush