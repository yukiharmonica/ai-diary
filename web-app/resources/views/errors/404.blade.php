<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Page Not Found - {{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        
        <!-- Fonts & Styles -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet" />
        
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
        
        <style>body { font-family: 'Inter', 'Noto Sans JP', sans-serif; }</style>
    </head>
    <body class="antialiased">
        <div class="welcome-container justify-center">
            {{-- 背景装飾 --}}
            <div class="welcome-bg-text">404</div>

            {{-- コンテンツ --}}
            <main class="hero-section">
                <h1 class="hero-title text-8xl mb-4">
                    404
                </h1>
                <p class="hero-subtitle text-slate-600">
                    お探しのページは見つかりませんでした。<br>
                    AIたちがページを食べてしまったかもしれません。
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('home') }}" class="hero-btn-primary">
                        ホームに戻る
                    </a>
                </div>
            </main>

            <footer class="welcome-footer">
                &copy; {{ date('Y') }} AI Diary.
            </footer>
        </div>
    </body>
</html>