<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Jadwal Kelas {{ $classGroup->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #22322a; font-size: 10px; }
        .kop { text-align: center; margin-bottom: 18px; }
        .kop h1 { font-size: 14px; letter-spacing: 1px; margin: 0 0 2px; color: #12402f; }
        .kop p { margin: 0; color: #55665b; }
        h2 { font-size: 12px; border-bottom: 2px solid #12402f; padding-bottom: 5px; color: #12402f; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #12402f; color: #fff; text-align: left; padding: 5px 8px; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 8px; border: 1px solid #d3c8ad; vertical-align: top; }
        .day { font-weight: bold; color: #12402f; }
        .meta { margin-top: 16px; font-size: 9px; color: #55665b; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>MADRASAH TSANAWIYAH AL-IKHLAS MULIA</h1>
        <p>Jadwal Pelajaran Kelas {{ $classGroup->name }} — Tahun Ajaran {{ $tahun->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:60px;">Hari</th>
                <th style="width:50px;">Jam ke-</th>
                <th>Mata Pelajaran</th>
                <th>Guru</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($days as $day)
                @php
                    $dayCells = $cells->where('day', $day)->sortBy('period_no');
                @endphp
                @forelse ($dayCells as $cell)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ $dayCells->count() }}" class="day">{{ ucfirst($day) }}</td>
                        @endif
                        <td>{{ $cell->period_no }}</td>
                        <td>{{ $cell->subject?->name ?? '—' }}</td>
                        <td>{{ $cell->teacher?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="day">{{ ucfirst($day) }}</td>
                        <td colspan="3">—</td>
                    </tr>
                @endforelse
            @endforeach
        </tbody>
    </table>

    <p class="meta">Dicetak pada {{ now()->isoFormat('D MMMM YYYY') }} · SIM Madrasah</p>
</body>
</html>
