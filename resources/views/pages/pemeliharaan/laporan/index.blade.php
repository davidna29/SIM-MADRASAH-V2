<x-layouts.page title="Pusat Laporan" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pusat Laporan</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">Laporan terkonsolidasi untuk pimpinan madrasah. Semua data bersifat agregat dari modul yang sudah ada.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Rekap Akademik --}}
            <a href="{{ route('laporan.akademik') }}" class="group flex flex-col rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60 transition hover:shadow-sheet-raised hover:ring-primary/30">
                <div class="mb-3 flex size-10 items-center justify-center rounded-[var(--radius-control)] bg-primary-soft">
                    <x-svg-academic-cap class="size-5 text-primary" />
                </div>
                <h2 class="text-sm font-bold text-ink group-hover:text-primary">Rekap Akademik</h2>
                <p class="mt-1 text-xs leading-relaxed text-ink-soft">Rata-rata rapor per kelas, distribusi predikat, kelas terbaik & terendah.</p>
            </a>

            {{-- Rekap Kehadiran --}}
            <a href="{{ route('laporan.kehadiran') }}" class="group flex flex-col rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60 transition hover:shadow-sheet-raised hover:ring-primary/30">
                <div class="mb-3 flex size-10 items-center justify-center rounded-[var(--radius-control)] bg-info-soft">
                    <x-svg-clipboard-document-check class="size-5 text-info" />
                </div>
                <h2 class="text-sm font-bold text-ink group-hover:text-primary">Rekap Kehadiran</h2>
                <p class="mt-1 text-xs leading-relaxed text-ink-soft">Hadir, sakit, izin, alpha per kelas per bulan semester aktif.</p>
            </a>

            {{-- Rekap Keuangan --}}
            <a href="{{ route('laporan.keuangan') }}" class="group flex flex-col rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60 transition hover:shadow-sheet-raised hover:ring-primary/30">
                <div class="mb-3 flex size-10 items-center justify-center rounded-[var(--radius-control)] bg-success-soft">
                    <x-svg-banknotes class="size-5 text-success" />
                </div>
                <h2 class="text-sm font-bold text-ink group-hover:text-primary">Rekap Keuangan</h2>
                <p class="mt-1 text-xs leading-relaxed text-ink-soft">SPP terkumpul per kelas, persentase pelunasan, total keseluruhan.</p>
            </a>

            {{-- Rekap Kesiswaan --}}
            <a href="{{ route('laporan.kesiswaan') }}" class="group flex flex-col rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60 transition hover:shadow-sheet-raised hover:ring-primary/30">
                <div class="mb-3 flex size-10 items-center justify-center rounded-[var(--radius-control)] bg-warning-soft">
                    <x-svg-trophy class="size-5 text-warning" />
                </div>
                <h2 class="text-sm font-bold text-ink group-hover:text-primary">Rekap Kesiswaan</h2>
                <p class="mt-1 text-xs leading-relaxed text-ink-soft">Jumlah prestasi terverifikasi & pelanggaran selesai per kelas.</p>
            </a>

            {{-- Rekap Tenaga --}}
            <a href="{{ route('laporan.tenaga') }}" class="group flex flex-col rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60 transition hover:shadow-sheet-raised hover:ring-primary/30">
                <div class="mb-3 flex size-10 items-center justify-center rounded-[var(--radius-control)] bg-primary-soft">
                    <x-svg-user-group class="size-5 text-primary" />
                </div>
                <h2 class="text-sm font-bold text-ink group-hover:text-primary">Rekap Tenaga</h2>
                <p class="mt-1 text-xs leading-relaxed text-ink-soft">Jumlah guru & pegawai per role, rasio guru-siswa, status aktif.</p>
            </a>

            {{-- Rekap Perpustakaan --}}
            <a href="{{ route('laporan.perpustakaan') }}" class="group flex flex-col rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60 transition hover:shadow-sheet-raised hover:ring-primary/30">
                <div class="mb-3 flex size-10 items-center justify-center rounded-[var(--radius-control)] bg-info-soft">
                    <x-svg-book-open class="size-5 text-info" />
                </div>
                <h2 class="text-sm font-bold text-ink group-hover:text-primary">Rekap Perpustakaan</h2>
                <p class="mt-1 text-xs leading-relaxed text-ink-soft">Jumlah buku, peminjaman aktif, anggota, buku paling populer.</p>
            </a>
        </div>
    </div>
</x-layouts.page>
