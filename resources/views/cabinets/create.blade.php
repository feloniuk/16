@extends('layouts.app')

@section('title', 'Новий кабінет')

@section('content')
<div class="mx-auto max-w-xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Новий кабінет</h2>

    <form method="POST" action="{{ route('cabinets.store') }}" class="flex flex-col gap-4">
        @csrf

        <div>
            <label for="branch_id" class="mb-1 block text-sm font-medium text-gray-700">Філія</label>
            <select name="branch_id" id="branch_id" required class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Оберіть філію</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="number" class="mb-1 block text-sm font-medium text-gray-700">Номер кабінету</label>
            <input type="text" name="number" id="number" value="{{ old('number') }}" required
                   class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <label for="ambulatoriya_id" class="mb-1 block text-sm font-medium text-gray-700">Амбулаторія (необов'язково)</label>
            <select name="ambulatoriya_id" id="ambulatoriya_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">—</option>
                @foreach($ambulatorii as $ambulatoriya)
                    <option value="{{ $ambulatoriya->id }}" @selected(old('ambulatoriya_id') == $ambulatoriya->id)>{{ $ambulatoriya->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">Нотатки</label>
            <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('cabinets.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Скасувати</a>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Створити</button>
        </div>
    </form>
</div>
@endsection
