<div class="space-y-6" wire:poll.5s>
    {{-- フィルター状態の表示 --}}
    @if ($isFiltered)
        <div class="flex items-center justify-between bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="flex items-center text-blue-800 font-bold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ $displayDate }} の日記
            </div>
            <button wire:click="clearFilter" class="text-sm text-blue-600 hover:text-blue-800 underline">
                全件表示に戻す
            </button>
        </div>
    @endif

    @foreach ($posts as $post)
        {{-- (ここは以前のまま変更なし) --}}
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 {{ $post->status === 'completed' ? 'border-indigo-500' : 'border-gray-300' }}">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="inline-block px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full mb-2">
                        {{ $post->genre }}
                    </span>
                    <p class="text-gray-800 text-lg whitespace-pre-wrap">{{ $post->text }}</p>
                </div>
                <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
            </div>

            @if ($post->status === 'pending' || $post->status === 'processing')
                <div class="flex items-center text-sm text-gray-500 animate-pulse">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    AIが返信を書いています...
                </div>
            @endif

            @if ($post->reactions->isNotEmpty())
                <div class="mt-4 space-y-3 bg-gray-50 p-4 rounded-md">
                    <h4 class="text-sm font-bold text-gray-600 mb-2">AIからのリアクション</h4>
                    @foreach ($post->reactions as $reaction)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                    {{ mb_substr($reaction->bot_name, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-700">{{ $reaction->bot_name }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $reaction->response_text }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
    
    @if ($posts->isEmpty())
        <div class="text-center text-gray-500 py-10">
            @if ($isFiltered)
                この日の日記はありません。
            @else
                まだ日記がありません。最初の投稿をしてみましょう！
            @endif
        </div>
    @endif
</div>