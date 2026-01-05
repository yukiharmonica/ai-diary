
# AI Diary - Laravel アプリ本体

このディレクトリは「Laravel 12 + Livewire 3 + Volt」によるAI日記アプリケーション本体です。

> 環境構築・運用の全体ガイドは [../README.md](../README.md) を参照してください。

---

## 主な開発フロー

### 1. Dev Container（推奨）
1. サーバーで `infrastructure/docker-compose.yml` を起動
2. VS Codeでweb-appをDev Containerとして開く
3. コンテナ内で `composer setup` → `composer dev` で開発開始

### 2. ローカルホスト開発
1. web-app配下で `docker compose up -d` でMySQL/Redis/Mailpit起動
2. `composer setup` → `composer dev` で開発

---

## 技術スタック

- Laravel 12 (PHP 8.2+)
- Livewire 3 + Volt
- Tailwind CSS 3
- Laravel Breeze
- Vite 7

---

## よく使うコマンド

| 作業 | コマンド | 備考 |
|------|---------|------|
| 開発サーバー起動 | `composer dev` | 一括起動（サーバー/キュー/ログ/Vite） |
| テスト実行 | `composer test` | configクリア→テスト |
| コード整形 | `./vendor/bin/pint` | Laravel Pint |
| 静的解析 | `./vendor/bin/phpstan analyse` | PHPStan + Larastan |
| マイグレーション | `php artisan migrate` | DBスキーマ更新 |
| リアルタイムログ | `php artisan pail` | ログ監視 |

---

## ディレクトリ構成（抜粋）

```
web-app/
├── app/                # アプリロジック
│   ├── Livewire/       # Livewire/Volt/フォーム/アクション
│   ├── Models/         # Eloquentモデル
│   └── Providers/      # VoltServiceProvider等
├── resources/
│   └── views/          # Blade/Voltコンポーネント
├── routes/             # web.php, auth.php
├── .devcontainer/      # Dev Container設定
├── docker-compose.yml  # ローカルサービス用
├── composer.json       # スクリプト/依存
└── package.json        # JSツール
```

---

## 環境変数例

### Dev Container
```
DB_HOST=db
REDIS_HOST=redis
```
### ローカルホスト
```
DB_HOST=127.0.0.1
REDIS_HOST=127.0.0.1
```

---

## トラブルシューティング

- Dev Containerが開けない → サーバーのDocker/SSH設定を確認
- ポート競合 → web-appとinfrastructureのdocker-composeを同時起動しない
- DB接続エラー → .envのDB_HOST/REDIS_HOSTを確認
- npm not found → `sudo apk add --no-cache nodejs npm` 実行

詳細は [../README.md](../README.md) を参照してください。

---

## 参考リンク

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Livewire 3 Documentation](https://livewire.laravel.com/docs)
- [Volt Documentation](https://livewire.laravel.com/docs/volt)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

---

## ライセンス

MIT License
