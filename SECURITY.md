# Security Policy

## Supported Versions

現在サポートされているバージョン：

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |

## Reporting a Vulnerability

セキュリティ脆弱性を発見した場合は、公開のIssueを作成せず、以下の方法で報告してください：

### 報告方法

1. **GitHubのSecurity Advisory**
   - リポジトリの「Security」タブから「Report a vulnerability」を選択

2. **プライベートな報告**
   - 詳細を含めてプライベートに報告してください
   - 可能であれば、再現手順を含めてください

### 対応プロセス

1. **確認**: 48時間以内に受領確認
2. **調査**: 脆弱性の影響範囲を評価
3. **修正**: パッチの開発
4. **リリース**: セキュリティアップデートの公開
5. **公開**: 修正後に詳細を公開

### セキュリティのベストプラクティス

このプロジェクトを使用する際は、以下を推奨します：

- `.env` ファイルは**絶対に**コミットしない
- 本番環境では `APP_DEBUG=false` に設定
- 強力なデータベースパスワードを使用
- HTTPSを使用（本番環境）
- 定期的に依存パッケージを更新

```bash
# 依存パッケージの更新
composer update
npm update

# セキュリティ監査
composer audit
npm audit
```

## セキュリティアップデート

セキュリティアップデートは優先的に対応し、速やかにリリースします。

重要な更新は以下で通知されます：
- GitHubリリース
- Security Advisory
