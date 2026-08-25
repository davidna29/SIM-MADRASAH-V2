<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIM Madrasah' }} · {{ config('app.name', 'SIM Madrasah') }}</title>
    @fonts
    <script>
        (function(){var t=localStorage.getItem('sim-theme');if(t==='dark'||(!t&&matchMedia('(prefers-color-scheme:dark)').matches)){document.documentElement.classList.add('dark');}})();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<!--
DIRECTION CONTRACT — SIM Madrasah, Tahap 11 Design System & Navigasi
THESIS: Seluruh sistem operasional madrasah disajikan sebagai papan pengumuman (mading) — papan indeks berjilid hijau di kiri, lembar kertas bersemat di atas kertas hangat. Menolak dashboard admin sidebar-abu generik.
OWN-WORLD: Kertas hangat (paper #f6f1e7) sebagai tanah; lembar putih bergaris halus sebagai kartu; hijau madrasah dalam sebagai papan dan aksen; status = pin berwarna (hijau lunas, kuning peringatan, merah error, biru info); Plus Jakarta Sans + JetBrains Mono untuk angka tabular; pin-dot sebagai penanda status; bayangan lembar bersemat sebagai kedalaman.
STORY: Pengunjung memahami madrasah sebagai dinding papan yang tertata — setiap modul adalah papan, setiap catatan adalah lembar; persetujuan berarti "menyematkan" sebuah lembar, dan riwayat tak pernah hilang dari papan.
FIRST VIEWPORT: Papan hijau (sidebar) dengan indeks kelompok navigasi di kiri; lembar KPI digit-bank di baris pertama; papan "Perlu Tindakan" berisi lembar persetujuan dengan pin; lembar agenda dan pengumuman; tabel terakhir sebagai lembar kas. Tindakan utama: tombol hijau "Sematkan & Setujui".
FORM: Arah Mading, kandidat 4 dari daftar grounded (mode operate), seed key 75d536ba.
FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance
-->
    <div class="min-h-screen">
        {{ $slot }}
    </div>
    <x-ui.toast />
</body>
</html>
