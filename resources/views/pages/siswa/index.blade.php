<x-layouts.page
    :title="'Data Siswa'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="siswa.index">

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Data Siswa</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Papan data siswa aktif Tahun Ajaran 2026/2027 — riwayat penempatan kelas tidak pernah hilang.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="arrow-down-tray">Import Excel</x-ui.button>
                <x-ui.button variant="primary" icon="user-plus" href="{{ route('siswa.create') }}">Tambah Siswa</x-ui.button>
            </div>
        </div>

        <!-- Filter -->
        <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="min-w-[220px] flex-1">
                    <div class="flex items-center gap-2 rounded-[var(--radius-control)] bg-paper px-3 ring-1 ring-inset ring-rule-strong transition focus-within:ring-2 focus-within:ring-primary">
                        <x-svg-magnifying-glass class="size-4 text-ink-faint" aria-hidden="true" />
                        <input type="search" placeholder="Cari nama atau NIS…" class="w-full bg-transparent py-2.5 text-sm text-ink placeholder:text-ink-faint focus:outline-none" aria-label="Cari siswa">
                    </div>
                </div>
                <x-ui.select name="tahun" class="w-44" :options="[2026 => 'TA 2026/2027', 2025 => 'TA 2025/2026']" />
                <x-ui.select name="kelas" class="w-40" :options="['VII-A' => 'VII-A', 'VIII-C' => 'VIII-C', 'IX-B' => 'IX-B']" />
                <x-ui.select name="status" class="w-40" :options="['aktif' => 'Aktif', 'lulus' => 'Lulus', 'alumni' => 'Alumni']" />
                <x-ui.button variant="secondary" size="md">Terapkan</x-ui.button>
            </div>
        </div>

        <!-- Tabel -->
        <div class="mt-4">
            <x-ui.sheet :padding="false">
                <x-ui.table :headers="['NIS', 'Nama Lengkap', 'Kelas', 'Jenis Kelamin', 'Status', '']">
                    <x-slot name="emptySlot">Tidak ada siswa yang cocok dengan filter.</x-slot>
                    <x-slot>
                        @foreach ($siswa as $s)
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $s['nis'] }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">
                                            {{ \Illuminate\Support\Str::substr($s['nama'], 0, 1) }}
                                        </span>
                                        <span class="font-semibold text-ink">{{ $s['nama'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <x-ui.badge variant="info">{{ $s['kelas'] }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-ink-soft">{{ $s['jk'] === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.button size="sm" variant="ghost" icon="eye">Detail</x-ui.button>
                                        <x-ui.button size="sm" variant="ghost" icon="pencil-square">Ubah</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-4 py-3.5">
                    <x-ui.pagination :current="1" :last="3" />
                </div>
            </x-ui.sheet>
        </div>

        <!-- Nota riwayat -->
        <div class="mt-6">
            <x-ui.alert variant="info" dismissible>
                <strong class="font-bold">Catatan riwayat:</strong> perpindahan kelas, naik kelas, hingga status alumni selalu
                dicatat sebagai lembar baru pada papan riwayat — data lama tidak pernah ditimpa.
            </x-ui.alert>
        </div>
    </div>
</x-layouts.page>
