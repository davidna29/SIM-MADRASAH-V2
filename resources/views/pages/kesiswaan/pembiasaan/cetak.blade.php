<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 12mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 6px; margin-bottom: 6px; }
        .kop img { height: 54px; margin-bottom: 2px; }
        .kop .naungan { font-size: 11px; }
        .kop .nama { font-size: 14px; font-weight: bold; }
        .kop .akred { font-size: 10px; }
        .kop .alamat { font-size: 9px; margin-top: 2px; }
        h2 { text-align: center; font-size: 14px; margin: 8px 0; text-decoration: underline; }
        .identitas { margin-bottom: 6px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 2px 3px; text-align: center; vertical-align: middle; }
        th.materi, td.materi { text-align: left; }
        .footer-row td { font-weight: bold; }
        .sign { margin-top: 24px; display: flex; justify-content: space-between; font-size: 10px; text-align: center; }
        .sign div { width: 30%; }
    </style>
</head>
<body>
    @php
        $grade = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];
    @endphp

    <div class="kop">
        @if ($kop['logoPath'])
            <div><img src="{{ $kop['logoPath'] }}"></div>
        @endif
        <div class="naungan">{{ $kop['naungan'] }}</div>
        <div class="nama">{{ $kop['name'] }}</div>
        <div class="akred">TER AKREDITASI "{{ $kop['akreditasi'] }}"</div>
        <div class="alamat">{{ $kop['address'] }}</div>
    </div>

    <h2>{{ $title }}</h2>

    <div class="identitas">
        Nama Siswa: <strong>{{ $siswa->name }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
        Kelas: <strong>{{ $current ? $grade[$current['kelas']] : '-' }}</strong> &nbsp;&nbsp;|&nbsp;&nbsp;
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
                        <td>{{ $cell['nilai'] !== null ? $cell['nilai'] : '' }}</td>
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

    <div class="sign">
        <div>Mengetahui,<br>Orang Tua / Wali<br><br><br>( ............................ )</div>
        <div>Palangka Raya, {{ now()->format('d F Y') }}<br>Wali Kelas<br><br><br>( ............................ )</div>
        <div>Kepala Madrasah<br><br><br>( ............................ )</div>
    </div>
</body>
</html>
