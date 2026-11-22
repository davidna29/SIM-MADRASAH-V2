<x-layouts.publik :title="$page?->title ?: 'Sarana & Prasarana'" :description="$page?->meta_description ?: $page?->summary ?: 'Ruang belajar dan fasilitas madrasah.'">
    <div class="mx-auto max-w-6xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Profil', 'href' => route('publik.profil')], ['label' => 'Sarana & Prasarana']]" />
        <header class="mt-8 max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">{{ $page?->title ?: 'Sarana & Prasarana' }}</h1>
            @if ($page?->subtitle || $page?->summary)
                <p class="mt-3 text-base leading-relaxed text-ink-soft">{{ $page->subtitle ?: $page->summary }}</p>
            @else
                <p class="mt-3 text-base leading-relaxed text-ink-soft">Gambaran ruang belajar dan fasilitas yang tersedia untuk mendukung kegiatan madrasah.</p>
            @endif
        </header>

        @if ($page?->body)
            <div class="mt-6 max-w-[70ch] whitespace-pre-line text-sm leading-7 text-ink">{{ $page->body }}</div>
        @endif

        @if ($rooms->isNotEmpty())
            <ul class="mt-9 grid grid-cols-1 gap-0 sm:grid-cols-2 sm:gap-x-8 lg:grid-cols-3">
                @foreach ($rooms as $room)
                    <li class="flex gap-4 border-t border-rule-strong/70 py-5">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-control bg-primary-soft text-primary-strong">
                            <x-dynamic-component :component="$room->type === 'laboratorium' ? 'svg-beaker' : 'svg-building-library'" class="size-5" aria-hidden="true" />
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-base font-extrabold text-ink">{{ $room->name }}</h2>
                            <p class="mt-1 text-sm text-ink-soft">{{ $room->type === 'laboratorium' ? 'Laboratorium' : 'Ruang belajar' }}</p>
                            @if ($room->building || $room->floor)
                                <p class="mt-2 text-xs text-ink-faint">{{ collect([$room->building, $room->floor ? 'Lantai '.$room->floor : null])->filter()->implode(' · ') }}</p>
                            @endif
                            @if ($room->description)
                                <p class="mt-2 text-sm leading-relaxed text-ink-soft">{{ $room->description }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="mt-8 rounded-sheet bg-sheet p-7 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="font-bold text-ink">Informasi fasilitas sedang disiapkan.</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Pengelola akan menambahkan informasi fasilitas yang siap digunakan dan relevan untuk pengunjung.</p>
            </div>
        @endif
    </div>
</x-layouts.publik>
