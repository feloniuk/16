@extends('layouts.app')

@section('title', 'Отчеты')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2>Центр отчетов</h2>
        <p class="text-muted">Создание и экспорт различных отчетов по деятельности</p>
    </div>
</div>

<div class="row g-4">
    <!-- Отчеты по ремонтам -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <div class="text-center mb-3">
                <i class="bi bi-tools fs-1 text-primary"></i>
                <h5 class="mt-2">Отчеты по ремонтам</h5>
                <p class="text-muted small">Анализ заявок на ремонт и их выполнения</p>
            </div>
            
            <div class="d-grid gap-2">
                <a href="{{ route('reports.repairs') }}" class="btn btn-primary">
                    <i class="bi bi-eye"></i> Просмотр отчета
                </a>
                <a href="{{ route('reports.export', ['type' => 'repairs']) }}" class="btn btn-outline-success">
                    <i class="bi bi-download"></i> Скачать CSV
                </a>
            </div>
        </div>
    </div>
    
    <!-- Отчеты по картриджам -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <div class="text-center mb-3">
                <i class="bi bi-printer fs-1 text-warning"></i>
                <h5 class="mt-2">Отчеты по картриджам</h5>
                <p class="text-muted small">Статистика замен картриджей по филиалам</p>
            </div>
            
            <div class="d-grid gap-2">
                <a href="{{ route('reports.cartridges') }}" class="btn btn-warning">
                    <i class="bi bi-eye"></i> Просмотр отчета
                </a>
                <a href="{{ route('reports.export', ['type' => 'cartridges']) }}" class="btn btn-outline-success">
                    <i class="bi bi-download"></i> Скачать CSV
                </a>
            </div>
        </div>
    </div>
    
    <!-- Отчеты по инвентарю -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <div class="text-center mb-3">
                <i class="bi bi-pc-display fs-1 text-info"></i>
                <h5 class="mt-2">Отчеты по инвентарю</h5>
                <p class="text-muted small">Полный список и состояние оборудования</p>
            </div>
            
            <div class="d-grid gap-2">
                <a href="{{ route('reports.inventory') }}" class="btn btn-info">
                    <i class="bi bi-eye"></i> Просмотр отчета
                </a>
                <a href="{{ route('reports.export', ['type' => 'inventory']) }}" class="btn btn-outline-success">
                    <i class="bi bi-download"></i> Скачать CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Финансовые отчеты -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <div class="text-center mb-3">
                <i class="bi bi-cash-coin fs-1 text-success"></i>
                <h5 class="mt-2">Финансовые отчеты</h5>
                <p class="text-muted small">Затраты на подрядчиков и ремонты</p>
            </div>
            
            <div class="d-grid gap-2">
                <a href="{{ route('reports.financial') }}" class="btn btn-success">
                    <i class="bi bi-eye"></i> Просмотр отчета
                </a>
                <a href="{{ route('reports.export', ['type' => 'financial']) }}" class="btn btn-outline-success">
                    <i class="bi bi-download"></i> Скачать CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Отчеты по подрядчикам -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <div class="text-center mb-3">
                <i class="bi bi-people fs-1 text-secondary"></i>
                <h5 class="mt-2">Отчеты по подрядчикам</h5>
                <p class="text-muted small">Эффективность работы подрядчиков</p>
            </div>
            
            <div class="d-grid gap-2">
                <a href="{{ route('reports.contractors') }}" class="btn btn-secondary">
                    <i class="bi bi-eye"></i> Просмотр отчета
                </a>
                <a href="{{ route('reports.export', ['type' => 'contractors']) }}" class="btn btn-outline-success">
                    <i class="bi bi-download"></i> Скачать CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Сводный отчет -->
    <div class="col-lg-4">
        <div class="stats-card p-4 h-100">
            <div class="text-center mb-3">
                <i class="bi bi-file-earmark-text fs-1 text-dark"></i>
                <h5 class="mt-2">Сводный отчет</h5>
                <p class="text-muted small">Комплексный анализ всех показателей</p>
            </div>
            
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#summaryReportModal">
                    <i class="bi bi-gear"></i> Настроить отчет
                </button>
                <a href="{{ route('kpi.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-speedometer2"></i> KPI Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Быстрые действия -->
<div class="row mt-5">
    <div class="col">
        <div class="stats-card p-4">
            <h5 class="mb-3">Быстрые действия</h5>
            
            <div class="row g-3">
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="generateQuickReport('today')">
                        <i class="bi bi-calendar-day"></i>
                        <br>Отчет за сегодня
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="generateQuickReport('week')">
                        <i class="bi bi-calendar-week"></i>
                        <br>Отчет за неделю
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="generateQuickReport('month')">
                        <i class="bi bi-calendar-month"></i>
                        <br>Отчет за месяц
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#customPeriodModal">
                        <i class="bi bi-calendar-range"></i>
                        <br>Произвольный период
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно сводного отчета -->
<div class="modal fade" id="summaryReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Настройка сводного отчета</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="summaryReportForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Период отчета</label>
                            <select class="form-select" name="period">
                                <option value="last_week">Последняя неделя</option>
                                <option value="last_month" selected>Последний месяц</option>
                                <option value="last_quarter">Последний квартал</option>
                                <option value="last_year">Последний год</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Формат экспорта</label>
                            <select class="form-select" name="format">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Включить разделы:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_summary" name="sections[]" value="summary" checked>
                                        <label class="form-check-label" for="include_summary">Общая сводка</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_repairs_summary" name="sections[]" value="repairs" checked>
                                        <label class="form-check-label" for="include_repairs_summary">Статистика ремонтов</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_inventory_summary" name="sections[]" value="inventory" checked>
                                        <label class="form-check-label" for="include_inventory_summary">Инвентарь</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_financial_summary" name="sections[]" value="financial">
                                        <label class="form-check-label" for="include_financial_summary">Финансовые показатели</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_kpi_summary" name="sections[]" value="kpi">
                                        <label class="form-check-label" for="include_kpi_summary">KPI метрики</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="include_trends" name="sections[]" value="trends">
                                        <label class="form-check-label" for="include_trends">Тренды и графики</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сгенерировать отчет</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно для произвольного периода -->
<div class="modal fade" id="customPeriodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Отчет за произвольный период</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="customPeriodForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="custom_date_from" class="form-label">Дата от</label>
                            <input type="date" class="form-control" id="custom_date_from" name="date_from" required>
                        </div>
                        <div class="col-6">
                            <label for="custom_date_to" class="form-label">Дата до</label>
                            <input type="date" class="form-control" id="custom_date_to" name="date_to" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Тип отчета</label>
                            <select class="form-select" name="report_type">
                                <option value="summary">Сводный</option>
                                <option value="repairs">Ремонты</option>
                                <option value="cartridges">Картриджи</option>
                                <option value="inventory">Инвентарь</option>
                                <option value="financial">Финансовый</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать отчет</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateQuickReport(period) {
    const url = `{{ route('reports.quick') }}?period=${period}`;
    window.open(url, '_blank');
}

document.getElementById('summaryReportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    
    const url = `{{ route('reports.summary') }}?${params.toString()}`;
    window.open(url, '_blank');
    
    bootstrap.Modal.getInstance(document.getElementById('summaryReportModal')).hide();
});

document.getElementById('customPeriodForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    
    const url = `{{ route('reports.custom-period') }}?${params.toString()}`;
    window.open(url, '_blank');
    
    bootstrap.Modal.getInstance(document.getElementById('customPeriodModal')).hide();
});
</script>
@endpush