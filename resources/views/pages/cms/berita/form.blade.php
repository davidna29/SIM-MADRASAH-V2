<x-layouts.page
    :title="$editing ? 'Ubah Berita' : 'Tulis Berita'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.berita.edit">

    <div class="mx-auto max-w-4xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Berita' : 'Tulis Berita' }}</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Berita tersimpan sebagai <strong>Draft</strong> — ajukan untuk masuk alur review editor.
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
            action="{{ $editing ? route('cms.berita.update', $article) : route('cms.berita.store') }}"
            enctype="multipart/form-data"
            class="mt-6 space-y-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Konten Berita">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-ui.field label="Judul" required :error="$errors->first('title')">
                            <x-ui.input name="title" :value="old('title', $editing ? $article->title : '')" placeholder="Judul berita" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Slug" hint="Kosongkan untuk otomatis dari judul." :error="$errors->first('slug')">
                        <x-ui.input name="slug" :value="old('slug', $editing ? $article->slug : '')" placeholder="otomatis" />
                    </x-ui.field>
                    <x-ui.field label="Kategori" :error="$errors->first('category')">
                        <x-ui.input name="category" :value="old('category', $editing ? $article->category : '')" placeholder="Mis. Prestasi, Kegiatan, PPDB" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Ringkasan" :error="$errors->first('summary')">
                            <textarea name="summary" rows="2" maxlength="300" placeholder="Ringkasan singkat untuk tampilan list…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('summary', $editing ? $article->summary : '') }}</textarea>
                        </x-ui.field>
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Isi Berita" required :error="$errors->first('body')">
                            <textarea name="body" rows="12" required placeholder="Tulis isi berita di sini…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('body', $editing ? $article->body : '') }}</textarea>
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Tag" hint="Pisahkan dengan koma." :error="$errors->first('tags')">
                        <x-ui.input name="tags" :value="old('tags', $editing ? $article->tags : '')" placeholder="ppdb, prestasi" />
                    </x-ui.field>
                    <x-ui.field label="Gambar Utama" hint="Opsional — jpg/png/webp maks. 2 MB." :error="$errors->first('featured_image')">
                        <input type="file" name="featured_image"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink file:mr-3 file:rounded file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-strong ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                        @if ($editing && $article->featured_image)
                            <p class="mt-2 text-xs text-ink-faint">Gambar saat ini: {{ basename($article->featured_image) }} — unggah gambar baru untuk menggantinya.</p>
                        @endif
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('cms.berita.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">
                    {{ $editing ? 'Sematkan & Simpan Perubahan' : 'Sematkan & Simpan Draft' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
