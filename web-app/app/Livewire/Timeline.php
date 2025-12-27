<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class Timeline extends Component
{
    // フィルタリングする日付（nullの場合は全件表示）
    public ?string $targetDate = null;

    // イベントリスナーの登録
    // 'refreshTimeline' -> 自動更新用
    // 'dateSelected' -> カレンダーで日付が選ばれたときに実行
    protected $listeners = [
        'refreshTimeline' => '$refresh',
        'dateSelected' => 'setTargetDate',
    ];

    /**
     * カレンダーから日付を受け取ってセットする
     */
    public function setTargetDate($date)
    {
        $this->targetDate = $date;
    }

    /**
     * フィルターを解除して全件表示に戻す
     */
    public function clearFilter()
    {
        $this->targetDate = null;
    }

    public function render()
    {
        // クエリの構築
        $query = Auth::user()
            ->posts()
            ->with('reactions')
            ->latest();

        // 日付が指定されていれば絞り込み
        if ($this->targetDate) {
            $query->whereDate('created_at', $this->targetDate);
        }

        $posts = $query->get();

        return view('livewire.timeline', [
            'posts' => $posts,
            'isFiltered' => !is_null($this->targetDate),
            'displayDate' => $this->targetDate ? Carbon::parse($this->targetDate)->isoFormat('Y年M月D日(ddd)') : null,
        ]);
    }
}