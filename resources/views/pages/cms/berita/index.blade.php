<x-layouts.page
    :title="'Kelola Berita'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.berita.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kelola Berita</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Portal Berita/CMS — alur 8 status: draft → diajukan → review → revisi → disetujui → dijadwalkan → dipublikasikan → arsip.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('cms.berita.create') }}">Tulis Berita</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('cms.berita.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="false" class="w-48" :options="$statusLabels" :selected="request('status')" placeholder="Semua status" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Judul atau isi…" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('cms.berita.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Daftar Berita" :subtitle="$articles->total() . ' berita'" pinned :padding="false">
                <x-ui.table :headers="['Judul', 'Status', 'Kategori', 'Penulis', 'Diperbarui', '']">
                    <x-slot name="emptySlot">Belum ada berita.</x-slot>
                    <x-slot>
                        @foreach ($articles as $article)
                            @php
                                $variant = match ($article->status) {
                                    'publish', 'disetujui' => 'success',
                                    'dijadwalkan' => 'warning',
                                    'revisi' => 'danger',
                                    'diajukan', 'review' => 'info',
                                    default => 'neutral',
                                };
                            @endphp
                            <tr class="transition hover:bg-paper/60">
                                <td class="max-w-[340px] px-4 py-3">
                                    <p class="truncate font-semibold text-ink">{{ $article->title }}</p>
                                    <p class="tabular mt-0.5 font-mono text-[11px] text-ink-faint">/{{ $article->slug }}</p>
                                </td>
                                <td class="px-4 py-3"><x-ui.badge :variant="$variant">{{ $statusLabels[$article->status] ?? $article->status }}</x-ui.badge></td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ $article->category ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $article->author?->name ?? '—' }}</td>
                                <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs text-ink-faint">{{ $article->updated_at->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button size="sm" variant="secondary" icon="eye" href="{{ route('cms.berita.show', $article) }}">Lihat</x-ui.button>
                                        <form method="POST" action="{{ route('cms.berita.destroy', $article) }}" onsubmit="return confirm('Hapus berita ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3">
                    <x-ui.pagination :current="$articles->currentPage()" :last="$articles->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
