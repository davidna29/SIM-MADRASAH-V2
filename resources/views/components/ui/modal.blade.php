@props([
    'id' => null,
    'title' => null,
    'confirmText' => 'Simpan',
    'variant' => 'primary',
])

<div x-data="{ open: false, submitting: false }"
    x-cloak
    @keydown.escape.window="open = false">
    @if (isset($trigger))
        <span @click="open = true">{{ $trigger }}</span>
    @endif

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
        @click.self="open = false" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]"></div>

        <div class="relative w-full max-w-lg rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0 translate-y-3 scale-[0.98]">
            @if ($title)
                <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                    <h3 id="{{ $id }}-title" class="text-sm font-bold tracking-tight text-ink">{{ $title }}</h3>
                    <button type="button" @click="open = false" class="rounded-md p-1 text-ink-faint transition hover:bg-paper-deep hover:text-ink" aria-label="Tutup">
                        <x-svg-x-mark class="size-5" aria-hidden="true" />
                    </button>
                </header>
            @endif

            <div class="px-5 py-5">
                {{ $slot }}
            </div>

            @if (isset($footer))
                <footer class="flex items-center justify-end gap-2 border-t border-rule/70 px-5 py-4">
                    {{ $footer }}
                </footer>
            @endif
        </div>
    </div>
</div>
