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

    public int $tries = 5;

    public function __construct(
        public Post $post
    ) {}

    public function handle(GeminiService $gemini): void
    {
        $this->post->update(['status' => 'processing']);

        $bots = [
            [
                'name' => '美食家ボット',
                'persona' => 'あなたは優雅な美食家です。食事に関する話題には情熱的に、それ以外でも上品な言葉遣いでコメントしてください。共感を示しつつ、独自の視点で感想を述べてください。必ず日本語で、100文字以内で答えてください。',
            ],
            [
                'name' => '熱血トレーナー',
                'persona' => 'あなたは熱血スポーツトレーナーです。どんな話題でも「筋肉」「健康」「努力」に結びつけて暑苦しく応援してください。ポジティブなエネルギーでユーザーを鼓舞してください。必ず日本語で、100文字以内で答えてください。',
            ],
            [
                'name' => '皮肉屋の猫',
                'persona' => 'あなたは賢いが冷笑的な猫です。人間の営みを少し馬鹿にしたような、皮肉っぽいコメントを返してください。語尾は「ニャ」。媚びず、しかしどこか愛嬌のある視点でコメントしてください。必ず日本語で、100文字以内で答えてください。',
            ],
        ];

        try {
            foreach ($bots as $bot) {
                // 【変更】有料枠であれば制限が緩いため、待機時間を1秒に短縮して高速化
                sleep(1);

                $responseText = $gemini->generateReaction($bot['persona'], $this->post->text);

                if ($responseText) {
                    $this->post->reactions()->updateOrCreate(
                        [
                            'bot_name' => $bot['name'],
                        ],
                        [
                            'bot_persona_id' => 1,
                            'response_text' => trim($responseText),
                        ]
                    );
                }
            }

            $this->post->update(['status' => 'completed']);

        } catch (\Exception $e) {
            Log::error('GenerateReactionJob Error: '.$e->getMessage());

            // 429エラー対策
            if ($e->getCode() === 429) {
                if (preg_match('/Please retry in ([0-9.]+)s/', $e->getMessage(), $matches)) {
                    $delay = (int) ceil((float) $matches[1]) + 5;
                } else {
                    $delay = 60;
                }

                Log::warning("Rate limit hit. Releasing job for {$delay} seconds...");
                $this->release($delay);

                return;
            }

            $this->post->update(['status' => 'failed']);
            throw $e;
        }
    }
}
