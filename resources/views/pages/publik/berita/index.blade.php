<x-layouts.publik :title="'Berita'">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Berita Madrasah</h1>
        <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
            Informasi dan kegiatan terbaru dari MTs Al-Ikhlas Mulia.
        </p>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($articles as $article)
            <a href="{{ route('publik.berita.show', $article) }}"
                class="group flex flex-col overflow-hidden rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 transition duration-150 hover:-translate-y-0.5 hover:shadow-sheet-raised">
                @if ($article->featured_image)
                    <div class="aspect-video w-full overflow-hidden">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->featured_image) }}"
                            alt="{{ $article->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    </div>
                @endif
                <div class="flex flex-1 flex-col p-5">
                    @if ($article->category)
                        <x-ui.badge variant="primary" :dot="false" class="self-start">{{ $article->category }}</x-ui.badge>
                    @endif
                    <h2 class="mt-3 text-base font-bold leading-snug text-ink group-hover:text-primary-strong">{{ $article->title }}</h2>
                    <p class="mt-2 line-clamp-3 flex-1 text-[13px] leading-relaxed text-ink-soft">{{ $article->excerpt() }}</p>
                    <p class="tabular mt-4 font-mono text-xs text-ink-faint">{{ $article->published_at?->isoFormat('D MMM YYYY') }}</p>
                </div>
            </a>
        @empty
            <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center md:col-span-2 lg:col-span-3">
                <p class="text-sm font-semibold text-ink">Belum ada berita.</p>
                <p class="mt-1 text-xs text-ink-faint">Kembali lagi nanti.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        <x-ui.pagination :current="$articles->currentPage()" :last="$articles->lastPage()" />
    </div>
</x-layouts.publik>
