<x-layouts.page
    :title="'Kelola Album'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.galeri.show">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $album->title }}</h1>
                <p class="mt-1.5 text-xs text-ink-soft">
                    <span class="tabular font-mono">/{{ $album->slug }}</span>
                    @if ($album->kategori) · {{ $album->kategori }} @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.badge :variant="$album->status === 'publik' ? 'success' : 'neutral'">{{ ucfirst($album->status) }}</x-ui.badge>
                <x-ui.button variant="secondary" size="sm" icon="pencil-square" href="{{ route('cms.galeri.edit', $album) }}">Ubah</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Unggah foto -->
        <form method="POST" action="{{ route('cms.galeri.foto', $album) }}" enctype="multipart/form-data" class="mt-6">
            @csrf
            <x-ui.form-section title="Unggah Foto" description="Bisa pilih beberapa file sekaligus — maks. 10 file × 4 MB (jpg/png/webp).">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-ui.field label="Foto" required :error="$errors->first('photos.*')">
                            <input type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp" required
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink file:mr-3 file:rounded file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-strong ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                        </x-ui.field>
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Keterangan" hint="Opsional — berlaku untuk semua foto pada unggahan ini." :error="$errors->first('caption')">
                            <x-ui.input name="caption" :value="old('caption')" placeholder="Mis. Siswa mengikuti sesi pengenalan" />
                        </x-ui.field>
                    </div>
                    <div class="sm:col-span-2 flex justify-end">
                        <x-ui.button type="submit" variant="primary" icon="arrow-up-tray">Unggah Foto</x-ui.button>
                    </div>
                </div>
            </x-ui.form-section>
        </form>

        <!-- Tambah video -->
        <form method="POST" action="{{ route('cms.galeri.video', $album) }}" class="mt-6">
            @csrf
            <x-ui.form-section title="Tambah Video (Tautan Eksternal)" description="Gunakan tautan YouTube/dll agar storage hosting tidak cepat penuh.">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                    <x-ui.field label="URL Video" required :error="$errors->first('video_url')">
                        <x-ui.input name="video_url" :value="old('video_url')" placeholder="https://www.youtube.com/watch?v=…" />
                    </x-ui.field>
                    <x-ui.field label="Keterangan" :error="$errors->first('caption')">
                        <x-ui.input name="caption" :value="old('caption')" placeholder="Opsional" />
                    </x-ui.field>
                    <x-ui.button type="submit" variant="secondary" icon="video-camera">Tambah Video</x-ui.button>
                </div>
            </x-ui.form-section>
        </form>

        <!-- Isi album -->
        <div class="mt-6">
            <x-ui.sheet title="Isi Album" :subtitle="$album->items()->count() . ' item'" pinned ruled>
                @if ($album->items()->count() === 0)
                    <p class="py-8 text-center text-sm text-ink-faint">Album masih kosong.</p>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($album->items()->get() as $item)
                            <div class="overflow-hidden rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                                @if ($item->tipe === 'foto')
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->file_path) }}"
                                        alt="" class="aspect-video w-full object-cover">
                                @else
                                    <div class="flex aspect-video w-full items-center justify-center bg-paper-deep text-ink-faint">
                                        <x-svg-video-camera class="size-8" aria-hidden="true" />
                                    </div>
                                @endif
                                <div class="space-y-2 p-4">
                                    <div class="flex items-center gap-2">
                                        @if ($item->tipe === 'foto')
                                            <x-ui.badge variant="info" :dot="false">Foto</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="warning" :dot="false">Video</x-ui.badge>
                                        @endif
                                        @if ($album->cover_image === $item->file_path)
                                            <x-ui.badge variant="success" icon="check">Cover</x-ui.badge>
                                        @endif
                                    </div>
                                    <p class="text-xs leading-relaxed text-ink-soft">{{ $item->caption ?: '—' }}</p>
                                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                        @if ($item->tipe === 'foto')
                                            <form method="POST" action="{{ route('cms.galeri.cover', [$album, $item]) }}">
                                                @csrf
                                                <x-ui.button type="submit" size="sm" variant="ghost" icon="star">Jadikan Cover</x-ui.button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('cms.galeri.item.destroy', [$album, $item]) }}" onsubmit="return confirm('Hapus item ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.sheet>
        </div>

        <div class="mt-6 flex items-center justify-between border-t border-rule-strong/60 pt-5">
            <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('cms.galeri.index') }}">Kembali ke Galeri</x-ui.button>
        </div>
    </div>
</x-layouts.page>
