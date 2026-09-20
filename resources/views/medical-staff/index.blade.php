@extends('layouts.app')

@section('title', 'Медперсонал')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-900">Лікарі та медсестри</h2>
</div>

<form method="GET" class="mb-4 grid grid-cols-1 gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
    <div>
        <label for="type" class="mb-1 block text-sm font-medium text-gray-700">Тип</label>
        <select name="type" id="type" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Всі</option>
            <option value="doctor" @selected(request('type') === 'doctor')>Лікарі</option>
            <option value="nurse" @selected(request('type') === 'nurse')>Медсестри</option>
        </select>
    </div>

    <div>
        <label for="ambulatoriya_id" class="mb-1 block text-sm font-medium text-gray-700">Амбулаторія</label>
        <select name="ambulatoriya_id" id="ambulatoriya_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">Всі</option>
            @foreach($ambulatorii as $ambulatoriya)
                <option value="{{ $ambulatoriya->id }}" @selected((string) request('ambulatoriya_id') === (string) $ambulatoriya->id)>{{ $ambulatoriya->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex items-end gap-2">
        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            <i class="bi bi-search"></i>
            Фільтрувати
        </button>
        <a href="{{ route('medical-staff.index') }}" class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Скинути
        </a>
    </div>
</form>

<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <!-- Desktop table -->
    <div class="hidden overflow-x-auto md:block">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">ПІБ</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Тип</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Амбулаторія</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Філія</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Статус</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($staff as $person)
                    <tr>
                        <td class="px-4 py-3 text-gray-900">{{ $person->full_name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $person->type === 'doctor' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $person->type === 'doctor' ? 'Лікар' : 'Медсестра' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $person->ambulatoriya?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $person->ambulatoriya?->branch?->name ?? 'Не призначено' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $person->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $person->is_active ? 'Активний' : 'Неактивний' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Нічого не знайдено</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile cards -->
    <div class="divide-y divide-gray-100 md:hidden">
        @forelse($staff as $person)
            <div class="p-4">
                <div class="mb-1 flex items-center justify-between gap-2">
                    <span class="font-medium text-gray-900">{{ $person->full_name }}</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $person->type === 'doctor' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ $person->type === 'doctor' ? 'Лікар' : 'Медсестра' }}
                    </span>
                </div>
                <div class="text-sm text-gray-600">{{ $person->ambulatoriya?->name ?? '—' }} · {{ $person->ambulatoriya?->branch?->name ?? 'Не призначено' }}</div>
                <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $person->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $person->is_active ? 'Активний' : 'Неактивний' }}
                </span>
            </div>
        @empty
            <div class="p-4 text-center text-gray-500">Нічого не знайдено</div>
        @endforelse
    </div>
</div>

<div class="mt-4">
    {{ $staff->links() }}
</div>
@endsection
