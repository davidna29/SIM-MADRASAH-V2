<x-layouts.page title="Rekap Perpustakaan" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    <x-slot:actions>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.pdf', 'perpustakaan') }}">PDF</x-ui.button>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.csv', 'perpustakaan') }}">CSV</x-ui.button>
    </x-slot:actions>

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Total Buku</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['total_buku'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Pinjaman Aktif</p>
                <p class="mt-1 text-2xl font-bold tabular text-warning">{{ $data['pinjaman_aktif'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Anggota Aktif</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['anggota_aktif'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Terlambat</p>
                <p class="mt-1 text-2xl font-bold tabular text-danger">{{ $data['terlambat'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ui.sheet title="Komposisi Buku" :pinned="true">
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-ink-soft">Buku Fisik</span>
                        <span class="font-bold tabular text-ink">{{ $data['buku_fisik'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-ink-soft">Ebook</span>
                        <span class="font-bold tabular text-ink">{{ $data['buku_ebook'] }}</span>
                    </div>
                </div>
            </x-ui.sheet>

            <x-ui.sheet title="Buku Paling Populer" :pinned="true">
                @if ($data['buku_populer']->isEmpty())
                    <p class="text-sm text-ink-soft">Belum ada data peminjaman.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($data['buku_populer'] as $item)
                            <div class="flex items-center justify-between text-sm">
                                <span class="truncate text-ink">{{ $item->book?->title ?? '–' }}</span>
                                <span class="shrink-0 font-bold tabular text-ink">{{ $item->jumlah }}x</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
