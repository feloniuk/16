{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Профіль користувача')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card p-4 mb-4">
            <div class="mb-4">
                <h4>Інформація профілю</h4>
                <p class="text-muted">Оновіть інформацію профілю та email адресу вашого акаунту.</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Ім'я</label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                            <div class="mt-2">
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Ваш email не підтверджено.
                                    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 text-decoration-underline">
                                            Натисніть тут, щоб повторно надіслати лист підтвердження.
                                        </button>
                                    </form>
                                </small>
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <label class="form-label">Роль</label>
                        <div class="form-control-plaintext">
                            @switch($user->role)
                                @case('admin')
                                    <span class="badge bg-primary">Адміністратор</span>
                                    @break
                                @case('director')
                                    <span class="badge bg-success">Директор</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">Користувач</span>
                            @endswitch
                        </div>
                    </div>

                    @if($user->telegram_id)
                    <div class="col-12">
                        <label class="form-label">Telegram ID</label>
                        <div class="form-control-plaintext">
                            <code>{{ $user->telegram_id }}</code>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Зберегти зміни
                    </button>
                    
                    @if(session('status') === 'profile-updated')
                        <div class="text-success">
                            <i class="bi bi-check-circle"></i> Профіль оновлено!
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Зміна пароля -->
        <div class="stats-card p-4 mb-4">
            <div class="mb-4">
                <h4>Зміна пароля</h4>
                <p class="text-muted">Переконайтеся, що ваш акаунт використовує довгий, випадковий пароль для безпеки.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <label for="current_password" class="form-label">Поточний пароль</label>
                        <input type="password" name="current_password" id="current_password" 
                               class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                               required>
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Новий пароль</label>
                        <input type="password" name="password" id="password" 
                               class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                               required>
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Підтвердження пароля</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                               required>
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-shield-lock"></i> Змінити пароль
                    </button>
                    
                    @if(session('status') === 'password-updated')
                        <div class="text-success">
                            <i class="bi bi-check-circle"></i> Пароль оновлено!
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Видалення акаунту -->
        <div class="stats-card p-4">
            <div class="mb-4">
                <h4 class="text-danger">Видалення акаунту</h4>
                <p class="text-muted">
                    Після видалення вашого акаунту всі його ресурси та дані будуть остаточно видалені. 
                    Перед видаленням акаунту завантажте всі дані або інформацію, які ви хочете зберегти.
                </p>
            </div>

            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                <i class="bi bi-trash"></i> Видалити акаунт
            </button>
        </div>
    </div>
</div>

<!-- Modal для видалення акаунту -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Видалити акаунт</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <p class="mb-3">
                        Ви впевнені, що хочете видалити свій акаунт? 
                        Після видалення акаунту всі його ресурси та дані будуть остаточно видалені.
                    </p>
                    
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Пароль</label>
                        <input type="password" name="password" id="delete_password" 
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                               placeholder="Введіть ваш пароль для підтвердження" required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Скасувати
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Видалити акаунт
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Показать modal если есть ошибки валидации при удалении
@if($errors->userDeletion->any())
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
    deleteModal.show();
@endif
</script>
@endpush