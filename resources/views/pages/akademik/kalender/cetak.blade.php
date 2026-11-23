<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kalender Pendidikan {{ $tahun->name }}</title>
    <style>
        @page { margin: 12mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; margin: 0; }
        .kop { text-align: center; border-bottom: 2px solid #111; padding-bottom: 6px; margin-bottom: 10px; }
        .kop h1 { font-size: 15px; margin: 0; }
        .kop p { margin: 2px 0 0; font-size: 9px; }
        h2 { font-size: 12px; text-align: center; margin: 10px 0 8px; text-decoration: underline; }
        .ringkas { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .ringkas th, .ringkas td { border: 1px solid #555; padding: 3px 5px; text-align: center; }
        .ringkas th { background: #eee; }
        .month { margin-bottom: 10px; page-break-inside: avoid; }
        .month h3 { font-size: 10px; margin: 0 0 3px; background: #eee; padding: 3px 5px; border: 1px solid #555; }
        table.events { width: 100%; border-collapse: collapse; }
        table.events th, table.events td { border: 1px solid #777; padding: 2px 4px; vertical-align: top; }
        table.events th { background: #f5f5f5; font-size: 9px; }
        .libur { color: #9b1c1c; }
        .ttd { margin-top: 18px; width: 100%; }
        .ttd td { width: 50%; text-align: center; font-size: 10px; }
    </style>
</head>
<body>
    <div class="kop">
        <h1>{{ \App\Models\Setting::get('madrasah_name', 'Madrasah') }}</h1>
        <p>{{ \App\Models\Setting::get('madrasah_jalan') }}, {{ \App\Models\Setting::get('madrasah_desa') }}, {{ \App\Models\Setting::get('madrasah_kecamatan') }}, {{ \App\Models\Setting::get('madrasah_kabupaten') }}</p>
        <p>NSM {{ \App\Models\Setting::get('madrasah_nsm') }} · NPSN {{ \App\Models\Setting::get('madrasah_npsn') }}</p>
    </div>

    <h2>KALENDER PENDIDIKAN TAHUN AJARAN {{ $tahun->name }}</h2>

    <table class="ringkas">
        <thead>
            <tr>
                <th>Semester</th>
                <th>Periode</th>
                <th>Pekan Efektif</th>
                <th>Hari Efektif</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach (['ganjil', 'genap'] as $semester)
                @php($period = $periods->get($semester))
                <tr>
                    <td>{{ ucfirst($semester) }}</td>
                    <td>{{ $period?->start_date?->format('d/m/Y') ?? '—' }} – {{ $period?->end_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $period?->effective_weeks ?? '—' }}</td>
                    <td>{{ $period?->effective_days ?? '—' }}</td>
                    <td>{{ $period?->note ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @foreach ($months as $month)
        <div class="month">
            <h3>{{ $month['label'] }} — {{ $month['pekan_efektif'] }} pekan efektif · {{ $month['hari_efektif'] }} hari efektif · {{ $month['hari_libur'] }} hari libur</h3>
            @if ($month['events']->isEmpty())
                <table class="events"><tr><td style="text-align:center;color:#777">Tidak ada kegiatan tercatat.</td></tr></table>
            @else
                <table class="events">
                    <thead>
                        <tr>
                            <th style="width:18%">Tanggal</th>
                            <th>Kegiatan</th>
                            <th style="width:18%">Kategori</th>
                            <th style="width:22%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($month['events'] as $event)
                            <tr class="{{ $event->is_libur ? 'libur' : '' }}">
                                <td>
                                    {{ $event->start_date->format('d/m/Y') }}
                                    @if ($event->end_date && ! $event->end_date->equalTo($event->start_date))
                                        – {{ $event->end_date->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->categoryLabel() }}</td>
                                <td>{{ $event->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

    <table class="ttd">
        <tr>
            <td>Mengetahui,<br>Kepala Madrasah</td>
            <td>Palangka Raya, {{ now()->locale('id')->translatedFormat('d F Y') }}<br>Wakamad Kurikulum</td>
        </tr>
        <tr>
            <td style="padding-top:50px">{{ \App\Models\Setting::get('madrasah_name') ? '' : '' }}<strong>{{ \App\Models\Employee::where('position_id', \App\Models\Position::where('code', 'KEPALA_MADRASAH')->value('id'))->first()?->person?->name ?? '' }}</strong></td>
            <td style="padding-top:50px"><strong>{{ \App\Models\Employee::where('position_id', \App\Models\Position::where('code', 'WAKAMAD_KURIKULUM')->value('id'))->first()?->person?->name ?? '' }}</strong></td>
        </tr>
    </table>
</body>
</html>
