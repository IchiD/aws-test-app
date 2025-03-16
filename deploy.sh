#!/bin/bash

# エラー発生時にスクリプトを停止
set -e

echo "Deployment started..."

# 1. Pull the latest changes
echo "Pulling the latest changes..."
git pull origin main

# 2. Install/update Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# 3. Install/update npm dependencies and build assets
echo "Installing npm dependencies..."
npm install
echo "Building assets..."
npm run build

# 4. Clear and cache configuration
echo "Clearing and caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# 5. Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# 6. Optimize autoloader
echo "Optimizing autoloader..."
composer dump-autoload -o

# 7. Set appropriate permissions
echo "Setting file permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 8. Restart PHP-FPM
echo "Restarting PHP-FPM..."
sudo systemctl restart php8.2-fpm

# 9. Restart Nginx
echo "Restarting Nginx..."
sudo systemctl restart nginx

echo "Deployment completed successfully!" 