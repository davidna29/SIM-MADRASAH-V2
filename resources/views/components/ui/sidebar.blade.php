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

    $roleIn = fn (array $roles): bool => in_array('*', $roles, true) || in_array($role, $roles, true);

    // Item/child yang ditampilkan: bukan placeholder, role cocok, rute terjangkau.
    $isVisibleLeaf = function (array $def) use ($roleIn, $roleCanReach): bool {
        if (! empty($def['placeholder'])) {
            return false;
        }
        if (! $roleIn($def['roles'] ?? [])) {
            return false;
        }
        if (! empty($def['external'])) {
            return true;
        }

        return $roleCanReach($def['route'] ?? '');
    };

    // Kecocokan rute aktif (mencakup routeParams, mis. surat.index?type=masuk).
    $isRouteMatch = function (array $def) use ($active): bool {
        if (($def['route'] ?? null) !== $active) {
            return false;
        }
        foreach ($def['routeParams'] ?? [] as $key => $value) {
            if ((string) (request()->query($key) ?? '') !== (string) $value) {
                return false;
            }
        }

        return true;
    };

    // Kelompokkan menu yang terlihat per role; lewati placeholder & grup kosong.
    $visibleGroups = [];
    foreach ($groups as $group) {
        $items = [];
        foreach ($group['items'] as $item) {
            if (isset($item['children'])) {
                if (! $roleIn($item['roles'] ?? [])) {
                    continue;
                }
                $children = array_values(array_filter($item['children'], $isVisibleLeaf));
                if ($children === []) {
                    continue;
                }
                $item['children'] = $children;
                $items[] = $item;
            } elseif ($isVisibleLeaf($item)) {
                $items[] = $item;
            }
        }
        if ($items === []) {
            continue;
        }
        $visibleGroups[] = [
            'label' => $group['label'],
            'icon' => $group['icon'] ?? 'folder',
            'items' => $items,
        ];
    }

    // Daftar rata (untuk pencarian) + slug grup/parent yang aktif.
    $slugFor = fn (string $label): string => \Illuminate\Support\Str::slug($label);
    $flatMenu = [];
    $activeSlugs = [];
    foreach ($visibleGroups as $group) {
        $groupSlug = $slugFor($group['label']);
        foreach ($group['items'] as $item) {
            if (isset($item['children'])) {
                $parentSlug = $slugFor($group['label'].'-'.$item['label']);
                foreach ($item['children'] as $child) {
                    $flatMenu[] = [
                        'label' => $child['label'],
                        'match' => trim($item['label'].' '.$child['label']),
                        'route' => $child['route'] ?? null,
                        'routeParams' => $child['routeParams'] ?? [],
                        'icon' => $child['icon'] ?? 'arrow-small-right',
                        'external' => $child['external'] ?? false,
                    ];
                    if ($isRouteMatch($child)) {
                        $activeSlugs[] = $groupSlug;
                        $activeSlugs[] = $parentSlug;
                    }
                }
            } else {
                $flatMenu[] = [
                    'label' => $item['label'],
                    'match' => trim($group['label'].' '.$item['label']),
                    'route' => $item['route'] ?? null,
                    'routeParams' => $item['routeParams'] ?? [],
                    'icon' => $item['icon'] ?? 'arrow-small-right',
                    'external' => $item['external'] ?? false,
                ];
                if ($isRouteMatch($item)) {
                    $activeSlugs[] = $groupSlug;
                }
            }
        }
    }
    $activeSlugMap = collect($activeSlugs)->unique()->mapWithKeys(fn ($slug) => [$slug => true])->all();
    $searchLabels = collect($flatMenu)->pluck('match')->values()->all();
@endphp

