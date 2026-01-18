<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #f0f0f0; }
        .text-left { text-align: left; }
        .header { margin-bottom: 20px; }
        .header h2, .header h3 { margin: 5px 0; text-align: center; }
        .info { margin-bottom: 10px; }
        .info td { border: none; text-align: left; padding: 2px; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 10px; color: white; }
        .bg-green { background-color: #28a745; color: black; } /* H */
        .bg-blue { background-color: #17a2b8; color: black; } /* I */
        .bg-yellow { background-color: #ffc107; color: black; } /* S */
        .bg-red { background-color: #dc3545; color: white; font-weight: bold; } /* A */
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <h3>{{ $unit }}</h3>
    </div>

    <table class="info" style="width: auto;">
        <tr>
            <td><strong>Kegiatan</strong></td>
            <td>: {{ $subject }}</td>
        </tr>
        <tr>
            <td><strong>Semester</strong></td>
            <td>: {{ $semester }}</td>
        </tr>
        <tr>
            <td><strong>Pembina</strong></td>
            <td>: {{ $pembina }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">No</th>
                <th rowspan="2">Nama Siswa</th>
                <th colspan="{{ $activities->count() }}">Pertemuan / Tanggal</th>
                <th colspan="4">Rekap</th>
            </tr>
            <tr>
                @foreach($activities as $act)
                    <th style="font-size: 9px; width: 25px;">
                        {{ \Carbon\Carbon::parse($act->tanggal)->format('d/m') }}
                        <br>
                        <span style="font-size: 8px;">Topik {{ $loop->iteration }}</span>
                    </th>
                @endforeach
                <th style="width: 25px;">H</th>
                <th style="width: 25px;">S</th>
                <th style="width: 25px;">I</th>
                <th style="width: 25px;">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
                @php
                    $h = 0; $s = 0; $i = 0; $a = 0;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $member->siswa->nama_lengkap }}</td>
                    
                    @foreach($activities as $act)
                        @php
                            // Determine key based on context (Ekskul or Koku)
                            // The controller grouped attendance by activity ID (key)
                            // So $attendance[$act->id] is a Collection or array of records
                            $status = '-';
                            $class = '';
                            
                            if (isset($attendance[$act->id])) {
                                // Filter collection for this student
                                $record = $attendance[$act->id]->firstWhere('siswa_id', $member->siswa_id);
                                if ($record) {
                                    $status = $record->status;
                                    
                                    // Count
                                    if ($status == 'H') { $h++; $class='bg-green'; }
                                    elseif ($status == 'S') { $s++; $class='bg-yellow'; }
                                    elseif ($status == 'I') { $i++; $class='bg-blue'; }
                                    elseif ($status == 'A') { $a++; $class='bg-red'; }
                                }
                            }
                        @endphp
                        <td class="{{ $class }}">{{ $status == '-' ? '' : $status }}</td>
                    @endforeach

                    <td>{{ $h }}</td>
                    <td>{{ $s }}</td>
                    <td>{{ $i }}</td>
                    <td>{{ $a }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 30px; text-align: right; padding-right: 50px;">
        <p>Sentani, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>{{ $pembina }}</strong></p>
        <p>Pembina</p>
    </div>
</body>
</html>
