<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    /**
     * AIボットとしてのリアクションを生成する
     *
     * @param string $systemPrompt ボットのペルソナ（性格）
     * @param string $userPost ユーザーの投稿内容
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
                        ['text' => "以下の設定になりきって、ユーザーの投稿にリアクションしてください。\n\n【設定】\n{$systemPrompt}\n\n【ユーザーの投稿】\n{$userPost}"]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 4000, 
            ]
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
            Log::error('Gemini API Connection Error: ' . $e->getMessage());
            throw $e;
        }
    }
}