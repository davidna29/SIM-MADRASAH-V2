<x-layouts.publik :title="$article->title">
    <div class="mx-auto max-w-3xl">
        <p class="text-xs font-semibold text-ink-faint">
            <a href="{{ route('publik.berita.index') }}" class="text-primary hover:underline">← Semua berita</a>
        </p>

        <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $article->title }}</h1>

        <div class="mt-3 flex flex-wrap items-center gap-2">
            @if ($article->category)
                <x-ui.badge variant="primary" :dot="false">{{ $article->category }}</x-ui.badge>
            @endif
            <span class="tabular font-mono text-xs text-ink-faint">
                {{ $article->author?->name ?? 'Admin' }} · {{ $article->published_at?->isoFormat('D MMMM YYYY') }}
            </span>
        </div>

        <div class="mt-8">
            @if ($article->featured_image)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->featured_image) }}"
                    alt="{{ $article->title }}" class="mb-6 w-full rounded-sheet object-cover shadow-sheet">
            @endif

            @if ($article->summary)
                <p class="text-[15px] font-semibold leading-relaxed text-ink-soft">{{ $article->summary }}</p>
            @endif

            <div class="mt-4 whitespace-pre-line text-[15px] leading-relaxed text-ink">{{ $article->body }}</div>

            @if ($article->tags)
                <div class="mt-8 flex flex-wrap gap-2 border-t border-rule/70 pt-4">
                    @foreach (explode(',', $article->tags) as $tag)
                        <x-ui.badge variant="neutral" :dot="false">#{{ trim($tag) }}</x-ui.badge>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if ($lainnya->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-lg font-bold tracking-tight text-ink">Berita Lainnya</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($lainnya as $item)
                    <a href="{{ route('publik.berita.show', $item) }}"
                        class="group rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60 transition duration-150 hover:-translate-y-0.5 hover:shadow-sheet-raised">
                        <p class="text-sm font-bold leading-snug text-ink group-hover:text-primary-strong">{{ $item->title }}</p>
                        <p class="tabular mt-2 font-mono text-xs text-ink-faint">{{ $item->published_at?->isoFormat('D MMM YYYY') }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.publik>
