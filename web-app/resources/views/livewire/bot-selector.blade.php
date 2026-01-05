<div class="bot-selector-card">
    @php
        // 設定ファイルに存在するボット名のリスト
        $availableBotNames = array_column($availableBots, 'name');
        
        // 選択されているが、設定ファイルに存在しないボット（データの不整合）を特定
        // これらを「不明なボット」として表示し、削除できるようにする
        $unknownBots = array_diff($selectedBots, $availableBotNames);
    @endphp

    <div class="bot-selector-header">
        <h3 class="bot-selector-title">
            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            AI Team
        </h3>
        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">
            {{ count($selectedBots) }} / 3
        </span>
    </div>

    {{-- 1. 選択済み（現在のチーム） --}}
    <div class="mb-8">
        <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-3 flex items-center">
            <svg class="w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Active Members
        </h4>
        
        <div class="space-y-3">
            @php $hasSelection = false; @endphp
            
            {{-- A. 正常に選択されているボット --}}
            @foreach ($availableBots as $bot)
                @if (in_array($bot['name'], $selectedBots))
                    @php $hasSelection = true; @endphp
                    <div 
                        wire:key="active-{{ md5($bot['name']) }}"
                        class="flex items-center p-3 bg-indigo-50 border border-indigo-200 rounded-xl shadow-sm transition-all duration-200 relative group"
                    >
                        <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold mr-3 flex-shrink-0">
                            {{ mb_substr($bot['name'], 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-indigo-900">{{ $bot['name'] }}</p>
                        </div>
                        <button 
                            wire:click="toggleBot('{{ $bot['name'] }}')"
                            wire:loading.attr="disabled"
                            class="p-2 text-indigo-400 hover:text-red-500 hover:bg-white rounded-full transition-colors focus:outline-none"
                            title="チームから外す"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        {{-- ローディング --}}
                        <div wire:loading wire:target="toggleBot('{{ $bot['name'] }}')" class="absolute inset-0 bg-white/60 flex items-center justify-center rounded-xl z-10">
                            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- B. データの不整合で残っているボット（救済措置） --}}
            @foreach ($unknownBots as $unknownBotName)
                @php $hasSelection = true; @endphp
                <div 
                    wire:key="unknown-{{ md5($unknownBotName) }}"
                    class="flex items-center p-3 bg-red-50 border border-red-200 rounded-xl shadow-sm transition-all duration-200 relative group"
                >
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500 font-bold mr-3 flex-shrink-0">
                        ?
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-red-800">
                            {{ $unknownBotName }}
                            <span class="text-xs font-normal text-red-600 block">（現在は利用できません）</span>
                        </p>
                    </div>
                    <button 
                        wire:click="toggleBot('{{ $unknownBotName }}')"
                        wire:loading.attr="disabled"
                        class="p-2 text-red-400 hover:text-red-600 hover:bg-white rounded-full transition-colors focus:outline-none"
                        title="削除して枠を空ける"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>

                    {{-- ローディング --}}
                    <div wire:loading wire:target="toggleBot('{{ $unknownBotName }}')" class="absolute inset-0 bg-white/60 flex items-center justify-center rounded-xl z-10">
                        <svg class="animate-spin h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            @endforeach

            @if (!$hasSelection)
                <div class="text-center py-4 bg-gray-50 rounded-xl border border-dashed border-gray-300 text-gray-400 text-sm">
                    メンバーが選択されていません
                </div>
            @endif
        </div>
    </div>

    {{-- 2. 未選択（待機メンバー） --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Available Members</h4>
        
        <div class="space-y-3">
            @foreach ($availableBots as $bot)
                @if (!in_array($bot['name'], $selectedBots))
                    @php
                        $isMaxReached = count($selectedBots) >= 3;
                    @endphp
                    <div 
                        wire:key="available-{{ md5($bot['name']) }}"
                        class="flex items-center p-3 bg-white border border-gray-200 rounded-xl transition-all duration-200 relative {{ $isMaxReached ? 'opacity-50' : 'hover:border-indigo-300 hover:shadow-sm' }}"
                    >
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold mr-3 flex-shrink-0">
                            {{ mb_substr($bot['name'], 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-700">{{ $bot['name'] }}</p>
                        </div>
                        <button 
                            wire:click="toggleBot('{{ $bot['name'] }}')"
                            wire:loading.attr="disabled"
                            @if($isMaxReached) disabled @endif
                            class="p-2 rounded-full transition-colors focus:outline-none {{ $isMaxReached ? 'text-gray-300 cursor-not-allowed' : 'text-gray-400 hover:text-indigo-600 hover:bg-indigo-50' }}"
                            title="{{ $isMaxReached ? 'これ以上追加できません' : 'チームに追加' }}"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>

                        {{-- ローディング --}}
                        <div wire:loading wire:target="toggleBot('{{ $bot['name'] }}')" class="absolute inset-0 bg-white/60 flex items-center justify-center rounded-xl z-10">
                            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- エラーメッセージ --}}
    @if (session()->has('error'))
        <div class="mt-3 text-sm text-red-600 font-bold bg-red-50 p-2 rounded-lg border border-red-100 fade-out">
            {{ session('error') }}
        </div>
    @endif
    
    @error('selectedBots') 
        <div class="mt-4 text-sm text-red-600 font-bold bg-red-50 p-2 rounded-lg border border-red-100">
            {{ $message }}
        </div>
    @enderror

    {{-- 保存ボタン --}}
    <button wire:click="save" class="bot-save-btn">
        チームを更新
    </button>
    
    {{-- 完了メッセージ --}}
    @if (session()->has('message'))
        <div class="mt-3 text-center text-xs text-green-600 font-bold fade-out">
            {{ session('message') }}
        </div>
    @endif
</div>