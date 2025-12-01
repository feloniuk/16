@extends('layouts.app')

@section('title', 'Інвентар - Групований перегляд')

@section('content')
<div class="container-fluid">
    <!-- Шапка -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Групований перегляд інвентарю</h2>
                    <p class="text-muted mb-0">Позиції згруповані за найменуванням</p>
                </div>
                <div>
                    <a href="{{ route('inventory.index', request()->except('group_view')) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-list"></i> Табличний вигляд
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary mb-0">{{ $grouped->count() }}</h3>
                    <small class="text-muted">Унікальних найменувань</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info mb-0">{{ $filteredStats['total_items'] }}</h3>
                    <small class="text-muted">Всього позицій</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success mb-0">{{ $filteredStats['total_quantity'] }}</h3>
                    <small class="text-muted">Загальна кількість</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <a href="{{ route('inventory.export', request()->all()) }}" 
                       class="btn btn-success btn-sm w-100">
                        <i class="bi bi-download"></i> Експортувати
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Групи -->
    @if($grouped->count() > 0)
        @foreach($grouped as $equipmentName => $group)
        <div class="card mb-3 shadow-sm">
            <!-- Заголовок групи -->
            <div class="card-header bg-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <button class="btn btn-link text-decoration-none text-dark p-0" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#group-{{ $loop->index }}">
                                <i class="bi bi-chevron-right me-2" id="icon-{{ $loop->index }}"></i>
                                {{ $group['name'] }}
                            </button>
                        </h5>
                        @if($group['balance_code'])
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-tag"></i> {{ $group['balance_code'] }}
                            </small>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge bg-primary me-2 fs-6">
                            {{ $group['count'] }} {{ $group['count'] == 1 ? 'позиція' : 'позицій' }}
                        </span>
                        <span class="badge bg-success fs-6">
                            Σ {{ $group['total_quantity'] }} од.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Деталі групи (згорнуто) -->
            <div class="collapse" id="group-{{ $loop->index }}">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 10%">Інв. №</th>
                                    <th style="width: 15%">Філія</th>
                                    <th style="width: 12%">Кабінет</th>
                                    <th style="width: 8%">К-сть</th>
                                    <th style="width: 15%">Бренд</th>
                                    <th style="width: 15%">Модель</th>
                                    <th style="width: 15%">Серійний №</th>
                                    <th style="width: 10%">Дії</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group['items'] as $item)
                                <tr>
                                    <td>
                                        <code class="text-primary">{{ $item->inventory_number }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->branch->name }}</span>
                                    </td>
                                    <td>{{ $item->room_number }}</td>
                                    <td>
                                        <strong>{{ $item->quantity }}</strong> 
                                        <small class="text-muted">{{ $item->unit }}</small>
                                    </td>
                                    <td>{{ $item->brand ?: '—' }}</td>
                                    <td>{{ $item->model ?: '—' }}</td>
                                    <td>
                                        @if($item->serial_number)
                                            <small class="text-muted">{{ $item->serial_number }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('inventory.show', $item) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Переглянути">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('inventory.transfer-form', $item) }}" 
                                               class="btn btn-outline-info" 
                                               title="Перемістити">
                                                <i class="bi bi-arrow-left-right"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <!-- Пусто -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3 text-muted">Немає даних</h4>
                <p class="text-muted">Спробуйте змінити фільтри або додати нові позиції</p>
                <a href="{{ route('inventory.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Повернутись
                </a>
            </div>
        </div>
    @endif

    <!-- Итоговая статистика -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stats-card p-4">
            <h5 class="mb-3">Итоговая статистика</h5>
            <div class="row">
                <div class="col-md-3">
                    <div class="card border-primary mb-2">
                        <div class="card-body text-center">
                            <h3 class="text-primary mb-0">{{ $totals['total_equipment_types'] }}</h3>
                            <small class="text-muted">Уникальных наименований</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info mb-2">
                        <div class="card-body text-center">
                            <h3 class="text-info mb-0">{{ $totals['total_positions'] }}</h3>
                            <small class="text-muted">Позиций</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success mb-2">
                        <div class="card-body text-center">
                            <h3 class="text-success mb-0">{{ $totals['total_quantity'] }}</h3>
                            <small class="text-muted">Общее количество</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning mb-2">
                        <div class="card-body text-center">
                            <h3 class="text-warning mb-0">{{ $totals['total_balance_groups'] }}</h3>
                            <small class="text-muted">Групп баланса</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Детали групп баланса -->
            <div class="table-responsive mt-3">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Группа баланса</th>
                            <th>Уникальных наименований</th>
                            <th>Позиций</th>
                            <th>Общее количество</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($totals['balance_code_details'] as $balanceCode => $details)
                        <tr>
                            <td>{{ $balanceCode }}</td>
                            <td>{{ $details['equipment_types_count'] }}</td>
                            <td>{{ $details['positions_count'] }}</td>
                            <td>{{ $details['total_quantity'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Кнопка экспорта -->
            <div class="text-end mt-3">
                <a href="{{ route('inventory.export.totals', request()->all()) }}" class="btn btn-success">
                    <i class="bi bi-file-excel"></i> Экспорт итогов
                </a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
.btn-link {
    font-weight: 600;
}
.btn-link:hover {
    text-decoration: underline !important;
}
.card-header .btn-link:focus {
    box-shadow: none;
}
code.text-primary {
    background-color: #e7f1ff;
    padding: 0.2em 0.4em;
    border-radius: 3px;
}
.table td, .table th {
    vertical-align: middle;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Автоматично розгорнути перші 3 групи
    @for($i = 0; $i < min(3, $grouped->count()); $i++)
        const collapse{{ $i }} = document.getElementById('group-{{ $i }}');
        if(collapse{{ $i }}) {
            collapse{{ $i }}.classList.add('show');
            const icon{{ $i }} = document.getElementById('icon-{{ $i }}');
            if(icon{{ $i }}) {
                icon{{ $i }}.classList.remove('bi-chevron-right');
                icon{{ $i }}.classList.add('bi-chevron-down');
            }
        }
    @endfor

    // Зміна іконки при розгортанні/згортанні
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-bs-target');
            const icon = this.querySelector('i');
            
            setTimeout(() => {
                const target = document.querySelector(targetId);
                if(target && target.classList.contains('show')) {
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-down');
                } else {
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-right');
                }
            }, 100);
        });
    });
});
</script>
@endpush