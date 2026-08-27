<x-layouts.page
    :title="'Struktur Aspek — '.$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => $periode->judul, 'href' => route('ujianppi.periode.show', $periode)],
        ['label' => 'Struktur Aspek'],
    ]"
    active-route="ujianppi.periode.index">

    @php
        $penguji = [1 => 'Penguji I', 2 => 'Penguji II', 3 => 'Penguji III'];
    @endphp

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Struktur Aspek Penilaian</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Induk aspek di-assign ke penguji ke-1/2/3; urutan menentukan urutan kolom di Rekap Kelas VI.
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow-left" href="{{ route('ujianppi.periode.show', $periode) }}">Kembali</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        @if (! $editable)
            <div class="mt-6">
                <x-ui.alert variant="warning" dismissible>
                    Struktur aspek terkunci — periode sudah <b>{{ $periode->statusLabel() }}</b>.
                </x-ui.alert>
            </div>
        @endif

        @if ($editable)
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                <form method="POST" action="{{ route('ujianppi.konfigurasi.aspek.store', $periode) }}" class="grid gap-3 sm:grid-cols-12 sm:items-end">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="block pb-1.5 text-xs font-bold text-ink">Kode</label>
                        <x-ui.input name="kode" placeholder="mis. 4" :value="old('kode')" />
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block pb-1.5 text-xs font-bold text-ink">Nama Induk</label>
                        <x-ui.input name="nama" placeholder="mis. Wudhu" :value="old('nama')" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block pb-1.5 text-xs font-bold text-ink">Penguji</label>
                        <x-ui.select name="penguji_urutan" :full="false" class="w-full" :options="[1 => 'Penguji I', 2 => 'Penguji II', 3 => 'Penguji III']" :selected="old('penguji_urutan', 1)" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block pb-1.5 text-xs font-bold text-ink">Urutan</label>
                        <x-ui.input type="number" name="urutan" :value="old('urutan', $categories->max('urutan') + 1 ?? 1)" min="1" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.button type="submit" variant="primary" icon="plus" class="w-full">Tambah</x-ui.button>
                    </div>
                </form>
            </div>
        @endif

        <div class="mt-6 space-y-6">
            @foreach ([1, 2, 3] as $urutanPenguji)
                @php
                    $byPenguji = $categories->where('penguji_urutan', $urutanPenguji);
                @endphp
                <x-ui.sheet :title="$penguji[$urutanPenguji]" :subtitle="$byPenguji->isEmpty() ? 'Belum ada induk aspek.' : $byPenguji->sum(fn ($c) => $c->aspects->count()).' item dalam '.$byPenguji->count().' induk'" pinned>
                    <div class="space-y-4">
                        @foreach ($byPenguji as $category)
                            <div x-data="{ editCat: false }" class="rounded-[var(--radius-control)] bg-paper ring-1 ring-inset ring-rule/60">
                                <div class="flex flex-wrap items-center justify-between gap-2 px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="tabular font-mono text-xs font-bold text-ink-faint">{{ $category->kode }}</span>
                                        <span class="text-sm font-bold text-ink">{{ $category->nama }}</span>
                                        <x-ui.badge variant="neutral" :dot="false">Urutan {{ $category->urutan }}</x-ui.badge>
                                        <x-ui.badge variant="info" :dot="false">{{ $penguji[$category->penguji_urutan] ?? $category->penguji_urutan }}</x-ui.badge>
                                    </div>
                                    @if ($editable)
                                        <div class="flex items-center gap-2">
                                            <x-ui.button variant="ghost" size="sm" @click="editCat = !editCat" x-text="editCat ? 'Tutup' : 'Ubah'"></x-ui.button>
                                            <form method="POST" action="{{ route('ujianppi.konfigurasi.aspek.destroy', [$periode, $category]) }}"
                                                onsubmit="return confirm('Hapus induk {{ $category->nama }} beserta item-nya?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" variant="ghost" size="sm" icon="trash"></x-ui.button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                @if ($editable)
                                    <div x-show="editCat" x-cloak class="border-t border-rule/70 px-4 py-4">
                                        <form method="POST" action="{{ route('ujianppi.konfigurasi.aspek.update', [$periode, $category]) }}" class="grid gap-3 sm:grid-cols-4 sm:items-end">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="block pb-1.5 text-xs font-bold text-ink">Kode</label>
                                                <x-ui.input name="kode" :value="$category->kode" />
                                            </div>
                                            <div>
                                                <label class="block pb-1.5 text-xs font-bold text-ink">Nama</label>
                                                <x-ui.input name="nama" :value="$category->nama" />
                                            </div>
                                            <div>
                                                <label class="block pb-1.5 text-xs font-bold text-ink">Penguji</label>
                                                <x-ui.select name="penguji_urutan" :full="false" class="w-full" :options="[1 => 'Penguji I', 2 => 'Penguji II', 3 => 'Penguji III']" :selected="$category->penguji_urutan" />
                                            </div>
                                            <div>
                                                <label class="block pb-1.5 text-xs font-bold text-ink">Urutan</label>
                                                <x-ui.input type="number" name="urutan" :value="$category->urutan" min="1" />
                                            </div>
                                            <div class="sm:col-span-4">
                                                <x-ui.button type="submit" variant="primary" size="sm" icon="check">Simpan Induk</x-ui.button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                <ul class="divide-y divide-rule/60 border-t border-rule/60">
                                    @foreach ($category->aspects as $aspect)
                                        <li x-data="{ editItem: false }" class="flex flex-wrap items-center justify-between gap-2 px-4 py-2">
                                            <div class="flex items-center gap-2">
                                                <span class="tabular font-mono text-[11px] font-semibold text-ink-faint">{{ trim($category->kode.'.'.$aspect->kode, '.') }}</span>
                                                <span class="text-sm text-ink">{{ $aspect->nama }}</span>
                                            </div>
                                            @if ($editable)
                                                <div class="flex items-center gap-2">
                                                    <x-ui.button variant="ghost" size="sm" @click="editItem = !editItem" x-text="editItem ? 'Tutup' : 'Ubah'"></x-ui.button>
                                                    <form method="POST" action="{{ route('ujianppi.konfigurasi.aspek.item.destroy', [$periode, $category, $aspect]) }}"
                                                        onsubmit="return confirm('Hapus item {{ $aspect->nama }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.button type="submit" variant="ghost" size="sm" icon="trash"></x-ui.button>
                                                    </form>
                                                </div>
                                            @endif
                                            @if ($editable)
                                                <div x-show="editItem" x-cloak class="w-full">
                                                    <form method="POST" action="{{ route('ujianppi.konfigurasi.aspek.item.update', [$periode, $category, $aspect]) }}" class="flex flex-wrap items-end gap-2 py-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <div>
                                                            <label class="block pb-1 text-xs font-bold text-ink">Kode</label>
                                                            <x-ui.input name="kode" :value="$aspect->kode" class="!w-24" />
                                                        </div>
                                                        <div class="min-w-[220px] flex-1">
                                                            <label class="block pb-1 text-xs font-bold text-ink">Nama</label>
                                                            <x-ui.input name="nama" :value="$aspect->nama" />
                                                        </div>
                                                        <x-ui.button type="submit" variant="primary" size="sm" icon="check">Simpan</x-ui.button>
                                                    </form>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>

                                @if ($editable)
                                    <div class="border-t border-rule/60 px-4 py-3">
                                        <form method="POST" action="{{ route('ujianppi.konfigurasi.aspek.item.store', [$periode, $category]) }}" class="flex flex-wrap items-end gap-2">
                                            @csrf
                                            <div>
                                                <label class="block pb-1 text-xs font-bold text-ink">Kode Item</label>
                                                <x-ui.input name="kode" class="!w-24" :value="$category->aspects->max('urutan') + 1 ?? 1" />
                                            </div>
                                            <div class="min-w-[220px] flex-1">
                                                <label class="block pb-1 text-xs font-bold text-ink">Nama Item Baru</label>
                                                <x-ui.input name="nama" placeholder="mis. Niat Wudhu" />
                                            </div>
                                            <x-ui.button type="submit" variant="secondary" size="sm" icon="plus">Tambah Item</x-ui.button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-ui.sheet>
            @endforeach
        </div>
    </div>
</x-layouts.page>