<x-layouts.page
    :title="'Kategori Perpustakaan'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="perpustakaan.kategori.index">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kategori Perpustakaan</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Kelola kategori atau koleksi buku perpustakaan.
                </p>
            </div>
            @can('create', \App\Models\LibraryCategory::class)
                <x-ui.modal id="modal-tambah-kategori" title="Tambah Kategori">
                    <x-slot:trigger>
                        <x-ui.button variant="primary" icon="plus">Tambah Kategori</x-ui.button>
                    </x-slot:trigger>

                    <form method="POST" action="{{ route('perpustakaan.kategori.store') }}" class="space-y-4">
                        @csrf
                        <x-ui.field label="Nama Kategori" required :error="$errors->first('name')">
                            <x-ui.input name="name" :value="old('name')" placeholder="Mis. Fiksi, Agama, Pelajaran…" />
                        </x-ui.field>
                        <x-ui.field label="Deskripsi" :error="$errors->first('description')">
                            <textarea name="description" rows="2" placeholder="Singkat…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description') }}</textarea>
                        </x-ui.field>
                    </form>

                    <x-slot:footer>
                        <x-ui.button type="button" variant="ghost" x-on:click="open = false">Batal</x-ui.button>
                        <x-ui.button type="button" variant="primary" x-on:click="$root.querySelector('[role=dialog] form').requestSubmit()">Simpan</x-ui.button>
                    </x-slot:footer>
                </x-ui.modal>
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

        <div class="mt-6">
            <x-ui.sheet title="Daftar Kategori" :subtitle="$categories->total() . ' kategori'" pinned :padding="false">
                <x-ui.table :headers="['Kategori', 'Jumlah Buku', '']">
                    <x-slot name="emptySlot">Belum ada kategori.</x-slot>
                    <x-slot>
                        @foreach ($categories as $category)
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-ink">{{ $category->name }}</p>
                                    @if ($category->description)
                                        <p class="mt-0.5 text-[11px] text-ink-faint">{{ $category->description }}</p>
                                    @endif
                                </td>
                                <td class="tabular px-4 py-3 text-center font-mono text-xs font-semibold text-ink">{{ $category->books_count }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @can('update', $category)
                                            <form method="POST" action="{{ route('perpustakaan.kategori.update', $category) }}" class="inline-flex"
                                                x-data="{ editing: false, name: '{{ $category->name }}', desc: '{{ $category->description ?? '' }}' }">
                                                @csrf
                                                @method('PUT')
                                                <template x-if="!editing">
                                                    <x-ui.button size="sm" variant="ghost" icon="pencil" x-on:click="editing=true">Ubah</x-ui.button>
                                                </template>
                                                <template x-if="editing">
                                                    <div class="flex items-center gap-2">
                                                        <x-ui.input name="name" x-model="name" class="w-40" />
                                                        <x-ui.input name="description" x-model="desc" class="w-48" placeholder="Deskripsi…" />
                                                        <x-ui.button type="submit" size="sm" variant="primary">Simpan</x-ui.button>
                                                        <x-ui.button type="button" size="sm" variant="ghost" x-on:click="editing=false">Batal</x-ui.button>
                                                    </div>
                                                </template>
                                            </form>
                                        @endcan
                                        @can('delete', $category)
                                            <form method="POST" action="{{ route('perpustakaan.kategori.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?');">
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
                    <x-ui.pagination :current="$categories->currentPage()" :last="$categories->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
