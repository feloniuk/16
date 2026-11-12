@extends('layouts.app')

@section('title', 'Обхід кабінетів')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="mb-1">Обхід кабінетів</h2>
        <p class="text-muted mb-0">
            Чернетка фактичної наявності → звірка з вкладкою «Інвентар» → застосування. До застосування записи в системі не змінюються.
        </p>
    </div>
    <form method="POST" action="{{ route('inventory-audits.store') }}">
        @csrf
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Почати обхід
        </button>
    </form>
</div>

<div class="stats-card">
    <div class="card-body p-0">
        @if($audits->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Обхід</th>
                            <th>Хто проводив</th>
                            <th>Кабінетів</th>
                            <th>Позицій</th>
                            <th>Статус</th>
                            <th>Створено</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audits as $audit)
                        <tr>
                            <td><strong>#{{ $audit->id }}</strong> {{ $audit->title }}</td>
                            <td>{{ $audit->user?->name }}</td>
                            <td><span class="badge bg-info">{{ $audit->rooms_count }}</span></td>
                            <td><span class="badge bg-secondary">{{ $audit->items_count }}</span></td>
                            <td>
                                @if($audit->isApplied())
                                    <span class="badge bg-success">Застосовано</span>
                                    <br><small class="text-muted">{{ $audit->applied_at?->format('d.m.Y H:i') }}</small>
                                @else
                                    <span class="badge bg-warning">Чернетка</span>
                                @endif
                            </td>
                            <td>{{ $audit->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if($audit->isApplied())
                                        <a href="{{ route('inventory-audits.review', $audit) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Результат
                                        </a>
                                    @else
                                        <a href="{{ route('inventory-audits.show', $audit) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil"></i> Продовжити
                                        </a>
                                        <a href="{{ route('inventory-audits.review', $audit) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-list-check"></i> Звірка
                                        </a>
                                        <form method="POST" action="{{ route('inventory-audits.destroy', $audit) }}" x-data="{ confirming: false }">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger" x-show="!confirming" @click="confirming = true" title="Видалити чернетку">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <button type="submit" class="btn btn-sm btn-danger" x-show="confirming" x-cloak @click.outside="confirming = false">
                                                Точно видалити?
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
        @else
            <div class="p-5 text-center text-muted">
                <i class="bi bi-clipboard-check fs-1"></i>
                <p class="mt-2 mb-0">Обходів ще не було. Натисніть «Почати обхід».</p>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    {{ $audits->links() }}
</div>
@endsection
