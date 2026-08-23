@props([
    'error' => null,
    'options' => [],
])

<select {{ $attributes->merge(['class' => 'w-full appearance-none rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition duration-150 hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary ' . ($error ? 'ring-danger/60 focus:ring-danger' : '')]) }}>
    <option value="">{{ $attributes->get('placeholder', 'Pilih…') }}</option>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>
