@extends('print.layout')

@section('content')
    {{-- Header --}}
    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="margin: 0; text-transform: uppercase; color: #1e40af;">Laporan Hasil Belajar</h3>
        <h3 style="margin: 0; text-transform: uppercase; color: #1e40af;">Sumatif Akhir Semester</h3>
    </div>

    {{-- Identitas Siswa --}}
    <table class="no-border" style="width: 100%; margin-bottom: 15px; font-size: 11pt;">
        <tr>
            <td width="15%">Nama Siswa</td>
            <td width="2%">:</td>
            <td width="33%"><b>{{ strtoupper($siswa->nama_lengkap) }}</b></td>
            <td width="15%">Kelas</td>
            <td width="2%">:</td>
            <td width="33%"><b>{{ $rombel->nama }}</b></td>
        </tr>
        <tr>
            <td>NIS / NISN</td>
            <td>:</td>
            <td><b>{{ $siswa->nis }} / {{ $siswa->nisn }}</b></td>
            <td>Semester</td>
            <td>:</td>
            <td><b>{{ $semester->tipe == 'ganjil' ? 'GANJIL' : 'GENAP' }}</b></td>
        </tr>
        <tr>
            <td>Nama Sekolah</td>
            <td>:</td>
            <td><b>{{ $settings['school_name'] ?? 'SMP KRISTEN KALAM KUDUS SENTANI' }}</b></td>
            <td>TP.</td>
            <td>:</td>
            <td><b>{{ $tahunAjaran->nama }}</b></td>
        </tr>
    </table>

    {{-- Academic Grades --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #1e40af; color: white;">
                <th width="5%" class="text-center">NO</th>
                <th width="30%">MUATAN PELAJARAN</th>
                <th width="10%" class="text-center">NILAI AKHIR</th>
                <th width="55%">CAPAIAN KOMPETENSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiData as $index => $nilai)
                <tr style="page-break-inside: avoid;">
                    <td class="text-center" style="vertical-align: top; padding-top: 10px;">{{ $index + 1 }}</td>
                    <td style="vertical-align: top; padding: 10px 5px;">
                        <b>{{ $nilai['mapel']->nama }}</b>
                        <br><small style="color: #666;">Guru: {{ $nilai['nama_guru'] }}</small>
                    </td>
                    <td class="text-center font-bold" style="vertical-align: top; padding-top: 10px; font-size: 14pt;">
                        {{ $nilai['nilai'] ?? '-' }}
                    </td>
                    <td style="font-size: 9pt; padding: 10px 5px; text-align: justify;">
                        {{ $nilai['deskripsi'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Extracurricular --}}
    @if($ekskul && count($ekskul) > 0)
    <div style="margin-bottom: 20px;">
        <b>EKSTRAKURIKULER</b>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #1e40af; color: white;">
                    <th width="5%">NO</th>
                    <th width="30%">EKSTRAKURIKULER</th>
                    <th width="10%">NILAI</th>
                    <th width="55%">DESKRIPSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ekskul as $idx => $eks)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td style="font-weight: bold;">{{ $eks->kegiatan->nama ?? '-' }}</td>
                        <td class="text-center">{{ $eks->nilai ?? '-' }}</td>
                        <td style="font-size: 9pt; text-align: justify;">{{ $eks->deskripsi ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Page Break --}}
    <div style="page-break-before: always;"></div>

    {{-- Tinggi dan Berat Badan --}}
    <div style="margin-bottom: 20px;">
        <b>TINGGI DAN BERAT BADAN</b>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #1e40af; color: white;">
                    <th width="5%">NO</th>
                    <th width="45%">ASPEK YANG DINILAI</th>
                    <th width="25%" class="text-center">Ganjil</th>
                    <th width="25%" class="text-center">Genap</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1.</td>
                    <td>Tinggi Badan</td>
                    <td class="text-center">{{ $dataTubuhGanjil?->tinggi_badan ?? '-' }} CM</td>
                    <td class="text-center">{{ $dataTubuhGenap?->tinggi_badan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">2.</td>
                    <td>Berat Badan</td>
                    <td class="text-center">{{ $dataTubuhGanjil?->berat_badan ?? '-' }} Kg</td>
                    <td class="text-center">{{ $dataTubuhGenap?->berat_badan ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Kondisi Kesehatan --}}
    <div style="margin-bottom: 20px;">
        <b>KONDISI KESEHATAN</b>
        @php $health = $siswa->kesehatans->where('semester_id', $semester->id)->first(); @endphp
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #1e40af; color: white;">
                    <th width="5%">NO</th>
                    <th width="45%">ASPEK FISIK</th>
                    <th width="50%">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1.</td>
                    <td>Pendengaran</td>
                    <td class="text-center">{{ $health?->pendengaran ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">2.</td>
                    <td>Penglihatan</td>
                    <td class="text-center">{{ $health?->penglihatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">3.</td>
                    <td>Gigi</td>
                    <td class="text-center">{{ $health?->gigi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="text-center">4.</td>
                    <td>Lainnya</td>
                    <td class="text-center">{{ $health?->lainnya ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Prestasi --}}
    <div style="margin-bottom: 20px;">
        <b>PRESTASI</b>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #1e40af; color: white;">
                    <th width="5%">NO</th>
                    <th width="45%">JENIS PRESTASI</th>
                    <th width="50%">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa->prestasis as $idx => $p)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}.</td>
                        <td>{{ $p->jenis ?? '-' }}</td>
                        <td>{{ $p->keterangan ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center">1.</td>
                        <td>-</td>
                        <td></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Ketidakhadiran --}}
    <div style="margin-bottom: 20px;">
        <b>KETIDAKHADIRAN</b>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #1e40af; color: white;">
                    <th width="70%">ABSENSI</th>
                    <th width="30%">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sakit</td>
                    <td class="text-center">{{ $sakit }}</td>
                </tr>
                <tr>
                    <td>Ijin</td>
                    <td class="text-center">{{ $ijin }}</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan</td>
                    <td class="text-center">{{ $alpa }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Saran-Saran --}}
    <div style="margin-bottom: 20px;">
        <b>SARAN-SARAN</b>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #1e40af; color: white;">
                    <th class="text-center">CATATAN:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: 60px; vertical-align: top; padding: 10px; font-style: italic; text-align: center;">
                        {{ $catatanAkhir?->catatan ?? '-' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Footer with Signatures --}}
    <div style="margin-top: 30px;">
        <table class="no-border" style="width: 100%;">
            <tr>
                <td width="50%"></td>
                <td width="50%" style="text-align: center;">
                    Sentani, {{ now()->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center; padding-top: 10px;">
                    <p>Orang Tua/Wali</p>
                    <br><br><br>
                    <p>.................................</p>
                </td>
                <td style="text-align: center; padding-top: 10px;">
                    <p>Wali Kelas</p>
                    <br><br><br>
                    <p><b><u>{{ $namaWaliKelas }}</u></b></p>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; padding-top: 30px;">
                    <p>Mengetahui</p>
                    <p><b>Kepala Sekolah Menengah Pertama</b></p>
                    <br><br><br>
                    <p><b><u>{{ $settings['kepala_sekolah_smp'] ?? 'NAMA KEPALA SEKOLAH' }}</u></b></p>
                </td>
            </tr>
        </table>
    </div>
@endsection
