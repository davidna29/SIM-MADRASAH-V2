<x-layouts.page
    :title="'Mutasi Siswa Keluar'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mutasi-keluar.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Mutasi Siswa Keluar</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Pencatatan siswa yang pindah keluar madrasah. Mencatat mutasi akan melepas siswa dari
                    rombel aktif tahun berjalan (status <code>keluar</code>).
                </p>
            </div>
            <x-ui.button variant="primary" size="sm" icon="user-plus" href="{{ route('mutasi-keluar.create') }}">Catat Mutasi Keluar</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>@foreach ($errors->all() as $error) {{ $error }} @endforeach</x-ui.alert></div>
        @endif

        {{-- Ringkasan statistik --}}
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
            <x-ui.kpi label="Total Mutasi" :value="$stats['total']" icon="arrow-right-end-on-rectangle" />
            <x-ui.kpi label="Tahun {{ $tahun?->name ?? 'Berjalan' }}" :value="$stats['thisYear']" icon="calendar-days" />
            <x-ui.kpi label="3 Bulan Terakhir" :value="$stats['recent']" icon="clock" />
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('mutasi-keluar.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="academic_year_id" class="block pb-1.5 text-xs font-bold text-ink">Tahun Ajaran</label>
                    <x-ui.select name="academic_year_id" :full="false" class="w-44"
                        :options="$years" :selected="request('academic_year_id')" placeholder="Semua tahun" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama / NIS…"
                        class="w-56 rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('mutasi-keluar.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        {{-- Tabel --}}
        <div class="mt-6">
            <x-ui.sheet :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-rule-strong">
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Siswa</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">NIS</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Sekolah Tujuan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Alasan</th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule/70">
                            @forelse ($mutations as $m)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">
                                                {{ mb_substr($m->student->displayName(), 0, 1) }}
                                            </span>
                                            <span class="font-semibold text-ink">{{ $m->student->displayName() }}</span>
                                        </div>
                                    </td>
                                    <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $m->student->nis ?? '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-ink-soft">
                                        {{ $m->sekolah_tujuan }}
                                        @if ($m->academicYear)
                                            <br><span class="text-ink-faint">TA {{ $m->academicYear->name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-ink-soft">{{ $m->tanggal_mutasi?->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <x-ui.badge variant="neutral">{{ $m->alasanLabel() }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <x-ui.button variant="ghost" size="sm" href="{{ route('mutasi-keluar.show', $m) }}" icon="eye" aria-label="Lihat" />
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-8 text-center text-xs text-ink-faint">Belum ada catatan mutasi keluar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($mutations->hasPages())
                    <div class="border-t border-rule/70 px-5 py-3">{{ $mutations->links() }}</div>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
