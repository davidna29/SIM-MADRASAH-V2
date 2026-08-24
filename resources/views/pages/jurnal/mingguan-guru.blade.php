<x-layouts.page
    :title="'Jurnal Mingguan per Guru'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="jurnal.admin.mingguan.guru">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Jurnal Mingguan per Guru</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Susunan kegiatan pembelajaran mingguan seorang guru lintas seluruh kelas dan mapel
                    pada pekan yang sama. Hanya jurnal berstatus <span class="font-semibold">Terisi</span> yang dicantumkan.
                </p>
            </div>
            <div class="flex items-center gap-2 print:hidden">
                <x-ui.button variant="secondary" icon="printer" onclick="window.print()">Cetak</x-ui.button>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('jurnal.admin.mingguan.guru') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4 print:hidden">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="teacher_id" class="block pb-1.5 text-xs font-bold text-ink">Guru</label>
                    <x-ui.select name="teacher_id" :full="false" class="w-56" :options="$teachers->pluck('name', 'id')" :selected="$teacher?->id" />
                </div>
                <div>
                    <label for="week_start" class="block pb-1.5 text-xs font-bold text-ink">Awal Minggu (Senin)</label>
                    <input type="date" name="week_start" value="{{ $weekStart->format('Y-m-d') }}"
                        class="rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    <p class="pt-1 text-[11px] text-ink-faint">Sistem menghitung otomatis Senin–Sabtu.</p>
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Tampilkan</x-ui.button>
            </div>
        </form>

        @if ($teacher)
            <div class="mt-6">
                <x-ui.sheet
                    title="Jurnal Kegiatan Pembelajaran Mingguan"
                    :subtitle="$teacher->name . ' · ' . $weekRange"
                    pinned
                    :padding="false">

                    <!-- Kepala formulir -->
                    <div class="border-b border-rule/70 px-5 py-4 sm:px-6">
                        <div class="grid grid-cols-1 gap-px overflow-hidden rounded-[var(--radius-control)] bg-rule-strong ring-1 ring-inset ring-rule-strong sm:grid-cols-2 lg:grid-cols-4">
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Bulan</p>
                                <p class="mt-0.5 text-sm font-bold text-ink">{{ $monthLabel }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Rentang Tanggal</p>
                                <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ $weekRange }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Guru</p>
                                <p class="mt-0.5 text-sm font-bold text-ink">{{ $teacher->name }}</p>
                            </div>
                            <div class="bg-paper px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Beban Penugasan</p>
                                <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ $rombelCount }} rombel · {{ $mapelCount }} mapel</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel mingguan -->
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[780px] border-collapse text-sm">
                            <thead>
                                <tr class="border-b-2 border-rule-strong bg-paper-deep/60">
                                    <th scope="col" class="w-24 px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Hari</th>
                                    <th scope="col" class="w-16 px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Tgl</th>
                                    <th scope="col" class="w-16 px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Jam Ke</th>
                                    <th scope="col" class="w-40 px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Mata Pelajaran</th>
                                    <th scope="col" class="min-w-[220px] px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Materi Pokok / Sub Materi Pokok</th>
                                    <th scope="col" class="w-28 px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Kelas</th>
                                    <th scope="col" class="min-w-[180px] px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $dayLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; @endphp
                                @foreach ($days as $day)
                                    @php
                                        $entries = $day['entries'];
                                        $count = $entries->count();
                                        $dayLabel = $dayLabels[$loop->index] ?? '';
                                    @endphp
                                    @if ($count === 0)
                                        <tr class="border-b border-rule/70">
                                            <td class="bg-paper-deep/40 px-4 py-3 font-bold text-ink">{{ $dayLabel }}</td>
                                            <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $day['date']->format('j') }}</td>
                                            <td colspan="5" class="px-4 py-3 text-center text-xs italic text-ink-faint">Belum ada jurnal terisi</td>
                                        </tr>
                                    @else
                                        @foreach ($entries as $i => $entry)
                                            <tr class="border-b border-rule/70 align-top">
                                                @if ($i === 0)
                                                    <td class="bg-paper-deep/40 px-4 py-3 font-bold text-ink" rowspan="{{ $count }}">{{ $dayLabel }}</td>
                                                @endif
                                                <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $day['date']->format('j') }}</td>
                                                <td class="tabular whitespace-nowrap px-4 py-3 text-center font-mono text-xs text-ink-faint">{{ $entry->period_no ? 'Ke-'.$entry->period_no : '—' }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-ink">{{ $entry->assignment?->subject?->name ?? '—' }}</td>
                                                <td class="px-4 py-3 text-[13px] leading-relaxed text-ink">{{ $entry->materi }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-ink">{{ $entry->assignment?->classGroup?->name ?? '—' }}</td>
                                                <td class="px-4 py-3 text-xs leading-relaxed text-ink-soft">{{ $entry->catatan ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-rule/70 px-5 py-3 text-xs text-ink-faint">
                        Total {{ collect($days)->sum(fn ($d) => $d['entries']->count()) }} catatan terisi pada pekan ini.
                        Jurnal berstatus draf tidak dicantumkan.
                    </div>
                </x-ui.sheet>
            </div>
        @else
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Belum ada guru terdaftar.</p>
                <p class="mt-1 text-xs text-ink-faint">Data guru diisi pada modul Data Guru & Pegawai.</p>
            </div>
        @endif
    </div>
</x-layouts.page>
