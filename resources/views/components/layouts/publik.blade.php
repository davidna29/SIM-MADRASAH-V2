@props(['title' => null])

<x-layouts.root :title="$title">
    <div class="flex min-h-screen flex-col bg-paper">
        <header class="board-face sticky top-0 z-30">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
                <a href="{{ route('publik.berita.index') }}" class="flex items-center gap-2 text-board-ink">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-[var(--radius-control)] bg-board-ink text-board">
                        <x-svg-academic-cap class="size-5" aria-hidden="true" />
                    </span>
                    <span class="text-sm font-extrabold tracking-tight">MTs Al-Ikhlas Mulia</span>
                </a>
                <nav class="flex items-center gap-4 text-[13px] font-semibold text-board-ink/80">
                    <a href="{{ route('publik.berita.index') }}" class="transition hover:text-board-ink">Berita</a>
                    <a href="{{ route('publik.agenda.index') }}" class="transition hover:text-board-ink">Agenda</a>
                    <a href="{{ route('publik.galeri.index') }}" class="transition hover:text-board-ink">Galeri</a>
                    <a href="{{ route('ppdb.form') }}" class="transition hover:text-board-ink">PPDB</a>
                    <a href="{{ route('pindahan.form') }}" class="transition hover:text-board-ink">Pindahan</a>
                    <a href="{{ route('login') }}" class="rounded-[var(--radius-control)] bg-board-ink px-3 py-1.5 text-board transition hover:opacity-90">Masuk</a>
                </nav>
            </div>
        </header>

        <main class="flex-1 px-4 py-8 sm:px-6 lg:py-10">
            <div class="mx-auto max-w-6xl">
                {{ $slot }}
            </div>
        </main>

        <footer class="border-t border-rule-strong/60 bg-sheet py-5 text-center text-xs text-ink-faint">
            © {{ date('Y') }} {{ \App\Models\Setting::get('madrasah_name', 'SIM Madrasah') }} · SIM Madrasah
        </footer>
    </div>
</x-layouts.root>
