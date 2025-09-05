{{-- resources/views/repair-tracking/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редагувати запис #' . $repairTracking->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Редагувати запис про ремонт #{{ $repairTracking->id }}</h4>
                <p class="text-muted">Оновіть інформацію про ремонт обладнання</p>
            </div>
            
            <form method="POST" action="{{ route('repair-tracking.update', $repairTracking) }}">
                @csrf
                @method('PATCH')
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="equipment_id" class="form-label">Обладнання <span class="text-danger">*</span></label>
                        <select name="equipment_id" id="equipment_id" class="form-select @error('equipment_id') is-invalid @enderror" required>
                            <option value="">Оберіть обладнання</option>
                            @foreach($equipment as $item)
                                <option value="{{ $item->id }}" 
                                        {{ (old('equipment_id', $repairTracking->equipment_id) == $item->id) ? 'selected' : '' }}>
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
                                <option value="{{ $master->id }}" 
                                        {{ (old('repair_master_id', $repairTracking->repair_master_id) == $master->id) ? 'selected' : '' }}>
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
                               value="{{ old('sent_date', $repairTracking->sent_date->format('Y-m-d')) }}" required>
                        @error('sent_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="returned_date" class="form-label">Дата повернення</label>
                        <input type="date" name="returned_date" id="returned_date" 
                               class="form-control @error('returned_date') is-invalid @enderror" 
                               value="{{ old('returned_date', $repairTracking->returned_date?->format('Y-m-d')) }}">
                        @error('returned_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="invoice_number" class="form-label">Номер накладної</label>
                        <input type="text" name="invoice_number" id="invoice_number" 
                               class="form-control @error('invoice_number') is-invalid @enderror" 
                               value="{{ old('invoice_number', $repairTracking->invoice_number) }}">
                        @error('invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="cost" class="form-label">Вартість ремонту (грн)</label>
                        <input type="number" step="0.01" name="cost" id="cost" 
                               class="form-control @error('cost') is-invalid @enderror" 
                               value="{{ old('cost', $repairTracking->cost) }}" placeholder="0.00">
                        @error('cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label">Статус <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="sent" {{ old('status', $repairTracking->status) === 'sent' ? 'selected' : '' }}>
                                Відправлено
                            </option>
                            <option value="in_repair" {{ old('status', $repairTracking->status) === 'in_repair' ? 'selected' : '' }}>
                                На ремонті
                            </option>
                            <option value="completed" {{ old('status', $repairTracking->status) === 'completed' ? 'selected' : '' }}>
                                Завершено
                            </option>
                            <option value="cancelled" {{ old('status', $repairTracking->status) === 'cancelled' ? 'selected' : '' }}>
                                Скасовано
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="our_description" class="form-label">Наш опис поломки <span class="text-danger">*</span></label>
                        <textarea name="our_description" id="our_description" rows="3" 
                                  class="form-control @error('our_description') is-invalid @enderror" 
                                  placeholder="Детальний опис проблеми з обладнанням" required>{{ old('our_description', $repairTracking->our_description) }}</textarea>
                        @error('our_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="repair_description" class="form-label">Опис ремонту від майстра</label>
                        <textarea name="repair_description" id="repair_description" rows="3" 
                                  class="form-control @error('repair_description') is-invalid @enderror" 
                                  placeholder="Що було виправлено, які деталі замінено...">{{ old('repair_description', $repairTracking->repair_description) }}</textarea>
                        @error('repair_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="notes" class="form-label">Примітки</label>
                        <textarea name="notes" id="notes" rows="2" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Додаткові примітки...">{{ old('notes', $repairTracking->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('repair-tracking.show', $repairTracking) }}" class="btn btn-outline-secondary">
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

@push('scripts')
<script>
// Автоматично заповнити дату повернення при зміні статусу на "завершено"
document.getElementById('status').addEventListener('change', function() {
    const returnedDateInput = document.getElementById('returned_date');
    
    if (this.value === 'completed' && !returnedDateInput.value) {
        returnedDateInput.value = new Date().toISOString().split('T')[0];
    }
});
</script>
@endpush