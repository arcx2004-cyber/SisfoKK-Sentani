<!DOCTYPE html>
<html>
<head>
    <title>RPP - {{ $mapel->nama }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }
        .header img {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
            height: auto;
        }
        .header .school-info {
            margin: 0 90px;
        }
        .header h2 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 10pt;
        }
        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            font-size: 14pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .content {
            text-align: justify;
        }
        .content h1, .content h2, .content h3 {
            font-size: 12pt;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .signature-table {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            vertical-align: top;
            width: 33%;
        }
        .signature-name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    @php
        $logo = \App\Models\SchoolSetting::get('logo');
        $namaSekolah = \App\Models\SchoolSetting::get('nama_sekolah', 'SEKOLAH CONTOH');
        $alamat = \App\Models\SchoolSetting::get('alamat', 'Jl. Contoh No. 1, Kota Contoh');
        $kepsek = \App\Models\SchoolSetting::get('kepala_sekolah', 'Nama Kepsek');
        $nipKepsek = \App\Models\SchoolSetting::get('nip_kepala_sekolah', '.......................');
    @endphp

    <div class="header">
        @if($logo)
            <img src="{{ public_path('storage/' . $logo) }}" alt="Logo">
        @endif
        <div class="school-info">
            <h2>{{ $namaSekolah }}</h2>
            <p>{{ $alamat }}</p>
        </div>
    </div>

    <div class="title">
        RENCANA PELAKSANAAN PEMBELAJARAN (RPP)
    </div>

    <table class="info-table">
        <tr>
            <td width="20%">Mata Pelajaran</td>
            <td width="2%">:</td>
            <td>{{ $mapel->nama }}</td>
            <td width="20%">Kelas/Fase</td>
            <td width="2%">:</td>
            <td>{{ $cp->kelas }} / {{ $cp->fase ?? '-' }}</td>
        </tr>
        <tr>
            <td>Guru Pengampu</td>
            <td>:</td>
            <td>{{ $guru->nama_lengkap }}</td>
            <td>Semester</td>
            <td>:</td>
            <td>{{ $cp->semester->tipe ?? 'Ganjil' }}</td>
        </tr>
        <tr>
             <td>Materi Pokok</td>
             <td>:</td>
             <td>{{ $cp->kode }}</td>
             <td>Alokasi Waktu</td>
             <td>:</td>
             <td>2 JP x 40 Menit</td>
        </tr>
    </table>

    <div class="content">
        {!! Str::markdown($rpp->konten_rpp) !!}
    </div>

    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala Sekolah
                <div class="signature-name">{{ $kepsek }}</div>
                NIP. {{ $nipKepsek }}
            </td>
            <td></td>
            <td>
                Sentani, {{ now()->translatedFormat('d F Y') }}<br>
                Guru Mata Pelajaran
                <div class="signature-name">{{ $guru->nama_lengkap }}</div>
                NIP. {{ $guru->nip ?? '.......................' }}
            </td>
        </tr>
    </table>
</body>
</html>
