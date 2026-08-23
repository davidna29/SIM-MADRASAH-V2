@props([
    'activeRoute' => null,
    'role' => 'super_admin',
])

@php
    $groups = config('navigation');
    $active = $activeRoute ?? request()->route()?->getName();
@endphp<nav aria-label="Navigasi utama" class="flex-1 overflow-y-auto px-3 py-4">
    <ul class="space-y-5">
        @foreach ($groups as $group)
            <li>
                <p class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-[0.12em] text-board-ink/60">{{ $group['label'] }}</p>
                <ul class="space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @if (!in_array('*', $item['roles']) && !in_array($role, $item['roles']))
                            @continue
                        @endif
                        @php
                            $isActive = $active === $item['route'];
                            $href = route($item['route']);
                        @endphp
                        <li>
                            <a href="{{ $href }}"
                                @class([
                                    'group/nav relative flex items-center gap-2.5 rounded-[var(--radius-control)] px-2.5 py-2 text-[13px] font-semibold transition duration-150 ease-out',
                                    'bg-board-soft/40 text-board-ink ring-1 ring-inset ring-board-soft/60 shadow-sm' => $isActive,
                                    'text-board-ink/70 hover:bg-board-soft/30 hover:text-board-ink' => !$isActive,
                                ])
                                aria-current="{{ $isActive ? 'page' : 'false' }}">
                                <x-dynamic-component :component="'svg-' . $item['icon']" class="size-[18px] shrink-0" aria-hidden="true" />
                                <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                                @if ($isActive)
                                    <span class="pin-dot size-1.5 shrink-0 bg-board-ink" aria-hidden="true"></span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</nav>
