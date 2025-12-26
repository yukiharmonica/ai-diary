<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. テスト用ユーザーの作成
        // ログイン情報: email=test@example.com / password=password
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テストユーザー',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. ダミー投稿とリアクションの作成

        // --- ケースA: グルメ投稿 ---
        $post1 = Post::create([
            'user_id' => $user->id,
            'genre' => 'グルメ',
            'text' => '駅前の新しいイタリアンでランチ。カルボナーラが濃厚で最高だった！食後のティラミスも絶品。また行きたいな。',
            'status' => 'completed',
            'created_at' => now()->subHours(2),
        ]);

        Reaction::create([
            'post_id' => $post1->id,
            'bot_name' => '美食家ボット',
            'bot_persona_id' => 1,
            'response_text' => 'ボーノ！濃厚なカルボナーラとは、まさに至福の時ですね。卵とチーズの黄金比率を感じ取れましたか？ティラミスでの締めくくりも完璧です。素晴らしい食体験に乾杯！',
        ]);

        Reaction::create([
            'post_id' => $post1->id,
            'bot_name' => '熱血トレーナー',
            'bot_persona_id' => 2,
            'response_text' => 'カーボローディング完了だな！そのエネルギーを無駄にするなよ！食べた分だけスクワットだ！明日はそのカロリーを筋肉に変えるためにジムへGO！',
        ]);

        // --- ケースB: 運動投稿 ---
        $post2 = Post::create([
            'user_id' => $user->id,
            'genre' => '運動',
            'text' => '今日は久しぶりに5km走った。足がパンパンだけど、心地よい疲れ。明日は筋肉痛確定かも。',
            'status' => 'completed',
            'created_at' => now()->subDay(),
        ]);

        Reaction::create([
            'post_id' => $post2->id,
            'bot_name' => '熱血トレーナー',
            'bot_persona_id' => 2,
            'response_text' => 'ナイスファイト！！その痛みは成長痛だ！筋肉が喜んでいる証拠だぞ！プロテインを飲んで、超回復を待て！君ならフルマラソンも夢じゃない！',
        ]);

        Reaction::create([
            'post_id' => $post2->id,
            'bot_name' => '皮肉屋の猫',
            'bot_persona_id' => 3,
            'response_text' => '人間って不思議だニャ。わざわざ自分を痛めつけて喜ぶなんて。こたつで丸まってる方がよっぽど幸せだと思うけどニャ。まあ、お大事に。',
        ]);

        // --- ケースC: 悩み投稿 ---
        $post3 = Post::create([
            'user_id' => $user->id,
            'genre' => '仕事',
            'text' => '上司に理不尽なことで怒られた。やる気が出ない...',
            'status' => 'completed',
            'created_at' => now()->subDays(2),
        ]);

        Reaction::create([
            'post_id' => $post3->id,
            'bot_name' => '皮肉屋の猫',
            'bot_persona_id' => 3,
            'response_text' => 'ふーん、人間界の上下関係って大変だニャ。でも、怒ってる上司の顔を猫だと思えば笑えるかもよ？ カリカリでも食べて元気出すニャ。',
        ]);
    }
}
