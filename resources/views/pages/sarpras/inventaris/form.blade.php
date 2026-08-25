<x-layouts.page
    :title="$editing ? 'Ubah Barang' : 'Tambah Barang'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="inventaris.edit">

    <div class="mx-auto max-w-3xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Barang Inventaris' : 'Tambah Barang Inventaris' }}</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Kode barang dibuat otomatis. Mutasi & pemeliharaan dikelola di halaman detail.
            </p>
        </div>

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <form method="POST"
            action="{{ $editing ? route('inventaris.update', $item) : route('inventaris.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Identitas Barang">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Nama Barang" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $editing ? $item->name : '')" placeholder="Mis. Proyektor LCD" />
                    </x-ui.field>
                    <x-ui.field label="Kategori" required :error="$errors->first('category_id')">
                        <x-ui.select name="category_id" :options="$categories" :selected="old('category_id', $editing ? $item->category_id : null)" placeholder="Pilih kategori…" />
                    </x-ui.field>
                    <x-ui.field label="Merek" :error="$errors->first('brand')">
                        <x-ui.input name="brand" :value="old('brand', $editing ? $item->brand : '')" placeholder="Mis. Epson" />
                    </x-ui.field>
                    <x-ui.field label="Model / Tipe" :error="$errors->first('model')">
                        <x-ui.input name="model" :value="old('model', $editing ? $item->model : '')" placeholder="Mis. EB-X05" />
                    </x-ui.field>
                    <x-ui.field label="No. Seri" :error="$errors->first('serial_number')">
                        <x-ui.input name="serial_number" :value="old('serial_number', $editing ? $item->serial_number : '')" placeholder="Opsional" />
                    </x-ui.field>
                    <x-ui.field label="Lokasi / Ruangan" :error="$errors->first('location')">
                        <x-ui.input name="location" :value="old('location', $editing ? $item->location : '')" placeholder="Mis. Ruang Kelas I-A" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Jumlah & Kondisi">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <x-ui.field label="Jumlah" required :error="$errors->first('quantity')">
                        <x-ui.input type="number" name="quantity" min="1" :value="old('quantity', $editing ? $item->quantity : 1)" />
                    </x-ui.field>
                    <x-ui.field label="Satuan" :error="$errors->first('unit')">
                        <x-ui.select name="unit" :options="['unit' => 'unit', 'set' => 'set', 'buah' => 'buah', 'pcs' => 'pcs', 'rim' => 'rim']" :selected="old('unit', $editing ? $item->unit : 'unit')" />
                    </x-ui.field>
                    <x-ui.field label="Kondisi" required :error="$errors->first('condition')">
                        <x-ui.select name="condition" :options="collect($conditions)->mapWithKeys(fn ($c) => [$c => ucwords(str_replace('_', ' ', $c))])" :selected="old('condition', $editing ? $item->condition : 'baik')" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Detail Pengadaan">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Tanggal Beli" :error="$errors->first('purchase_date')">
                        <x-ui.input type="date" name="purchase_date" :value="old('purchase_date', $editing && $item->purchase_date ? $item->purchase_date->format('Y-m-d') : '')" />
                    </x-ui.field>
                    <x-ui.field label="Harga Beli (Rp)" :error="$errors->first('purchase_price')">
                        <x-ui.input type="number" name="purchase_price" min="0" step="1" :value="old('purchase_price', $editing ? $item->purchase_price : '')" placeholder="0" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Status" required :error="$errors->first('status')">
                            <x-ui.select name="status" :options="collect($statuses)->mapWithKeys(fn ($s) => [$s => ucwords(str_replace('_', ' ', $s))])" :selected="old('status', $editing ? $item->status : 'tersedia')" />
                        </x-ui.field>
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Catatan" :error="$errors->first('notes')">
                            <textarea name="notes" rows="3" placeholder="Keterangan tambahan…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('notes', $editing ? $item->notes : '') }}</textarea>
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('inventaris.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Sematkan & Simpan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
