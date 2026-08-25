<x-layouts.publik :title="'Galeri'">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Galeri Madrasah</h1>
        <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
            Dokumentasi foto dan video kegiatan MTs Al-Ikhlas Mulia.
        </p>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($albums as $album)
            <a href="{{ route('publik.galeri.show', $album) }}"
                class="group flex flex-col overflow-hidden rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 transition duration-150 hover:-translate-y-0.5 hover:shadow-sheet-raised">
                <div class="aspect-video w-full overflow-hidden bg-paper-deep">
                    @if ($album->cover_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($album->cover_image) }}"
                            alt="" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    @else
                        <span class="flex h-full items-center justify-center text-ink-faint">
                            <x-svg-photo class="size-8" aria-hidden="true" />
                        </span>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-5">
                    @if ($album->kategori)
                        <x-ui.badge variant="primary" :dot="false" class="self-start">{{ $album->kategori }}</x-ui.badge>
                    @endif
                    <h2 class="mt-3 text-base font-bold leading-snug text-ink group-hover:text-primary-strong">{{ $album->title }}</h2>
                    <p class="mt-2 line-clamp-2 flex-1 text-[13px] leading-relaxed text-ink-soft">{{ $album->description }}</p>
                    <p class="tabular mt-4 font-mono text-xs text-ink-faint">{{ $album->items_count }} item</p>
                </div>
            </a>
        @empty
            <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center md:col-span-2 lg:col-span-3">
                <p class="text-sm font-semibold text-ink">Belum ada galeri.</p>
                <p class="mt-1 text-xs text-ink-faint">Kembali lagi nanti.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        <x-ui.pagination :current="$albums->currentPage()" :last="$albums->lastPage()" />
    </div>
</x-layouts.publik>
