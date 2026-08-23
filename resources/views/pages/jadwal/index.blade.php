<x-layouts.page
    :title="'Jadwal Mengajar'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="jadwal.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Jadwal Mengajar</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Jadwal per rombel pada Tahun Ajaran {{ $tahun->name }} — disusun dari penugasan mengajar.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('jadwal.create') }}">Tambah Jadwal</x-ui.button>
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

        <!-- Pilih rombel -->
        <form method="GET" action="{{ route('jadwal.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Rombel</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-40" :options="$classes->pluck('name', 'id')" :selected="request('class_group_id')" placeholder="Pilih rombel…" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Muat</x-ui.button>
            </div>
        </form>

        @if ($selectedClass)
            <!-- Grid per hari -->
            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($days as $day)
                    <x-ui.sheet :title="ucfirst($day)" :subtitle="'Kelas ' . $selectedClass->name" pinned>
                        @php
                            $daySchedules = $schedules->where('day', $day);
                        @endphp
                        <ul class="space-y-2">
                            @forelse ($daySchedules as $schedule)
                                <li class="group rounded-[var(--radius-control)] bg-paper/70 p-3 ring-1 ring-inset ring-rule/70">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="tabular font-mono text-xs font-semibold text-primary-strong">
                                                {{ $schedule->start_time->format('H:i') }}–{{ $schedule->end_time->format('H:i') }}
                                            </p>
                                            <p class="mt-0.5 text-[13px] font-semibold text-ink">{{ $schedule->assignment->subject->name }}</p>
                                            <p class="mt-0.5 text-xs text-ink-soft">{{ $schedule->assignment->teacher->name }}</p>
                                            @if ($schedule->room)
                                                <p class="tabular mt-0.5 font-mono text-[11px] text-ink-faint">Ruang {{ $schedule->room }}</p>
                                            @endif
                                        </div>
                                        <div class="flex shrink-0 items-center gap-1">
                                            <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('jadwal.edit', $schedule) }}">Ubah</x-ui.button>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="rounded-[var(--radius-control)] px-3 py-4 text-center text-xs text-ink-faint">
                                    Belum ada jadwal.
                                </li>
                            @endforelse
                        </ul>
                    </x-ui.sheet>
                @endforeach
            </div>
        @else
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Pilih rombel untuk melihat jadwalnya.</p>
            </div>
        @endif
    </div>
</x-layouts.page>
