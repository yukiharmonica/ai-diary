<?php

namespace App\Services;

use App\Jobs\GenerateReactionJob;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class PostService
{
    /**
     * 新しい日記を投稿する
     *
     * @param User $user 投稿するユーザー
     * @param string $text 日記本文
     * @param string $genre ジャンル
     * @throws Exception 投稿制限などのルール違反時
     */
    public function createPost(User $user, string $text, string $genre): void
    {
        // 1. 投稿制限チェック
        // ユーザーの「今日の投稿数」をカウントします
        $todayPostCount = $user->posts()
            ->whereDate('created_at', today())
            ->count();

        // 制限回数の設定
        // ※仕様では「無料会員: 1回」「プレミアム: 10回」ですが、
        // 現時点では開発テスト用に「10回」としています。
        // 将来的には $user->is_premium ? 10 : 1; のように分岐させます。
        $limit = 10; 

        if ($todayPostCount >= $limit) {
            throw new Exception("本日の投稿回数制限（{$limit}回）に達しました。明日また投稿してください！");
        }

        // 2. データの保存とジョブ登録
        // DB::transactionを使うことで、途中でエラーが起きても
        // 「DBには保存されたけどジョブが登録されていない」という不整合を防ぎます
        DB::transaction(function () use ($user, $text, $genre) {
            // 投稿をデータベースに保存
            $post = $user->posts()->create([
                'text' => $text,
                'genre' => $genre,
                'status' => 'pending', // 初期ステータス: 処理待ち
            ]);

            // AI生成ジョブをRedisキューに登録
            GenerateReactionJob::dispatch($post);
        });
    }
}