@php
    $p = $portofolio;
@endphp

<x-layouts.root title="Verifikasi Portofolio — {{ $student->name }}">
    <body class="min-h-screen bg-paper">
        <div class="mx-auto max-w-2xl px-4 py-8">
            {{-- Header --}}
            <div class="mb-6 text-center">
                <div class="mx-auto mb-3 flex size-14 items-center justify-center rounded-full bg-primary text-lg font-bold text-white">
                    {{ mb_strtoupper(mb_substr($student->name, 0, 2)) }}
                </div>
                <h1 class="text-xl font-bold tracking-tight text-ink">{{ $student->name }}</h1>
                <p class="mt-1 text-sm text-ink-soft">NIS: <span class="font-mono font-semibold text-ink">{{ $student->nis }}</span> · Kelas: {{ $p['kelas'] }}</p>
                <p class="mt-0.5 text-xs text-ink-faint">{{ $p['report']?->academicYear?->name ?? '–' }} · {{ ucfirst($p['report']?->semester ?? '–') }}</p>
            </div>

            {{-- Ringkasan --}}
            <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-sheet bg-sheet p-3 text-center ring-1 ring-inset ring-rule/60">
                    <p class="text-xs text-ink-soft">Kehadiran</p>
                    <p class="mt-1 text-lg font-bold tabular text-ink">{{ $p['persentaseHadir'] }}%</p>
                </div>
                <div class="rounded-sheet bg-sheet p-3 text-center ring-1 ring-inset ring-rule/60">
                    <p class="text-xs text-ink-soft">Prestasi</p>
                    <p class="mt-1 text-lg font-bold tabular text-ink">{{ $p['prestasi']->count() }}</p>
                </div>
                <div class="rounded-sheet bg-sheet p-3 text-center ring-1 ring-inset ring-rule/60">
                    <p class="text-xs text-ink-soft">Pelanggaran</p>
                    <p class="mt-1 text-lg font-bold tabular {{ $p['totalPoinPelanggaran'] > 50 ? 'text-danger' : 'text-ink' }}">{{ $p['totalPoinPelanggaran'] }} poin</p>
                </div>
                <div class="rounded-sheet bg-sheet p-3 text-center ring-1 ring-inset ring-rule/60">
                    <p class="text-xs text-ink-soft">SPP</p>
                    <p class="mt-1 text-lg font-bold tabular text-ink">{{ $p['sppLunas'] }}/{{ $p['sppTotal'] }}</p>
                </div>
            </div>

            {{-- Rapor --}}
            @if ($p['raporItems']->isNotEmpty())
                <div class="mb-6 rounded-sheet bg-sheet p-5 ring-1 ring-inset ring-rule/60">
                    <h2 class="mb-3 text-sm font-bold text-ink">Nilai / Rapor</h2>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-rule/70 text-left text-xs font-bold uppercase text-ink-soft">
                                <th class="pb-2">Mata Pelajaran</th>
                                <th class="pb-2 text-right">Nilai</th>
                                <th class="pb-2 text-center">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule/70">
                            @foreach ($p['raporItems'] as $item)
                                <tr>
                                    <td class="py-2 font-semibold text-ink">{{ $item->subject_name }}</td>
                                    <td class="py-2 text-right font-mono tabular">{{ $item->score ?? '–' }}</td>
                                    <td class="py-2 text-center">
                                        @if ($item->score)
                                            <x-ui.badge variant="{{ $item->score >= 85 ? 'success' : ($item->score >= 70 ? 'info' : 'warning') }}">
                                                {{ \App\Support\Rapor::predikat($item->score) }}
                                            </x-ui.badge>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Kehadiran --}}
            <div class="mb-6 rounded-sheet bg-sheet p-5 ring-1 ring-inset ring-rule/60">
                <h2 class="mb-3 text-sm font-bold text-ink">Kehadiran</h2>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-rule/70 text-left text-xs font-bold uppercase text-ink-soft">
                            <th class="pb-2">Bulan</th>
                            <th class="pb-2 text-center">H</th>
                            <th class="pb-2 text-center">S</th>
                            <th class="pb-2 text-center">I</th>
                            <th class="pb-2 text-center">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rule/70">
                        @foreach ($p['kehadiran'] as $k)
                            <tr>
                                <td class="py-2 text-ink">{{ $k['label'] }}</td>
                                <td class="py-2 text-center font-mono tabular text-success">{{ $k['H'] }}</td>
                                <td class="py-2 text-center font-mono tabular text-warning">{{ $k['S'] }}</td>
                                <td class="py-2 text-center font-mono tabular text-info">{{ $k['I'] }}</td>
                                <td class="py-2 text-center font-mono tabular text-danger">{{ $k['A'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Prestasi --}}
            @if ($p['prestasi']->isNotEmpty())
                <div class="mb-6 rounded-sheet bg-sheet p-5 ring-1 ring-inset ring-rule/60">
                    <h2 class="mb-3 text-sm font-bold text-ink">Prestasi</h2>
                    <ul class="space-y-2">
                        @foreach ($p['prestasi'] as $item)
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-ink">{{ $item->nama_kegiatan }}</span>
                                <x-ui.badge variant="success">{{ ucfirst($item->tingkat) }}</x-ui.badge>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-center text-xs text-ink-faint">Diverifikasi dari SIM Madrasah · {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </body>
</x-layouts.root>
