# AI Diary - Docker 本番環境

> **📘 メインドキュメント**: 環境構築の完全ガイドは [ルートディレクトリの README.md](../README.md) を参照してください。

このディレクトリは本番環境用のDocker構成です。

## 想定環境

- **サーバー**: さくらVPS（Ubuntu）
- **デプロイ**: このディレクトリでdocker-compose.ymlを起動
- **開発時**: web-appをDev Containerで開く際、このサービスに接続

## クイックスタート

```bash
# 【サーバー側（さくらVPS）】

# イメージをビルド
docker compose build

# コンテナを起動
docker compose up -d

# Laravel環境設定
docker compose exec app cp .env.example .env
docker compose exec app php artisan key:generate

# Composerパッケージをインストール
docker compose exec app composer install

# マイグレーション
docker compose exec app php artisan migrate

# ブラウザでアクセス
# http://<さくらVPSのIPアドレス> (ポート80)
```

詳細は [../README.md](../README.md) の「方法3: フルDocker環境」を参照してください。

---

## このディレクトリについて

Laravel + Nginx + MySQL + Redis による完全なコンテナ化構成です。

### 環境構成

| サービス | コンテナ名 | ポート | 用途 |
|---------|-----------|--------|------|
| Nginx | ai_diary_nginx | 80 | Webサーバー |
| PHP-FPM | ai_diary_app | 9000 | Laravel実行環境 |
| MySQL | ai_diary_db | 3306 (内部) | データベース |
| Redis | ai_diary_redis | 6379 (内部) | キャッシュ/セッション/Queue |
| Queue Worker | ai_diary_queue_worker | - | 非同期処理 |

### データフロー

```
ブラウザ → nginx:80 → app:9000 (PHP-FPM) → db:3306 / redis:6379
                                         ↓
                                    queue worker
```

### システム要件

- Docker Engine 20.10以降
- Docker Compose V2 (2.0以降)
- 空きポート: 80
- 推奨メモリ: 4GB以上

---

## ディレクトリ構造

```
infrastructure/
├── docker-compose.yml      # サービス定義（app, nginx, db, redis, queue）
├── Dockerfile              # PHP 8.3-FPM Alpine（マルチステージビルド）
├── .env.docker             # Docker環境変数
├── nginx/
│   └── default.conf        # Nginx設定（FastCGI, セキュリティヘッダー）
├── php/
│   ├── php.ini             # PHP設定（OPcache, Redis sessions）
│   └── php-fpm.conf        # PHP-FPM設定（health checks, slow log）
├── CHANGELOG.md            # 最近のセキュリティ/パフォーマンス改善
└── README.md               # このファイル
```

---

## よく使うコマンド

### コンテナの状態確認

```bash
# コンテナ一覧とヘルスチェック状態
docker compose ps

# ログをリアルタイム表示
docker compose logs -f

# 特定のサービスのログのみ表示
docker compose logs -f app
```

### Laravel コマンド実行

```bash
# マイグレーション
docker compose exec app php artisan migrate

# キャッシュクリア
docker compose exec app php artisan cache:clear

# Tinker（対話型シェル）
docker compose exec app php artisan tinker

# テスト実行
docker compose exec app php artisan test
```

### Composer コマンド

```bash
# パッケージインストール
docker compose exec app composer install

# パッケージ追加
docker compose exec app composer require package/name
```

### データベース操作

```bash
# MySQLに接続
docker compose exec db mysql -u user -ppassword ai_diary_db

# データベースバックアップ
docker compose exec db mysqldump -u user -ppassword ai_diary_db > backup.sql
```

### Queue Worker の再起動

Jobクラスを変更した場合：

```bash
docker compose restart queue
```

---

## トラブルシューティング

### ポート80が既に使用されている

```bash
# 他のプロセスを確認
sudo lsof -i :80

# docker-compose.ymlのポート設定を変更
# ports:
#   - '8080:80'  # ホスト側を8080に変更
```

### ファイル権限エラー

ホストのUID/GIDが1000でない場合：

```bash
# 現在のUID/GIDを確認
id -u  # UID
id -g  # GID

# ビルド時に指定
docker compose build --build-arg HOST_UID=$(id -u) --build-arg HOST_GID=$(id -g)
```

### コンテナが起動しない

```bash
# エラーログを確認
docker compose logs app
docker compose logs nginx

# コンテナを再ビルド
docker compose build --no-cache
docker compose up -d
```

### Queue Workerが動作しない

```bash
# Queue Workerのログ確認
docker compose logs queue

# 手動で実行してエラー確認
docker compose exec app php artisan queue:work --once
```

詳細なトラブルシューティングは [../README.md](../README.md) を参照してください。

---

## 本番環境への適用

本番環境では以下の変更が必要です：

1. **環境変数の変更** (`.env.docker`)
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - 強固なパスワードに変更

2. **PHP設定の変更** (`php/php.ini`)
   - `display_errors = Off`

3. **HTTPSの設定**
   - SSL証明書の追加
   - Nginx設定の更新

4. **セキュリティ**
   - データベースパスワードの変更
   - シークレットキーの管理

---

## 参考資料

- [CHANGELOG.md](CHANGELOG.md) - 最近のセキュリティ/パフォーマンス改善
- [../README.md](../README.md) - プロジェクト全体のドキュメント
- [../web-app/.github/copilot-instructions.md](../web-app/.github/copilot-instructions.md) - Laravel開発ガイド

---

## ライセンス

このプロジェクトのライセンスは [LICENSE](../LICENSE) ファイルを参照してください。
