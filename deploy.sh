#!/bin/bash
# Deploy script untuk cloud server kid31.sankei-dharma.id
cd /var/www/AbsenKID

git pull origin main

# Fix konfigurasi untuk cloud
sed -i 's|APP_URL=http://192.168.135.162|APP_URL=https://kid31.sankei-dharma.id|' .env
sed -i 's|MAIL_HOST=mx.sankei-dharma.id|MAIL_HOST=127.0.0.1|' .env
sed -i 's|MAIL_PORT=587|MAIL_PORT=25|' .env
sed -i 's|MAIL_ENCRYPTION=tls|MAIL_ENCRYPTION=null|' .env

# Rebuild cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permission
chown -R www-data:www-data storage bootstrap/cache database public/storage 2>/dev/null
chmod 664 database/database.sqlite
php artisan storage:link 2>/dev/null || true

echo "✅ Deploy selesai!"
