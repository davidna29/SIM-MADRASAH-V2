<x-layouts.page
    :title="$editing ? 'Ubah Album' : 'Tambah Album'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.galeri.edit">

    <div class="mx-auto max-w-3xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Album' : 'Tambah Album' }}</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Setelah album dibuat, unggah foto atau tambahkan tautan video di halaman kelola album.
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
            action="{{ $editing ? route('cms.galeri.update', $album) : route('cms.galeri.store') }}"
            enctype="multipart/form-data"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Detail Album">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-ui.field label="Judul" required :error="$errors->first('title')">
                            <x-ui.input name="title" :value="old('title', $editing ? $album->title : '')" placeholder="Mis. Dokumentasi MPLS 2026" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Kategori" :error="$errors->first('kategori')">
                        <x-ui.input name="kategori" :value="old('kategori', $editing ? $album->kategori : '')" placeholder="Mis. Kegiatan, Prestasi" />
                    </x-ui.field>
                    <x-ui.field label="Status" required :error="$errors->first('status')">
                        <x-ui.select name="status" :options="['publik' => 'Publik (tampil di website)', 'privat' => 'Privat']" :selected="old('status', $editing ? $album->status : 'publik')" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Deskripsi" :error="$errors->first('description')">
                            <textarea name="description" rows="3" placeholder="Keterangan album…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description', $editing ? $album->description : '') }}</textarea>
                        </x-ui.field>
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Cover Album" hint="Opsional — otomatis dari foto pertama bila kosong." :error="$errors->first('cover_image')">
                            <input type="file" name="cover_image"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink file:mr-3 file:rounded file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-strong ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('cms.galeri.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Sematkan & Simpan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
