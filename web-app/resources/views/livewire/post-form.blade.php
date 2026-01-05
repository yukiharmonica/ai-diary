<div 
    x-data="{ isVisible: true }"
    {{-- 日付がnullまたは今日の場合のみフォーム表示 --}}
    x-on:date-selected.window="isVisible = ($event.detail.date === null || $event.detail.date === '{{ now()->format('Y-m-d') }}')"
    class="mb-8 relative"
>
    {{-- 投稿フォーム本体 --}}
    <div 
        class="post-form-card transition-all duration-300"
        :class="{ 'opacity-40 pointer-events-none filter blur-[1px]': !isVisible }"
    >
        <div class="post-form-header-bar"></div>

        <h3 class="post-form-title">
            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            新しい日記を書く
        </h3>

        <form wire:submit="save">
            {{-- 成功時のフラッシュメッセージ --}}
            @if (session()->has('message'))
                <div x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform translate-y-8"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-8"
                     x-init="setTimeout(() => show = false, 5000)"
                     class="fixed bottom-6 right-6 z-50 p-4 bg-white text-gray-800 rounded-xl border-l-4 border-green-500 shadow-2xl flex items-center max-w-xs md:max-w-sm"
                     style="display: none;"
                >
                    <div class="flex-shrink-0 p-2 bg-green-100 rounded-full mr-3 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm text-green-700">Success</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ session('message') }}</p>
                    </div>
                </div>
            @endif

            {{-- ジャンル選択 --}}
            <div class="mb-5">
                <label for="genre" class="post-form-label">Genre</label>
                <div class="relative">
                    <select wire:model="genre" id="genre" class="post-form-select">
                        @foreach($genres as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                @error('genre') <span class="post-form-error">{{ $message }}</span> @enderror
            </div>

            {{-- 日記本文 --}}
            <div class="mb-6">
                <label for="text" class="post-form-label">Diary Content</label>
                <div class="relative">
                    <textarea wire:model="text" id="text" rows="4" class="post-form-textarea" placeholder="今日の出来事や、感じたことを自由に書いてみましょう..."></textarea>
                    <div class="post-form-char-count">
                        <span x-text="$wire.text.length" :class="$wire.text.length > 400 ? 'text-red-500' : ''"></span> / 400
                    </div>
                </div>
                @error('text') <span class="post-form-error">{{ $message }}</span> @enderror
            </div>

            {{-- 送信ボタン --}}
            <div class="flex justify-end">
                <button type="submit" class="post-form-submit-btn group relative overflow-hidden" wire:loading.attr="disabled">
                    <span class="flex items-center transition-opacity duration-200" wire:loading.class="opacity-0">
                        投稿する
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </span>
                    <span class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-200" wire:loading.class="opacity-100">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        送信中...
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- 2. 投稿不可メッセージ (オーバーレイ) --}}
    <div 
        x-show="!isVisible" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="display: none;" 
        {{-- 【修正】absolute inset-0 で親要素いっぱいに広げ、中央配置にする --}}
        class="absolute inset-0 z-10 flex items-center justify-center p-4"
    >
        {{-- メッセージボックス本体 --}}
        <div class="bg-white/90 backdrop-blur-sm p-8 rounded-2xl border-2 border-dashed border-gray-300 text-center shadow-lg w-full h-full flex flex-col justify-center items-center">
            <div class="text-gray-300 mb-4 flex justify-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-600 mb-2">過去の日付を表示中</h3>
            <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                日記の投稿は「今日」の日付でのみ可能です。<br>
                過去の記録は閲覧のみとなります。
            </p>
            <button 
                @click="$dispatch('date-selected', { date: null });" 
                class="post-form-back-btn group"
            >
                <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                今日（最新）に戻って投稿する
            </button>
        </div>
    </div>
</div>