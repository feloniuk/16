@extends('layouts.app')

@section('title', 'Заміни картриджів')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <!-- Filters -->
            <form method="GET" action="{{ route('cartridges.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="branch_id" class="form-label">Філія</label>
                    <select name="branch_id" id="branch_id" class="form-select">
                        <option value="">Усі філії</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Дата от</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Дата до</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Поиск</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Поиск по типу картриджа, принтеру..." value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Найти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Історія замін картриджів ({{ $cartridges->total() }})</h5>
        <div>
            <a href="{{ route('cartridges.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise"></i> Обновить
            </a>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if($cartridges->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Філія</th>
                            <th>Кабинет</th>
                            <th>Принтер</th>
                            <th>Тип картриджа</th>
                            <th>Користувач</th>
                            <th>Дата заміни</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartridges as $cartridge)
                        <tr>
                            <td><strong>#{{ $cartridge->id }}</strong></td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $cartridge->branch->name }}</span>
                            </td>
                            <td>{{ $cartridge->room_number }}</td>
                            <td>
                                <div style="max-width: 200px;">
                                    {{ Str::limit($cartridge->printer_info, 50) }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning">{{ $cartridge->cartridge_type }}</span>
                            </td>
                            <td>
                                @if($cartridge->username)
                                    <i class="bi bi-person"></i> @{{ $cartridge->username }}
                                @else
                                    <i class="bi bi-hash"></i> {{ $cartridge->user_telegram_id }}
                                @endif
                            </td>
                            <td>
                                <div>{{ $cartridge->replacement_date->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $cartridge->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('cartridges.show', $cartridge) }}"
                                       class="btn btn-sm btn-outline-primary" title="Просмотр">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
                                    <button type="button"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Видати картридж зі складу"
                                            data-issue-url="{{ route('cartridges.issue-from-warehouse', $cartridge) }}"
                                            data-label="Картридж #{{ $cartridge->id }} · {{ $cartridge->cartridge_type }} · {{ $cartridge->branch->name }}, каб. {{ $cartridge->room_number }}"
                                            data-presearch="{{ $cartridge->cartridge_type }}"
                                            onclick="openIssueModal(this)">
                                        <i class="bi bi-box-arrow-up"></i>
                                    </button>
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
                <i class="bi bi-printer fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Записи не найдены</h5>
                <p class="text-muted">Попробуйте изменить параметры поиска</p>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($cartridges->total() > 0)
<div class="stats-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Показано {{ $cartridges->firstItem() }} - {{ $cartridges->lastItem() }}
            з {{ $cartridges->total() }} записів
        </div>
        <div>
            {{ $cartridges->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endif

@if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
<div class="modal fade" id="issueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-arrow-up text-warning me-2"></i>Видати картридж зі складу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="issueForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3" id="issueModalLabel"></p>

                    <div class="mb-2">
                        <label class="form-label">Картриджі зі складу <span class="text-danger">*</span></label>
                    </div>
                    <div id="cartridgeModalItemsContainer"></div>
                    <button type="button" id="addMultiIssueItemBtn_cartridgeModalItemsContainer" class="btn btn-sm btn-outline-success mb-3">
                        <i class="bi bi-plus"></i> Додати товар
                    </button>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Дата видачі <span class="text-danger">*</span></label>
                            <input type="date" name="operation_date" class="form-control"
                                   value="{{ date('Y-m-d') }}" required>
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
@include('partials.multi-item-issue-script', ['containerId' => 'cartridgeModalItemsContainer'])
@endif
<script>
function openIssueModal(btn) {
    document.getElementById('issueForm').action = btn.dataset.issueUrl;
    document.getElementById('issueModalLabel').textContent = btn.dataset.label;

    if (window.resetMultiIssueRows_cartridgeModalItemsContainer) {
        window.resetMultiIssueRows_cartridgeModalItemsContainer(btn.dataset.presearch || '');
    }

    new bootstrap.Modal(document.getElementById('issueModal')).show();
}
</script>
@endpush
@endsection