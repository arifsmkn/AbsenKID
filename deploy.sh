#!/bin/bash
# Deploy script untuk cloud server kid31.sankei-dharma.id
cd /var/www/AbsenKID

git pull origin main

# Fix APP_URL untuk cloud (domain publik)
sed -i 's|APP_URL=http://192.168.135.162|APP_URL=https://kid31.sankei-dharma.id|' .env

# Rebuild cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permission
chown -R www-data:www-data storage bootstrap/cache database public/storage 2>/dev/null
chmod 664 database/database.sqlite
php artisan storage:link 2>/dev/null || true

echo "✅ Deploy selesai!"
