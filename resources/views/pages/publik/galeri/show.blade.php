<x-layouts.publik :title="$album->title">
    <div class="mx-auto max-w-5xl">
        <p class="text-xs font-semibold text-ink-faint">
            <a href="{{ route('publik.galeri.index') }}" class="text-primary hover:underline">← Semua galeri</a>
        </p>

        <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $album->title }}</h1>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            @if ($album->kategori)
                <x-ui.badge variant="primary" :dot="false">{{ $album->kategori }}</x-ui.badge>
            @endif
            <span class="tabular font-mono text-xs text-ink-faint">{{ $items->count() }} item · {{ $album->created_at->isoFormat('D MMMM YYYY') }}</span>
        </div>
        @if ($album->description)
            <p class="mt-4 text-[15px] leading-relaxed text-ink-soft">{{ $album->description }}</p>
        @endif

        @if ($photos->isNotEmpty())
            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($photos as $item)
                    <figure class="overflow-hidden rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->file_path) }}"
                            alt="{{ $item->caption ?: $album->title }}" class="aspect-video w-full object-cover">
                        @if ($item->caption)
                            <figcaption class="px-4 py-3 text-xs leading-relaxed text-ink-soft">{{ $item->caption }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @endif

        @if ($videos->isNotEmpty())
            <div class="mt-10 space-y-6">
                <h2 class="text-lg font-bold tracking-tight text-ink">Video</h2>
                @foreach ($videos as $video)
                    @php
                        // Embed YouTube bila tautannya dikenali, selain itu tampilkan sebagai tautan
                        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{11})/', $video->video_url, $m);
                    @endphp
                    <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                        @if (! empty($m[1]))
                            <div class="aspect-video w-full overflow-hidden rounded-[var(--radius-control)]">
                                <iframe src="https://www.youtube.com/embed/{{ $m[1] }}" title="{{ $video->caption ?: 'Video' }}"
                                    class="h-full w-full" allowfullscreen loading="lazy"></iframe>
                            </div>
                        @else
                            <a href="{{ $video->video_url }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1.5 font-semibold text-primary hover:underline">
                                <x-svg-video-camera class="size-4" aria-hidden="true" />
                                Tonton video
                            </a>
                        @endif
                        @if ($video->caption)
                            <p class="mt-3 text-[13px] leading-relaxed text-ink-soft">{{ $video->caption }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.publik>
