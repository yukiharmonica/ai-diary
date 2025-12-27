<?php

namespace App\Livewire;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Timeline extends Component
{
    public ?string $targetDate = null;

    public ?string $selectedGenre = null;

    public int $userId; // 【追加】ユーザーIDプロパティ

    public function mount()
    {
        $this->userId = Auth::id(); // 【追加】初期化時にIDをセット
    }

    // 動的リスナー定義
    public function getListeners()
    {
        return [
            'refreshTimeline' => '$refresh',
            'date-selected' => 'setTargetDate',
            'post-created' => '$refresh',

            // 【修正】イベント名の先頭にドット(.)を付け、broadcastAsで定義した名前を指定
            "echo-private:users.{$this->userId},.reaction.generated" => '$refresh',
        ];
    }

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
            'isFiltered' => ! is_null($this->targetDate),
            'displayDate' => $this->targetDate ? Carbon::parse($this->targetDate)->isoFormat('Y年M月D日(ddd)') : null,
            'genres' => array_keys(Post::GENRES),
        ]);
    }
}
