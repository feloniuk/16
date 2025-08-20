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