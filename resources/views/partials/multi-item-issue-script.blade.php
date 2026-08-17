{{-- resources/views/partials/multi-item-issue-script.blade.php --}}
{{-- Expects a container element with id given via $containerId, and window.addMultiIssueItemRow to be called for the first row. --}}
<script>
(function () {
    const containerId = '{{ $containerId }}';
    const container = document.getElementById(containerId);
    if (!container) { return; }

    let multiIssueIndex_{{ $containerId }} = 0;

    function escapeHtml(text) {
        if (!text) { return ''; }
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function addRow() {
        const idx = multiIssueIndex_{{ $containerId }}++;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-start mb-2 multi-issue-row';
        row.innerHTML = `
            <input type="hidden" name="items[${idx}][inventory_id]" class="multi-issue-inventory-id">
            <div class="col-md-7">
                <input type="text" class="form-control form-control-sm multi-issue-search"
                       placeholder="Введіть назву товару для пошуку..." autocomplete="off">
                <div class="multi-issue-results border rounded mt-1 bg-white" style="display:none; max-height:200px; overflow-y:auto; position:relative; z-index:9999;"></div>
            </div>
            <div class="col-md-3">
                <input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm" min="1" value="1" required>
            </div>
            <div class="col-md-2 d-flex align-items-start">
                <button type="button" class="btn btn-sm btn-outline-danger remove-multi-issue-row"><i class="bi bi-trash"></i></button>
            </div>
        `;

        row.querySelector('.remove-multi-issue-row').addEventListener('click', function () {
            if (container.querySelectorAll('.multi-issue-row').length > 1) {
                row.remove();
            }
        });

        const searchInput = row.querySelector('.multi-issue-search');
        const resultsBox = row.querySelector('.multi-issue-results');
        const hiddenId = row.querySelector('.multi-issue-inventory-id');
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
                            return `<div class="p-2 border-bottom multi-issue-result" style="cursor:pointer;" data-id="${item.id}" data-name="${escapeHtml(name)}">
                                        <strong>${escapeHtml(name)}</strong>
                                        <small class="text-muted d-block">${escapeHtml(item.code ?? '')}</small>
                                    </div>`;
                        }).join('');
                        resultsBox.style.display = 'block';

                        resultsBox.querySelectorAll('.multi-issue-result').forEach(el => {
                            el.addEventListener('click', function () {
                                hiddenId.value = this.dataset.id;
                                searchInput.value = this.dataset.name;
                                resultsBox.style.display = 'none';
                            });
                        });
                    });
            }, 300);
        });

        container.appendChild(row);
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.multi-issue-search') && !e.target.closest('.multi-issue-results')) {
            document.querySelectorAll('.multi-issue-results').forEach(el => { el.style.display = 'none'; });
        }
    });

    const addBtn = document.getElementById('addMultiIssueItemBtn_{{ $containerId }}');
    if (addBtn) {
        addBtn.addEventListener('click', addRow);
    }

    addRow();
    window['addMultiIssueRow_' + containerId] = addRow;
    window['resetMultiIssueRows_' + containerId] = function (presearchText) {
        container.innerHTML = '';
        addRow();

        if (presearchText) {
            const searchInput = container.querySelector('.multi-issue-search');
            searchInput.value = presearchText;
            searchInput.dispatchEvent(new Event('input'));
        }
    };
})();
</script>
