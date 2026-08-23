<div x-data x-cloak
    class="pointer-events-none fixed bottom-4 right-4 z-[60] flex w-full max-w-sm flex-col gap-2"
    x-show="$store.toasts.items.length">
    <template x-for="t in $store.toasts.items" :key="t.id">
        <div x-show="t.show ?? true"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0 scale-[0.98]"
            class="pointer-events-auto flex items-start gap-3 rounded-sheet bg-board text-board-ink px-4 py-3 shadow-sheet-raised"
            role="status">
            <span class="pin-dot mt-1.5 size-2 shrink-0 bg-success" aria-hidden="true"></span>
            <p class="min-w-0 flex-1 text-sm leading-snug" x-text="t.message"></p>
            <button type="button" class="shrink-0 opacity-70 transition hover:opacity-100" @click="$store.toasts.remove(t.id)" aria-label="Tutup">
                <x-svg-x-mark class="size-4" aria-hidden="true" />
            </button>
        </div>
    </template>
</div>
