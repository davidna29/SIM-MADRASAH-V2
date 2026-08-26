<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} — SIM Madrasah</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #22322a; margin: 0; padding: 15px; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { font-size: 10px; color: #55665b; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 5px 8px; text-align: left; border-bottom: 1px solid #e4dcc8; font-size: 10px; }
        th { font-weight: 700; text-transform: uppercase; font-size: 8px; letter-spacing: 0.05em; color: #55665b; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .summary { display: flex; gap: 12px; margin-bottom: 12px; }
        .summary-box { flex: 1; border: 1px solid #e4dcc8; border-radius: 4px; padding: 6px; text-align: center; }
        .summary-box .label { font-size: 8px; color: #55665b; }
        .summary-box .value { font-size: 14px; font-weight: 700; margin-top: 2px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">
        {{ $tahun?->name ?? 'Semua Tahun' }} · {{ ucfirst($tahun?->semester ?? '') }} · Dicetak {{ now()->format('d/m/Y H:i') }}
    </div>

    @if (isset($data['rows']) && $data['rows']->isNotEmpty())
        @php $first = $data['rows']->first(); @endphp
        <table>
            <thead>
                <tr>
                    @foreach (array_keys($first) as $key)
                        <th>{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data['rows'] as $row)
                    <tr>
                        @foreach ($row as $value)
                            <td>{{ $value ?? '–' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Tidak ada data.</p>
    @endif
</body>
</html>
