<x-layouts.page
    :title="'Pengguna & Role'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pengguna.index">

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pengguna & Role</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Papan data akun pengguna sistem — identitas, role, dan status tercatat utuh.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="primary" icon="user-plus" href="{{ route('pengguna.create') }}">Tambah Pengguna</x-ui.button>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('pengguna.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="w-56">
                    <div class="flex items-center gap-2 rounded-[var(--radius-control)] bg-paper px-3 ring-1 ring-inset ring-rule-strong transition focus-within:ring-2 focus-within:ring-primary">
                        <x-svg-magnifying-glass class="size-4 shrink-0 text-ink-faint" aria-hidden="true" />
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, username, email…" class="w-full bg-transparent py-2.5 text-sm text-ink placeholder:text-ink-faint focus:outline-none" aria-label="Cari pengguna">
                    </div>
                </div>
                <x-ui.select name="role" :full="false" class="w-44" :options="$roleOptions" :selected="request('role')" placeholder="Semua role" />
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->hasAny(['q', 'role']))
                    <x-ui.button variant="ghost" size="md" href="{{ route('pengguna.index') }}">Reset</x-ui.button>
                @endif
            </div>
        </form>

        <!-- Tabel -->
        <div class="mt-4">
            <x-ui.sheet :padding="false">
                <x-ui.table :headers="['Nama', 'Username', 'Email', 'Role Utama', 'Role Tambahan', '']">
                    <x-slot name="emptySlot">Tidak ada pengguna yang cocok dengan filter.</x-slot>
                    <x-slot>
                        @foreach ($users as $u)
                            @php
                                $roleBadge = match($u->role) {
                                    'super_admin' => ['Super Admin', 'danger'],
                                    'kepala_madrasah' => ['Kepala Madrasah', 'info'],
                                    'guru' => ['Guru', 'primary'],
                                    'siswa' => ['Siswa', 'success'],
                                    'orang_tua' => ['Orang Tua', 'warning'],
                                    default => [ucfirst(str_replace('_', ' ', $u->role)), 'neutral'],
                                };
                            @endphp
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">
                                            {{ mb_substr($u->name, 0, 1) }}
                                        </span>
                                        <span class="font-semibold text-ink">{{ $u->name }}</span>
                                    </div>
                                </td>
                                <td class="tabular px-4 py-3">
                                    <span class="font-mono text-xs font-semibold text-ink-faint">{{ $u->username ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-ink-soft">{{ $u->email }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$roleBadge[1]">{{ $roleBadge[0] }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3">
                                    @forelse ($u->userRoles as $ur)
                                        @php
                                            $extraLabel = match($ur->role) {
                                                'super_admin' => 'Super Admin',
                                                'kepala_madrasah' => 'Kepala Madrasah',
                                                'guru' => 'Guru',
                                                'siswa' => 'Siswa',
                                                'orang_tua' => 'Orang Tua',
                                                default => ucfirst(str_replace('_', ' ', $ur->role)),
                                            };
                                        @endphp
                                        <x-ui.badge :variant="'neutral'" :dot="false">{{ $extraLabel }}</x-ui.badge>
                                    @empty
                                        <span class="text-xs text-ink-faint">—</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.button size="sm" variant="ghost" icon="eye" href="{{ route('pengguna.show', $u) }}">Detail</x-ui.button>
                                        <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('pengguna.edit', $u) }}">Ubah</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-4 py-3.5">
                    <x-ui.pagination :current="$users->currentPage()" :last="$users->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
