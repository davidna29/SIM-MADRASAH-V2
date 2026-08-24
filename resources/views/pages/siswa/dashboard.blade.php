<x-layouts.page
    :title="'Data Saya'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="siswa.dashboard">

    <div class="mx-auto max-w-4xl">
        @if (! $student || ! $enrollment)
            <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Akun Anda belum terhubung ke data siswa.</p>
                <p class="mt-1 text-xs text-ink-faint">Hubungi tata usaha madrasah untuk menghubungkan akun Anda.</p>
            </div>
        @else
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $student->name }}</h1>
                    <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                        Ringkasan nilai, kehadiran, dan SPP pada Tahun Ajaran {{ $tahun->name }} · Semester {{ ucfirst($tahun->semester) }}.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge variant="info" icon="building-library">{{ $enrollment?->classGroup?->name ?? '—' }}</x-ui.badge>
                    <x-ui.badge variant="neutral" icon="identification">{{ $student->nis }}</x-ui.badge>
                </div>
            </div>

            <!-- Nilai / Rapor -->
            <div class="mt-6">
                <x-ui.sheet title="Nilai / Rapor" :subtitle="$report ? 'Rapor terbit — nilai per mata pelajaran' : 'Belum ada rapor terbit pada tahun ajaran ini'" pinned ruled
                    :actions="view('components.ui.button', ['variant' => 'secondary', 'size' => 'sm', 'icon' => 'eye', 'href' => route('siswa.rapor')])->withSlot('Lihat Rapor')->render()">
                    @if ($items->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-rule-strong">
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Mata Pelajaran</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Nilai</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Predikat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rule/70">
                                    @foreach ($items as $item)
                                        <tr class="transition hover:bg-paper/60">
                                            <td class="px-4 py-3 font-semibold text-ink">{{ $item->subject_name }}</td>
                                            <td class="tabular px-4 py-3 text-right font-mono font-semibold text-ink">{{ $item->score ?? '–' }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <x-ui.badge :variant="$item->score !== null ? 'success' : 'neutral'">{{ $item->score !== null ? \App\Support\Rapor::predikat($item->score) : '–' }}</x-ui.badge>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="py-4 text-center text-sm text-ink-faint">Belum ada nilai tercatat — nilai tampil setelah rapor diterbitkan.</p>
                    @endif
                </x-ui.sheet>
            </div>

            <!-- Kehadiran -->
            <div class="mt-6">
                <x-ui.sheet title="Kehadiran Bulanan" subtitle="Rekap status kehadiran pada semester berjalan" pinned ruled :padding="false">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-rule-strong">
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Bulan</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Hadir</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Sakit</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Izin</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Alpha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @foreach ($kehadiran as $row)
                                    <tr class="transition hover:bg-paper/60">
                                        <td class="px-4 py-3 font-semibold text-ink">{{ $monthsLabel[$row['bulan']] }}</td>
                                        <td class="tabular px-4 py-3 text-right font-mono font-semibold text-success">{{ $row['H'] }}</td>
                                        <td class="tabular px-4 py-3 text-right font-mono font-semibold text-ink">{{ $row['S'] }}</td>
                                        <td class="tabular px-4 py-3 text-right font-mono font-semibold text-ink">{{ $row['I'] }}</td>
                                        <td class="tabular px-4 py-3 text-right font-mono font-semibold text-ink">{{ $row['A'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-rule/70 px-5 py-3 text-xs text-ink-faint">
                        Catatan diambil dari kehadiran yang sudah direview madrasah.
                    </div>
                </x-ui.sheet>
            </div>

            <!-- SPP -->
            <div class="mt-6">
                <x-ui.sheet title="SPP" pinned
                    :actions="view('components.ui.button', ['variant' => 'secondary', 'size' => 'sm', 'icon' => 'banknotes', 'href' => route('siswa.spp')])->withSlot('Lihat Detail SPP')->render()">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary-strong">
                                <x-svg-banknotes class="size-5" aria-hidden="true" />
                            </span>
                            <div>
                                <p class="text-sm font-bold text-ink">{{ $sppLunas }} dari {{ $sppTotal }} bulan lunas</p>
                                <p class="mt-0.5 text-xs text-ink-faint">
                                    @if ($sppTerakhir)
                                        Pembayaran terakhir: {{ $sppTerakhir->tanggal_bayar?->isoFormat('D MMM YYYY') ?? '—' }} · Rp {{ number_format($sppTerakhir->nominal, 0, ',', '.') }}
                                    @else
                                        Belum ada pembayaran tercatat pada semester ini.
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if ($sppLunas === $sppTotal && $sppTotal > 0)
                            <x-ui.badge variant="success" icon="check">Lunas seluruhnya</x-ui.badge>
                        @endif
                    </div>
                </x-ui.sheet>
            </div>
        @endif
    </div>
</x-layouts.page>
