@php
    $statusList = array_keys($statuses);
    $unitOptions = $units->pluck('name', 'id');
@endphp

<x-layouts.page
    :title="'Kehadiran Guru & Pegawai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pegawai.kehadiran.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kehadiran Guru & Pegawai</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Catat kehadiran harian tenaga pendidik & kependidikan. Status meliputi hadir, izin, sakit,
                    dinas luar, cuti, terlambat, pulang awal, dan alpha.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button variant="secondary" size="sm" icon="chart-bar" href="{{ route('pegawai.kehadiran.rekap-bulanan') }}">Rekap Bulanan</x-ui.button>
                <x-ui.button variant="secondary" size="sm" icon="calendar-days" href="{{ route('pegawai.kehadiran.rekap-tahunan') }}">Rekap Tahunan</x-ui.button>
            </div>
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

        <!-- Filter unit, pencarian, & tanggal -->
        <form method="GET" action="{{ route('pegawai.kehadiran.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="unit_id" class="block pb-1.5 text-xs font-bold text-ink">Unit Kerja</label>
                    <x-ui.select name="unit_id" :full="false" class="w-48" :options="$unitOptions" :selected="request('unit_id')" placeholder="Semua unit" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama / NIP…"
                        class="w-48 rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="date" class="block pb-1.5 text-xs font-bold text-ink">Tanggal</label>
                    <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                        class="rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Muat</x-ui.button>
            </div>
        </form>

        @if ($employees->isNotEmpty())
            <!-- Form absensi -->
            <form method="POST" action="{{ route('pegawai.kehadiran.store') }}" class="mt-6">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $date->format('Y-m-d') }}">

                <x-ui.sheet
                    :title="'Absensi — ' . $date->isoFormat('dddd, D MMMM YYYY')"
                    :subtitle="count($employees) . ' guru & pegawai aktif'"
                    pinned
                    :padding="false">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[860px] border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-rule-strong">
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">NIP</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Unit</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Status</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Jam Masuk</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Jam Keluar</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @foreach ($employees as $employee)
                                    @php $existing = $attendances->get($employee->id); @endphp
                                    <tr class="transition hover:bg-paper/60">
                                        <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">
                                            {{ $employee->nip ?: '—' }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-ink">{{ $employee->person->name }}</td>
                                        <td class="px-4 py-3 text-xs text-ink-soft">{{ $employee->organizationalUnit?->name ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            <x-ui.select
                                                name="attendances[{{ $employee->id }}][status]"
                                                :full="false"
                                                class="w-40"
                                                :options="$statuses"
                                                :selected="$existing?->status ?? 'hadir'" />
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="time" name="attendances[{{ $employee->id }}][clock_in]" value="{{ $existing?->clock_in }}"
                                                class="rounded-[var(--radius-control)] bg-sheet px-2.5 py-1.5 text-xs text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="time" name="attendances[{{ $employee->id }}][clock_out]" value="{{ $existing?->clock_out }}"
                                                class="rounded-[var(--radius-control)] bg-sheet px-2.5 py-1.5 text-xs text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="attendances[{{ $employee->id }}][note]" value="{{ $existing?->note }}"
                                                placeholder="—"
                                                class="w-full max-w-[180px] rounded-[var(--radius-control)] bg-sheet px-2.5 py-1.5 text-xs text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule/70 px-5 py-4 sm:flex-row">
                        <p class="text-xs text-ink-faint">Menyimpan dengan status "Hadir" untuk baris yang tidak diubah. TU / Super Admin dapat mengoreksi tanggal lampau.</p>
                        <x-ui.button type="submit" variant="primary" icon="check">Simpan Kehadiran</x-ui.button>
                    </div>
                </x-ui.sheet>
            </form>
        @else
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Tidak ada guru & pegawai aktif.</p>
                <p class="mt-1 text-xs text-ink-faint">Tambahkan data pada modul Data Guru & Pegawai terlebih dahulu.</p>
            </div>
        @endif
    </div>
</x-layouts.page>
