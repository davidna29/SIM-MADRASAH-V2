@props([
    'title' => null,
    'breadcrumb' => null,
    'role' => null,
    'roleLabel' => null,
    'activeRoute' => null,
])

<x-layouts.root :title="$title">
    <x-layouts.app :role="$role" :roleLabel="$roleLabel" :activeRoute="$activeRoute">
        @if ($breadcrumb)
            <div class="mb-6">
                <x-ui.breadcrumb :items="$breadcrumb" />
            </div>
        @endif
        {{ $slot }}
    </x-layouts.app>
</x-layouts.root>
