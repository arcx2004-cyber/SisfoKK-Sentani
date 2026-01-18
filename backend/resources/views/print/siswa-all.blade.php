<!DOCTYPE html>
<html>
<head>
    <title>Data Peserta Didik</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; }
        .rombel-header { background-color: #f0f0f0; font-weight: bold; }
        .recap-section { display: flex; justify-content: space-between; margin-top: 30px; }
        .recap-table { width: 48%; display: inline-block; vertical-align: top; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DATA PESERTA DIDIK</h2>
        <h3>{{ $unitName }} - Tahun Ajaran {{ $tahunAjaran }}</h3>
    </div>

    @foreach($grouped as $rombel => $students)
        @if($students->isNotEmpty())
            <table class="table">
                <thead>
                    <tr class="rombel-header">
                        <td colspan="7">Kelas / Rombel: {{ $rombel }}</td>
                    </tr>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">NIS / NISN</th>
                        <th>Nama Lengkap</th>
                        <th width="5%">L/P</th>
                        <th>Tempat, Tanggal Lahir</th>
                        <th>Agama</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $siswa)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $siswa->nis }} / {{ $siswa->nisn }}</td>
                        <td>{{ $siswa->nama_lengkap }}</td>
                        <td style="text-align: center;">{{ $siswa->jenis_kelamin }}</td>
                        <td>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir?->format('d-m-Y') }}</td>
                        <td>{{ $siswa->agama }}</td>
                        <td>{{ $siswa->alamat }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <div style="page-break-inside: avoid;">
        <h3>REKAPITULASI DATA</h3>
        
        <div style="width: 100%;">
            <!-- Recap Rombel & Gender -->
            <div class="recap-table">
                <h4>Jumlah Siswa per Rombel</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rombel</th>
                            <th>L</th>
                            <th>P</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recap['rombels'] as $rombel => $data)
                        <tr>
                            <td>{{ $rombel }}</td>
                            <td style="text-align: center;">{{ $data['L'] }}</td>
                            <td style="text-align: center;">{{ $data['P'] }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $data['total'] }}</td>
                        </tr>
                        @endforeach
                        <tr style="background-color: #f0f0f0; font-weight: bold;">
                            <td>TOTAL SELURUHNYA</td>
                            <td style="text-align: center;">{{ $totalL }}</td>
                            <td style="text-align: center;">{{ $totalP }}</td>
                            <td style="text-align: center;">{{ $totalSiswa }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Recap Agama -->
            <div class="recap-table" style="margin-left: 2%;">
                <h4>Jumlah Siswa Berdasarkan Agama</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Agama</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agamas as $agama => $count)
                        <tr>
                            <td>{{ $agama ?: 'Tidak Ada Data' }}</td>
                            <td style="text-align: center;">{{ $count }}</td>
                        </tr>
                        @endforeach
                        <tr style="background-color: #f0f0f0; font-weight: bold;">
                            <td>TOTAL</td>
                            <td style="text-align: center;">{{ $totalSiswa }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <br>
        <div style="width: 100%; text-align: right; margin-top: 30px;">
            <p>Sentani, {{ date('d F Y') }}</p>
            <p>Kepala Sekolah</p>
            <br><br><br>
            <p>_________________________</p>
        </div>
    </div>
</body>
</html>
