{{-- Tombol toggle terang/gelap --}}
<button
    type="button"
    @click="$store.theme.toggle()"
    x-data
    :aria-label="$store.theme.mode === 'dark' ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
    :title="$store.theme.mode === 'dark' ? 'Mode terang' : 'Mode gelap'"
    class="rounded-md p-2 text-ink-soft transition duration-150 ease-out hover:bg-paper-deep hover:text-ink">
    <x-svg-sun x-show="$store.theme.mode === 'dark'" class="size-5" aria-hidden="true" x-cloak />
    <x-svg-moon x-show="$store.theme.mode !== 'dark'" class="size-5" aria-hidden="true" x-cloak />
</button>
