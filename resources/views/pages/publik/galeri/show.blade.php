<x-layouts.publik :title="$album->title" :description="$album->description">
    <article class="mx-auto max-w-6xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Galeri', 'href' => route('publik.galeri.index')], ['label' => $album->title]]" />
        <header class="mt-8 border-b border-rule-strong/70 pb-6">
            @if ($album->kategori)<x-ui.badge variant="primary" :dot="false">{{ $album->kategori }}</x-ui.badge>@endif
            <h1 class="mt-3 text-3xl font-extrabold leading-tight tracking-tight text-ink sm:text-4xl">{{ $album->title }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-ink-faint">
                <span>{{ $items->count() }} item</span><span aria-hidden="true">·</span>
                <time class="font-mono" datetime="{{ $album->created_at->toDateString() }}">{{ $album->created_at->isoFormat('D MMMM YYYY') }}</time>
            </div>
            @if ($album->description)<p class="mt-4 max-w-3xl text-base leading-relaxed text-ink-soft">{{ $album->description }}</p>@endif
        </header>

        @if ($photos->isNotEmpty())
            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($photos as $item)
                    <figure class="overflow-hidden rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->file_path) }}" alt="{{ $item->caption ?: $album->title }}" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                        @if ($item->caption)<figcaption class="px-4 py-3 text-sm leading-relaxed text-ink-soft">{{ $item->caption }}</figcaption>@endif
                    </figure>
                @endforeach
            </div>
        @endif

        @if ($videos->isNotEmpty())
            <section class="mt-12" aria-labelledby="video-album-heading">
                <h2 id="video-album-heading" class="text-xl font-extrabold tracking-tight text-ink">Video dalam album ini</h2>
                <div class="mt-5 grid grid-cols-1 gap-8 md:grid-cols-2">
                    @foreach ($videos as $video)
                        <div>
                            @if ($video->youtubeVideoId())
                                <div class="aspect-video overflow-hidden rounded-sheet bg-board shadow-sheet ring-1 ring-inset ring-rule/60">
                                    <iframe src="https://www.youtube-nocookie.com/embed/{{ $video->youtubeVideoId() }}" title="{{ $video->caption ?: $album->title }}" class="size-full" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                </div>
                            @else
                                <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer" class="flex aspect-video items-center justify-center gap-3 rounded-sheet bg-board p-6 text-board-ink shadow-sheet ring-1 ring-inset ring-rule/60 hover:bg-board-soft">
                                    <x-svg-play-circle class="size-8" aria-hidden="true" /><span class="font-bold">Tonton video dari sumber resmi</span>
                                </a>
                            @endif
                            @if ($video->caption)<p class="mt-3 text-sm font-semibold leading-relaxed text-ink-soft">{{ $video->caption }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($photos->isEmpty() && $videos->isEmpty())
            <div class="mt-8 rounded-sheet bg-sheet p-7 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="font-bold text-ink">Album ini sedang dilengkapi.</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Dokumentasi akan tampil setelah media yang disetujui ditambahkan.</p>
            </div>
        @endif
    </article>
</x-layouts.publik>
