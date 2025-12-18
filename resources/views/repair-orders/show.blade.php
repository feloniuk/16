@extends('layouts.app')

@section('title', 'Заявка ' . $repairOrder->order_number)

@section('content')
<div class="row">
    <div class="col-lg-10">
        <div class="stats-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>{{ $repairOrder->order_number }}</h4>
                    <p class="text-muted mb-0">{{ $repairOrder->description }}</p>
                </div>
                <div>
                    {!! $repairOrder->status_badge !!}
                </div>
            </div>

            <!-- Основна інформація -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Створив:</label>
                        <p class="text-muted mb-0">{{ $repairOrder->user->name }} ({{ $repairOrder->user->email }})</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Дата створення:</label>
                        <p class="text-muted mb-0">{{ $repairOrder->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    @if($repairOrder->approver)
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $repairOrder->status === 'rejected' ? 'Відхилив' : 'Затвердив' }}:</label>
                            <p class="text-muted mb-0">{{ $repairOrder->approver->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $repairOrder->status === 'rejected' ? 'Дата відхилення' : 'Дата затвердження' }}:</label>
                            <p class="text-muted mb-0">{{ $repairOrder->approved_at->format('d.m.Y H:i') }}</p>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Майстер з ремонту:</label>
                        <p class="text-muted mb-0">{{ $repairOrder->repairMaster->name ?? '-' }}</p>
                    </div>
                    @if($repairOrder->invoice_number)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Номер накладної:</label>
                            <p class="text-muted mb-0">{{ $repairOrder->invoice_number }}</p>
                        </div>
                    @endif
                    @if($repairOrder->sent_date)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Дата відправки:</label>
                            <p class="text-muted mb-0">{{ $repairOrder->sent_date->format('d.m.Y') }}</p>
                        </div>
                    @endif
                    @if($repairOrder->returned_date)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Дата повернення:</label>
                            <p class="text-muted mb-0">{{ $repairOrder->returned_date->format('d.m.Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($repairOrder->rejection_reason && $repairOrder->status === 'rejected')
                <div class="alert alert-danger mb-4">
                    <h6 class="alert-heading">Причина відхилення:</h6>
                    {{ $repairOrder->rejection_reason }}
                </div>
            @endif

            <!-- Таблиця предметів -->
            <h5 class="mb-3">Обладнання для ремонту</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="20%">Обладнання</th>
                            <th width="10%">Інвент. №</th>
                            <th width="15%">Філія</th>
                            <th width="35%">Описання проблеми</th>
                            <th width="12%">Вартість</th>
                            <th width="8%">Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repairOrder->items as $item)
                            <tr>
                                <td>{{ $item->equipment->equipment_type }}</td>
                                <td>{{ $item->equipment->inventory_number }}</td>
                                <td>{{ $item->equipment->branch->name ?? '-' }}</td>
                                <td>{{ $item->repair_description }}</td>
                                <td class="fw-bold">{{ number_format($item->cost, 2, ',', ' ') }} грн</td>
                                <td><span class="badge bg-success">OK</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Немає предметів</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="4" class="text-end">Загальна вартість:</td>
                            <td>{{ number_format($repairOrder->total_cost, 2, ',', ' ') }} грн</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($repairOrder->notes)
                <div class="mb-4">
                    <h6>Додаткові примітки:</h6>
                    <p class="text-muted">{{ $repairOrder->notes }}</p>
                </div>
            @endif

            <!-- Кнопки дій -->
            <div class="d-flex gap-2 flex-wrap">
                @if($repairOrder->canBeEditedBy(auth()->user()))
                    <a href="{{ route('repair-orders.edit', $repairOrder) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Редагувати
                    </a>
                @endif

                @if($repairOrder->status === 'draft' && $repairOrder->user_id === auth()->id())
                    <form method="POST" action="{{ route('repair-orders.submit', $repairOrder) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send"></i> Подати на затвердження
                        </button>
                    </form>
                @endif

                @if($repairOrder->status === 'pending_approval' && in_array(auth()->user()->role, ['admin', 'director']))
                    <form method="POST" action="{{ route('repair-orders.approve', $repairOrder) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Затвердити
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle"></i> Відхилити
                    </button>
                @endif

                @if($repairOrder->canBeEditedBy(auth()->user()) && $repairOrder->status === 'draft')
                    <form method="POST" action="{{ route('repair-orders.destroy', $repairOrder) }}" class="d-inline"
                          onsubmit="return confirm('Ви впевнені?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Видалити
                        </button>
                    </form>
                @endif

                <a href="{{ route('repair-orders.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar з інформацією -->
    <div class="col-lg-2">
        <div class="stats-card p-3 mb-3 text-center">
            <h6 class="text-muted mb-2">Статус</h6>
            <div class="badge bg-primary fs-6 p-2 w-100">
                @switch($repairOrder->status)
                    @case('draft')
                        Чернетка
                        @break
                    @case('pending_approval')
                        На затвердженні
                        @break
                    @case('approved')
                        Затверджено
                        @break
                    @case('rejected')
                        Відхилено
                        @break
                    @case('sent')
                        Відправлено
                        @break
                    @case('in_repair')
                        На ремонті
                        @break
                    @case('completed')
                        Завершено
                        @break
                    @case('cancelled')
                        Скасовано
                        @break
                @endswitch
            </div>
        </div>

        <div class="stats-card p-3 text-center">
            <h6 class="text-muted mb-2">Предметів</h6>
            <h3 class="mb-0">{{ $repairOrder->items_count ?? $repairOrder->items->count() }}</h3>
        </div>
    </div>
</div>

<!-- Modal для відхилення -->
@if($repairOrder->status === 'pending_approval' && in_array(auth()->user()->role, ['admin', 'director']))
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('repair-orders.reject', $repairOrder) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Відхилити заявку</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-bold">Причина відхилення *</label>
                        <textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror"
                                  rows="4" required placeholder="Вкажіть причину відхилення заявки"></textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                        <button type="submit" class="btn btn-danger">Відхилити заявку</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection
