<x-layouts.page
    :title="'Mata Pelajaran'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mapel.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Mata Pelajaran</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Papan daftar mata pelajaran madrasah — dipakai lintas kelas, penugasan, dan penilaian.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('mapel.create') }}">Tambah Mata Pelajaran</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <form method="GET" action="{{ route('mapel.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="w-64">
                    <div class="flex items-center gap-2 rounded-[var(--radius-control)] bg-paper px-3 ring-1 ring-inset ring-rule-strong transition focus-within:ring-2 focus-within:ring-primary">
                        <x-svg-magnifying-glass class="size-4 shrink-0 text-ink-faint" aria-hidden="true" />
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kode mapel…" class="w-full bg-transparent py-2.5 text-sm text-ink placeholder:text-ink-faint focus:outline-none" aria-label="Cari mapel">
                    </div>
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Cari</x-ui.button>
                @if (request('q'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('mapel.index') }}">Reset</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-4">
            <x-ui.sheet :padding="false">
                <x-ui.table :headers="['Kode', 'Nama Mata Pelajaran', '']">
                    <x-slot name="emptySlot">Tidak ada mata pelajaran.</x-slot>
                    <x-slot>
                        @foreach ($subjects as $subject)
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular px-4 py-3 font-mono text-xs font-bold text-primary">{{ $subject->code }}</td>
                                <td class="px-4 py-3 font-semibold text-ink">{{ $subject->name }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('mapel.edit', $subject) }}">Ubah</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-4 py-3.5">
                    <x-ui.pagination :current="$subjects->currentPage()" :last="$subjects->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
