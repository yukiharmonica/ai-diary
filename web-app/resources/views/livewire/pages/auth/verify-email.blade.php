<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('home', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Verify Email</h2>
    </div>

    {{-- 【修正】whitespace-pre-line を追加して、翻訳テキストの改行(\n)を反映させる --}}
    <div class="mb-6 text-sm text-gray-600 leading-relaxed text-center whitespace-pre-line">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 font-medium text-sm text-green-600 text-center bg-green-50 p-3 rounded-lg border border-green-200">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex flex-col gap-4 items-center">
        <button wire:click="sendVerification" class="auth-submit-btn w-full">
            {{ __('Resend Verification Email') }}
        </button>

        <button wire:click="logout" type="submit" class="auth-link text-sm text-gray-500 hover:text-gray-900">
            {{ __('Log Out') }}
        </button>
    </div>
</div>