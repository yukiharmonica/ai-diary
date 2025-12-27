<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- 左カラム（PC） / 上部（スマホ）: 投稿フォームとカレンダー --}}
                <div class="md:col-span-1 space-y-6">
                    <livewire:post-form />
                    
                    <div class="sticky top-6">
                        <livewire:calendar />
                    </div>
                </div>

                {{-- 右カラム（PC） / 下部（スマホ）: タイムライン --}}
                <div class="md:col-span-2">
                    <livewire:timeline />
                </div>

            </div>
        </div>
    </div>
</x-app-layout>