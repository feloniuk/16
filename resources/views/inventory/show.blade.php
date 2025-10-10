@extends('layouts.app')

@section('title', 'Обладнання #' . $inventory->inventory_number)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Основна інформація -->
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>{{ $inventory->equipment_type }}</h4>
                    <p class="text-muted mb-2">
                        Інвентарний номер: <strong>{{ $inventory->inventory_number }}</strong>
                    </p>
                    @if($inventory->balance_code)
                        <p class="text-muted mb-0">
                            <small>{{ $inventory->balance_code }}</small>
                        </p>
                    @endif
                </div>
                <div>
                    <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Редагувати
                    </a>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Філія</h6>
                    <p class="mb-0">{{ $inventory->branch->name }}</p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Кабінет</h6>
                    <p class="mb-0">{{ $inventory->room_number }}</p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Кількість</h6>
                    <p class="mb-0">
                        <span class="badge bg-primary fs-6">{{ $inventory->quantity }} {{ $inventory->unit }}</span>
                    </p>
                </div>
                
                @if($inventory->price)
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Ціна за одиницю</h6>
                    <p class="mb-0"><strong>{{ number_format($inventory->price, 2) }} грн</strong></p>
                </div>
                @endif
                
                @if($inventory->brand)
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Бренд</h6>
                    <p class="mb-0">{{ $inventory->brand }}</p>
                </div>
                @endif
                
                @if($inventory->model)
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Модель</h6>
                    <p class="mb-0">{{ $inventory->model }}</p>
                </div>
                @endif
                
                @if($inventory->serial_number)
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Серійний номер</h6>
                    <p class="mb-0"><code>{{ $inventory->serial_number }}</code></p>
                </div>
                @endif
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Дата додавання</h6>
                    <p class="mb-0">{{ $inventory->created_at->format('d.m.Y H:i') }}</p>
                </div>
                
                @if($inventory->notes)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Примітки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $inventory->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Історія переміщень -->
        @if($transfers && $transfers->count() > 0)
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3"><i class="bi bi-clock-history"></i> Історія переміщень</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Звідки</th>
                            <th>Куди</th>
                            <th>К-сть</th>
                            <th>Користувач</th>
                            <th>Примітка</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->transfer_date->format('d.m.Y') }}</td>
                            <td>
                                <small>
                                    {{ $transfer->fromBranch ? $transfer->fromBranch->name : '-' }}
                                    <br>{{ $transfer->from_room_number }}
                                </small>
                            </td>
                            <td>
                                <small>
                                    {{ $transfer->toBranch->name }}
                                    <br>{{ $transfer->to_room_number }}
                                </small>
                            </td>
                            <td><span class="badge bg-info">{{ $transfer->quantity }}</span></td>
                            <td>{{ $transfer->user->name }}</td>
                            <td>{{ $transfer->notes ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        <!-- Історія замін картриджів -->
        @if($cartridgeReplacements && $cartridgeReplacements->count() > 0)
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3"><i class="bi bi-printer"></i> Історія замін картриджів</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Тип картриджа</th>
                            <th>Користувач</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartridgeReplacements as $replacement)
                        <tr>
                            <td>{{ $replacement->replacement_date->format('d.m.Y') }}</td>
                            <td><span class="badge bg-warning">{{ $replacement->cartridge_type }}</span></td>
                            <td>
                                @if($replacement->username)
                                    @{{ $replacement->username }}
                                @else
                                    ID: {{ $replacement->user_telegram_id }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Бічна панель з діями -->
    <div class="col-lg-4">
        <div class="stats-card p-4">
            <h5 class="mb-3">Дії</h5>
            
            <div class="d-grid gap-2">
                <a href="{{ route('inventory.transfer-form', $inventory) }}" class="btn btn-info">
                    <i class="bi bi-arrow-left-right"></i> Переміщення товару
                </a>
                
                <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Редагувати
                </a>
                
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад до списку
                </a>
                
                <hr>
                
                <form method="POST" action="{{ route('inventory.destroy', $inventory) }}" 
                      onsubmit="return confirm('Ви впевнені? Це видалить запис назавжди.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash"></i> Видалити
                    </button>
                </form>
            </div>
        </div>
        
        <!-- QR код -->
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">QR код</h5>
            <div class="text-center">
                <canvas id="qrcode"></canvas>
                <small class="text-muted mt-2 d-block">{{ $inventory->inventory_number }}</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new QRious({
        element: document.getElementById('qrcode'),
        value: '{{ $inventory->inventory_number }}',
        size: 200
    });
});
</script>
@endpush