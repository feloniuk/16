@php
    $ukrainianMonths = [
        'січня', 'лютого', 'березня', 'квітня', 'травня', 'червня',
        'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня',
    ];
    $selectedMonth = old('print_month', $printMonth ?? null);
    $selectedYear = old('print_year', $printYear ?? date('Y'));
@endphp
<div class="col-md-3">
    <label for="print_month" class="form-label">Місяць у друку заявки</label>
    <select name="print_month" id="print_month" class="form-select @error('print_month') is-invalid @enderror">
        <option value="">—</option>
        @foreach($ukrainianMonths as $month)
            <option value="{{ $month }}" {{ $selectedMonth === $month ? 'selected' : '' }}>{{ ucfirst($month) }}</option>
        @endforeach
    </select>
    @error('print_month')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-3">
    <label for="print_year" class="form-label">Рік у друку заявки</label>
    <input type="number" name="print_year" id="print_year"
           class="form-control @error('print_year') is-invalid @enderror"
           value="{{ $selectedYear }}" min="2000" max="2100">
    @error('print_year')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
