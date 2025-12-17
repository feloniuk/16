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

    <!-- Експорт з розширеним пошуком -->
    <div class="col-lg-12">
        <div class="stats-card p-4">
            <div class="text-center mb-3">
                <i class="bi bi-funnel fs-1 text-info"></i>
                <h5 class="mt-2">Експорт з розширеним пошуком</h5>
                <p class="text-muted small">Експорт з можливістю детального фільтрування (І/НЕ умови)</p>
            </div>

            <form method="GET" action="{{ route('inventory.export.grouped') }}" id="advancedExportForm">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="adv_branch_id" class="form-label">Філія</label>
                        <select name="branch_id" id="adv_branch_id" class="form-select">
                            <option value="">Всі філії</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="adv_balance_code" class="form-label">Код балансу</label>
                        <select name="balance_code" id="adv_balance_code" class="form-select">
                            <option value="">Всі коди</option>
                            @foreach($balanceCodes as $code)
                                <option value="{{ $code }}">{{ Str::limit($code, 30) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="adv_equipment" class="form-label">Найменування</label>
                        <input type="text" name="equipment_type" id="adv_equipment"
                               class="form-control" placeholder="Пошук по найменуванню">
                    </div>

                    <div class="col-md-3">
                        <label for="adv_search" class="form-label">Загальний пошук</label>
                        <input type="text" name="search" id="adv_search" class="form-control"
                               placeholder="Інв.номер, серійний...">
                    </div>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleAdvancedExportFilters">
                        <i class="bi bi-funnel-fill"></i> Розширені фільтри (І/НЕ умови)
                    </button>
                </div>

                <!-- Розширені фільтри -->
                <div id="advancedExportFiltersContainer" style="display: none;">
                    <div class="card border-info">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="bi bi-funnel-fill"></i> Розширені фільтри
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="advancedExportFilters"></div>
                            <button type="button" class="btn btn-sm btn-success mt-2" id="addAdvancedExportFilter">
                                <i class="bi bi-plus-circle"></i> Додати фільтр
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-3">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-info w-100" formaction="{{ route('inventory.export.grouped') }}">
                            <i class="bi bi-download"></i> Експорт групований (окремі аркуші)
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-outline-info w-100" formaction="{{ route('inventory.export.totals') }}">
                            <i class="bi bi-download"></i> Експорт зведення (статистика)
                        </button>
                    </div>
                </div>
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

@push('styles')
<style>
.advanced-filter-row {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    background-color: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let filterIndex = 0;
    const branches = @json($branches);

    // Toggle advanced export filters
    document.getElementById('toggleAdvancedExportFilters').addEventListener('click', function() {
        const container = document.getElementById('advancedExportFiltersContainer');
        if (container.style.display === 'none') {
            container.style.display = 'block';
            this.innerHTML = '<i class="bi bi-funnel-fill"></i> Приховати розширені фільтри';
        } else {
            container.style.display = 'none';
            this.innerHTML = '<i class="bi bi-funnel-fill"></i> Розширені фільтри (І/НЕ умови)';
        }
    });

    // Add new filter for export
    document.getElementById('addAdvancedExportFilter').addEventListener('click', function() {
        addExportFilterRow();
    });

    function createExportValueInput(field, value, index) {
        if (field === 'branch_id') {
            // Для филіалів - select з їхніми ID
            return `
                <select name="advanced_filters[${index}][value]" class="form-control form-control-sm filter-value">
                    <option value="">Оберіть філію...</option>
                    ${branches.map(b => `<option value="${b.id}" ${String(value) === String(b.id) ? 'selected' : ''}>${b.name}</option>`).join('')}
                </select>
            `;
        } else {
            // Для інших полів - текстовое поле
            return `
                <input type="text" name="advanced_filters[${index}][value]"
                       class="form-control form-control-sm filter-value"
                       placeholder="Значення..."
                       value="${value}">
            `;
        }
    }

    function addExportFilterRow(field = '', operator = 'and', value = '') {
        const currentIndex = filterIndex; // Захопити індекс для замикання

        const filterHtml = `
            <div class="advanced-filter-row" id="export-filter-${currentIndex}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-1">
                        <select name="advanced_filters[${currentIndex}][operator]" class="form-select form-select-sm">
                            <option value="and" ${operator === 'and' ? 'selected' : ''}>І (AND)</option>
                            <option value="not" ${operator === 'not' ? 'selected' : ''}>НЕ (NOT)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="advanced_filters[${currentIndex}][field]" class="form-select form-select-sm export-filter-field">
                            <option value="">Оберіть поле...</option>
                            <option value="branch_id" ${field === 'branch_id' ? 'selected' : ''}>Філія</option>
                            <option value="room_number" ${field === 'room_number' ? 'selected' : ''}>Кабінет</option>
                            <option value="balance_code" ${field === 'balance_code' ? 'selected' : ''}>Код балансу</option>
                            <option value="equipment_type" ${field === 'equipment_type' ? 'selected' : ''}>Найменування</option>
                            <option value="brand" ${field === 'brand' ? 'selected' : ''}>Бренд</option>
                            <option value="model" ${field === 'model' ? 'selected' : ''}>Модель</option>
                            <option value="serial_number" ${field === 'serial_number' ? 'selected' : ''}>Серійний номер</option>
                            <option value="inventory_number" ${field === 'inventory_number' ? 'selected' : ''}>Інвентарний номер</option>
                        </select>
                    </div>
                    <div class="col-md-7" id="export-value-input-${currentIndex}">
                        ${createExportValueInput(field, value, currentIndex)}
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-danger remove-export-filter">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('advancedExportFilters').insertAdjacentHTML('beforeend', filterHtml);

        // Отримуємо контейнер фільтра
        const filterRow = document.getElementById(`export-filter-${currentIndex}`);

        // Знаходимо select поля всередині цього контейнера
        const fieldSelect = filterRow.querySelector('.export-filter-field');

        // Знаходимо контейнер для значення
        const valueContainer = filterRow.querySelector(`#export-value-input-${currentIndex}`);

        // Вешаємо listener на change поля
        fieldSelect.addEventListener('change', function() {
            const newField = this.value;
            valueContainer.innerHTML = createExportValueInput(newField, '', currentIndex);
        });

        // Вешаємо listener на remove кнопку
        const removeBtn = filterRow.querySelector('.remove-export-filter');
        removeBtn.addEventListener('click', function() {
            filterRow.remove();
        });

        filterIndex++;
    }
});
</script>
@endpush
@endsection