<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. テスト用ユーザーの作成
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テストユーザー',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                // 全てのボットを選択した状態にしておく（デモ用）
                'selected_bots' => [
                    '美食家 ル・コント・ド・サヴール',
                    '熱血トレーナー キャプテン・マッスル',
                    '皮肉屋の猫 バロン'
                ],
            ]
        );

        // 2. ポートフォリオ用ダミーデータの定義
        $datasets = [
            [
                'genre' => 'グルメ',
                'text' => '駅前の新しいイタリアンでランチ。カルボナーラが濃厚で最高だった！卵とチーズのバランスが絶妙。食後のティラミスもほろ苦くて大人の味。また行きたいな。',
                'created_at' => Carbon::now()->subHours(3),
                'reactions' => [
                    [
                        'bot_name' => '美食家 ル・コント・ド・サヴール',
                        'text' => 'ボーノ！濃厚なカルボナーラとは、まさに至福の時ですね。卵とチーズの黄金比率を感じ取れましたか？ティラミスでの締めくくりも完璧です。素晴らしい食体験に乾杯！'
                    ],
                    [
                        'bot_name' => '熱血トレーナー キャプテン・マッスル',
                        'text' => 'カーボローディング完了だな！そのエネルギーを無駄にするなよ！食べた分だけスクワットだ！明日はそのカロリーを筋肉に変えるためにジムへGO！'
                    ]
                ]
            ],
            [
                'genre' => '運動',
                'text' => '今日は久しぶりに5km走った。足がパンパンだけど、心地よい疲れ。風が気持ちよくて、悩み事も少し吹き飛んだ気がする。継続は力なり、頑張ろう。',
                'created_at' => Carbon::now()->subDay()->hour(19),
                'reactions' => [
                    [
                        'bot_name' => '熱血トレーナー キャプテン・マッスル',
                        'text' => 'ナイスファイト！！その痛みは成長痛だ！筋肉が喜んでいる証拠だぞ！プロテインを飲んで、超回復を待て！君ならフルマラソンも夢じゃない！'
                    ],
                    [
                        'bot_name' => '効率マニア ロジカル・タナカ',
                        'text' => '5kmの有酸素運動、素晴らしい成果です。しかし「足がパンパン」はケア不足のサイン。フォームローラーでの筋膜リリースを即時実行し、翌日のパフォーマンス低下を防ぐのが合理的です。'
                    ]
                ]
            ],
            [
                'genre' => '仕事',
                'text' => 'プレゼン資料の作成が終わらない...。上司からのフィードバックが細かすぎて心が折れそう。でも、これを乗り越えれば昇進のチャンスかもしれない。やるしかない。',
                'created_at' => Carbon::now()->subDays(2)->hour(22),
                'reactions' => [
                    [
                        'bot_name' => '皮肉屋の猫 バロン',
                        'text' => 'ふーん、人間界の上下関係って大変だニャ。でも、怒ってる上司の顔を猫だと思えば笑えるかもよ？ カリカリでも食べて元気出すニャ。無理しすぎは禁物だニャ。'
                    ],
                    [
                        'bot_name' => '心のカウンセラー マリア',
                        'text' => '責任感が強くて素晴らしいですね。でも、心が折れそうな時は無理に頑張らなくていいんですよ。少し休憩して、温かい飲み物でもいかがですか？あなたの努力はきっと誰かが見ています。'
                    ]
                ]
            ],
             [
                'genre' => '日常',
                'text' => '朝起きたら雨が降っていた。気分が少し落ち込むけど、お気に入りの傘を使えるから良しとしよう。水たまりを避けて歩くのも、ちょっとしたゲームみたいで楽しいかも。',
                'created_at' => Carbon::now()->subDays(3)->hour(8),
                'reactions' => [
                    [
                        'bot_name' => '心のカウンセラー マリア',
                        'text' => '雨の日は心も静かになりますね。お気に入りのアイテムで気分を上げる工夫、とても素敵です。視点を変えるだけで、世界は違って見えますよ。今日も穏やかな一日になりますように。'
                    ],
                    [
                        'bot_name' => '皮肉屋の猫 バロン',
                        'text' => '雨なんて濡れるだけニャ。人間は道具を使って大変だね。ボクなら一日中寝てるけどニャ。まあ、水たまりで転ばないように気をつけるんだニャ。'
                    ]
                ]
            ],
            [
                'genre' => '趣味',
                'text' => 'ずっと見たかったSF映画を観た。映像美に圧倒されたし、ストーリーの伏線回収も見事だった。やっぱり映画館の大画面で観るのが一番だね。余韻に浸りながらパンフレットを熟読中。',
                'created_at' => Carbon::now()->subDays(5)->hour(15),
                'reactions' => [
                    [
                        'bot_name' => '美食家 ル・コント・ド・サヴール',
                        'text' => '映画という名のフルコースを堪能されたようですな。映像美は前菜、ストーリーはメインディッシュ。その余韻は食後の極上のワインのよう。芸術は心の栄養でございます。'
                    ],
                    [
                        'bot_name' => '効率マニア ロジカル・タナカ',
                        'text' => '映画鑑賞によるリフレッシュ効果は生産性向上に寄与します。パンフレットの熟読も情報の定着に有効。次は感想をアウトプットして、言語化能力を鍛えるのが最も効率的な時間の使い方です。'
                    ]
                ]
            ],
            [
                'genre' => 'その他',
                'text' => '部屋の掃除をした。ずっと探していた失くし物が出てきてラッキー！部屋が綺麗になると頭の中もスッキリする気がする。断捨離も進めて、もっとシンプルな生活を目指したい。',
                'created_at' => Carbon::now()->subDays(8)->hour(10),
                'reactions' => [
                    [
                        'bot_name' => '効率マニア ロジカル・タナカ',
                        'text' => '整理整頓はタスク処理能力を20%向上させるというデータもあります。失くし物の発見は、探索コストの削減成功です。次はモノの定位置を決め、探す時間をゼロにしましょう。'
                    ],
                     [
                        'bot_name' => '熱血トレーナー キャプテン・マッスル',
                        'text' => '掃除も立派な有酸素運動だ！！スクワットしながら拭き掃除をすれば一石二鳥！部屋も体もシェイプアップだ！その調子で脂肪も断捨離しろォォ！！'
                    ]
                ]
            ]
        ];

        // 3. データの投入
        foreach ($datasets as $data) {
            $post = Post::create([
                'user_id' => $user->id,
                'genre' => $data['genre'],
                'text' => $data['text'],
                'status' => 'completed',
                'created_at' => $data['created_at'],
                'updated_at' => $data['created_at'],
            ]);

            foreach ($data['reactions'] as $reaction) {
                // ボット名からIDを簡易的にマッピング（設定ファイルと一致させるための仮ID）
                $personaId = match($reaction['bot_name']) {
                    '美食家 ル・コント・ド・サヴール' => 1,
                    '熱血トレーナー キャプテン・マッスル' => 2,
                    '皮肉屋の猫 バロン' => 3,
                    '心のカウンセラー マリア' => 4,
                    '効率マニア ロジカル・タナカ' => 5,
                    default => 0,
                };

                Reaction::create([
                    'post_id' => $post->id,
                    'bot_name' => $reaction['bot_name'],
                    'bot_persona_id' => $personaId,
                    'response_text' => $reaction['text'],
                    'created_at' => $data['created_at']->copy()->addSeconds(30), // 投稿の30秒後に設定
                    'updated_at' => $data['created_at']->copy()->addSeconds(30),
                ]);
            }
        }
    }
}