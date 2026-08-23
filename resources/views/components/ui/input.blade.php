@props([
    'type' => 'text',
    'error' => null,
    'prefix' => null,
])

@php
    $name = $attributes->get('name');
    $classes = 'w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary ' .
        (($error || ($name && $errors->has($name))) ? 'ring-danger/60 focus:ring-danger' : 'ring-rule-strong hover:ring-ink-faint/60 focus:ring-primary');
@endphp

<div class="relative">
    @if ($prefix)
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm font-medium text-ink-faint">{{ $prefix }}</span>
        <input type="{{ $type }}" {{ $attributes->merge(['class' => $classes . ' pl-10']) }} @if ($name && $errors->has($name)) aria-invalid="true" @endif />
    @else
        <input type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if ($name && $errors->has($name)) aria-invalid="true" @endif />
    @endif
</div>
