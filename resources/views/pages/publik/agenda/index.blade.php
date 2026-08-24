<x-layouts.publik :title="'Agenda'">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Agenda & Pengumuman</h1>
        <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
            Kegiatan dan pemberitahuan resmi madrasah.
        </p>
    </div>

    <div class="mt-8 space-y-4">
        @forelse ($agenda as $item)
            <div class="rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.badge :variant="$item->jenis === 'agenda' ? 'info' : 'warning'">{{ $item->jenis === 'agenda' ? 'Agenda' : 'Pengumuman' }}</x-ui.badge>
                            @if ($item->tanggal)
                                <span class="tabular font-mono text-xs font-semibold text-ink">
                                    {{ $item->tanggal->isoFormat('dddd, D MMM YYYY') }}
                                    @if ($item->waktu) · {{ $item->waktu->format('H:i') }} WIB @endif
                                </span>
                            @endif
                        </div>
                        <h2 class="mt-2 text-base font-bold leading-snug text-ink">{{ $item->title }}</h2>
                        @if ($item->isi)
                            <p class="mt-1.5 whitespace-pre-line text-[13px] leading-relaxed text-ink-soft">{{ $item->isi }}</p>
                        @endif
                    </div>
                    @if ($item->lokasi)
                        <x-ui.badge variant="neutral" icon="map-pin">{{ $item->lokasi }}</x-ui.badge>
                    @endif
                </div>
                @if ($item->penanggung_jawab)
                    <p class="mt-3 border-t border-rule/70 pt-3 text-xs text-ink-faint">Penanggung jawab: {{ $item->penanggung_jawab }}</p>
                @endif
            </div>
        @empty
            <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Belum ada agenda/pengumuman.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        <x-ui.pagination :current="$agenda->currentPage()" :last="$agenda->lastPage()" />
    </div>
</x-layouts.publik>
