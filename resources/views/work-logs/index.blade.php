@extends('layouts.app')

@section('title', 'Журнал робіт')

@section('content')
<!-- Кнопка показать/скрыть фильтры на мобильных -->
<button class="btn btn-outline-secondary d-md-none w-100 mb-3" type="button"
        data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
    <i class="bi bi-funnel"></i> Фільтри
</button>

<!-- Форма фильтров -->
<div class="collapse show" id="filtersCollapse">
    <div class="stats-card p-4 mb-4">
        <form method="GET" action="{{ route('work-logs.index') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-6 col-lg-2">
                <label for="work_type" class="form-label">Тип роботи</label>
                <select name="work_type" id="work_type" class="form-select">
                    <option value="">Усі типи</option>
                    <option value="inventory_transfer" {{ request('work_type') === 'inventory_transfer' ? 'selected' : '' }}>Перемішення інвентарю</option>
                    <option value="cartridge_replacement" {{ request('work_type') === 'cartridge_replacement' ? 'selected' : '' }}>Заміна картриджа</option>
                    <option value="repair_sent" {{ request('work_type') === 'repair_sent' ? 'selected' : '' }}>Відправка на ремонт</option>
                    <option value="repair_returned" {{ request('work_type') === 'repair_returned' ? 'selected' : '' }}>Повернення з ремонту</option>
                    <option value="manual" {{ request('work_type') === 'manual' ? 'selected' : '' }}>Ручний запис</option>
                </select>
            </div>

            <div class="col-12 col-md-6 col-lg-2">
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

            <div class="col-12 col-md-6 col-lg-2">
                <label for="date_from" class="form-label">Дата від</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="col-12 col-md-6 col-lg-2">
                <label for="date_to" class="form-label">Дата до</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <label for="search" class="form-label">Пошук</label>
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Опис, кабінет..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-6 col-md-3 col-lg-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            @if(request()->hasAny(['work_type', 'branch_id', 'date_from', 'date_to', 'search']))
            <div class="col-6 col-md-3">
                <a href="{{ route('work-logs.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x"></i>
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Заголовок та кнопка додавання -->
<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Журнал робіт ({{ $workLogs->total() }})</h2>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('work-logs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Додати запис
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Таблиця записів -->
<div class="stats-card">
    <div class="card-body p-0">
        @if($workLogs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 85px;">Тип</th>
                            <th>Опис</th>
                            <th class="d-none d-md-table-cell">Філія</th>
                            <th class="d-none d-lg-table-cell">Кабінет</th>
                            <th class="text-center" style="width: 85px;">Дата</th>
                            <th class="d-none d-lg-table-cell">Користувач</th>
                            <th class="text-center" style="width: 75px;">Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workLogs as $log)
                        <tr>
                            <td>
                                @switch($log->work_type)
                                    @case('inventory_transfer')
                                        <span class="badge bg-info" style="font-size: 0.7rem;">Перем.</span>
                                        @break
                                    @case('cartridge_replacement')
                                        <span class="badge bg-warning" style="font-size: 0.7rem;">Карт.</span>
                                        @break
                                    @case('repair_sent')
                                        <span class="badge bg-danger" style="font-size: 0.7rem;">Ремонт ↗</span>
                                        @break
                                    @case('repair_returned')
                                        <span class="badge bg-success" style="font-size: 0.7rem;">Ремонт ↙</span>
                                        @break
                                    @case('manual')
                                        <span class="badge bg-secondary" style="font-size: 0.7rem;">Ручн.</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div>
                                    <strong class="d-block">{{ Str::limit($log->description, 40) }}</strong>
                                    <small class="text-muted d-md-none">
                                        {{ $log->branch->name ?? '-' }}
                                        @if($log->room_number)
                                            , каб. {{ $log->room_number }}
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <small class="badge bg-light text-dark">{{ $log->branch->name ?? '-' }}</small>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small>{{ $log->room_number ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <small>{{ $log->performed_at->format('d.m') }}</small>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small><i class="bi bi-person"></i> {{ Str::limit($log->user->name ?? '-', 15) }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button"
                                       class="btn btn-outline-primary view-worklog-btn"
                                       data-worklog-id="{{ $log->id }}"
                                       title="Перегляд">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('work-logs.edit', $log) }}"
                                       class="btn btn-outline-warning" title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('work-logs.destroy', $log) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('Ви впевнені?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Видалити">
                                            <i class="bi bi-trash"></i>
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
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Записів не знайдено</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку</p>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('work-logs.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати запис
                </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Пагінація -->
@if($workLogs->total() > 0)
<div class="stats-card mt-4 p-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Показано {{ $workLogs->firstItem() }} - {{ $workLogs->lastItem() }}
            з {{ $workLogs->total() }} записів
        </div>
        <div>
            {{ $workLogs->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endif

<!-- Modal for viewing work log details -->
<div class="modal fade" id="viewWorkLogModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title mb-0" id="workLogModalTitle">Запис про роботу</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="workLogModalContent" style="max-height: 65vh; overflow-y: auto;">
                <!-- Content will be loaded here -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Завантаження...</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top" id="workLogModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.view-worklog-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const worklogId = this.getAttribute('data-worklog-id');
        const modal = new bootstrap.Modal(document.getElementById('viewWorkLogModal'));

        // Fetch work log data
        fetch(`/work-logs/${worklogId}`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update modal title
            document.getElementById('workLogModalTitle').textContent = `Запис #${data.id}`;

            // Build badge based on work type
            let badgeColor = 'secondary';
            switch(data.work_type) {
                case 'inventory_transfer': badgeColor = 'primary'; break;
                case 'cartridge_replacement': badgeColor = 'info'; break;
                case 'repair_sent': badgeColor = 'warning'; break;
                case 'repair_returned': badgeColor = 'success'; break;
            }

            // Build modal content
            let content = `
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="badge bg-${badgeColor}" style="font-size: 0.95rem; padding: 0.5rem 0.75rem;">
                            ${data.work_type_label}
                        </span>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2 fw-600">Опис роботи</h6>
                                <p class="mb-0">${data.description}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2 fw-600">Тип роботи</h6>
                                <p class="mb-0">${data.work_type_label}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2 fw-600">Філіал</h6>
                                <p class="mb-0">
                                    <span class="badge bg-light text-dark">${data.branch_name}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2 fw-600">Номер кабінету</h6>
                                <p class="mb-0">${data.room_number}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2 fw-600">Дата виконання роботи</h6>
                                <p class="mb-0">${data.performed_at}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2 fw-600">Користувач</h6>
                                <p class="mb-0"><i class="bi bi-person"></i> ${data.user_name}</p>
                            </div>
                        </div>
                    </div>
            `;

            if (data.notes) {
                content += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="mb-3">
                                <h6 class="text-muted mb-2 fw-600">Примітки</h6>
                                <p class="mb-0">${data.notes}</p>
                            </div>
                        </div>
                    </div>
                `;
            }

            if (data.has_loggable) {
                content += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info mb-0" role="alert">
                                <h6 class="alert-heading mb-2">Пов'язаний об'єкт</h6>
                                <p class="mb-0">
                                    <strong>Тип:</strong> ${data.loggable_type} (#${data.loggable_id})
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            }

            content += `
                    <div class="border-top pt-3 mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2 fw-600">Дата створення запису</h6>
                                <small class="text-muted">${data.created_at}</small>
                            </div>
            `;

            if (data.updated_differs) {
                content += `
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2 fw-600">Дата останнього оновлення</h6>
                                <small class="text-muted">${data.updated_at}</small>
                            </div>
                `;
            }

            content += `
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('workLogModalContent').innerHTML = content;

            // Update footer with action buttons
            let footer = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>';

            if (data.is_admin) {
                footer += `
                    <a href="${data.edit_url}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Редагувати
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteWorkLogModal"
                            onclick="setDeleteUrl('${data.delete_url}')">
                        <i class="bi bi-trash"></i> Видалити
                    </button>
                `;
            }

            document.getElementById('workLogModalFooter').innerHTML = footer;

            // Show modal
            modal.show();
        })
        .catch(error => {
            console.error('Error loading work log:', error);
            document.getElementById('workLogModalContent').innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Помилка при завантаженні даних. Спробуйте ще раз.
                </div>
            `;
        });
    });
});

function setDeleteUrl(url) {
    const deleteForm = document.getElementById('deleteWorkLogForm');
    deleteForm.action = url;
}
</script>

<!-- Delete Modal -->
<div class="modal fade" id="deleteWorkLogModal" tabindex="-1">
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
                <form method="POST" id="deleteWorkLogForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Видалити</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
