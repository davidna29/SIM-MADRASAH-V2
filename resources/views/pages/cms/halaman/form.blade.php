<x-layouts.page
    :title="$editing ? 'Ubah Halaman Publik' : 'Tambah Halaman Publik'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.halaman.index">
    <div class="mx-auto max-w-4xl">
        <header>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Halaman Publik' : 'Tambah Halaman Publik' }}</h1>
            <p class="mt-2 max-w-prose text-sm leading-relaxed text-ink-soft">Isi dengan informasi yang telah dikonfirmasi. Gunakan status Draf sampai konten siap ditayangkan.</p>
        </header>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible><strong>Periksa kembali:</strong> {{ $errors->first() }}</x-ui.alert></div>
        @endif

        <form method="POST" action="{{ $editing ? route('cms.halaman.update', $page) : route('cms.halaman.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf
            @if ($editing) @method('PUT') @endif

            <x-ui.form-section title="Konten Halaman" description="Teks ditampilkan apa adanya; gunakan baris baru untuk memisahkan paragraf.">
                <div class="space-y-5">
                    <x-ui.field label="Judul" required :error="$errors->first('title')">
                        <x-ui.input name="title" :value="old('title', $editing ? $page->title : '')" required maxlength="180" placeholder="Contoh: Profil Madrasah" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Slug" hint="Boleh dikosongkan untuk dibuat otomatis." :error="$errors->first('slug')">
                            <x-ui.input name="slug" :value="old('slug', $editing ? $page->slug : '')" maxlength="160" placeholder="profil-madrasah" />
                        </x-ui.field>
                        <x-ui.field label="Status tayang" required :error="$errors->first('status')">
                            <x-ui.select name="status" :options="['draft' => 'Draf — belum tampil', 'publik' => 'Publik — dapat dibaca pengunjung']" :selected="old('status', $editing ? $page->status : 'draft')" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Subjudul" :error="$errors->first('subtitle')">
                        <x-ui.input name="subtitle" :value="old('subtitle', $editing ? $page->subtitle : '')" maxlength="255" placeholder="Kalimat pengantar, jika diperlukan" />
                    </x-ui.field>
                    <x-ui.field label="Ringkasan" hint="Digunakan sebagai deskripsi singkat halaman." :error="$errors->first('summary')">
                        <textarea name="summary" rows="3" maxlength="500" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('summary', $editing ? $page->summary : '') }}</textarea>
                    </x-ui.field>
                    <x-ui.field label="Isi halaman" :error="$errors->first('body')">
                        <textarea name="body" rows="14" maxlength="50000" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm leading-relaxed text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Tulis informasi yang sudah diverifikasi…">{{ old('body', $editing ? $page->body : '') }}</textarea>
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Gambar & Pencarian" description="Unggah hanya gambar yang diizinkan untuk dipublikasikan. Jangan tampilkan siswa yang dapat dikenali sebelum izin publikasi wali dikonfirmasi.">
                <div class="space-y-5">
                    <x-ui.field label="Gambar utama" hint="JPG, PNG, atau WebP; maks. 4 MB." :error="$errors->first('featured_image')">
                        <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong file:mr-3 file:rounded-control file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-strong focus:outline-none focus:ring-2 focus:ring-primary">
                        @if ($editing && $page->featured_image)
                            <p class="mt-2 text-xs text-ink-faint">Gambar saat ini: {{ basename($page->featured_image) }}.</p>
                        @endif
                    </x-ui.field>
                    <x-ui.field label="Teks alternatif gambar" :error="$errors->first('image_alt')">
                        <x-ui.input name="image_alt" :value="old('image_alt', $editing ? $page->image_alt : '')" maxlength="255" placeholder="Jelaskan informasi yang tampak pada gambar" />
                    </x-ui.field>
                    <x-ui.field label="Deskripsi mesin pencari" :error="$errors->first('meta_description')">
                        <x-ui.input name="meta_description" :value="old('meta_description', $editing ? $page->meta_description : '')" maxlength="255" placeholder="Ringkasan singkat halaman" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" :href="route('cms.halaman.index')">Kembali</x-ui.button>
                <div class="flex flex-wrap gap-2">
                    @if ($editing)
                        <button type="submit" form="hapus-halaman" class="inline-flex min-h-10 items-center rounded-control px-3 text-sm font-semibold text-danger hover:bg-danger-soft focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-danger" onclick="return confirm('Hapus halaman ini?')">Hapus</button>
                    @endif
                    <x-ui.button type="submit" variant="primary" icon="check">{{ $editing ? 'Simpan Halaman' : 'Buat Halaman' }}</x-ui.button>
                </div>
            </div>
        </form>

        @if ($editing)
            <form id="hapus-halaman" method="POST" action="{{ route('cms.halaman.destroy', $page) }}" class="hidden">@csrf @method('DELETE')</form>
        @endif
    </div>
</x-layouts.page>
