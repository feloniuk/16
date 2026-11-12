@extends('layouts.app')

@section('title', 'Результат обходу')

@php
    use App\Models\InventoryAuditItem;

    $resolutionBadges = [
        InventoryAuditItem::RESOLUTION_CONFIRMED => ['bg-success', 'Підтверджено'],
        InventoryAuditItem::RESOLUTION_UPDATED => ['bg-warning', 'Оновлено'],
        InventoryAuditItem::RESOLUTION_MOVED => ['bg-primary', 'Переміщено'],
        InventoryAuditItem::RESOLUTION_CREATED => ['bg-info', 'Створено'],
        InventoryAuditItem::RESOLUTION_SKIPPED => ['bg-secondary', 'Пропущено'],
    ];
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h4 class="mb-1">Обхід #{{ $audit->id }} — застосовано</h4>
        <p class="text-muted mb-0">
            {{ $audit->applied_at?->format('d.m.Y H:i') }} · {{ $audit->user?->name }} · кабінетів: {{ $audit->rooms->count() }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('inventory-audits.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Усі обходи
        </a>
        <a href="{{ route('inventory.index') }}" class="btn btn-primary">
            <i class="bi bi-pc-display"></i> До інвентарю
        </a>
    </div>
</div>

@foreach($audit->rooms as $room)
    <div class="card mb-3">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-door-open"></i> {{ $room->branch?->name }} — каб. {{ $room->room_number }}</h5>
        </div>
        <div class="card-body p-0">
            @if($room->items->isEmpty())
                <div class="p-3 text-muted">У кабінеті нічого не відмічено.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Рішення</th>
                                <th>Знайдено</th>
                                <th width="10%">К-сть</th>
                                <th width="25%">Запис в інвентарі</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($room->items as $item)
                                @php [$badgeClass, $badgeText] = $resolutionBadges[$item->resolution] ?? ['bg-secondary', '—']; @endphp
                                <tr>
                                    <td><span class="badge {{ $badgeClass }}">{{ $badgeText }}</span></td>
                                    <td>
                                        <strong>{{ $item->equipment_type }}</strong>
                                        <span class="small text-muted">
                                            {{ implode(' · ', array_filter([
                                                trim($item->brand.' '.$item->model),
                                                $item->inventory_number ? 'інв. '.$item->inventory_number : null,
                                                $item->serial_number ? 'S/N '.$item->serial_number : null,
                                            ])) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        @if($item->roomInventory)
                                            <a href="{{ route('inventory.show', $item->roomInventory) }}">#{{ $item->roomInventory->id }} {{ $item->roomInventory->equipment_type }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endforeach
@endsection
