<x-layouts.page
    :title="'Rekap Rapor & Nilai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="akademik.rapor.index">

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Rekap Rapor & Nilai</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Pantau keterisian nilai dan status rapor per kelas · Semester {{ ucfirst($semester) }} · {{ $tahun->name }}.
                </p>
            </div>

            <form method="GET" action="{{ route('akademik.rapor.index') }}" class="flex flex-wrap items-center gap-3">
                <div>
                    <label for="semester" class="sr-only">Semester</label>
                    <select id="semester" name="semester" onchange="this.form.submit()"
                        class="tabular rounded-[var(--radius-control)] bg-sheet px-3 py-2 font-mono text-sm font-semibold text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>

                @if ($allClasses->isNotEmpty())
                    <div>
                        <label for="class_group_id" class="sr-only">Filter Kelas</label>
                        <select id="class_group_id" name="class_group_id" onchange="this.form.submit()"
                            class="tabular rounded-[var(--radius-control)] bg-sheet px-3 py-2 font-mono text-sm font-semibold text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Semua Kelas</option>
                            @foreach ($allClasses as $class)
                                <option value="{{ $class->id }}" {{ $classFilter == $class->id ? 'selected' : '' }}>
                                    {{ $class->grade_level }} {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </form>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.kpi label="Total Siswa" :value="$totals['total_siswa']" icon="user-group" />
            <x-ui.kpi label="Nilai Terisi" :value="$totals['nilai_terisi']" :suffix="' ('.$totals['persentase_nilai'].'%)'" icon="pencil-square" />
            <x-ui.kpi label="Rapor Terbit" :value="$totals['rapor_terbit']" :suffix="' ('.$totals['persentase_rapor'].'%)'" icon="document-text" />
            <div class="flex items-center gap-3 rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <span class="flex size-10 items-center justify-center rounded-full bg-secondary-soft text-secondary" aria-hidden="true">
                    <x-svg-check-circle class="size-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold text-ink-faint">Kesiapan Rapor</p>
                    <p class="tabular text-xl font-extrabold text-ink">{{ $totals['persentase_rapor'] }}%</p>
                </div>
            </div>
        </div>

        @if ($perClass->isEmpty())
            <div class="mt-6">
                <x-ui.alert variant="info">
                    Tidak ada data penempatan siswa aktif di semester ini.
                </x-ui.alert>
            </div>
        @else
            <div class="mt-6">
                <x-ui.sheet title="Statistik per Kelas" :padding="false">
                    <x-ui.table :headers="['Kelas', 'Wali Kelas', 'Total Siswa', 'Nilai Terisi', '%', 'Rapor Terbit', '%', 'Aksi']">
                        <x-slot name="emptySlot">Tidak ada data.</x-slot>
                        <x-slot>
                            @foreach ($perClass as $stat)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-accent-soft text-xs font-extrabold text-accent">
                                                {{ mb_substr($stat['class']->grade_level, 0, 1) }}
                                            </span>
                                            <div>
                                                <span class="font-semibold text-ink">{{ $stat['class']->grade_level }} {{ $stat['class']->name }}</span>
                                                <span class="block text-xs text-ink-faint">
                                                    {{ $stat['class']->homeroom?->teacher?->name ?? 'Belum ada wali kelas' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 tabular font-mono text-sm font-semibold text-ink">{{ $stat['total_siswa'] }}</td>
                                    <td class="px-4 py-3 tabular font-mono text-sm font-semibold text-ink">{{ $stat['nilai_terisi'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <x-ui.badge :variant="$stat['persentase_nilai'] >= 90 ? 'success' : ($stat['persentase_nilai'] >= 70 ? 'info' : ($stat['persentase_nilai'] >= 50 ? 'warning' : 'danger'))">
                                            {{ $stat['persentase_nilai'] }}%
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 tabular font-mono text-sm font-semibold text-ink">{{ $stat['rapor_terbit'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <x-ui.badge :variant="$stat['persentase_rapor'] >= 90 ? 'success' : ($stat['persentase_rapor'] >= 70 ? 'info' : ($stat['persentase_rapor'] >= 50 ? 'warning' : 'danger'))">
                                            {{ $stat['persentase_rapor'] }}%
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3">
                                        <x-ui.button size="sm" variant="ghost" icon="arrow-right"
                                            href="{{ route('akademik.rapor.kelas', $stat['class']) }}">
                                            Detail
                                        </x-ui.button>
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-ui.table>
                    <div class="border-t border-rule/70 px-4 py-3.5">
                        <x-ui.pagination :current="$classes->currentPage()" :last="$classes->lastPage()" />
                    </div>
                </x-ui.sheet>
            </div>

            <div class="mt-6">
                <x-ui.alert variant="info">
                    <p class="text-sm">
                        <strong class="font-bold">Catatan:</strong>
                        <span class="block mt-1 text-ink-soft">
                            • <strong>Nilai Terisi</strong> = jumlah siswa yang sudah memiliki minimal satu nilai akhir di semester ini.
                            <br>
                            • <strong>Rapor Terbit</strong> = jumlah siswa yang rapor-nya sudah diterbitkan (status <strong>terbit</strong>).
                        </span>
                    </p>
                </x-ui.alert>
            </div>
        @endif
    </div>
</x-layouts.page>
