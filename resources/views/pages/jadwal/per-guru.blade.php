<x-layouts.page
    :title="'Jadwal Guru'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Jadwal Guru</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    {{ $teacher->name }} — jadwal mengajar lintas rombel (turunan dari tabel master).
                </p>
            </div>
            <x-ui.button variant="primary" icon="arrow-down-tray" href="{{ route('jadwal.guru.cetak', $teacher) }}">Cetak PDF</x-ui.button>
        </div>

        <div class="mt-6 overflow-x-auto rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
            <table class="w-full min-w-[720px] border-collapse text-sm">
                <thead>
                    <tr class="border-b-2 border-rule-strong bg-paper/50">
                        <th scope="col" class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Hari</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Jam ke-</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Kelas</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Mata Pelajaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule/70">
                    @forelse ($cells->sortBy(fn ($c) => array_search($c->day, $days) * 100 + $c->period_no) as $cell)
                        <tr class="transition hover:bg-paper/60">
                            <td class="px-3 py-3 text-xs font-extrabold uppercase tracking-wide text-board-deep">{{ ucfirst($cell->day) }}</td>
                            <td class="tabular px-3 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $cell->period_no }}</td>
                            <td class="px-3 py-3">
                                <x-ui.badge variant="info">{{ $cell->classGroup?->name }}</x-ui.badge>
                            </td>
                            <td class="px-3 py-3 font-semibold text-ink">{{ $cell->subject?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-6 text-center text-xs text-ink-faint">Belum ada jadwal untuk guru ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.page>
