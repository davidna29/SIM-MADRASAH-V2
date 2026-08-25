<x-layouts.page
    :title="'Ekstrakurikuler'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ekskul.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Ekstrakurikuler</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Daftar ekskul, pembina, jadwal, anggota, presensi & penilaian predikat.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('ekskul.create') }}">Tambah Ekskul</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('ekskul.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="false" class="w-44" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" :selected="request('status')" placeholder="Semua status" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Nama ekskul…" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('ekskul.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Daftar Ekstrakurikuler" :subtitle="$ekskuls->total() . ' ekskul'" pinned :padding="false">
                <x-ui.table :headers="['Nama', 'Pembina', 'Jadwal', 'Anggota', 'Status', '']">
                    <x-slot name="emptySlot">Belum ada ekstrakurikuler.</x-slot>
                    <x-slot>
                        @foreach ($ekskuls as $ekskul)
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-ink">{{ $ekskul->name }}</p>
                                    @if ($ekskul->lokasi)
                                        <p class="mt-0.5 text-[11px] text-ink-faint">{{ $ekskul->lokasi }}</p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $ekskul->pembina?->name ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">
                                    {{ $ekskul->hari ? ucfirst($ekskul->hari) : '—' }}
                                    {{ $ekskul->waktu ? '· '.substr($ekskul->waktu, 0, 5) : '' }}
                                </td>
                                <td class="tabular px-4 py-3 text-center font-mono text-xs font-semibold text-ink">{{ $ekskul->members_count }}</td>
                                <td class="px-4 py-3"><x-ui.badge :variant="$ekskul->status === 'aktif' ? 'success' : 'neutral'">{{ ucfirst($ekskul->status) }}</x-ui.badge></td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button size="sm" variant="secondary" icon="eye" href="{{ route('ekskul.show', $ekskul) ?>">Kelola</x-ui.button>
                                        <form method="POST" action="{{ route('ekskul.destroy', $ekskul) }}" onsubmit="return confirm('Hapus ekskul beserta anggota & presensinya?');">
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
                    <x-ui.pagination :current="$ekskuls->currentPage()" :last="$ekskuls->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
