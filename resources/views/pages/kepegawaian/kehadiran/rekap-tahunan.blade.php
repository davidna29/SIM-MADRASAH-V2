@php
    $unitOptions = $units->pluck('name', 'id');
    $monthLabels = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthLabels[$m] = \Carbon\Carbon::create($year, $m, 1)->locale('id')->translatedFormat('M');
    }
    $monthTotals = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthTotals[$m] = ['hadir' => 0, 'tercatat' => 0];
    }
    foreach ($rows as $row) {
        foreach ($row['months'] as $m => $mm) {
            $monthTotals[$m]['hadir'] += $mm['hadir'];
            $monthTotals[$m]['tercatat'] += $mm['tercatat'];
        }
    }
@endphp

<x-layouts.page
    :title="'Rekap Tahunan Kehadiran Pegawai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pegawai.kehadiran.rekap-tahunan">

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Rekap Tahunan Kehadiran</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Ringkasan kehadiran guru & pegawai per bulan dalam satu tahun. Sel tiap bulan menampilkan
                    jumlah Hadir per hari tercatat (mis. "18/20").
                </p>
            </div>
            <div class="flex items-center gap-2 print:hidden">
                <x-ui.button variant="secondary" icon="printer" onclick="window.print()">Cetak</x-ui.button>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('pegawai.kehadiran.rekap-tahunan') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4 print:hidden">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="unit_id" class="block pb-1.5 text-xs font-bold text-ink">Unit Kerja</label>
                    <x-ui.select name="unit_id" :full="false" class="w-48" :options="$unitOptions" :selected="request('unit_id')" placeholder="Semua unit" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama / NIP…"
                        class="w-48 rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="year" class="block pb-1.5 text-xs font-bold text-ink">Tahun</label>
                    <input type="number" name="year" value="{{ $year }}" min="2000" max="2100"
                        class="w-32 rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Tampilkan</x-ui.button>
            </div>
        </form>

        @if ($rows->isNotEmpty())
            <div class="mt-6">
                <x-ui.sheet
                    title="Ringkasan Kehadiran Pegawai"
                    :subtitle="'Tahun ' . $year"
                    pinned
                    :padding="false">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-xs">
                            <thead>
                                <tr class="border-b-2 border-rule-strong bg-paper-deep/60">
                                    <th scope="col" class="w-10 px-2 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">No</th>
                                    <th scope="col" class="min-w-[160px] px-2 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">Nama Pegawai</th>
                                    <th scope="col" class="min-w-[110px] px-2 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">Unit</th>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <th scope="col" class="w-14 px-1 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft">{{ $monthLabels[$m] }}</th>
                                    @endfor
                                    <th scope="col" class="w-16 px-1 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft">Total Hadir</th>
                                    <th scope="col" class="w-24 px-1 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft">Ketidakhadiran</th>
                                    <th scope="col" class="w-20 px-2 py-2.5 text-right font-bold uppercase tracking-wide text-ink-soft">% Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @forelse ($rows as $row)
                                    <tr class="transition hover:bg-paper/60">
                                        <td class="tabular px-2 py-2 text-center font-mono text-ink-faint">{{ $loop->iteration }}</td>
                                        <td class="whitespace-nowrap px-2 py-2 font-semibold text-ink">{{ $row['employee']->person->name }}</td>
                                        <td class="whitespace-nowrap px-2 py-2 text-xs text-ink-soft">{{ $row['employee']->organizationalUnit?->name ?? '—' }}</td>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <td class="tabular px-1 py-2 text-center font-mono text-ink">
                                                @if ($row['months'][$m]['tercatat'] > 0)
                                                    <span class="font-semibold text-primary-strong">{{ $row['months'][$m]['hadir'] }}</span>
                                                    <span class="text-ink-faint">/{{ $row['months'][$m]['tercatat'] }}</span>
                                                @else
                                                    <span class="text-rule-strong">–</span>
                                                @endif
                                            </td>
                                        @endfor
                                        <td class="tabular px-1 py-2 text-center font-mono font-bold text-ink">{{ $row['total_hadir'] }}</td>
                                        <td class="tabular px-1 py-2 text-center font-mono font-semibold text-ink">{{ $row['ketidakhadiran'] }}</td>
                                        <td class="tabular px-2 py-2 text-right font-mono font-semibold text-ink">
                                            {{ $row['persentase_kehadiran'] !== null ? $row['persentase_kehadiran'].'%' : '–' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="17" class="px-4 py-8 text-center text-xs text-ink-faint">
                                            Tidak ada data kehadiran tahun ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-rule-strong bg-paper-deep/70 font-bold text-ink">
                                    <td colspan="3" class="px-3 py-2.5">Rekap Total ({{ count($rows) }} pegawai)</td>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <td class="tabular px-1 py-2.5 text-center font-mono">
                                            @if ($monthTotals[$m]['tercatat'] > 0)
                                                <span class="font-semibold">{{ $monthTotals[$m]['hadir'] }}</span>
                                                <span class="text-ink-faint">/{{ $monthTotals[$m]['tercatat'] }}</span>
                                            @else
                                                <span class="text-rule-strong">–</span>
                                            @endif
                                        </td>
                                    @endfor
                                    <td class="tabular px-1 py-2.5 text-center font-mono">{{ $summary['total_hadir'] }}</td>
                                    <td class="tabular px-1 py-2.5 text-center font-mono">{{ $summary['ketidakhadiran'] }}</td>
                                    <td class="tabular px-2 py-2.5 text-right font-mono">
                                        {{ $summary['persentase_kehadiran'] !== null ? $summary['persentase_kehadiran'].'%' : '–' }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-rule/70 bg-paper/60 px-5 py-3 text-xs text-ink-soft">
                        <span>
                            Catatan tercatat: <span class="tabular font-mono font-semibold text-ink">{{ $summary['hari_tercatat'] }}</span>
                            · Total hadir: <span class="tabular font-mono font-semibold text-ink">{{ $summary['total_hadir'] }}</span>
                        </span>
                        <span>
                            Kehadiran:
                            <span class="tabular font-mono font-semibold text-ink">{{ $summary['persentase_kehadiran'] !== null ? $summary['persentase_kehadiran'].'%' : '–' }}</span>
                        </span>
                    </div>
                </x-ui.sheet>
            </div>
        @else
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Belum ada pegawai aktif terdaftar.</p>
                <p class="mt-1 text-xs text-ink-faint">Tambahkan data pada modul Data Guru & Pegawai terlebih dahulu.</p>
            </div>
        @endif
    </div>
</x-layouts.page>
