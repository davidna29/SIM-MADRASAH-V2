<x-layouts.page
    :title="'Inventaris Barang'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="inventaris.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Inventaris Barang</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Daftar aset barang madrasah beserta kondisi, lokasi, mutasi & pemeliharaan.
                </p>
            </div>
            @can('create', \App\Models\InventoryItem::class)
                <x-ui.button variant="primary" icon="plus" href="{{ route('inventaris.create') }}">Tambah Barang</x-ui.button>
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
        <form method="GET" action="{{ route('inventaris.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="category_id" class="block pb-1.5 text-xs font-bold text-ink">Kategori</label>
                    <x-ui.select name="category_id" :full="false" class="w-48" :options="$categories->pluck('name', 'id')" :selected="request('category_id')" placeholder="Semua kategori" />
                </div>
                <div>
                    <label for="condition" class="block pb-1.5 text-xs font-bold text-ink">Kondisi</label>
                    <x-ui.select name="condition" :full="false" class="w-44" :options="collect($conditions)->mapWithKeys(fn ($c) => [$c => ucwords(str_replace('_', ' ', $c))])" :selected="request('condition')" placeholder="Semua kondisi" />
                </div>
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="false" class="w-44" :options="collect($statuses)->mapWithKeys(fn ($s) => [$s => ucwords(str_replace('_', ' ', $s))])" :selected="request('status')" placeholder="Semua status" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Nama / kode / merek…" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('inventaris.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Daftar Barang" :subtitle="$items->total() . ' barang'" pinned :padding="false">
                <x-ui.table :headers="['Kode', 'Barang', 'Kategori', 'Jumlah', 'Kondisi', 'Status', '']">
                    <x-slot name="emptySlot">Belum ada barang inventaris.</x-slot>
                    <x-slot>
                        @foreach ($items as $item)
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $item->code }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-ink">{{ $item->name }}</p>
                                    @if ($item->brand || $item->location)
                                        <p class="mt-0.5 text-[11px] text-ink-faint">
                                            {{ trim(($item->brand ?? '').' '.($item->model ?? '')) }}
                                            @if ($item->location) · {{ $item->location }} @endif
                                        </p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $item->category?->name ?? '—' }}</td>
                                <td class="tabular px-4 py-3 text-center font-mono text-xs font-semibold text-ink">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <x-ui.badge :variant="$item->condition === 'baik' ? 'success' : ($item->condition === 'rusak_ringan' ? 'warning' : 'danger')">{{ ucwords(str_replace('_', ' ', $item->condition)) }}</x-ui.badge>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <x-ui.badge :variant="$item->status === 'tersedia' ? 'success' : ($item->status === 'dalam_perawatan' ? 'warning' : 'neutral')">{{ ucwords(str_replace('_', ' ', $item->status)) }}</x-ui.badge>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button size="sm" variant="secondary" icon="eye" href="{{ route('inventaris.show', $item) }}">Detail</x-ui.button>
                                        @can('delete', $item)
                                            <form method="POST" action="{{ route('inventaris.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini?');">
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
                    <x-ui.pagination :current="$items->currentPage()" :last="$items->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
