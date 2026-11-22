<x-layouts.publik :title="'Galeri Madrasah'" :description="'Dokumentasi foto kegiatan madrasah yang telah dipilih untuk publikasi.'">
    <div class="mx-auto max-w-6xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Galeri']]" />
        <header class="mt-8 flex flex-wrap items-end justify-between gap-4 border-b border-rule-strong/70 pb-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Galeri Madrasah</h1>
                <p class="mt-2 max-w-prose text-sm leading-relaxed text-ink-soft">Dokumentasi foto dan video kegiatan yang telah dipilih untuk dibagikan.</p>
            </div>
            <span class="font-mono text-xs text-ink-faint">{{ $albums->total() }} album</span>
        </header>

        @if ($albums->isNotEmpty())
            <div class="mt-7 grid grid-cols-1 gap-x-6 gap-y-9 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albums as $album)
                    <a href="{{ route('publik.galeri.show', $album) }}" class="group block">
                        @php
                            $coverImage = $album->items->firstWhere('file_path', $album->cover_image)?->file_path ?: $album->items->first()?->file_path;
                        @endphp
                        <div class="relative aspect-[4/3] overflow-hidden rounded-sheet bg-paper-deep shadow-sheet ring-1 ring-inset ring-rule/60">
                            @if ($coverImage)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($coverImage) }}" alt="{{ $album->title }}" class="size-full object-cover transition duration-500 group-hover:scale-[1.03]" loading="lazy" decoding="async">
                            @else
                                <div class="board-face flex size-full items-center justify-center text-board-ink/80"><x-svg-photo class="size-10" aria-hidden="true" /></div>
                            @endif
                            <span class="absolute bottom-3 right-3 rounded-full bg-board px-3 py-1.5 text-xs font-bold text-board-ink">{{ $album->items_count }} item</span>
                        </div>
                        <div class="mt-4 flex items-start justify-between gap-3">
                            <div>
                                @if ($album->kategori)<p class="text-xs font-bold text-primary-strong">{{ $album->kategori }}</p>@endif
                                <h2 class="mt-1 text-lg font-extrabold leading-snug text-ink group-hover:text-primary-strong">{{ $album->title }}</h2>
                                @if ($album->description)<p class="mt-2 line-clamp-2 text-sm leading-relaxed text-ink-soft">{{ $album->description }}</p>@endif
                                <time class="mt-3 block font-mono text-xs text-ink-faint" datetime="{{ $album->created_at->toDateString() }}">{{ $album->created_at->isoFormat('D MMM YYYY') }}</time>
                            </div>
                            <x-svg-arrow-up-right class="mt-1 size-5 shrink-0 text-ink-faint transition group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:text-primary" aria-hidden="true" />
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-9"><x-ui.pagination :current="$albums->currentPage()" :last="$albums->lastPage()" /></div>
        @else
            <div class="mt-8 rounded-sheet bg-sheet p-8 shadow-sheet ring-1 ring-inset ring-rule/60 sm:p-12">
                <x-svg-photo class="size-8 text-primary" aria-hidden="true" />
                <h2 class="mt-4 text-lg font-extrabold text-ink">Belum ada dokumentasi publik</h2>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Dokumentasi akan ditampilkan setelah pengelola memilih foto yang layak dan diizinkan untuk dibagikan.</p>
            </div>
        @endif
    </div>
</x-layouts.publik>
