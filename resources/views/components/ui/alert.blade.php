@props([
    'variant' => 'info',
    'dismissible' => false,
])

@php
    $variants = [
        'info' => ['bg-info-soft text-info ring-info/30', 'svg-information-circle', 'information-circle'],
        'success' => ['bg-success-soft text-success ring-success/30', 'svg-check-circle', 'check-circle'],
        'warning' => ['bg-warning-soft text-warning ring-warning/30', 'svg-exclamation-triangle', 'exclamation-triangle'],
        'danger' => ['bg-danger-soft text-danger ring-danger/30', 'svg-x-circle', 'x-circle'],
    ];
    [$badge, $icon, $iconName] = $variants[$variant] ?? $variants['info'];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-out duration-200" x-transition:leave-end="opacity-0 -translate-y-1"
    {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-[var(--radius-control)] px-4 py-3 text-sm ring-1 ring-inset {$badge}", 'role' => 'status']) }}>
    <x-dynamic-component :component="$icon" class="mt-0.5 size-5 shrink-0" aria-hidden="true" />
    <div class="min-w-0 flex-1 text-[13px] leading-relaxed">{{ $slot }}</div>
    @if ($dismissible)
        <button type="button" @click="show = false" class="shrink-0 opacity-60 transition hover:opacity-100" aria-label="Tutup pemberitahuan">
            <x-svg-x-mark class="size-4" aria-hidden="true" />
        </button>
    @endif
</div>
