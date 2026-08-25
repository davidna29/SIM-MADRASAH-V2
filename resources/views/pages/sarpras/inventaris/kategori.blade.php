<x-layouts.page
    :title="'Kategori Barang'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="inventaris.kategori.index">

    <div class="mx-auto max-w-4xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kategori Barang</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Kelompok jenis barang untuk memudahkan pelaporan inventaris.
            </p>
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

        <!-- Tambah kategori -->
        <form method="POST" action="{{ route('inventaris.kategori.store') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-5">
            @csrf
            <p class="text-sm font-bold text-ink">Tambah Kategori</p>
            <div class="mt-4 flex flex-wrap items-end gap-3">
                <div class="min-w-52 flex-1">
                    <x-ui.field label="Nama" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name')" placeholder="Mis. Elektronik" />
                    </x-ui.field>
                </div>
                <x-ui.button type="submit" variant="primary" icon="plus">Tambah</x-ui.button>
            </div>
        </form>

        <!-- Daftar kategori -->
        <div class="mt-6">
            <x-ui.sheet title="Daftar Kategori" :subtitle="$categories->total() . ' kategori'" :padding="false">
                <x-ui.table :headers="['Nama', 'Deskripsi', 'Jumlah Barang', '']">
                    <x-slot name="emptySlot">Belum ada kategori.</x-slot>
                    <x-slot>
                        @foreach ($categories as $category)
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3 font-semibold text-ink">{{ $category->name }}</td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ $category->description ?? '—' }}</td>
                                <td class="tabular px-4 py-3 text-center font-mono text-xs font-semibold text-ink">{{ $category->items_count }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('inventaris.kategori.destroy', $category) }}"
                                        onsubmit="return confirm('Hapus kategori ini? (kategori berisi barang tidak dapat dihapus)');">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3">
                    <x-ui.pagination :current="$categories->currentPage()" :last="$categories->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
