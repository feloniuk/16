{{-- resources/views/repair-tracking/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Запис про ремонт #' . $repairTracking->id)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Запис про ремонт #{{ $repairTracking->id }}</h4>
                    <p class="text-muted mb-0">Створено {{ $repairTracking->created_at->format('d.m.Y в H:i') }}</p>
                </div>
                <div>
                    {!! $repairTracking->status_badge !!}
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Обладнання</h6>
                    <div class="bg-light p-3 rounded">
                        <strong>{{ $repairTracking->equipment->equipment_type }}</strong>
                        @if($repairTracking->equipment->brand || $repairTracking->equipment->model)
                            <br><small class="text-muted">{{ $repairTracking->equipment->brand }} {{ $repairTracking->equipment->model }}</small>
                        @endif
                        <br><small class="text-muted">Інв. №: {{ $repairTracking->equipment->inventory_number }}</small>
                        @if($repairTracking->equipment->serial_number)
                            <br><small class="text-muted">С/Н: {{ $repairTracking->equipment->serial_number }}</small>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Розташування</h6>
                    <p class="mb-0">
                        <strong>{{ $repairTracking->equipment->branch->name }}</strong><br>
                        Кімната: {{ $repairTracking->equipment->room_number }}
                    </p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Майстер з ремонту</h6>
                    <p class="mb-0">
                        @if($repairTracking->repairMaster)
                            <strong>{{ $repairTracking->repairMaster->name }}</strong>
                            @if($repairTracking->repairMaster->phone)
                                <br><i class="bi bi-telephone"></i> {{ $repairTracking->repairMaster->phone }}
                            @endif
                            @if($repairTracking->repairMaster->email)
                                <br><i class="bi bi-envelope"></i> {{ $repairTracking->repairMaster->email }}
                            @endif
                        @else
                            <span class="text-muted">Не вказано</span>
                        @endif
                    </p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Дата відправки</h6>
                    <p class="mb-0">{{ $repairTracking->sent_date->format('d.m.Y') }}</p>
                    
                    @if($repairTracking->returned_date)
                        <h6 class="text-muted mb-2 mt-3">Дата повернення</h6>
                        <p class="mb-0">{{ $repairTracking->returned_date->format('d.m.Y') }}</p>
                    @endif
                </div>
                
                @if($repairTracking->invoice_number)
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Номер накладної</h6>
                    <p class="mb-0"><code>{{ $repairTracking->invoice_number }}</code></p>
                </div>
                @endif
                
                @if($repairTracking->cost)
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Вартість ремонту</h6>
                    <p class="mb-0 text-success">
                        <strong>{{ number_format($repairTracking->cost, 2) }} грн</strong>
                    </p>
                </div>
                @endif
                
                <div class="col-12">
                    <h6 class="text-muted mb-2">Наш опис поломки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $repairTracking->our_description }}</p>
                    </div>
                </div>
                
                @if($repairTracking->repair_description)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Опис ремонту від майстра</h6>
                    <div class="bg-success bg-opacity-10 p-3 rounded border border-success border-opacity-25">
                        <p class="mb-0">{{ $repairTracking->repair_description }}</p>
                    </div>
                </div>
                @endif
                
                @if($repairTracking->notes)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Примітки</h6>
                    <div class="bg-warning bg-opacity-10 p-3 rounded border border-warning border-opacity-25">
                        <p class="mb-0">{{ $repairTracking->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="stats-card p-4">
            <h5 class="mb-3">Дії</h5>
            
            <div class="d-grid gap-2">
                <a href="{{ route('repair-tracking.edit', $repairTracking) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Редагувати
                </a>
                
                <a href="{{ route('repair-tracking.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад до списку
                </a>
                
                @if($repairTracking->repairMaster && $repairTracking->repairMaster->phone)
                <a href="tel:{{ $repairTracking->repairMaster->phone }}" class="btn btn-outline-primary">
                    <i class="bi bi-telephone"></i> Зателефонувати майстру
                </a>
                @endif
                
                <hr>
                
                <form method="POST" action="{{ route('repair-tracking.destroy', $repairTracking) }}" 
                      onsubmit="return confirm('Ви впевнені, що хочете видалити цей запис?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash"></i> Видалити запис
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Timeline -->
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Історія</h5>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Відправлено на ремонт</h6>
                        <small class="text-muted">{{ $repairTracking->sent_date->format('d.m.Y') }}</small>
                    </div>
                </div>
                
                @if($repairTracking->status !== 'sent')
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">
                            @switch($repairTracking->status)
                                @case('in_repair') Прийнято в ремонт @break
                                @case('completed') Ремонт завершено @break
                                @case('cancelled') Ремонт скасовано @break
                            @endswitch
                        </h6>
                        <small class="text-muted">{{ $repairTracking->updated_at->format('d.m.Y H:i') }}</small>
                    </div>
                </div>
                @endif
                
                @if($repairTracking->returned_date)
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Повернуто з ремонту</h6>
                        <small class="text-muted">{{ $repairTracking->returned_date->format('d.m.Y') }}</small>
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
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    padding-left: 15px;
}
</style>
@endpush