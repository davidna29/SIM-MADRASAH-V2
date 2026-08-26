@props([
    // Nama rute halaman aktif: ppdb.index | ppdb.show | ppdb.generate-nis | ppdb.assign-class-page
    'active' => 'ppdb.index',
    // Catatan guardrail singkat (opsional)
    'note' => null,
])

@php
    $steps = [
        [
            'route' => 'ppdb.index',
            'label' => '1. Terima / Tolak',
            'hint' => 'Review tiap pendaftar, lalu Terima (status submitted → accepted).',
            'icon' => 'user-plus',
        ],
        [
            'route' => 'ppdb.generate-nis',
            'label' => '2. Generate NIS',
            'hint' => 'Beri NIS massal, urut abjad, counter berlanjut.',
            'icon' => 'hashtag',
        ],
        [
            'route' => 'ppdb.assign-class-page',
            'label' => '3. Tentukan Kelas',
            'hint' => 'Sebar calon siswa ke rombel yang sudah ada.',
            'icon' => 'building-library',
        ],
        [
            'route' => 'ppdb.export',
            'label' => '4. Export Excel',
            'hint' => 'Unduh rekap EMIS + link Google Drive.',
            'icon' => 'arrow-down-tray',
        ],
    ];
@endphp

<x-ui.sheet title="Alur Pengerjaan Admin" subtitle="Urutan penanganan calon siswa hingga jadi siswa baru" pinned ruled>
    <ol class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($steps as $step)
            @php
                $isActive = $step['route'] === $active;
                $isDone = false; // tidak ada state "selesai" mutlak; sorot saja step aktif
            @endphp
            <li
                class="flex items-start gap-2.5 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset transition
                    {{ $isActive ? 'bg-primary-soft ring-primary/40' : 'bg-paper-deep/40 ring-rule/60' }}">
                <span class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-full text-xs font-bold
                    {{ $isActive ? 'bg-primary text-white' : 'bg-sheet text-ink-soft ring-1 ring-inset ring-rule-strong' }}">
                    @if ($isActive)
                        <x-svg-{{ $step['icon'] }} class="size-4" />
                    @else
                        {{ substr($step['label'], 0, 1) }}
                    @endif
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-bold {{ $isActive ? 'text-primary-strong' : 'text-ink' }}">{{ $step['label'] }}</p>
                    <p class="mt-0.5 text-xs leading-snug text-ink-soft">{{ $step['hint'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>

    @if ($note)
        <div class="mt-3 flex items-start gap-2 rounded-[var(--radius-control)] bg-warning-soft px-3 py-2 text-xs text-warning ring-1 ring-inset ring-warning/30">
            <x-svg-exclamation-triangle class="mt-0.5 size-4 shrink-0" />
            <p>{{ $note }}</p>
        </div>
    @endif
</x-ui.sheet>
