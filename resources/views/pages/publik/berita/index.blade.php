<x-layouts.publik :title="'Berita Madrasah'" :description="'Berita dan informasi terbaru yang diterbitkan madrasah.'">
    <div class="mx-auto max-w-6xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Berita']]" />
        <header class="mt-8 flex flex-wrap items-end justify-between gap-5 border-b border-rule-strong/70 pb-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Berita Madrasah</h1>
                <p class="mt-2 max-w-prose text-sm leading-relaxed text-ink-soft">Informasi dan kegiatan terbaru dari madrasah.</p>
            </div>
            <span class="font-mono text-xs text-ink-faint">{{ $articles->total() }} berita</span>
        </header>

        @if ($articles->isNotEmpty())
            <div class="mt-7 grid grid-cols-1 gap-x-8 lg:grid-cols-12">
                @foreach ($articles as $article)
                    @if ($loop->first)
                        <article class="group border-b border-rule-strong/70 pb-7 lg:col-span-7 lg:row-span-2 lg:border-b-0 lg:pb-0 lg:pr-3">
                            <a href="{{ route('publik.berita.show', $article) }}" class="block overflow-hidden rounded-sheet bg-paper-deep shadow-sheet ring-1 ring-inset ring-rule/60">
                                @if ($article->featured_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->featured_image) }}" alt="{{ $article->title }}" class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-[1.02]" fetchpriority="high">
                                @else
                                    <div class="board-face flex aspect-[16/10] items-end p-6 sm:p-8"><span class="max-w-sm text-3xl font-extrabold leading-tight tracking-tight text-white">Kabar dan informasi madrasah</span></div>
                                @endif
                            </a>
                            <div class="mt-5">
                                @if ($article->category)<x-ui.badge variant="primary" :dot="false">{{ $article->category }}</x-ui.badge>@endif
                                <h2 class="mt-3 text-2xl font-extrabold leading-tight tracking-tight text-ink sm:text-3xl"><a href="{{ route('publik.berita.show', $article) }}" class="hover:text-primary-strong">{{ $article->title }}</a></h2>
                                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-ink-soft">{{ $article->excerpt(220) }}</p>
                                <div class="mt-5 flex flex-wrap items-center gap-2 font-mono text-xs text-ink-faint">
                                    <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->isoFormat('D MMMM YYYY') }}</time>
                                    <span aria-hidden="true">·</span><span>{{ $article->author?->name ?: 'Redaksi Madrasah' }}</span>
                                </div>
                            </div>
                        </article>
                    @else
                        <article class="group grid grid-cols-[6rem_1fr] gap-4 border-b border-rule/70 py-5 first:pt-0 sm:grid-cols-[8rem_1fr] lg:col-span-5 lg:py-6 lg:first:pt-0">
                            @if ($article->featured_image)
                                <a href="{{ route('publik.berita.show', $article) }}" class="block aspect-square overflow-hidden rounded-control bg-paper-deep">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->featured_image) }}" alt="" class="size-full object-cover transition duration-500 group-hover:scale-[1.03]" loading="lazy" decoding="async">
                                </a>
                            @else
                                <a href="{{ route('publik.berita.show', $article) }}" class="board-face flex aspect-square items-center justify-center rounded-control text-board-ink"><x-svg-newspaper class="size-7" aria-hidden="true" /></a>
                            @endif
                            <div class="min-w-0 py-1">
                                @if ($article->category)<p class="text-xs font-bold text-primary-strong">{{ $article->category }}</p>@endif
                                <h2 class="mt-1 text-base font-extrabold leading-snug text-ink group-hover:text-primary-strong"><a href="{{ route('publik.berita.show', $article) }}">{{ $article->title }}</a></h2>
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-ink-soft">{{ $article->excerpt(150) }}</p>
                                <time class="mt-3 block font-mono text-xs text-ink-faint" datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->isoFormat('D MMM YYYY') }}</time>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
            <div class="mt-8"><x-ui.pagination :current="$articles->currentPage()" :last="$articles->lastPage()" /></div>
        @else
            <div class="mt-8 rounded-sheet bg-sheet p-8 shadow-sheet ring-1 ring-inset ring-rule/60 sm:p-12">
                <x-svg-newspaper class="size-8 text-primary" aria-hidden="true" />
                <h2 class="mt-4 text-lg font-extrabold text-ink">Belum ada berita yang diterbitkan</h2>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Silakan kembali lagi untuk melihat informasi terbaru dari madrasah.</p>
                <x-ui.button class="mt-5" variant="secondary" :href="route('publik.beranda')">Kembali ke Beranda</x-ui.button>
            </div>
        @endif
    </div>
</x-layouts.publik>
