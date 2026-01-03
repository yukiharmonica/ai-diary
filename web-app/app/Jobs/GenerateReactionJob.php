<?php

namespace App\Jobs;

use App\Events\ReactionGenerated;
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

        // 1. 全ボット定義を取得
        $allBots = config('ai_bots', []);

        // 2. ユーザーが選択しているボット名リストを取得
        $selectedBotNames = $this->post->user->selected_bots;

        // 未設定（nullや空）の場合は、設定ファイルの最初の3体をデフォルトとする
        if (empty($selectedBotNames)) {
            $selectedBotNames = collect($allBots)->take(3)->pluck('name')->toArray();
        }

        // 3. 実行するボットをフィルタリング
        $targetBots = array_filter($allBots, function ($bot) use ($selectedBotNames) {
            return in_array($bot['name'], $selectedBotNames);
        });

        try {
            foreach ($targetBots as $bot) {
                sleep(1);

                $responseText = $gemini->generateReaction($bot['system_prompt'], $this->post->text);

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

                    ReactionGenerated::dispatch($this->post);
                }
            }

            $this->post->update(['status' => 'completed']);
            ReactionGenerated::dispatch($this->post);

        } catch (\Exception $e) {
            Log::error('GenerateReactionJob Error: '.$e->getMessage());

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
