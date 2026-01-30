#!/bin/bash
set -e

echo 🚀 Starting Deployment...

# Navigate to project directory
cd /var/www/SisfoKK-Sentani

# Pull latest changes
echo 📥 Pulling latest changes from GitHub...
git pull origin main

# Update Backend
echo 🐘 Updating Backend (Laravel)...
docker exec sisfokk_php composer install --no-interaction --prefer-dist --optimize-autoloader
docker exec sisfokk_php php artisan migrate --force
docker exec sisfokk_php php artisan cache:clear
docker exec sisfokk_php php artisan config:cache
docker exec sisfokk_php php artisan route:cache
docker exec sisfokk_php php artisan view:cache

# Update Frontend
echo ⚛️ Updating Frontend (Astro)...
docker exec sisfokk_node npm install
docker exec sisfokk_node npm run build

echo ✅ Deployment Finished Successfully!
