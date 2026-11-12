@extends('layouts.app')

@section('title', 'Обхід кабінетів')

@section('content')
<div x-data="inventoryWalk()" @keydown.window="hotkeys($event)">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="mb-1">Обхід кабінетів #{{ $audit->id }}</h4>
            <p class="text-muted mb-0">
                Кабінет і ПІБ потрапляють у довідники одразу. Обладнання — лише в чернетку, вкладка «Інвентар» зміниться після звірки.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory-audits.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Усі обходи
            </a>
            <a href="{{ route('inventory-audits.review', $audit) }}" class="btn btn-success">
                <i class="bi bi-list-check"></i> До звірки ({{ $audit->rooms->count() }} каб.)
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-9">
            <div class="stats-card p-4">
                <form method="POST" action="{{ route('inventory-audits.rooms.save', $audit) }}" x-ref="form"
                      @keydown.enter="handleEnter($event)">
                    @csrf

                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Локація</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="branch_id" class="form-label">Філія <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-select" required x-model="branchId" @change="loadRoom()">
                                        <option value="">Оберіть філію</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="room_number" class="form-label">Номер кабінету <span class="text-danger">*</span></label>
                                    <input type="text" name="room_number" id="room_number" class="form-control" required maxlength="50"
                                           placeholder="Наприклад: 101" autocomplete="off" list="auditRoomList"
                                           x-ref="room" x-model="roomNumber" @change="loadRoom()" data-enter="load">
                                    <datalist id="auditRoomList">
                                        <template x-for="room in roomOptions" :key="room">
                                            <option :value="room"></option>
                                        </template>
                                    </datalist>
                                </div>
                            </div>
                            <div class="mt-2 small" x-show="statusText" x-cloak>
                                <span :class="statusClass" x-text="statusText"></span>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3" x-show="!isWarehouse">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bi bi-person-badge"></i> Хто працює в кабінеті</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach([1, 2] as $shift)
                                    <div class="col-md-2 d-flex align-items-center">
                                        <strong>Зміна {{ $shift }}</strong>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="staff[{{ $shift }}][doctor]" class="form-control form-control-sm" maxlength="255"
                                               placeholder="Лікар: Прізвище Ім'я По батькові" autocomplete="off" list="doctorList"
                                               x-model="staff[{{ $shift }}].doctor" :disabled="isWarehouse">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="staff[{{ $shift }}][nurse]" class="form-control form-control-sm" maxlength="255"
                                               placeholder="Медсестра: Прізвище Ім'я По батькові" autocomplete="off" list="nurseList"
                                               x-model="staff[{{ $shift }}].nurse" :disabled="isWarehouse">
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">
                                Оберіть зі списку або впишіть нове ПІБ — співробітника буде додано в «Медперсонал». Порожнє поле знімає призначення.
                            </small>
                        </div>
                    </div>

                    <div class="card mb-3" x-show="records.length > 0" x-cloak>
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-database-check"></i> Числиться в системі
                                    <small class="text-muted">(відмічено <span x-text="foundCount"></span> з <span x-text="records.length"></span>)</small>
                                </h5>
                                <div>
                                    <button type="button" class="btn btn-sm btn-success" @click="markAll(true)">
                                        <i class="bi bi-check2-all"></i> Все на місці
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" @click="markAll(false)">Зняти</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="6%">Є</th>
                                            <th>Тип обладнання</th>
                                            <th>Бренд / модель</th>
                                            <th>Серійний номер</th>
                                            <th>Інвентарний номер</th>
                                            <th width="10%">К-сть</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="record in records" :key="record.id">
                                            <tr :class="record.found ? 'table-success' : ''">
                                                <td>
                                                    <input type="checkbox" class="form-check-input" style="width: 1.4em; height: 1.4em;" x-model="record.found">
                                                    <template x-if="record.found">
                                                        <span>
                                                            <input type="hidden" :name="`items[r${record.id}][room_inventory_id]`" :value="record.id">
                                                            <input type="hidden" :name="`items[r${record.id}][quantity]`" :value="record.quantity">
                                                        </span>
                                                    </template>
                                                </td>
                                                <td @click="record.found = !record.found" style="cursor: pointer;" x-text="record.equipment_type"></td>
                                                <td x-text="[record.brand, record.model].filter(Boolean).join(' ')"></td>
                                                <td x-text="record.serial_number"></td>
                                                <td x-text="record.inventory_number"></td>
                                                <td>
                                                    <input type="number" min="0" class="form-control form-control-sm" x-model.number="record.quantity" :disabled="!record.found">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-laptop"></i>
                                    <span x-text="records.length > 0 ? 'Знайдено понад список' : 'Обладнання в кабінеті'"></span>
                                </h5>
                                <div>
                                    <button type="button" class="btn btn-sm btn-success" @click="addComputerSet()">
                                        <i class="bi bi-pc-display"></i> Комп'ютерний набір
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary" @click="addRow()">
                                        <i class="bi bi-plus-lg"></i> Додати обладнання
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" x-show="extraRows.length > 0">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="4%">#</th>
                                            <th width="22%">Тип обладнання *</th>
                                            <th width="13%">Бренд</th>
                                            <th width="13%">Модель</th>
                                            <th width="16%">Серійний номер</th>
                                            <th width="18%">Інвентарний номер</th>
                                            <th width="9%">К-сть</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody x-ref="extraBody">
                                        <template x-for="(row, index) in extraRows" :key="row.uid">
                                            <tr>
                                                <td class="text-center" x-text="index + 1"></td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" maxlength="100" placeholder="Комп'ютер, Принтер..."
                                                           list="equipmentTypeList" autocomplete="off" data-enter="next"
                                                           :name="`items[n${row.uid}][equipment_type]`" x-model="row.equipment_type">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" maxlength="100" placeholder="HP, Dell..."
                                                           list="brandList" autocomplete="off" data-enter="next"
                                                           :name="`items[n${row.uid}][brand]`" x-model="row.brand">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" maxlength="100" placeholder="Model" data-enter="next"
                                                           :name="`items[n${row.uid}][model]`" x-model="row.model">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" maxlength="100" placeholder="S/N" data-enter="next"
                                                           :name="`items[n${row.uid}][serial_number]`" x-model="row.serial_number">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" maxlength="100" placeholder="Інв. №" data-enter="next"
                                                           :name="`items[n${row.uid}][inventory_number]`" x-model="row.inventory_number">
                                                </td>
                                                <td>
                                                    <input type="number" min="0" class="form-control form-control-sm" data-enter="next"
                                                           :name="`items[n${row.uid}][quantity]`" x-model.number="row.quantity">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Видалити" tabindex="-1" @click="removeRow(index)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 bg-light text-center text-muted" x-show="extraRows.length === 0">
                                <span x-text="records.length > 0
                                    ? 'Якщо в кабінеті є щось, чого немає у списку вище — додайте тут.'
                                    : 'Натисніть «Додати обладнання» або Ctrl+Enter, щоб почати.'"></span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-keyboard"></i>
                                    Enter — наступний рядок · Ctrl+Enter — новий рядок · Ctrl+D — дублювати · Ctrl+S — зберегти кабінет
                                </small>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" @click="extraRows = []" x-show="extraRows.length > 0">
                                        <i class="bi bi-trash"></i> Очистити
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" @click="duplicateLast()">
                                        <i class="bi bi-files"></i> Дублювати останнє
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <label for="notes" class="form-label"><i class="bi bi-sticky"></i> Примітка до кабінету</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" maxlength="1000" x-model="notes"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg" :disabled="!canSubmit">
                            <i class="bi bi-save"></i>
                            Зберегти кабінет (<span x-text="totalCount"></span>) і до наступного
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="stats-card p-3">
                <h5 class="mb-3">Пройдено: {{ $audit->rooms->count() }}</h5>
                @forelse($audit->rooms as $room)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2 gap-2">
                        <button type="button" class="btn btn-link p-0 text-start text-decoration-none"
                                data-branch="{{ $room->branch_id }}" data-room="{{ $room->room_number }}"
                                @click="openRoom($el.dataset.branch, $el.dataset.room)">
                            <strong>{{ $room->room_number }}</strong>
                            <span class="badge bg-secondary">{{ $room->items_count }}</span>
                            <br><small class="text-muted">{{ $room->branch?->name }}</small>
                        </button>
                        <form method="POST" action="{{ route('inventory-audits.rooms.destroy', [$audit, $room]) }}" x-data="{ confirming: false }">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger" x-show="!confirming" @click="confirming = true" title="Прибрати з обходу">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            <button type="submit" class="btn btn-sm btn-danger" x-show="confirming" x-cloak @click.outside="confirming = false">Так</button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted mb-0">Ще жодного кабінету. Оберіть філію, введіть номер кабінету і почніть.</p>
                @endforelse
            </div>
        </div>
    </div>

    <datalist id="equipmentTypeList">
        @foreach($equipmentTypes->merge(["Комп'ютер", 'Ноутбук', 'Принтер', 'МФУ', 'Сканер', 'Монітор', 'Клавіатура', 'Миша', 'ДБЖ', 'Телефон', 'Телевізор', 'Проектор'])->unique() as $type)
            <option value="{{ $type }}">
        @endforeach
    </datalist>
    <datalist id="brandList">
        @foreach(['HP', 'Dell', 'Lenovo', 'Asus', 'Acer', 'Canon', 'Epson', 'Samsung', 'LG'] as $brand)
            <option value="{{ $brand }}">
        @endforeach
    </datalist>
    <datalist id="doctorList">
        @foreach($doctorNames as $name)
            <option value="{{ $name }}">
        @endforeach
    </datalist>
    <datalist id="nurseList">
        @foreach($nurseNames as $name)
            <option value="{{ $name }}">
        @endforeach
    </datalist>
