{{-- resources/views/repair-tracking/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Додати запис про ремонт')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Додати запис про відправку на ремонт</h4>
                <p class="text-muted">Заповніть форму для створення запису</p>
            </div>
            
            <form method="POST" action="{{ route('repair-tracking.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="equipment_id" class="form-label">Обладнання <span class="text-danger">*</span></label>
                        <select name="equipment_id" id="equipment_id" class="form-select @error('equipment_id') is-invalid @enderror" required>
                            <option value="">Оберіть обладнання</option>
                            @foreach($equipment as $item)
                                <option value="{{ $item->id }}" {{ old('equipment_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->equipment_type }} - {{ $item->inventory_number }} ({{ $item->branch->name }}, кімн. {{ $item->room_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('equipment_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="repair_master_id" class="form-label">Майстер</label>
                        <select name="repair_master_id" id="repair_master_id" class="form-select @error('repair_master_id') is-invalid @enderror">
                            <option value="">Оберіть майстра</option>
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
                    
                    <div class="col-md-6">
                        <label for="sent_date" class="form-label">Дата відправки <span class="text-danger">*</span></label>
                        <input type="date" name="sent_date" id="sent_date" 
                               class="form-control @error('sent_date') is-invalid @enderror" 
                               value="{{ old('sent_date', date('Y-m-d')) }}" required>
                        @error('sent_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="invoice_number" class="form-label">Номер накладної</label>
                        <input type="text" name="invoice_number" id="invoice_number" 
                               class="form-control @error('invoice_number') is-invalid @enderror" 
                               value="{{ old('invoice_number') }}">
                        @error('invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="cost" class="form-label">Вартість ремонту (грн)</label>
                        <input type="number" step="0.01" name="cost" id="cost" 
                               class="form-control @error('cost') is-invalid @enderror" 
                               value="{{ old('cost') }}" placeholder="0.00">
                        @error('cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="our_description" class="form-label">Опис поломки <span class="text-danger">*</span></label>
                        <textarea name="our_description" id="our_description" rows="3" 
                                  class="form-control @error('our_description') is-invalid @enderror" 
                                  placeholder="Детальний опис проблеми з обладнанням" required>{{ old('our_description') }}</textarea>
                        @error('our_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('repair-tracking.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Зберегти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection