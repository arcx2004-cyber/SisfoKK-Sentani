<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Page;
use App\Models\Slider;
use App\Models\Gallery;
use App\Models\GalleryPhoto;
use App\Models\Kegiatan;
use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // ===== SCHOOL SETTINGS =====
        SchoolSetting::updateOrCreate(['key' => 'nama_sekolah'], ['value' => 'Sekolah Kristen Kalam Kudus', 'type' => 'text']);
        SchoolSetting::updateOrCreate(['key' => 'nama_pendek'], ['value' => 'SKKK Sentani', 'type' => 'text']);
        SchoolSetting::updateOrCreate(['key' => 'motto'], ['value' => 'Dengan Kasih & Disiplin Meningkatkan Prestasi', 'type' => 'text']);
        SchoolSetting::updateOrCreate(['key' => 'alamat'], ['value' => 'Jl. Raya Sentani, Sentani, Jayapura, Papua 99352', 'type' => 'text']);
        SchoolSetting::updateOrCreate(['key' => 'telepon'], ['value' => '(0967) 123456', 'type' => 'text']);
        SchoolSetting::updateOrCreate(['key' => 'email'], ['value' => 'info@sisfokk.sch.id', 'type' => 'text']);
        SchoolSetting::updateOrCreate(['key' => 'whatsapp'], ['value' => '62967123456', 'type' => 'text']);

        // ===== SLIDERS =====
        // Fields: judul, deskripsi, gambar, link, urutan, is_active
        Slider::updateOrCreate(
            ['judul' => 'Selamat Datang di SKKK Sentani'],
            [
                'deskripsi' => 'Pendidikan berkualitas berbasis iman Kristiani',
                'gambar' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=1200&h=500&fit=crop',
                'link' => '/ppdb',
                'urutan' => 1,
                'is_active' => true,
            ]
        );
        Slider::updateOrCreate(
            ['judul' => 'PPDB 2026/2027 Dibuka'],
            [
                'deskripsi' => 'Daftarkan putra-putri Anda sekarang untuk bergabung bersama kami',
                'gambar' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1200&h=500&fit=crop',
                'link' => '/ppdb',
                'urutan' => 2,
                'is_active' => true,
            ]
        );
        Slider::updateOrCreate(
            ['judul' => 'Prestasi Gemilang'],
            [
                'deskripsi' => 'Siswa kami meraih berbagai prestasi tingkat nasional',
                'gambar' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=1200&h=500&fit=crop',
                'link' => '/berita',
                'urutan' => 3,
                'is_active' => true,
            ]
        );

        // ===== NEWS / BERITA =====
        // Fields: judul, slug, ringkasan, konten, featured_image, kategori, status, created_by, published_at, views
        News::updateOrCreate(
            ['slug' => 'ppdb-2026-2027-resmi-dibuka'],
            [
                'judul' => 'PPDB 2026/2027 Resmi Dibuka',
                'ringkasan' => 'Pendaftaran Peserta Didik Baru untuk tahun ajaran 2026/2027 telah resmi dibuka. Segera daftarkan putra-putri Anda.',
                'konten' => '<p>Pendaftaran Peserta Didik Baru (PPDB) untuk tahun ajaran 2026/2027 telah resmi dibuka oleh Sekolah Kristen Kalam Kudus Sentani.</p>
<p>Pendaftaran dapat dilakukan secara online melalui website resmi sekolah. Calon peserta didik dapat mendaftar untuk jenjang:</p>
<ul>
<li>TK (Taman Kanak-Kanak)</li>
<li>SD (Sekolah Dasar)</li>
<li>SMP (Sekolah Menengah Pertama)</li>
</ul>
<p>Persyaratan pendaftaran meliputi:</p>
<ul>
<li>Fotokopi Akta Kelahiran</li>
<li>Fotokopi Kartu Keluarga</li>
<li>Pas foto 3x4 (2 lembar)</li>
<li>Ijazah/Surat Keterangan Lulus (untuk SD dan SMP)</li>
</ul>',
                'featured_image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&h=400&fit=crop',
                'kategori' => 'Pengumuman',
                'status' => 'published',
                'published_at' => '2026-01-12',
                'views' => 150,
            ]
        );
        News::updateOrCreate(
            ['slug' => 'siswa-raih-medali-olimpiade-sains'],
            [
                'judul' => 'Siswa Raih Medali Olimpiade Sains',
                'ringkasan' => 'Siswa SMP Kalam Kudus berhasil meraih medali emas dalam Olimpiade Sains Nasional tingkat provinsi Papua.',
                'konten' => '<p>Prestasi membanggakan diraih oleh siswa SMP Kalam Kudus Sentani dalam ajang Olimpiade Sains Nasional (OSN) tingkat Provinsi Papua.</p>
<p>Siswa bernama <strong>Jonathan Wambrauw</strong> dari kelas IX berhasil meraih medali emas untuk bidang Matematika.</p>
<p>Kepala Sekolah SMP Kalam Kudus menyampaikan rasa bangga atas prestasi yang diraih siswa tersebut.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=800&h=400&fit=crop',
                'kategori' => 'Prestasi',
                'status' => 'published',
                'published_at' => '2026-01-08',
                'views' => 230,
            ]
        );
        News::updateOrCreate(
            ['slug' => 'perayaan-natal-bersama'],
            [
                'judul' => 'Perayaan Natal Bersama',
                'ringkasan' => 'Seluruh civitas akademika merayakan Natal bersama dengan penuh sukacita.',
                'konten' => '<p>Sekolah Kristen Kalam Kudus Sentani menggelar perayaan Natal bersama yang dihadiri seluruh civitas akademika.</p>
<p>Acara diawali dengan ibadah syukur, dilanjutkan dengan penampilan paduan suara dari masing-masing unit (TK, SD, dan SMP).</p>
<p>Acara ditutup dengan tukar kado dan makan bersama dalam suasana kekeluargaan yang hangat.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&h=400&fit=crop',
                'kategori' => 'Kegiatan',
                'status' => 'published',
                'published_at' => '2025-12-25',
                'views' => 180,
            ]
        );
        News::updateOrCreate(
            ['slug' => 'pelatihan-guru-kurikulum-merdeka'],
            [
                'judul' => 'Pelatihan Guru Kurikulum Merdeka',
                'ringkasan' => 'Para guru mengikuti pelatihan intensif implementasi Kurikulum Merdeka.',
                'konten' => '<p>Seluruh guru Sekolah Kristen Kalam Kudus Sentani mengikuti pelatihan implementasi Kurikulum Merdeka selama 3 hari.</p>
<p>Materi yang disampaikan meliputi:</p>
<ul>
<li>Filosofi Kurikulum Merdeka</li>
<li>Pembelajaran Berdiferensiasi</li>
<li>Asesmen Formatif dan Sumatif</li>
<li>Proyek Penguatan Profil Pelajar Pancasila</li>
</ul>',
                'featured_image' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&h=400&fit=crop',
                'kategori' => 'Kegiatan',
                'status' => 'published',
                'published_at' => '2025-11-25',
                'views' => 95,
            ]
        );
        News::updateOrCreate(
            ['slug' => 'renovasi-perpustakaan-selesai'],
            [
                'judul' => 'Renovasi Perpustakaan Selesai',
                'ringkasan' => 'Perpustakaan sekolah telah selesai direnovasi dengan fasilitas baru.',
                'konten' => '<p>Perpustakaan Sekolah Kristen Kalam Kudus Sentani telah selesai direnovasi dengan berbagai fasilitas baru yang lebih modern.</p>
<p>Fasilitas baru meliputi:</p>
<ul>
<li>Ruang baca digital dengan 10 unit komputer</li>
<li>Area diskusi kelompok</li>
<li>Koleksi buku yang diperbanyak hingga 5000 judul</li>
<li>E-library access</li>
</ul>',
                'featured_image' => 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=800&h=400&fit=crop',
                'kategori' => 'Pengumuman',
                'status' => 'published',
                'published_at' => '2025-11-01',
                'views' => 120,
            ]
        );

        // ===== PAGES =====
        // Fields: judul, slug, konten, featured_image, status, created_by, published_at
        Page::updateOrCreate(
            ['slug' => 'profile'],
            [
                'judul' => 'Profile Sekolah',
                'konten' => '<h2>Tentang SKKK Sentani</h2>
<p>Sekolah Kristen Kalam Kudus Sentani didirikan dengan visi untuk menyediakan pendidikan berkualitas berbasis iman Kristiani di wilayah Sentani, Papua.</p>
<p>Dengan motto <strong>"Dengan Kasih & Disiplin Meningkatkan Prestasi"</strong>, kami percaya bahwa pendidikan yang baik harus dilandasi oleh kasih sayang sekaligus disiplin yang membangun karakter.</p>
<h3>Nilai-Nilai yang Kami Tanamkan</h3>
<ul>
<li><strong>Iman</strong> - Berpegang teguh pada iman Kristiani</li>
<li><strong>Kasih</strong> - Mendidik dengan kasih yang tulus</li>
<li><strong>Disiplin</strong> - Membangun karakter melalui kedisiplinan</li>
<li><strong>Prestasi</strong> - Mendorong siswa mencapai prestasi terbaik</li>
</ul>',
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        Page::updateOrCreate(
            ['slug' => 'visi-misi'],
            [
                'judul' => 'Visi & Misi',
                'konten' => '<h2>Visi Sekolah</h2>
<blockquote>"Menjadi sekolah Kristen yang unggul dalam iman, ilmu, dan karakter, menghasilkan generasi yang takut akan Tuhan dan berdampak bagi masyarakat."</blockquote>
<h2>Misi Sekolah</h2>
<ol>
<li><strong>Pendidikan Berbasis Iman</strong> - Mengintegrasikan iman Kristiani dalam setiap aspek pembelajaran.</li>
<li><strong>Pengembangan Akademik</strong> - Melaksanakan pembelajaran berkualitas tinggi.</li>
<li><strong>Pembentukan Karakter</strong> - Membentuk siswa yang berkarakter mulia.</li>
<li><strong>Pengembangan Bakat</strong> - Mengembangkan potensi melalui kegiatan ekstrakurikuler.</li>
<li><strong>Tenaga Pendidik Profesional</strong> - Menyediakan guru yang kompeten.</li>
<li><strong>Fasilitas Pembelajaran</strong> - Menyediakan fasilitas yang kondusif.</li>
</ol>
<h2>Motto</h2>
<p><strong>"Dengan Kasih & Disiplin Meningkatkan Prestasi"</strong></p>',
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        Page::updateOrCreate(
            ['slug' => 'unit-tk'],
            [
                'judul' => 'TK Kalam Kudus',
                'konten' => '<h2>Taman Kanak-Kanak Kalam Kudus</h2>
<p>TK Kalam Kudus menyediakan pendidikan anak usia dini dengan pendekatan bermain sambil belajar.</p>
<h3>Program Pembelajaran</h3>
<ul>
<li>Kurikulum PAUD Merdeka</li>
<li>Pendidikan Karakter Kristiani</li>
<li>Pengembangan Motorik Halus & Kasar</li>
<li>Kegiatan Ekstrakurikuler</li>
</ul>
<h3>Usia Penerimaan</h3>
<p>4-6 tahun</p>',
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        Page::updateOrCreate(
            ['slug' => 'unit-sd'],
            [
                'judul' => 'SD Kalam Kudus',
                'konten' => '<h2>Sekolah Dasar Kalam Kudus</h2>
<p>SD Kalam Kudus menyediakan pendidikan dasar 6 tahun dengan kurikulum terpadu.</p>
<h3>Program Pembelajaran</h3>
<ul>
<li>Kurikulum Merdeka</li>
<li>Pembelajaran Aktif & Kreatif</li>
<li>English Program</li>
<li>Laboratorium & Perpustakaan</li>
</ul>
<h3>Jenjang Kelas</h3>
<p>Kelas 1 - 6</p>',
                'status' => 'published',
                'published_at' => now(),
            ]
        );
        Page::updateOrCreate(
            ['slug' => 'unit-smp'],
            [
                'judul' => 'SMP Kalam Kudus',
                'konten' => '<h2>Sekolah Menengah Pertama Kalam Kudus</h2>
<p>SMP Kalam Kudus menyediakan pendidikan menengah pertama dengan pengembangan akademik dan life skills.</p>
<h3>Program Pembelajaran</h3>
<ul>
<li>Kurikulum Merdeka</li>
<li>Bimbingan Konseling</li>
<li>English & Computer Program</li>
<li>Laboratorium IPA & Komputer</li>
</ul>
<h3>Jenjang Kelas</h3>
<p>Kelas 7 - 9</p>',
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        // ===== GALLERIES =====
        // Fields: judul, deskripsi, kategori, is_active
        $gallery1 = Gallery::updateOrCreate(
            ['judul' => 'Kegiatan Belajar Mengajar'],
            ['deskripsi' => 'Suasana pembelajaran di kelas dengan metode aktif dan interaktif', 'kategori' => 'Akademik', 'is_active' => true]
        );
        GalleryPhoto::updateOrCreate(
            ['gallery_id' => $gallery1->id, 'gambar' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&h=600&fit=crop'],
            ['caption' => 'Suasana belajar di kelas', 'urutan' => 1]
        );

        $gallery2 = Gallery::updateOrCreate(
            ['judul' => 'Upacara Bendera'],
            ['deskripsi' => 'Pembinaan karakter kebangsaan melalui upacara rutin', 'kategori' => 'Kegiatan', 'is_active' => true]
        );
        GalleryPhoto::updateOrCreate(
            ['gallery_id' => $gallery2->id, 'gambar' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=800&h=600&fit=crop'],
            ['caption' => 'Upacara bendera hari Senin', 'urutan' => 1]
        );

        $gallery3 = Gallery::updateOrCreate(
            ['judul' => 'Perayaan Natal'],
            ['deskripsi' => 'Sukacita perayaan Natal bersama seluruh civitas akademika', 'kategori' => 'Kegiatan', 'is_active' => true]
        );
        GalleryPhoto::updateOrCreate(
            ['gallery_id' => $gallery3->id, 'gambar' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&h=600&fit=crop'],
            ['caption' => 'Perayaan Natal bersama', 'urutan' => 1]
        );

        $gallery4 = Gallery::updateOrCreate(
            ['judul' => 'Perpustakaan'],
            ['deskripsi' => 'Fasilitas perpustakaan dengan koleksi buku yang lengkap', 'kategori' => 'Fasilitas', 'is_active' => true]
        );
        GalleryPhoto::updateOrCreate(
            ['gallery_id' => $gallery4->id, 'gambar' => 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=800&h=600&fit=crop'],
            ['caption' => 'Perpustakaan sekolah', 'urutan' => 1]
        );

        $gallery5 = Gallery::updateOrCreate(
            ['judul' => 'Kegiatan Ekstrakurikuler'],
            ['deskripsi' => 'Pengembangan bakat dan minat siswa melalui ekskul', 'kategori' => 'Ekstrakurikuler', 'is_active' => true]
        );
        GalleryPhoto::updateOrCreate(
            ['gallery_id' => $gallery5->id, 'gambar' => 'https://images.unsplash.com/photo-1594608661623-aa0bd3a69d98?w=800&h=600&fit=crop'],
            ['caption' => 'Kegiatan ekstrakurikuler', 'urutan' => 1]
        );

        $gallery6 = Gallery::updateOrCreate(
            ['judul' => 'Wisuda & Kelulusan'],
            ['deskripsi' => 'Momen kelulusan siswa-siswi berprestasi', 'kategori' => 'Kegiatan', 'is_active' => true]
        );
        GalleryPhoto::updateOrCreate(
            ['gallery_id' => $gallery6->id, 'gambar' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800&h=600&fit=crop'],
            ['caption' => 'Wisuda dan kelulusan', 'urutan' => 1]
        );

        // ===== KEGIATAN / EVENTS =====
        // Fields: judul, slug, deskripsi, konten, featured_image, tanggal_mulai, tanggal_selesai, lokasi, status, is_published
        Kegiatan::updateOrCreate(
            ['slug' => 'pembukaan-semester-genap'],
            [
                'judul' => 'Pembukaan Semester Genap',
                'deskripsi' => 'Upacara pembukaan semester genap tahun ajaran 2025/2026',
                'konten' => '<p>Upacara pembukaan semester genap akan dilaksanakan di Lapangan Sekolah.</p><p>Seluruh siswa dan guru wajib hadir tepat waktu.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?w=800&h=400&fit=crop',
                'tanggal_mulai' => '2026-01-15',
                'tanggal_selesai' => '2026-01-15',
                'lokasi' => 'Lapangan Sekolah',
                'status' => 'upcoming',
                'is_published' => true,
            ]
        );
        Kegiatan::updateOrCreate(
            ['slug' => 'rapat-orang-tua-murid'],
            [
                'judul' => 'Rapat Orang Tua Murid',
                'deskripsi' => 'Pertemuan dengan wali murid untuk pembahasan program semester',
                'konten' => '<p>Rapat Orang Tua Murid akan dilaksanakan di Aula Sekolah.</p><p>Agenda rapat meliputi pembahasan program semester dan hasil belajar.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=800&h=400&fit=crop',
                'tanggal_mulai' => '2026-01-20',
                'tanggal_selesai' => '2026-01-20',
                'lokasi' => 'Aula Sekolah',
                'status' => 'upcoming',
                'is_published' => true,
            ]
        );
        Kegiatan::updateOrCreate(
            ['slug' => 'lomba-literasi'],
            [
                'judul' => 'Lomba Literasi',
                'deskripsi' => 'Kompetisi menulis dan membaca untuk semua jenjang',
                'konten' => '<p>Lomba Literasi untuk semua jenjang pendidikan.</p><p>Kategori: Menulis Cerpen, Puisi, dan Lomba Bercerita.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=800&h=400&fit=crop',
                'tanggal_mulai' => '2026-01-25',
                'tanggal_selesai' => '2026-01-25',
                'lokasi' => 'Perpustakaan',
                'status' => 'upcoming',
                'is_published' => true,
            ]
        );
        Kegiatan::updateOrCreate(
            ['slug' => 'field-trip-museum'],
            [
                'judul' => 'Field Trip ke Museum',
                'deskripsi' => 'Kunjungan edukatif ke Museum Loka Budaya Papua',
                'konten' => '<p>Field trip ke Museum Loka Budaya untuk mengenalkan siswa pada kekayaan budaya Papua.</p>',
                'featured_image' => 'https://images.unsplash.com/photo-1594608661623-aa0bd3a69d98?w=800&h=400&fit=crop',
                'tanggal_mulai' => '2026-02-01',
                'tanggal_selesai' => '2026-02-01',
                'lokasi' => 'Museum Loka Budaya, Jayapura',
                'status' => 'upcoming',
                'is_published' => true,
            ]
        );

        $this->command->info('✅ Semua konten sample berhasil di-seed!');
    }
}
