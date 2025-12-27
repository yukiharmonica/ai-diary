<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'AI Diary') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        
        <!-- 英語フォント Inter と 日本語フォント Noto Sans JP を併用 -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet" />
        
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Inter', 'Noto Sans JP', sans-serif; }
        </style>
    </head>
    <body class="antialiased">
        <div class="welcome-container">
            {{-- 背景装飾 --}}
            <div class="welcome-bg-text">AI DIARY</div>

            {{-- ナビゲーション --}}
            <nav class="welcome-nav">
                <div class="welcome-logo">
                    <x-application-logo class="w-6 h-6 text-indigo-600" />
                    AI Diary
                </div>
                <div class="flex items-center gap-8">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/home') }}" class="welcome-nav-link">Home</a>
                        @else
                            <a href="{{ route('login') }}" class="welcome-nav-link">Log in</a>
                        @endauth
                    @endif
                </div>
            </nav>

            {{-- ヒーローセクション --}}
            <main class="hero-section">
                <h1 class="hero-title">
                    Write.<br>
                    <span>Talk.</span>
                </h1>
                {{-- 可読性向上のため文字色を濃くし、スマホで改行を入れる --}}
                <p class="hero-subtitle text-slate-600">
                    あなたに応える、<br class="sm:hidden" />あなただけの日記。
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="hero-btn-primary">START NOW</a>
                    {{-- ログインボタンを追加 --}}
                    <a href="{{ route('login') }}" class="hero-btn-secondary">LOG IN</a>
                </div>

                {{-- ミニマルな特徴リスト --}}
                <div class="features-minimal">
                    <div class="feature-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Instant Feedback
                    </div>
                    <div class="feature-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Multi Persona
                    </div>
                    <div class="feature-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Private & Secure
                    </div>
                </div>
            </main>

            <footer class="welcome-footer">
                &copy; {{ date('Y') }} AI Diary.
            </footer>
        </div>
    </body>
</html>