#!/usr/bin/env bash
set -e

echo "Composer install..."
composer install --no-dev --optimize-autoloader

# Build asset (jika ada Vite)
if [ -f package.json ]; then
  echo "Building frontend..."
  # Jika tidak butuh, hapus baris berikut
  npm ci || npm install
  npm run build || true
fi

php artisan storage:link || true

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

echo "Caching config & routes..."
php artisan config:cache
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

echo "Done."
