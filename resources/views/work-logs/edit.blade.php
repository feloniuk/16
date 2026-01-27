@extends('layouts.app')

@section('title', 'Редагувати запис про роботу #' . $workLog->id)

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="stats-card p-4">
            <h4 class="mb-4">Редагувати запис #{{ $workLog->id }}</h4>

            <form method="POST" action="{{ route('work-logs.update', $workLog) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="work_type" class="form-label">Тип роботи <span class="text-danger">*</span></label>
                        <select name="work_type" id="work_type" class="form-select @error('work_type') is-invalid @enderror" required>
                            <option value="">Оберіть тип роботи</option>
                            <option value="inventory_transfer" {{ old('work_type', $workLog->work_type) == 'inventory_transfer' ? 'selected' : '' }}>Переміщення інвентарю</option>
                            <option value="cartridge_replacement" {{ old('work_type', $workLog->work_type) == 'cartridge_replacement' ? 'selected' : '' }}>Заміна картриджа</option>
                            <option value="repair_sent" {{ old('work_type', $workLog->work_type) == 'repair_sent' ? 'selected' : '' }}>Відправка на ремонт</option>
                            <option value="repair_returned" {{ old('work_type', $workLog->work_type) == 'repair_returned' ? 'selected' : '' }}>Повернення з ремонту</option>
                            <option value="manual" {{ old('work_type', $workLog->work_type) == 'manual' ? 'selected' : '' }}>Інше</option>
                        </select>
                        @error('work_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Опис роботи <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4" placeholder="Детально опишіть проведену роботу..." maxlength="1000" required>{{ old('description', $workLog->description) }}</textarea>
                        <small class="form-text text-muted">Максимум 1000 символів</small>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">Філіал <span class="text-danger">*</span></label>
                                <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                    <option value="">Оберіть філіал</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $workLog->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="room_number" class="form-label">Номер кабінету <span class="text-danger">*</span></label>
                                <input type="text" name="room_number" id="room_number" class="form-control @error('room_number') is-invalid @enderror"
                                       placeholder="Наприклад: 101, каб. 5" maxlength="50" value="{{ old('room_number', $workLog->room_number) }}" required>
                                @error('room_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="performed_at" class="form-label">Дата виконання роботи <span class="text-danger">*</span></label>
                        <input type="date" name="performed_at" id="performed_at" class="form-control @error('performed_at') is-invalid @enderror"
                               value="{{ old('performed_at', $workLog->performed_at->toDateString()) }}" required>
                        @error('performed_at')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Примітки</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                  rows="3" placeholder="Додаткова інформація..." maxlength="2000">{{ old('notes', $workLog->notes) }}</textarea>
                        <small class="form-text text-muted">Максимум 2000 символів (необов'язково)</small>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('work-logs.show', $workLog) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i> Скасувати
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Зберегти зміни
                        </button>
                    </div>
                </form>
        </div>
    </div>
</div>
@endsection
