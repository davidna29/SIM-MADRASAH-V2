<x-layouts.page :title="$type === 'masuk' ? 'Surat Masuk' : 'Surat Keluar'" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $type === 'masuk' ? 'Surat Masuk' : 'Surat Keluar' }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Kelola surat {{ $type === 'masuk' ? 'masuk' : 'keluar' }} madrasah.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('surat.create', ['type' => $type]) }}">
                Tambah Surat {{ $type === 'masuk' ? 'Masuk' : 'Keluar' }}
            </x-ui.button>
        </div>

        {{-- Flash messages --}}
    @if(session('status'))
        <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
    @endif

    {{-- Filter --}}
    <x-ui.sheet title="Filter" :pinned="true" class="mb-6">
        <form method="GET" action="{{ route('surat.index', ['type' => $type]) }}">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="flex flex-wrap items-end gap-4">
                {{-- Search --}}
                <div class="flex-1 min-w-[200px]">
                    <x-ui.field label="Cari">
                        <x-ui.input name="q" value="{{ request('q') }}" placeholder="Nomor, dari/ke, perihal…" />
                    </x-ui.field>
                </div>

                {{-- Status --}}
                <div class="w-40">
                    <x-ui.field label="Status">
                        <x-ui.select name="status" :options="collect($statuses)->mapWithKeys(fn($s) => [$s => ucwords(str_replace('_', ' ', $s))])" :selected="request('status')" placeholder="Semua" :full="false" />
                    </x-ui.field>
                </div>

                {{-- Kategori --}}
                <div class="w-44">
                    <x-ui.field label="Kategori">
                        <x-ui.select name="category" :options="$categories->pluck('name', 'name')" :selected="request('category')" placeholder="Semua" :full="false" />
                    </x-ui.field>
                </div>

                {{-- Prioritas --}}
                <div class="w-36">
                    <x-ui.field label="Prioritas">
                        <x-ui.select name="priority" :options="collect($priorities)->mapWithKeys(fn($p) => [$p => ucfirst($p)])" :selected="request('priority')" placeholder="Semua" :full="false" />
                    </x-ui.field>
                </div>

                {{-- Tanggal --}}
                <div class="w-40">
                    <x-ui.field label="Dari Tanggal">
                        <x-ui.input type="date" name="from" :value="request('from')" :full="false" />
                    </x-ui.field>
                </div>
                <div class="w-40">
                    <x-ui.field label="Sampai Tanggal">
                        <x-ui.input type="date" name="to" :value="request('to')" :full="false" />
                    </x-ui.field>
                </div>

                {{-- Tombol --}}
                <div class="flex gap-2">
                    <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                    <a href="{{ route('surat.index', ['type' => $type]) }}" class="inline-flex h-10 items-center gap-1.5 rounded-[var(--radius-control)] px-4 text-sm font-semibold text-ink-soft transition hover:bg-paper-deep hover:text-ink">
                        Hapus Filter
                    </a>
                </div>
            </div>
        </form>
    </x-ui.sheet>

    {{-- Tabel Surat --}}
    <x-ui.sheet :title="$type === 'masuk' ? 'Daftar Surat Masuk' : 'Daftar Surat Keluar'" :subtitle="$letters->total() . ' surat'" :pinned="true" :padding="false">
        <x-ui.table :headers="$type === 'masuk' ? ['No. Surat', 'Tanggal', 'Pengirim', 'Perihal', 'Status', 'Prioritas', ''] : ['No. Surat', 'Tanggal', 'Penerima', 'Perihal', 'Status', 'Prioritas', '']">
            @forelse ($letters as $letter)
                <tr>
                    <td class="px-4 py-3 font-mono text-xs">{{ $letter->number ?? '–' }}</td>
                    <td class="px-4 py-3">{{ $letter->date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">{{ $letter->from_to }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('surat.show', $letter) }}" class="font-medium text-ink hover:text-primary">{{ $letter->subject }}</a>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <x-ui.badge :variant="$letter->statusBadgeVariant()">{{ $letter->statusLabel() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <x-ui.badge :variant="$letter->priorityBadgeVariant()">{{ $letter->priorityLabel() }}</x-ui.badge>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <x-ui.button size="sm" variant="secondary" icon="eye" href="{{ route('surat.show', $letter) }}">Detail</x-ui.button>
                            <x-ui.button size="sm" variant="ghost" icon="pencil" href="{{ route('surat.edit', $letter) }}">Ubah</x-ui.button>
                            <form method="POST" action="{{ route('surat.destroy', $letter) }}" onsubmit="return confirm('Yakin ingin menghapus surat ini?')">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <x-svg-envelope class="size-12 text-ink-faint" />
                            <div>
                                <p class="font-semibold text-ink">Belum ada surat {{ $type === 'masuk' ? 'masuk' : 'keluar' }}</p>
                                <p class="mt-1 text-sm text-ink-soft">Mulai menambahkan surat baru.</p>
                            </div>
                            <x-ui.button variant="primary" icon="plus" href="{{ route('surat.create', ['type' => $type]) }}">
                                Tambah Surat {{ $type === 'masuk' ? 'Masuk' : 'Keluar' }}
                            </x-ui.button>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        @if ($letters->hasPages())
            <div class="border-t border-rule/70 px-5 py-4">
                <x-ui.pagination :current="$letters->currentPage()" :last="$letters->lastPage()" />
            </div>
        @endif
    </x-ui.sheet>
    </div>
</x-layouts.page>
