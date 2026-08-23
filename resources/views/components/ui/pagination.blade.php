@props([
    'current' => 1,
    'last' => 1,
    'baseUrl' => null,
])

@php
    $base = $baseUrl ?? url()->current();
    $preserve = request()->except('page');
    $pageUrl = function (int $page) use ($base, $preserve) {
        $query = array_merge($preserve, ['page' => $page]);

        return $base.'?'.http_build_query($query);
    };
@endphp

<nav class="flex flex-wrap items-center justify-between gap-3" aria-label="Paginasi">
    <p class="text-xs text-ink-soft">
        Halaman <span class="font-semibold text-ink">{{ $current }}</span> dari
        <span class="font-semibold text-ink">{{ $last }}</span>
    </p>
    <div class="flex items-center gap-1">
        <a href="{{ $pageUrl($current - 1) }}"
            class="inline-flex h-8 items-center gap-1 rounded-[var(--radius-control)] px-2.5 text-xs font-semibold text-ink-soft ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep hover:text-ink {{ $current <= 1 ? 'pointer-events-none opacity-50' : '' }}"
            @if ($current <= 1) aria-disabled="true" tabindex="-1" @endif>
            <x-svg-arrow-left class="size-3.5" aria-hidden="true" />
            <span class="hidden sm:inline">Sebelumnya</span>
        </a>
        @for ($i = max(1, $current - 2); $i <= min($last, $current + 2); $i++)
            <a href="{{ $pageUrl($i) }}" @if ($i === $current) aria-current="page" @endif
                class="tabular inline-flex h-8 w-8 items-center justify-center rounded-[var(--radius-control)] text-xs font-semibold transition {{ $i === $current ? 'bg-primary text-white shadow-sm' : 'text-ink-soft ring-1 ring-inset ring-rule-strong hover:bg-paper-deep hover:text-ink' }}">
                {{ $i }}
            </a>
        @endfor
        <a href="{{ $pageUrl($current + 1) }}"
            class="inline-flex h-8 items-center gap-1 rounded-[var(--radius-control)] px-2.5 text-xs font-semibold text-ink-soft ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep hover:text-ink {{ $current >= $last ? 'pointer-events-none opacity-50' : '' }}"
            @if ($current >= $last) aria-disabled="true" tabindex="-1" @endif>
            <span class="hidden sm:inline">Berikutnya</span>
            <x-svg-arrow-right class="size-3.5" aria-hidden="true" />
        </a>
    </div>
</nav>
