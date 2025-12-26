<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <form wire:submit="save">
        {{-- フラッシュメッセージ (Alpine.jsで5秒後に自動消去) --}}
        @if (session()->has('message'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-transition.duration.500ms
                 x-init="setTimeout(() => show = false, 5000)"
                 class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">
                {{ session('message') }}
            </div>
        @endif

        {{-- ジャンル選択 --}}
        <div class="mb-4">
            <label for="genre" class="block text-sm font-medium text-gray-700 mb-1">ジャンル</label>
            <select wire:model="genre" id="genre" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="日常">日常</option>
                <option value="グルメ">グルメ</option>
                <option value="運動">運動・健康</option>
                <option value="読書">読書・学習</option>
                <option value="仕事">仕事・キャリア</option>
            </select>
            @error('genre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- 日記本文 --}}
        <div class="mb-4">
            <label for="text" class="block text-sm font-medium text-gray-700 mb-1">
                日記を書く (400文字以内)
            </label>
            <textarea 
                wire:model="text" 
                id="text" 
                rows="4" 
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="今の気持ちや出来事を書いてみよう..."
            ></textarea>
            <div class="text-right text-sm text-gray-500 mt-1">
                <span x-text="$wire.text.length"></span> / 400文字
            </div>
            @error('text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- 送信ボタン --}}
        <div class="flex justify-end">
            <button type="submit" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>投稿する</span>
                <span wire:loading>送信中...</span>
            </button>
        </div>
    </form>
</div>