@props([
    'error' => null,
    'options' => [],
    'full' => true,
    'selected' => null,
])

<select {{ $attributes->merge(['class' => ($full ? 'w-full' : '') . ' appearance-none rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition duration-150 hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary ' . ($error ? 'ring-danger/60 focus:ring-danger' : '')]) }}>
    @if ($attributes->get('placeholder'))
        <option value="" @selected($selected === null || $selected === '')>{{ $attributes->get('placeholder') }}</option>
    @endif
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $label }}</option>
    @endforeach
</select>
