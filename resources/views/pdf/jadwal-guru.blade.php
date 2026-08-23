<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Jadwal Guru {{ $teacher->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #22322a; font-size: 10px; }
        .kop { text-align: center; margin-bottom: 18px; }
        .kop h1 { font-size: 14px; letter-spacing: 1px; margin: 0 0 2px; color: #12402f; }
        .kop p { margin: 0; color: #55665b; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #12402f; color: #fff; text-align: left; padding: 5px 8px; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 8px; border: 1px solid #d3c8ad; }
        .meta { margin-top: 16px; font-size: 9px; color: #55665b; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>JADWAL MENGAJAR GURU</h1>
        <p>{{ $teacher->name }} — Tahun Ajaran {{ $tahun->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:60px;">Hari</th>
                <th style="width:50px;">Jam ke-</th>
                <th>Kelas</th>
                <th>Mata Pelajaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cells->sortBy(fn ($c) => array_search($c->day, $days) * 100 + $c->period_no) as $cell)
                <tr>
                    <td>{{ ucfirst($cell->day) }}</td>
                    <td>{{ $cell->period_no }}</td>
                    <td>{{ $cell->classGroup?->name ?? '—' }}</td>
                    <td>{{ $cell->subject?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada jadwal.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="meta">Dicetak pada {{ now()->isoFormat('D MMMM YYYY') }} · SIM Madrasah</p>
</body>
</html>
