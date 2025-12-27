<?php

namespace App\Livewire;

use App\Models\Post; // ジャンル定数を利用するために追加
use App\Services\PostService; // ビジネスロジックを委譲するために追加
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PostForm extends Component
{
    // フォームの入力値とバインディングするプロパティ
    public string $text = '';
    public string $genre = '日常'; // デフォルト値

    /**
     * @var array<string, string>
     */
    protected $rules = [
        'text' => 'required|string|max:400',
        'genre' => 'required|string',
    ];

    // 投稿処理
    // PostServiceをメソッドインジェクションで受け取る
    public function save(PostService $postService)
    {
        // バリデーション実行
        $this->validate();

        try {
            // ビジネスロジック（保存、制限チェック、ジョブ登録）はサービスに委譲
            $postService->createPost(Auth::user(), $this->text, $this->genre);

            // フォームをリセット
            $this->reset('text');

            // 完了メッセージを表示
            session()->flash('message', '日記を投稿しました！AIの反応を待っています...');

            // タイムライン更新などのイベントを発火
            $this->dispatch('post-created');

        } catch (\Exception $e) {
            // 投稿制限などのエラーが発生した場合
            $this->addError('text', $e->getMessage());
        }
    }

    public function render()
    {
        // モデルで定義したジャンル一覧をビューに渡す
        return view('livewire.post-form', [
            'genres' => Post::GENRES,
        ]);
    }
}