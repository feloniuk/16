@extends('layouts.app')

@section('title', 'Заявка #' . $repair->id)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Заявка на ремонт #{{ $repair->id }}</h4>
                    <p class="text-muted mb-0">Створена {{ $repair->created_at->format('d.m.Y о H:i') }}</p>
                </div>
                <div>{!! $repair->status_badge !!}</div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Філія</h6>
                    <p class="mb-0">{{ $repair->branch->name }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Номер кабінету</h6>
                    <p class="mb-0">{{ $repair->room_number }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Користувач</h6>
                    <p class="mb-0">
                        @include('partials.telegram-user-link', ['username' => $repair->username, 'telegramId' => $repair->user_telegram_id])
                    </p>
                </div>

                @if($repair->phone)
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Телефон</h6>
                    <p class="mb-0">
                        <i class="bi bi-telephone"></i>
                        <a href="tel:{{ $repair->phone }}">{{ $repair->phone }}</a>
                    </p>
                </div>
                @endif

                <div class="col-12">
                    <h6 class="text-muted mb-2">Опис проблеми</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $repair->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Видача зі складу -->
        @if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3"><i class="bi bi-box-arrow-up me-2 text-warning"></i>Видати матеріали зі складу</h5>

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('repairs.issue-from-warehouse', $repair) }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Товари зі складу <span class="text-danger">*</span></label>
                </div>
                <div id="repairItemsContainer"></div>
                <button type="button" id="addMultiIssueItemBtn_repairItemsContainer" class="btn btn-sm btn-outline-success mb-3">
                    <i class="bi bi-plus"></i> Додати товар
                </button>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Дата видачі <span class="text-danger">*</span></label>
                        <input type="date" name="operation_date" class="form-control"
                               value="{{ old('operation_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Примітка</label>
                        <input type="text" name="note" class="form-control"
                               value="{{ old('note') }}" placeholder="Деталь, матеріал тощо">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-box-arrow-up"></i> Видати зі складу
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

            @if($repair->status !== 'виконана')
                <div class="d-grid gap-2 mb-3">
                    @if($repair->status === 'нова')
                    <form method="POST" action="{{ route('repairs.update', $repair) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="в_роботі">
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bi bi-gear"></i> Взяти в роботу
                        </button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('repairs.update', $repair) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="виконана">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle"></i> Позначити як виконано
                        </button>
                    </form>
                </div>
                <hr>
            @endif

            <div class="d-grid gap-2">
                <a href="{{ route('repairs.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад до списку
                </a>
                @if($repair->phone)
                <a href="tel:{{ $repair->phone }}" class="btn btn-outline-primary">
                    <i class="bi bi-telephone"></i> Зателефонувати
                </a>
                @endif
            </div>
        </div>

        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Історія змін</h5>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Заявку створено</h6>
                        <small class="text-muted">{{ $repair->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
                @if($repair->status !== 'нова')
                <div class="timeline-item">
                    <div class="timeline-marker bg-warning"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Прийнято в роботу</h6>
                        <small class="text-muted">{{ $repair->updated_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
                @endif
                @if($repair->status === 'виконана')
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Заявку виконано</h6>
                        <small class="text-muted">{{ $repair->updated_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.timeline { position: relative; padding-left: 30px; }
.timeline::before { content:''; position:absolute; left:15px; top:0; bottom:0; width:2px; background:#dee2e6; }
.timeline-item { position:relative; margin-bottom:20px; }
.timeline-marker { position:absolute; left:-37px; top:5px; width:12px; height:12px; border-radius:50%; border:2px solid white; }
.timeline-content { padding-left:15px; }
</style>
@if(in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
@include('partials.multi-item-issue-script', ['containerId' => 'repairItemsContainer'])
@endif
@endpush
