@props([
    'username' => null,
    'telegramId' => null,
])

@php
    $normalizedUsername = filled($username) ? ltrim((string) $username, '@') : null;
    $telegramIdValue = filled($telegramId) ? (string) $telegramId : null;
@endphp

@if($normalizedUsername)
    <a href="https://t.me/{{ $normalizedUsername }}"
       target="_blank"
       rel="noopener noreferrer"
       class="text-decoration-none"
       title="Відкрити Telegram профіль {{ '@'.$normalizedUsername }}">
        <i class="bi bi-telegram"></i> {{ '@'.$normalizedUsername }}
    </a>
@elseif($telegramIdValue)
    <a href="tg://user?id={{ $telegramIdValue }}"
       class="text-decoration-none"
       title="Відкрити Telegram користувача за ID">
        <i class="bi bi-telegram"></i> ID: {{ $telegramIdValue }}
    </a>
@else
    <span class="text-muted">
        <i class="bi bi-person-x"></i> Невідомий користувач
    </span>
@endif