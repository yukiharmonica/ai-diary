<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="profile-container">
        <!-- 基本情報更新 -->
        <div class="profile-card">
            <div class="profile-card-header-bar"></div>
            <livewire:profile.update-profile-information-form />
        </div>

        <!-- パスワード更新 -->
        <div class="profile-card">
            <div class="profile-card-header-bar"></div>
            <livewire:profile.update-password-form />
        </div>

        <!-- アカウント削除 -->
        <div class="profile-card border-red-100">
            <div class="absolute top-0 left-0 w-full h-1 bg-red-400"></div>
            <livewire:profile.delete-user-form />
        </div>
    </div>
</x-app-layout>