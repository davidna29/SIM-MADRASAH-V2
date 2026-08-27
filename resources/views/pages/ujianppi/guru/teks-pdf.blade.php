<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara — {{ $peserta->student?->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111; margin: 32px; }
        .kop { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px double #111; padding-bottom: 10px; }
        .kop img { height: 56px; }
        .kop-text { text-align: center; line-height: 1.4; padding: 0 10px; }
        .kop-text .naungan { font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .kop-text .nama { font-size: 15px; font-weight: bold; }
        .kop-text .alamat { font-size: 8px; }
        h1 { text-align: center; font-size: 14px; margin: 18px 0 2px; text-transform: uppercase; }
        .subtitle { text-align: center; font-size: 11px; font-weight: bold; margin-bottom: 14px; }
        .body { white-space: pre-wrap; font-size: 12px; line-height: 1.65; }
        .ttd { margin-top: 40px; display: flex; justify-content: space-between; padding: 0 10px; }
        .ttd .col { text-align: center; width: 30%; }
        .ttd .space { height: 64px; }
        .ttd .name { border-top: 1px solid #111; padding-top: 6px; font-weight: bold; font-size: 11px; }
        .ttd .role { font-size: 10px; }
    </style>
</head>
<body>
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

    <h1>Berita Acara</h1>
    <div class="subtitle">Asesmen Praktek Pengamalan Ibadah (PPI) Siswa Kelas VI</div>

    <div class="body" style="white-space:pre-wrap;font-size:12px;line-height:1.65;">{!! $teks_ba !!}</div>
</body>
</html>