
# AI Diary - Docker本番/開発インフラ

このディレクトリは「Nginx + PHP-FPM + MySQL + Redis + Queue Worker」によるAI DiaryのDocker構成です。

> プロジェクト全体ガイドは [../README.md](../README.md) を参照してください。

---

## 主な運用フロー

### 1. 本番/開発インフラの起動
1. `docker compose build` でイメージ作成
2. `docker compose up -d` で全サービス起動
3. 必要に応じて `docker compose exec app` でLaravelコマンド実行

---

## サービス構成

| サービス      | コンテナ名              | ポート   | 用途                |
|--------------|------------------------|---------|---------------------|
| Nginx        | ai_diary_nginx         | 80      | Webサーバー         |
| PHP-FPM      | ai_diary_app           | 9000    | Laravel実行環境     |
| MySQL        | ai_diary_db            | 3306    | データベース        |
| Redis        | ai_diary_redis         | 6379    | キャッシュ/Queue    |
| Queue Worker | ai_diary_queue_worker  | -       | 非同期処理          |

---

## ディレクトリ構成（抜粋）

```
infrastructure/
├── docker-compose.yml      # サービス定義
├── Dockerfile              # PHP-FPMビルド
├── nginx/                  # Nginx設定
├── php/                    # PHP設定
└── README.md               # このファイル
```

---

## よく使うコマンド

| 作業 | コマンド例 |
|------|------------|
| イメージ作成 | `docker compose build` |
| サービス起動 | `docker compose up -d` |
| サービス状態 | `docker compose ps` |
| ログ確認 | `docker compose logs -f app` |
| Laravelコマンド | `docker compose exec app php artisan migrate` |
| Queue再起動 | `docker compose restart queue` |

---

## 本番環境Tips

- `.env.docker` で `APP_ENV=production` `APP_DEBUG=false` を設定
- DB/Redis/APPのパスワードは強固なものに変更
- NginxでSSL証明書を設定しHTTPS化
- PHP設定（php.ini）で `display_errors = Off` に

---

## トラブルシューティング

- ポート80競合 → `sudo lsof -i :80` で確認、必要ならポート変更
- 権限エラー → DockerfileのUID/GIDビルド引数を調整
- コンテナ起動失敗 → `docker compose logs` でエラー確認
- Queue Worker不調 → `docker compose logs queue` で確認、`docker compose restart queue` で再起動

詳細は [../README.md](../README.md) を参照してください。

---

## 参考資料

- [CHANGELOG.md](CHANGELOG.md) - セキュリティ/パフォーマンス改善履歴
- [../README.md](../README.md) - プロジェクト全体ガイド
- [../web-app/.github/copilot-instructions.md](../web-app/.github/copilot-instructions.md) - Laravel開発ガイド

---

## ライセンス

このプロジェクトのライセンスは [LICENSE](../LICENSE) を参照してください。
