# AI Diary Docker プロジェクト - AI コーディングガイド

## プロジェクト概要

このプロジェクトは、**Laravel + Nginx + MySQL + Redis**をDockerで構成した「AI日記」アプリケーションの開発環境です。すべてのサービスはDocker Composeで管理され、Laravel本体は親ディレクトリにマウントされる想定です。

## アーキテクチャ

### サービス構成 ([docker-compose.yml](docker-compose.yml))

1. **app**: PHP 8.3-FPM + Laravelアプリケーション本体 (php-fpm, ポート9000)
2. **nginx**: Webサーバー (ポート80公開)
3. **db**: MySQL 8.0 (永続化: `db_data` volume)
4. **redis**: キャッシュ・セッション・Queue用
5. **queue**: Laravel Queue Workerコンテナ (`php artisan queue:work`)

### データフロー

```
ブラウザ → nginx:80 → app:9000 (PHP-FPM) → db:3306 / redis:6379
                                         ↓
                                    queue worker
```

- **Nginxの役割**: 静的ファイルを直接配信し、PHPリクエストを`app:9000`へFastCGI経由で転送 ([nginx/default.conf](nginx/default.conf))
- **ドキュメントルート**: `/var/www/html/public` (Laravelの標準構造)
- **非同期処理**: `queue`コンテナが`redis`をキューバックエンドとして`queue:work`を常時実行

## 開発ワークフロー

### 環境の起動・停止

```powershell
# すべてのサービスを起動 (バックグラウンド)
docker-compose up -d

# ログをリアルタイム表示
docker-compose logs -f app

# 停止
docker-compose down

# 完全クリーンアップ (ボリューム含む)
docker-compose down -v
```

### Laravel アプリケーション操作

**重要**: Laravelコマンドは`app`コンテナ内で実行します。

```powershell
# マイグレーション実行
docker-compose exec app php artisan migrate

# キャッシュクリア
docker-compose exec app php artisan cache:clear

# Composer依存関係インストール
docker-compose exec app composer install

# Tinker (Laravelインタラクティブシェル)
docker-compose exec app php artisan tinker
```

### データベース操作

```powershell
# MySQL接続
docker-compose exec db mysql -u user -ppassword ai_diary_db

# データベースバックアップ
docker-compose exec db mysqldump -u user -ppassword ai_diary_db > backup.sql
```

### Queueワーカーの再起動

Jobクラスを変更した場合、Queueワーカーを再起動する必要があります:

```powershell
docker-compose restart queue
```

## プロジェクト固有の規約

### 環境変数の管理

- Laravel `.env`ファイルは親ディレクトリにあり、コンテナにマウントされる
- [docker-compose.yml](docker-compose.yml)でも主要な環境変数を設定:
  - `DB_HOST=db`, `DB_DATABASE=ai_diary_db`, `REDIS_HOST=redis`
  - タイムゾーン: `Asia/Tokyo`

### ファイル権限

- [Dockerfile](Dockerfile)で`www-data`のUID/GIDをホストと同期 (デフォルト1000)
- ボリュームマウント時の権限エラーを回避

### PHP拡張機能

[Dockerfile](Dockerfile)にインストール済み:
- **データベース**: `pdo_mysql`, `mysqli`
- **画像処理**: `gd` (JPEG/PNG/フリータイプフォント対応)
- **キャッシュ**: `redis`, `opcache`
- **その他**: `bcmath`, `intl`, `zip`, `mbstring`, `dom`, `xml`

### Nginxの設定パターン

[nginx/default.conf](nginx/default.conf)の処理順序:
1. 静的ファイル (画像/CSS/JS) → キャッシュ1年、直接配信
2. PHPファイル → FastCGI経由で`app:9000`へ転送
3. 隠しファイル・Laravel設定ファイル → アクセス拒否

## トラブルシューティング

### コンテナが起動しない場合

```powershell
# ログを確認
docker-compose logs app
docker-compose logs db

# ヘルスチェック状態確認
docker-compose ps
```

### 依存関係の問題

- `app`コンテナは`db`のヘルスチェック完了を待ってから起動
- `queue`は`app`と`redis`の起動を待つ

### 権限エラー

ホストのUID/GIDが1000でない場合、[Dockerfile](Dockerfile)のビルド引数を変更:

```powershell
docker-compose build --build-arg HOST_UID=$(id -u) --build-arg HOST_GID=$(id -g)
```

## 注意事項

- Laravelアプリケーションコード本体はこのディレクトリの外にあり、`.`としてマウント
- ポート80を占有するため、他のWebサーバーと競合しないこと
- 本番環境では`APP_DEBUG=false`、強固なパスワード、HTTPSを使用すること
