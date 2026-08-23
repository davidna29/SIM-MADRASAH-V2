<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rapor {{ data_get($report->snapshot, 'siswa') }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #22322a; font-size: 11px; }
        .kop { text-align: center; margin-bottom: 24px; }
        .kop h1 { font-size: 15px; letter-spacing: 1px; margin: 0 0 2px; }
        .kop p { margin: 0; color: #55665b; }
        h2 { font-size: 13px; border-bottom: 2px solid #12402f; padding-bottom: 6px; color: #12402f; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #12402f; color: #fff; text-align: left; padding: 6px 10px; font-size: 10px; text-transform: uppercase; }
        td { padding: 6px 10px; border-bottom: 1px solid #e4dcc8; }
        .num { text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
        .meta { width: 100%; margin-top: 8px; }
        .meta td { border: none; padding: 2px 0; }
        .label { color: #55665b; width: 140px; }
        .catatan { margin-top: 24px; font-size: 10px; color: #55665b; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>MADRASAH TSANAWIYAH AL-IKHLAS MULIA</h1>
        <p>Laporan Hasil Belajar Siswa — Tahun Ajaran {{ data_get($report->snapshot, 'tahun') }} Semester {{ ucfirst(data_get($report->snapshot, 'semester')) }}</p>
    </div>

    <table class="meta">
        <tr><td class="label">NIS</td><td>{{ data_get($report->snapshot, 'nis') }}</td></tr>
        <tr><td class="label">Nama Siswa</td><td>{{ data_get($report->snapshot, 'siswa') }}</td></tr>
        <tr><td class="label">Kelas</td><td>{{ data_get($report->snapshot, 'kelas') }}</td></tr>
        <tr><td class="label">Guru Pengampu</td><td>{{ data_get($report->snapshot, 'guru') }}</td></tr>
    </table>

    <h2 style="margin-top:20px;">Hasil Belajar</h2>
    <table>
        <thead>
            <tr>
                <th>Mata Pelajaran</th>
                <th style="text-align:right;">Nilai</th>
                <th style="text-align:right;">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @php
                $score = (int) data_get($report->snapshot, 'score');
                $predikat = match (true) {
                    $score >= 90 => 'A',
                    $score >= 80 => 'B',
                    $score >= 70 => 'C',
                    $score >= 60 => 'D',
                    default => 'E',
                };
            @endphp
            <tr>
                <td>{{ data_get($report->snapshot, 'mapel') }}</td>
                <td class="num">{{ $score }}</td>
                <td class="num">{{ $predikat }}</td>
            </tr>
        </tbody>
    </table>

    <p class="catatan">
        Diterbitkan pada {{ \Carbon\Carbon::parse(data_get($report->snapshot, 'terbit_pada'))->isoFormat('D MMMM YYYY, HH:mm') }}.
        Dokumen ini adalah snapshot dari rapor yang terbit — isinya tidak berubah meskipun data sumber diperbarui.
    </p>
</body>
</html>
