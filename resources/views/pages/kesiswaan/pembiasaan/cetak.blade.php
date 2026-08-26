<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 12mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; margin: 0; }
        h1 { text-align: center; font-size: 16px; margin: 0 0 4px; text-decoration: underline; }
        .info { font-size: 11px; margin-bottom: 8px; }
        .info strong { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th, td { border: 1px solid #000; padding: 2px 3px; text-align: center; vertical-align: middle; }
        th { font-size: 9px; font-weight: bold; background: #f0f0f0; }
        td.materi { text-align: left; font-size: 9px; }
        .footer-row td { font-weight: bold; background: #f8f8f8; }
    </style>
</head>
<body>
    @php
        $grade = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];
    @endphp

    <h1>{{ $title }}</h1>

    <div class="info">
        Nama Siswa: <strong>{{ $siswa->name }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
        Kelas: <strong>{{ $current ? $grade[$current['kelas']] : '-' }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
        Semester: <strong>{{ $current['semester'] ?? '-' }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
        Tahun Pelajaran: <strong>{{ $current['tahun'] ?? '-' }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th class="materi">No</th>
                <th class="materi">Materi</th>
                @foreach ($pairs as $pair)
                    <th>{{ $grade[$pair[0]] }}.{{ $pair[1] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td class="materi">{{ $row['materi']->no_urut }}</td>
                    <td class="materi">{{ $row['materi']->nama_materi }}</td>
                    @foreach ($row['cells'] as $cell)
                        <td>{{ $cell['nilai'] ?? '' }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr class="footer-row">
                <td class="materi" colspan="2">Jumlah</td>
                @foreach ($pairs as $pair)
                    <td>{{ $footers[$pair[0].'-'.$pair[1]]['jumlah'] }}</td>
                @endforeach
            </tr>
            <tr class="footer-row">
                <td class="materi" colspan="2">Rata-rata</td>
                @foreach ($pairs as $pair)
                    <td>{{ $footers[$pair[0].'-'.$pair[1]]['rata_rata'] ?? '-' }}</td>
                @endforeach
            </tr>
            <tr class="footer-row">
                <td class="materi" colspan="2">Kategori</td>
                @foreach ($pairs as $pair)
                    <td>{{ $footers[$pair[0].'-'.$pair[1]]['kategori'] }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</body>
</html>
