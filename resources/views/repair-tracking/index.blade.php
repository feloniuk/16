{{-- resources/views/repair-tracking/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Облік ремонтів')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="stats-card p-4">
            <form method="GET" action="{{ route('repair-tracking.index') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="status" class="form-label">Статус</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Всі статуси</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Відправлено</option>
                        <option value="in_repair" {{ request('status') === 'in_repair' ? 'selected' : '' }}>На ремонті</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Завершено</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Скасовано</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="branch_id" class="form-label">Філія</label>
                    <select name="branch_id" id="branch_id" class="form-select">
                        <option value="">Всі філії</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="master_id" class="form-label">Майстер</label>
                    <select name="master_id" id="master_id" class="form-select">
                        <option value="">Всі майстри</option>
                        @foreach($masters as $master)
                            <option value="{{ $master->id }}" {{ request('master_id') == $master->id ? 'selected' : '' }}>
                                {{ $master->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="search" class="form-label">Пошук</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Пошук по накладній, опису..." value="{{ request('search') }}">
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
            <h2>Облік ремонтів ({{ $trackings->total() }})</h2>
            <div>
                <a href="{{ route('repair-masters.index') }}" class="btn btn-outline-info me-2">
                    <i class="bi bi-people"></i> Майстри
                </a>
                <a href="{{ route('repair-tracking.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати запис
                </a>
            </div>
        </div>
    </div>
</div>

<div class="stats-card">
    <div class="card-body p-0">
        @if($trackings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Обладнання</th>
                            <th>Філія/Кімната</th>
                            <th>Майстер</th>
                            <th>Дата відправки</th>
                            <th>Номер накладної</th>
                            <th>Статус</th>
                            <th>Вартість</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trackings as $tracking)
                        <tr>
                            <td><strong>#{{ $tracking->id }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $tracking->equipment->equipment_type }}</strong>
                                    @if($tracking->equipment->brand || $tracking->equipment->model)
                                        <br><small class="text-muted">{{ $tracking->equipment->brand }} {{ $tracking->equipment->model }}</small>
                                    @endif
                                    <br><small class="text-muted">Інв. №: {{ $tracking->equipment->inventory_number }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $tracking->equipment->branch->name }}</span>
                                <br>кімн. {{ $tracking->equipment->room_number }}
                            </td>
                            <td>
                                @if($tracking->repairMaster)
                                    {{ $tracking->repairMaster->name }}
                                    @if($tracking->repairMaster->phone)
                                        <br><small class="text-muted">{{ $tracking->repairMaster->phone }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">Не вказано</span>
                                @endif
                            </td>
                            <td>{{ $tracking->sent_date->format('d.m.Y') }}</td>
                            <td>{{ $tracking->invoice_number ?? '-' }}</td>
                            <td>{!! $tracking->status_badge !!}</td>
                            <td>
                                @if($tracking->cost)
                                    {{ number_format($tracking->cost, 2) }} грн
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('repair-tracking.show', $tracking) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Перегляд">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('repair-tracking.edit', $tracking) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('repair-tracking.destroy', $tracking) }}" 
                                          class="d-inline" onsubmit="return confirm('Видалити цей запис?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Видалити">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white">
                {{ $trackings->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tools fs-1 text-muted"></i>
                <h5 class="text-muted mt-3">Записи не знайдені</h5>
                <p class="text-muted">Спробуйте змінити параметри пошуку або додайте новий запис</p>
                <a href="{{ route('repair-tracking.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Додати запис
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

{{-- resources/views/repair-tracking/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Додати запис про ремонт')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4>Додати запис про відправку на ремонт</h4>
                <p class="text-muted">Заповніть форму для створення запису</p>
            </div>
            
            <form method="POST" action="{{ route('repair-tracking.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="equipment_id" class="form-label">Обладнання <span class="text-danger">*</span></label>
                        <select name="equipment_id" id="equipment_id" class="form-select @error('equipment_id') is-invalid @enderror" required>
                            <option value="">Оберіть обладнання</option>
                            @foreach($equipment as $item)
                                <option value="{{ $item->id }}" {{ old('equipment_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->equipment_type }} - {{ $item->inventory_number }} ({{ $item->branch->name }}, кімн. {{ $item->room_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('equipment_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="repair_master_id" class="form-label">Майстер</label>
                        <select name="repair_master_id" id="repair_master_id" class="form-select @error('repair_master_id') is-invalid @enderror">
                            <option value="">Оберіть майстра</option>
                            @foreach($masters as $master)
                                <option value="{{ $master->id }}" {{ old('repair_master_id') == $master->id ? 'selected' : '' }}>
                                    {{ $master->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('repair_master_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="sent_date" class="form-label">Дата відправки <span class="text-danger">*</span></label>
                        <input type="date" name="sent_date" id="sent_date" 
                               class="form-control @error('sent_date') is-invalid @enderror" 
                               value="{{ old('sent_date', date('Y-m-d')) }}" required>
                        @error('sent_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="invoice_number" class="form-label">Номер накладної</label>
                        <input type="text" name="invoice_number" id="invoice_number" 
                               class="form-control @error('invoice_number') is-invalid @enderror" 
                               value="{{ old('invoice_number') }}">
                        @error('invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="cost" class="form-label">Вартість ремонту (грн)</label>
                        <input type="number" step="0.01" name="cost" id="cost" 
                               class="form-control @error('cost') is-invalid @enderror" 
                               value="{{ old('cost') }}" placeholder="0.00">
                        @error('cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="our_description" class="form-label">Опис поломки <span class="text-danger">*</span></label>
                        <textarea name="our_description" id="our_description" rows="3" 
                                  class="form-control @error('our_description') is-invalid @enderror" 
                                  placeholder="Детальний опис проблеми з обладнанням" required>{{ old('our_description') }}</textarea>
                        @error('our_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('repair-tracking.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Скасувати
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Зберегти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- resources/views/repair-masters/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Майстри з ремонту')

@section('content')
<div class="row mb-4">
    <div class="col">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Майстри з ремонту</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMasterModal">
                <i class="bi bi-plus"></i> Додати майстра
            </button>
        </div>
    </div>
</div>

<div class="row g-4">
    @foreach($masters as $master)
    <div class="col-lg-6">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="mb-1">{{ $master->name }}</h5>
                    <span class="badge {{ $master->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $master->is_active ? 'Активний' : 'Неактивний' }}
                    </span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <button class="dropdown-item" onclick="editMaster({{ $master->id }}, '{{ $master->name }}', '{{ $master->phone }}', '{{ $master->email }}', '{{ $master->notes }}', {{ $master->is_active ? 'true' : 'false' }})">
                                <i class="bi bi-pencil"></i> Редагувати
                            </button>
                        </li>
                        @if($master->repair_trackings_count == 0)
                        <li>
                            <form method="POST" action="{{ route('repair-masters.destroy', $master) }}" 
                                  class="d-inline" onsubmit="return confirm('Видалити майстра?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-trash"></i> Видалити
                                </button>
                            </form>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="row g-3">
                @if($master->phone)
                <div class="col-12">
                    <small class="text-muted">Телефон:</small>
                    <div><i class="bi bi-telephone"></i> {{ $master->phone }}</div>
                </div>
                @endif
                
                @if($master->email)
                <div class="col-12">
                    <small class="text-muted">Email:</small>
                    <div><i class="bi bi-envelope"></i> {{ $master->email }}</div>
                </div>
                @endif
                
                <div class="col-12">
                    <small class="text-muted">Кількість ремонтів:</small>
                    <div><strong>{{ $master->repair_trackings_count }}</strong></div>
                </div>
                
                @if($master->notes)
                <div class="col-12">
                    <small class="text-muted">Примітки:</small>
                    <div class="text-muted small">{{ $master->notes }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Add Master Modal -->
<div class="modal fade" id="addMasterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('repair-masters.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Додати майстра</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ім'я <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Примітки</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-primary">Створити</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Master Modal -->
<div class="modal fade" id="editMasterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editMasterForm">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Редагувати майстра</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Ім'я <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Примітки</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                        <label class="form-check-label" for="edit_is_active">
                            Активний
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-primary">Зберегти</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editMaster(id, name, phone, email, notes, isActive) {
    document.getElementById('editMasterForm').action = `/repair-masters/${id}`;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_phone').value = phone || '';
    document.getElementById('edit_email').value = email || '';
    document.getElementById('edit_notes').value = notes || '';
    document.getElementById('edit_is_active').checked = isActive;
    
    const editModal = new bootstrap.Modal(document.getElementById('editMasterModal'));
    editModal.show();
}
</script>
@endpush

{{-- resources/views/inventory/export.blade.php --}}
@extends('layouts.app')

@section('title', 'Експорт інвентарю')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2>Експорт інвентарю в Excel</h2>
        <p class="text-muted">Оберіть тип експорту та параметри для створення звіту</p>
    </div>
</div>

<div class="row g-4">
    <!-- Експорт принтерів -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <div class="text-center mb-3">
                <i class="bi bi-printer fs-1 text-primary"></i>
                <h5 class="mt-2">Експорт принтерів</h5>
                <p class="text-muted small">Всі принтери, МФУ та сканери</p>
            </div>
            
            <form method="GET" action="{{ route('inventory.export.printers') }}">
                <div class="mb-3">
                    <label for="printer_branch_id" class="form-label">Філія (необов'язково)</label>
                    <select name="branch_id" id="printer_branch_id" class="form-select">
                        <option value="">Всі філії</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="printer_room" class="form-label">Кімната (необов'язково)</label>
                    <input type="text" name="room_number" id="printer_room" class="form-control" 
                           placeholder="Номер кімнати">
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-download"></i> Експортувати принтери
                </button>
            </form>
        </div>
    </div>
    
    <!-- Експорт по філії -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <div class="text-center mb-3">
                <i class="bi bi-building fs-1 text-success"></i>
                <h5 class="mt-2">Експорт по філії</h5>
                <p class="text-muted small">Весь інвентар конкретної філії</p>
            </div>
            
            <form method="GET" action="{{ route('inventory.export.branch') }}">
                <div class="mb-3">
                    <label for="branch_export_id" class="form-label">Філія <span class="text-danger">*</span></label>
                    <select name="branch_id" id="branch_export_id" class="form-select" required>
                        <option value="">Оберіть філію</option>
                        @foreach($branchStats as $branch)
                            <option value="{{ $branch->id }}">
                                {{ $branch->name }} ({{ $branch->inventory_count }} од.)
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-download"></i> Експортувати філію
                </button>
            </form>
        </div>
    </div>
    
    <!-- Експорт по кімнаті -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <div class="text-center mb-3">
                <i class="bi bi-door-open fs-1 text-warning"></i>
                <h5 class="mt-2">Експорт по кімнаті</h5>
                <p class="text-muted small">Інвентар конкретної кімнати</p>
            </div>
            
            <form method="GET" action="{{ route('inventory.export.room') }}">
                <div class="mb-3">
                    <label for="room_branch_id" class="form-label">Філія <span class="text-danger">*</span></label>
                    <select name="branch_id" id="room_branch_id" class="form-select" required>
                        <option value="">Оберіть філію</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="room_number" class="form-label">Номер кімнати <span class="text-danger">*</span></label>
                    <input type="text" name="room_number" id="room_number" class="form-control" 
                           placeholder="101, Кабінет директора..." required>
                </div>
                
                <button type="submit" class="btn btn-warning w-100">
                    <i class="bi bi-download"></i> Експортувати кімнату
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Статистика -->
<div class="row mt-5">
    <div class="col">
        <div class="stats-card p-4">
            <h5 class="mb-3">Статистика по філіям</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Філія</th>
                            <th>Загалом обладнання</th>
                            <th>Принтери/МФУ/Сканери</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branchStats as $branch)
                        <tr>
                            <td>{{ $branch->name }}</td>
                            <td><span class="badge bg-primary">{{ $branch->inventory_count }}</span></td>
                            <td><span class="badge bg-info">{{ $branch->printers_count }}</span></td>
                            <td>
                                <a href="{{ route('inventory.export.branch', ['branch_id' => $branch->id]) }}" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-download"></i> Експорт
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection