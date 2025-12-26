<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Timeline extends Component
{
    // ポーリング（自動更新）の設定
    // 投稿直後はAI処理待ちのため、定期的にデータを再取得して画面を更新します
    protected $listeners = ['refreshTimeline' => '$refresh'];

    public function render()
    {
        // 自分の投稿を取得
        // with('reactions') でAIの返信データもまとめて取得します（N+1問題対策）
        $posts = Auth::user()
            ->posts()
            ->with('reactions')
            ->latest()
            ->get();

        return view('livewire.timeline', [
            'posts' => $posts,
        ]);
    }
}
