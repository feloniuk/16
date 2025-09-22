@extends('layouts.app')

@section('title', 'Додати товар на склад')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Додати новий товар на склад</h4>
                <p class="text-muted">Заповніть інформацію про товар</p>
            </div>
            
            <form method="POST" action="{{ route('warehouse.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Назва товару <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="code" class="form-label">Код товару <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" 
                               class="form-control @error('code') is-invalid @enderror" 
                               value="{{ old('code') }}" required placeholder="Унікальний код">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="category" class="form-label">Категорія</label>
                        <input type="text" name="category" id="category" 
                               class="form-control @error('category') is-invalid @enderror" 
                               value="{{ old('category') }}" list="categoryList">
                        <datalist id="categoryList">
                            @foreach($categories as $category)
                                <option value="{{ $category }}">
                            @endforeach
                        </datalist>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="unit" class="form-label">Одиниця виміру <span class="text-danger">*</span></label>
                        <input type="text" name="unit" id="unit" 
                               class="form-control @error('unit') is-invalid @enderror" 
                               value="{{ old('unit', 'шт') }}" required list="unitList">
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
                        <label for="quantity" class="form-label">Початкова кількість <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" 
                               class="form-control @error('quantity') is-invalid @enderror" 
                               value="{{ old('quantity', 0) }}" min="0" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="min_quantity" class="form-label">Мінімальна кількість <span class="text-danger">*</span></label>
                        <input type="number" name="min_quantity" id="min_quantity" 
                               class="form-control @error('min_quantity') is-invalid @enderror" 
                               value="{{ old('min_quantity', 0) }}" min="0" required>
                        <small class="form-text text-muted">Кількість для попередження про низькі залишки</small>
                        @error('min_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="price" class="form-label">Ціна за одиницю (грн)</label>
                        <input type="number" step="0.01" name="price" id="price" 
                               class="form-control @error('price') is-invalid @enderror" 
                               value="{{ old('price') }}" min="0">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="description" class="form-label">Опис товару</label>
                        <textarea name="description" id="description" rows="3" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Детальний опис товару">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Зберегти товар
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
