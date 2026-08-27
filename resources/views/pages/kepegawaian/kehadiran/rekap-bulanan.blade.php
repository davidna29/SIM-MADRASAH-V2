@php
    $statusOrder = array_keys(App\Models\EmployeeAttendance::STATUSES);
    $unitOptions = $units->pluck('name', 'id');
@endphp

<x-layouts.page
    :title="'Rekap Bulanan Kehadiran Pegawai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pegawai.kehadiran.rekap-bulanan">

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Rekap Bulanan Kehadiran</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Daftar kehadiran guru & pegawai satu bulan. Hari tanpa catatan tampil kosong —
                    pembagi persentase kehadiran adalah hari yang tercatat.
                </p>
            </div>
            <div class="flex items-center gap-2 print:hidden">
                <x-ui.button variant="secondary" icon="printer" onclick="window.print()">Cetak</x-ui.button>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('pegawai.kehadiran.rekap-bulanan') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4 print:hidden">
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
                    <label for="month" class="block pb-1.5 text-xs font-bold text-ink">Bulan</label>
                    <input type="month" name="month" value="{{ $month->format('Y-m') }}"
                        class="rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Tampilkan</x-ui.button>
            </div>
        </form>

        @if ($rows->isNotEmpty())
            <div class="mt-6">
                <x-ui.sheet
                    title="Daftar Kehadiran Pegawai"
                    :subtitle="'Bulan ' . $month->locale('id')->translatedFormat('F Y')"
                    pinned
                    :padding="false">

                    <!-- Kepala formulir -->
                    <div class="border-b border-rule/70 px-5 py-4 sm:px-6">
                        <div class="grid grid-cols-1 gap-px overflow-hidden rounded-[var(--radius-control)] bg-rule-strong ring-1 ring-inset ring-rule-strong sm:grid-cols-3">
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Bulan</p>
                                <p class="mt-0.5 text-sm font-bold text-ink">{{ $month->locale('id')->translatedFormat('F Y') }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Pegawai Aktif</p>
                                <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ count($rows) }} orang</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Hari Tercatat</p>
                                <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ $summary['hari_tercatat'] }} catatan</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-ink-faint">
                            Hari tercatat = jumlah catatan kehadiran bulan ini — pembagi persentase kehadiran.
                        </p>
                    </div>

                    <!-- Tabel rekap -->
                    @php $colspan = 4 + $daysInMonth + count($statusOrder) + 2; @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-xs">
                            <thead>
                                <tr class="border-b-2 border-rule-strong bg-paper-deep/60">
                                    <th scope="col" class="w-10 px-2 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">No</th>
                                    <th scope="col" class="min-w-[160px] px-2 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">Nama Pegawai</th>
                                    <th scope="col" class="min-w-[110px] px-2 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">Unit</th>
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        <th scope="col" class="tabular w-7 px-1 py-2.5 text-center font-mono font-semibold text-ink-faint">{{ $d }}</th>
                                    @endfor
                                    @foreach ($statusOrder as $status)
                                        <th scope="col" class="w-8 px-1 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft" title="{{ App\Models\EmployeeAttendance::STATUSES[$status] }}">
                                            {{ App\Models\EmployeeAttendance::STATUS_MARKS[$status] }}
                                        </th>
                                    @endforeach
                                    <th scope="col" class="w-14 px-1 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft">Jumlah</th>
                                    <th scope="col" class="w-20 px-2 py-2.5 text-right font-bold uppercase tracking-wide text-ink-soft">% Hadir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @forelse ($rows as $row)
                                    <tr class="transition hover:bg-paper/60">
                                        <td class="tabular px-2 py-2 text-center font-mono text-ink-faint">{{ $loop->iteration }}</td>
                                        <td class="whitespace-nowrap px-2 py-2 font-semibold text-ink">{{ $row['employee']->person->name }}</td>
                                        <td class="whitespace-nowrap px-2 py-2 text-xs text-ink-soft">{{ $row['employee']->organizationalUnit?->name ?? '—' }}</td>
                                        @for ($d = 1; $d <= $daysInMonth; $d++)
                                            @php $mark = $row['cells'][$d] ?? null; @endphp
                                            <td class="px-1 py-2 text-center">
                                                @if ($mark === '•')
                                                    <span class="text-[13px] font-bold leading-none text-primary-strong" title="Hadir">•</span>
                                                @elseif ($mark)
                                                    <span class="tabular font-mono font-semibold text-ink">{{ $mark }}</span>
                                                @else
                                                    <span class="text-rule-strong">–</span>
                                                @endif
                                            </td>
                                        @endfor
                                        @foreach ($statusOrder as $status)
                                            <td class="tabular px-1 py-2 text-center font-mono font-semibold text-ink">{{ $row['tally'][$status] }}</td>
                                        @endforeach
                                        <td class="tabular px-1 py-2 text-center font-mono font-bold text-ink">{{ $row['jumlah_ketidakhadiran'] }}</td>
                                        <td class="tabular px-2 py-2 text-right font-mono font-semibold text-ink">
                                            {{ $row['persentase_kehadiran'] !== null ? $row['persentase_kehadiran'].'%' : '–' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $colspan }}" class="px-4 py-8 text-center text-xs text-ink-faint">
                                            Tidak ada catatan kehadiran bulan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-rule-strong bg-paper-deep/70 font-bold text-ink">
                                    <td colspan="{{ 3 + $daysInMonth }}" class="px-3 py-2.5">Rekap Total ({{ count($rows) }} pegawai)</td>
                                    @foreach ($statusOrder as $status)
                                        <td class="tabular px-1 py-2.5 text-center font-mono">{{ $summary['tally'][$status] }}</td>
                                    @endforeach
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
                            · Jumlah ketidakhadiran: <span class="tabular font-mono font-semibold text-ink">{{ $summary['ketidakhadiran'] }}</span>
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
