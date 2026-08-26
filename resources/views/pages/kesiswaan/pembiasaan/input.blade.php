<x-layouts.page
    :title="$title"
    :roleLabel="$roleLabel"
    :breadcrumb="[['label' => 'Kesiswaan', 'href' => route('dashboard')], ['label' => $modul === 'ppi' ? 'PPI' : 'Tahfidz', 'href' => route($modul.'.index')], ['label' => 'Input Nilai']]"
    active-route="{{ $modul }}.input">

    @php
        $grade = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];
    @endphp

    <div class="mx-auto max-w-full">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $title }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Input nilai per siswa. Kolom yang dapat diisi hanya
                    <strong>Kelas {{ $current ? $grade[$current['kelas']] : '-' }} Semester {{ $current['semester'] ?? '-' }}</strong>
                    (sel lain terkunci otomatis sesuai konfigurasi materi).
                </p>
            </div>
            <x-ui.button variant="secondary" icon="printer" href="{{ route($modul.'.cetak', $siswa) }}">Cetak PDF</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if (! $current)
            <div class="mt-6">
                <x-ui.alert variant="warning">Siswa tidak memiliki enrollmen aktif, sehingga belum ada kolom yang dapat diisi.</x-ui.alert>
            </div>
        @else
            <form method="POST" action="{{ route($modul.'.store', $siswa) }}" class="mt-6">
                @csrf
                <div class="overflow-x-auto rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                    <table class="w-full min-w-[1100px] border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-rule-strong">
                                <th class="px-3 py-2 text-left text-xs font-bold text-ink-soft">No</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-ink-soft">Materi</th>
                                @foreach ($pairs as $pair)
                                    <th class="px-2 py-2 text-center text-xs font-bold {{ $editablePair === $pair[0].'-'.$pair[1] ? 'bg-primary-soft text-primary-strong' : '' }}">
                                        {{ $grade[$pair[0]] }}.{{ $pair[1] }}
                                    </th>
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
                                        <td class="px-2 py-1 text-center {{ $editablePair === $cell['kelas'].'-'.$cell['semester'] ? 'bg-primary-soft/40' : '' }}">
                                            @if ($editablePair === $cell['kelas'].'-'.$cell['semester'] && $cell['aktif'])
                                                <input type="number" min="0" max="100" name="nilai[{{ $row['materi']->id }}]"
                                                    value="{{ old('nilai.'.$row['materi']->id, $cell['nilai']) }}"
                                                    class="w-16 rounded-[var(--radius-control)] bg-sheet px-2 py-1 text-center text-sm ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">
                                            @elseif ($cell['nilai'] !== null)
                                                {{ $cell['nilai'] }}
                                            @else
                                                <span class="text-ink-faint">–</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr class="border-t border-rule-strong font-semibold">
                                <td></td>
                                <td class="px-3 py-2">Jumlah / Rata-rata / Kategori</td>
                                @foreach ($pairs as $pair)
                                    @php $f = $footers[$pair[0].'-'.$pair[1]]; @endphp
                                    <td class="px-2 py-1 text-center text-xs leading-tight">
                                        {{ $f['jumlah'] }}<br>{{ $f['rata_rata'] ?? '–' }}<br>{{ $f['kategori'] }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <x-ui.button variant="ghost" icon="arrow-left" href="{{ route($modul.'.index') }}">Kembali</x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check">Simpan Nilai</x-ui.button>
                </div>
            </form>
        @endif
    </div>
</x-layouts.page>
