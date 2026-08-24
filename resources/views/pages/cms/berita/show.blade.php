<x-layouts.page
    :title="'Detail Berita'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.berita.show">

    @php
        $statusVariant = match ($article->status) {
            'publish', 'disetujui' => 'success',
            'dijadwalkan' => 'warning',
            'revisi' => 'danger',
            'diajukan', 'review' => 'info',
            default => 'neutral',
        };
    @endphp

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $article->title }}</h1>
                <p class="mt-1.5 text-xs text-ink-soft">
                    <span class="tabular font-mono">/{{ $article->slug }}</span>
                    · oleh {{ $article->author?->name ?? '—' }} · {{ $article->updated_at->isoFormat('D MMM YYYY, HH:mm') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.badge :variant="$statusVariant">{{ $statusLabels[$article->status] ?? $article->status }}</x-ui.badge>
                <x-ui.button variant="secondary" size="sm" icon="pencil-square" href="{{ route('cms.berita.edit', $article) }}">Ubah</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Aksi transisi -->
        @if ($actions->isNotEmpty())
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-ink-faint">Alur Persetujuan</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    @foreach ($actions as $action)
                        @if ($action['aksi'] === 'jadwalkan')
                            <form method="POST" action="{{ route('cms.berita.transition', [$article, 'jadwalkan']) }}" class="flex items-center gap-2">
                                @csrf
                                <input type="datetime-local" name="scheduled_at"
                                    class="rounded-[var(--radius-control)] bg-sheet px-2.5 py-1.5 text-xs text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">
                                <x-ui.button type="submit" size="sm" :variant="$action['variant']" icon="clock">{{ $action['label'] }}</x-ui.button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('cms.berita.transition', [$article, $action['aksi']]) }}">
                                @csrf
                                <x-ui.button type="submit" size="sm" :variant="$action['variant']" icon="check">{{ $action['label'] }}</x-ui.button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-6">
            <x-ui.sheet title="Isi Berita" pinned ruled>
                @if ($article->featured_image)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->featured_image) }}"
                        alt="{{ $article->title }}" class="mb-4 w-full rounded-[var(--radius-control)] object-cover">
                @endif
                @if ($article->summary)
                    <p class="text-sm font-semibold leading-relaxed text-ink-soft">{{ $article->summary }}</p>
                @endif
                <div class="mt-4 whitespace-pre-line text-[15px] leading-relaxed text-ink">{{ $article->body }}</div>

                <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-rule/70 pt-4">
                    @if ($article->category)
                        <x-ui.badge variant="neutral" :dot="false">{{ $article->category }}</x-ui.badge>
                    @endif
                    @if ($article->tags)
                        @foreach (explode(',', $article->tags) as $tag)
                            <x-ui.badge variant="primary" :dot="false">#{{ trim($tag) }}</x-ui.badge>
                        @endforeach
                    @endif
                    @if ($article->published_at)
                        <span class="ml-auto text-xs text-ink-faint">Terbit {{ $article->published_at->isoFormat('D MMM YYYY, HH:mm') }}</span>
                    @elseif ($article->scheduled_at)
                        <span class="tabular ml-auto font-mono text-xs text-ink-faint">Jadwal {{ $article->scheduled_at->isoFormat('D MMM YYYY, HH:mm') }}</span>
                    @endif
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
