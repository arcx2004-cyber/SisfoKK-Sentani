<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Belajar STS - {{ $siswa->nama_lengkap }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .header h3 {
            margin: 5px 0 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse; /* Ensure no spacing for cleaner look */
        }
        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }
        .info-table .label {
            width: 15%; /* Adjust width for labels */
            font-weight: normal;
        }
        .info-table .colon {
            width: 2%;
            text-align: center;
        }
        .info-table .value {
            width: 40%; /* Adjust based on needs */
            font-weight: bold;
        }
        /* Layout for Info Table similar to image: split left/right */
        .info-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .info-left, .info-right {
            width: 48%;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th, .main-table td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }
        .main-table th {
            background-color: #3b82f6; /* Blue header similar to image */
            color: white;
            font-weight: bold;
        }
        .main-table td.left-align {
            text-align: left;
        }
        .sikap-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }
        .sikap-table, .absensi-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sikap-table th, .sikap-table td, .absensi-table th, .absensi-table td {
            border: 1px solid black;
            padding: 4px;
        }
        .sikap-table th, .absensi-table th {
             background-color: #3b82f6;
             color: white;
        }
        .catatan-box {
            border: 1px solid black;
            padding: 10px;
            margin-bottom: 20px;
            min-height: 50px;
        }
        .catatan-header {
            background-color: #3b82f6;
            color: white;
            padding: 5px;
            font-weight: bold;
            text-align: center;
            border: 1px solid black;
            border-bottom: none;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            text-align: center;
        }
        .signature-box {
            width: 30%;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px dashed black;
            display: inline-block;
            width: 100%;
        }
        .kepsek-container {
            margin-top: 40px;
            text-align: center;
        }
        
        /* Print optimization */
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .main-table th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .catatan-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN HASIL BELAJAR</h2>
        <h3>SUMATIF TENGAH SEMESTER</h3>
    </div>

    <!-- Info Section: Split Left/Right logic using a single table for alignment or flex -->
    <table class="info-table" style="border: none;">
        <tr>
            <!-- Left Info -->
            <td class="label">Nama Siswa</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($siswa->nama_lengkap) }}</td>
            
            <td width="5%"></td> <!-- Spacer -->
            
            <!-- Right Info -->
            <td class="label">Kelas</td>
            <td class="colon">:</td>
            <td class="value">{{ $rombel->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIS / NISN</td>
            <td class="colon">:</td>
            <td class="value">{{ $siswa->nis }} / {{ $siswa->nisn }}</td>
            
            <td></td>
            
            <td class="label">Semester</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($semester->nama) }}</td>
        </tr>
        <tr>
            <td class="label">Nama Sekolah</td>
            <td class="colon">:</td>
            <td class="value">{{ strtoupper($settings['school_name'] ?? 'SD KRISTEN KALAM KUDUS SENTANI') }}</td>
            
            <td></td>
            
            <td class="label">TP.</td>
            <td class="colon">:</td>
            <td class="value">{{ $tahunAjaran->nama }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">MUATAN PELAJARAN</th>
                <th width="15%">NILAI CAPAIAN PEMBELAJARAN</th>
                <th width="10%">GRADE</th>
                <th width="35%">DESKRIPSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiData as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="left-align">
                    <b>{{ $data['mapel']->nama }}</b><br>
                    <span style="font-size: 9pt;">Nama Guru : {{ $data['mapel']->guru_name ?? '-' }}</span>
                </td>
                <td>{{ $data['nilai'] }}</td>
                <td>{{ $data['grade'] }}</td>
                <!-- Merge Description cell for the first row of a group if needed, 
                     but here assuming simple list -->
                <td class="left-align" style="font-size: 10pt;">
                    {{ $data['deskripsi'] }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="sikap-container">
        <!-- Sikap Table (Left) -->
        <div style="width: 48%;">
            <table class="sikap-table">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th colspan="2">PENILAIAN SIKAP</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center">1</td>
                        <td>Kedisiplinan</td>
                        <td align="center" width="15%"><b>{{ $sikap->kedisiplinan ?? '-' }}</b></td>
                    </tr>
                    <tr>
                        <td align="center">2</td>
                        <td>Kejujuran</td>
                        <td align="center"><b>{{ $sikap->kejujuran ?? '-' }}</b></td>
                    </tr>
                    <tr>
                        <td align="center">3</td>
                        <td>Kesopanan</td>
                        <td align="center"><b>{{ $sikap->kesopanan ?? '-' }}</b></td>
                    </tr>
                    <tr>
                        <td align="center">4</td>
                        <td>Kebersihan</td>
                        <td align="center"><b>{{ $sikap->kebersihan ?? '-' }}</b></td>
                    </tr>
                    <tr>
                        <td align="center">5</td>
                        <td>Kepedulian</td>
                        <td align="center"><b>{{ $sikap->kepedulian ?? '-' }}</b></td>
                    </tr>
                    <tr>
                        <td align="center">6</td>
                        <td>Tanggung Jawab</td>
                        <td align="center"><b>{{ $sikap->tanggung_jawab ?? '-' }}</b></td>
                    </tr>
                    <tr>
                        <td align="center">7</td>
                        <td>Percaya Diri</td>
                        <td align="center"><b>{{ $sikap->percaya_diri ?? '-' }}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Absensi Table (Right) -->
        <div style="width: 48%;">
            <table class="absensi-table" style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th colspan="3">ABSENSI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sakit</td>
                        <td width="5%" align="center">:</td>
                        <td align="center"><b>{{ $sakit }}</b> hari</td>
                    </tr>
                     <tr>
                        <td>Ijin</td>
                        <td align="center">:</td>
                        <td align="center"><b>{{ $ijin }}</b> hari</td>
                    </tr>
                     <tr>
                        <td>Alpa</td>
                        <td align="center">:</td>
                        <td align="center"><b>{{ $alpa }}</b> hari</td>
                    </tr>
                </tbody>
            </table>

            <!-- Catatan Table (Integrated below absensi usually or separate) -->
            <div class="catatan-header">CATATAN</div>
            <div class="catatan-box">
                {{ $catatan->catatan ?? 'Tidak ada catatan.' }}
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        Sentani, {{ now()->translatedFormat('d F Y') }}
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p>Orang Tua / Wali Murid</p>
            <div class="signature-line"></div>
        </div>
        
        <div class="signature-box">
            <p>Wali Kelas</p>
            <br><br><br>
            <b>{{ $rombel->waliKelas->first()?->guru?->nama_lengkap ?? '(.........................)' }}</b>
        </div>
    </div>

    <div class="kepsek-container">
        <p>Mengetahui</p>
        <p>Kepala Sekolah Dasar</p>
        <br><br><br>
        <b>{{ $unit->kepala_sekolah ?? 'REZA KALIGIS, S.Pd., Gr., M.M.' }}</b>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
