<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('home', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- ヘッダー部分：シンプルにタイトルのみ、中央寄せ --}}
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Welcome Back</h2>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div class="auth-input-group">
            <label for="email" class="auth-label">Email</label>
            <input wire:model="form.email" id="email" class="auth-input" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="auth-error" />
        </div>

        <!-- Password -->
        <div class="auth-input-group">
            <label for="password" class="auth-label">Password</label>
            <input wire:model="form.password" id="password" class="auth-input" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('form.password')" class="auth-error" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4 mb-6">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Remember me</span>
            </label>
        </div>

        <div class="mt-6">
            <button class="auth-submit-btn w-full">
                LOG IN
            </button>
        </div>

        {{-- フッターリンク：英語化してレイアウト調整 --}}
        <div class="flex flex-col items-center justify-center mt-8 gap-4">
            @if (Route::has('password.request'))
                <a class="auth-link text-gray-400 hover:text-gray-600" href="{{ route('password.request') }}" wire:navigate>
                    Forgot your password?
                </a>
            @endif
            
            <div class="text-sm text-gray-500">
                Don't have an account? 
                <a class="auth-link font-bold text-indigo-600" href="{{ route('register') }}" wire:navigate>
                    Sign up
                </a>
            </div>
        </div>
    </form>
</div>