<x-layouts.page
    :title="'Unit Kerja'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="unit-kerja.index">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Unit Kerja</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">Daftar unit kerja / bagian di madrasah.</p>
            </div>
            @can('create', \App\Models\OrganizationalUnit::class)
                <x-ui.button variant="primary" icon="plus" href="{{ route('unit-kerja.create') }}">Tambah Unit</x-ui.button>
            @endcan
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>@foreach ($errors->all() as $error) {{ $error }} @endforeach</x-ui.alert></div>
        @endif

        <div class="mt-6">
            <x-ui.sheet :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-rule-strong">
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Kode</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama Unit</th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Pegawai</th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule/70">
                            @forelse ($units as $unit)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $unit->code }}</td>
                                    <td class="px-4 py-3 font-semibold text-ink">
                                        <a href="{{ route('unit-kerja.show', $unit) }}" class="text-primary hover:underline">{{ $unit->name }}</a>
                                    </td>
                                    <td class="tabular px-4 py-3 text-center font-mono text-ink">{{ $unit->employees_count }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            @can('update', $unit)
                                                <x-ui.button variant="ghost" size="sm" href="{{ route('unit-kerja.edit', $unit) }}" icon="pencil-square" aria-label="Ubah" />
                                            @endcan
                                            @can('delete', $unit)
                                                @if ($unit->employees_count === 0)
                                                    <form method="POST" action="{{ route('unit-kerja.destroy', $unit) }}" class="inline" x-data="{ open: false }" @submit.prevent="open = true">
                                                        @csrf @method('DELETE')
                                                        <x-ui.modal title="Hapus Unit?" description="Hapus unit {{ $unit->name }}?" x-show="open" @close="open = false">
                                                            <x-slot:actions>
                                                                <x-ui.button variant="ghost" @click="open = false">Batal</x-ui.button>
                                                                <x-ui.button variant="danger" type="submit">Hapus</x-ui.button>
                                                            </x-slot:actions>
                                                        </x-ui.modal>
                                                        <x-ui.button variant="ghost" size="sm" icon="trash" type="submit" aria-label="Hapus" />
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-xs text-ink-faint">Belum ada unit kerja.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>