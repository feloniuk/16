@extends('layouts.app')

@section('title', 'Заявки на списання')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('writeoff-requests.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Всі статуси</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Чернетка</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Подана</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Затверджена</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Завершена</option>
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
                @if(request()->hasAny(['status','date_from','date_to']))
                <div class="col-md-2">
                    <a href="{{ route('writeoff-requests.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle"></i> Скинути
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Заявки на списання ({{ $requests->total() }})</h2>
            @if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
            <a href="{{ route('writeoff-requests.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Нова заявка
            </a>
            @endif
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

        <div class="stats-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Дата списання</th>
                            <th>Позицій</th>
                            <th>Опис</th>
                            <th>Статус</th>
                            <th>Автор</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td>
                                <a href="{{ route('writeoff-requests.show', $req) }}" class="fw-semibold text-decoration-none">
                                    {{ $req->request_number }}
                                </a>
                            </td>
                            <td>{{ $req->writeoff_date->format('d.m.Y') }}</td>
                            <td>{{ $req->items_count }}</td>
                            <td class="text-muted small">{{ Str::limit($req->description, 60) }}</td>
                            <td>{!! $req->status_badge !!}</td>
                            <td class="small text-muted">{{ $req->user->name ?? '—' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('writeoff-requests.show', $req) }}" class="btn btn-sm btn-outline-primary" title="Переглянути">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($req->status === 'draft')
                                    <a href="{{ route('writeoff-requests.edit', $req) }}" class="btn btn-sm btn-outline-secondary" title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('writeoff-requests.print', $req) }}" class="btn btn-sm btn-outline-dark" title="Друк" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Заявки не знайдено</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
