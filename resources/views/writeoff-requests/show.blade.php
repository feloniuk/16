@extends('layouts.app')

@section('title', 'Заявка ' . $writeoffRequest->request_number)

@section('content')
<div class="row mb-3">
    <div class="col">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('writeoff-requests.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Назад
            </a>
            <h2 class="mb-0">Заявка на списання {{ $writeoffRequest->request_number }}</h2>
            {!! $writeoffRequest->status_badge !!}
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ $errors->first() }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-3">
        <div class="stats-card p-4">
            <h6 class="text-muted text-uppercase small mb-3">Інформація</h6>
            <dl class="row mb-0 small">
                <dt class="col-5 text-muted">Номер</dt>
                <dd class="col-7 fw-semibold">{{ $writeoffRequest->request_number }}</dd>

                <dt class="col-5 text-muted">Дата</dt>
                <dd class="col-7">{{ $writeoffRequest->writeoff_date->format('d.m.Y') }}</dd>

                <dt class="col-5 text-muted">Статус</dt>
                <dd class="col-7">{!! $writeoffRequest->status_badge !!}</dd>

                <dt class="col-5 text-muted">Позицій</dt>
                <dd class="col-7">{{ $writeoffRequest->items->count() }}</dd>

                <dt class="col-5 text-muted">Автор</dt>
                <dd class="col-7">{{ $writeoffRequest->user->name ?? '—' }}</dd>

                <dt class="col-5 text-muted">Створено</dt>
                <dd class="col-7">{{ $writeoffRequest->created_at->format('d.m.Y H:i') }}</dd>
            </dl>

            @if($writeoffRequest->description)
            <hr>
            <div class="small">
                <div class="text-muted mb-1">Опис:</div>
                {{ $writeoffRequest->description }}
            </div>
            @endif

            @if($writeoffRequest->notes)
            <hr>
            <div class="small">
                <div class="text-muted mb-1">Примітки:</div>
                {{ $writeoffRequest->notes }}
            </div>
            @endif

            <hr>
            <div class="d-grid gap-2">
                <a href="{{ route('writeoff-requests.print', $writeoffRequest) }}" class="btn btn-outline-dark btn-sm" target="_blank">
                    <i class="bi bi-printer"></i> Друкувати
                </a>

                @if($writeoffRequest->status === 'draft')
                    @if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']) || $writeoffRequest->user_id === auth()->id())
                    <a href="{{ route('writeoff-requests.edit', $writeoffRequest) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-pencil"></i> Редагувати
                    </a>
                    <form method="POST" action="{{ route('writeoff-requests.submit', $writeoffRequest) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm w-100">
                            <i class="bi bi-send"></i> Подати на затвердження
                        </button>
                    </form>
                    <form method="POST" action="{{ route('writeoff-requests.destroy', $writeoffRequest) }}"
                        onsubmit="return confirm('Видалити заявку?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-trash"></i> Видалити
                        </button>
                    </form>
                    @endif
                @endif

                @if($writeoffRequest->status === 'submitted' && in_array(auth()->user()->role, ['admin', 'director']))
                <form method="POST" action="{{ route('writeoff-requests.approve', $writeoffRequest) }}">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-check-circle"></i> Затвердити
                    </button>
                </form>
                <form method="POST" action="{{ route('writeoff-requests.reject', $writeoffRequest) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-x-circle"></i> Повернути до чернетки
                    </button>
                </form>
                @endif

                @if($writeoffRequest->status === 'approved' && in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
                <form method="POST" action="{{ route('writeoff-requests.complete', $writeoffRequest) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-check2-all"></i> Завершити
                    </button>
                </form>
                <form method="POST" action="{{ route('writeoff-requests.reject', $writeoffRequest) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-x-circle"></i> Повернути до чернетки
                    </button>
                </form>
                @endif

                @if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']) && $writeoffRequest->status === 'completed')
                <form method="POST" action="{{ route('writeoff-requests.archive', $writeoffRequest) }}"
                    onsubmit="return confirm('Архівувати заявку?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-archive"></i> Архівувати
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="stats-card p-4">
            <h5 class="mb-3">Позиції заявки ({{ $writeoffRequest->items->count() }})</h5>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">№</th>
                            <th>Найменування</th>
                            <th style="width:80px">Од.</th>
                            <th style="width:100px">Кількість</th>
                            <th>Примітка</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($writeoffRequest->items as $i => $item)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>
                                {{ $item->item_name }}
                                @if($item->inventoryItem)
                                <small class="text-muted d-block">{{ $item->inventoryItem->equipment_type }}</small>
                                @endif
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td class="fw-semibold">{{ $item->quantity }}</td>
                            <td class="text-muted small">{{ $item->notes }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Немає позицій</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
