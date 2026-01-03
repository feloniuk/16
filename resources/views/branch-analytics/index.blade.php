@extends('layouts.app')

@section('title', 'Аналітика філій')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3">Аналітика філій</h1>
        <p class="text-muted">Всього філій: {{ $branchesWithMetrics->count() }}</p>
    </div>
</div>

@if($branchesWithMetrics->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        Активних філій не знайдено
    </div>
@else
    <div class="row g-4">
        @foreach($branchesWithMetrics as $item)
            <div class="col-lg-6">
                <a href="{{ route('branch-analytics.show', $item['branch']) }}" class="text-decoration-none">
                    <div class="stats-card p-4 h-100 transition-all" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $item['branch']->name }}</h5>
                                <small class="text-muted">ID: {{ $item['branch']->id }}</small>
                            </div>
                            <span class="badge bg-primary">{{ $item['total_repairs'] }} заявок</span>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-success mb-1">{{ $item['completion_rate'] }}%</h6>
                                    <small class="text-muted">Завершено</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-info mb-1">{{ $item['sla_compliance'] }}%</h6>
                                    <small class="text-muted">SLA Дотримання</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-warning mb-1">{{ $item['avg_response_time'] }}h</h6>
                                    <small class="text-muted">Середн. час</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h6 class="text-secondary mb-1">{{ $item['cartridges'] }}</h6>
                                    <small class="text-muted">Картриджів</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted d-block">
                                <i class="bi bi-arrow-right"></i>
                                Натисніть для детальної аналітики
                            </small>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif
@endsection
