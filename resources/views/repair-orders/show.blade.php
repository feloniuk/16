@extends('layouts.app')

@section('title', 'Заявка ' . $repairOrder->order_number)

@section('content')
<div class="row">
    <div class="col-lg-9">
        <div class="stats-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4 class="mb-1">{{ $repairOrder->order_number }}</h4>
                    @if($repairOrder->description)
                        <p class="text-muted mb-0">{{ $repairOrder->description }}</p>
                    @endif
                </div>
                <div>{!! $repairOrder->status_badge !!}</div>
            </div>

            <!-- Основна інформація -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">СТВОРИВ</label>
                        <p class="mb-0">{{ $repairOrder->user->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">ДАТА СТВОРЕННЯ</label>
                        <p class="mb-0">{{ $repairOrder->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">МАЙСТЕР З РЕМОНТУ</label>
                        <p class="mb-0">{{ $repairOrder->repairMaster->name ?? '—' }}</p>
                    </div>
                    @if($repairOrder->invoice_number)
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">НОМЕР НАКЛАДНОЇ</label>
                            <p class="mb-0"><code>{{ $repairOrder->invoice_number }}</code></p>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">ВІДПРАВЛЕНО В РЕМОНТ</label>
                        <p class="mb-0">
                            @if($repairOrder->sent_date)
                                <span class="text-primary fw-semibold">{{ $repairOrder->sent_date->format('d.m.Y') }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">ПОВЕРНЕНО З РЕМОНТУ</label>
                        <p class="mb-0">
                            @php $returned = $repairOrder->returnedItemsCount(); $total = $repairOrder->items->count(); @endphp
                            @if($returned > 0)
                                <span class="{{ $returned === $total ? 'text-success' : 'text-warning' }} fw-semibold">
                                    {{ $returned }} / {{ $total }} позицій
                                </span>
                                @if($repairOrder->returned_date)
                                    <small class="text-muted d-block">{{ $repairOrder->returned_date->format('d.m.Y') }}</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">ЗАГАЛЬНА ВАРТІСТЬ</label>
                        <p class="mb-0 fw-bold fs-5">{{ number_format($repairOrder->total_cost, 2, ',', ' ') }} грн</p>
                    </div>
                    @if($repairOrder->approver)
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">
                                {{ $repairOrder->status === 'rejected' ? 'ВІДХИЛИВ' : 'ЗАТВЕРДИВ' }}
                            </label>
                            <p class="mb-0">{{ $repairOrder->approver->name }}
                                <small class="text-muted">{{ $repairOrder->approved_at->format('d.m.Y') }}</small>
                            </p>
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

            <!-- Таблиця обладнання -->
            <h5 class="mb-3">Обладнання для ремонту</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Обладнання</th>
                            <th>Інвент. №</th>
                            <th>Серійний №</th>
                            <th>Філія</th>
                            <th>Причина ремонту</th>
                            <th>Вартість</th>
                            <th>Отримано</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repairOrder->items as $item)
                            <tr class="{{ $item->isReturned() ? 'table-success' : '' }}">
                                <td>
                                    <div class="fw-semibold">{{ $item->equipment->equipment_type }}</div>
                                    @if($item->equipment->brand || $item->equipment->model)
                                        <small class="text-muted">{{ implode(' ', array_filter([$item->equipment->brand, $item->equipment->model])) }}</small>
                                    @endif
                                </td>
                                <td><code class="small">{{ $item->equipment->inventory_number }}</code></td>
                                <td>
                                    @if($item->equipment->serial_number)
                                        <code class="small">{{ $item->equipment->serial_number }}</code>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><small>{{ $item->equipment->branch->name ?? '—' }}</small></td>
                                <td>{{ $item->repair_description }}</td>
                                <td class="fw-bold text-nowrap">{{ $item->formatted_cost }}</td>
                                <td>
                                    @if($item->isReturned())
                                        <span class="badge bg-success">Отримано</span>
                                        <div class="small text-muted mt-1">{{ $item->returned_at->format('d.m.Y') }}</div>
                                        @if($item->return_document_number)
                                            <div class="small"><code>{{ $item->return_document_number }}</code></div>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Не повернено</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">Немає позицій</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="5" class="text-end">Загальна вартість:</td>
                            <td class="text-nowrap">{{ number_format($repairOrder->total_cost, 2, ',', ' ') }} грн</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($repairOrder->notes)
                <div class="mb-4">
                    <h6>Додаткові примітки:</h6>
                    <p class="text-muted mb-0">{{ $repairOrder->notes }}</p>
                </div>
            @endif

            <!-- Кнопки дій -->
            <div class="d-flex gap-2 flex-wrap">
                @if($repairOrder->canReceiveItems() && in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#receiveModal">
                        <i class="bi bi-box-arrow-in-down"></i> Отримати з ремонту
                    </button>
                @endif

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
                        <button type="submit" class="btn btn-outline-danger">
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

    <!-- Sidebar -->
    <div class="col-lg-3">
        <div class="stats-card p-3 mb-3">
            <h6 class="text-muted mb-3">Прогрес повернення</h6>
            @php $returned = $repairOrder->returnedItemsCount(); $total = $repairOrder->items->count(); @endphp
            <div class="d-flex justify-content-between mb-1">
                <small>Повернено</small>
                <small class="fw-bold">{{ $returned }} / {{ $total }}</small>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar {{ $returned === $total && $total > 0 ? 'bg-success' : 'bg-warning' }}"
                     style="width: {{ $total > 0 ? round($returned / $total * 100) : 0 }}%"></div>
            </div>
        </div>

        <div class="stats-card p-3 mb-3 text-center">
            <h6 class="text-muted mb-2">Вартість ремонту</h6>
            <h4 class="mb-0 text-primary">{{ number_format($repairOrder->total_cost, 2, ',', ' ') }}</h4>
            <small class="text-muted">грн</small>
        </div>

        <div class="stats-card p-3 text-center">
            <h6 class="text-muted mb-2">Позицій</h6>
            <h3 class="mb-0">{{ $total }}</h3>
        </div>
    </div>
</div>

<!-- Modal отримання з ремонту -->
@if($repairOrder->canReceiveItems() && in_array(auth()->user()->role, ['admin', 'warehouse_keeper']))
<div class="modal fade" id="receiveModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" action="{{ route('repair-orders.receive-items', $repairOrder) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-in-down me-2"></i>Отримати з ремонту</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Позиції -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Позиції, що повертаються</label>
                        <div class="border rounded">
                            @foreach($repairOrder->items as $item)
                                @if(!$item->isReturned())
                                    <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                                        <input type="checkbox" name="item_ids[]" value="{{ $item->id }}"
                                               class="form-check-input receive-checkbox mt-0" checked>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $item->equipment->equipment_type }}</div>
                                            <small class="text-muted">
                                                Інвент.: <code>{{ $item->equipment->inventory_number }}</code>
                                                @if($item->equipment->serial_number)
                                                    &nbsp;|&nbsp; Серійний: <code>{{ $item->equipment->serial_number }}</code>
                                                @endif
                                            </small>
                                            <div class="small text-muted mt-1">{{ Str::limit($item->repair_description, 80) }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            @if($repairOrder->items->every(fn($i) => $i->isReturned()))
                                <p class="text-center text-muted py-3 mb-0">Всі позиції вже повернені</p>
                            @endif
                        </div>
                        @error('item_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Дата отримання <span class="text-danger">*</span></label>
                            <input type="date" name="returned_at" class="form-control @error('returned_at') is-invalid @enderror"
                                   value="{{ old('returned_at', date('Y-m-d')) }}" required>
                            @error('returned_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Номер документа</label>
                            <input type="text" name="return_document_number"
                                   class="form-control @error('return_document_number') is-invalid @enderror"
                                   value="{{ old('return_document_number') }}"
                                   placeholder="Акт, накладна тощо">
                            @error('return_document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg"></i> Підтвердити отримання
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal відхилення -->
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

@if($errors->any() && session()->has('modal_receive'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('receiveModal')).show();
        });
    </script>
@endif
@endsection
