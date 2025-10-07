{{-- resources/views/warehouse/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Товар: ' . $item->equipment_type)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h4>{{ $item->equipment_type }}</h4>
                    <p class="text-muted mb-0">Код товару: <strong>{{ $item->inventory_number }}</strong></p>
                </div>
                <div>
                    <a href="{{ route('warehouse.edit', $item) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Редагувати
                    </a>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Категорія</h6>
                    <p class="mb-0">{{ $item->category ?: 'Не вказано' }}</p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Одиниця виміру</h6>
                    <p class="mb-0">{{ $item->unit }}</p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Поточний залишок</h6>
                    <p class="mb-0">
                        <span class="badge {{ $item->isLowStock() ? 'bg-warning' : 'bg-success' }} fs-6">
                            {{ $item->quantity }} {{ $item->unit }}
                        </span>
                        @if($item->isLowStock())
                            <i class="bi bi-exclamation-triangle text-warning ms-2" title="Низький залишок"></i>
                        @endif
                    </p>
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Мінімальна кількість</h6>
                    <p class="mb-0">{{ $item->min_quantity }} {{ $item->unit }}</p>
                </div>
                
                @if($item->price)
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Ціна за одиницю</h6>
                    <p class="mb-0"><strong>{{ number_format($item->price, 2) }} грн</strong></p>
                </div>
                @endif
                
                @if($item->notes)
                <div class="col-12">
                    <h6 class="text-muted mb-2">Опис</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $item->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        @if($item->movements->count() > 0)
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Останні рухи товару</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Операція</th>
                            <th>Кількість</th>
                            <th>Залишок</th>
                            <th>Користувач</th>
                            <th>Примітка</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->movements as $movement)
                        <tr>
                            <td>{{ $movement->operation_date->format('d.m.Y') }}</td>
                            <td>{!! $movement->type_badge !!}</td>
                            <td>
                                @if($movement->quantity > 0)
                                    <span class="text-success">+{{ $movement->quantity }}</span>
                                @else
                                    <span class="text-danger">{{ $movement->quantity }}</span>
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $movement->balance_after }}</span></td>
                            <td>{{ $movement->user->name }}</td>
                            <td>{{ $movement->note ?: '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-lg-4">
        <div class="stats-card p-4">
            <h5 class="mb-3">Операції з товаром</h5>
            
            <div class="d-grid gap-2">
                @if($item->quantity > 0)
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#issueModal">
                    <i class="bi bi-box-arrow-right"></i> Видати товар
                </button>
                @endif
                
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#receiptModal">
                    <i class="bi bi-box-arrow-in-left"></i> Надходження товару
                </button>
                
                <a href="{{ route('warehouse.edit', $item) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil"></i> Редагувати товар
                </a>
                
                <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Назад до списку
                </a>
            </div>
        </div>
        
        <div class="stats-card p-4 mt-4">
            <h5 class="mb-3">Статистика</h5>
            
            <div class="row g-3">
                <div class="col-12">
                    <div class="text-center p-2 bg-light rounded">
                        <div class="fs-5 fw-bold">{{ $item->movements->count() }}</div>
                        <small class="text-muted">Всього операцій</small>
                    </div>
                </div>
                
                @if($item->price && $item->quantity)
                <div class="col-12">
                    <div class="text-center p-2 bg-success bg-opacity-10 rounded">
                        <div class="fs-5 fw-bold text-success">{{ number_format($item->quantity * $item->price, 2) }} грн</div>
                        <small class="text-muted">Вартість залишку</small>
                    </div>
                </div>
                @endif
                
                <div class="col-6">
                    <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                        <div class="fs-6 fw-bold text-info">
                            {{ $item->movements->where('type', 'receipt')->sum('quantity') }}
                        </div>
                        <small class="text-muted">Надійшло</small>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="text-center p-2 bg-warning bg-opacity-10 rounded">
                        <div class="fs-6 fw-bold text-warning">
                            {{ abs($item->movements->where('type', 'issue')->sum('quantity')) }}
                        </div>
                        <small class="text-muted">Видано</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('warehouse.receipt', $item) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Надходження товару</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Товар</label>
                        <input type="text" class="form-control" value="{{ $item->equipment_type }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Кількість <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="quantity" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="document_number" class="form-label">Номер документа</label>
                        <input type="text" class="form-control" name="document_number">
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Примітка</label>
                        <textarea class="form-control" name="note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-success">Зафіксувати надходження</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($item->quantity > 0)
<!-- Issue Modal -->
<div class="modal fade" id="issueModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('warehouse.issue', $item) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Видача товару</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Товар</label>
                        <input type="text" class="form-control" value="{{ $item->equipment_type }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Доступно на складі</label>
                        <input type="text" class="form-control" value="{{ $item->quantity }} {{ $item->unit }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Кількість <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="quantity" required min="1" max="{{ $item->quantity }}">
                    </div>
                    <div class="mb-3">
                        <label for="issued_to" class="form-label">Кому видано</label>
                        <input type="text" class="form-control" name="issued_to" placeholder="ПІБ отримувача">
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Примітка</label>
                        <textarea class="form-control" name="note" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-warning">Видати</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection