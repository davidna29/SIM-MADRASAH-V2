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

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <!-- Sub-menu tingkat -->
        <nav class="mt-6 flex flex-wrap items-center gap-1.5" aria-label="Filter tingkat">
            <a href="{{ route('kelas.index') }}"
                @class([
                    'rounded-full px-3.5 py-1.5 text-xs font-semibold transition',
                    'bg-primary text-white shadow-sm' => !request('grade_level'),
                    'bg-sheet text-ink-soft ring-1 ring-inset ring-rule-strong hover:bg-paper-deep hover:text-ink' => request('grade_level'),
                ])>
                Semua
            </a>
            @foreach (['I', 'II', 'III', 'IV', 'V', 'VI'] as $level)
                <a href="{{ route('kelas.index', ['grade_level' => $level]) }}"
                    @class([
                        'rounded-full px-3.5 py-1.5 text-xs font-semibold transition',
                        'bg-primary text-white shadow-sm' => request('grade_level') === $level,
                        'bg-sheet text-ink-soft ring-1 ring-inset ring-rule-strong hover:bg-paper-deep hover:text-ink' => request('grade_level') !== $level,
                    ])>
                    Tingkat {{ $level }}
                </a>
            @endforeach
        </nav>

        <!-- Grup per tingkat -->
        @php
            $selected = request('grade_level');
            $grouped = $classes->getCollection()->groupBy('grade_level');
            $levels = collect(['I', 'II', 'III', 'IV', 'V', 'VI']);
        @endphp

        @if ($classes->isEmpty())
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Belum ada kelas.</p>
                <p class="mt-1 text-xs text-ink-faint">Tambahkan kelas untuk mulai menempatkan siswa.</p>
            </div>
        @endif

        <div class="mt-8 space-y-8">
            @foreach ($levels as $level)
                @php
                    $group = $grouped->get($level, collect());
                @endphp
                @if ($group->isEmpty() && ! $selected)
                    @continue
                @endif
                <section aria-labelledby="tingkat-{{ $level }}">
                    <div class="flex items-center gap-3">
                        <h2 id="tingkat-{{ $level }}" class="text-sm font-extrabold uppercase tracking-[0.12em] text-ink-soft">
                            Tingkat {{ $level }}
                        </h2>
                        <span class="tabular font-mono text-xs text-ink-faint">{{ $group->count() }} kelas</span>
                        <span class="h-px flex-1 bg-rule-strong/60" aria-hidden="true"></span>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse ($group as $classGroup)
                            <x-ui.sheet
                                :title="$classGroup->name"
                                :subtitle="'Tingkat ' . $classGroup->grade_level"
                                pinned
                                :actions="view('pages.kelas._actions', ['classGroup' => $classGroup])->render()">
                                <div class="flex items-center justify-between">
                                    <x-ui.badge variant="info" icon="academic-cap">{{ $classGroup->enrollments_count }} siswa aktif</x-ui.badge>
                                </div>
                            </x-ui.sheet>
                        @empty
                            <p class="text-sm text-ink-faint sm:col-span-2 xl:col-span-3">
                                Belum ada kelas pada tingkat ini.
                            </p>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.page>
