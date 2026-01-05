<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __($status));
        } else {
            $this->addError('email', __($status));
        }
    }
}; ?>

<div>
    <div class="mb-6">
        <h2 class="auth-title">パスワードをお忘れですか？</h2>
        <p class="auth-subtitle">
            ご登録のメールアドレスを入力してください。<br>
            パスワード再設定用のリンクをお送りします。
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Email Address -->
        <div class="auth-input-group">
            <label for="email" class="auth-label">{{ __('Email') }}</label>
            <input wire:model="email" id="email" class="auth-input" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="auth-error" />
        </div>

        <div class="mt-6">
            <button class="auth-submit-btn">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>

        <div class="mt-6 flex items-center justify-center">
            <a class="auth-link" href="{{ route('login') }}" wire:navigate>
                {{ __('Log in') }} に戻る
            </a>
        </div>
    </form>
</div>