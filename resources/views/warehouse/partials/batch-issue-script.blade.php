{{-- resources/views/warehouse/partials/batch-issue-script.blade.php --}}
<script>
(function () {
    const itemsContainer = document.getElementById('batchItemsContainer');
    let batchItemIndex = 0;

    function escapeHtml(text) {
        if (!text) { return ''; }
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function addBatchItemRow(preset) {
        const idx = batchItemIndex++;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-start mb-2 batch-item-row';
        row.innerHTML = `
            <input type="hidden" name="items[${idx}][inventory_id]" class="batch-item-inventory-id" value="${preset && preset.id ? preset.id : ''}">
            <div class="col-md-7">
                <input type="text" class="form-control form-control-sm batch-item-search"
                       placeholder="Введіть назву товару для пошуку..." autocomplete="off"
                       value="${preset && preset.name ? escapeHtml(preset.name) : ''}"
                       ${preset && preset.readonly ? 'readonly' : ''}>
                <div class="batch-item-results border rounded mt-1 bg-white" style="display:none; max-height:200px; overflow-y:auto; position:relative; z-index:9999;"></div>
            </div>
            <div class="col-md-3">
                <input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm" min="1" value="${preset && preset.quantity ? preset.quantity : 1}" required>
            </div>
            <div class="col-md-2 d-flex align-items-start">
                <button type="button" class="btn btn-sm btn-outline-danger remove-batch-item"><i class="bi bi-trash"></i></button>
            </div>
        `;

        row.querySelector('.remove-batch-item').addEventListener('click', function () {
            if (itemsContainer.querySelectorAll('.batch-item-row').length > 1) {
                row.remove();
            }
        });

        const searchInput = row.querySelector('.batch-item-search');
        const resultsBox = row.querySelector('.batch-item-results');
        const hiddenId = row.querySelector('.batch-item-inventory-id');
        let searchTimeout;

        searchInput.addEventListener('input', function () {
            hiddenId.value = '';
            clearTimeout(searchTimeout);
            const q = this.value.trim();

            if (q.length < 2) {
                resultsBox.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('api.warehouse-items.search') }}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(items => {
                        if (!items.length) {
                            resultsBox.innerHTML = '<div class="p-2 text-muted small">Нічого не знайдено</div>';
                            resultsBox.style.display = 'block';
                            return;
                        }
                        resultsBox.innerHTML = items.map(item => {
                            const name = (item.full_name && item.full_name.trim()) ? item.full_name : item.name;
                            return `<div class="p-2 border-bottom batch-item-result" style="cursor:pointer;" data-id="${item.id}" data-name="${escapeHtml(name)}">
                                        <strong>${escapeHtml(name)}</strong>
                                        <small class="text-muted d-block">${escapeHtml(item.code ?? '')}</small>
                                    </div>`;
                        }).join('');
                        resultsBox.style.display = 'block';

                        resultsBox.querySelectorAll('.batch-item-result').forEach(el => {
                            el.addEventListener('click', function () {
                                hiddenId.value = this.dataset.id;
                                searchInput.value = this.dataset.name;
                                resultsBox.style.display = 'none';
                            });
                        });
                    });
            }, 300);
        });

        itemsContainer.appendChild(row);
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.batch-item-search') && !e.target.closest('.batch-item-results')) {
            document.querySelectorAll('.batch-item-results').forEach(el => { el.style.display = 'none'; });
        }
    });

    document.getElementById('addBatchItemBtn').addEventListener('click', () => addBatchItemRow());
    window.addBatchItemRow = addBatchItemRow;

    // Заміна обладнання: підвантаження техніки кабінету та перемикання полів
    const branchSelect = document.getElementById('destinationBranchId');
    const roomInput = document.getElementById('destinationRoomNumber');
    const oldItemSelect = document.getElementById('replaceOldItemId');
    const replaceActionWrap = document.getElementById('replaceActionWrap');
    const transferFields = document.getElementById('replaceTransferFields');

    function loadRoomEquipment() {
        const branchId = branchSelect.value;
        const room = roomInput.value.trim();

        if (!branchId || !room) {
            oldItemSelect.innerHTML = '<option value="">— Спершу оберіть філію і кабінет —</option>';
            replaceActionWrap.style.display = 'none';
            return;
        }

        oldItemSelect.innerHTML = '<option value="">Завантаження...</option>';

        fetch(`{{ route('warehouse.room-equipment') }}?branch_id=${branchId}&room_number=${encodeURIComponent(room)}`)
            .then(r => r.json())
            .then(items => {
                if (!items.length) {
                    oldItemSelect.innerHTML = '<option value="">У цьому кабінеті немає обладнання</option>';
                    return;
                }
                oldItemSelect.innerHTML = '<option value="">— Не замінюється —</option>' +
                    items.map(item => {
                        const label = (item.full_name && item.full_name.trim()) ? item.full_name : item.equipment_type;
                        return `<option value="${item.id}">${escapeHtml(label)} (інв. № ${escapeHtml(item.inventory_number ?? '—')})</option>`;
                    }).join('');
            });
    }

    branchSelect.addEventListener('change', loadRoomEquipment);
    roomInput.addEventListener('change', loadRoomEquipment);

    oldItemSelect.addEventListener('change', function () {
        replaceActionWrap.style.display = this.value ? 'block' : 'none';
        if (!this.value) {
            document.querySelectorAll('input[name="replace_action"]').forEach(r => { r.checked = false; });
            transferFields.style.display = 'none';
        }
    });

    document.querySelectorAll('input[name="replace_action"]').forEach(radio => {
        radio.addEventListener('change', function () {
            transferFields.style.display = this.value === 'transfer' ? 'flex' : 'none';
        });
    });

    // Ініціалізація: хоча б один рядок товару при відкритті модалки
    // (window.__batchIssuePresetItem дозволяє попередньо заповнити перший рядок конкретним товаром)
    document.getElementById('batchIssueModal').addEventListener('show.bs.modal', function () {
        if (itemsContainer.querySelectorAll('.batch-item-row').length === 0) {
            addBatchItemRow(window.__batchIssuePresetItem || null);
        }
    });
})();
</script>
