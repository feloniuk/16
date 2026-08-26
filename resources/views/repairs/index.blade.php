@extends('layouts.app')

@section('title', 'Заявки на ремонт')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <!-- Filters -->
            <form method="GET" action="{{ route('repairs.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Всі статуси</option>
                        <option value="нова" {{ request('status') === 'нова' ? 'selected' : '' }}>Нові</option>
                        <option value="в_роботі" {{ request('status') === 'в_роботі' ? 'selected' : '' }}>В роботі</option>
                        <option value="виконана" {{ request('status') === 'виконана' ? 'selected' : '' }}>Виконано</option>
                    </select>
                </div>
                
                <div class="col-md-3">
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
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Пошук</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Пошук по опису, кабінету..." value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Знайти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Список заявок ({{ $repairs->total() }})</h5>
        <div>
            <a href="{{ route('repairs.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise"></i> Оновити
            </a>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($repairs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Філія</th>
                            <th>Кабінет</th>
                            <th>Опис</th>
                            <th>Користувач</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($repairs as $repair)
                        <tr>
                            <td><strong>#{{ $repair->id }}</strong></td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $repair->branch->name }}</span>
                            </td>
                            <td>{{ $repair->room_number }}</td>
                            <td>
                                <div style="max-width: 300px;">
                                    {{ Str::limit($repair->description, 100) }}
                                </div>
                            </td>
                            <td>
                                @include('partials.telegram-user-link', ['username' => $repair->username, 'telegramId' => $repair->user_telegram_id])
                                @if($repair->phone)
                                    <br><small class="text-muted">
                                        <i class="bi bi-telephone"></i> {{ $repair->phone }}
                                    </small>
                                @endif
                            </td>
                            <td>{!! $repair->status_badge !!}</td>
                            <td>
                                <div>{{ $repair->created_at->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $repair->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('repairs.show', $repair) }}"
                                       class="btn btn-sm btn-outline-primary" title="Перегляд">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
                                    <button type="button"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Видати зі складу"
                                            data-issue-url="{{ route('repairs.issue-from-warehouse', $repair) }}"
                                            data-label="Ремонт #{{ $repair->id }} · {{ $repair->branch->name }}, каб. {{ $repair->room_number }}"
                                            onclick="openIssueModal(this)">
                                        <i class="bi bi-box-arrow-up"></i>
                                    </button>
                                    @endif

                                    @if($repair->status !== 'виконана')
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success dropdown-toggle"
                                                data-bs-toggle="dropdown" title="Змінити статус">
                                            <i class="bi bi-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($repair->status !== 'в_роботі')
                                            <li>
                                                <form method="POST" action="{{ route('repairs.update', $repair) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="в_роботі">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-gear text-warning"></i> В роботу
                                                    </button>
                                                </form>
                                            </li>
                                            @endif
                                            <li>
                                                <form method="POST" action="{{ route('repairs.update', $repair) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="виконана">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bi bi-check-circle text-success"></i> Виконано
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Заявки не знайдені</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку</p>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($repairs->total() > 0)
<div class="stats-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Показано {{ $repairs->firstItem() }} - {{ $repairs->lastItem() }}
            з {{ $repairs->total() }} записів
        </div>
        <div>
            {{ $repairs->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endif

@if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
<div class="modal fade" id="issueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-arrow-up text-warning me-2"></i>Видати зі складу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="issueForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3" id="issueModalLabel"></p>

                    <div class="mb-2">
                        <label class="form-label">Товари зі складу <span class="text-danger">*</span></label>
                    </div>
                    <div id="repairModalItemsContainer"></div>
                    <button type="button" id="addMultiIssueItemBtn_repairModalItemsContainer" class="btn btn-sm btn-outline-success mb-3">
                        <i class="bi bi-plus"></i> Додати товар
                    </button>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Дата видачі <span class="text-danger">*</span></label>
                            <input type="date" name="operation_date" class="form-control"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Примітка</label>
                            <input type="text" name="note" class="form-control"
                                   placeholder="Деталь, матеріал тощо">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-box-arrow-up"></i> Видати
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
.pagination {
    margin: 0;
}
.page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}
</style>
@endpush

@push('scripts')
@if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
@include('partials.multi-item-issue-script', ['containerId' => 'repairModalItemsContainer'])
@endif
<script>
function openIssueModal(btn) {
    document.getElementById('issueForm').action = btn.dataset.issueUrl;
    document.getElementById('issueModalLabel').textContent = btn.dataset.label;
    document.querySelector('#issueModal [name="operation_date"]').value = new Date().toISOString().slice(0, 10);
    document.querySelector('#issueModal [name="note"]').value = '';

    if (window.resetMultiIssueRows_repairModalItemsContainer) {
        window.resetMultiIssueRows_repairModalItemsContainer();
    }

    new bootstrap.Modal(document.getElementById('issueModal')).show();
}
</script>
@endpush
@endsection