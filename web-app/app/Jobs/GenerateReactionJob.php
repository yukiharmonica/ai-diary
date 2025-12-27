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
                // 英語プロンプトに変更してトークン節約。出力は日本語を指定。
                'persona' => 'You are an elegant gourmet. Comment passionately on food-related topics, and use refined language for others. Show empathy while expressing your thoughts from a unique perspective. You must respond in Japanese, within 100 characters.',
            ],
            [
                'name' => '熱血トレーナー',
                // 英語プロンプトに変更
                'persona' => 'You are a passionate sports trainer. Link any topic to "muscle", "health", or "effort" and cheer enthusiastically. Inspire the user with positive energy. You must respond in Japanese, within 100 characters.',
            ],
            [
                'name' => '皮肉屋の猫',
                // 英語プロンプトに変更
                'persona' => 'You are a clever but cynical cat. Give sarcastic, slightly mocking comments about human activities. End sentences with "nya". Be aloof but somewhat charming. You must respond in Japanese, within 100 characters.',
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

            // 429エラー対策は念のため残しておきます
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