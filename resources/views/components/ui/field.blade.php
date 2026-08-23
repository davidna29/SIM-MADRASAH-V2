@props([
    'label' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <label class="block text-xs font-bold text-ink">
            {{ $label }}
            @if ($required)
                <span class="text-danger" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if ($error)
        <p class="flex items-center gap-1 text-xs font-medium text-danger">
            <x-svg-exclamation-circle class="size-3.5" aria-hidden="true" />
            {{ $error }}
        </p>
    @elseif ($hint)
        <p class="text-xs text-ink-faint">{{ $hint }}</p>
    @endif
</div>
