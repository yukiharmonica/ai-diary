<div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
    {{-- ヘッダー：月移動 --}}
    <div class="flex items-center justify-between mb-6 px-1">
        <button wire:click="prevMonth" class="p-2 hover:bg-gray-100 rounded-full text-gray-400 hover:text-indigo-600 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <h3 class="flex items-baseline gap-2 text-gray-800">
            <span class="text-2xl font-extrabold text-indigo-600 tracking-tight">{{ $currentDate->format('n') }}月</span>
            <span class="text-sm font-medium text-gray-400">{{ $currentDate->format('Y') }}</span>
        </h3>
        <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 rounded-full text-gray-400 hover:text-indigo-600 transition duration-200">
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
                    // コンポーネントのプロパティと比較
                    $isSelected = $date === $selectedDate; 
                    $day = \Carbon\Carbon::parse($date)->day;
                @endphp
                <button 
                    wire:click="selectDate('{{ $date }}')"
                    class="relative aspect-square flex flex-col items-center justify-center rounded-xl transition-all duration-200 group
                        {{-- 状態によるスタイル分岐 --}}
                        @if($isSelected)
                            bg-indigo-600 text-white shadow-md scale-105 z-10
                            @if($isToday) ring-2 ring-offset-2 ring-orange-400 @endif
                        @elseif($isToday)
                            bg-white text-indigo-900 font-extrabold border-2 border-orange-400 z-10
                        @else
                            text-gray-600 hover:bg-indigo-50 hover:text-indigo-700
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