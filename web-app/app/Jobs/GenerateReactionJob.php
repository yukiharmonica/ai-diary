<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\GeminiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateReactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ジョブが失敗した場合の最大試行回数
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GeminiService $gemini): void
    {
        // 1. 処理開始：ステータスを 'processing' に更新
        $this->post->update(['status' => 'processing']);

        // 2. ボットの定義
        // 本来は設定ファイルやDBから取得すべきですが、MVPではここに直接定義します
        $bots = [
            [
                'name' => '美食家ボット',
                'persona' => 'あなたは優雅な美食家です。食事に関する話題には情熱的に、それ以外でも上品な言葉遣いでコメントしてください。必ず日本語で、100文字以内で答えてください。',
            ],
            [
                'name' => '熱血トレーナー',
                'persona' => 'あなたは熱血スポーツトレーナーです。どんな話題でも「筋肉」「健康」「努力」に結びつけて暑苦しく応援してください。必ず日本語で、100文字以内で答えてください。',
            ],
            [
                'name' => '皮肉屋の猫',
                'persona' => 'あなたは賢いが冷笑的な猫です。人間の営みを少し馬鹿にしたような、皮肉っぽいコメントを短く返してください。語尾は「ニャ」。必ず日本語で、100文字以内で答えてください。',
            ],
        ];

        try {
            foreach ($bots as $bot) {
                // 3. GeminiServiceを使ってAIの返信を生成
                $responseText = $gemini->generateReaction($bot['persona'], $this->post->text);

                if ($responseText) {
                    // 4. 生成されたリアクションをDBに保存
                    $this->post->reactions()->create([
                        'bot_name' => $bot['name'],
                        'bot_persona_id' => 1, // 将来的にID管理するために仮置き
                        'response_text' => trim($responseText),
                    ]);
                } else {
                    Log::warning("Gemini API returned empty response for bot: {$bot['name']}");
                }
            }

            // 5. 全ての処理が完了したらステータスを 'completed' に更新
            $this->post->update(['status' => 'completed']);

        } catch (\Exception $e) {
            // エラーハンドリング
            Log::error('GenerateReactionJob Failed: '.$e->getMessage());

            // ステータスを 'failed' にして、何が起きたかわかるようにする
            $this->post->update(['status' => 'failed']);

            // 例外を再スローして、Laravelにジョブ失敗を通知（リトライさせるため）
            throw $e;
        }
    }
}
