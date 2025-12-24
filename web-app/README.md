# AI Diary - Laravel アプリケーション

> **📘 メインドキュメント**: 環境構築の完全ガイドは [ルートディレクトリの README.md](../README.md) を参照してください。

このディレクトリは Laravel アプリケーション本体です。

## 想定環境

- **サーバー**: さくらVPS（Ubuntu）にDocker環境を構築
- **クライアント**: ローカルPCのVS CodeからSSH接続
- **開発方法**: このディレクトリをDev Containerで開く

## クイックスタート

### Dev Container（推奨）

```bash
# 【サーバー側（さくらVPS）】infrastructure のサービスを起動
cd /home/ubuntu/aiDiary/infrastructure
docker compose up -d

# 【ローカルPC】VS Code で SSH接続
# コマンドパレット → "Remote-SSH: Connect to Host"
# → さくらVPSに接続

# 【ローカルPC】web-app を Dev Container で開く
# File → Open Folder → /home/ubuntu/aiDiary/web-app
# コマンドパレット → "Dev Containers: Reopen in Container"

# 【Dev Container内】初回のみ
sudo apk add --no-cache nodejs npm
composer setup

# 【Dev Container内】開発サーバー起動
composer dev
```

### ローカルホスト開発

```bash
# ローカルサービスを起動
docker compose up -d

# 初回のみ
composer setup

# 開発サーバー起動
composer dev
```

詳細は [../README.md](../README.md) を参照してください。

---

## このディレクトリについて

Laravel 12 + Livewire 3 + Volt を使用した AI 日記アプリケーション本体です。

### 技術スタック

- **バックエンド**: Laravel 12 (PHP 8.2+)
- **フロントエンド**: Livewire 3 + Volt, Tailwind CSS 3
- **認証**: Laravel Breeze
- **ビルドツール**: Vite 7

### ローカル開発用サービス（docker-compose.yml）

このディレクトリの `docker-compose.yml` は**ローカル開発用**のサービスのみを提供します：

- **MySQL** (`:3306`)
- **Redis** (`:6379`)
- **Mailpit** (`:1025` SMTP, `:8025` Web UI)

> **注意**: Laravel本体は含まれません。ホストまたはDev Containerで実行します。

### 本番環境用Docker構成

完全なコンテナ化構成（Nginx + PHP-FPM + Queue Worker含む）は `infrastructure/` ディレクトリを参照してください。

---

### 前提条件

### Dev Container使用時

**サーバー側（さくらVPS - Ubuntu）**:
- Docker / Docker Desktop がインストール済み
- SSH接続可能な状態

**クライアント側（ローカルPC）**:
- **VS Code**
- **Remote - SSH 拡張機能**
- **Dev Containers 拡張機能**

### ローカルホスト開発時

- **PHP 8.2 以上**
- **Composer**
- **Node.js 18 以上** + **npm**
- **Docker Desktop**

---

## 環境変数の設定

`.env` ファイルは `composer setup` で自動作成されます。以下を確認してください：

### Dev Container使用時

```env
DB_HOST=db              # infrastructure の db サービス
REDIS_HOST=redis        # infrastructure の redis サービス
```

### ローカルホスト開発時

```env
DB_HOST=127.0.0.1       # ホストの docker-compose サービス
REDIS_HOST=127.0.0.1
```

---

## 開発ツール

### よく使うコマンド

```bash
# 開発サーバー一括起動（推奨）
composer dev

# テスト実行
composer test

# コードスタイル修正
./vendor/bin/pint

# 静的解析
./vendor/bin/phpstan analyse

# リアルタイムログ確認
php artisan pail

# マイグレーション実行
php artisan migrate

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
```

---

## プロジェクト構造

```
web-app/
├── app/
│   ├── Livewire/
│   │   ├── Actions/       # 単一アクションクラス（例: Logout.php）
│   │   └── Forms/         # Livewire フォームクラス（例: LoginForm.php）
│   ├── Models/            # Eloquent モデル
│   └── Providers/
│       └── VoltServiceProvider.php  # Volt マウントパス設定
├── resources/
│   └── views/
│       ├── livewire/      # Volt コンポーネント
│       │   ├── pages/     # ページコンポーネント（auth など）
│       │   └── profile/   # プロファイル関連
│       ├── components/    # Blade コンポーネント
│       └── layouts/       # レイアウトテンプレート
├── routes/
│   ├── web.php           # Web ルート
│   └── auth.php          # 認証ルート（Volt::route()使用）
├── .devcontainer/        # Dev Container 設定
├── docker-compose.yml    # ローカルサービス用
├── composer.json         # dev, setup, test スクリプト
└── package.json          # Husky, lint-staged, concurrently
```

---

## Livewire + Volt の使い方

### Volt シングルファイルコンポーネント

Blade ファイル内に PHP ロジックを直接記述：

```php
<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public string $message = '';

    public function submit(): void {
        // ロジック
    }
}; ?>

<div>
    <input wire:model="message" type="text">
    <button wire:click="submit">送信</button>
</div>
```

### Volt ルート定義

```php
use Livewire\Volt\Volt;

Volt::route('login', 'pages.auth.login')->name('login');
```

### Livewire Form クラス

```php
use Livewire\Form;
use Livewire\Attributes\Validate;

class LoginForm extends Form
{
    #[Validate('required|email')]
    public string $email = '';
}
```

---

## トラブルシューティング

### SSH接続経由のDev Containerで開けない

**解決方法**:
```bash
# SSH接続先（さくらVPS）で実行
sudo systemctl status docker
sudo systemctl start docker
sudo usermod -aG docker $USER
# 一度ログアウト→再ログイン
```

詳細は [../README.md](../README.md) のトラブルシューティングを参照。

### ポートフォワーディングが機能しない

VS Codeの「ポート」タブでポート8000と5173が転送されているか確認してください。

### Docker Desktop が起動していない（Windows）

1. Docker Desktop を起動
2. タスクトレイのアイコンが緑色になるまで待つ

### ポート競合（web-app と infrastructure）

このプロジェクトには2つの docker-compose.yml があります。両方同時に起動すると競合します。

**Dev Container開発の場合**:
```bash
# infrastructure を使用
cd ../infrastructure
docker compose up -d
```

### データベース接続エラー

環境変数を確認：
- **Dev Container**: `DB_HOST=db`
- **ローカルホスト**: `DB_HOST=127.0.0.1`

### npm not found エラー

```bash
sudo apk add --no-cache nodejs npm
composer setup
```

詳細なトラブルシューティングは [../README.md](../README.md) を参照してください。

---

## 参考リンク

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Livewire 3 Documentation](https://livewire.laravel.com/docs)
- [Volt Documentation](https://livewire.laravel.com/docs/volt)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

---

## ライセンス

MIT License
