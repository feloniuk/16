@extends('layouts.app')

@section('title', 'Редагувати обладнання')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Редагувати обладнання</h4>
                <p class="text-muted">Інвентарний номер: <strong>{{ $inventory->inventory_number }}</strong></p>
            </div>
            
            <form method="POST" action="{{ route('inventory.update', $inventory) }}">
                @csrf
                @method('PUT')
                
                <!-- Локація -->
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Локація</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="branch_id" class="form-label">Філія <span class="text-danger">*</span></label>
                                <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                    <option value="">Оберіть філію</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $inventory->branch_id) == $branch->id ? 'selected' : '' }}>
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
                                       value="{{ old('room_number', $inventory->room_number) }}" 
                                       placeholder="Наприклад: 101, Кабінет директора"
                                       required>
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Інформація про обладнання -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-laptop"></i> Інформація про обладнання</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="equipment_type" class="form-label">Тип обладнання <span class="text-danger">*</span></label>
                                <input type="text" name="equipment_type" id="equipment_type" 
                                       class="form-control @error('equipment_type') is-invalid @enderror" 
                                       value="{{ old('equipment_type', $inventory->equipment_type) }}" 
                                       placeholder="Комп'ютер, Принтер..."
                                       required list="equipmentTypeList">
                                <datalist id="equipmentTypeList">
                                    <option value="Комп'ютер">
                                    <option value="Ноутбук">
                                    <option value="Принтер">
                                    <option value="МФУ">
                                    <option value="Сканер">
                                    <option value="Монітор">
                                    <option value="Клавіатура">
                                    <option value="Миша">
                                    <option value="ДБЖ">
                                </datalist>
                                @error('equipment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="balance_code" class="form-label">Код балансу</label>
                                <input type="text" name="balance_code" id="balance_code" 
                                       class="form-control @error('balance_code') is-invalid @enderror" 
                                       value="{{ old('balance_code', $inventory->balance_code) }}" 
                                       placeholder="Код балансу"
                                       list="balanceCodeList">
                                <datalist id="balanceCodeList">
                                    @foreach($balanceCodes as $code)
                                        <option value="{{ $code }}">
                                    @endforeach
                                </datalist>
                                @error('balance_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="brand" class="form-label">Бренд</label>
                                <input type="text" name="brand" id="brand" 
                                       class="form-control @error('brand') is-invalid @enderror" 
                                       value="{{ old('brand', $inventory->brand) }}" 
                                       placeholder="HP, Dell..." list="brandList">
                                <datalist id="brandList">
                                    <option value="HP">
                                    <option value="Dell">
                                    <option value="Lenovo">
                                    <option value="Asus">
                                    <option value="Canon">
                                    <option value="Epson">
                                </datalist>
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="model" class="form-label">Модель</label>
                                <input type="text" name="model" id="model" 
                                       class="form-control @error('model') is-invalid @enderror" 
                                       value="{{ old('model', $inventory->model) }}" 
                                       placeholder="Model">
                                @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="serial_number" class="form-label">Серійний номер</label>
                                <input type="text" name="serial_number" id="serial_number" 
                                       class="form-control @error('serial_number') is-invalid @enderror" 
                                       value="{{ old('serial_number', $inventory->serial_number) }}" 
                                       placeholder="S/N">
                                @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="inventory_number" class="form-label">Інвентарний номер <span class="text-danger">*</span></label>
                                <input type="text" name="inventory_number" id="inventory_number" 
                                       class="form-control @error('inventory_number') is-invalid @enderror" 
                                       value="{{ old('inventory_number', $inventory->inventory_number) }}" 
                                       placeholder="INV-001" 
                                       required>
                                @error('inventory_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="category" class="form-label">Категорія</label>
                                <input type="text" name="category" id="category" 
                                       class="form-control @error('category') is-invalid @enderror" 
                                       value="{{ old('category', $inventory->category) }}" 
                                       placeholder="Категорія">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кількість та ціна -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-calculator"></i> Кількість та ціна</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="quantity" class="form-label">Кількість <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantity" 
                                       class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', $inventory->quantity) }}" 
                                       min="0" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="unit" class="form-label">Одиниця виміру</label>
                                <input type="text" name="unit" id="unit" 
                                       class="form-control @error('unit') is-invalid @enderror" 
                                       value="{{ old('unit', $inventory->unit ?? 'шт') }}" 
                                       placeholder="шт, кг...">
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="price" class="form-label">Ціна за одиницю (грн)</label>
                                <input type="number" name="price" id="price" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       value="{{ old('price', $inventory->price) }}" 
                                       step="0.01" min="0"
                                       placeholder="0.00">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="min_quantity" class="form-label">Мінімальна кількість</label>
                                <input type="number" name="min_quantity" id="min_quantity" 
                                       class="form-control @error('min_quantity') is-invalid @enderror" 
                                       value="{{ old('min_quantity', $inventory->min_quantity ?? 0) }}" 
                                       min="0">
                                <small class="form-text text-muted">Для складу</small>
                                @error('min_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Примітки -->
                <div class="card mb-4">
                    <div class="card-body">
                        <label for="notes" class="form-label">
                            <i class="bi bi-sticky"></i> Примітки
                        </label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" 
                                  placeholder="Додаткова інформація про обладнання...">{{ old('notes', $inventory->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Зберегти зміни
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card-header {
    font-weight: 500;
}
</style>
@endpush