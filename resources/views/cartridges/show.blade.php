@extends('layouts.app')

@section('title', 'Заміна картриджа #' . $cartridge->id)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Заміна картриджа #{{ $cartridge->id }}</h4>
                    <p class="text-muted mb-0">Запит створено {{ $cartridge->created_at->format('d.m.Y о H:i') }}</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Філія</h6>
                    <p class="mb-0">{{ $cartridge->branch->name }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Номер кабінету</h6>
                    <p class="mb-0">{{ $cartridge->room_number }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Користувач</h6>
                    <p class="mb-0">
                        @include('partials.telegram-user-link', ['username' => $cartridge->username, 'telegramId' => $cartridge->user_telegram_id])
                    </p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Дата заміни</h6>
                    <p class="mb-0">{{ $cartridge->replacement_date->format('d.m.Y') }}</p>
                </div>

                <div class="col-12">
                    <h6 class="text-muted mb-2">Інформація про принтер</h6>
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
                    <h6 class="text-muted mb-2">Нотатки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $cartridge->notes }}</p>
                    </div>
                </div>
                @endif

                @if($cartridge->printer)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Пов'язаний інвентар</h6>
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
                <div class="mb-2">
                    <label class="form-label">Картриджі зі складу <span class="text-danger">*</span></label>
                </div>
                <div id="cartridgeItemsContainer"></div>
                <button type="button" id="addMultiIssueItemBtn_cartridgeItemsContainer" class="btn btn-sm btn-outline-success mb-3">
                    <i class="bi bi-plus"></i> Додати товар
                </button>

                <div class="row g-3">
                    <div class="col-md-8">
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
@if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
@include('partials.multi-item-issue-script', ['containerId' => 'cartridgeItemsContainer'])
@endif
@endpush
