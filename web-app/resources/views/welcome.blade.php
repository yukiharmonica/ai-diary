<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'AI Diary') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="welcome-container">
            {{-- ナビゲーション --}}
            <nav class="welcome-nav">
                <div class="welcome-logo">
                    {{-- ロゴコンポーネントを使用するように変更 --}}
                    <x-application-logo class="w-8 h-8" />
                    {{ config('app.name', 'AI Diary') }}
                </div>
                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="welcome-nav-link">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="welcome-nav-link mr-4">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-indigo-600 text-white rounded-full font-bold text-sm shadow-md hover:bg-indigo-700 transition">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>

            {{-- ヒーローセクション --}}
            <main class="hero-section">
                <span class="hero-badge">New Experience</span>
                <h1 class="hero-title">
                    あなたの日常に、<br>
                    <span class="text-indigo-600">AIたちの彩り</span>を。
                </h1>
                <p class="hero-subtitle">
                    ただの日記ではありません。美食家、熱血トレーナー、皮肉屋の猫...<br>
                    個性豊かなAIキャラクターたちが、あなたの投稿にリアクションを返します。
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="hero-btn-primary">
                        今すぐ始める
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <a href="{{ route('login') }}" class="hero-btn-secondary">
                        ログイン
                    </a>
                </div>
            </main>

            {{-- 特徴セクション --}}
            <section class="bg-white border-t border-gray-100">
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <h3 class="feature-title">AIからの即レス</h3>
                        <p class="feature-desc">投稿するとすぐにAIが反応。肯定もツッコミも、多様な視点からのコメントが届きます。</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="feature-title">カレンダーで振り返り</h3>
                        <p class="feature-desc">過去の記録はカレンダー形式で簡単アクセス。あの日どんな会話をしたか、すぐに思い出せます。</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <h3 class="feature-title">自分だけの体験</h3>
                        <p class="feature-desc">ジャンルごとの絞り込みや、今後のアップデートで追加される新キャラクターにもご期待ください。</p>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>