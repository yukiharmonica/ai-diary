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

        $user->sendEmailVerificationNotification();

        Auth::login($user);

        $this->redirect(route('verification.notice'), navigate: true);
    }
}; ?>

<div>
    {{-- ヘッダー部分：英語化・中央寄せ --}}
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Create Account</h2>
    </div>

    <form wire:submit="register">
        <!-- Name -->
        <div class="auth-input-group">
            <label for="name" class="auth-label">Name</label>
            <input wire:model="name" id="name" class="auth-input" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="auth-error" />
        </div>

        <!-- Email Address -->
        <div class="auth-input-group">
            <label for="email" class="auth-label">Email</label>
            <input wire:model="email" id="email" class="auth-input" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="auth-error" />
        </div>

        <!-- Password -->
        <div class="auth-input-group">
            <label for="password" class="auth-label">Password</label>
            <input wire:model="password" id="password" class="auth-input" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="auth-error" />
        </div>

        <!-- Confirm Password -->
        <div class="auth-input-group">
            <label for="password_confirmation" class="auth-label">Confirm Password</label>
            <input wire:model="password_confirmation" id="password_confirmation" class="auth-input" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" />
        </div>

        <div class="mt-8">
            {{-- ボタン：英語化・全幅 --}}
            <button class="auth-submit-btn w-full">
                REGISTER
            </button>
        </div>

        {{-- フッターリンク：英語化・レイアウト調整 --}}
        <div class="flex items-center justify-center mt-8">
            <div class="text-sm text-gray-500">
                Already have an account? 
                <a class="auth-link font-bold text-indigo-600" href="{{ route('login') }}" wire:navigate>
                    Log in
                </a>
            </div>
        </div>
    </form>
</div>