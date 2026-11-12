@extends('layouts.app')

@section('title', 'Звірка обходу')

@php
    use App\Services\InventoryAuditService as Audit;

    $typeBadges = [
        Audit::TYPE_OK => ['bg-success', 'Збігається'],
        Audit::TYPE_CHANGED => ['bg-warning', 'Є відмінності'],
        Audit::TYPE_MOVED => ['bg-primary', 'В іншому місці'],
        Audit::TYPE_NEW => ['bg-info', 'Немає в системі'],
    ];
    $actionLabels = [
        Audit::ACTION_UPDATE => 'Оновити запис',
        Audit::ACTION_MOVE => 'Перемістити сюди',
        Audit::ACTION_CREATE => 'Створити новий запис',
        Audit::ACTION_SKIP => 'Пропустити',
    ];
    $fieldLabels = [
        'quantity' => 'К-сть',
        'brand' => 'Бренд',
        'model' => 'Модель',
        'serial_number' => 'S/N',
        'inventory_number' => 'Інв. №',
    ];
    $totals = $comparison['totals'];

    $defaults = [];
    foreach ($comparison['rooms'] as $roomComparison) {
        foreach ($roomComparison['rows'] as $row) {
            $defaults[$row['key']] = $row['default'];
        }
        foreach ($roomComparison['missing'] as $row) {
            $defaults[$row['key']] = Audit::ACTION_KEEP;
        }
    }

    $describe = fn ($source): string => implode(' · ', array_filter([
        trim($source->brand.' '.$source->model),
        $source->inventory_number ? 'інв. '.$source->inventory_number : null,
        $source->serial_number ? 'S/N '.$source->serial_number : null,
    ]));
@endphp

