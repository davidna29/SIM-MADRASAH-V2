@props([
    'role' => null,
    'roleLabel' => null,
    'madrasah' => 'MTs Al-Ikhlas Mulia',
    'activeRoute' => null,
])

@php
    $user = auth()->user();
    $role = $role ?? $user?->role ?? 'super_admin';
    $roleLabel = $roleLabel ?? (match ($role) {
        'guru' => 'Guru Mata Pelajaran',
        'orang_tua' => 'Orang Tua / Wali',
        default => 'Super Admin',
    });
    $initials = $user ? mb_strtoupper(mb_substr(collect(preg_split('/\s+/', $user->name))->first(), 0, 2)) : 'AM';
@endphp

<div x-data="{ mobileOpen: false, userOpen: false, notifOpen: false }" class="min-h-screen lg:flex">
    <!-- Backdrop mobile -->
    <div x-show="mobileOpen" x-cloak @click="mobileOpen = false"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-board-deep/60 backdrop-blur-[2px] lg:hidden"></div>

    <!-- Sidebar (Papan) -->
    <aside :class="mobileOpen ? 'translate-x-0' : '-translate-x-full'"
        class="board-face fixed inset-y-0 left-0 z-50 flex w-72 shrink-0 flex-col shadow-sheet-raised transition-transform duration-200 ease-out lg:static lg:h-screen lg:translate-x-0 lg:shadow-none lg:transition-none">

        <!-- Kop papan -->
        <div class="flex items-center gap-3 border-b border-white/10 px-5 py-4">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-[var(--radius-control)] bg-board-ink text-board">
                <x-svg-academic-cap class="size-6" aria-hidden="true" />
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-extrabold leading-tight tracking-tight text-board-ink">{{ $madrasah }}</p>
                <p class="mt-0.5 text-[11px] font-medium text-board-ink/60">Sistem Informasi Manajemen</p>
            </div>
            <button type="button" @click="mobileOpen = false" class="ml-auto rounded-md p-1.5 text-board-ink/60 transition hover:bg-white/10 hover:text-board-ink lg:hidden" aria-label="Tutup menu">
                <x-svg-x-mark class="size-5" aria-hidden="true" />
            </button>
        </div>

        <x-ui.sidebar :role="$role" :active-route="$activeRoute" />

        <!-- Pengguna papan -->
        <div class="border-t border-white/10 p-3">
            <div class="flex items-center gap-3 rounded-[var(--radius-control)] bg-board-soft/40 px-3 py-2.5">
                <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xs font-extrabold text-primary-strong">{{ $initials }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[13px] font-bold text-board-ink">{{ $user?->name ?? "Admin Madrasah" }}</p>
                    <p class="truncate text-[11px] text-board-ink/60">{{ $roleLabel }}</p>
                </div>
                <span class="pin-dot size-2 bg-success" aria-hidden="true"></span>
            </div>
        </div>
    </aside>

    <div class="flex min-h-screen min-w-0 flex-1 flex-col">
        <!-- Topbar -->
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-rule-strong/70 bg-paper/85 px-4 py-3 backdrop-blur-md sm:px-6 lg:px-8">
            <button type="button" @click="mobileOpen = true" class="rounded-md p-2 text-ink-soft transition hover:bg-paper-deep hover:text-ink lg:hidden" aria-label="Buka menu">
                <x-svg-menu class="size-5" aria-hidden="true" />
            </button>

            <div class="min-w-0">
                {{ $breadcrumb ?? '' }}
            </div>

            <div class="ml-auto flex items-center gap-2">
                <!-- Pencarian -->
                <div class="hidden items-center gap-2 rounded-[var(--radius-control)] bg-sheet px-3 py-2 ring-1 ring-inset ring-rule-strong transition focus-within:ring-2 focus-within:ring-primary md:flex">
                    <x-svg-magnifying-glass class="size-4 text-ink-faint" aria-hidden="true" />
                    <input type="search" placeholder="Cari data, menu, atau fitur…" class="w-44 bg-transparent text-sm text-ink placeholder:text-ink-faint focus:outline-none lg:w-56" aria-label="Pencarian global">
                </div>

                <!-- Notifikasi -->
                <div class="relative">
                    <button type="button" @click="notifOpen = !notifOpen; userOpen = false"
                        class="relative rounded-md p-2 text-ink-soft transition hover:bg-paper-deep hover:text-ink" aria-label="Notifikasi">
                        <x-svg-bell class="size-5" aria-hidden="true" />
                        <span class="pin-dot absolute right-1.5 top-1.5 size-2 bg-danger" aria-hidden="true"></span>
                    </button>
                    <div x-show="notifOpen" x-cloak @click.outside="notifOpen = false"
                        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute right-0 top-full z-40 mt-2 w-80 rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule">
                        <p class="border-b border-rule/70 px-4 py-3 text-sm font-bold text-ink">Notifikasi</p>
                        <ul class="max-h-80 divide-y divide-rule/60 overflow-y-auto">
                            <li class="flex items-start gap-3 px-4 py-3">
                                <span class="pin-dot mt-1 size-2 shrink-0 bg-warning" aria-hidden="true"></span>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold text-ink">3 tagihan SPP belum diverifikasi</p>
                                    <p class="mt-0.5 text-xs text-ink-faint">5 menit lalu</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 px-4 py-3">
                                <span class="pin-dot mt-1 size-2 shrink-0 bg-info" aria-hidden="true"></span>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold text-ink">Berita baru menunggu persetujuan</p>
                                    <p class="mt-0.5 text-xs text-ink-faint">1 jam lalu</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-3 px-4 py-3">
                                <span class="pin-dot mt-1 size-2 shrink-0 bg-success" aria-hidden="true"></span>
                                <div class="min-w-0">
                                    <p class="text-[13px] font-semibold text-ink">Backup database berhasil (02.00 WIB)</p>
                                    <p class="mt-0.5 text-xs text-ink-faint">6 jam lalu</p>
                                </div>
                            </li>
                        </ul>
                        <a href="#" class="block border-t border-rule/70 px-4 py-2.5 text-center text-xs font-bold text-primary transition hover:bg-primary-soft">Lihat semua</a>
                    </div>
                </div>

                <!-- Pengguna -->
                <div class="relative">
                    <button type="button" @click="userOpen = !userOpen; notifOpen = false"
                        class="flex items-center gap-2 rounded-[var(--radius-control)] px-2 py-1.5 transition hover:bg-paper-deep">
                        <span class="flex size-8 items-center justify-center rounded-full bg-primary text-xs font-extrabold text-white">{{ $initials }}</span>
                        <span class="hidden text-left sm:block">
                            <span class="block text-[13px] font-bold leading-tight text-ink">{{ $user?->name ?? "Admin Madrasah" }}</span>
                            <span class="block text-[11px] text-ink-faint">{{ $roleLabel }}</span>
                        </span>
                        <x-svg-chevron-down class="hidden size-4 text-ink-faint sm:block" aria-hidden="true" />
                    </button>
                    <div x-show="userOpen" x-cloak @click.outside="userOpen = false"
                        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute right-0 top-full z-40 mt-2 w-56 rounded-sheet bg-sheet py-1.5 shadow-sheet-raised ring-1 ring-inset ring-rule">
                        <a href="#" class="flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-ink-soft transition hover:bg-paper-deep hover:text-ink">
                            <x-svg-user class="size-4" aria-hidden="true" /> Profil saya
                        </a>
                        <a href="#" class="flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-ink-soft transition hover:bg-paper-deep hover:text-ink">
                            <x-svg-cog-6-tooth class="size-4" aria-hidden="true" /> Pengaturan akun
                        </a>
                        <div class="my-1.5 border-t border-rule/70"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-[13px] font-medium text-danger transition hover:bg-danger-soft">
                                <x-svg-arrow-right-on-rectangle class="size-4" aria-hidden="true" /> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Konten (kertas) -->
        <main class="paper-grain flex-1 overflow-x-clip px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            {{ $slot }}
        </main>

        <footer class="flex flex-col items-center justify-between gap-2 border-t border-rule-strong/60 px-4 py-4 text-center text-xs text-ink-faint sm:flex-row sm:px-6 sm:text-left">
            <p>© 2026 {{ $madrasah }} · SIM Madrasah v2.1 · Backup terakhir 02.00 WIB</p>
            <x-ui.badge variant="neutral" :dot="false">Data demo — bukan data riil</x-ui.badge>
        </footer>
    </div>
</div>
