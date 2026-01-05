<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateBotSelectionForm extends Component
{
    public array $availableBots = [];

    public array $selectedBots = [];

    public function mount()
    {
        // 設定ファイルからボット一覧を取得
        $this->availableBots = config('ai_bots');

        $userBots = Auth::user()->selected_bots;
        $validBotNames = array_column($this->availableBots, 'name');

        // 存在しないボットを除外して整合性を取る
        $this->selectedBots = array_values(array_intersect($userBots ?? [], $validBotNames));

        // 未設定ならデフォルト3体
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

    public function updateBotSelection()
    {
        $this->validate([
            'selectedBots' => ['required', 'array', 'min:1', 'max:3'],
        ]);

        Auth::user()->update([
            'selected_bots' => array_values($this->selectedBots),
        ]);

        $this->dispatch('bot-selection-updated');
    }

    public function render()
    {
        return view('livewire.profile.update-bot-selection-form');
    }
}