<nav aria-label="Navigasi utama" class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-4"
    x-data="{
        query: '',
        labels: @json($searchLabels),
        open: Object.assign(
            JSON.parse(localStorage.getItem('sim-nav-open') || '{}'),
            @json($activeSlugMap)
        ),
        matches(label) {
            const q = this.query.trim().toLowerCase();
            return q === '' || String(label).toLowerCase().includes(q);
        },
        anyMatch() {
            return this.labels.some(label => this.matches(label));
        },
        toggle(key) {
            this.open[key] = !this.open[key];
            localStorage.setItem('sim-nav-open', JSON.stringify(this.open));
        },
    }"
    x-init="
        const saved = parseInt(localStorage.getItem('sim-sidebar-scroll') || '0', 10);
        if (! Number.isNaN(saved)) { $el.scrollTop = saved; }
        $el.querySelector('[aria-current=&quot;page&quot;]')?.scrollIntoView({ block: 'nearest' });
    "
    @scroll.passive.throttle.150ms="localStorage.setItem('sim-sidebar-scroll', String(Math.round($el.scrollTop)))">

    {{-- Pencarian menu --}}
    <div x-cloak :class="collapsed ? 'lg:hidden' : ''" class="px-1 pb-3">
        <div class="flex items-center gap-2 rounded-[var(--radius-control)] bg-board-soft/40 px-2.5 py-2 ring-1 ring-inset ring-white/10 transition focus-within:ring-2 focus-within:ring-board-ink/40">
            <x-svg-magnifying-glass class="size-4 shrink-0 text-board-ink/50" aria-hidden="true" />
            <input x-model="query" type="search" placeholder="Cari menu…" aria-label="Cari menu"
                class="w-full min-w-0 bg-transparent text-[13px] text-board-ink placeholder:text-board-ink/40 focus:outline-none">
        </div>
    </div>

    {{-- Hasil pencarian (daftar rata) --}}
    <div x-show="query !== ''" x-cloak>
        <p class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-[0.12em] text-board-ink/60">Hasil pencarian</p>
        <ul class="space-y-0.5">
            @foreach ($flatMenu as $menu)
                @php
                    $href = $menu['route'] ? route($menu['route'], $menu['routeParams']) : '#';
                    $isExternal = (bool) $menu['external'];
                @endphp
                <li x-show="matches(@json($menu['match']))">
                    <a href="{{ $href }}"
                        @if ($isExternal)
                            rel="noopener" x-on:click.prevent="$dispatch('open-external-link', { url: '{{ $href }}', label: '{{ addslashes($menu['label']) }}' })"
                        @endif
                        class="flex items-center gap-2.5 rounded-[var(--radius-control)] px-2.5 py-2 text-[13px] font-semibold text-board-ink/70 transition duration-150 ease-out hover:bg-board-soft/30 hover:text-board-ink">
                        <x-dynamic-component :component="'svg-' . $menu['icon']" class="size-[16px] shrink-0" aria-hidden="true" />
                        <span class="min-w-0 flex-1 truncate">{{ $menu['label'] }}</span>
                    </a>
                </li>
            @endforeach
            <li x-show="query !== '' && !anyMatch()" class="px-2.5 py-2 text-xs text-board-ink/50">Tidak ada menu yang cocok.</li>
        </ul>
    </div>

    {{-- Navigasi normal (akordeon grup) — tampil di mobile & desktop diperluas --}}
    <div x-show="query === ''" x-cloak :class="collapsed ? 'lg:hidden' : ''">
        <ul class="space-y-4">
            @foreach ($visibleGroups as $group)
                @php
                    $groupSlug = $slugFor($group['label']);
                @endphp
                <li>
                    <button type="button" @click="toggle('{{ $groupSlug }}')"
                        :aria-expanded="!!open['{{ $groupSlug }}']" aria-controls="g-{{ $groupSlug }}"
                        class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em] text-board-ink/60 transition duration-150 ease-out hover:bg-board-soft/20 hover:text-board-ink/90">
                        <x-dynamic-component :component="'svg-' . $group['icon']" class="size-3.5 shrink-0" aria-hidden="true" />
                        <span class="min-w-0 flex-1 truncate text-left">{{ $group['label'] }}</span>
                        <x-svg-chevron-down class="size-3.5 shrink-0 transition-transform duration-150" x-bind:class="open['{{ $groupSlug }}'] ? 'rotate-180' : ''" aria-hidden="true" />
                    </button>
                    <ul x-show="open['{{ $groupSlug }}']" x-cloak id="g-{{ $groupSlug }}" class="space-y-0.5"
                        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        @foreach ($group['items'] as $item)
                            @if (isset($item['children']))
                                @php
                                    $parentSlug = $slugFor($group['label'].'-'.$item['label']);
                                    $parentActive = collect($item['children'])->contains(fn ($child) => $isRouteMatch($child));
                                @endphp
                                <li>
                                    <button type="button" @click="toggle('{{ $parentSlug }}')"
                                        :aria-expanded="!!open['{{ $parentSlug }}']" aria-controls="p-{{ $parentSlug }}"
                                        @class([
                                            'group/nav relative flex w-full items-center gap-2.5 rounded-[var(--radius-control)] px-2.5 py-2 pl-6 text-[13px] font-semibold transition duration-150 ease-out',
                                            'bg-board-soft/40 text-board-ink ring-1 ring-inset ring-board-soft/60 shadow-sm' => $parentActive,
                                            'text-board-ink/70 hover:bg-board-soft/30 hover:text-board-ink' => ! $parentActive,
                                        ])>
                                        <x-dynamic-component :component="'svg-' . ($item['icon'] ?? 'arrow-small-right')" class="size-[16px] shrink-0" aria-hidden="true" />
                                        <span class="min-w-0 flex-1 truncate text-left">{{ $item['label'] }}</span>
                                        <x-svg-chevron-down class="size-3.5 shrink-0 transition-transform duration-150" x-bind:class="open['{{ $parentSlug }}'] ? 'rotate-180' : ''" aria-hidden="true" />
                                    </button>
                                    <ul x-show="open['{{ $parentSlug }}']" x-cloak id="p-{{ $parentSlug }}" class="space-y-0.5">
                                        @foreach ($item['children'] as $child)
                                            @php
                                                $childActive = $isRouteMatch($child);
                                                $href = route($child['route'], $child['routeParams'] ?? []);
                                                $isExternal = (bool) ($child['external'] ?? false);
                                            @endphp
                                            <li>
                                                <a href="{{ $href }}"
                                                    @if ($isExternal)
                                                        rel="noopener" x-on:click.prevent="$dispatch('open-external-link', { url: '{{ $href }}', label: '{{ addslashes($child['label']) }}' })"
                                                    @endif
                                                    :title="collapsed ? '{{ addslashes($child['label']) }}' : ''"
                                                    @class([
                                                        'group/nav relative flex items-center gap-2.5 rounded-[var(--radius-control)] px-2.5 py-2 pl-6 text-[13px] font-semibold transition duration-150 ease-out',
                                                        'bg-board-soft/40 text-board-ink ring-1 ring-inset ring-board-soft/60 shadow-sm' => $childActive,
                                                        'text-board-ink/70 hover:bg-board-soft/30 hover:text-board-ink' => ! $childActive,
                                                    ])
                                                    :class="collapsed ? 'lg:justify-center lg:pl-0' : 'lg:justify-start lg:pl-6'"
                                                    aria-current="{{ $childActive ? 'page' : 'false' }}">
                                                    <x-dynamic-component :component="'svg-' . ($child['icon'] ?? 'arrow-small-right')" class="size-[16px] shrink-0" aria-hidden="true" />
                                                    <span x-cloak :class="collapsed ? 'lg:hidden' : ''" class="min-w-0 flex-1 truncate">{{ $child['label'] }}</span>
                                                    @if ($childActive)
                                                        <span class="size-1.5 shrink-0 rounded-full bg-board-ink" aria-hidden="true"></span>
                                                    @endif
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                @php
                                    $itemActive = $isRouteMatch($item);
                                    $href = route($item['route'], $item['routeParams'] ?? []);
                                    $isExternal = (bool) ($item['external'] ?? false);
                                @endphp
                                <li>
                                    <a href="{{ $href }}"
                                        @if ($isExternal)
                                            rel="noopener" x-on:click.prevent="$dispatch('open-external-link', { url: '{{ $href }}', label: '{{ addslashes($item['label']) }}' })"
                                        @endif
                                        :title="collapsed ? '{{ addslashes($item['label']) }}' : ''"
                                        @class([
                                            'group/nav relative flex items-center gap-2.5 rounded-[var(--radius-control)] px-2.5 py-2 pl-6 text-[13px] font-semibold transition duration-150 ease-out',
                                            'bg-board-soft/40 text-board-ink ring-1 ring-inset ring-board-soft/60 shadow-sm' => $itemActive,
                                            'text-board-ink/70 hover:bg-board-soft/30 hover:text-board-ink' => ! $itemActive,
                                        ])
                                        :class="collapsed ? 'lg:justify-center lg:pl-0' : 'lg:justify-start lg:pl-6'"
                                        aria-current="{{ $itemActive ? 'page' : 'false' }}">
                                        <x-dynamic-component :component="'svg-' . ($item['icon'] ?? 'arrow-small-right')" class="size-[16px] shrink-0" aria-hidden="true" />
                                        <span x-cloak :class="collapsed ? 'lg:hidden' : ''" class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                                        @if ($itemActive)
                                            <span class="size-1.5 shrink-0 rounded-full bg-board-ink" aria-hidden="true"></span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Tampilan desktop ciut (ikon saja) — rata tanpa akordeon --}}
    <div x-cloak :class="collapsed ? 'lg:flex' : ''" class="hidden flex-col">
        <ul class="space-y-0.5">
            @foreach ($flatMenu as $menu)
                @php
                    $href = $menu['route'] ? route($menu['route'], $menu['routeParams']) : '#';
                    $isExternal = (bool) $menu['external'];
                @endphp
                <li>
                    <a href="{{ $href }}"
                        @if ($isExternal)
                            rel="noopener" x-on:click.prevent="$dispatch('open-external-link', { url: '{{ $href }}', label: '{{ addslashes($menu['label']) }}' })"
                        @endif
                        :title="'{{ addslashes($menu['label']) }}'"
                        class="group/nav relative flex items-center justify-center rounded-[var(--radius-control)] p-2 text-board-ink/70 transition duration-150 ease-out hover:bg-board-soft/30 hover:text-board-ink">
                        <x-dynamic-component :component="'svg-' . $menu['icon']" class="size-5 shrink-0" aria-hidden="true" />
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>