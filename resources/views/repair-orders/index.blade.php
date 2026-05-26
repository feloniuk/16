@extends('layouts.app')

@section('title', 'Заявки на ремонт')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4>Заявки на ремонт</h4>
                    <p class="text-muted">Управління заявками на ремонт обладнання</p>
                </div>
                @if(in_array(auth()->user()->role, ['admin', 'director', 'warehouse_keeper']))
                    <a href="{{ route('repair-orders.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Нова заявка
                    </a>
                @endif
            </div>

            <!-- Фільтри -->
            <form method="GET" action="{{ route('repair-orders.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Статус</label>
                        <select name="status" class="form-select">
                            <option value="">-- Всі --</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Чернетка</option>
                            <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>На затвердженні</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Затверджено</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Відхилено</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Відправлено</option>
                            <option value="in_repair" {{ request('status') == 'in_repair' ? 'selected' : '' }}>На ремонті</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершено</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Скасовано</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Філія</label>
                        <select name="branch_id" class="form-select">
                            <option value="">-- Всі --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Майстер</label>
                        <select name="master_id" class="form-select">
                            <option value="">-- Всі --</option>
                            @foreach($masters as $master)
                                <option value="{{ $master->id }}" {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                    {{ $master->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Пошук</label>
                        <input type="text" name="search" class="form-control" placeholder="Номер, опис..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-search"></i> Фільтрувати
                    </button>
                    <a href="{{ route('repair-orders.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Очистити
                    </a>
                </div>
            </form>

            <!-- Таблиця -->
            @if($repairOrders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Номер</th>
                                <th>Статус</th>
                                <th>Позицій</th>
                                <th>Сума</th>
                                <th>Майстер</th>
                                <th>Відправлено</th>
                                <th>Повернення</th>
                                <th>Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($repairOrders as $order)
                                @php
                                    $returnedCount = $order->items->filter(fn($i) => $i->returned_at)->count();
                                    $totalCount = $order->items_count;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('repair-orders.show', $order) }}" class="fw-semibold text-decoration-none">
                                            {{ $order->order_number }}
                                        </a>
                                        @if($order->items->count() > 0)
                                            @php
                                                $itemsHtml = '<ul class="mb-0 ps-3">' . $order->items->map(fn($item) => '<li>' . e($item->equipment->equipment_type ?? '—') . ($item->equipment->inventory_number ? ' <small class=\"text-muted\">(' . e($item->equipment->inventory_number) . ')</small>' : '') . '</li>')->implode('') . '</ul>';
                                            @endphp
                                            <button type="button"
                                                    class="btn btn-link p-0 ms-1 align-baseline text-info"
                                                    data-bs-toggle="popover"
                                                    data-bs-trigger="hover focus"
                                                    data-bs-placement="right"
                                                    data-bs-html="true"
                                                    data-bs-title="Обладнання"
                                                    data-bs-content="{{ $itemsHtml }}"
                                                    style="font-size:0.85rem;">
                                                <i class="bi bi-list-ul"></i>
                                            </button>
                                        @endif
                                        @if($order->description)
                                            <div class="small text-muted">{{ Str::limit($order->description, 40) }}</div>
                                        @endif
                                    </td>
                                    <td>{!! $order->status_badge !!}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $totalCount }}</span>
                                    </td>
                                    <td class="text-nowrap">
                                        @if($order->total_cost > 0)
                                            {{ number_format($order->total_cost, 2, ',', ' ') }} грн
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $order->repairMaster->name ?? '—' }}</small>
                                    </td>
                                    <td class="text-nowrap">
                                        @if($order->sent_date)
                                            <small>{{ $order->sent_date->format('d.m.Y') }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($returnedCount > 0)
                                            <span class="badge {{ $returnedCount === $totalCount ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ $returnedCount }}/{{ $totalCount }}
                                            </span>
                                        @elseif(in_array($order->status, ['sent', 'in_repair']))
                                            <span class="badge bg-secondary">0/{{ $totalCount }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('repair-orders.show', $order) }}" class="btn btn-outline-primary"
                                               title="Переглянути">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($order->canBeEditedBy(auth()->user()))
                                                <a href="{{ route('repair-orders.edit', $order) }}" class="btn btn-outline-warning"
                                                   title="Редагувати">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endif
                                            @if($order->status === 'draft' && $order->user_id === auth()->id())
                                                <form method="POST" action="{{ route('repair-orders.submit', $order) }}" class="d-inline"
                                                      style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success btn-sm"
                                                            title="Подати на затвердження">
                                                        <i class="bi bi-send"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Показано {{ $repairOrders->firstItem() }}-{{ $repairOrders->lastItem() }}
                        з {{ $repairOrders->total() }} заявок
                    </div>
                    {{ $repairOrders->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Немає заявок на ремонт
                </div>
            @endif
        </div>
    </div>
</div>
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
