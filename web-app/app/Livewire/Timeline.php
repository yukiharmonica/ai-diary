<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class Timeline extends Component
{
    public ?string $targetDate = null;
    
    // 【追加】ジャンル絞り込み用プロパティ
    public ?string $selectedGenre = null;
    public array $genres = ['日常', 'グルメ', '運動', '読書', '仕事'];

    protected $listeners = [
        'refreshTimeline' => '$refresh',
        'date-selected' => 'setTargetDate',
    ];

    public function setTargetDate($date)
    {
        $this->targetDate = $date;
    }

    public function clearFilter()
    {
        $this->targetDate = null;
        // ジャンル選択は保持したまま日付だけ解除したい場合はこのまま
        // ジャンルもリセットしたい場合は $this->selectedGenre = null; を追加
        $this->dispatch('date-selected', date: null);
    }

    /**
     * 【追加】ジャンルを選択する処理
     */
    public function selectGenre($genre)
    {
        $this->selectedGenre = $genre === 'null' ? null : $genre;
    }

    public function render()
    {
        $query = Auth::user()
            ->posts()
            ->with('reactions')
            ->latest();

        // 日付絞り込み
        if ($this->targetDate) {
            $query->whereDate('created_at', $this->targetDate);
        }

        // 【追加】ジャンル絞り込み
        if ($this->selectedGenre) {
            $query->where('genre', $this->selectedGenre);
        }

        $posts = $query->get();

        return view('livewire.timeline', [
            'posts' => $posts,
            'isFiltered' => !is_null($this->targetDate),
            'displayDate' => $this->targetDate ? Carbon::parse($this->targetDate)->isoFormat('Y年M月D日(ddd)') : null,
        ]);
    }
}