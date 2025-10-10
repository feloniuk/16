@extends('layouts.app')

@section('title', 'Переміщення товару')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4><i class="bi bi-arrow-left-right"></i> Переміщення товару</h4>
                <p class="text-muted">Переміщення з поточного місця в інше</p>
            </div>

            <!-- Поточне розташування -->
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Поточне розташування</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <h6 class="text-muted mb-2">Найменування</h6>
                            <p class="mb-0"><strong>{{ $inventory->equipment_type }}</strong></p>
                            @if($inventory->balance_code)
                                <small class="text-muted">{{ $inventory->balance_code }}</small>
                            @endif
                        </div>
                        
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Інв. номер</h6>
                            <p class="mb-0"><code>{{ $inventory->inventory_number }}</code></p>
                        </div>
                        
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Філія</h6>
                            <p class="mb-0">{{ $inventory->branch->name }}</p>
                        </div>
                        
                        <div class="col-md-4">
                            <h6 class="text-muted mb-2">Кабінет</h6>
                            <p class="mb-0">{{ $inventory->room_number }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Доступна кількість</h6>
                            <p class="mb-0">
                                <span class="badge bg-primary fs-6">{{ $inventory->quantity }} {{ $inventory->unit }}</span>
                            </p>
                        </div>
                        
                        @if($inventory->price)
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Ціна за одиницю</h6>
                            <p class="mb-0">{{ number_format($inventory->price, 2) }} грн</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Форма переміщення -->
            <form method="POST" action="{{ route('inventory.transfer', $inventory) }}">
                @csrf
                
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-pin-map"></i> Куди переміщуємо</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="to_branch_id" class="form-label">
                                    Філія призначення <span class="text-danger">*</span>
                                </label>
                                <select name="to_branch_id" id="to_branch_id" 
                                        class="form-select @error('to_branch_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Оберіть філію</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" 
                                                {{ old('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="to_room_number" class="form-label">
                                    Кабінет призначення <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="to_room_number" id="to_room_number" 
                                       class="form-control @error('to_room_number') is-invalid @enderror" 
                                       value="{{ old('to_room_number') }}" 
                                       placeholder="Наприклад: 101, Склад"
                                       required>
                                @error('to_room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">
                                    Кількість для переміщення <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="quantity" id="quantity" 
                                       class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', $inventory->quantity) }}" 
                                       min="1" 
                                       max="{{ $inventory->quantity }}"
                                       required>
                                <small class="form-text text-muted">
                                    Макс: {{ $inventory->quantity }} {{ $inventory->unit }}
                                </small>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="transfer_date" class="form-label">
                                    Дата переміщення <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="transfer_date" id="transfer_date" 
                                       class="form-control @error('transfer_date') is-invalid @enderror" 
                                       value="{{ old('transfer_date', date('Y-m-d')) }}"
                                       required>
                                @error('transfer_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="notes" class="form-label">
                                    <i class="bi bi-sticky"></i> Примітка до переміщення
                                </label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" 
                                          placeholder="Причина переміщення, додаткова інформація...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Попередження при частковому переміщенні -->
                <div class="alert alert-warning mt-3" id="partialWarning" style="display: none;">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Увага!</strong> Ви переміщуєте не всю кількість. 
                    Буде створено новий запис в місці призначення, а в поточному залишиться 
                    <strong id="remainingQty"></strong> {{ $inventory->unit }}.
                </div>

                <!-- Кнопки -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('inventory.show', $inventory) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                        <i class="bi bi-arrow-right-circle"></i> Виконати переміщення
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const maxQty = {{ $inventory->quantity }};
    const qtyInput = document.getElementById('quantity');
    const partialWarning = document.getElementById('partialWarning');
    const remainingQty = document.getElementById('remainingQty');

    // Перевірка на часткове переміщення
    qtyInput.addEventListener('input', function() {
        const value = parseInt(this.value);
        
        if (value < maxQty && value > 0) {
            partialWarning.style.display = 'block';
            remainingQty.textContent = maxQty - value;
        } else {
            partialWarning.style.display = 'none';
        }
    });

    // Підтвердження перед відправкою
    document.querySelector('form').addEventListener('submit', function(e) {
        const toBranch = document.getElementById('to_branch_id');
        const toRoom = document.getElementById('to_room_number').value;
        const qty = qtyInput.value;
        const branchName = toBranch.options[toBranch.selectedIndex].text;

        const message = `Переміщуємо ${qty} {{ $inventory->unit }} в:\n` +
                       `Філія: ${branchName}\n` +
                       `Кабінет: ${toRoom}\n\n` +
                       `Продовжити?`;

        if (!confirm(message)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card-header h6 {
    margin: 0;
}
.alert-warning {
    animation: fadeIn 0.3s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush