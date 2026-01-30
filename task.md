# Roadmap Pengembangan & Maintenance Website SKKK Sentani

## 1. Infrastruktur & Deployment 🚀
- [x] Setup VPS Alpine Linux (Docker & Git installed)
- [x] Konfigurasi SSH & Security (Key-based login)
- [x] Setup Containerization (Docker Compose: Nginx, PHP, MySQL, Redis, Node)
- [x] Konfigurasi Domain skkksentani.web.id
- [x] Setup SSL/HTTPS (via Nginx/Certbot)
- [x] Script Deployment Otomatis (deploy.sh)
- [x] Implementasi CI/CD Auto Deployment (GitHub Actions)
- [x] Setup Backup Otomatis Database & Storage (Cron Job Active)

## 2. Backend Development (Laravel + Filament) 🛠️
- [x] Setup Core Framework & Database Connection
- [x] Implementasi Role & Permission (Fix Admin Access)
- [x] Modul Manajemen Berita/Artikel (Rich Editor Updated)
- [x] Modul Galeri & Media (Multiple Upload & Repeater Implemented)
- [x] Modul Profil Sekolah & Visi Misi (Rich Editor Implemented)
- [x] Modul PPDB (Penerimaan Peserta Didik Baru)
    - [x] Form Pendaftaran (Wizard Step)
    - [x] Upload Dokumen (Akta, KK, Ijazah, Pas Foto)
    - [x] Fitur 'Terima Siswa' (Auto Create User, Siswa & Avatar)
- [x] API Endpoint untuk Frontend Public
- [ ] Optimasi Query & Caching (Redis)

## 3. Frontend Development (Vite Client) 🎨
- [x] Setup Vite & Basic Routing
- [x] Halaman Beranda (Home - *Dynamic Sliders & News*)
- [x] Halaman Profil & Sejarah (*Dynamic Content - Layout Fixed*)
- [x] Halaman Berita & Kegiatan (*List, Detail View & Calendar Refined*)
- [x] Halaman Galeri Foto/Video (*Dynamic Album & Grid*)
- [x] Halaman Kontak & Lokasi (*Form Submission & Map Restoration*)
- [x] Halaman Pendaftaran PPDB Online (Form & Photo Upload)
- [x] Responsive Design (Mobile Friendly)

## 4. Konten & Data 📝
- [ ] Migrasi Data Lama (jika ada)
- [x] Input Data Awal (Seeding: User Admin, Profil Sekolah)
- [x] Testing & Quality Assurance (QA - Phase 1 Complete)

## 5. Maintenance & Monitoring 🛡️
- [ ] Monitoring Server Resource (CPU/RAM)
- [ ] Log Monitoring (Error logs)
- [ ] Update Security Patch Berkala

## 6. Selesaikan Isu Spesifik 🐞
- [x] Investigasi Modul RAPBS yang Hilang
    - [x] Cek Database & File System
    - [x] Identifikasi Masalah Visibility (Fix logic di `RapbsResource.php`)
    - [x] Deploy Perbaikan & Verifikasi
- [x] Modul Impor Data Siswa
    - [x] Perencanaan (CSV Parser approach)
    - [x] Pembuatan Template Excel/CSV (Built-in to Action)
    - [x] Implementasi Backend Action (`SiswaResource.php`)
    - [x] Testing & Verifikasi

*Last Updated: 2026-01-30 12:40*
