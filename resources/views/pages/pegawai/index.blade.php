<x-layouts.page
    :title="'Data Guru & Pegawai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pegawai.index">

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Data Guru & Pegawai</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Papan data guru, tenaga kependidikan, dan pimpinan madrasah — identitas dan jabatan tercatat utuh, riwayat tidak pernah hilang.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="arrow-down-tray">Import Excel</x-ui.button>
                <x-ui.button variant="primary" icon="user-plus" href="{{ route('pegawai.create') }}">Tambah Guru/Pegawai</x-ui.button>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('pegawai.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="w-56">
                    <div class="flex items-center gap-2 rounded-[var(--radius-control)] bg-paper px-3 ring-1 ring-inset ring-rule-strong transition focus-within:ring-2 focus-within:ring-primary">
                        <x-svg-magnifying-glass class="size-4 shrink-0 text-ink-faint" aria-hidden="true" />
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIP, NIK…" class="w-full bg-transparent py-2.5 text-sm text-ink placeholder:text-ink-faint focus:outline-none" aria-label="Cari pegawai">
                    </div>
                </div>
                <x-ui.select name="position_id" :full="false" class="w-44" :options="$positionOptions" :selected="request('position_id')" placeholder="Semua jabatan" />
                <x-ui.select name="employee_status" :full="false" class="w-36" :options="['pns' => 'PNS', 'pppk' => 'PPPK', 'honor' => 'Honor']" :selected="request('employee_status')" placeholder="Semua status" />
                <x-ui.select name="status" :full="false" class="w-36" :options="['aktif' => 'Aktif', 'cuti' => 'Cuti', 'nonaktif' => 'Nonaktif']" :selected="request('status')" placeholder="Semua" />
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->hasAny(['q', 'position_id', 'employee_status', 'status']))
                    <x-ui.button variant="ghost" size="md" href="{{ route('pegawai.index') }}">Reset</x-ui.button>
                @endif
            </div>
        </form>

        <!-- Tabel -->
        <div class="mt-4">
            <x-ui.sheet :padding="false">
                <x-ui.table :headers="['Nama Lengkap', 'NIP / NIK', 'Jabatan', 'Unit', 'Status Pegawai', 'Status', '']">
                    <x-slot name="emptySlot">Tidak ada pegawai yang cocok dengan filter.</x-slot>
                    <x-slot>
                        @foreach ($employees as $emp)
                            @php
                                $sp = match ($emp->employee_status) {
                                    'pns' => ['PNS', 'primary'],
                                    'pppk' => ['PPPK', 'info'],
                                    default => ['Honor', 'neutral'],
                                };
                                $st = match ($emp->status) {
                                    'aktif' => ['Aktif', 'success'],
                                    'cuti' => ['Cuti', 'warning'],
                                    default => ['Nonaktif', 'danger'],
                                };
                            @endphp
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">
                                            {{ mb_substr($emp->person->name, 0, 1) }}
                                        </span>
                                        <div>
                                            <span class="font-semibold text-ink">{{ $emp->person->name }}</span>
                                            <p class="mt-0.5 text-xs text-ink-faint">
                                                {{ collect([$emp->person->birth_place, optional($emp->person->birth_date)->format('d/m/Y')])->filter()->implode(', ') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="tabular px-4 py-3">
                                    <span class="font-mono text-xs font-semibold text-ink-faint">{{ $emp->nip ?? $emp->person->nik }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-ink">{{ $emp->positionLabel() }}</td>
                                <td class="px-4 py-3 text-ink-soft">{{ $emp->organizationalUnit?->name }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$sp[1]" :dot="false">{{ $sp[0] }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$st[1]">{{ $st[0] }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.button size="sm" variant="ghost" icon="eye" href="{{ route('pegawai.show', $emp) }}">Detail</x-ui.button>
                                        <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('pegawai.edit', $emp) }}">Ubah</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-4 py-3.5">
                    <x-ui.pagination :current="$employees->currentPage()" :last="$employees->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>

        <!-- Nota riwayat -->
        <div class="mt-6">
            <x-ui.alert variant="info" dismissible>
                <strong class="font-bold">Catatan riwayat:</strong> jabatan dan mutasi (pindah tugas, promosi, cuti) dicatat sebagai lembar riwayat
                jabatan pada papan kepegawaian — data lama tidak pernah ditimpa.
            </x-ui.alert>
        </div>
    </div>
</x-layouts.page>
