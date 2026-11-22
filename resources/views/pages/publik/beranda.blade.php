<x-layouts.publik
    :title="$homePage?->title && $homePage->title !== 'Beranda' ? $homePage->title : ($siteSettings->get('madrasah_name') ?: 'Madrasah')"
    :description="$homePage?->meta_description ?: $homePage?->summary ?: $siteSettings->get('madrasah_tagline')"
    :image="$homePage?->featured_image ? url(\Illuminate\Support\Facades\Storage::disk('public')->url($homePage->featured_image)) : null">
    @php
        $schoolName = $siteSettings->get('madrasah_name') ?: 'Madrasah';
        $heroTitle = $homePage?->title && $homePage->title !== 'Beranda' ? $homePage->title : $schoolName;
        $heroText = $homePage?->subtitle ?: $siteSettings->get('madrasah_tagline') ?: 'Berita, agenda, dan informasi resmi madrasah untuk keluarga dan masyarakat.';
    @endphp

    <section class="relative overflow-hidden rounded-sheet bg-board text-board-ink shadow-sheet-raised">
        <div class="grid min-h-[420px] grid-cols-1 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="flex flex-col justify-center px-6 py-10 sm:px-10 sm:py-14 lg:px-14">
                <h1 class="max-w-3xl text-4xl font-extrabold leading-[1.06] tracking-tight text-white sm:text-5xl lg:text-6xl">{{ $heroTitle }}</h1>
                <p class="mt-5 max-w-2xl text-base leading-relaxed text-board-ink/85 sm:text-lg">{{ $heroText }}</p>
                @if ($homePage?->body)
                    <p class="mt-3 max-w-2xl whitespace-pre-line text-sm leading-relaxed text-board-ink/75">{{ \Illuminate\Support\Str::limit($homePage->body, 260) }}</p>
                @endif
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <x-ui.button variant="primary" size="lg" icon="academic-cap" :href="route('ppdb.form')">
                        {{ $ppdbOpen ? 'Daftar PPDB' : 'Informasi PPDB' }}
                    </x-ui.button>
                    <x-ui.button variant="secondary" size="lg" icon="newspaper" :href="route('publik.berita.index')">Baca berita</x-ui.button>
                </div>
                <div class="mt-7 flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold text-board-ink/80">
                    <span class="inline-flex items-center gap-2"><span class="pin-dot size-2 bg-success"></span>Berita dan agenda madrasah</span>
                    <a href="{{ route('publik.kontak') }}" class="underline decoration-board-ink/40 underline-offset-4 hover:text-white">Hubungi madrasah</a>
                </div>
            </div>

            <div class="relative min-h-64 overflow-hidden bg-board-deep/50 lg:min-h-full">
                @if ($homePage?->featured_image)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($homePage->featured_image) }}"
                        alt="{{ $homePage->image_alt ?: $schoolName }}" class="absolute inset-0 size-full object-cover" fetchpriority="high">
                    <div class="absolute inset-0 bg-gradient-to-t from-board-deep/55 via-transparent to-transparent" aria-hidden="true"></div>
                @elseif ($agenda->isNotEmpty())
                    <div class="paper-grain flex h-full min-h-64 items-center justify-center p-6 sm:p-10">
                        <article class="w-full max-w-md rotate-1 rounded-sheet bg-sheet p-6 text-ink shadow-sheet-raised ring-1 ring-inset ring-rule/60 sm:p-8">
                            <div class="flex items-center justify-between gap-4 border-b border-rule/70 pb-4">
                                <span class="text-sm font-extrabold tracking-tight">Agenda terdekat</span>
                                <x-ui.badge variant="info">{{ $agenda->first()->jenis === 'pengumuman' ? 'Pengumuman' : 'Agenda' }}</x-ui.badge>
                            </div>
                            <p class="mt-5 text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl">{{ $agenda->first()->title }}</p>
                            <p class="mt-3 text-sm font-semibold text-ink-soft">
                                {{ $agenda->first()->tanggal?->isoFormat('dddd, D MMMM YYYY') ?: 'Tanggal menyusul' }}
                                @if ($agenda->first()->waktu) · {{ $agenda->first()->waktu->format('H:i') }} WIB @endif
                            </p>
                            @if ($agenda->first()->lokasi)
                                <p class="mt-1 text-sm text-ink-faint">{{ $agenda->first()->lokasi }}</p>
                            @endif
                            <a href="{{ route('publik.agenda.index') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-primary-strong hover:underline">
                                Lihat semua agenda <x-svg-arrow-right class="size-4" aria-hidden="true" />
                            </a>
                        </article>
                    </div>
                @else
                    <div class="board-face flex h-full min-h-64 items-center justify-center p-8 text-center lg:min-h-full">
                        <div class="max-w-xs rounded-sheet bg-board-deep/45 px-6 py-8 ring-1 ring-inset ring-board-ink/15">
                            <x-svg-academic-cap class="mx-auto size-12 text-board-ink/85" aria-hidden="true" />
                            <p class="mt-4 text-lg font-extrabold text-white">{{ $schoolName }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-board-ink/75">Ruang informasi madrasah dan kegiatan belajar.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($featuredArticle || $articles->isNotEmpty())
        <section class="mt-16 sm:mt-20" aria-labelledby="berita-heading">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 id="berita-heading" class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kabar dari Madrasah</h2>
                    <p class="mt-2 max-w-prose text-sm leading-relaxed text-ink-soft">Informasi dan cerita terbaru yang telah dipublikasikan.</p>
                </div>
                <x-ui.button variant="outline" iconRight="arrow-right" :href="route('publik.berita.index')">Semua berita</x-ui.button>
            </div>

            @if ($featuredArticle)
                <div class="mt-7 grid overflow-hidden rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 md:grid-cols-[1.1fr_0.9fr]">
                    @if ($featuredArticle->featured_image)
                        <a href="{{ route('publik.berita.show', $featuredArticle) }}" class="group block min-h-64 overflow-hidden bg-paper-deep md:min-h-80">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($featuredArticle->featured_image) }}" alt="{{ $featuredArticle->title }}" class="size-full min-h-64 object-cover transition duration-500 group-hover:scale-[1.02]" loading="lazy" decoding="async">
                        </a>
                    @else
                        <div class="board-face flex min-h-64 items-end p-7 sm:p-9 md:min-h-80">
                            <span class="max-w-xs text-3xl font-extrabold leading-tight tracking-tight text-white sm:text-4xl">Informasi yang perlu diketahui keluarga madrasah</span>
                        </div>
                    @endif
                    <div class="flex flex-col justify-center p-6 sm:p-9 lg:p-11">
                        @if ($featuredArticle->category)
                            <x-ui.badge variant="primary" :dot="false" class="self-start">{{ $featuredArticle->category }}</x-ui.badge>
                        @endif
                        <h3 class="mt-4 text-2xl font-extrabold leading-tight tracking-tight text-ink sm:text-3xl">
                            <a href="{{ route('publik.berita.show', $featuredArticle) }}" class="hover:text-primary-strong">{{ $featuredArticle->title }}</a>
                        </h3>
                        <p class="mt-3 text-sm leading-relaxed text-ink-soft">{{ $featuredArticle->excerpt(210) }}</p>
                        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-rule/70 pt-4">
                            <time class="font-mono text-xs text-ink-faint" datetime="{{ $featuredArticle->published_at?->toDateString() }}">{{ $featuredArticle->published_at?->isoFormat('D MMMM YYYY') }}</time>
                            <a href="{{ route('publik.berita.show', $featuredArticle) }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-bold text-primary-strong hover:underline">
                                Baca selengkapnya <x-svg-arrow-right class="size-4" aria-hidden="true" />
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if ($articles->isNotEmpty())
                <div class="mt-5 grid grid-cols-1 gap-x-8 divide-y divide-rule/70 sm:grid-cols-2 sm:divide-y-0">
                    @foreach ($articles as $article)
                        <a href="{{ route('publik.berita.show', $article) }}" class="group flex min-h-24 items-start justify-between gap-4 border-b border-rule/70 py-5 sm:odd:pr-4 sm:even:pl-4">
                            <div>
                                @if ($article->category)
                                    <p class="text-xs font-bold text-primary-strong">{{ $article->category }}</p>
                                @endif
                                <h3 class="mt-1 text-base font-bold leading-snug text-ink group-hover:text-primary-strong">{{ $article->title }}</h3>
                                <time class="mt-2 block font-mono text-xs text-ink-faint" datetime="{{ $article->published_at?->toDateString() }}">{{ $article->published_at?->isoFormat('D MMM YYYY') }}</time>
                            </div>
                            <x-svg-arrow-up-right class="mt-1 size-5 shrink-0 text-ink-faint transition group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:text-primary" aria-hidden="true" />
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @else
        <section class="mt-16 rounded-sheet bg-sheet p-7 shadow-sheet ring-1 ring-inset ring-rule/60 sm:mt-20 sm:p-10">
            <h2 class="text-2xl font-extrabold tracking-tight text-ink">Kabar Madrasah</h2>
            <p class="mt-2 text-sm leading-relaxed text-ink-soft">Belum ada berita yang dipublikasikan. Informasi akan ditambahkan oleh tim madrasah.</p>
        </section>
    @endif

    @if ($agenda->isNotEmpty())
        <section class="mt-16 sm:mt-20" aria-labelledby="agenda-heading">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 id="agenda-heading" class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Agenda Mendatang</h2>
                    <p class="mt-2 text-sm leading-relaxed text-ink-soft">Jadwal dan pengumuman yang sedang berlaku.</p>
                </div>
                <a href="{{ route('publik.agenda.index') }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-bold text-primary-strong hover:underline">Lihat agenda <x-svg-arrow-right class="size-4" aria-hidden="true" /></a>
            </div>
            <div class="mt-6 divide-y divide-rule-strong/70 border-y border-rule-strong/70">
                @foreach ($agenda as $item)
                    <article class="grid grid-cols-[4.5rem_1fr] items-start gap-4 py-5 sm:grid-cols-[5.5rem_1fr_auto] sm:items-center sm:gap-6">
                        <div class="rounded-control bg-primary-soft px-2 py-3 text-center text-primary-strong">
                            <span class="block font-mono text-2xl font-extrabold tabular">{{ $item->tanggal?->format('d') ?: '—' }}</span>
                            <span class="mt-1 block text-xs font-bold uppercase">{{ $item->tanggal?->isoFormat('MMM') ?: 'Info' }}</span>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.badge :variant="$item->jenis === 'agenda' ? 'info' : 'warning'">{{ $item->jenis === 'agenda' ? 'Agenda' : 'Pengumuman' }}</x-ui.badge>
                                @if ($item->waktu)<span class="text-xs font-semibold text-ink-faint">{{ $item->waktu->format('H:i') }} WIB</span>@endif
                            </div>
                            <h3 class="mt-2 text-base font-bold leading-snug text-ink">{{ $item->title }}</h3>
                            @if ($item->isi)<p class="mt-1 line-clamp-2 text-sm leading-relaxed text-ink-soft">{{ $item->isi }}</p>@endif
                        </div>
                        @if ($item->lokasi)
                            <p class="col-start-2 inline-flex items-center gap-1.5 text-xs font-semibold text-ink-faint sm:col-start-auto"><x-svg-map-pin class="size-4" aria-hidden="true" />{{ $item->lokasi }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if ($photos->isNotEmpty())
        <section class="mt-16 sm:mt-20" aria-labelledby="galeri-heading">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 id="galeri-heading" class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Potret Kegiatan</h2>
                    <p class="mt-2 text-sm leading-relaxed text-ink-soft">Dokumentasi yang dibagikan melalui galeri madrasah.</p>
                </div>
                <x-ui.button variant="secondary" icon="photo" :href="route('publik.galeri.index')">Buka galeri</x-ui.button>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                @foreach ($photos as $photo)
                    <a href="{{ route('publik.galeri.show', $photo->album) }}" class="group relative aspect-[4/5] overflow-hidden rounded-sheet bg-paper-deep shadow-sheet ring-1 ring-inset ring-rule/60 first:col-span-2 first:row-span-2 first:aspect-square sm:first:aspect-auto">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($photo->file_path) }}" alt="{{ $photo->caption ?: $photo->album->title }}" class="size-full object-cover transition duration-500 group-hover:scale-[1.03]" loading="lazy" decoding="async">
                        <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-board-deep/85 to-transparent px-4 pb-4 pt-12 text-sm font-bold text-white">{{ $photo->caption ?: $photo->album->title }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-16 overflow-hidden rounded-sheet bg-paper-deep sm:mt-20">
        <div class="grid gap-8 px-6 py-8 sm:px-10 sm:py-10 lg:grid-cols-[1fr_auto] lg:items-center lg:px-14">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Informasi penerimaan peserta didik</h2>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-ink-soft">Lihat jadwal, persyaratan, dan informasi pendaftaran PPDB maupun siswa pindahan.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <x-ui.button variant="primary" icon="academic-cap" :href="route('ppdb.form')">{{ $ppdbOpen ? 'Daftar PPDB' : 'Informasi PPDB' }}</x-ui.button>
                <x-ui.button variant="secondary" icon="arrow-right-start-on-rectangle" :href="route('pindahan.form')">{{ $mutasiOpen ? 'Daftar Pindahan' : 'Info Pindahan' }}</x-ui.button>
            </div>
        </div>
    </section>
</x-layouts.publik>
