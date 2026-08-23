@props([
    'items' => [],
])

<nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-1.5 text-xs text-ink-faint">
    @foreach ($items as $i => $item)
        @if ($i > 0)
            <x-svg-chevron-right class="size-3 shrink-0" aria-hidden="true" />
        @endif
        @if (isset($item['href']) && !$loop->last)
            <a href="{{ $item['href'] }}" class="font-medium transition hover:text-primary">{{ $item['label'] }}</a>
        @else
            <span class="font-semibold text-ink" aria-current="page">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
