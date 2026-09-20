@extends('layouts.app')

@section('title', 'Кабінет ' . $cabinet->number)

@section('content')
<div class="mx-auto flex max-w-3xl flex-col gap-6">
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Кабінет {{ $cabinet->number }} — {{ $cabinet->branch->name }}</h2>

        <form method="POST" action="{{ route('cabinets.update', $cabinet) }}" class="flex flex-col gap-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="branch_id" class="mb-1 block text-sm font-medium text-gray-700">Філія</label>
                    <select name="branch_id" id="branch_id" required class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(old('branch_id', $cabinet->branch_id) == $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="number" class="mb-1 block text-sm font-medium text-gray-700">Номер кабінету</label>
                    <input type="text" name="number" id="number" value="{{ old('number', $cabinet->number) }}" required
                           class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label for="ambulatoriya_id" class="mb-1 block text-sm font-medium text-gray-700">Амбулаторія (необов'язково)</label>
                <select name="ambulatoriya_id" id="ambulatoriya_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">—</option>
                    @foreach($ambulatorii as $ambulatoriya)
                        <option value="{{ $ambulatoriya->id }}" @selected(old('ambulatoriya_id', $cabinet->ambulatoriya_id) == $ambulatoriya->id)>{{ $ambulatoriya->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">Нотатки</label>
                <textarea name="notes" id="notes" rows="2" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $cabinet->notes) }}</textarea>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $cabinet->is_active)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                Активний
            </label>

            <div class="flex justify-end gap-2">
                <a href="{{ route('cabinets.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Назад</a>
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Зберегти</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @foreach($cabinet->shifts->sortBy('shift') as $shift)
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-medium text-gray-900">Зміна {{ $shift->shift }}</h3>

                <form method="POST" action="{{ route('cabinet-shifts.update', $shift) }}" class="flex flex-col gap-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="doctor_id_{{ $shift->id }}" class="mb-1 block text-sm font-medium text-gray-700">Лікар</label>
                        <select name="doctor_id" id="doctor_id_{{ $shift->id }}" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Без лікаря</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected($shift->doctor_id === $doctor->id)>{{ $doctor->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="nurse_id_{{ $shift->id }}" class="mb-1 block text-sm font-medium text-gray-700">Медсестра</label>
                        <select name="nurse_id" id="nurse_id_{{ $shift->id }}" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Без медсестри</option>
                            @foreach($nurses as $nurse)
                                <option value="{{ $nurse->id }}" @selected($shift->nurse_id === $nurse->id)>{{ $nurse->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Призначити</button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
