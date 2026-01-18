# Catatan Teknis & Workflow Sistem SisfoKK Sentani

Dokumen ini berisi rangkuman teknis, alur kerja (workflow), dan panduan pengembangan untuk sistem Informasi Sekolah Kalam Kudus (SisfoKK). Dokumen ini bertujuan memudahkan pengembangan fitur baru di masa depan.

## 1. Arsitektur & Teknologi

*   **Framework**: Laravel 10/11
*   **Admin Panel**: FilamentPHP v3
*   **Livewire**: v3 (Untuk interaktivitas dinamis)
*   **Database**: MySQL
*   **Permissions**: `spatie/laravel-permission` & `bezhan-salleh/filament-shield`

### Konsep Inti
Sistem ini dibangun dengan pendekatan **Monolith** berbasis Filament Resources. Halaman-halaman admin digenerate otomatis oleh Filament, namun banyak logika kustom yang ditanamkan melalui:
*   **BaseResource**: Parent class untuk semua Resource agar memiliki logika permission yang seragam (`app/Filament/Resources/BaseResource.php`).
*   **Custom Pages**: Halaman manual untuk fitur kompleks seperti "Cetak Kartu Ujian" atau "Cetak Rapor".
*   **Widgets**: Komponen dashboard untuk statistik.

---

## 2. Struktur Direktori Penting

*   `app/Filament/Resources/`: Berisi CRUD utama (Siswa, Guru, Mapel, dll).
*   `app/Filament/Pages/`: Berisi halaman kustom (non-CRUD) seperti Laporan atau Tools.
*   `app/Filament/Widgets/`: Widget dashboard.
*   `app/Models/`: Model Eloquent (Database).
*   `app/Providers/Filament/AdminPanelProvider.php`: Konfigurasi utama panel admin (Warna, Font, CSS Override).
*   `resources/views/filament/`: View kustom (Blade) untuk Page atau Widget khusus.
*   `resources/views/print/`: Template khusus untuk cetak PDF (Kartu Ujian, Rapor).

---

## 3. Workflow Utama

### A. Manajemen Pengguna & Role
Sistem menggunakan **Role-Based Access Control (RBAC)**.
*   **Super Admin**: Akses penuh.
*   **Kepsek**: Akses `read-only` ke hampir semua data, akses `write` untuk persetujuan (RAPBS).
*   **PTK (Guru)**: Hanya melihat data yang relevan dengan dirinya (Jadwal, Nilai Siswanya).
*   **Siswa/Wali**: Hanya melihat data pribadi (Rapor, Tagihan).

> **Penting**: Saat membuat Resource baru, pastikan `extend BaseResource` agar permission otomatis terapkan, atau definisikan `canViewAny` secara manual.

### B. Alur Akademik (Kurikulum Merdeka)
1.  **Data Master**: Admin input Tahun Ajaran, Semester, Kelas, Mapel.
2.  **Pembagian Kelas (Rombel)**: Siswa dimasukkan ke Rombel.
3.  **KBM**:
    *   Guru ditugaskan ke Mapel & Rombel tertentu (`JadwalPelajaran`).
    *   Tujuan Pembelajaran (TP) diinput per tingkat/fase.
4.  **Penilaian**:
    *   Guru menginput nilai TP (Formatif/Sumatif) di `InputNilaiResource`.
    *   Data disimpan di tabel `niilais` (atau sejenisnya).
5.  **Rapor**:
    *   Sistem menghitung rata-rata & deskripsi capaian.
    *   Cetak Rapor (STS/SAS) melalui Halaman `CetakRapor`.

### C. Alur Keuangan
1.  **Tarif**: Admin set tarif SPP (`TarifSpp`) dan Kegiatan (`TarifKegiatan`) per Tahun Ajaran/Unit.
2.  **Tagihan**: Tagihan digenerate otomatis atau di-cek `on-the-fly` berdasarkan relasi Siswa ke Unit/Tingkat.
3.  **Pembayaran**:
    *   Pembayaran dicatat di `PembayaranSpp` / `PembayaranKegiatan`.
    *   Validasi "Lunas" dilakukan dengan membandingkan `Total Bayar` vs `Tarif`.
4.  **RAPBS (Anggaran)**:
    *   Kepsek/Bendahara buat Draft RAPBS.
    *   Diajukan -> Disetujui Direktur.

### D. PPDB (Penerimaan Peserta Didik Baru)
1.  Calon Siswa mendaftar (Frontend Public).
2.  Data masuk ke `PendaftaranResource`.
3.  Admin memverifikasi berkas.
4.  **Konversi**: Jika diterima, satu klik tombol "Terima" akan:
    *   Membuat user akun untuk siswa.
    *   Membuat data `Siswa` baru.
    *   Mengirim notifikasi (opsional/future).

---

## 4. Panduan Pengembangan (How-To)

### Menambah Fitur Baru (CRUD)
Jalankan perintah:
```bash
php artisan make:filament-resource NamaFitur --generate
```
Lalu edit file di `app/Filament/Resources/NamaFiturResource.php`.
**Tips**: Tambahkan `$navigationIcon` dan `$navigationGroup` agar rapi di sidebar.

### Menambah Halaman Kustom (Custom Page)
Jalankan perintah:
```bash
php artisan make:filament-page NamaHalaman
```
Ini berguna jika fitur butuh form kompleks yang bukan sekadar tabel database (contoh: Form Filter Laporan).

### Mengatur Ikon Sidebar
Jika ikon tidak muncul (terutama di dalam Grup), ingat bahwa kita telah melakukan **Override View** pada:
`resources/views/vendor/filament-panels/components/sidebar/group.blade.php`.
Pastikan logika di sana tetap mengizinkan child item memiliki ikon.

### Debugging Tampilan
Jika ada perubahan CSS atau Ikon yang tidak nampak:
```bash
php artisan filament:optimize-clear
php artisan view:clear
```

### Deployment
Pastikan menjalankan:
1. `composer install`
2. `php artisan migrate`
3. `php artisan db:seed --class=RolesAndPermissionsSeeder` (Jika ada permission baru)
4. `php artisan storage:link` (Untuk gambar/file)

---

## 5. Rencana Pengembangan Mendatang (To-Do)
*   [ ] **E-Rapor SMP**: Menyesuaikan format cetak rapor untuk unit SMP.
*   [ ] **Notifikasi WA**: Integrasi API WhatsApp untuk tagihan/pengumuman.
*   [ ] **Sistem Alumni**: Tracking lulusan.

---
*Dibuat oleh AI Assistant - Update Terakhir: Januari 2026*
