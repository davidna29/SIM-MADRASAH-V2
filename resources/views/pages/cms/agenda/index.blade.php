<x-layouts.page
    :title="'Agenda & Pengumuman'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.agenda.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Agenda & Pengumuman</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Kegiatan dan pemberitahuan madrasah — tampil di website sesuai masa tampil.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('cms.agenda.create') }}">Tambah</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('cms.agenda.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="jenis" class="block pb-1.5 text-xs font-bold text-ink">Jenis</label>
                    <x-ui.select name="jenis" :full="false" class="w-44" :options="['agenda' => 'Agenda', 'pengumuman' => 'Pengumuman']" :selected="request('jenis')" placeholder="Semua jenis" />
                </div>
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="false" class="w-40" :options="['aktif' => 'Aktif', 'arsip' => 'Arsip']" :selected="request('status')" placeholder="Semua status" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('cms.agenda.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Daftar Agenda & Pengumuman" :subtitle="$agenda->total() . ' item'" pinned :padding="false">
                <x-ui.table :headers="['Judul', 'Jenis', 'Tanggal', 'Target', 'Status', '']">
                    <x-slot name="emptySlot">Belum ada agenda/pengumuman.</x-slot>
                    <x-slot>
                        @foreach ($agenda as $item)
                            <tr class="transition hover:bg-paper/60">
                                <td class="max-w-[320px] px-4 py-3">
                                    <p class="truncate font-semibold text-ink">{{ $item->title }}</p>
                                    <p class="mt-0.5 text-xs text-ink-faint">{{ $item->lokasi ?: '—' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$item->jenis === 'agenda' ? 'info' : 'warning'">{{ $item->jenis === 'agenda' ? 'Agenda' : 'Pengumuman' }}</x-ui.badge>
                                </td>
                                <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs text-ink-soft">
                                    {{ $item->tanggal?->format('d M Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ ucfirst($item->target) }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$item->status === 'aktif' ? 'success' : 'neutral'">{{ ucfirst($item->status) }}</x-ui.badge>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button size="sm" variant="secondary" icon="pencil-square" href="{{ route('cms.agenda.edit', $item) }}">Ubah</x-ui.button>
                                        <form method="POST" action="{{ route('cms.agenda.destroy', $item) }}" onsubmit="return confirm('Hapus item ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3">
                    <x-ui.pagination :current="$agenda->currentPage()" :last="$agenda->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
