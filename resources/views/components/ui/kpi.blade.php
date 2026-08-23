@props([
    'label' => null,
    'value' => null,
    'prefix' => null,
    'suffix' => null,
    'trend' => null,
    'trendUp' => true,
    'hint' => null,
    'icon' => null,
])

@php
    $trendColor = $trendUp ? 'text-success' : 'text-danger';
@endphp

<article {{ $attributes->merge(['class' => 'group relative bg-sheet rounded-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-4 overflow-hidden ruled-paper']) }}>
    <span class="pin-dot absolute right-3 top-3 size-2 bg-primary/70 transition group-hover:scale-125" aria-hidden="true"></span>

    <div class="flex items-center gap-2 text-xs font-semibold text-ink-soft">
        @if ($icon)
            <x-dynamic-component :component="'svg-' . $icon" class="size-4 text-ink-faint" aria-hidden="true" />
        @endif
        {{ $label }}
    </div>

    <div class="mt-2 flex items-baseline gap-1.5">
        @if ($prefix)
            <span class="text-sm font-semibold text-ink-soft">{{ $prefix }}</span>
        @endif
        <span class="tabular font-mono text-3xl font-bold leading-none tracking-tight text-ink">
            {{ $value }}
        </span>
        @if ($suffix)
            <span class="text-sm font-semibold text-ink-soft">{{ $suffix }}</span>
        @endif
    </div>

    @if ($trend || $hint)
        <div class="mt-2 flex items-center gap-1.5 text-xs">
            @if ($trend)
                <span class="inline-flex items-center gap-0.5 font-semibold {{ $trendColor }}">
                    <x-dynamic-component :component="'svg-' . ($trendUp ? 'arrow-up-right' : 'arrow-down-right')" class="size-3.5" aria-hidden="true" />
                    {{ $trend }}
                </span>
            @endif
            @if ($hint)
                <span class="text-ink-faint">{{ $hint }}</span>
            @endif
        </div>
    @endif
</article>
