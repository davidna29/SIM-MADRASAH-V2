@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'pinned' => false,
    'padding' => true,
    'ruled' => false,
    'class' => null,
])

<section {{ $attributes->merge(['class' => "bg-sheet rounded-sheet shadow-sheet ring-1 ring-inset ring-rule/60 relative {$class}"]) }}>
    @if ($title || $actions || $subtitle)
        <header class="flex flex-wrap items-start justify-between gap-3 border-b border-rule/70 px-5 py-4 sm:px-6">
            <div class="pr-4">
                @if ($title)
                    <h2 class="flex items-center gap-2 text-sm font-bold tracking-tight text-ink">
                        @if ($pinned)
                            <span class="pin-dot size-2 bg-primary" aria-hidden="true"></span>
                        @endif
                        {{ $title }}
                    </h2>
                @endif
                @if ($subtitle)
                    <p class="mt-1 text-xs text-ink-soft">{{ $subtitle }}</p>
                @endif
            </div>
            @if ($actions)
                <div class="flex shrink-0 items-center gap-2">{!! $actions !!}</div>
            @endif
        </header>
    @endif
    <div @class(['px-5 py-5 sm:px-6' => $padding, 'ruled-paper' => $ruled])>
        {{ $slot }}
    </div></section>
