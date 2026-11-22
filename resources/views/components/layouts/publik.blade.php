@props(['title' => null, 'description' => null, 'image' => null, 'canonical' => null])

@php
    $schoolName = $siteSettings->get('madrasah_name') ?: config('app.name', 'Madrasah');
    $address = collect([
        $siteSettings->get('madrasah_jalan'),
        $siteSettings->get('madrasah_desa'),
        $siteSettings->get('madrasah_kecamatan'),
        $siteSettings->get('madrasah_kabupaten'),
        $siteSettings->get('madrasah_provinsi'),
    ])->filter()->implode(', ');
@endphp

<x-layouts.root
    :title="$title"
    :description="$description"
    :image="$image"
    :site-name="$schoolName"
    :canonical="$canonical ?? url()->current()">
    <div class="flex min-h-screen flex-col bg-paper" x-data="{ mobileOpen: false }">
        <a href="#konten" class="sr-only z-50 rounded-control bg-sheet px-4 py-3 font-semibold text-ink focus:not-sr-only focus:fixed focus:left-4 focus:top-4">
            Lewati ke konten
        </a>

        <header class="board-face sticky top-0 z-30 text-board-ink shadow-sheet">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('publik.beranda') }}" class="flex min-w-0 items-center gap-3 rounded-control focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-board-ink">
                    @if ($siteLogoUrl)
                        <img src="{{ $siteLogoUrl }}" alt="Logo {{ $schoolName }}" class="size-11 shrink-0 rounded-control bg-white object-contain p-1.5">
                    @else
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-control bg-board-ink text-board">
                            <x-svg-academic-cap class="size-6" aria-hidden="true" />
                        </span>
                    @endif
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-extrabold tracking-tight sm:text-base">{{ $schoolName }}</span>
                        <span class="hidden truncate text-[11px] text-board-ink/75 sm:block">{{ $siteSettings->get('madrasah_jenjang') ?: 'Madrasah' }} · Informasi resmi</span>
                    </span>
                </a>

                <nav aria-label="Navigasi utama" class="hidden items-center gap-1 lg:flex">
                    <a href="{{ route('publik.beranda') }}" @class(['rounded-control px-3 py-2 text-sm font-semibold transition hover:bg-board-soft/50 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-board-ink', 'bg-board-soft/60 text-white' => request()->routeIs('publik.beranda'), 'text-board-ink/85' => ! request()->routeIs('publik.beranda')])>Beranda</a>
                    <details class="group relative">
                        <summary class="flex cursor-pointer list-none items-center gap-1 rounded-control px-3 py-2 text-sm font-semibold text-board-ink/85 transition hover:bg-board-soft/50 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-board-ink">
                            Profil <x-svg-chevron-down class="size-4 transition group-open:rotate-180" aria-hidden="true" />
                        </summary>
                        <div class="absolute left-0 top-full z-40 mt-2 w-64 rounded-sheet bg-sheet p-2 text-ink shadow-sheet-raised ring-1 ring-rule/70">
                            <a href="{{ route('publik.profil') }}" class="block rounded-control px-3 py-2 text-sm font-semibold hover:bg-paper">Profil Madrasah</a>
                            <a href="{{ route('publik.sambutan') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Sambutan Kepala Madrasah</a>
                            <a href="{{ route('publik.sejarah') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Sejarah</a>
                            <a href="{{ route('publik.visi-misi') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Visi & Misi</a>
                            <a href="{{ route('publik.struktur') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Struktur Organisasi</a>
                            <a href="{{ route('publik.guru') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Guru & Tenaga Kependidikan</a>
                            <a href="{{ route('publik.sarpras') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Sarana & Prasarana</a>
                        </div>
                    </details>
                    <details class="group relative">
                        <summary class="flex cursor-pointer list-none items-center gap-1 rounded-control px-3 py-2 text-sm font-semibold text-board-ink/85 transition hover:bg-board-soft/50 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-board-ink">
                            Berita & Kegiatan <x-svg-chevron-down class="size-4 transition group-open:rotate-180" aria-hidden="true" />
                        </summary>
                        <div class="absolute left-0 top-full z-40 mt-2 w-60 rounded-sheet bg-sheet p-2 text-ink shadow-sheet-raised ring-1 ring-rule/70">
                            <a href="{{ route('publik.berita.index') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Berita</a>
                            <a href="{{ route('publik.agenda.index') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Agenda & Pengumuman</a>
                            <a href="{{ route('publik.galeri.index') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Galeri</a>
                            <a href="{{ route('publik.video') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Video</a>
                        </div>
                    </details>
                    <details class="group relative">
                        <summary class="flex cursor-pointer list-none items-center gap-1 rounded-control px-3 py-2 text-sm font-semibold text-board-ink/85 transition hover:bg-board-soft/50 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-board-ink">
                            Penerimaan <x-svg-chevron-down class="size-4 transition group-open:rotate-180" aria-hidden="true" />
                        </summary>
                        <div class="absolute left-0 top-full z-40 mt-2 w-52 rounded-sheet bg-sheet p-2 text-ink shadow-sheet-raised ring-1 ring-rule/70">
                            <a href="{{ route('ppdb.form') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">PPDB</a>
                            <a href="{{ route('pindahan.form') }}" class="block rounded-control px-3 py-2 text-sm hover:bg-paper">Siswa Pindahan</a>
                        </div>
                    </details>
                    <a href="{{ route('publik.kontak') }}" @class(['rounded-control px-3 py-2 text-sm font-semibold transition hover:bg-board-soft/50 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-board-ink', 'bg-board-soft/60 text-white' => request()->routeIs('publik.kontak'), 'text-board-ink/85' => ! request()->routeIs('publik.kontak')])>Kontak</a>
                    <a href="{{ route('login') }}" class="ml-2 inline-flex min-h-10 items-center justify-center rounded-control bg-board-ink px-4 text-sm font-bold text-board transition hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-board-ink">Masuk</a>
                </nav>

                <button type="button" class="inline-flex min-h-11 items-center gap-2 rounded-control px-3 text-sm font-bold text-board-ink ring-1 ring-inset ring-board-ink/30 transition hover:bg-board-soft/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-board-ink lg:hidden"
                    aria-controls="publik-mobile-nav" :aria-expanded="mobileOpen.toString()" @click="mobileOpen = !mobileOpen">
                    <x-svg-bars-3 class="size-5" aria-hidden="true" />
                    <span>Menu</span>
                </button>
            </div>

            <nav id="publik-mobile-nav" aria-label="Navigasi utama" x-cloak x-show="mobileOpen" x-transition @keydown.escape.window="mobileOpen = false" class="border-t border-board-ink/15 px-4 pb-4 pt-2 lg:hidden">
                <div class="mx-auto grid max-w-7xl gap-1 sm:px-2">
                    <a @click="mobileOpen = false" href="{{ route('publik.beranda') }}" class="rounded-control px-3 py-3 text-sm font-semibold hover:bg-board-soft/40">Beranda</a>
                    <details class="rounded-control px-3 py-2 open:bg-board-soft/25">
                        <summary class="cursor-pointer py-1 text-sm font-bold">Profil Madrasah</summary>
                        <div class="grid gap-1 pb-2 pt-2 pl-3">
                            <a @click="mobileOpen = false" href="{{ route('publik.profil') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Profil</a>
                            <a @click="mobileOpen = false" href="{{ route('publik.sambutan') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Sambutan Kepala Madrasah</a>
                            <a @click="mobileOpen = false" href="{{ route('publik.sejarah') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Sejarah</a>
                            <a @click="mobileOpen = false" href="{{ route('publik.visi-misi') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Visi & Misi</a>
                            <a @click="mobileOpen = false" href="{{ route('publik.struktur') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Struktur Organisasi</a>
                            <a @click="mobileOpen = false" href="{{ route('publik.guru') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Guru & Tenaga Kependidikan</a>
                            <a @click="mobileOpen = false" href="{{ route('publik.sarpras') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Sarana & Prasarana</a>
                        </div>
                    </details>
                    <details class="rounded-control px-3 py-2 open:bg-board-soft/25">
                        <summary class="cursor-pointer py-1 text-sm font-bold">Berita & Kegiatan</summary>
                        <div class="grid gap-1 pb-2 pt-2 pl-3">
                            <a @click="mobileOpen = false" href="{{ route('publik.berita.index') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Berita</a>
                            <a @click="mobileOpen = false" href="{{ route('publik.agenda.index') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Agenda & Pengumuman</a>
                            <a @click="mobileOpen = false" href="{{ route('publik.galeri.index') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Galeri</a>
                            <a @click="mobileOpen = false" href="{{ route('publik.video') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Video</a>
                        </div>
                    </details>
                    <details class="rounded-control px-3 py-2 open:bg-board-soft/25">
                        <summary class="cursor-pointer py-1 text-sm font-bold">Penerimaan</summary>
                        <div class="grid gap-1 pb-2 pt-2 pl-3">
                            <a @click="mobileOpen = false" href="{{ route('ppdb.form') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">PPDB</a>
                            <a @click="mobileOpen = false" href="{{ route('pindahan.form') }}" class="rounded-control px-3 py-2 text-sm hover:bg-board-soft/40">Siswa Pindahan</a>
                        </div>
                    </details>
                    <a @click="mobileOpen = false" href="{{ route('publik.kontak') }}" class="rounded-control px-3 py-3 text-sm font-semibold hover:bg-board-soft/40">Kontak</a>
                    <a href="{{ route('login') }}" class="mt-2 inline-flex min-h-11 items-center justify-center rounded-control bg-board-ink px-4 text-sm font-bold text-board">Masuk ke SIM Madrasah</a>
                </div>
            </nav>
        </header>

        <main id="konten" class="flex-1 px-4 py-7 sm:px-6 sm:py-9 lg:px-8 lg:py-12">
            <div class="mx-auto max-w-7xl">
                {{ $slot }}
            </div>
        </main>

        <footer class="border-t border-rule-strong/70 bg-sheet">
            <div class="mx-auto grid max-w-7xl gap-9 px-4 py-9 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8 lg:py-12">
                <div class="sm:col-span-2">
                    <a href="{{ route('publik.beranda') }}" class="inline-flex items-center gap-3 text-ink">
                        @if ($siteLogoUrl)
                            <img src="{{ $siteLogoUrl }}" alt="" class="size-10 rounded-control object-contain">
                        @else
                            <span class="flex size-10 items-center justify-center rounded-control bg-board text-board-ink"><x-svg-academic-cap class="size-5" aria-hidden="true" /></span>
                        @endif
                        <span class="font-extrabold tracking-tight">{{ $schoolName }}</span>
                    </a>
                    @if ($address !== '')
                        <p class="mt-4 max-w-md text-sm leading-relaxed text-ink-soft">{{ $address }}</p>
                    @endif
                    @if ($siteSettings->get('madrasah_tagline'))
                        <p class="mt-2 max-w-md text-sm leading-relaxed text-ink-faint">{{ $siteSettings->get('madrasah_tagline') }}</p>
                    @endif
                </div>

                <div>
                    <h2 class="text-sm font-extrabold text-ink">Jelajahi</h2>
                    <ul class="mt-3 space-y-2 text-sm text-ink-soft">
                        <li><a href="{{ route('publik.profil') }}" class="hover:text-primary-strong">Profil Madrasah</a></li>
                        <li><a href="{{ route('publik.berita.index') }}" class="hover:text-primary-strong">Berita</a></li>
                        <li><a href="{{ route('publik.agenda.index') }}" class="hover:text-primary-strong">Agenda</a></li>
                        <li><a href="{{ route('publik.galeri.index') }}" class="hover:text-primary-strong">Galeri</a></li>
                        <li><a href="{{ route('publik.privasi') }}" class="hover:text-primary-strong">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-sm font-extrabold text-ink">Hubungi Madrasah</h2>
                    <ul class="mt-3 space-y-2 text-sm text-ink-soft">
                        @if ($siteSettings->get('madrasah_phone'))
                            <li><a href="tel:{{ preg_replace('/\D+/', '', $siteSettings->get('madrasah_phone')) }}" class="hover:text-primary-strong">Telepon: {{ $siteSettings->get('madrasah_phone') }}</a></li>
                        @endif
                        @if ($siteSettings->get('madrasah_email'))
                            <li><a href="mailto:{{ $siteSettings->get('madrasah_email') }}" class="break-all hover:text-primary-strong">{{ $siteSettings->get('madrasah_email') }}</a></li>
                        @endif
                        @if ($siteSettings->get('madrasah_whatsapp'))
                            <li><a href="https://wa.me/{{ preg_replace('/\D+/', '', $siteSettings->get('madrasah_whatsapp')) }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary-strong">WhatsApp madrasah</a></li>
                        @endif
                        <li><a href="{{ route('publik.kontak') }}" class="font-semibold text-primary-strong hover:underline">Lihat kontak & lokasi</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-rule/70 px-4 py-4 text-center text-xs text-ink-faint sm:px-6">
                © {{ date('Y') }} {{ $schoolName }} · Informasi resmi madrasah
            </div>
        </footer>
    </div>
</x-layouts.root>
