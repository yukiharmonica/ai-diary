<div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 relative">
    {{-- ローディングオーバーレイ --}}
    <div wire:loading wire:target="prevMonth,nextMonth,selectDate" class="absolute inset-0 bg-white/60 z-20 flex items-center justify-center rounded-2xl">
        <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- ヘッダー：月移動 --}}
    <div class="flex items-center justify-between mb-6 px-1">
        <button 
            wire:click="prevMonth" 
            wire:loading.attr="disabled"
            class="p-2 hover:bg-gray-100 rounded-full text-gray-400 hover:text-indigo-600 transition duration-200 disabled:opacity-50"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <h3 class="flex items-baseline gap-2 text-gray-800">
            <span class="text-2xl font-extrabold text-indigo-600 tracking-tight">{{ $currentDate->format('n') }}月</span>
            <span class="text-sm font-medium text-gray-400">{{ $currentDate->format('Y') }}</span>
        </h3>
        <button 
            wire:click="nextMonth" 
            wire:loading.attr="disabled"
            class="p-2 hover:bg-gray-100 rounded-full text-gray-400 hover:text-indigo-600 transition duration-200 disabled:opacity-50"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>

    {{-- 曜日ヘッダー --}}
    <div class="grid grid-cols-7 gap-1 mb-2 text-center">
        @foreach(['日', '月', '火', '水', '木', '金', '土'] as $index => $day)
            <div class="text-xs font-bold py-2 {{ $index === 0 ? 'text-red-400' : ($index === 6 ? 'text-blue-400' : 'text-gray-400') }}">
                {{ $day }}
            </div>
        @endforeach
    </div>

    {{-- 日付グリッド --}}
    <div class="grid grid-cols-7 gap-1 md:gap-2">
        @foreach ($calendar as $date)
            @if ($date)
                @php
                    $isPosted = in_array($date, $postedDates);
                    $isToday = $date === now()->format('Y-m-d');
                    $isSelected = $date === $selectedDate; 
                    
                    $carbonDate = \Carbon\Carbon::parse($date);
                    $day = $carbonDate->day;
                    $dayOfWeek = $carbonDate->dayOfWeek; // 0 (Sun) - 6 (Sat)

                    // 【追加】未来の日付かどうか判定
                    $isFuture = $date > now()->format('Y-m-d');

                    // 文字色の決定
                    $textColor = 'text-gray-600';
                    if ($isFuture) {
                        $textColor = 'text-gray-300 cursor-not-allowed'; // 未来はグレーアウト
                    } elseif ($isToday) {
                        $textColor = 'text-indigo-900'; 
                    } elseif ($dayOfWeek === 0) {
                        $textColor = 'text-red-500';
                    } elseif ($dayOfWeek === 6) {
                        $textColor = 'text-blue-500';
                    }
                @endphp
                <button 
                    {{-- 未来ならクリック無効化 --}}
                    @if(!$isFuture) wire:click="selectDate('{{ $date }}')" @endif
                    @if($isFuture) disabled @endif
                    class="relative aspect-square flex flex-col items-center justify-center rounded-xl transition-all duration-200 group
                        {{-- 状態によるスタイル分岐 --}}
                        @if($isSelected)
                            bg-indigo-600 text-white shadow-md scale-105 z-10
                            @if($isToday) ring-2 ring-offset-2 ring-orange-400 @endif
                        @elseif($isToday)
                            bg-white {{ $textColor }} font-extrabold border-2 border-orange-400 z-10
                        @else
                            {{ $textColor }} 
                            @if(!$isFuture) hover:bg-indigo-50 hover:text-indigo-700 @endif
                        @endif
                    "
                >
                    <span class="text-sm {{ $isToday || $isSelected ? 'font-bold' : '' }}">{{ $day }}</span>
                    
                    {{-- 投稿がある日のマーカー --}}
                    @if ($isPosted)
                        <div class="absolute bottom-2 w-1 h-1 rounded-full {{ $isSelected ? 'bg-white/90' : 'bg-indigo-400 group-hover:bg-indigo-500' }}"></div>
                    @endif
                </button>
            @else
                <div class="aspect-square"></div> {{-- 空白セル --}}
            @endif
        @endforeach
    </div>
</div>