<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[WebReinvent](https://webreinvent.com/)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Jump24](https://jump24.co.uk)**
-   **[Redberry](https://redberry.international/laravel/)**
-   **[Active Logic](https://activelogic.com)**
-   **[byte5](https://byte5.de)**
-   **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Diary Application

## 概要

日記を記録・管理するための Web アプリケーション。

## 機能

-   日記の作成、編集、削除
-   タグによる分類
-   気分の記録
-   日付による検索
-   キーワード検索
-   月別表示

## 技術スタック

-   Laravel 12.2.0
-   PHP 8.4.5
-   MySQL 8.0
-   Vue.js 3
-   Tailwind CSS
-   Inertia.js

## ローカル開発環境のセットアップ

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

## 本番環境へのデプロイ手順

### 前提条件

-   AWS アカウント
-   EC2 インスタンス（t2.micro）
-   RDS インスタンス（MySQL）
-   ドメイン名（オプション）

### デプロイ手順

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

2. アプリケーションのデプロイ:

```bash
# アプリケーションディレクトリの作成
sudo mkdir -p /var/www/diary-app
sudo chown -R ec2-user:ec2-user /var/www/diary-app

# コードのクローン
cd /var/www/diary-app
git clone <repository-url> .

# 環境設定
cp .env.production .env
php artisan key:generate

# 依存関係のインストールとビルド
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

3. Nginx の設定:

```bash
# Nginx設定ファイルのコピー
sudo cp nginx.conf /etc/nginx/conf.d/diary-app.conf
sudo systemctl restart nginx
```

4. SSL 証明書の設定（推奨）:

```bash
sudo yum install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

5. デプロイスクリプトの実行:

```bash
chmod +x deploy.sh
./deploy.sh
```

### メンテナンス

1. アプリケーションの更新:

```bash
./deploy.sh
```

2. ログの確認:

```bash
tail -f /var/log/nginx/error.log
tail -f storage/logs/laravel.log
```

3. バックアップ:

```bash
# データベースのバックアップ
mysqldump -u username -p database_name > backup.sql

# ファイルのバックアップ
tar -czf backup.tar.gz /var/www/diary-app
```

## セキュリティ注意事項

-   本番環境では `APP_DEBUG=false` に設定
-   適切なファイルパーミッションの設定
-   定期的なセキュリティアップデート
-   強力なパスワードの使用
-   ファイアウォールの適切な設定

## トラブルシューティング

### よくある問題と解決方法

1. 500 エラー:

-   ストレージディレクトリのパーミッション確認
-   ログファイルの確認
-   .env 設定の確認

2. 403 エラー:

-   Nginx の設定確認
-   ファイルパーミッションの確認

3. データベース接続エラー:

-   RDS 接続情報の確認
-   セキュリティグループの設定確認

## ライセンス

MIT License

## 手動デプロイの手順

コードを変更した後、以下の手順で本番環境に反映させることができます：

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

注意事項:

-   コードをプッシュする前に、必ずローカルでテストを行ってください
-   本番環境の.env ファイルは変更しないように注意してください
-   デプロイ後は、アプリケーションが正常に動作することを確認してください
-   ブラウザのキャッシュをクリアする必要がある場合があります
