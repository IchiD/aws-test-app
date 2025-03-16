# 日記アプリケーション

## 概要

日記を記録・管理するための Web アプリケーション。
タグ付けや気分の記録、検索機能を備えた使いやすい日記システムです。

## 機能

-   日記の作成、編集、削除
-   タグによる分類
-   気分の記録（😊 良い、😐 普通、😢 悪い）
-   日付による検索
-   キーワード検索
-   月別表示
-   レスポンシブデザイン

## 技術スタック

-   Laravel 12.2.0
-   PHP 8.4.5
-   MySQL 8.0
-   Vue.js 3
-   Tailwind CSS
-   Inertia.js

## 開発環境のセットアップ

1. リポジトリのクローン:

```bash
git clone <repository-url>
cd diary-app
```

2. 依存関係のインストール:

```bash
composer install
npm install
```

3. 環境設定:

```bash
cp .env.example .env
php artisan key:generate
```

4. データベースの設定:

-   .env ファイルでデータベース接続情報を設定

```bash
php artisan migrate
```

5. 開発サーバーの起動:

```bash
php artisan serve
npm run dev
```

## デプロイ手順

### 前提条件

-   AWS アカウント
-   EC2 インスタンス（t2.micro）
-   RDS インスタンス（MySQL）
-   ドメイン名

### 初回デプロイ時の設定

1. EC2 インスタンスの初期設定:

```bash
# 必要なパッケージのインストール
sudo yum update -y
sudo yum install -y nginx
sudo amazon-linux-extras install -y php8.2
sudo yum install -y php-fpm php-mysqlnd php-zip php-devel php-gd php-mbstring php-curl php-xml php-pear php-bcmath php-json

# Composerのインストール
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.jsのインストール
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
source ~/.bashrc
nvm install 16
nvm use 16
```

2. アプリケーションのセットアップ:

```bash
# アプリケーションディレクトリの作成
sudo mkdir -p /var/www/test-app
sudo chown -R ec2-user:ec2-user /var/www/test-app

# コードのクローン
cd /var/www/test-app
git clone <repository-url> .

# 環境設定
cp .env.production .env
php artisan key:generate
```

3. Nginx の設定:

```bash
# Nginx設定ファイルのコピー
sudo cp nginx.conf /etc/nginx/conf.d/test-app.conf
sudo systemctl restart nginx
```

4. SSL 証明書の設定:

```bash
sudo yum install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 手動デプロイの手順

コードを変更した後、以下の手順で本番環境に反映させます：

1. ローカルでの作業:

```bash
# 変更をステージングに追加
git add .

# 変更をコミット
git commit -m "変更内容の説明"

# GitHubにプッシュ
git push origin main
```

2. EC2 インスタンスでのデプロイ:

```bash
# EC2インスタンスに接続
ssh -i ~/.ssh/test-app-key.pem ec2-user@ec2-54-252-196-67.ap-southeast-2.compute.amazonaws.com

# アプリケーションディレクトリに移動
cd /var/www/test-app

# 最新のコードを取得
sudo git pull origin main

# 依存関係のインストール
sudo composer install --no-dev --optimize-autoloader
sudo npm ci
sudo npm run build

# パーミッションの設定
sudo chown -R nginx:nginx .
sudo chmod -R 775 storage bootstrap/cache

# サービスの再起動
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

### 注意事項

-   コードをプッシュする前に、必ずローカルでテストを行う
-   本番環境の.env ファイルは変更しない
-   デプロイ後は、アプリケーションが正常に動作することを確認
-   ブラウザのキャッシュをクリアする必要がある場合がある

## トラブルシューティング

### よくある問題と解決方法

1. 500 エラー:

-   ストレージディレクトリのパーミッション確認
-   ログファイルの確認（/var/log/nginx/error.log, storage/logs/laravel.log）
-   .env 設定の確認

2. 403 エラー:

-   Nginx の設定確認
-   ファイルパーミッションの確認

3. データベース接続エラー:

-   RDS 接続情報の確認
-   セキュリティグループの設定確認

## セキュリティ設定

-   本番環境では`APP_DEBUG=false`に設定
-   適切なファイルパーミッションの設定
-   定期的なセキュリティアップデート
-   強力なパスワードの使用
-   ファイアウォールの適切な設定

## ライセンス

MIT License

## Remote-SSH での開発

### 設定手順

1. VS Code または Cursor に Remote-SSH 拡張機能をインストール

2. `~/.ssh/config` に以下の設定を追加:

```bash
Host aws-test-app
    HostName ec2-54-252-196-67.ap-southeast-2.compute.amazonaws.com
    User ec2-user
    IdentityFile ~/.ssh/test-app-key.pem
    ForwardAgent yes
```

3. 接続手順:
    - コマンドパレットを開く (Cmd+Shift+P)
    - "Remote-SSH: Connect to Host..." を選択
    - `aws-test-app` を選択
    - `/var/www/test-app` フォルダを開く

### セキュリティ上の注意点

Remote-SSH を使用する際は、以下のセキュリティ対策を必ず実施してください：

1. アクセス制限

    - 開発用の特定の IP アドレスからのみ SSH アクセスを許可
    - EC2 のセキュリティグループで接続元 IP を制限
    - 本番環境での直接編集は原則禁止

2. 認証情報の管理

    - SSH 鍵は適切な権限設定で保管 (chmod 600)
    - パスフレーズ付きの鍵を使用
    - 共有の開発環境では鍵を使い回さない

3. 作業ルール

    - 本番環境での直接的なファイル編集は避け、GitHub を経由したデプロイを使用
    - データベースへの直接アクセスは必要最小限に制限
    - 重要な設定ファイル（.env 等）の変更は慎重に実施

4. 監査とモニタリング
    - SSH アクセスログを定期的に確認
    - 不審なアクセスの監視
    - 作業履歴の記録

推奨される開発フロー：

1. ローカル環境で開発
2. GitHub にプッシュ
3. CI/CD パイプラインを通じてデプロイ
4. Remote-SSH は緊急時やデバッグ時のみ使用
