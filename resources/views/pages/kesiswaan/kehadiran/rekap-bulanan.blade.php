<x-layouts.page
    :title="'Rekap Bulanan Kehadiran'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="kehadiran.rekap">

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Rekap Bulanan Kehadiran</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Daftar kehadiran siswa satu rombel selama satu bulan. Hari yang belum direview tampil kosong
                    (bukan dianggap Alpha) — pembagi persentase adalah hari yang sudah direview.
                </p>
            </div>
            <div class="flex items-center gap-2 print:hidden">
                <x-ui.button variant="secondary" icon="printer" onclick="window.print()">Cetak</x-ui.button>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('kehadiran.rekap') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4 print:hidden">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas / Rombel</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-44" :options="$classes->pluck('name', 'id')" :selected="$classGroup?->id" />
                </div>
                <div>
                    <label for="month" class="block pb-1.5 text-xs font-bold text-ink">Bulan</label>
                    <input type="month" name="month" value="{{ $month->format('Y-m') }}"
                        class="rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Tampilkan</x-ui.button>
            </div>
        </form>

        @if ($classGroup)
            <div class="mt-6">
                <x-ui.sheet
                    title="Daftar Kehadiran Siswa"
                    :subtitle="$classGroup->name . ' · Bulan ' . $month->locale('id')->translatedFormat('F Y')"
                    pinned
                    :padding="false">

                    <!-- Kepala formulir -->
                    <div class="border-b border-rule/70 px-5 py-4 sm:px-6">
                        <div class="grid grid-cols-1 gap-px overflow-hidden rounded-[var(--radius-control)] bg-rule-strong ring-1 ring-inset ring-rule-strong sm:grid-cols-2 lg:grid-cols-4">
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Bulan</p>
                                <p class="mt-0.5 text-sm font-bold text-ink">{{ $month->locale('id')->translatedFormat('F Y') }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Tahun Pelajaran</p>
                                <p class="mt-0.5 text-sm font-bold text-ink">{{ $tahun->name }} · Semester {{ ucfirst($tahun->semester) }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Kelas</p>
                                <p class="mt-0.5 text-sm font-bold text-ink">{{ $classGroup->name }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Hari Efektif</p>
                                <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ $reviewedCount }} hari</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-ink-faint">
                            Hari efektif = jumlah tanggal yang sudah direview bulan ini — pembagi persentase kehadiran.
                        </p>
                    </div>

                    <!-- Tabel rekap -->
                    @php $colspan = 7 + $daysInMonth; @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-xs">
                            <thead>
                                <tr class="border-b-2 border-rule-strong bg-paper-deep/60">
                                    <th scope="col" class="w-10 px-2 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">No</th>
                                    <th scope="col" class="min-w-[150px] px-2 py-2.5 text-left font-bold uppercase tracking-wide text-ink-soft">Nama Siswa</th>
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        <th scope="col" class="tabular w-7 px-1 py-2.5 text-center font-mono font-semibold text-ink-faint">{{ $d }}</th>
                                    @endfor
                                    <th scope="col" class="w-8 px-1 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft">S</th>
                                    <th scope="col" class="w-8 px-1 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft">I</th>
                                    <th scope="col" class="w-8 px-1 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft">A</th>
                                    <th scope="col" class="w-12 px-1 py-2.5 text-center font-bold uppercase tracking-wide text-ink-soft">Jumlah</th>
                                    <th scope="col" class="w-20 px-2 py-2.5 text-right font-bold uppercase tracking-wide text-ink-soft">% Hadir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @forelse ($rows as $row)
                                    <tr class="transition hover:bg-paper/60">
                                        <td class="tabular px-2 py-2 text-center font-mono text-ink-faint">{{ $loop->iteration }}</td>
                                        <td class="whitespace-nowrap px-2 py-2 font-semibold text-ink">{{ $row['student']->displayName() }}</td>
                                        @for ($d = 1; $d <= $daysInMonth; $d++)
                                            @php $mark = $row['cells'][$d] ?? null; @endphp
                                            <td class="px-1 py-2 text-center">
                                                @if ($mark === '•')
                                                    <span class="text-[13px] font-bold leading-none text-primary-strong" title="Hadir">•</span>
                                                @elseif (in_array($mark, ['S', 'I', 'A'], true))
                                                    <span class="tabular font-mono font-semibold text-ink">{{ $mark }}</span>
                                                @else
                                                    <span class="text-rule-strong">–</span>
                                                @endif
                                            </td>
                                        @endfor
                                        <td class="tabular px-1 py-2 text-center font-mono font-semibold text-ink">{{ $row['tally']['S'] }}</td>
                                        <td class="tabular px-1 py-2 text-center font-mono font-semibold text-ink">{{ $row['tally']['I'] }}</td>
                                        <td class="tabular px-1 py-2 text-center font-mono font-semibold text-ink">{{ $row['tally']['A'] }}</td>
                                        <td class="tabular px-1 py-2 text-center font-mono font-bold text-ink">{{ $row['jumlah'] }}</td>
                                        <td class="tabular px-2 py-2 text-right font-mono font-semibold text-ink">
                                            {{ $row['persentase_kehadiran'] !== null ? $row['persentase_kehadiran'].'%' : '–' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $colspan }}" class="px-4 py-8 text-center text-xs text-ink-faint">
                                            Tidak ada siswa aktif di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-rule-strong bg-paper-deep/70 font-bold text-ink">
                                    <td colspan="{{ 2 + $daysInMonth }}" class="px-3 py-2.5">Rekap Kelas</td>
                                    <td class="tabular px-1 py-2.5 text-center font-mono">{{ $summary['total_sakit'] }}</td>
                                    <td class="tabular px-1 py-2.5 text-center font-mono">{{ $summary['total_izin'] }}</td>
                                    <td class="tabular px-1 py-2.5 text-center font-mono">{{ $summary['total_alfa'] }}</td>
                                    <td class="tabular px-1 py-2.5 text-center font-mono">{{ $summary['total_ketidakhadiran'] }}</td>
                                    <td class="tabular px-2 py-2.5 text-right font-mono">
                                        {{ $summary['persentase_kehadiran'] !== null ? $summary['persentase_kehadiran'].'%' : '–' }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-rule/70 bg-paper/60 px-5 py-3 text-xs text-ink-soft">
                        <span>Hari efektif bulan ini: <span class="tabular font-mono font-semibold text-ink">{{ $reviewedCount }} hari</span> · Jumlah ketidakhadiran: <span class="tabular font-mono font-semibold text-ink">{{ $summary['total_ketidakhadiran'] }}</span></span>
                        <span>
                            Ketidakhadiran:
                            <span class="tabular font-mono font-semibold text-ink">{{ $summary['persentase_ketidakhadiran'] !== null ? $summary['persentase_ketidakhadiran'].'%' : '–' }}</span>
                            · Kehadiran:
                            <span class="tabular font-mono font-semibold text-ink">{{ $summary['persentase_kehadiran'] !== null ? $summary['persentase_kehadiran'].'%' : '–' }}</span>
                        </span>
                    </div>
                </x-ui.sheet>
            </div>
        @else
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Belum ada kelas terdaftar.</p>
                <p class="mt-1 text-xs text-ink-faint">Buat kelas pada modul Kelas & Penempatan terlebih dahulu.</p>
            </div>
        @endif
    </div>
</x-layouts.page>
