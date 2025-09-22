{{-- resources/views/purchase-requests/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Заявка ' . $purchaseRequest->request_number)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>Заявка {{ $purchaseRequest->request_number }}</h4>
                    <p class="text-muted mb-0">
                        Створена {{ $purchaseRequest->created_at->format('d.m.Y в H:i') }}
                        користувачем {{ $purchaseRequest->user->name }}
                    </p>
                </div>
                <div>
                    {!! $purchaseRequest->status_badge !!}
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Дата потреби</h6>
                    <p class="mb-0">{{ $purchaseRequest->requested_date->format('d.m.Y') }}</p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Загальна сума</h6>
                    <p class="mb-0">
                        @if($purchaseRequest->total_amount > 0)
                            <span class="fs-5 fw-bold text-success">{{ number_format($purchaseRequest->total_amount, 2) }} грн</span>
                        @else
                            <span class="text-muted">Не вказано</span>
                        @endif
                    </p>
                </div>
                
                @if($purchaseRequest->description)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Опис заявки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $purchaseRequest->description }}</p>
                    </div>
                </div>
                @endif
                
                @if($purchaseRequest->notes)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Примітки</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $purchaseRequest->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
            
            <h5 class="mb-3">Товари для закупівлі</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="5%">№</th>
                            <th>Назва товару</th>
                            <th>Код</th>
                            <th>Кількість</th>
                            <th>Одиниця</th>
                            <th>Очікувана ціна</th>
                            <th>Сума</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseRequest->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->item_name }}</strong>
                                @if($item->specifications)
                                    <br><small class="text-muted">{{ $item->specifications }}</small>
                                @endif
                            </td>
                            <td>
                                @if($item->item_code)
                                    <code>{{ $item->item_code }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $item->quantity }}</span></td>
                            <td>{{ $item->unit }}</td>
                            <td>
                                @if($item->estimated_price)
                                    {{ number_format($item->estimated_price, 2) }} грн
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->estimated_price)
                                    <strong>{{ number_format($item->total, 2) }} грн</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="6" class="text-end">Загальна сума:</th>
                            <th>
                                @if($purchaseRequest->total_amount > 0)
                                    <strong class="text-success">{{ number_format($purchaseRequest->total_amount, 2) }} грн</strong>
                                @else
                                    <span class="text-muted">Не визначено</span>
                                @endif
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="stats-card p-4">
            <h5 class="mb-3">Інформація про заявку</h5>
            
            <div class="row g-3">
                <div class="col-12">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="fs-4 fw-bold">{{ $purchaseRequest->items->count() }}</div>
                        <small class="text-muted">Позицій у заявці</small>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                        <div class="fs-6 fw-bold text-info">{{ $purchaseRequest->items->sum('quantity') }}</div>
                        <small class="text-muted">Загальна кількість</small>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                        <div class="fs-6 fw-bold text-warning">{{ $purchaseRequest->items->whereNotNull('estimated_price')->count() }}</div>
                        <small class="text-muted">З цінами</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Дії</h5>
            
            <div class="d-grid gap-2">
                @if(in_array($purchaseRequest->status, ['draft', 'submitted']) && $purchaseRequest->user_id === Auth::id())
                    <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Редагувати заявку
                    </a>
                @endif
                
                @if($purchaseRequest->status === 'draft' && $purchaseRequest->user_id === Auth::id())
                    <form method="POST" action="{{ route('purchase-requests.submit', $purchaseRequest) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" 
                                onclick="return confirm('Подати заявку на розгляд?')">
                            <i class="bi bi-send"></i> Подати заявку
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('purchase-requests.print', $purchaseRequest) }}" 
                   class="btn btn-outline-success" target="_blank">
                    <i class="bi bi-printer"></i> Друкувати заявку
                </a>
                
                <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад до списку
                </a>
            </div>
        </div>
        
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Історія змін</h5>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Створено</h6>
                        <p class="timeline-text">{{ $purchaseRequest->created_at->format('d.m.Y H:i') }}</p>
                        <small class="text-muted">{{ $purchaseRequest->user->name }}</small>
                    </div>
                </div>
                
                @if($purchaseRequest->status !== 'draft')
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6 class="timeline-title">Статус змінено</h6>
                        <p class="timeline-text">{{ $purchaseRequest->updated_at->format('d.m.Y H:i') }}</p>
                        <small class="text-muted">
                            @switch($purchaseRequest->status)
                                @case('submitted') Подана на розгляд @break
                                @case('approved') Затверджена @break
                                @case('rejected') Відхилена @break
                                @case('completed') Виконана @break
                                @default {{ ucfirst($purchaseRequest->status) }}
                            @endswitch
                        </small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

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
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 0.875rem;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 2px;
    font-size: 0.875rem;
}
</style>
@endsection