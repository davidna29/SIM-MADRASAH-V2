<x-layouts.publik :title="'Agenda & Pengumuman'" :description="'Jadwal kegiatan dan pemberitahuan resmi madrasah.'">
    <div class="mx-auto max-w-5xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Agenda & Pengumuman']]" />
        <header class="mt-8 flex flex-wrap items-end justify-between gap-4 border-b border-rule-strong/70 pb-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Agenda & Pengumuman</h1>
                <p class="mt-2 max-w-prose text-sm leading-relaxed text-ink-soft">Jadwal kegiatan dan pemberitahuan yang ditujukan untuk masyarakat.</p>
            </div>
            <span class="font-mono text-xs text-ink-faint">{{ $agenda->total() }} informasi</span>
        </header>

        @if ($agenda->isNotEmpty())
            <div class="mt-3 divide-y divide-rule-strong/70">
                @foreach ($agenda as $item)
                    <article class="grid grid-cols-[4.5rem_1fr] gap-4 py-6 sm:grid-cols-[6rem_1fr_auto] sm:gap-6">
                        <div class="self-start rounded-control bg-board px-2 py-3 text-center text-board-ink">
                            <span class="block font-mono text-3xl font-extrabold tabular">{{ $item->tanggal?->format('d') ?: '—' }}</span>
                            <span class="mt-1 block text-xs font-bold uppercase">{{ $item->tanggal?->isoFormat('MMM') ?: 'Info' }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.badge :variant="$item->jenis === 'agenda' ? 'info' : 'warning'">{{ $item->jenis === 'agenda' ? 'Agenda' : 'Pengumuman' }}</x-ui.badge>
                                @if ($item->tanggal)
                                    <time class="text-xs font-semibold text-ink-faint" datetime="{{ $item->tanggal->toDateString() }}">{{ $item->tanggal->isoFormat('dddd, D MMMM YYYY') }}</time>
                                @endif
                            </div>
                            <h2 class="mt-2 text-lg font-extrabold leading-snug tracking-tight text-ink sm:text-xl">{{ $item->title }}</h2>
                            @if ($item->isi)
                                <p class="mt-2 max-w-[70ch] whitespace-pre-line text-sm leading-relaxed text-ink-soft">{{ $item->isi }}</p>
                            @endif
                            @if ($item->penanggung_jawab)
                                <p class="mt-3 text-xs text-ink-faint">Informasi: {{ $item->penanggung_jawab }}</p>
                            @endif
                        </div>
                        @if ($item->waktu || $item->lokasi)
                            <div class="col-start-2 flex flex-wrap gap-x-4 gap-y-2 text-xs font-semibold text-ink-soft sm:col-start-auto sm:flex-col sm:items-end sm:text-right">
                                @if ($item->waktu)<span class="inline-flex items-center gap-1.5"><x-svg-clock class="size-4 text-primary" aria-hidden="true" />{{ $item->waktu->format('H:i') }} WIB</span>@endif
                                @if ($item->lokasi)<span class="inline-flex items-center gap-1.5"><x-svg-map-pin class="size-4 text-primary" aria-hidden="true" />{{ $item->lokasi }}</span>@endif
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
            <div class="mt-8"><x-ui.pagination :current="$agenda->currentPage()" :last="$agenda->lastPage()" /></div>
        @else
            <div class="mt-8 rounded-sheet bg-sheet p-8 shadow-sheet ring-1 ring-inset ring-rule/60 sm:p-12">
                <x-svg-calendar-days class="size-8 text-primary" aria-hidden="true" />
                <h2 class="mt-4 text-lg font-extrabold text-ink">Belum ada agenda publik</h2>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Kegiatan dan pengumuman baru akan ditampilkan di halaman ini.</p>
                <x-ui.button class="mt-5" variant="secondary" :href="route('publik.beranda')">Kembali ke Beranda</x-ui.button>
            </div>
        @endif
    </div>
</x-layouts.publik>
