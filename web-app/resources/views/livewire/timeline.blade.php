<div class="space-y-8" wire:poll.5s>
    {{-- 【追加】ジャンルフィルター --}}
    <div class="flex flex-wrap gap-2 mb-4">
        <button 
            wire:click="selectGenre('null')"
            class="px-4 py-1.5 text-xs font-bold rounded-full transition-all duration-200 border shadow-sm {{ is_null($selectedGenre) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-500 border-gray-200 hover:border-indigo-300 hover:text-indigo-600' }}"
        >
            すべて
        </button>
        @foreach($genres as $genre)
            <button 
                wire:click="selectGenre('{{ $genre }}')"
                class="px-4 py-1.5 text-xs font-bold rounded-full transition-all duration-200 border shadow-sm {{ $selectedGenre === $genre ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-500 border-gray-200 hover:border-indigo-300 hover:text-indigo-600' }}"
            >
                {{ $genre }}
            </button>
        @endforeach
    </div>

    {{-- 日付フィルター状態の表示 --}}
    @if ($isFiltered)
        <div class="flex items-center justify-between bg-indigo-50 p-5 rounded-2xl border border-indigo-100 shadow-sm transition-all duration-300">
            <div class="flex items-center text-indigo-800 font-bold">
                <div class="p-2 bg-white rounded-full text-indigo-500 mr-3 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                {{ $displayDate }} の日記
            </div>
            <button 
                wire:click="clearFilter" 
                class="px-4 py-2 bg-white text-indigo-600 text-sm font-bold rounded-xl shadow-sm border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-colors duration-200"
            >
                全件表示に戻す
            </button>
        </div>
    @endif

    @foreach ($posts as $post)
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl relative overflow-hidden group">
            
            {{-- 完了ステータスの装飾バー --}}
            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $post->status === 'completed' ? 'bg-gradient-to-b from-indigo-400 to-indigo-600' : ($post->status === 'failed' ? 'bg-red-400' : 'bg-gray-300') }}"></div>

            {{-- 1. 投稿ヘッダー --}}
            <div class="pl-4">
                <div class="flex justify-between items-start mb-3">
                    <span class="inline-block px-3 py-1 text-xs font-bold text-indigo-700 bg-indigo-50 rounded-full border border-indigo-100">
                        {{ $post->genre }}
                    </span>
                    <span class="text-xs font-medium text-gray-400 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $post->created_at->diffForHumans() }}
                    </span>
                </div>

                {{-- 投稿本文 --}}
                <p class="text-gray-800 text-lg leading-relaxed whitespace-pre-wrap font-medium">
                    {{ $post->text }}
                </p>
            </div>

            {{-- 2. AI処理中の表示 --}}
            @if ($post->status === 'pending' || $post->status === 'processing')
                <div class="ml-4 mt-6 p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center text-sm text-gray-500 animate-pulse">
                    <svg class="w-5 h-5 mr-2 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>AIチームが返信を執筆中...</span>
                </div>
            @endif

            {{-- 3. AIからの返信一覧 --}}
            @if ($post->reactions->isNotEmpty())
                <div class="ml-4 mt-6 space-y-4 pt-4 border-t border-dashed border-gray-200">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        AI Reactions
                    </h4>
                    
                    @foreach ($post->reactions as $reaction)
                        <div class="flex items-start group/reaction transition-transform hover:translate-x-1 duration-200">
                            <div class="flex-shrink-0 mr-3 mt-1">
                                {{-- ボットアイコン --}}
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-white flex items-center justify-center text-indigo-600 font-extrabold text-sm shadow-sm border border-indigo-100 ring-2 ring-transparent group-hover/reaction:ring-indigo-100 transition-all">
                                    {{ mb_substr($reaction->bot_name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1 bg-gray-50 p-4 rounded-r-2xl rounded-bl-2xl border border-gray-100 relative group-hover/reaction:bg-indigo-50/50 group-hover/reaction:border-indigo-100 transition-colors">
                                <div class="flex justify-between items-center mb-1">
                                    <p class="text-xs font-bold text-indigo-900">{{ $reaction->bot_name }}</p>
                                </div>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    {!! nl2br(e($reaction->response_text)) !!}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 4. エラー表示 --}}
            @if ($post->status === 'failed')
                <div class="ml-4 mt-4 p-3 bg-red-50 text-red-600 text-sm rounded-xl border border-red-100 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    AIの処理中にエラーが発生しました。
                </div>
            @endif
        </div>
    @endforeach

    {{-- 投稿がない場合 --}}
    @if ($posts->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
            <div class="text-gray-300 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <p class="text-gray-500 text-lg font-medium">
                @if ($isFiltered)
                    この日の日記はありません。
                @else
                    まだ日記がありません。<br>
                    <span class="text-sm text-gray-400">最初の投稿をして、AIたちと話してみましょう！</span>
                @endif
            </p>
        </div>
    @endif
</div>