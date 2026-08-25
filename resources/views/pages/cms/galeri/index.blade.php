<x-layouts.page
    :title="'Galeri'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.galeri.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Galeri & Media</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Album dokumentasi foto & video — album publik tampil di website madrasah.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('cms.galeri.create') }}">Tambah Album</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('cms.galeri.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="false" class="w-44" :options="['publik' => 'Publik', 'privat' => 'Privat']" :selected="request('status')" placeholder="Semua status" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Judul album…" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('cms.galeri.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Daftar Album" :subtitle="$albums->total() . ' album'" pinned :padding="false">
                <x-ui.table :headers="['Album', 'Kategori', 'Isi', 'Status', 'Dibuat', '']">
                    <x-slot name="emptySlot">Belum ada album.</x-slot>
                    <x-slot>
                        @foreach ($albums as $album)
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($album->cover_image)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($album->cover_image) }}"
                                                alt="" class="size-12 rounded-[var(--radius-control)] object-cover">
                                        @else
                                            <span class="flex size-12 items-center justify-center rounded-[var(--radius-control)] bg-paper-deep text-ink-faint">
                                                <x-svg-photo class="size-5" aria-hidden="true" />
                                            </span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">{{ $album->title }}</p>
                                            <p class="tabular mt-0.5 font-mono text-[11px] text-ink-faint">/{{ $album->slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ $album->kategori ?: '—' }}</td>
                                <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink">{{ $album->items_count }} item</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$album->status === 'publik' ? 'success' : 'neutral'">{{ ucfirst($album->status) }}</x-ui.badge>
                                </td>
                                <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs text-ink-faint">{{ $album->created_at->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button size="sm" variant="secondary" icon="eye" href="{{ route('cms.galeri.show', $album) }}">Kelola</x-ui.button>
                                        <form method="POST" action="{{ route('cms.galeri.destroy', $album) }}" onsubmit="return confirm('Hapus album beserta seluruh isinya?');">
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
                    <x-ui.pagination :current="$albums->currentPage()" :last="$albums->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
