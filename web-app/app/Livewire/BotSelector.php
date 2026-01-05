<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BotSelector extends Component
{
    public array $availableBots = [];

    public array $selectedBots = [];

    public function mount()
    {
        $this->availableBots = config('ai_bots');

        // 設定ファイルに存在するボット名のリストを作成
        $validBotNames = array_column($this->availableBots, 'name');

        // ユーザーの選択データを取得
        $userBots = Auth::user()->selected_bots ?? [];

        // 存在しないボット（古いデータ）を除外して整合性を取る
        $this->selectedBots = array_values(array_intersect($userBots, $validBotNames));

        // もし有効なボットが1体もなければ、設定ファイルの最初の3体をデフォルトとして選択状態にする
        if (empty($this->selectedBots)) {
            $this->selectedBots = collect($this->availableBots)
                ->take(3)
                ->pluck('name')
                ->values()
                ->toArray();
        }
    }

    /**
     * ボットの選択状態を切り替える
     */
    public function toggleBot($botName)
    {
        $selected = collect($this->selectedBots);

        if ($selected->contains($botName)) {
            // 解除（最低1体）
            if ($selected->count() > 1) {
                $this->selectedBots = $selected
                    ->reject(fn ($name) => $name === $botName)
                    ->values()
                    ->all();
            } else {
                session()->flash('error', 'チームには最低1体必要です。');
            }
        } else {
            // 追加（最大3体）
            if ($selected->count() < 3) {
                $this->selectedBots = $selected
                    ->push($botName)
                    ->values()
                    ->all();
            } else {
                session()->flash('error', '選択できるのは最大3体までです。どれか外してください。');
            }
        }
    }

    public function save()
    {
        $this->validate([
            'selectedBots' => ['required', 'array', 'min:1', 'max:3'],
        ]);

        Auth::user()->update([
            'selected_bots' => array_values($this->selectedBots),
        ]);

        session()->flash('message', 'AIチームを更新しました！');

        $this->dispatch('bot-selection-updated');
    }

    public function render()
    {
        return view('livewire.bot-selector');
    }
}
