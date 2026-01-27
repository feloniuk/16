@extends('layouts.app')

@section('title', 'Запис про роботу #' . $workLog->id)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="stats-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Запис #{{ $workLog->id }}
                    @switch($workLog->work_type)
                        @case('inventory_transfer')
                            <span class="badge bg-primary">Переміщення</span>
                            @break
                        @case('cartridge_replacement')
                            <span class="badge bg-info">Картридж</span>
                            @break
                        @case('repair_sent')
                            <span class="badge bg-warning">Відправка</span>
                            @break
                        @case('repair_returned')
                            <span class="badge bg-success">Повернення</span>
                            @break
                        @case('manual')
                            <span class="badge bg-secondary">Інше</span>
                            @break
                    @endswitch
                </h5>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Опис роботи</h6>
                        <p>{{ $workLog->description }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Тип роботи</h6>
                        <p>{{ $workLog->getWorkTypeLabel() }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Філіал</h6>
                        <p>
                            @if($workLog->branch)
                                <span class="badge bg-light text-dark">{{ $workLog->branch->name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Номер кабінету</h6>
                        <p>{{ $workLog->room_number ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Дата виконання роботи</h6>
                        <p>{{ $workLog->performed_at->format('d.m.Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Користувач</h6>
                        <p>
                            @if($workLog->user)
                                <i class="bi bi-person"></i> {{ $workLog->user->name }}
                            @endif
                        </p>
                    </div>
                </div>

                @if($workLog->notes)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Примітки</h6>
                            <p>{{ $workLog->notes }}</p>
                        </div>
                    </div>
                @endif

                @if($workLog->loggable)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                <h6 class="alert-heading">Пов'язаний об'єкт</h6>
                                <p class="mb-0">
                                    <strong>Тип:</strong> {{ class_basename($workLog->loggable_type) }} (#{{ $workLog->loggable_id }})
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stats-card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Дії</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('work-logs.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Повернутися до списку
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('work-logs.edit', $workLog) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Редагувати
                        </a>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash"></i> Видалити
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="stats-card">
            <div class="card-header bg-white">
                <h6 class="mb-0">Додаткова інформація</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted mb-2">Дата створення запису</h6>
                    <p class="mb-0">{{ $workLog->created_at->format('d.m.Y H:i:s') }}</p>
                </div>

                @if($workLog->updated_at != $workLog->created_at)
                    <div>
                        <h6 class="text-muted mb-2">Дата останнього оновлення</h6>
                        <p class="mb-0">{{ $workLog->updated_at->format('d.m.Y H:i:s') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
@if(auth()->user()->role === 'admin')
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Видалити запис?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Ви впевнені, що бажаєте видалити цей запис про роботу?</p>
                <p class="text-muted"><small>Цю дію неможливо скасувати.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <form method="POST" action="{{ route('work-logs.destroy', $workLog) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Видалити</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
