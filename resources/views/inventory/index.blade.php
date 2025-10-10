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


@section('content')
<!-- Фільтри -->
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('inventory.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="branch_id" class="form-label">Філія</label>
                    <select name="branch_id" id="branch_id" class="form-select">
                        <option value="">Всі філії</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="balance_code" class="form-label">Код балансу</label>
                    <select name="balance_code" id="balance_code" class="form-select">
                        <option value="">Всі коди</option>
                        @foreach($balanceCodes as $code)
                            <option value="{{ $code }}" {{ request('balance_code') === $code ? 'selected' : '' }}>
                                {{ Str::limit($code, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="equipment_type" class="form-label">Найменування</label>
                    <input type="text" name="equipment_type" id="equipment_type" 
                           class="form-control" placeholder="Пошук по найменуванню"
                           value="{{ request('equipment_type') }}">
                </div>
                
                <div class="col-md-3">
                    <label for="search" class="form-label">Загальний пошук</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Інв.номер, серійний номер..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="group_view" value="1" 
                               id="group_view" {{ request('group_view') ? 'checked' : '' }}>
                        <label class="form-check-label" for="group_view">
                            Групувати
                        </label>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Фільтрувати
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Статистика фільтрації -->
@if(request()->hasAny(['branch_id', 'balance_code', 'equipment_type', 'search']))
<div class="row mb-3">
    <div class="col">
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-funnel"></i>
                <strong>Результати фільтрації:</strong> 
                знайдено <strong>{{ $filteredStats['total_items'] }}</strong> позицій, 
                загальна кількість: <strong>{{ $filteredStats['total_quantity'] }}</strong> од.
            </div>
            <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x"></i> Очистити фільтри
            </a>
        </div>
    </div>
</div>
@endif

<!-- Заголовок та кнопки -->
<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>
                Інвентар ({{ $inventory->total() }})
                @if(request('group_view'))
                    <span class="badge bg-info">Групований вигляд</span>
                @endif
            </h2>
            <div>
                <a href="{{ route('inventory.export', request()->all()) }}" 
                   class="btn btn-outline-success me-2">
                    <i class="bi bi-file-earmark-excel"></i> Експорт
                </a>
                <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати обладнання
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Таблиця інвентарю -->
<div class="stats-card">
    <div class="card-body p-0">
        @if($inventory->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="120">Інв. №</th>
                            <th width="200">Код балансу</th>
                            <th>Найменування</th>
                            <th width="120">Філія</th>
                            <th width="100">Кабінет</th>
                            <th width="80">Кількість</th>
                            <th width="100">Од.виміру</th>
                            <th width="150">Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory as $item)
                        <tr>
                            <td>
                                <code class="d-block">{{ $item->inventory_number }}</code>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($item->balance_code, 35) }}</small>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $item->equipment_type }}</strong>
                                    @if($item->brand || $item->model)
                                        <br><small class="text-muted">
                                            {{ $item->brand }} {{ $item->model }}
                                        </small>
                                    @endif
                                    @if($item->serial_number)
                                        <br><small class="text-muted">S/N: {{ $item->serial_number }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $item->branch->name }}</span>
                            </td>
                            <td>{{ $item->room_number }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $item->quantity }}</span>
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('inventory.show', $item) }}" 
                                       class="btn btn-outline-primary" title="Перегляд">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('inventory.edit', $item) }}" 
                                       class="btn btn-outline-warning" title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('inventory.transfer-form', $item) }}" 
                                       class="btn btn-outline-info" title="Переміщення">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white">
                {{ $inventory->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Обладнання не знайдено</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку</p>
                <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати обладнання
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Статистика по типах -->
@if($equipmentStats->count() > 0)
<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="stats-card p-4">
            <h5 class="mb-3">Топ-10 найменувань</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Найменування</th>
                            <th>Код балансу</th>
                            <th>Позицій</th>
                            <th>Загальна к-сть</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipmentStats->take(10) as $stat)
                        <tr>
                            <td>
                                <a href="{{ route('inventory.index', ['equipment_type' => $stat->equipment_type]) }}">
                                    {{ $stat->equipment_type }}
                                </a>
                            </td>
                            <td><small class="text-muted">{{ Str::limit($stat->balance_code, 30) }}</small></td>
                            <td><span class="badge bg-info">{{ $stat->count }}</span></td>
                            <td><span class="badge bg-primary">{{ $stat->total_qty }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.table td {
    vertical-align: middle;
}
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
code {
    font-size: 0.9em;
}
</style>
@endpush