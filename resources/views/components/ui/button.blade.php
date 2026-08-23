@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconRight' => null,
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
    $variants = [
        'primary' => 'bg-primary text-white shadow-sm hover:bg-primary-strong active:bg-primary-strong focus-visible:outline-primary',
        'secondary' => 'bg-sheet text-ink ring-1 ring-inset ring-rule-strong hover:bg-paper-deep active:bg-paper-deep',
        'outline' => 'bg-transparent text-primary ring-1 ring-inset ring-primary/40 hover:bg-primary-soft active:bg-primary-soft',
        'danger' => 'bg-danger text-white shadow-sm hover:brightness-95 active:brightness-90',
        'success' => 'bg-success text-white shadow-sm hover:brightness-95 active:brightness-90',
        'ghost' => 'bg-transparent text-ink-soft hover:bg-paper-deep hover:text-ink active:bg-paper-deep',
    ];
    $sizes = [
        'sm' => 'h-8 px-3 text-xs gap-1.5',
        'md' => 'h-10 px-4 text-sm gap-2',
        'lg' => 'h-12 px-5 text-sm gap-2.5',
    ];
    $class = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-[var(--radius-control)] font-semibold transition duration-150 ease-out active:scale-[0.98] {$sizeClass} {$class}"]) }}>
        @if ($icon)
            <x-dynamic-component :component="'svg-' . $icon" class="size-4 shrink-0" aria-hidden="true" />
        @endif
        {{ $slot }}
        @if ($iconRight)
            <x-dynamic-component :component="'svg-' . $iconRight" class="size-4 shrink-0" aria-hidden="true" />
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-[var(--radius-control)] font-semibold transition duration-150 ease-out active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50 {$sizeClass} {$class}"]) }}
        @disabled($disabled)>
        @if ($icon)
            <x-dynamic-component :component="'svg-' . $icon" class="size-4 shrink-0" aria-hidden="true" />
        @endif
        {{ $slot }}
        @if ($iconRight)
            <x-dynamic-component :component="'svg-' . $iconRight" class="size-4 shrink-0" aria-hidden="true" />
        @endif
    </button>
@endif
