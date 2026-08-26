<x-layouts.page
    :title="$title"
    :roleLabel="$roleLabel"
    :breadcrumb="[['label' => 'Kesiswaan', 'href' => route('dashboard')], ['label' => $modul === 'ppi' ? 'PPI' : 'Tahfidz', 'href' => route($modul.'.index')], ['label' => 'Cetak']]"
    active-route="{{ $modul }}.cetak">

    @php
        $grade = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];
    @endphp

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $title }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Preview cetak {{ $label }} — {{ $siswa->name }}.
                </p>
            </div>
            <div class="flex items-center gap-2 print:hidden">
                <x-ui.button variant="secondary" icon="arrow-left" href="{{ route($modul.'.index') }}">Kembali</x-ui.button>
                <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route($modul.'.cetak.pdf', $siswa) }}">Download PDF</x-ui.button>
                <x-ui.button variant="secondary" icon="table-cells" href="{{ route($modul.'.cetak.excel', $siswa) }}">Export Excel</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6 print:hidden">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
            <!-- Kepala formulir -->
            <div class="border-b border-rule/70 px-5 py-4 sm:px-6">
                <div class="grid grid-cols-1 gap-px overflow-hidden rounded-[var(--radius-control)] bg-rule-strong ring-1 ring-inset ring-rule-strong sm:grid-cols-3">
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Nama Siswa</p>
                        <p class="mt-0.5 text-sm font-bold text-ink">{{ $siswa->name }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Kelas / Semester</p>
                        <p class="mt-0.5 text-sm font-bold text-ink">{{ $current ? $grade[$current['kelas']] : '-' }} · Semester {{ $current['semester'] ?? '-' }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Tahun Pelajaran</p>
                        <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ $current['tahun'] ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabel matrix -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] border-collapse text-sm">
                    <thead>
                        <tr class="border-b-2 border-rule-strong bg-paper-deep/60">
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">No</th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Materi</th>
                            @foreach ($pairs as $pair)
                                <th class="px-2 py-2 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">{{ $grade[$pair[0]] }}.{{ $pair[1] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rule/70">
                        @foreach ($rows as $row)
                            <tr>
                                <td class="px-3 py-2 text-ink-soft">{{ $row['materi']->no_urut }}</td>
                                <td class="px-3 py-2">
                                    {{ $row['materi']->nama_materi }}
                                    @if ($row['materi']->jenis)
                                        <span class="text-xs text-ink-faint">({{ $row['materi']->jenis }})</span>
                                    @endif
                                </td>
                                @foreach ($row['cells'] as $cell)
                                    <td class="px-2 py-1 text-center {{ $editablePair === $cell['kelas'].'-'.$cell['semester'] ? 'bg-primary-soft/40 font-semibold' : '' }}">
                                        {{ $cell['nilai'] ?? '' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr class="border-t-2 border-rule-strong bg-paper-deep/40 font-semibold">
                            <td class="px-3 py-2"></td>
                            <td class="px-3 py-2 text-xs">Jumlah</td>
                            @foreach ($pairs as $pair)
                                <td class="px-2 py-1 text-center text-xs">{{ $footers[$pair[0].'-'.$pair[1]]['jumlah'] }}</td>
                            @endforeach
                        </tr>
                        <tr class="bg-paper-deep/40 font-semibold">
                            <td class="px-3 py-2"></td>
                            <td class="px-3 py-2 text-xs">Rata-rata</td>
                            @foreach ($pairs as $pair)
                                <td class="px-2 py-1 text-center text-xs">{{ $footers[$pair[0].'-'.$pair[1]]['rata_rata'] ?? '–' }}</td>
                            @endforeach
                        </tr>
                        <tr class="bg-paper-deep/40 font-semibold">
                            <td class="px-3 py-2"></td>
                            <td class="px-3 py-2 text-xs">Kategori</td>
                            @foreach ($pairs as $pair)
                                <td class="px-2 py-1 text-center text-xs">{{ $footers[$pair[0].'-'.$pair[1]]['kategori'] }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.page>
