{{-- wire:poll.5s で5秒ごとに自動更新（AIの完了を検知するため） --}}
<div class="space-y-6" wire:poll.5s>
    @foreach ($posts as $post)
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 {{ $post->status === 'completed' ? 'border-indigo-500' : 'border-gray-300' }}">
            
            {{-- 1. 投稿内容 --}}
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span class="inline-block px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full mb-2">
                        {{ $post->genre }}
                    </span>
                    <p class="text-gray-800 text-lg whitespace-pre-wrap">{{ $post->text }}</p>
                </div>
                <span class="text-xs text-gray-500">
                    {{ $post->created_at->diffForHumans() }}
                </span>
            </div>

            {{-- 2. AI処理中の表示 --}}
            @if ($post->status === 'pending' || $post->status === 'processing')
                <div class="flex items-center text-sm text-gray-500 animate-pulse mt-4 p-3 bg-gray-50 rounded">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>AIボットたちが返信を考えています...</span>
                </div>
            @endif

            {{-- 3. AIからの返信一覧 --}}
            @if ($post->reactions->isNotEmpty())
                <div class="mt-4 space-y-4 pt-4 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">AI Reactions</h4>
                    
                    @foreach ($post->reactions as $reaction)
                        <div class="flex items-start bg-indigo-50 p-3 rounded-lg">
                            <div class="flex-shrink-0 mr-3">
                                {{-- ボットのアイコン（頭文字） --}}
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-indigo-600 font-bold text-lg shadow-sm border border-indigo-100">
                                    {{ mb_substr($reaction->bot_name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-indigo-900">{{ $reaction->bot_name }}</p>
                                <p class="text-gray-700 mt-1 leading-relaxed text-sm">
                                    {!! nl2br(e($reaction->response_text)) !!}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 4. エラー表示 --}}
            @if ($post->status === 'failed')
                <div class="mt-4 p-3 bg-red-50 text-red-600 text-sm rounded">
                    AIの処理中にエラーが発生しました。
                </div>
            @endif
        </div>
    @endforeach

    {{-- 投稿がない場合 --}}
    @if ($posts->isEmpty())
        <div class="text-center text-gray-500 py-10">
            まだ日記がありません。<br>
            上のフォームから最初の投稿をしてみましょう！
        </div>
    @endif
</div>