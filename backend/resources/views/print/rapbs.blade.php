@extends('print.layout')

@section('content')
    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid black; padding-bottom: 10px;">
        <h3 style="margin: 0; text-transform: uppercase;">RENCANA ANGGARAN PENDAPATAN DAN BELANJA SEKOLAH (RAPBS)</h3>
        <h4 style="margin: 5px 0;">TAHUN AJARAN {{ $rapbs->tahunAjaran->nama }}</h4>
        <div style="font-size: 11pt;">UNIT: {{ $rapbs->unit->nama }}</div>
    </div>

    <h4 style="margin-bottom: 5px;">I. PENDAPATAN</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Uraian</th>
                <th width="25%">Nominal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($rapbs->details->where('jenis', 'pendapatan') as $detail)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $detail->uraian }}</td>
                <td style="text-align: right;">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
                <td>{{ $detail->keterangan }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="2" style="text-align: center;">TOTAL PENDAPATAN</td>
                <td style="text-align: right;">Rp {{ number_format($rapbs->total_pendapatan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <h4 style="margin-bottom: 5px; margin-top: 20px;">II. PENGELUARAN</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Uraian</th>
                <th width="25%">Nominal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
             @php $no = 1; @endphp
            @foreach($rapbs->details->where('jenis', 'pengeluaran') as $detail)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $detail->uraian }}</td>
                <td style="text-align: right;">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
                <td>{{ $detail->keterangan }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="2" style="text-align: center;">TOTAL PENGELUARAN</td>
                <td style="text-align: right;">Rp {{ number_format($rapbs->total_pengeluaran, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px; border: 1px solid #000; padding: 10px;">
        <strong>SURPLUS / DEFISIT: </strong> 
        Rp {{ number_format($rapbs->total_pendapatan - $rapbs->total_pengeluaran, 0, ',', '.') }}
    </div>

    <table style="width: 100%; margin-top: 50px;">
        <tr>
            <td style="text-align: center; width: 33%;">
                Mengetahui,<br>
                Direktur Pelaksana
                <br><br><br><br>
                <strong>{{ $rapbs->approver->name ?? '( ..................... )' }}</strong>
            </td>
            <td style="width: 33%;"></td>
            <td style="text-align: center; width: 33%;">
                Sentani, {{ now()->translatedFormat('d F Y') }}<br>
                Kepala Sekolah
                <br><br><br><br>
                <strong>{{ $rapbs->creator->name ?? '( ..................... )' }}</strong>
            </td>
        </tr>
    </table>
@endsection
