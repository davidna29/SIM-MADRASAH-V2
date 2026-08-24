<x-layouts.page
    :title="'Ringkasan Anak'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ortu.ringkasan">

    <div class="mx-auto max-w-4xl">
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
            <x-ui.sheet title="Nilai / Rapor" :subtitle="$report ? 'Rapor terbit — nilai per mata pelajaran' : 'Belum ada rapor terbit pada tahun ajaran ini'" pinned ruled :padding="false">
                @if ($items->isNotEmpty())
                    <x-ui.table :headers="['Mata Pelajaran', 'Guru', 'Nilai', 'Predikat']">
                        <x-slot>
                            @foreach ($items as $item)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="px-4 py-3 font-semibold text-ink">{{ $item->subject_name }}</td>
                                    <td class="px-4 py-3 text-xs text-ink-soft">{{ $item->teacher_name }}</td>
                                    <td class="tabular px-4 py-3 font-mono font-semibold text-ink">{{ $item->score ?? '–' }}</td>
                                    <td class="px-4 py-3">
                                        <x-ui.badge :variant="$item->score !== null ? 'success' : 'neutral'">{{ $item->score !== null ? \App\Support\Rapor::predikat($item->score) : '–' }}</x-ui.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-ui.table>
                @else
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm font-semibold text-ink">Belum ada nilai tercatat.</p>
                        <p class="mt-1 text-xs text-ink-faint">Nilai akan tampil setelah rapor diterbitkan oleh madrasah.</p>
                    </div>
                @endif
            </x-ui.sheet>
        </div>

        <!-- Kehadiran -->
        <div class="mt-6">
            <x-ui.sheet title="Kehadiran Bulanan" subtitle="Rekap status kehadiran pada semester berjalan" pinned ruled :padding="false">
                <x-ui.table :headers="['Bulan', 'Hadir', 'Sakit', 'Izin', 'Alpha']">
                    <x-slot>
                        @foreach ($kehadiran as $row)
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3 font-semibold text-ink">{{ $monthsLabel[$row['bulan']] }}</td>
                                <td class="tabular px-4 py-3 font-mono font-semibold text-success">{{ $row['H'] }}</td>
                                <td class="tabular px-4 py-3 font-mono font-semibold text-ink">{{ $row['S'] }}</td>
                                <td class="tabular px-4 py-3 font-mono font-semibold text-ink">{{ $row['I'] }}</td>
                                <td class="tabular px-4 py-3 font-mono font-semibold text-ink">{{ $row['A'] }}</td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3 text-xs text-ink-faint">
                    Catatan diambil dari kehadiran yang sudah direview madrasah.
                </div>
            </x-ui.sheet>
        </div>

        <!-- SPP -->
        <div class="mt-6">
            <x-ui.sheet title="SPP" pinned
                :actions="view('components.ui.button', ['variant' => 'secondary', 'size' => 'sm', 'icon' => 'banknotes', 'href' => route('ortu.spp.show', $student)])->withSlot('Lihat Detail SPP')->render()">
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
    </div>
</x-layouts.page>
