<x-layouts.publik :title="'Video Madrasah'" :description="'Video kegiatan dan informasi dari kanal resmi madrasah.'">
    <div class="mx-auto max-w-6xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Berita & Kegiatan', 'href' => route('publik.berita.index')], ['label' => 'Video']]" />
        <header class="mt-8 max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Video Madrasah</h1>
            <p class="mt-3 text-base leading-relaxed text-ink-soft">Tayangan video dari album yang ditandai publik oleh pengelola madrasah.</p>
        </header>

        @if ($videos->isNotEmpty())
            <div class="mt-9 grid grid-cols-1 gap-x-8 gap-y-10 md:grid-cols-2">
                @foreach ($videos as $video)
                    <article class="min-w-0">
                        @if ($video->youtubeVideoId())
                            <div class="aspect-video overflow-hidden rounded-sheet bg-board shadow-sheet ring-1 ring-inset ring-rule/60">
                                <iframe src="https://www.youtube-nocookie.com/embed/{{ $video->youtubeVideoId() }}" title="{{ $video->caption ?: $video->album->title }}" class="size-full" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                        @else
                            <a href="{{ $video->video_url }}" target="_blank" rel="noopener noreferrer" class="flex aspect-video items-center justify-center gap-3 rounded-sheet bg-board p-6 text-board-ink shadow-sheet ring-1 ring-inset ring-rule/60 hover:bg-board-soft">
                                <x-svg-play-circle class="size-9" aria-hidden="true" />
                                <span class="font-bold">Tonton video dari sumber resmi</span>
                            </a>
                        @endif
                        <p class="mt-3 text-xs font-semibold text-ink-faint"><a href="{{ route('publik.galeri.show', $video->album) }}" class="hover:text-primary-strong">{{ $video->album->title }}</a></p>
                        @if ($video->caption)<h2 class="mt-1 text-lg font-extrabold leading-snug text-ink">{{ $video->caption }}</h2>@endif
                    </article>
                @endforeach
            </div>
            <div class="mt-8"><x-ui.pagination :current="$videos->currentPage()" :last="$videos->lastPage()" /></div>
        @else
            <div class="mt-8 rounded-sheet bg-sheet p-7 shadow-sheet ring-1 ring-inset ring-rule/60 sm:p-10">
                <p class="font-bold text-ink">Belum ada video publik.</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Video akan ditampilkan setelah pengelola menambahkan tautan ke album publik.</p>
                <a href="{{ route('publik.galeri.index') }}" class="mt-5 inline-flex min-h-11 items-center gap-2 text-sm font-bold text-primary-strong hover:underline">Lihat galeri foto <x-svg-arrow-right class="size-4" aria-hidden="true" /></a>
            </div>
        @endif
    </div>
</x-layouts.publik>
