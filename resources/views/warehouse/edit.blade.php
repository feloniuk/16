{{-- resources/views/warehouse/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редагувати товар: ' . $item->equipment_type)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Редагувати товар: {{ $item->equipment_type }}</h4>
                <p class="text-muted">Оновіть інформацію про товар</p>
            </div>
            
            <form method="POST" action="{{ route('warehouse.update', $item) }}">
                @csrf
                @method('PATCH')
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="equipment_type" class="form-label">Назва товару <span class="text-danger">*</span></label>
                        <input type="text" name="equipment_type" id="equipment_type" 
                               class="form-control @error('equipment_type') is-invalid @enderror" 
                               value="{{ old('equipment_type', $item->equipment_type) }}" required>
                        @error('equipment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="inventory_number" class="form-label">Код товару <span class="text-danger">*</span></label>
                        <input type="text" name="inventory_number" id="inventory_number" 
                               class="form-control @error('inventory_number') is-invalid @enderror" 
                               value="{{ old('inventory_number', $item->inventory_number) }}" required>
                        @error('inventory_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="category" class="form-label">Категорія</label>
                        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror">
                            <option value="">-- Виберіть категорію --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category', $item->category) === $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="unit" class="form-label">Одиниця виміру <span class="text-danger">*</span></label>
                        <input type="text" name="unit" id="unit" 
                               class="form-control @error('unit') is-invalid @enderror" 
                               value="{{ old('unit', $item->unit) }}" required list="unitList">
                        <datalist id="unitList">
                            <option value="шт">
                            <option value="кг">
                            <option value="л">
                            <option value="м">
                            <option value="упак">
                            <option value="пачка">
                            <option value="коробка">
                        </datalist>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="min_quantity" class="form-label">Мінімальна кількість <span class="text-danger">*</span></label>
                        <input type="number" name="min_quantity" id="min_quantity" 
                               class="form-control @error('min_quantity') is-invalid @enderror" 
                               value="{{ old('min_quantity', $item->min_quantity) }}" min="0" required>
                        @error('min_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="price" class="form-label">Ціна за одиницю (грн)</label>
                        <input type="number" step="0.01" name="price" id="price" 
                               class="form-control @error('price') is-invalid @enderror" 
                               value="{{ old('price', $item->price) }}" min="0">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="notes" class="form-label">Опис товару</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Детальний опис товару">{{ old('notes', $item->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <hr>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-2">Поточний залишок</h6>
                            <div class="fs-4 fw-bold">{{ $item->quantity }} {{ $item->unit }}</div>
                            <small class="text-muted">Змінюється тільки через операції надходження/видачі</small>
                        </div>
                    </div>
                    
                    @if($item->price && $item->quantity)
                    <div class="col-md-6">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <h6 class="text-muted mb-2">Вартість залишку</h6>
                            <div class="fs-4 fw-bold text-success">{{ number_format($item->quantity * $item->price, 2) }} грн</div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('warehouse.show', $item) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Зберегти зміни
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection