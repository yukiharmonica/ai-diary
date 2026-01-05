<div class="calendar-card">
    {{-- ローディングオーバーレイ --}}
    <div wire:loading wire:target="prevMonth,nextMonth,selectDate" class="calendar-loading-overlay">
        <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- ヘッダー：月移動 --}}
    <div class="calendar-header">
        <button wire:click="prevMonth" wire:loading.attr="disabled" class="calendar-nav-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <h3 class="flex items-baseline gap-2 text-gray-800">
            <span class="text-2xl font-extrabold text-indigo-600 tracking-tight">{{ $currentDate->format('n') }}月</span>
            <span class="text-sm font-medium text-gray-400">{{ $currentDate->format('Y') }}</span>
        </h3>
        <button wire:click="nextMonth" wire:loading.attr="disabled" class="calendar-nav-btn">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>

    {{-- 曜日ヘッダー --}}
    <div class="calendar-weekday-header">
        @foreach(['日', '月', '火', '水', '木', '金', '土'] as $index => $day)
            <div class="calendar-weekday {{ $index === 0 ? 'text-red-400' : ($index === 6 ? 'text-blue-400' : 'text-gray-400') }}">
                {{ $day }}
            </div>
        @endforeach
    </div>

    {{-- 日付グリッド --}}
    <div class="calendar-grid">
        @foreach ($calendar as $date)
            @if ($date)
                @php
                    $isPosted = in_array($date, $postedDates);
                    $isToday = $date === now()->format('Y-m-d');
                    $isSelected = $date === $selectedDate; 
                    
                    $carbonDate = \Carbon\Carbon::parse($date);
                    $day = $carbonDate->day;
                    $dayOfWeek = $carbonDate->dayOfWeek; // 0 (Sun) - 6 (Sat)
                    $isFuture = $date > now()->format('Y-m-d');

                    // 文字色の決定
                    $textColor = 'text-gray-600';
                    if ($isFuture) {
                        $textColor = 'text-gray-300';
                    } elseif ($isToday) {
                        $textColor = 'text-indigo-900'; 
                    } elseif ($dayOfWeek === 0) {
                        $textColor = 'text-red-500';
                    } elseif ($dayOfWeek === 6) {
                        $textColor = 'text-blue-500';
                    }
                @endphp
                <button 
                    @if(!$isFuture) wire:click="selectDate('{{ $date }}')" @endif
                    @if($isFuture) disabled @endif
                    {{-- 【修正】classに group を追加 --}}
                    class="calendar-day-btn group
                        @if($isSelected)
                            day-selected @if($isToday) day-today-selected-ring @endif
                        @elseif($isToday)
                            day-today-active {{ $textColor }}
                        @elseif($isFuture)
                            day-future
                        @else
                            day-default {{ $textColor }}
                        @endif
                    "
                >
                    <span class="text-sm {{ $isToday || $isSelected ? 'font-bold' : '' }}">{{ $day }}</span>
                    
                    {{-- 投稿マーカー --}}
                    @if ($isPosted)
                        <div class="day-marker {{ $isSelected ? 'bg-white/90' : 'bg-indigo-400 group-hover:bg-indigo-500' }}"></div>
                    @endif
                </button>
            @else
                <div class="aspect-square"></div> {{-- 空白セル --}}
            @endif
        @endforeach
    </div>
</div>