@props([
    'activeRoute' => null,
    'role' => 'super_admin',
])

@php
    $groups = config('navigation');
    $active = $activeRoute ?? request()->route()?->getName();

    $roleCanReach = function (string $routeName) use ($role): bool {
        if ($role === 'super_admin') {
            return \Illuminate\Support\Facades\Route::has($routeName);
        }
        if (! \Illuminate\Support\Facades\Route::has($routeName)) {
            return false;
        }
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName($routeName);
        $middleware = collect($route?->gatherMiddleware() ?? []);
        $roleRestrictions = $middleware->filter(fn ($m) => is_string($m) && str_starts_with($m, 'role:'))
            ->flatMap(fn ($m) => explode(':', $m)[1] ? explode('|', explode(':', $m)[1]) : []);
        if ($roleRestrictions->isEmpty()) {
            return true;
        }
        return $roleRestrictions->contains($role);
    };
@endphp<nav aria-label="Navigasi utama" class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-4">
    <ul class="space-y-5">
        @foreach ($groups as $group)
            @php
                $visibleItems = collect($group['items'])->filter(function ($item) use ($role, $roleCanReach) {
                    if (isset($item['children'])) {
                        if (!in_array('*', $item['roles'] ?? []) && !in_array($role, $item['roles'] ?? [])) {
                            return false;
                        }
                        return true; // parent dengan children selalu dipertahankan; children difilter di bawah
                    }
                    if (!in_array('*', $item['roles']) && !in_array($role, $item['roles'])) {
                        return false;
                    }
                    return $roleCanReach($item['route']);
                })->values();
            @endphp
            @if ($visibleItems->isEmpty())
                @continue
            @endif
            <li>
                <p x-cloak :class="collapsed ? 'lg:hidden' : 'lg:block'"
                    class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-[0.12em] text-board-ink/60">{{ $group['label'] }}</p>
                <ul class="space-y-0.5">
                    @foreach ($visibleItems as $item)
                        @if (isset($item['children']))
                            @php
                                $visibleChildren = collect($item['children'])->filter(function ($child) use ($role, $roleCanReach) {
                                    if (!in_array('*', $child['roles'] ?? []) && !in_array($role, $child['roles'] ?? [])) {
                                        return false;
                                    }
                                    return $roleCanReach($child['route']);
                                })->values();
                                $anyChildActive = $visibleChildren->contains(fn ($c) => $active === $c['route']);
                            @endphp
                            @if ($visibleChildren->isEmpty())
                                @continue
                            @endif
                            <li>
                                <p x-cloak :class="collapsed ? 'lg:hidden' : 'lg:block'"
                                    class="px-2.5 pb-1 pt-2 text-[10px] font-bold uppercase tracking-[0.12em] text-board-ink/60">{{ $item['label'] }}</p>
                                <ul class="space-y-0.5">
                                    @foreach ($visibleChildren as $child)
                                        @php
                                            $isActive = $active === $child['route'];
                                            $href = route($child['route']);
                                        @endphp
                                        <li>
                                            <a href="{{ $href }}"
                                                :title="collapsed ? '{{ $child['label'] }}' : ''"
                                                @class([
                                                    'group/nav relative flex items-center gap-2.5 rounded-[var(--radius-control)] px-2.5 py-2 pl-6 text-[13px] font-semibold transition duration-150 ease-out',
                                                    'bg-board-soft/40 text-board-ink ring-1 ring-inset ring-board-soft/60 shadow-sm' => $isActive,
                                                    'text-board-ink/70 hover:bg-board-soft/30 hover:text-board-ink' => !$isActive,
                                                ])
                                                :class="collapsed ? 'lg:justify-center lg:pl-0' : 'lg:justify-start lg:pl-6'"
                                                aria-current="{{ $isActive ? 'page' : 'false' }}">
                                                <x-dynamic-component :component="'svg-' . ($child['icon'] ?? 'arrow-small-right')" class="size-[16px] shrink-0" aria-hidden="true" />
                                                <span x-cloak :class="collapsed ? 'lg:hidden' : 'lg:inline'"
                                                    class="hidden min-w-0 flex-1 truncate">{{ $child['label'] }}</span>
                                                @if ($isActive)
                                                    <span x-cloak :class="collapsed ? 'lg:hidden' : 'lg:inline-block'"
                                                        class="hidden size-1.5 shrink-0 rounded-full bg-board-ink" aria-hidden="true"></span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                        @php
                            $isActive = $active === $item['route'];
                            $href = route($item['route']);
                        @endphp
                        <li>
                            <a href="{{ $href }}"
                                :title="collapsed ? '{{ $item['label'] }}' : ''"
                                @class([
                                    'group/nav relative flex items-center gap-2.5 rounded-[var(--radius-control)] px-2.5 py-2 text-[13px] font-semibold transition duration-150 ease-out',
                                    'bg-board-soft/40 text-board-ink ring-1 ring-inset ring-board-soft/60 shadow-sm' => $isActive,
                                    'text-board-ink/70 hover:bg-board-soft/30 hover:text-board-ink' => !$isActive,
                                ])
                                :class="collapsed ? 'lg:justify-center lg:px-0' : 'lg:justify-start lg:px-2.5'"
                                aria-current="{{ $isActive ? 'page' : 'false' }}">
                                <x-dynamic-component :component="'svg-' . $item['icon']" class="size-[18px] shrink-0" aria-hidden="true" />
                                <span x-cloak :class="collapsed ? 'lg:hidden' : 'lg:inline'"
                                    class="hidden min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                                @if ($isActive)
                                    <span x-cloak :class="collapsed ? 'lg:hidden' : 'lg:inline-block'"
                                        class="hidden size-1.5 shrink-0 rounded-full bg-board-ink" aria-hidden="true"></span>
                                @endif
                            </a>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</nav>
