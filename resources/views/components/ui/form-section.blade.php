@props([
    'title' => null,
    'description' => null,
    'aside' => null,
])

<section {{ $attributes->merge(['class' => 'bg-sheet rounded-sheet shadow-sheet ring-1 ring-inset ring-rule/60']) }}>
    @if ($title)
        <header class="border-b border-rule/70 px-5 py-4 sm:px-6">
            <h3 class="text-sm font-bold tracking-tight text-ink">{{ $title }}</h3>
            @if ($description)
                <p class="mt-1 max-w-prose text-xs leading-relaxed text-ink-soft">{{ $description }}</p>
            @endif
        </header>
    @endif
    <div class="px-5 py-5 sm:px-6 ruled-paper">
        {{ $slot }}
    </div>
</section>
