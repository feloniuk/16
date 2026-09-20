@extends('layouts.app')

@section('title', 'Кабінети')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-900">Кабінети</h2>
    <a href="{{ route('cabinets.create') }}" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
        <i class="bi bi-plus"></i>
        Додати кабінет
    </a>
</div>

<div class="flex flex-col gap-6">
    @forelse($branches as $branch)
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-4 py-3">
                <h3 class="font-medium text-gray-900">{{ $branch->name }}</h3>
            </div>

            @if($branch->cabinets->isEmpty())
                <div class="p-4 text-sm text-gray-500">Кабінетів ще не додано</div>
            @else
                <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($branch->cabinets as $cabinet)
                        <a href="{{ route('cabinets.edit', $cabinet) }}" class="block rounded-md border border-gray-200 p-3 transition hover:border-blue-300 hover:bg-blue-50">
                            <div class="mb-1 flex items-center justify-between">
                                <span class="font-medium text-gray-900">Кабінет {{ $cabinet->number }}</span>
                                @unless($cabinet->is_active)
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Неактивний</span>
                                @endunless
                            </div>
                            @if($cabinet->ambulatoriya)
                                <div class="mb-2 text-xs text-gray-500">{{ $cabinet->ambulatoriya->name }}</div>
                            @endif
                            <div class="flex flex-col gap-1 text-sm text-gray-600">
                                @foreach($cabinet->shifts as $shift)
                                    <div>
                                        Зміна {{ $shift->shift }}:
                                        {{ $shift->doctor?->full_name ?? 'без лікаря' }} /
                                        {{ $shift->nurse?->full_name ?? 'без медсестри' }}
                                    </div>
                                @endforeach
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
            Філій не знайдено
        </div>
    @endforelse
</div>
@endsection
