# Panduan Deployment ke Alpine Linux Server via GitHub Actions

Panduan ini akan membantu Anda mengatur server Alpine Linux agar website otomatis terupdate setiap kali Anda melakukan push ke GitHub.

## 1. Persiapan Server (Alpine Linux)

Login ke server Alpine Anda via SSH, lalu jalankan perintah berikut:

### A. Install Dependensi
```bash
# Update repository
apk update

# Install Docker, Docker Compose, Git, dan Nano (editor)
apk add docker docker-cli-compose git nano openssh

# Jalankan Docker saat booting
rc-update add docker boot

# Start service Docker
service docker start
```

### B. Clone Repository
Kita perlu men-download kodingan awal manual sekali saja.
```bash
# Masuk ke folder home atau var/www (sesuai preferensi)
cd ~

# Clone repo Anda
git clone https://github.com/arcx2004-cyber/SisfoKK-Sentani.git

# Masuk ke folder project
cd SisfoKK-Sentani

# Setup file environment
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
```
> **PENTING**: Edit file `.env` di backend dan frontend sesuai konfigurasi server Anda (Database password, Domain URL, dll). Gunakan `nano backend/.env`.

### C. Build Awal
Jalankan build manual pertama kali untuk memastikan semua berjalan lancar.
```bash
docker compose up -d --build
```

## 2. Setup SSH Key untuk GitHub Actions

Agar GitHub bisa masuk ke server Anda untuk melakukan update, kita perlu SSH Key.

### A. Di Server Alpine (Membuat Key Pair)
Jalankan command ini di server:
```bash
# Generate key baru (tekan Enter terus untuk default)
ssh-keygen -t rsa -b 4096 -C "github-actions"

# Tambahkan public key ke authorized_keys agar bisa login
cat ~/.ssh/id_rsa.pub >> ~/.ssh/authorized_keys

# Tampilkan Private Key (Copy isi dari -----BEGIN sampai -----END)
cat ~/.ssh/id_rsa
```

### B. Di GitHub Repository
1. Buka Repo SisfoKK-Sentani di GitHub.
2. Masuk ke **Settings** > **Secrets and variables** > **Actions**.
3. Klik **New repository secret**.
4. Tambahkan secret berikut:
    - `SERVER_HOST`: IP Address server Alpine Anda.
    - `SERVER_USER`: Username server (misal: `root` atau `arcx`).
    - `SSH_PRIVATE_KEY`: Paste isi Private Key yang tadi Anda copy dari server.

## 3. GitHub Actions Workflow

File workflow akan otomatis dibuatkan di `.github/workflows/deploy.yml`. 
Setiap kali Anda push ke branch `main`, GitHub akan:
1. Login ke server Anda via SSH.
2. Masuk ke folder project.
3. Pull perubahan terbaru dari git.
4. Rebuild container jika ada perubahan config/dockerfile.
5. Menjalankan migrasi database.
