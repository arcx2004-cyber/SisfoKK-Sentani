@extends('print.layout')

@section('content')
    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px;">
        <h3 style="margin: 0; text-transform: uppercase;">KARTU PESERTA UJIAN</h3>
        <h4 style="margin: 5px 0; text-transform: uppercase;">{{ $title }}</h4>
        <div style="font-size: 10pt;">Tahun Pelajaran: {{ $tahunAjaran->nama }} - Semester {{ $semester->semester }}</div>
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td width="20%">Nama Peserta</td>
            <td width="1%">:</td>
            <td width="79%"><strong>{{ $siswa->nama_lengkap }}</strong></td>
        </tr>
        <tr>
            <td>Nomor Induk / NISN</td>
            <td>:</td>
            <td>{{ $siswa->nis }} / {{ $siswa->nisn }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $siswa->rombel->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Unit</td>
            <td>:</td>
            <td>{{ $siswa->unit->nama }}</td>
        </tr>
    </table>

    <div style="border: 1px solid #000; padding: 10px; margin-bottom: 20px; text-align: center;">
        <p style="margin: 0; font-weight: bold; font-size: 14pt;">MEMENUHI SYARAT</p>
        <p style="margin: 5px 0;">Untuk Mengikuti Ujian</p>
    </div>

    <table style="width: 100%;">
        <tr>
            <td width="70%"></td>
            <td width="30%" style="text-align: center;">
                Sentani, {{ now()->translatedFormat('d F Y') }}<br>
                Ketua Panitia Ujian,
                <br><br><br><br>
                <strong>( ..................................... )</strong>
            </td>
        </tr>
    </table>
    
    <div style="margin-top: 50px; font-size: 8pt; color: #555; text-align: center;">
        <i>Kartu ini wajib dibawa selama pelaksanaan ujian berlangsung.</i>
    </div>
@endsection
