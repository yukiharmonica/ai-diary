# 開発開始前チェックリスト

本格的な開発を始める前に、以下を確認してください。

## ✅ 環境セットアップ

### サーバー側（さくらVPS）

- [ ] Dockerがインストール済み
- [ ] ユーザーがdockerグループに所属
- [ ] infrastructureのサービスが起動中
  ```bash
  cd /home/ubuntu/aiDiary/infrastructure
  docker compose ps
  # すべてのサービスが healthy または running
  ```

### ローカルPC

- [ ] VS Codeインストール済み
- [ ] Remote-SSH拡張機能インストール済み
- [ ] Dev Containers拡張機能インストール済み
- [ ] SSH接続でさくらVPSに接続可能

## ✅ アプリケーションセットアップ

### Dev Container内

- [ ] Node.js/npmインストール済み
  ```bash
  sudo apk add --no-cache nodejs npm
  ```

- [ ] 初期セットアップ完了
  ```bash
  composer setup
  ```

- [ ] .envファイルが存在し、設定が正しい
  ```bash
  cat .env | grep DB_HOST  # db になっているか確認
  ```

- [ ] データベースマイグレーション完了
  ```bash
  php artisan migrate:status
  ```

## ✅ 開発ツール確認

- [ ] コードスタイルチェックが動作
  ```bash
  ./vendor/bin/pint --test
  ```

- [ ] 静的解析が動作
  ```bash
  ./vendor/bin/phpstan analyse
  ```

- [ ] テストが通る
  ```bash
  composer test
  ```

- [ ] Git hooksが設定済み
  ```bash
  ls -la .husky/pre-commit
  ```

## ✅ 開発サーバー起動

- [ ] 開発サーバーが起動する
  ```bash
  composer dev
  ```

- [ ] ローカルブラウザでアクセス可能
  - http://localhost:8000

- [ ] ポートフォワーディングが機能
  - VS Codeの「ポート」タブで8000と5173を確認

## ✅ Git/GitHub

- [ ] リポジトリがGitHubに接続済み
  ```bash
  git remote -v
  # origin https://github.com/yukiharmonica/ai-diary.git
  ```

- [ ] 初回プッシュ完了
  ```bash
  git push -u origin main
  ```

- [ ] .gitignoreが適切に設定
  ```bash
  git status
  # vendor/, node_modules/, .env が表示されないこと
  ```

## ✅ ドキュメント

- [ ] README.mdが最新
- [ ] CONTRIBUTING.mdが存在
- [ ] LICENSEが存在
- [ ] SECURITY.mdが存在
- [ ] AI instructionsが各ディレクトリに存在

## 🚀 開発開始

すべてのチェック項目が完了したら、開発を始められます！

### 最初のタスク候補

1. **ダッシュボードのカスタマイズ**
   - `web-app/resources/views/dashboard.blade.php` を編集

2. **データベーステーブルの追加**
   - マイグレーションファイルを作成
   ```bash
   php artisan make:migration create_diaries_table
   ```

3. **Livewireコンポーネントの作成**
   - Voltコンポーネントを追加
   ```bash
   php artisan make:volt diary-entry --class
   ```

4. **認証フローのカスタマイズ**
   - `web-app/resources/views/livewire/pages/auth/` を編集

### 開発フロー

```bash
# 1. ブランチを作成
git checkout -b feature/your-feature

# 2. コードを書く
# 3. テストを実行
composer test

# 4. コードスタイルを修正
./vendor/bin/pint

# 5. コミット（自動でlint-stagedが実行される）
git add .
git commit -m "feat: Your feature description"

# 6. プッシュ
git push origin feature/your-feature

# 7. GitHubでプルリクエストを作成
```

Happy Coding! 🎉
