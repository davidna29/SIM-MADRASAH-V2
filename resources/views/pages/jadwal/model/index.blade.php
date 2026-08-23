<x-layouts.page
    :title="'Model Jadwal'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="jadwal.model.index">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Model Jadwal</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Template jadwal fleksibel — sesuaikan dengan kurikulum tahun {{ $tahun->name }}. Beberapa model bisa aktif
                    bersamaan selama tingkatan yang dicakup tidak tumpang tindih.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('jadwal.model.create') }}">Tambah Model</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <div class="mt-6 space-y-4">
            @forelse ($models as $model)
                <x-ui.sheet
                    :title="$model->name"
                    :subtitle="'Jam mulai ' . $model->start_time->format('H:i') . ' · maks ' . $model->max_hours_per_day . ' jam/hari · ' . $model->slots->count() . ' slot'"
                    pinned
                    :actions="view('pages.jadwal.model._actions', ['model' => $model])->render()">
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($model->gradeLevelRows as $row)
                            <x-ui.badge variant="info" :dot="false">Tingkat {{ $row->grade_level }}</x-ui.badge>
                        @endforeach
                        @if ($model->is_active)
                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                        @endif
                    </div>
                </x-ui.sheet>
            @empty
                <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                    <p class="text-sm font-semibold text-ink">Belum ada model jadwal.</p>
                    <p class="mt-1 text-xs text-ink-faint">Buat model pertama untuk mulai menyusun jadwal.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.page>
