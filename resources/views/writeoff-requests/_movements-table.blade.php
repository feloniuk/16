@if($groupedMovements->isEmpty())
<p class="text-muted text-center py-3">Немає виданих товарів за обраний період</p>
@else
<div class="table-responsive">
    <table class="table table-sm table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th style="width:36px"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                <th>Найменування</th>
                <th style="width:80px">Од.</th>
                <th style="width:100px">Видано</th>
                <th style="width:120px">К-сть для списання</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedMovements as $row)
            <tr class="movement-row" data-inventory-id="{{ $row['inventory_id'] }}"
                data-item-name="{{ $row['item_name'] }}"
                data-unit="{{ $row['unit'] }}"
                data-quantity="{{ $row['total_quantity'] }}">
                <td><input type="checkbox" class="form-check-input movement-checkbox"
                    data-inventory-id="{{ $row['inventory_id'] }}"
                    data-item-name="{{ $row['item_name'] }}"
                    data-unit="{{ $row['unit'] }}"
                    data-quantity="{{ $row['total_quantity'] }}"></td>
                <td>{{ $row['item_name'] }}</td>
                <td class="text-muted">{{ $row['unit'] }}</td>
                <td>{{ $row['total_quantity'] }}</td>
                <td><input type="number" class="form-control form-control-sm writeoff-qty" min="1" value="{{ $row['total_quantity'] }}" style="width:90px"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-2 text-end">
    <button type="button" class="btn btn-sm btn-primary" id="addSelectedBtn">
        <i class="bi bi-plus-circle"></i> Додати вибрані
    </button>
</div>
@endif
