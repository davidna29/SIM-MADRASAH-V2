<x-layouts.page
    :title="'Pelanggaran Siswa'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pelanggaran.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pelanggaran & Disiplin</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Catat pelanggaran siswa dan tindak lanjutnya (poin, pemanggilan orang tua, surat peringatan).
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('pelanggaran.create') }}">Catat Pelanggaran</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('pelanggaran.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-40" :options="$classes->pluck('name', 'id')" :selected="request('class_group_id')" placeholder="Semua kelas" />
                </div>
                <div>
                    <label for="tingkat" class="block pb-1.5 text-xs font-bold text-ink">Tingkat</label>
                    <x-ui.select name="tingkat" :full="false" class="w-40" :options="collect($tingkatList)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])->all()" :selected="request('tingkat')" placeholder="Semua tingkat" />
                </div>
                <div>
                    <label for="status_penyelesaian" class="block pb-1.5 text-xs font-bold text-ink">Penyelesaian</label>
                    <x-ui.select name="status_penyelesaian" :full="false" class="w-44" :options="['proses' => 'Proses', 'selesai' => 'Selesai', 'dibebaskan' => 'Dibebaskan']" :selected="request('status_penyelesaian')" placeholder="Semua" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Nama / NIS" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('pelanggaran.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Daftar Pelanggaran" :subtitle="$offenses->total() . ' pelanggaran'" pinned :padding="false">
                <x-ui.table :headers="['NIS', 'Nama', 'Kategori', 'Tingkat', 'Poin', 'Tanggal', 'Penyelesaian', '']">
                    <x-slot name="emptySlot">Belum ada pelanggaran.</x-slot>
                    <x-slot>
                        @foreach ($offenses as $o)
                            @php
                                $tv = match ($o->tingkat) {
                                    'berat' => 'danger',
                                    'sedang' => 'info',
                                    default => 'warning',
                                };
                                $sv = match ($o->status_penyelesaian) {
                                    'selesai' => 'success',
                                    'dibebaskan' => 'neutral',
                                    default => 'warning',
                                };
                            @endphp
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $o->student->nis }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-semibold text-ink">{{ $o->student->displayName() }}</td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ $o->kategori }}</td>
                                <td class="px-4 py-3"><x-ui.badge :variant="$tv">{{ ucfirst($o->tingkat) }}</x-ui.badge></td>
                                <td class="tabular px-4 py-3 text-center font-mono font-semibold text-ink">{{ $o->poin }}</td>
                                <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs text-ink-soft">{{ $o->tanggal_kejadian->format('d M Y') }}</td>
                                <td class="px-4 py-3"><x-ui.badge :variant="$sv">{{ ucfirst($o->status_penyelesaian) }}</x-ui.badge></td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button size="sm" variant="secondary" icon="pencil-square" href="{{ route('pelanggaran.edit', $o) }}">Ubah</x-ui.button>
                                        <form method="POST" action="{{ route('pelanggaran.destroy', $o) }}" onsubmit="return confirm('Hapus pelanggaran ini?');">
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
                    <x-ui.pagination :current="$offenses->currentPage()" :last="$offenses->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
