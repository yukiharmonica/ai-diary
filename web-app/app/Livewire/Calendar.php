<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Calendar extends Component
{
    public string $currentMonth; // 表示中の月 (Y-m)
    public ?string $selectedDate = null; // 【追加】選択中の日付

    public function mount()
    {
        $this->currentMonth = now()->format('Y-m');
    }

    public function prevMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->subMonth()->format('Y-m');
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth)->addMonth()->format('Y-m');
    }

    /**
     * 日付をクリックしたときの処理
     */
    public function selectDate($date)
    {
        // 【追加】選択状態を更新
        // すでに選択されている日をクリックした場合は解除（トグル）するならここを調整
        if ($this->selectedDate === $date) {
            $this->selectedDate = null;
            $this->dispatch('dateSelected', date: null); // フィルタ解除
        } else {
            $this->selectedDate = $date;
            $this->dispatch('dateSelected', date: $date);
        }
    }

    public function render()
    {
        $date = Carbon::parse($this->currentMonth);
        $daysInMonth = $date->daysInMonth;
        $firstDayOfWeek = $date->copy()->startOfMonth()->dayOfWeek;

        $calendar = [];
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendar[] = null;
        }
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $calendar[] = $date->copy()->startOfMonth()->addDays($day - 1)->format('Y-m-d');
        }

        $postedDates = Auth::user()->posts()
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->pluck('created_at')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->unique()
            ->toArray();

        return view('livewire.calendar', [
            'calendar' => $calendar,
            'currentDate' => $date,
            'postedDates' => $postedDates,
        ]);
    }
}