<x-layouts.page
    :title="'Konseling (BK)'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="konseling.index">

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Konseling (BK)</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Catatan sesi bimbingan dan konseling siswa — kerahasiaan terjaga sesuai level yang ditentukan.
                </p>
            </div>
            @can('create', \App\Models\CounselingSession::class)
                <x-ui.button variant="primary" icon="plus" href="{{ route('konseling.create') }}">Catat Konseling</x-ui.button>
            @endcan
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('konseling.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-40" :options="$classes->pluck('name', 'id')" :selected="request('class_group_id')" placeholder="Semua kelas" />
                </div>
                <div>
                    <label for="confidentiality_level" class="block pb-1.5 text-xs font-bold text-ink">Kerahasiaan</label>
                    <x-ui.select name="confidentiality_level" :full="false" class="w-52" :options="$confidentialityLevels" :selected="request('confidentiality_level')" placeholder="Semua level" />
                </div>
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="false" class="w-36" :options="['aktif' => 'Aktif', 'ditutup' => 'Ditutup']" :selected="request('status')" placeholder="Semua" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Nama / NIS" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('konseling.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet :padding="false">
                <x-ui.table :headers="['Tanggal', 'Siswa', 'Kelas', 'Jenis', 'Topik', 'Kerahasiaan', 'Status', '']">
                    <x-slot name="emptySlot">Belum ada sesi konseling.</x-slot>
                    <x-slot>
                        @foreach ($sessions as $s)
                            @php
                                $cv = match($s->confidentiality_level) {
                                    'guru_bk_only' => ['danger', 'Hanya Guru BK'],
                                    'plus_kepala' => ['warning', '+Kepala'],
                                    default => ['info', '+Wali Kelas'],
                                };
                                $sv = match($s->status) {
                                    'aktif' => ['success', 'Aktif'],
                                    default => ['neutral', 'Ditutup'],
                                };
                            @endphp
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs text-ink-soft">{{ $s->session_date->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-semibold text-ink">{{ $s->enrollment->student->displayName() }}</td>
                                <td class="px-4 py-3 text-sm text-ink-soft">{{ $s->enrollment->classGroup->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-ink-soft">{{ ucfirst($s->counseling_type) }}</td>
                                <td class="px-4 py-3 text-sm text-ink">{{ Str::limit($s->topic, 30) }}</td>
                                <td class="px-4 py-3"><x-ui.badge :variant="$cv[0]">{{ $cv[1] }}</x-ui.badge></td>
                                <td class="px-4 py-3"><x-ui.badge :variant="$sv[0]">{{ $sv[1] }}</x-ui.badge></td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-ui.button size="sm" variant="ghost" icon="eye" href="{{ route('konseling.show', $s) }}">Detail</x-ui.button>
                                        @can('update', $s)
                                            <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('konseling.edit', $s) }}">Ubah</x-ui.button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3">
                    <x-ui.pagination :current="$sessions->currentPage()" :last="$sessions->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
