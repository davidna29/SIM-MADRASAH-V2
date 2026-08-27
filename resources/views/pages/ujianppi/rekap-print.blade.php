<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Ujian PPI — {{ $periode->judul }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 7px; color: #111; margin: 0; }
        .kop { display: flex; align-items: center; justify-content: center; gap: 12px; border-bottom: 2px solid #111; padding-bottom: 8px; margin-bottom: 8px; }
        .kop img { height: 52px; }
        .kop-text { text-align: center; line-height: 1.35; }
        .kop-text .naungan { font-size: 8px; font-weight: bold; }
        .kop-text .nama { font-size: 13px; font-weight: bold; }
        .kop-text .alamat { font-size: 6.5px; }
        h1 { text-align: center; font-size: 12px; margin: 4px 0 2px; }
        .sub { text-align: center; font-size: 8px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 0.5px solid #555; padding: 2px 3px; text-align: center; }
        th { background: #eee; font-size: 6.5px; }
        td.left { text-align: left; }
        .head-group { background: #ddebf7; }
        .summary { background: #eaf4ea; }
        .strong { font-weight: bold; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: auto; }
    </style>
</head>
<body>
    @foreach ($participants->chunk(20) as $chunk)
        <div class="page">
            <div class="kop">
                @if (! empty($kop['logoPath']))
                    <img src="{{ $kop['logoPath'] }}" alt="logo">
                @endif
                <div class="kop-text">
                    <div class="naungan">{{ $kop['naungan'] }}</div>
                    <div class="nama">{{ $kop['name'] }}</div>
                    <div class="alamat">{{ $kop['address'] }}</div>
                </div>
            </div>
            <h1>REKAP NILAI UJIAN PPI KELAS VI (MUNAQASAH)</h1>
            <div class="sub">{{ $periode->judul }} — Tahun Pelajaran {{ $periode->academicYear?->name }}</div>

            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="width:22px">No</th>
                        <th rowspan="2" style="width:50px">NISN</th>
                        <th rowspan="2" class="left" style="width:110px">Nama Siswa</th>
                        <th rowspan="2" style="width:36px">Ruang</th>
                        @foreach ($categories as $category)
                            <th colspan="{{ $category->aspects->count() }}" class="head-group">{{ $category->nama }}</th>
                        @endforeach
                        @foreach ($hafalanMateri as $materi)
                            <th class="head-group" style="width:26px">{{ $materi->nama }}</th>
                        @endforeach
                        <th colspan="2" class="summary">P1</th>
                        <th colspan="2" class="summary">P2</th>
                        <th colspan="2" class="summary">P3</th>
                        <th class="summary">Rata Hafalan</th>
                        <th class="summary">Jml</th>
                        <th class="summary">Rata</th>
                        <th class="summary" style="width:26px">Pred</th>
                        <th class="summary" style="width:26px">Lulus</th>
                        <th class="summary" style="width:20px">JK</th>
                        <th class="summary" style="width:22px">Rank</th>
                        <th class="summary" style="width:26px">Rank Lokal</th>
                    </tr>
                    <tr>
                        @foreach ($categories as $category)
                            @foreach ($category->aspects as $aspect)
                                <th style="width:24px;font-weight:normal">{{ trim($category->kode.'.'.$aspect->kode, '.') }}</th>
                            @endforeach
                        @endforeach
                        @foreach ($hafalanMateri as $materi)
                            <th style="width:26px;font-weight:normal">{{ $loop->iteration }}</th>
                        @endforeach
                        <th>Jml</th><th>Rata</th>
                        <th>Jml</th><th>Rata</th>
                        <th>Jml</th><th>Rata</th>
                        <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chunk as $p)
                        <tr>
                            <td>{{ $p->no_urut }}</td>
                            <td>{{ $p->student ? $service->nisnOf($p->student) : '' }}</td>
                            <td class="left strong">{{ $p->student?->name }}</td>
                            <td>{{ $p->room?->nama }}</td>
                            @foreach ($categories as $category)
                                @foreach ($category->aspects as $aspect)
                                    <td>{{ $scores[$p->id][$aspect->id]->nilai ?? '' }}</td>
                                @endforeach
                            @endforeach
                            @foreach ($hafalanMateri as $materi)
                                <td>{{ $hafalanScores[$p->id][$materi->id]->nilai ?? '' }}</td>
                            @endforeach
                            <td class="summary">{{ $p->jumlah_p1 ?? '' }}</td>
                            <td class="summary">{{ $service::fmt($p->rata_p1) }}</td>
                            <td class="summary">{{ $p->jumlah_p2 ?? '' }}</td>
                            <td class="summary">{{ $service::fmt($p->rata_p2) }}</td>
                            <td class="summary">{{ $p->jumlah_p3 ?? '' }}</td>
                            <td class="summary">{{ $service::fmt($p->rata_p3) }}</td>
                            <td class="summary">{{ $service::fmt($p->rata_hafalan) }}</td>
                            <td class="summary">{{ $p->jumlah_ujian_lisan ?? '' }}</td>
                            <td class="summary strong">{{ $service::fmt($p->rata_ujian_lisan) }}</td>
                            <td class="summary strong">{{ $p->predicateScale?->predikat ?? '—' }}</td>
                            <td class="summary">{{ $p->status_lulus === null ? '—' : ($p->status_lulus ? 'L' : 'TL') }}</td>
                            <td>{{ $p->student?->gender ?? '' }}</td>
                            <td>{{ $p->rank_total ?? '' }}</td>
                            <td>{{ $p->rank_lokal ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>