</div>
@endsection

@php
    $restored = old('room_number') ? [
        'branch_id' => old('branch_id'),
        'room_number' => old('room_number'),
        'notes' => old('notes'),
        'staff' => old('staff', []),
        'items' => array_values(old('items', [])),
    ] : null;
@endphp

@push('scripts')
<script>
function inventoryWalk() {
    const config = {
        roomDataUrl: @json(route('inventory-audits.room-data', $audit)),
        roomsByBranch: @json((object) $roomsByBranch),
        warehouseBranchId: @json((string) $warehouseBranchId),
        initialBranchId: @json((string) request('branch_id', '')),
        restored: @json($restored),
    };

    const emptyStaff = () => ({ 1: { doctor: '', nurse: '' }, 2: { doctor: '', nurse: '' } });

    return {
        branchId: config.initialBranchId,
        roomNumber: '',
        staff: emptyStaff(),
        records: [],
        extraRows: [],
        notes: '',
        loading: false,
        loadError: false,
        loadedKey: null,
        hasSaved: false,
        rowSequence: 0,
        requestSequence: 0,

        init() {
            if (config.restored) {
                this.branchId = String(config.restored.branch_id ?? '');
                this.roomNumber = config.restored.room_number ?? '';
                this.loadRoom(config.restored);
            }

            this.$nextTick(() => (this.branchId ? this.$refs.room : document.getElementById('branch_id')).focus());
        },

        get currentKey() {
            return this.branchId && this.roomNumber.trim() ? `${this.branchId}|${this.roomNumber.trim()}` : null;
        },

        get isWarehouse() {
            return this.branchId === config.warehouseBranchId;
        },

        get roomOptions() {
            return config.roomsByBranch[this.branchId] ?? [];
        },

        get foundCount() {
            return this.records.filter((record) => record.found).length;
        },

        get totalCount() {
            return this.foundCount + this.extraRows.filter((row) => row.equipment_type.trim()).length;
        },

        get canSubmit() {
            return this.currentKey !== null && !this.loading && this.loadedKey === this.currentKey;
        },

        get statusText() {
            if (this.loading) {
                return 'Завантаження даних кабінету…';
            }
            if (this.loadError) {
                return 'Не вдалося завантажити дані кабінету. Перевірте зʼєднання і введіть номер ще раз.';
            }
            if (this.currentKey === null || this.loadedKey !== this.currentKey) {
                return '';
            }
            if (this.hasSaved) {
                return 'Цей кабінет уже є в обході — ви редагуєте збережене.';
            }
            if (this.records.length > 0) {
                return `У системі за кабінетом числиться ${this.records.length} поз. Відмітьте те, що на місці.`;
            }

            return 'У системі за цим кабінетом нічого не числиться — усе введене буде новим.';
        },

        get statusClass() {
            return this.loadError ? 'text-danger' : (this.hasSaved ? 'text-warning' : 'text-muted');
        },

        makeRow(data = {}) {
            return {
                uid: ++this.rowSequence,
                equipment_type: data.equipment_type ?? '',
                brand: data.brand ?? '',
                model: data.model ?? '',
                serial_number: data.serial_number ?? '',
                inventory_number: data.inventory_number ?? '',
                quantity: Number(data.quantity ?? 1),
            };
        },

        async loadRoom(override = null) {
            const key = this.currentKey;
            if (key === null) {
                return;
            }

            const request = ++this.requestSequence;
            this.loading = true;
            this.loadError = false;

            try {
                const params = new URLSearchParams({ branch_id: this.branchId, room_number: this.roomNumber.trim() });
                const response = await fetch(`${config.roomDataUrl}?${params}`, { headers: { Accept: 'application/json' } });
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const data = await response.json();
                if (request !== this.requestSequence) {
                    return;
                }
                this.applyRoomData(data, override);
                this.loadedKey = key;
            } catch (error) {
                if (request === this.requestSequence) {
                    this.loadError = true;
                    this.loadedKey = null;
                }
            } finally {
                if (request === this.requestSequence) {
                    this.loading = false;
                }
            }
        },

        applyRoomData(data, override) {
            const saved = override ?? data.saved;
            const savedById = {};
            const extras = [];

            (saved?.items ?? []).forEach((item) => {
                const id = Number(item.room_inventory_id);
                if (id && data.records.some((record) => record.id === id)) {
                    savedById[id] = item;
                } else if ((item.equipment_type ?? '').trim()) {
                    extras.push(item);
                }
            });

            this.records = data.records.map((record) => ({
                ...record,
                found: Boolean(savedById[record.id]),
                quantity: savedById[record.id] ? Number(savedById[record.id].quantity ?? record.quantity) : record.quantity,
            }));

            if (saved) {
                this.extraRows = extras.map((item) => this.makeRow(item));
                this.notes = saved.notes ?? '';
            }

            const staff = override?.staff ?? data.staff ?? {};
            if (saved || Object.keys(staff).length > 0) {
                const next = emptyStaff();
                [1, 2].forEach((shift) => {
                    next[shift].doctor = staff[shift]?.doctor ?? '';
                    next[shift].nurse = staff[shift]?.nurse ?? '';
                });
                this.staff = next;
            }

            this.hasSaved = Boolean(data.saved);
        },

        openRoom(branchId, roomNumber) {
            this.branchId = String(branchId);
            this.roomNumber = roomNumber;
            this.extraRows = [];
            this.notes = '';
            this.staff = emptyStaff();
            this.loadRoom();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        markAll(found) {
            this.records.forEach((record) => (record.found = found));
        },

        addRow(data = {}) {
            this.extraRows.push(this.makeRow(data));
            this.focusRow(this.extraRows.length - 1);
        },

        focusRow(index) {
            this.$nextTick(() => this.$refs.extraBody.querySelectorAll('tr')[index]?.querySelector('input')?.focus());
        },

        addComputerSet() {
            const first = this.extraRows.length;
            ["Комп'ютер", 'Монітор', 'Клавіатура', 'Миша'].forEach((type) => this.extraRows.push(this.makeRow({ equipment_type: type })));
            this.focusRow(first);
        },

        duplicateLast() {
            const last = this.extraRows[this.extraRows.length - 1];
            this.addRow(last ? { equipment_type: last.equipment_type, brand: last.brand, model: last.model, quantity: last.quantity } : {});
        },

        removeRow(index) {
            this.extraRows.splice(index, 1);
        },

        handleEnter(event) {
            const target = event.target;
            if (target.tagName !== 'INPUT') {
                return;
            }

            event.preventDefault();

            if (target.dataset.enter === 'load') {
                this.loadRoom();
            } else if (target.dataset.enter === 'next') {
                const rows = Array.from(this.$refs.extraBody.querySelectorAll('tr'));
                const index = rows.indexOf(target.closest('tr'));
                if (index === rows.length - 1) {
                    this.addRow();
                } else {
                    this.focusRow(index + 1);
                }
            }
        },

        hotkeys(event) {
            if (!event.ctrlKey && !event.metaKey) {
                return;
            }

            const key = event.key.toLowerCase();
            if (event.key === 'Enter') {
                event.preventDefault();
                this.addRow();
            } else if (key === 'd') {
                event.preventDefault();
                this.duplicateLast();
            } else if (key === 's') {
                event.preventDefault();
                if (this.canSubmit) {
                    this.$refs.form.requestSubmit();
                }
            }
        },
    };
}
</script>
@endpush
