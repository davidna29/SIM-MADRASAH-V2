@props([
    'variant' => 'neutral',
    'dot' => true,
    'icon' => null,
])

@php
    $variants = [
        'neutral' => ['bg-paper-deep text-ink-soft ring-rule-strong', 'bg-ink-faint'],
        'success' => ['bg-success-soft text-success ring-success/30', 'bg-success'],
        'warning' => ['bg-warning-soft text-warning ring-warning/30', 'bg-warning'],
        'danger' => ['bg-danger-soft text-danger ring-danger/30', 'bg-danger'],
        'info' => ['bg-info-soft text-info ring-info/30', 'bg-info'],
        'primary' => ['bg-primary-soft text-primary-strong ring-primary/30', 'bg-primary'],
    ];
    [$badge, $dotColor] = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {$badge}"]) }}>
    @if ($dot)
        <span class="pin-dot size-1.5 shrink-0 {{ $dotColor }}" aria-hidden="true"></span>
    @endif
    @if ($icon)
        <x-dynamic-component :component="'svg-' . $icon" class="size-3.5 shrink-0" aria-hidden="true" />
    @endif
    {{ $slot }}
</span>
