
@extends('layouts.app')

@section('title', 'Архів заявок на закупівлю')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('purchase-requests.archiveIndex') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Всі статуси</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Чернетка</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Подана</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Затверджена</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Відхилена</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Виконана</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="date_from" class="form-label">Дата від</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-3">
                    <label for="date_to" class="form-label">Дата до</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
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

<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Архів заявок на закупівлю ({{ $requests->total() }})</h2>
            <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Повернутися до заявок
            </a>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-body p-0">
        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>№ заявки</th>
                            <th>Ініціатор</th>
                            <th>Кількість позицій</th>
                            <th>Сума</th>
                            <th>Дата потреби</th>
                            <th>Статус</th>
                            <th>Створено</th>
                            <th>Архівовано</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>
                                <strong>{{ $request->request_number }}</strong>
                                @if($request->items->count() > 0)
                                    @php
                                        $itemsHtml = '<ul class="mb-0 ps-3">' . $request->items->map(fn($item) => '<li>' . e($item->item_name) . ' — <strong>' . $item->quantity . '</strong> ' . e($item->unit) . '</li>')->implode('') . '</ul>';
                                    @endphp
                                    <button type="button"
                                            class="btn btn-link p-0 ms-1 align-baseline text-info"
                                            data-bs-toggle="popover"
                                            data-bs-trigger="hover focus"
                                            data-bs-placement="right"
                                            data-bs-html="true"
                                            data-bs-title="Товари заявки"
                                            data-bs-content="{{ $itemsHtml }}"
                                            style="font-size:0.85rem;">
                                        <i class="bi bi-list-ul"></i>
                                    </button>
                                @endif
                            </td>
                            <td>{{ $request->user->name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $request->items_count }} поз.</span>
                            </td>
                            <td>
                                @if($request->total_amount > 0)
                                    <strong>{{ number_format($request->total_amount, 2) }} грн</strong>
                                @else
                                    <span class="text-muted">Не вказано</span>
                                @endif
                            </td>
                            <td>{{ $request->requested_date->format('d.m.Y') }}</td>
                            <td>{!! $request->status_badge !!}</td>
                            <td>
                                <div>{{ $request->created_at->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div>{{ $request->archived_at->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $request->archived_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('purchase-requests.show', $request) }}"
                                       class="btn btn-sm btn-outline-primary" title="Переглянути">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('purchase-requests.print', $request) }}"
                                       class="btn btn-sm btn-outline-success" title="Друк" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>

                                    <form method="POST" action="{{ route('purchase-requests.restore', $request) }}"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info"
                                                title="Відновити"
                                                onclick="return confirm('Відновити заявку {{ $request->request_number }}?')">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-archive fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Архівовані заявки не знайдені</h5>
                <p class="text-muted">У вас немає архівованих заявок на закупівлю</p>
                <a href="{{ route('purchase-requests.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Повернутися до заявок
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Pagination -->
@if($requests->total() > 0)
<div class="stats-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Показано {{ $requests->firstItem() }} - {{ $requests->lastItem() }}
            з {{ $requests->total() }} записів
        </div>
        <div>
            {{ $requests->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
        new bootstrap.Popover(el);
    });
});
</script>
@endpush

@endsection
