<x-layouts.page
    :title="$unit->name"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="unit-kerja.show">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $unit->name }}</h1>
                <p class="mt-1.5 text-sm text-ink-soft">Kode: <span class="tabular font-mono font-semibold text-ink">{{ $unit->code }}</span> · {{ $unit->employees->count() }} pegawai aktif</p>
            </div>
            @can('update', $unit)
                <x-ui.button variant="secondary" size="sm" icon="pencil-square" href="{{ route('unit-kerja.edit', $unit) }}">Ubah</x-ui.button>
            @endcan
        </div>

        <div class="mt-6">
            <x-ui.sheet title="Pegawai di Unit Ini" :padding="false">
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
                            @forelse ($unit->employees as $emp)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="tabular px-4 py-3 font-mono text-xs text-ink-faint">{{ $emp->nip ?? '—' }}</td>
                                    <td class="px-4 py-3 font-semibold text-ink">{{ $emp->person->name }}</td>
                                    <td class="px-4 py-3 text-ink">{{ $emp->position?->name ?? '—' }}</td>
                                    <td class="px-4 py-3"><x-ui.badge :variant="$emp->status === 'aktif' ? 'success' : 'warning'">{{ ucfirst($emp->status) }}</x-ui.badge></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-xs text-ink-faint">Belum ada pegawai di unit ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>