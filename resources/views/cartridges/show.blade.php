@extends('layouts.app')

@section('title', 'Замена картриджа #' . $cartridge->id)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Замена картриджа #{{ $cartridge->id }}</h4>
                    <p class="text-muted mb-0">Запрос создан {{ $cartridge->created_at->format('d.m.Y в H:i') }}</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Филиал</h6>
                    <p class="mb-0">{{ $cartridge->branch->name }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Номер кабинета</h6>
                    <p class="mb-0">{{ $cartridge->room_number }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Пользователь</h6>
                    <p class="mb-0">
                        @if($cartridge->username)
                            <i class="bi bi-person"></i> @{{ $cartridge->username }}
                        @else
                            <i class="bi bi-hash"></i> ID: {{ $cartridge->user_telegram_id }}
                        @endif
                    </p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Дата замены</h6>
                    <p class="mb-0">{{ $cartridge->replacement_date->format('d.m.Y') }}</p>
                </div>

                <div class="col-12">
                    <h6 class="text-muted mb-2">Информация о принтере</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $cartridge->printer_info }}</p>
                    </div>
                </div>

                <div class="col-12">
                    <h6 class="text-muted mb-2">Тип картриджа</h6>
                    <span class="badge bg-warning fs-6">{{ $cartridge->cartridge_type }}</span>
                </div>

                @if($cartridge->notes)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Заметки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $cartridge->notes }}</p>
                    </div>
                </div>
                @endif

                @if($cartridge->printer)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Связанный инвентарь</h6>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">{{ $cartridge->printer->equipment_type }}</h6>
                            <p class="card-text">
                                <strong>Бренд:</strong> {{ $cartridge->printer->brand }}<br>
                                <strong>Модель:</strong> {{ $cartridge->printer->model }}<br>
                                <strong>Інв. номер:</strong> {{ $cartridge->printer->inventory_number }}
                                @if($cartridge->printer->serial_number)
                                    <br><strong>Серійний номер:</strong> {{ $cartridge->printer->serial_number }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Видача зі складу -->
        @if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3"><i class="bi bi-box-arrow-up me-2 text-warning"></i>Видати картридж зі складу</h5>

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('cartridges.issue-from-warehouse', $cartridge) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Товар зі складу <span class="text-danger">*</span></label>
                        <input type="text" id="cartridgeItemSearch" class="form-control"
                               placeholder="Введіть назву картриджа для пошуку..."
                               autocomplete="off">
                        <input type="hidden" name="inventory_id" id="cartridgeInventoryId">
                        <div id="cartridgeSearchResults" class="border rounded mt-1" style="display:none; max-height:200px; overflow-y:auto;"></div>
                        <div id="cartridgeSelectedItem" class="mt-2"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Кількість <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity', 1) }}" min="1" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Дата видачі <span class="text-danger">*</span></label>
                        <input type="date" name="operation_date" class="form-control"
                               value="{{ old('operation_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-box-arrow-up"></i> Видати
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="stats-card p-4">
            <h5 class="mb-3">Дії</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('cartridges.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад до списку
                </a>
                <a href="{{ route('warehouse.movements') }}?item_name={{ urlencode($cartridge->cartridge_type) }}"
                   class="btn btn-outline-info" target="_blank">
                    <i class="bi bi-arrow-left-right"></i> Рух по цьому картриджу
                </a>
            </div>
        </div>

        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Статистика</h5>
            <div class="mb-3">
                <h6 class="text-muted mb-1">Дата створення</h6>
                <p class="mb-0">{{ $cartridge->created_at->format('d.m.Y H:i:s') }}</p>
            </div>
            <div class="mb-3">
                <h6 class="text-muted mb-1">Час обробки</h6>
                <p class="mb-0">{{ $cartridge->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cartridgeSearchTimeout;

document.getElementById('cartridgeItemSearch').addEventListener('input', function () {
    clearTimeout(cartridgeSearchTimeout);
    const q = this.value.trim();
    const results = document.getElementById('cartridgeSearchResults');

    if (q.length < 2) {
        results.style.display = 'none';
        return;
    }

    cartridgeSearchTimeout = setTimeout(() => {
        fetch(`{{ route('api.warehouse-items.search') }}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(items => {
                if (!items.length) {
                    results.innerHTML = '<div class="p-2 text-muted small">Нічого не знайдено</div>';
                    results.style.display = 'block';
                    return;
                }
                results.innerHTML = items.map(item => {
                    const name = (item.full_name && item.full_name.trim()) ? item.full_name : item.name;
                    return `<div class="p-2 border-bottom" style="cursor:pointer;"
                                 onclick="selectCartridgeItem(${item.id}, '${name.replace(/'/g,"\\'")}')">
                                <strong>${name}</strong>
                                <small class="text-muted d-block">${item.code ?? ''}</small>
                            </div>`;
                }).join('');
                results.style.display = 'block';
            });
    }, 300);
});

function selectCartridgeItem(id, name) {
    document.getElementById('cartridgeInventoryId').value = id;
    document.getElementById('cartridgeItemSearch').value = name;
    document.getElementById('cartridgeSearchResults').style.display = 'none';
    document.getElementById('cartridgeSelectedItem').innerHTML =
        `<span class="badge bg-success"><i class="bi bi-check"></i> Обрано: ${name}</span>`;
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('#cartridgeItemSearch') && !e.target.closest('#cartridgeSearchResults')) {
        document.getElementById('cartridgeSearchResults').style.display = 'none';
    }
});
</script>
@endpush
