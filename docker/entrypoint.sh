#!/bin/sh
set -e

echo "🚀 Starting mulai-aja deployment..."

cd /var/www/html

# Ensure correct permissions on writable directories
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "⚙️  Generating APP_KEY..."
    php artisan key:generate --force
fi

# Clear and cache config for production
echo "⚙️  Caching config & routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed default users hanya sekali (saat database pertama kali dibuat)
# Guard file mencegah seed ulang setiap container restart
if [ ! -f database/.seeded ]; then
    echo "🌱 Seeding default users (superuser, admin, teacher, student)..."
    php artisan db:seed --class=RoleAndPermissionSeeder --force
    touch database/.seeded
    echo "✅ Seeding selesai!"
else
    echo "⏭️  Seeder sudah pernah dijalankan, skip."
fi

# Start services
echo "✅ Starting Nginx + PHP-FPM via Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
