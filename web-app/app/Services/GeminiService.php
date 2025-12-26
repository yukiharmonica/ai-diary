<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;

    // 最も汎用的で安定したモデル名 'gemini-1.5-flash'
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * AIボットとしてのリアクションを生成する
     *
     * @param  string  $systemPrompt  ボットのペルソナ（性格）
     * @param  string  $userPost  ユーザーの投稿内容
     * @return string|null 生成されたテキスト（失敗時はnull）
     */
    public function generateReaction(string $systemPrompt, string $userPost): ?string
    {
        $url = "{$this->baseUrl}?key={$this->apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        // 【変更】指示文を英語化してトークンを節約
                        // 日本語: "以下の設定になりきって、ユーザーの投稿にリアクションしてください。..."
                        ['text' => "Act as the persona defined below and react to the user's post.\n\n[Persona]\n{$systemPrompt}\n\n[User Post]\n{$userPost}"],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1000,
            ],
        ];

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            if ($response->failed()) {
                Log::error('Gemini API Error Detail:', $response->json());
                $response->throw();
            }

            return $response->json('candidates.0.content.parts.0.text');

        } catch (\Exception $e) {
            Log::error('Gemini API Connection Error: '.$e->getMessage());
            throw $e;
        }
    }
}
