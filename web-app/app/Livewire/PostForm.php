<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostForm extends Component
{
    // フォームの入力値とバインディングするプロパティ
    public string $text = '';

    public string $genre = '日常'; // デフォルト値

    // バリデーションルール
    protected $rules = [
        'text' => 'required|string|max:400',
        'genre' => 'required|string',
    ];

    // 投稿処理
    public function save()
    {
        // バリデーション実行
        $this->validate();

        // 投稿を保存 (Userモデルのリレーション経由)
        Auth::user()->posts()->create([
            'text' => $this->text,
            'genre' => $this->genre,
            'status' => 'pending', // 初期状態はAI処理待ち
        ]);

        // フォームをリセット
        $this->reset('text');

        // 完了メッセージを表示
        session()->flash('message', '日記を投稿しました！AIの反応を待っています...');

        // TODO: 次のステップでAIジョブのディスパッチ処理を追加します
    }

    public function render()
    {
        return view('livewire.post-form');
    }
}
