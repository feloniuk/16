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