@php
    $p = $portofolio;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Portofolio — {{ $student->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #22322a; margin: 0; padding: 20px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        h2 { font-size: 14px; margin: 16px 0 8px; border-bottom: 1px solid #e4dcc8; padding-bottom: 4px; }
        .meta { font-size: 11px; color: #55665b; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #e4dcc8; font-size: 11px; }
        th { font-weight: 700; text-transform: uppercase; font-size: 9px; letter-spacing: 0.05em; color: #55665b; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'DejaVu Sans Mono', monospace; }
        .summary { display: flex; gap: 16px; margin-bottom: 16px; }
        .summary-box { flex: 1; border: 1px solid #e4dcc8; border-radius: 6px; padding: 8px; text-align: center; }
        .summary-box .label { font-size: 9px; color: #55665b; }
        .summary-box .value { font-size: 16px; font-weight: 700; margin-top: 2px; }
    </style>
</head>
<body>
    <h1>Portofolio Digital Siswa</h1>
    <div class="meta">
        {{ $student->name }} · NIS: {{ $student->nis }} · Kelas: {{ $p['kelas'] }} · {{ $p['report']?->academicYear?->name ?? '–' }} {{ ucfirst($p['report']?->semester ?? '') }}
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Kehadiran</div>
            <div class="value">{{ $p['persentaseHadir'] }}%</div>
        </div>
        <div class="summary-box">
            <div class="label">Prestasi</div>
            <div class="value">{{ $p['prestasi']->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Poin Pelanggaran</div>
            <div class="value">{{ $p['totalPoinPelanggaran'] }}</div>
        </div>
        <div class="summary-box">
            <div class="label">SPP</div>
            <div class="value">{{ $p['sppLunas'] }}/{{ $p['sppTotal'] }}</div>
        </div>
    </div>

    @if ($p['raporItems']->isNotEmpty())
        <h2>Nilai / Rapor</h2>
        <table>
            <thead><tr><th>Mata Pelajaran</th><th class="text-right">Nilai</th><th class="text-center">Predikat</th></tr></thead>
            <tbody>
                @foreach ($p['raporItems'] as $item)
                    <tr>
                        <td>{{ $item->subject_name }}</td>
                        <td class="text-right font-mono">{{ $item->score ?? '–' }}</td>
                        <td class="text-center">{{ $item->score ? \App\Support\Rapor::predikat($item->score) : '–' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Kehadiran</h2>
    <table>
        <thead><tr><th>Bulan</th><th class="text-center">H</th><th class="text-center">S</th><th class="text-center">I</th><th class="text-center">A</th></tr></thead>
        <tbody>
            @foreach ($p['kehadiran'] as $k)
                <tr>
                    <td>{{ $k['label'] }}</td>
                    <td class="text-center font-mono">{{ $k['H'] }}</td>
                    <td class="text-center font-mono">{{ $k['S'] }}</td>
                    <td class="text-center font-mono">{{ $k['I'] }}</td>
                    <td class="text-center font-mono">{{ $k['A'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($p['prestasi']->isNotEmpty())
        <h2>Prestasi</h2>
        <table>
            <thead><tr><th>Kegiatan</th><th>Jenis</th><th>Tingkat</th></tr></thead>
            <tbody>
                @foreach ($p['prestasi'] as $item)
                    <tr>
                        <td>{{ $item->nama_kegiatan }}</td>
                        <td>{{ ucfirst($item->jenis) }}</td>
                        <td>{{ ucfirst($item->tingkat) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($p['pelanggaran']->isNotEmpty())
        <h2>Pelanggaran</h2>
        <table>
            <thead><tr><th>Pelanggaran</th><th>Tingkat</th><th class="text-center">Poin</th></tr></thead>
            <tbody>
                @foreach ($p['pelanggaran'] as $item)
                    <tr>
                        <td>{{ $item->nama_pelanggaran }}</td>
                        <td>{{ ucfirst($item->tingkat) }}</td>
                        <td class="text-center font-mono">{{ $item->poin }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="meta" style="margin-top:24px; text-align:center;">
        Dicetak dari SIM Madrasah · {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
