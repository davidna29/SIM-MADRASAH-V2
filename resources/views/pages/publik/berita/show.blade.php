<x-layouts.publik
    :title="$article->title"
    :description="$article->summary ?: $article->excerpt()"
    :image="$article->featured_image ? url(\Illuminate\Support\Facades\Storage::disk('public')->url($article->featured_image)) : null">
    <article class="mx-auto max-w-4xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Berita', 'href' => route('publik.berita.index')], ['label' => $article->title]]" />

        <header class="mt-8 border-b border-rule-strong/70 pb-6">
            @if ($article->category)<x-ui.badge variant="primary" :dot="false">{{ $article->category }}</x-ui.badge>@endif
            <h1 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight text-ink sm:text-4xl lg:text-5xl">{{ $article->title }}</h1>
            <div class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-2 text-xs text-ink-faint">
                <span class="font-semibold text-ink-soft">{{ $article->author?->name ?: 'Redaksi Madrasah' }}</span>
                <span aria-hidden="true">·</span>
                <time class="font-mono" datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->isoFormat('D MMMM YYYY') }}</time>
            </div>
        </header>

        @if ($article->featured_image)
            <figure class="mt-7 overflow-hidden rounded-sheet bg-paper-deep shadow-sheet ring-1 ring-inset ring-rule/60">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->featured_image) }}" alt="{{ $article->title }}" class="max-h-[38rem] w-full object-cover" fetchpriority="high">
            </figure>
        @endif

        <div class="mx-auto mt-8 max-w-[70ch]">
            @if ($article->summary)
                <p class="border-b border-rule/70 pb-6 text-lg font-semibold leading-relaxed text-ink-soft">{{ $article->summary }}</p>
            @endif
            <div class="whitespace-pre-line py-6 text-[15px] leading-8 text-ink">{{ $article->body }}</div>
            @if ($article->tags)
                <div class="flex flex-wrap gap-2 border-t border-rule/70 pt-5">
                    @foreach (explode(',', $article->tags) as $tag)
                        <x-ui.badge variant="neutral" :dot="false">#{{ trim($tag) }}</x-ui.badge>
                    @endforeach
                </div>
            @endif
        </div>
    </article>

    @if ($lainnya->isNotEmpty())
        <section class="mt-16 border-t border-rule-strong/70 pt-8" aria-labelledby="berita-lainnya-heading">
            <div class="flex items-end justify-between gap-4">
                <h2 id="berita-lainnya-heading" class="text-xl font-extrabold tracking-tight text-ink">Berita lainnya</h2>
                <a href="{{ route('publik.berita.index') }}" class="text-sm font-bold text-primary-strong hover:underline">Semua berita</a>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-x-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($lainnya as $item)
                    <a href="{{ route('publik.berita.show', $item) }}" class="group border-b border-rule/70 py-5">
                        @if ($item->category)<p class="text-xs font-bold text-primary-strong">{{ $item->category }}</p>@endif
                        <h3 class="mt-1 text-base font-bold leading-snug text-ink group-hover:text-primary-strong">{{ $item->title }}</h3>
                        <time class="mt-2 block font-mono text-xs text-ink-faint" datetime="{{ $item->published_at?->toDateString() }}">{{ $item->published_at?->isoFormat('D MMM YYYY') }}</time>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.publik>
