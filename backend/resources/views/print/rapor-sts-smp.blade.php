@extends('print.layout')

@section('content')
    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="margin: 0; text-transform: uppercase;">Laporan Hasil Belajar</h3>
        <h3 style="margin: 0; text-transform: uppercase;">Unit SMP</h3>
        <h4 style="margin: 0; text-transform: uppercase;">Sumatif Akhir Semester</h4>
    </div>
    
    <!-- Academic Grades -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #3b82f6; color: white;">
                <th width="5%" class="text-center">NO</th>
                <th width="30%">MUATAN PELAJARAN</th>
                <th width="10%" class="text-center">NILAI AKHIR</th>
                <th width="55%">CAPAIAN KOMPETENSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiAkademik as $index => $nilai)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $nilai->mataPelajaran->nama }}</td>
                    <td class="text-center font-bold">{{ $nilai->nilai }}</td>
                    <td style="font-size: 9pt;">{{ $nilai->deskripsi_capaian }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Extracurricular -->
    @if(isset($ekskul) && count($ekskul) > 0)
    <div style="margin-bottom: 20px;">
        <b>EKSTRAKURIKULER</b>
        <table>
            <thead>
                <tr style="background-color: #3b82f6; color: white;">
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
                        <td style="font-weight: bold;">{{ $eks->kegiatan->nama }}</td>
                        <td class="text-center">{{ $eks->nilai }}</td>
                        <td style="font-size: 9pt;">{{ $eks->deskripsi }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Prestasi -->
    @if($siswa->prestasis->count() > 0)
    <div style="margin-bottom: 20px;">
        <b>PRESTASI</b>
        <table>
            <thead>
                <tr style="background-color: #3b82f6; color: white;">
                    <th width="5%">NO</th>
                    <th width="40%">JENIS PRESTASI</th>
                    <th width="55%">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswa->prestasis as $idx => $p)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $p->jenis }}</td>
                        <td>{{ $p->keterangan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Health & Attendance Grid -->
    <table class="no-border" style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 48%; vertical-align: top; padding: 0;">
                <!-- Ketidakhadiran -->
                <b>KETIDAKHADIRAN</b>
                <table style="width: 100%;">
                    <thead>
                        <tr style="background-color: #3b82f6; color: white;">
                            <th width="70%">ABSENSI</th>
                            <th width="30%">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $catatan = $siswa->catatanAkhirs->where('semester_id', $semester->id)->first(); @endphp
                        <tr>
                            <td>Sakit</td>
                            <td class="text-center">{{ $catatan->sakit ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Izin</td>
                            <td class="text-center">{{ $catatan->izin ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tanpa Keterangan</td>
                            <td class="text-center">{{ $catatan->alpha ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top; padding: 0;">
                <!-- Health -->
                <b>KONDISI KESEHATAN</b>
                <table style="width: 100%;">
                    <thead>
                        <tr style="background-color: #3b82f6; color: white;">
                            <th width="10%">NO</th>
                            <th width="40%">ASPEK FISIK</th>
                            <th width="50%">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $health = $siswa->kesehatans->where('semester_id', $semester->id)->first(); @endphp
                        <tr>
                            <td class="text-center">1.</td>
                            <td>Pendengaran</td>
                            <td class="text-center">{{ $health->pendengaran ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-center">2.</td>
                            <td>Penglihatan</td>
                            <td class="text-center">{{ $health->penglihatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-center">3.</td>
                            <td>Gigi</td>
                            <td class="text-center">{{ $health->gigi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-center">4.</td>
                            <td>Lainnya</td>
                            <td class="text-center">{{ $health->lainnya ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Data Tubuh -->
    <div style="margin-bottom: 20px;">
        <b>TINGGI DAN BERAT BADAN</b>
        <table style="width: 100%;">
            <thead>
                <tr style="background-color: #3b82f6; color: white;">
                    <th width="10%">NO</th>
                    <th width="60%">ASPEK YANG DINILAI</th>
                    <th width="30%">SEMESTER {{ $semester->semester }}</th>
                </tr>
            </thead>
            <tbody>
                 @php $body = $siswa->dataTubuhs->where('semester_id', $semester->id)->first(); @endphp
                 <tr>
                     <td class="text-center">1.</td>
                     <td>Tinggi Badan</td>
                     <td class="text-center">{{ $body->tinggi_badan ?? '-' }} CM</td>
                 </tr>
                 <tr>
                     <td class="text-center">2.</td>
                     <td>Berat Badan</td>
                     <td class="text-center">{{ $body->berat_badan ?? '-' }} KG</td>
                 </tr>
            </tbody>
        </table>
    </div>

    <!-- Saran -->
    <div style="margin-bottom: 20px;">
        <b>SARAN-SARAN</b>
        <table style="width: 100%;">
            <thead>
                <tr style="background-color: #3b82f6; color: white;">
                    <th>CATATAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: 60px; vertical-align: top; padding: 10px; font-style: italic;">
                        {{ $catatan->catatan ?? '-' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