@section('content')
<div x-data="auditReview()">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">Звірка обходу #{{ $audit->id }} з вкладкою «Інвентар»</h4>
            <p class="text-muted mb-0">Нічого ще не змінено. Перевірте рішення по кожному рядку і натисніть «Застосувати».</p>
        </div>
        <a href="{{ route('inventory-audits.show', $audit) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Повернутися до обходу
        </a>
    </div>

    <div class="stats-card p-3 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <span><span class="badge bg-success">{{ $totals[Audit::TYPE_OK] }}</span> збігається</span>
            <span><span class="badge bg-warning">{{ $totals[Audit::TYPE_CHANGED] }}</span> з відмінностями</span>
            <span><span class="badge bg-primary">{{ $totals[Audit::TYPE_MOVED] }}</span> числиться в іншому місці</span>
            <span><span class="badge bg-info">{{ $totals[Audit::TYPE_NEW] }}</span> немає в системі</span>
            <span><span class="badge bg-danger">{{ $totals['missing'] }}</span> не знайдено в кабінетах</span>
            <div class="form-check ms-auto mb-0">
                <input class="form-check-input" type="checkbox" id="onlyDiff" x-model="onlyDiff">
                <label class="form-check-label" for="onlyDiff">Лише розбіжності</label>
            </div>
        </div>
    </div>

    @forelse($comparison['rooms'] as $roomComparison)
        @php
            $room = $roomComparison['room'];
            $hasDifferences = count($roomComparison['missing']) > 0
                || collect($roomComparison['rows'])->contains(fn (array $row): bool => $row['type'] !== Audit::TYPE_OK);
        @endphp
        <div class="card mb-3" @unless($hasDifferences) x-show="!onlyDiff" @endunless>
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-door-open"></i> {{ $room->branch?->name }} — каб. {{ $room->room_number }}
                </h5>
                <small class="text-muted">
                    знайдено {{ count($roomComparison['rows']) }}, не знайдено {{ count($roomComparison['missing']) }}
                </small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="13%">Статус</th>
                                <th width="25%">Знайдено при обході</th>
                                <th width="25%">Запис у системі</th>
                                <th width="17%">Відмінності</th>
                                <th width="20%">Що зробити</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roomComparison['rows'] as $row)
                                @php
                                    $item = $row['item'];
                                    $record = $row['record'];
                                    [$badgeClass, $badgeText] = $typeBadges[$row['type']];
                                @endphp
                                <tr @if($row['type'] === Audit::TYPE_OK) x-show="!onlyDiff" @endif>
                                    <td><span class="badge {{ $badgeClass }}">{{ $badgeText }}</span></td>
                                    <td>
                                        <strong>{{ $item->equipment_type }}</strong> × {{ $item->quantity }}
                                        <div class="small text-muted">
                                            {{ $describe($item) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($record)
                                            <a href="{{ route('inventory.show', $record) }}" target="_blank">{{ $record->equipment_type }}</a> × {{ $record->quantity }}
                                            <div class="small text-muted">
                                                {{ $describe($record) }}
                                            </div>
                                            @if($row['type'] === Audit::TYPE_MOVED)
                                                <div class="small text-primary">
                                                    <i class="bi bi-geo-alt"></i> зараз: {{ $record->branch?->name }}, каб. {{ $record->room_number ?? '—' }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        @if(count($row['suggestions']) > 0)
                                            <div class="text-muted">
                                                <i class="bi bi-info-circle"></i> такий самий інв. № уже має {{ $row['suggestions'][0]->equipment_type }}
                                                ({{ $row['suggestions'][0]->branch?->name }}, каб. {{ $row['suggestions'][0]->room_number ?? '—' }})@if(count($row['suggestions']) > 1) та ще {{ count($row['suggestions']) - 1 }}@endif
                                            </div>
                                        @endif
                                        @forelse($row['changes'] as $field => $change)
                                            <div>
                                                {{ $fieldLabels[$field] }}:
                                                <span class="text-muted">{{ $change['old'] ?? '—' }}</span> → <strong>{{ $change['new'] }}</strong>
                                            </div>
                                        @empty
                                            @if(count($row['suggestions']) === 0)
                                                <span class="text-muted">—</span>
                                            @endif
                                        @endforelse
                                    </td>
                                    <td>
                                        @if($row['type'] === Audit::TYPE_OK)
                                            <span class="text-muted">Без змін</span>
                                        @else
                                            <select class="form-select form-select-sm" x-model="decisions['{{ $row['key'] }}']">
                                                @foreach($row['actions'] as $action)
                                                    @continue(str_starts_with($action, Audit::ACTION_MATCH_PREFIX))
                                                    <option value="{{ $action }}">{{ $actionLabels[$action] }}</option>
                                                @endforeach
                                                @if($row['type'] === Audit::TYPE_NEW && count($roomComparison['missing']) > 0)
                                                    <optgroup label="Це той самий запис, що не знайдено:">
                                                        @foreach($roomComparison['missing'] as $missing)
                                                            <option value="{{ Audit::ACTION_MATCH_PREFIX.$missing['record']->id }}"
                                                                    :disabled="isTaken({{ $missing['record']->id }}, '{{ $row['key'] }}')">
                                                                {{ $missing['record']->equipment_type }}
                                                                {{ $describe($missing['record']) }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                                @if(count($row['suggestions']) > 0)
                                                    <optgroup label="Той самий інв. № в іншому місці — перемістити сюди:">
                                                        @foreach($row['suggestions'] as $suggestion)
                                                            <option value="{{ Audit::ACTION_MATCH_PREFIX.$suggestion->id }}"
                                                                    :disabled="isTaken({{ $suggestion->id }}, '{{ $row['key'] }}')">
                                                                {{ $suggestion->equipment_type }} × {{ $suggestion->quantity }}
                                                                ({{ $suggestion->branch?->name }}, каб. {{ $suggestion->room_number ?? '—' }})
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            </select>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($roomComparison['missing'] as $missing)
                                @php $record = $missing['record']; @endphp
                                <tr :class="isTaken({{ $record->id }}) ? 'opacity-50' : ''">
                                    <td>
                                        <span class="badge bg-danger" x-show="!isTaken({{ $record->id }})">Не знайдено</span>
                                        <span class="badge bg-secondary" x-show="isTaken({{ $record->id }})" x-cloak>Зіставлено вручну</span>
                                    </td>
                                    <td><span class="text-muted">—</span></td>
                                    <td>
                                        <a href="{{ route('inventory.show', $record) }}" target="_blank">{{ $record->equipment_type }}</a> × {{ $record->quantity }}
                                        <div class="small text-muted">
                                            {{ $describe($record) }}
                                        </div>
                                    </td>
                                    <td class="small text-muted">числиться, але в кабінеті не відмічено</td>
                                    <td>
                                        <select class="form-select form-select-sm" x-model="decisions['{{ $missing['key'] }}']" :disabled="isTaken({{ $record->id }})">
                                            <option value="{{ Audit::ACTION_KEEP }}">Залишити як є</option>
                                            <option value="{{ Audit::ACTION_REMOVE }}">Обнулити кількість (не знайдено)</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($roomComparison['moved_away'] as $moved)
                                <tr class="table-light">
                                    <td><span class="badge bg-secondary">Знайдено деінде</span></td>
                                    <td><span class="text-muted">—</span></td>
                                    <td>
                                        {{ $moved['record']->equipment_type }}
                                        @if($moved['record']->inventory_number)
                                            <span class="small text-muted">· інв. {{ $moved['record']->inventory_number }}</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted" colspan="2">
                                        знайдено: {{ $moved['found_in']->branch?->name }}, каб. {{ $moved['found_in']->room_number }} — рішення в тому кабінеті
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="stats-card p-5 text-center text-muted">
            В обході ще немає жодного кабінету.
        </div>
    @endforelse

    @if(count($comparison['rooms']) > 0)
        <form method="POST" action="{{ route('inventory-audits.apply', $audit) }}" class="stats-card p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            @csrf
            <input type="hidden" name="decisions" :value="JSON.stringify(decisions)">
            <span class="text-muted">
                Після застосування вкладки «Інвентар» і «Склад» оновляться, обхід буде закрито. Кабінети, яких немає в обході, не зачіпаються.
            </span>
            <div>
                <button type="button" class="btn btn-success btn-lg" x-show="!confirming" @click="confirming = true">
                    <i class="bi bi-check2-circle"></i> Застосувати до інвентарю
                </button>
                <span x-show="confirming" x-cloak>
                    <button type="button" class="btn btn-outline-secondary" @click="confirming = false">Скасувати</button>
                    <button type="submit" class="btn btn-danger btn-lg">Так, внести зміни</button>
                </span>
            </div>
        </form>
    @endif
</div>
@endsection

@push('scripts')
<script>
function auditReview() {
    return {
        decisions: @json((object) $defaults),
        onlyDiff: false,
        confirming: false,

        isTaken(recordId, exceptKey = null) {
            return Object.entries(this.decisions).some(([key, action]) => key !== exceptKey && action === `match:${recordId}`);
        },
    };
}
</script>
@endpush
