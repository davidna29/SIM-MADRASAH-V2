@php
    $typeLabel = \App\Models\Room::TYPES[$type] ?? 'Ruangan';
@endphp

<x-layouts.page
    :title="$typeLabel"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ruangan.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $typeLabel }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    @if ($type === 'laboratorium')
                        Daftar laboratorium beserta kapasitas, kondisi, dan penanggung jawab.
                    @else
                        Daftar ruangan madrasah beserta gedung, lantai, kapasitas, dan kondisi.
                    @endif
                </p>
            </div>
            @can('create', \App\Models\Room::class)
                <x-ui.button variant="primary" icon="plus" href="{{ route('ruangan.create') }}">Tambah Ruangan</x-ui.button>
            @endcan
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('ruangan.index', ['type' => $type]) }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="building" class="block pb-1.5 text-xs font-bold text-ink">Gedung</label>
                    <x-ui.select name="building" :full="false" class="w-44" :options="$buildings->combine($buildings)" :selected="request('building')" placeholder="Semua gedung" />
                </div>
                <div>
                    <label for="condition" class="block pb-1.5 text-xs font-bold text-ink">Kondisi</label>
                    <x-ui.select name="condition" :full="false" class="w-44" :options="$conditions" :selected="request('condition')" placeholder="Semua kondisi" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama / kode / gedung…"
                        class="w-48 rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page', 'type'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('ruangan.index', ['type' => $type]) }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <!-- Tabel -->
        <div class="mt-6">
            <x-ui.sheet :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-rule-strong">
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Kode</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Gedung</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Lantai</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Kapasitas</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Penanggung Jawab</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Kondisi</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule/70">
                            @forelse ($rooms as $room)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $room->code }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('ruangan.show', $room) }}" class="font-semibold text-primary hover:underline">{{ $room->name }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-ink">{{ $room->building ?? '—' }}</td>
                                    <td class="px-4 py-3 text-ink">{{ $room->floor ?? '—' }}</td>
                                    <td class="tabular px-4 py-3 text-center font-mono text-ink">{{ $room->capacity }}</td>
                                    <td class="px-4 py-3 text-ink">{{ $room->penanggJawab?->person?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $condColor = match($room->condition) {
                                                'baik' => 'success',
                                                'rusak_ringan' => 'warning',
                                                'rusak_berat' => 'danger',
                                                'dalam_perbaikan' => 'info',
                                                default => 'neutral',
                                            };
                                        @endphp
                                        <x-ui.badge :variant="$condColor">{{ $room->conditionLabel() }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <x-ui.button variant="ghost" size="sm" href="{{ route('ruangan.show', $room) }}" icon="eye" aria-label="Lihat" />
                                            @can('update', $room)
                                                <x-ui.button variant="ghost" size="sm" href="{{ route('ruangan.edit', $room) }}" icon="pencil-square" aria-label="Ubah" />
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-xs text-ink-faint">
                                        Belum ada {{ strtolower($typeLabel) }} terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($rooms->hasPages())
                    <div class="border-t border-rule/70 px-5 py-3">
                        {{ $rooms->links() }}
                    </div>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
