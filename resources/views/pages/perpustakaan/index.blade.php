<x-layouts.page
    :title="'Katalog Buku'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="perpustakaan.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Katalog Buku</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Daftar koleksi buku perpustakaan beserta stok, lokasi, dan info ebook.
                </p>
            </div>
            @can('create', \App\Models\LibraryBook::class)
                <x-ui.button variant="primary" icon="plus" href="{{ route('perpustakaan.create') }}">Tambah Buku</x-ui.button>
            @endcan
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('perpustakaan.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="category_id" class="block pb-1.5 text-xs font-bold text-ink">Kategori</label>
                    <x-ui.select name="category_id" :full="false" class="w-48" :options="$categories->pluck('name', 'id')" :selected="request('category_id')" placeholder="Semua kategori" />
                </div>
                <div>
                    <label for="is_ebook" class="block pb-1.5 text-xs font-bold text-ink">Jenis</label>
                    <x-ui.select name="is_ebook" :full="false" class="w-36" :options="['1' => 'Ebook', '0' => 'Fisik']" :selected="request('is_ebook')" placeholder="Semua" />
                </div>
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="false" class="w-40" :options="collect(\App\Models\LibraryBook::STATUSES)->mapWithKeys(fn ($s) => [$s => ucwords(str_replace('_', ' ', $s))])" :selected="request('status')" placeholder="Semua status" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Judul / pengarang / kode…" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('perpustakaan.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Katalog Buku" :subtitle="$books->total() . ' buku'" pinned :padding="false">
                <x-ui.table :headers="['Kode', 'Judul', 'Pengarang', 'Kategori', 'Stok', 'Ebook', 'Status', '']">
                    <x-slot name="emptySlot">Belum ada buku dalam katalog.</x-slot>
                    <x-slot>
                        @foreach ($books as $book)
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $book->code }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-ink">{{ $book->title }}</p>
                                    @if ($book->publisher || $book->year)
                                        <p class="mt-0.5 text-[11px] text-ink-faint">
                                            {{ $book->publisher ?? '' }} @if ($book->year) · {{ $book->year }} @endif
                                        </p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $book->author }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $book->category?->name ?? '—' }}</td>
                                <td class="tabular px-4 py-3 text-center font-mono text-xs font-semibold text-ink">{{ $book->available_qty }}/{{ $book->total_qty }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if ($book->is_ebook)
                                        <x-ui.badge variant="info">Ebook</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral">Fisik</x-ui.badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <x-ui.badge :variant="$book->status === 'tersedia' ? 'success' : 'neutral'">{{ ucwords($book->status) }}</x-ui.badge>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button size="sm" variant="secondary" icon="eye" href="{{ route('perpustakaan.show', $book) }}">Detail</x-ui.button>
                                        @can('delete', $book)
                                            <form method="POST" action="{{ route('perpustakaan.destroy', $book) }}" onsubmit="return confirm('Hapus buku ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3">
                    <x-ui.pagination :current="$books->currentPage()" :last="$books->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
