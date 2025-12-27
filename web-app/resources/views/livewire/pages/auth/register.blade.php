<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6">
        <h2 class="auth-title">アカウント作成</h2>
        <p class="auth-subtitle">AIたちとの新しい日記体験を始めましょう</p>
    </div>

    <form wire:submit="register">
        <!-- Name -->
        <div class="auth-input-group">
            <label for="name" class="auth-label">{{ __('Name') }}</label>
            <input wire:model="name" id="name" class="auth-input" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="auth-error" />
        </div>

        <!-- Email Address -->
        <div class="auth-input-group">
            <label for="email" class="auth-label">{{ __('Email') }}</label>
            <input wire:model="email" id="email" class="auth-input" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="auth-error" />
        </div>

        <!-- Password -->
        <div class="auth-input-group">
            <label for="password" class="auth-label">{{ __('Password') }}</label>
            <input wire:model="password" id="password" class="auth-input" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="auth-error" />
        </div>

        <!-- Confirm Password -->
        <div class="auth-input-group">
            <label for="password_confirmation" class="auth-label">{{ __('Confirm Password') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" class="auth-input" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" />
        </div>

        <div class="mt-8">
            <button class="auth-submit-btn">
                {{ __('Register') }}
            </button>
        </div>

        <div class="flex items-center justify-center mt-6">
            <a class="auth-link" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>
        </div>
    </form>
</div>