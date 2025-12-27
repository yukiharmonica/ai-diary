<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class Timeline extends Component
{
    public ?string $targetDate = null;
    public ?string $selectedGenre = null;

    protected $listeners = [
        'refreshTimeline' => '$refresh',
        'date-selected' => 'setTargetDate',
        'post-created' => '$refresh', // 【追加】投稿完了時にリストを即座に更新
    ];

    public function setTargetDate($date)
    {
        $this->targetDate = $date;
    }

    public function clearFilter()
    {
        $this->targetDate = null;
        $this->dispatch('date-selected', date: null);
    }

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

        if ($this->targetDate) {
            $query->whereDate('created_at', $this->targetDate);
        }

        if ($this->selectedGenre) {
            $query->where('genre', $this->selectedGenre);
        }

        $posts = $query->get();

        return view('livewire.timeline', [
            'posts' => $posts,
            'isFiltered' => !is_null($this->targetDate),
            'displayDate' => $this->targetDate ? Carbon::parse($this->targetDate)->isoFormat('Y年M月D日(ddd)') : null,
            'genres' => array_keys(Post::GENRES),
        ]);
    }
}