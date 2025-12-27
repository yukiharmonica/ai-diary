import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // SCSSファイル構成を維持
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173, // Dockerのポートフォワーディングに合わせて明示
        hmr: {
            host: 'localhost',
        },
        watch: {
            // Docker環境でのパフォーマンス向上のため、不要なディレクトリを監視対象外にする
            ignored: ['**/vendor/**', '**/node_modules/**', '**/storage/**'],
        },
    },
});
