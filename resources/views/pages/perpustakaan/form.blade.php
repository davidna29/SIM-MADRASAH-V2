<x-layouts.page
    :title="$editing ? 'Ubah Buku' : 'Tambah Buku'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="perpustakaan.edit">

    <div class="mx-auto max-w-3xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Data Buku' : 'Tambah Buku Baru' }}</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Kode buku dibuat otomatis. Pengaturan peminjaman dilakukan di halaman detail.
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
            action="{{ $editing ? route('perpustakaan.update', $book) : route('perpustakaan.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Identitas Buku">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Judul Buku" required :error="$errors->first('title')">
                        <x-ui.input name="title" :value="old('title', $editing ? $book->title : '')" placeholder="Mis. Matematika Kelas VI" />
                    </x-ui.field>
                    <x-ui.field label="Pengarang" required :error="$errors->first('author')">
                        <x-ui.input name="author" :value="old('author', $editing ? $book->author : '')" placeholder="Nama pengarang" />
                    </x-ui.field>
                    <x-ui.field label="Penerbit" :error="$errors->first('publisher')">
                        <x-ui.input name="publisher" :value="old('publisher', $editing ? $book->publisher : '')" placeholder="Nama penerbit" />
                    </x-ui.field>
                    <x-ui.field label="Tahun Terbit" :error="$errors->first('year')">
                        <x-ui.input type="number" name="year" min="1900" :value="old('year', $editing ? $book->year : '')" placeholder="2024" />
                    </x-ui.field>
                    <x-ui.field label="ISBN" :error="$errors->first('isbn')">
                        <x-ui.input name="isbn" :value="old('isbn', $editing ? $book->isbn : '')" placeholder="978-602-XXX-XXX-X" />
                    </x-ui.field>
                    <x-ui.field label="Kategori" :error="$errors->first('category_id')">
                        <x-ui.select name="category_id" :options="$categories" :selected="old('category_id', $editing ? $book->category_id : null)" placeholder="Pilih kategori…" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Stok & Lokasi">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <x-ui.field label="Jumlah Stok" required :error="$errors->first('total_qty')">
                        <x-ui.input type="number" name="total_qty" min="1" :value="old('total_qty', $editing ? $book->total_qty : 1)" />
                    </x-ui.field>
                    <x-ui.field label="Lokasi / Rak" :error="$errors->first('location')">
                        <x-ui.input name="location" :value="old('location', $editing ? $book->location : '')" placeholder="Mis. Rak A-3" />
                    </x-ui.field>
                    <x-ui.field label="Status" required :error="$errors->first('status')">
                        <x-ui.select name="status" :options="collect(\App\Models\LibraryBook::STATUSES)->mapWithKeys(fn ($s) => [$s => ucwords(str_replace('_', ' ', $s))])" :selected="old('status', $editing ? $book->status : 'tersedia')" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Ebook (opsional)">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Ebook?" :error="$errors->first('is_ebook')">
                        <label class="flex items-center gap-2 text-sm text-ink">
                            <input type="checkbox" name="is_ebook" value="1" {{ old('is_ebook', $editing && $book->is_ebook ? '1' : '') ? 'checked' : '' }}
                                class="size-4 rounded border-rule text-primary focus:ring-primary">
                            Buku tersedia sebagai ebook (URL eksternal)
                        </label>
                    </x-ui.field>
                    <x-ui.field label="URL Ebook" :error="$errors->first('ebook_url')">
                        <x-ui.input name="ebook_url" type="url" :value="old('ebook_url', $editing ? $book->ebook_url : '')" placeholder="https://drive.google.com/..." />
                    </x-ui.field>
                </div>
                <div class="mt-5">
                    <x-ui.field label="Deskripsi Singkat" :error="$errors->first('description')">
                        <textarea name="description" rows="3" placeholder="Ringkasan isi buku…"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description', $editing ? $book->description : '') }}</textarea>
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('perpustakaan.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Simpan Buku</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
