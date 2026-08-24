<x-layouts.page
    :title="'Rapor Siswa'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb">

    @php
        $items = $report->subjectItems();
        $legacyMapel = data_get($report->snapshot, 'mapel');
    @endphp

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Rapor Terbit</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Snapshot rapor yang tersimpan persis saat diterbitkan — dapat dicetak ulang tanpa berubah.
                </p>
            </div>
            <x-ui.button variant="primary" icon="arrow-down-tray" href="{{ route('guru.rapor.unduh', $report) }}">Unduh PDF</x-ui.button>
        </div>

        <div class="mt-6">
            <x-ui.sheet title="Rapor {{ data_get($report->snapshot, 'siswa') }}" pinned ruled>
                <dl class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">NIS</dt>
                        <dd class="tabular mt-0.5 font-mono text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'nis') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Nama Siswa</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'siswa') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Kelas</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'kelas') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Tahun Ajaran / Semester</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'tahun') }} · {{ ucfirst(data_get($report->snapshot, 'semester')) }}</dd>
                    </div>
                </dl>

                <div class="mt-6 overflow-x-auto rounded-[var(--radius-control)] ring-1 ring-inset ring-rule">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-rule-strong">
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Mata Pelajaran</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Nilai</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule/70">
                            @if ($items->isNotEmpty())
                                @foreach ($items as $item)
                                    <tr class="transition hover:bg-paper/60">
                                        <td class="px-4 py-3">
                                            <p class="font-semibold text-ink">{{ $item->subject_name }}</p>
                                            <p class="mt-0.5 text-[11px] text-ink-faint">{{ $item->teacher_name }}</p>
                                        </td>
                                        <td class="tabular px-4 py-3 text-right font-mono font-semibold text-ink">{{ $item->score ?? '–' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <x-ui.badge :variant="$item->score !== null ? 'success' : 'neutral'">{{ $item->score !== null ? \App\Support\Rapor::predikat($item->score) : '–' }}</x-ui.badge>
                                        </td>
                                    </tr>
                                @endforeach
                            @elseif ($legacyMapel)
                                @php $score = (int) data_get($report->snapshot, 'score'); @endphp
                                <tr class="transition hover:bg-paper/60">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-ink">{{ $legacyMapel }}</p>
                                        <p class="mt-0.5 text-[11px] text-ink-faint">{{ data_get($report->snapshot, 'guru') }}</p>
                                    </td>
                                    <td class="tabular px-4 py-3 text-right font-mono font-semibold text-ink">{{ $score }}</td>
                                    <td class="px-4 py-3 text-right"><x-ui.badge variant="success">{{ \App\Support\Rapor::predikat($score) }}</x-ui.badge></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-xs text-ink-faint">Belum ada nilai tercatat.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <p class="mt-6 border-t border-rule/70 pt-4 text-xs text-ink-faint">
                    Diterbitkan pada {{ \Carbon\Carbon::parse(data_get($report->snapshot, 'terbit_pada'))->isoFormat('D MMM YYYY, HH:mm') }}.
                </p>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
