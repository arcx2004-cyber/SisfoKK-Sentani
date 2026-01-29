#!/bin/sh
# Script Deployment Otomatis untuk Alpine Linux
# Dibuat oleh AI Assistant

set -e # Stop script jika ada error

echo "=========================================="
echo "🚀 MEMULAI DEPLOYMENT KE SERVER ALPINE"
echo "=========================================="

# 1. Install Dependencies Dasar
echo "[1/7] Menginstall paket sistem (Docker, Git)..."
apk update
apk add docker docker-cli-compose git openssh nano || true

# 2. Setup Service Docker
echo "[2/7] Menjalankan service Docker..."
rc-update add docker boot || true
service docker start || true

# 3. Setup Direktori Project
echo "[3/7] Menyiapkan folder project..."
mkdir -p /var/www
cd /var/www

# Fix DNS jika diperlukan (kadang Alpine di VM suka hilang DNS)
echo "[3.5/7] Memeriksa DNS..."
if ! ping -c 1 github.com > /dev/null 2>&1; then
    echo "    -> DNS bermasalah (github tak terjangkau). Memaksa Google DNS..."
    echo "nameserver 8.8.8.8" > /etc/resolv.conf
    echo "nameserver 1.1.1.1" >> /etc/resolv.conf
    echo "    -> Mengkonfigurasi Docker DNS (daemon.json)..."
    echo '{ "dns": ["8.8.8.8", "1.1.1.1"] }' > /etc/docker/daemon.json
    echo "    -> Restarting Docker info agar DNS terupdate..."
    service docker restart
    sleep 5
fi

# 4. Clone / Pull Repository
if [ -d "SisfoKK-Sentani" ]; then
    echo "[4/7] Repository ditemukan, melakukan git pull..."
    cd SisfoKK-Sentani
    git pull origin main
else
    echo "[4/7] Repository belum ada, melakukan git clone..."
    git clone https://github.com/arcx2004-cyber/SisfoKK-Sentani.git
    cd SisfoKK-Sentani
fi

# 5. Konfigurasi Environment (.env) Otomatis
echo "[5/7] Mengkonfigurasi file .env..."

# Backend .env
if [ ! -f backend/.env ]; then
    echo "    -> Membuat backend/.env dari example"
    cp backend/.env.example backend/.env
    
    # Adjust konfigurasi untuk Production/Server IP
    # Mengganti APP_URL ke IP Server (Port 80)
    sed -i 's/APP_URL=http:\/\/localhost:8001/APP_URL=http:\/\/163.61.58.190/g' backend/.env
    # Mengganti APP_URL localhost:8080 (jika ada)
    sed -i 's/APP_URL=http:\/\/localhost:8080/APP_URL=http:\/\/163.61.58.190/g' backend/.env
    
    # Pastikan DB Host mengarah ke container mysql
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=mysql/g' backend/.env
    
    # Setup Superadmin Password (default)
    sed -i 's/SUPER_ADMIN_PASSWORD=SisfoKK2024!/SUPER_ADMIN_PASSWORD=V1taku2014@2025/g' backend/.env
fi

# Frontend .env
if [ ! -f frontend/.env ]; then
    echo "    -> Membuat frontend/.env dari example"
    cp frontend/.env.example frontend/.env
    
    # Pointing API ke Server IP
    sed -i 's/PUBLIC_API_URL=http:\/\/localhost:8001/PUBLIC_API_URL=http:\/\/163.61.58.190:8080/g' frontend/.env
    sed -i 's/PUBLIC_API_URL=http:\/\/localhost:8080/PUBLIC_API_URL=http:\/\/163.61.58.190:8080/g' frontend/.env
fi

# 6. Build & Run Docker Containers
echo "[6/7] Membangun dan menjalankan Container..."
docker compose down || true
docker compose up -d --build --remove-orphans

# 7. Post-Deployment Tasks (Migrations, Cache, Assets)
echo "[7/7] Finalisasi (Migrate DB & Clear Cache)..."
echo "    -> Menunggu database siap (15 detik)..."
sleep 15

echo "    -> Menjalankan migrasi database..."
docker compose exec -T php php artisan migrate --force

echo "    -> Membersihkan cache..."
docker compose exec -T php php artisan optimize:clear

echo "    -> Publish assets Filament..."
docker compose exec -T php php artisan filament:assets

echo "    -> Fix permissions..."
docker compose exec -T php chmod -R 777 storage bootstrap/cache

echo "=========================================="
echo "✅ DEPLOYMENT SUKSES!"
echo "🌐 Website: http://163.61.58.190:8080"
echo "🔑 Admin: http://163.61.58.190:8080/admin"
echo "=========================================="
