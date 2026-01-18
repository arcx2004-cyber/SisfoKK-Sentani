<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitLandingSeeder extends Seeder
{
    public function run(): void
    {
        // TK Kalam Kudus
        Unit::where('kode', 'TK')->update([
            'sekilas' => 'TK Kalam Kudus Sentani adalah tempat pertama bagi putra-putri Anda memulai perjalanan pendidikan dengan landasan iman Kristiani. Dengan pendekatan bermain sambil belajar, kami membantu anak-anak mengembangkan potensi terbaik mereka.',
            'konten' => '<p>Taman Kanak-Kanak Kalam Kudus Sentani didirikan dengan visi menyediakan pendidikan anak usia dini yang berkualitas, menyenangkan, dan berbasis nilai-nilai Kristiani.</p>
<h3>Keunggulan TK Kami</h3>
<ul>
<li><strong>Pembelajaran Tematik</strong> - Kurikulum yang dirancang sesuai tahap perkembangan anak</li>
<li><strong>Kelas Kecil</strong> - Maksimal 20 siswa per kelas dengan 2 guru pendamping</li>
<li><strong>Fasilitas Bermain</strong> - Area bermain indoor dan outdoor yang aman</li>
<li><strong>Program Karakter</strong> - Pembentukan karakter Kristiani sejak dini</li>
</ul>',
            'kepala_sekolah' => 'Ibu Maria Saragih, S.Pd',
            'visi' => 'Menjadi TK Kristen unggulan yang menghasilkan anak-anak cerdas, kreatif, dan berkarakter mulia berdasarkan kasih Kristus.',
            'misi' => '1. Menyelenggarakan pembelajaran yang menyenangkan dan bermakna
2. Mengembangkan kreativitas dan kemandirian anak
3. Menanamkan nilai-nilai iman Kristiani dalam setiap aktivitas
4. Membangun kerjasama yang baik dengan orang tua
5. Menyediakan lingkungan belajar yang aman dan nyaman',
            'fasilitas' => 'Ruang Kelas Ber-AC
Area Bermain Indoor
Playground Outdoor
Ruang Musik
Perpustakaan Mini
Kantin Sehat
UKS
Toilet Ramah Anak
Area Parkir Luas',
            'jam_belajar' => '07:30 - 11:00 WIT',
            'telepon' => '(0967) 123456 ext. 101',
            'email' => 'tk@sisfokk.sch.id',
        ]);

        // SD Kalam Kudus
        Unit::where('kode', 'SD')->update([
            'sekilas' => 'SD Kalam Kudus Sentani memberikan pendidikan dasar 6 tahun dengan kurikulum terpadu yang memadukan keunggulan akademik dan pembentukan karakter Kristiani. Kami berkomitmen menghasilkan lulusan yang cerdas, beriman, dan siap melanjutkan ke jenjang berikutnya.',
            'konten' => '<p>Sekolah Dasar Kalam Kudus Sentani berdiri sejak tahun 1985 dan telah meluluskan ribuan alumni yang tersebar di berbagai profesi.</p>
<h3>Program Unggulan</h3>
<ul>
<li><strong>Kurikulum Merdeka</strong> - Pembelajaran yang berpusat pada siswa</li>
<li><strong>English Program</strong> - Penguatan kemampuan Bahasa Inggris</li>
<li><strong>IT Literacy</strong> - Pengenalan teknologi sejak kelas 1</li>
<li><strong>Character Building</strong> - Program pembentukan karakter mingguan</li>
<li><strong>Ekstrakurikuler</strong> - Pramuka, Paduan Suara, Robotik, Seni Tari</li>
</ul>
<h3>Prestasi Terkini</h3>
<p>Siswa kami meraih berbagai prestasi di tingkat kabupaten dan provinsi dalam bidang akademik maupun non-akademik.</p>',
            'kepala_sekolah' => 'Bapak Daniel Wospakrik, S.Pd, M.M',
            'visi' => 'Menjadi Sekolah Dasar Kristen yang unggul dalam iman, ilmu pengetahuan, dan teknologi, serta menghasilkan lulusan yang berkarakter Kristus.',
            'misi' => '1. Melaksanakan pembelajaran berkualitas berbasis Kurikulum Merdeka
2. Mengintegrasikan nilai-nilai Kristiani dalam setiap mata pelajaran
3. Mengembangkan potensi siswa melalui kegiatan ekstrakurikuler
4. Membangun budaya literasi dan cinta buku
5. Menerapkan teknologi dalam pembelajaran
6. Menjalin kemitraan dengan orang tua dan masyarakat',
            'fasilitas' => 'Ruang Kelas Ber-AC dengan LCD Proyektor
Laboratorium IPA
Laboratorium Komputer
Perpustakaan
Ruang Musik
Lapangan Olahraga
Aula Serbaguna
Kantin
UKS
Musholla
Toilet Modern
Area Parkir',
            'jam_belajar' => '07:00 - 13:00 WIT',
            'telepon' => '(0967) 123456 ext. 102',
            'email' => 'sd@sisfokk.sch.id',
        ]);

        // SMP Kalam Kudus
        Unit::where('kode', 'SMP')->update([
            'sekilas' => 'SMP Kalam Kudus Sentani mempersiapkan siswa untuk menjadi remaja yang tangguh, cerdas, dan berintegritas. Dengan kombinasi akademik yang kuat dan pengembangan soft skills, lulusan kami siap menghadapi tantangan di jenjang pendidikan selanjutnya.',
            'konten' => '<p>Sekolah Menengah Pertama Kalam Kudus Sentani fokus pada pengembangan akademik sekaligus pembentukan karakter remaja yang tangguh.</p>
<h3>Program Unggulan</h3>
<ul>
<li><strong>Kurikulum Merdeka</strong> - Pembelajaran berbasis proyek dan kompetensi</li>
<li><strong>Bimbingan Konseling</strong> - Pendampingan personal untuk setiap siswa</li>
<li><strong>English & Computer Program</strong> - Penguatan kemampuan abad 21</li>
<li><strong>Leadership Program</strong> - Pengembangan jiwa kepemimpinan</li>
<li><strong>Life Skills</strong> - Keterampilan praktis untuk kehidupan</li>
</ul>
<h3>Tata Tertib & Kedisiplinan</h3>
<p>Kami menerapkan tata tertib yang tegas namun penuh kasih untuk membentuk kedisiplinan dan tanggung jawab siswa.</p>',
            'kepala_sekolah' => 'Bapak Samuel Yoman, S.Pd, M.Pd',
            'visi' => 'Menjadi SMP Kristen terdepan yang menghasilkan generasi muda berintegritas, berprestasi, dan siap menjadi berkat bagi bangsa.',
            'misi' => '1. Menyelenggarakan pendidikan berkualitas tinggi berbasis Kurikulum Merdeka
2. Mengembangkan kemampuan berpikir kritis dan kreatif siswa
3. Membentuk karakter Kristiani melalui pembinaan rohani rutin
4. Mempersiapkan siswa untuk kompetisi akademik tingkat nasional
5. Mengembangkan bakat melalui ekstrakurikuler yang beragam
6. Membangun jejaring alumni yang kuat',
            'fasilitas' => 'Ruang Kelas Ber-AC dengan Smart TV
Laboratorium IPA (Fisika, Kimia, Biologi)
Laboratorium Komputer dengan Internet
Laboratorium Bahasa
Perpustakaan Digital
Ruang Musik & Seni
Lapangan Basket & Futsal
Aula Serbaguna
Kantin Sehat
UKS Lengkap
Ruang BK
Wifi Zone',
            'jam_belajar' => '06:45 - 14:00 WIT',
            'telepon' => '(0967) 123456 ext. 103',
            'email' => 'smp@sisfokk.sch.id',
        ]);

        $this->command->info('✅ Unit landing page content berhasil di-seed!');
    }
}
