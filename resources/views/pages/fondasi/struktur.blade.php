<x-layouts.page
    :title="'Struktur Organisasi'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="struktur.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Struktur Organisasi</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">Daftar pegawai aktif madrasah dikelompokkan berdasarkan unit kerja.</p>
            </div>
        </div>

        <div class="mt-6 space-y-6">
            @forelse ($units as $unit)
                @if ($unit->employees->isNotEmpty())
                    <x-ui.sheet :title="$unit->name" :subtitle="$unit->code . ' · ' . $unit->employees->count() . ' pegawai'" :padding="false">
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-rule-strong">
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">NIP</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Jabatan</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rule/70">
                                    @foreach ($unit->employees as $emp)
                                        <tr class="transition hover:bg-paper/60">
                                            <td class="tabular px-4 py-3 font-mono text-xs text-ink-faint">{{ $emp->nip ?? '—' }}</td>
                                            <td class="px-4 py-3 font-semibold text-ink">{{ $emp->person->name }}</td>
                                            <td class="px-4 py-3 text-ink">{{ $emp->position?->name ?? '—' }}</td>
                                            <td class="px-4 py-3"><x-ui.badge :variant="$emp->status === 'aktif' ? 'success' : 'warning'">{{ ucfirst($emp->status) }}</x-ui.badge></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-ui.sheet>
                @endif
            @empty
                <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                    <p class="text-sm font-semibold text-ink">Belum ada data unit kerja.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.page>