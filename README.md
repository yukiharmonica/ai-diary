---
# AI Diary - プロジェクト全体ガイド

AI Diaryは「Laravel 12 + Livewire 3 + Volt」によるAI日記アプリケーションです。

## 構成と役割

- **web-app/** : Laravelアプリ本体（Livewire, Volt, Tailwind, Breeze）
- **infrastructure/** : 本番/開発用Docker構成（Nginx, PHP-FPM, MySQL, Redis, Queue）

## 技術スタック

- Laravel 12 (PHP 8.2+)
- Livewire 3 + Volt
- Tailwind CSS 3
- Laravel Breeze
- MySQL 8.0 / Redis 7
- Vite 7
- Docker Compose + Nginx

## 想定環境

- **サーバー**: さくらVPS（Ubuntu）+ Docker
- **クライアント**: VS Code（Remote-SSH, Dev Container）

## 開発・運用フロー

### 1. Dev Container（推奨）
1. サーバーで `infrastructure/docker-compose.yml` を起動
2. VS Codeでweb-appをDev Containerとして開く
3. コンテナ内で `composer setup` → `composer dev` で開発開始

### 2. ローカルホスト開発
1. web-app配下で `docker compose up -d` でMySQL/Redis/Mailpit起動
2. `composer setup` → `composer dev` で開発

### 3. 本番/フルDocker
1. infrastructure配下で `docker compose up -d` で全サービス起動
2. ブラウザで http://<VPS-IP> へアクセス

## よく使うコマンド

| 作業 | コマンド | ディレクトリ |
|------|---------|-----------|
| 開発サーバー起動 | `composer dev` | web-app/ |
| テスト実行 | `composer test` | web-app/ |
| DBマイグレーション | `php artisan migrate` | web-app/ |
| Docker起動 | `docker compose up -d` | infrastructure/ |
| ログ確認 | `docker compose logs -f app` | infrastructure/ |

## ディレクトリ構成

```
aiDiary/
├── web-app/           # Laravelアプリ本体
│   ├── docker-compose.yml
│   ├── .devcontainer/
│   └── ...
├── infrastructure/    # Docker/Nginx/DB/Queue構成
│   ├── docker-compose.yml
│   ├── Dockerfile
│   └── nginx/
└── README.md          # このファイル
```

## 詳細ガイド

- [web-app/README.md](web-app/README.md) : Laravelアプリ開発・ローカル運用ガイド
- [infrastructure/README.md](infrastructure/README.md) : Docker本番運用・サーバー管理ガイド

## トラブルシューティング

- サーバー起動/接続エラー → `docker compose logs` で詳細確認
- Queue/Job変更時 → `docker compose restart queue` で再起動
- 権限エラー → DockerfileのUID/GID設定を見直し

---
```bash
# リポジトリのクローン
git clone <repository-url>
cd aiDiary

# Dockerグループにユーザーを追加（初回のみ）
sudo usermod -aG docker $USER
# 一度ログアウト→再ログインしてグループ反映

# Dockerサービスの起動確認
sudo systemctl status docker
sudo systemctl start docker  # 停止している場合

# インフラサービスを起動
cd infrastructure
docker compose up -d

# コンテナの起動確認
docker compose ps
# app, nginx, db, redis, queue が healthy または running であることを確認
```

##### 2. ローカルPC側の準備

```bash
# VS Code拡張機能のインストール
# - Remote - SSH
# - Dev Containers

# VS CodeでSSH接続
# コマンドパレット (Ctrl+Shift+P) → "Remote-SSH: Connect to Host"
# さくらVPSのホスト名またはIPアドレスを入力
```

##### 3. Dev Container で開く

SSH接続後、リモートサーバー上で：

```bash
# web-appディレクトリを開く
code /home/ubuntu/aiDiary/web-app
```

VS Code で：
1. コマンドパレット (`Ctrl+Shift+P`) を開く
2. `Dev Containers: Reopen in Container` を選択
3. コンテナのビルド・起動を待つ（初回は数分かかります）

##### 4. コンテナ内で初期セットアップ

Dev Container内のターミナルで実行：

```bash
# Node.js と npm をインストール（初回のみ）
sudo apk add --no-cache nodejs npm

# 初期セットアップを実行
composer setup
```

このコマンドは以下を実行します：
- Composer パッケージのインストール
- `.env` ファイルの作成（存在しない場合）
- アプリケーションキーの生成
- データベースマイグレーション
- npm パッケージのインストール
- アセットのビルド

##### 5. 環境変数を確認

`web-app/.env` ファイルで以下を確認：

```env
DB_HOST=db              # infrastructure の db サービスを参照
REDIS_HOST=redis        # infrastructure の redis サービスを参照
MAIL_HOST=mailpit       # ローカル開発の場合は 127.0.0.1
```

#### 🔄 日常の開発作業

##### 1. SSH接続とDev Containerを開く

ローカルPCのVS Codeから：

```bash
# 1. VS CodeでSSH接続
# コマンドパレット → "Remote-SSH: Connect to Host"

# 2. リモートサーバー上で web-app を開く
# File → Open Folder → /home/ubuntu/aiDiary/web-app

# 3. Dev Containerで開く
# コマンドパレット → "Dev Containers: Reopen in Container"
```

##### 2. サーバー側のコンテナ状態確認（必要に応じて）

SSH接続してサーバー側で確認：

```bash
cd /home/ubuntu/aiDiary/infrastructure
docker compose ps

# 停止している場合は起動
docker compose up -d
```

##### 3. 開発サーバーを起動

Dev Container内のターミナルで：

```bash
composer dev
```

以下が並行起動します：
- Laravel 開発サーバー (`:8000`)
- Queue ワーカー
- リアルタイムログ (Pail)
- Vite 開発サーバー (`:5173` HMR対応)

##### 4. ブラウザでアクセス

**ポートフォワーディングが自動で設定されるため、ローカルブラウザからアクセスできます：**

- アプリケーション: [http://localhost:8000](http://localhost:8000)
- Vite HMR: [http://localhost:5173](http://localhost:5173)

> **注意**: VS Codeのポートフォワーディングタブで、ポート8000と5173が転送されていることを確認してください。

---

### 方法2: ローカルホスト開発

ホストマシンに PHP 環境がある場合、Laravel をホストで実行できます。

#### 前提条件

- **PHP 8.2 以上**
- **Composer**
- **Node.js 18 以上** + **npm**
- **Docker Desktop**（MySQL、Redis、Mailpit 用）

#### 🎯 初回セットアップ

1. **リポジトリのクローン**

   ```bash
   git clone <repository-url>
   cd aiDiary/web-app
   ```

2. **ローカルサービスを起動**

   ```bash
   docker compose up -d
   ```

   起動するサービス：
   - **MySQL** (`:3306`)
   - **Redis** (`:6379`)
   - **Mailpit** (`:1025` SMTP, `:8025` Web UI)

3. **初期セットアップ**

   ```bash
   composer setup
   ```

4. **環境変数を確認**

   `.env` ファイルで以下を確認：

   ```env
   DB_HOST=127.0.0.1       # ローカルホストのDockerサービスを参照
   REDIS_HOST=127.0.0.1
   ```

#### 🔄 日常の開発作業

1. **サービスの起動確認**

   ```bash
   docker compose ps
   # 停止している場合は起動
   docker compose up -d
   ```

2. **開発サーバーを起動**

   ```bash
   composer dev
   ```

3. **ブラウザでアクセス**

   [http://localhost:8000](http://localhost:8000)

---

### 方法3: フルDocker環境

Nginx + PHP-FPM + MySQL + Redis + Queue Worker を完全にコンテナ化した構成です。本番環境に近い構成でテストできます。

#### 前提条件

- **Docker Desktop** (Docker Compose 対応)
- 空きポート: `80`

#### 🎯 初回セットアップ

1. **リポジトリのクローン**

   ```bash
   git clone <repository-url>
   cd aiDiary/infrastructure
   ```

2. **イメージをビルド**

   ```bash
   docker compose build
   ```

   初回は5-10分程度かかります。

3. **コンテナを起動**

   ```bash
   docker compose up -d
   ```

4. **Laravel環境設定**

   ```bash
   # .env ファイルをコピー
   docker compose exec app cp .env.example .env
   
   # アプリケーションキーを生成
   docker compose exec app php artisan key:generate
   ```

5. **Composerパッケージをインストール**

   ```bash
   docker compose exec app composer install
   ```

6. **データベースマイグレーション**

   ```bash
   docker compose exec app php artisan migrate
   ```

7. **アセットをビルド（ホスト側で）**

   ```bash
   cd ../web-app
   npm install
   npm run build
   ```

8. **ブラウザでアクセス**

   [http://localhost](http://localhost) (ポート80)

#### 🔄 日常の開発作業

```bash
# 起動
docker compose up -d

# ログ確認
docker compose logs -f app

# 停止
docker compose down
```

---

## 日常の開発作業

### 開発サーバーの起動

```bash
# Dev Container または ローカルホストの場合
composer dev
```

このコマンドで以下が並行起動：
- Laravel 開発サーバー (`:8000`)
- Queue ワーカー
- リアルタイムログ (Pail)
- Vite 開発サーバー (`:5173` HMR対応)

### コード品質チェック

```bash
# テスト実行
composer test

# コードスタイル修正
./vendor/bin/pint

# 静的解析
./vendor/bin/phpstan analyse
```

### データベース操作

```bash
# マイグレーション実行
php artisan migrate

# マイグレーションロールバック
php artisan migrate:rollback

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
```

### Queue Worker の再起動

Jobクラスを変更した場合：

```bash
# Dev Container / ローカルホストの場合
# composer dev を再起動（Ctrl+C → composer dev）

# フルDockerの場合
cd infrastructure
docker compose restart queue
```

---

## よく使うコマンド

### 開発用コマンド（web-app/）

| コマンド | 説明 |
|---------|------|
| `composer dev` | 開発サーバー一括起動（推奨） |
| `composer test` | テスト実行 |
| `composer setup` | 初期セットアップ |
| `php artisan pail` | リアルタイムログ確認 |
| `php artisan migrate` | マイグレーション実行 |
| `./vendor/bin/pint` | コードスタイル修正 |
| `./vendor/bin/phpstan analyse` | 静的解析 |
| `npm run dev` | Vite開発サーバー |
| `npm run build` | アセットビルド |

### フルDocker用コマンド（infrastructure/）

| コマンド | 説明 |
|---------|------|
| `docker compose up -d` | コンテナ起動 |
| `docker compose down` | コンテナ停止・削除 |
| `docker compose logs -f app` | ログ確認 |
| `docker compose exec app <command>` | コンテナ内でコマンド実行 |
| `docker compose restart queue` | Queueワーカー再起動 |
| `docker compose ps` | コンテナ状態確認 |

---

## プロジェクト構造

```
aiDiary/
├── infrastructure/              # Docker本番環境設定
│   ├── docker-compose.yml      # app, nginx, db, redis, queue
│   ├── Dockerfile              # PHP 8.3-FPM Alpine
│   ├── .env.docker             # Docker環境変数
│   ├── nginx/default.conf      # Nginx設定
│   └── php/
│       ├── php.ini             # PHP設定
│       └── php-fpm.conf        # PHP-FPM設定
│
└── web-app/                    # Laravel アプリケーション
    ├── .devcontainer/          # Dev Container設定
    │   └── devcontainer.json   # infrastructure/docker-compose.yml に接続
    ├── docker-compose.yml      # ローカル開発用サービス（mysql, redis, mailpit）
    ├── app/
    │   ├── Livewire/
    │   │   ├── Actions/        # 単一アクションクラス
    │   │   └── Forms/          # Livewire フォームクラス
    │   ├── Models/             # Eloquent モデル
    │   └── Providers/
    │       └── VoltServiceProvider.php  # Volt マウントパス設定
    ├── resources/
    │   └── views/
    │       ├── livewire/       # Volt コンポーネント
    │       │   ├── pages/      # ページコンポーネント
    │       │   └── profile/    # プロファイル関連
    │       ├── components/     # Blade コンポーネント
    │       └── layouts/        # レイアウトテンプレート
    ├── routes/
    │   ├── web.php             # Web ルート
    │   └── auth.php            # 認証ルート（Volt）
    ├── composer.json           # dev, setup, test スクリプト
    ├── package.json            # Husky, lint-staged
    ├── phpstan.neon            # PHPStan レベル5
    └── .lintstagedrc.json      # 自動フォーマット設定
```

---

## Livewire + Volt の使い方

### Volt シングルファイルコンポーネント

`resources/views/livewire/` または `resources/views/pages/` に配置：

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

`routes/auth.php` や `routes/web.php`：

```php
use Livewire\Volt\Volt;

Volt::route('login', 'pages.auth.login')->name('login');
```

### Livewire Form クラス

`app/Livewire/Forms/` に配置：

```php
use Livewire\Form;
use Livewire\Attributes\Validate;

class LoginForm extends Form
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';
}
```

---

## トラブルシューティング

### Dev Container が開けない（SSH接続経由）

**エラー**: `failed to connect to the docker API`

**原因**: リモートサーバー（さくらVPS）のDocker環境に問題がある

**解決方法**:

```bash
# SSH接続先（さくらVPS）で実行

# 1. Dockerサービスの状態確認
sudo systemctl status docker
sudo systemctl start docker  # 停止している場合

# 2. dockerグループに所属しているか確認
groups
# 出力に "docker" が含まれているか確認

# 含まれていない場合は追加
sudo usermod -aG docker $USER

# 3. 一度ログアウト→再ログイン（グループ反映のため）
exit
# 再度SSH接続

# 4. infrastructure のサービスが起動しているか確認
cd /home/ubuntu/aiDiary/infrastructure
docker compose ps
docker compose up -d  # 停止している場合
```

**VS Code側の確認**:
1. VS Codeを完全に閉じる
2. 再度SSH接続
3. web-appをDev Containerで開く

### ポートフォワーディングが機能しない

**症状**: ローカルブラウザで http://localhost:8000 にアクセスできない

**原因**: VS CodeのSSH接続でポート転送が設定されていない

**解決方法**:

1. VS Codeの「ポート」タブを開く（下部パネル）
2. ポート8000と5173が転送されていることを確認
3. 転送されていない場合は手動で追加：
   - 「ポートの転送」ボタンをクリック
   - ポート番号を入力（8000, 5173）
   - Enterキーで確定

### Docker Desktop が起動していない（Windows）

**エラー**: `failed to connect to the docker API at npipe:////./pipe/dockerDesktopLinuxEngine`

**解決方法**:

1. Docker Desktop を起動
2. タスクトレイのアイコンが緑色になるまで待つ
3. `docker version` で確認

### ポート競合エラー

このプロジェクトには2つの docker-compose.yml があります：

- `web-app/docker-compose.yml` - ローカル開発用（MySQL, Redis, Mailpit）
- `infrastructure/docker-compose.yml` - 本番環境用（全サービス）

両方同時に起動すると**ポート競合**が発生します。

**解決方法**:

```bash
# Dev Container開発の場合は infrastructure を使用
cd infrastructure
docker compose up -d

# web-app側は停止
cd ../web-app
docker compose down
```

### Livewire の JavaScript が 404 エラー

**エラー**: `GET http://localhost/livewire/livewire.js net::ERR_ABORTED 404`

**解決方法**:

```bash
# .env の APP_URL を修正
APP_URL=http://localhost:8000

# 開発サーバーを再起動
composer dev
```

### npm not found エラー

Dev Container で `composer setup` 実行時：

```bash
sudo apk add --no-cache nodejs npm
composer setup
```

### データベース接続エラー

環境変数を確認：

- **Dev Container**: `DB_HOST=db`
- **ローカルホスト**: `DB_HOST=127.0.0.1`

### パーミッションエラー

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Queue Workerが動作しない（フルDocker）

```bash
# ログ確認
docker compose logs queue

# 再起動
docker compose restart queue
```

---

## CI/CD

GitHub Actions で自動実行：

- コードスタイルチェック (Pint)
- 静的解析 (PHPStan)
- テストスイート (PHPUnit)

設定ファイル: `web-app/.github/workflows/laravel.yml`

---

## Pre-commit フック

Husky + lint-staged で自動コード品質チェック：

```bash
# 初回のみ
npm install
```

コミット時に自動実行：
- **PHP**: Laravel Pint で自動フォーマット
- **JS/TS**: Prettier + ESLint
- **その他**: Prettier

---

## Git管理

このプロジェクトはモノレポとして管理されています。

### GitHubリポジトリへの接続

新しいGitHubリポジトリに接続する場合：

```bash
cd /home/ubuntu/aiDiary

# GitHubリポジトリを作成後、以下を実行
git remote add origin https://github.com/<username>/<repository-name>.git
git push -u origin main
```

### 既存リポジトリのバックアップ

モノレポ化前の既存リポジトリは以下にバックアップされています：

- `web-app/.git.bak` - 旧web-appリポジトリ
- `infrastructure/.git.bak` - 旧infrastructureリポジトリ

不要な場合は削除してください：

```bash
rm -rf web-app/.git.bak infrastructure/.git.bak
```

---

## 参考リンク

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Livewire 3 Documentation](https://livewire.laravel.com/docs)
- [Volt Documentation](https://livewire.laravel.com/docs/volt)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

---

## ライセンス

MIT License
