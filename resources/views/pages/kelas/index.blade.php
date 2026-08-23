<x-layouts.page
    :title="'Kelas & Penempatan'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="kelas.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kelas & Penempatan</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Rombongan belajar Tahun Ajaran {{ $tahun->name }} — penempatan siswa dicatat sebagai lembar per tahun, tidak pernah ditimpa.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('kelas.create') }}">Tambah Kelas</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($classes as $classGroup)
                <x-ui.sheet
                    :title="$classGroup->name"
                    :subtitle="'Tingkat ' . $classGroup->grade_level"
                    pinned
                    :actions="view('components.ui.button', ['variant' => 'secondary', 'size' => 'sm', 'icon' => 'arrow-up-right', 'href' => route('kelas.show', $classGroup)])->withSlot('Kelola')->render()">
                    <div class="flex items-center justify-between">
                        <x-ui.badge variant="info" icon="academic-cap">{{ $classGroup->enrollments_count }} siswa aktif</x-ui.badge>
                    </div>
                </x-ui.sheet>
            @endforeach
        </div>
    </div>
</x-layouts.page>
