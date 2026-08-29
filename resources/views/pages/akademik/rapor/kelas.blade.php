<x-layouts.page
    :title="'Nilai Kelas '.$classGroup->grade_level.' '.$classGroup->name"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="akademik.rapor.kelas">

    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                    Kelas {{ $classGroup->grade_level }} {{ $classGroup->name }}
                </h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Nilai akhir per siswa per mapel · Semester {{ ucfirst($semester) }} · {{ $tahun->name }}.
                    Wali Kelas: {{ $classGroup->homeroom?->teacher?->name ?? 'Belum ada' }}.
                </p>
            </div>

            <form method="GET" action="{{ route('akademik.rapor.kelas', $classGroup) }}" class="flex items-center gap-3">
                <div>
                    <label for="semester" class="sr-only">Semester</label>
                    <select id="semester" name="semester" onchange="this.form.submit()"
                        class="tabular rounded-[var(--radius-control)] bg-sheet px-3 py-2 font-mono text-sm font-semibold text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                        <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Genap</option>
                    </select>
                </div>
            </form>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <div class="mt-6">
            <x-ui.sheet :padding="false">
                <div class="overflow-x-auto">
                    @if ($subjects->isEmpty())
                        <div class="px-6 py-10 text-center text-sm text-ink-faint">
                            Belum ada nilai untuk kelas ini pada semester {{ ucfirst($semester) }}.
                        </div>
                    @else
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-rule/70 bg-paper/40">
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-ink-faint">NIS</th>
                                    <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-ink-faint">Nama Siswa</th>
                                    @foreach ($subjects as $subject)
                                        <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-ink-faint">{{ $subject->code }}</th>
                                    @endforeach
                                    <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-ink-faint">Rata-rata</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-ink-faint">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($matrix as $row)
                                    @php
                                        $enrollment = $row['enrollment'];
                                        $sum = 0; $count = 0;
                                        foreach ($row['scores'] as $score) {
                                            if ($score?->score !== null) {
                                                $sum += $score->score;
                                                $count++;
                                            }
                                        }
                                        $avg = $count > 0 ? round($sum / $count) : null;
                                    @endphp
                                    <tr class="border-b border-rule/50 transition hover:bg-paper/60">
                                        <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $enrollment->student->nis }}</td>
                                        <td class="px-4 py-3 font-semibold text-ink">{{ $enrollment->student->name }}</td>
                                        @foreach ($subjects as $subject)
                                            @php $score = $row['scores'][$subject->id] ?? null; @endphp
                                            <td class="px-3 py-3 text-center">
                                                @if ($score?->score !== null)
                                                    <span class="tabular font-mono text-sm font-semibold text-ink">{{ $score->score }}</span>
                                                @else
                                                    <span class="text-ink-faint">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-3 py-3 text-center">
                                            @if ($avg !== null)
                                                <span class="tabular font-mono text-sm font-bold text-primary">{{ $avg }}</span>
                                            @else
                                                <span class="text-ink-faint">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <x-ui.button size="sm" variant="ghost" icon="arrow-right"
                                                href="{{ route('akademik.rapor.siswa', $enrollment) }}">
                                                Detail
                                            </x-ui.button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="border-t border-rule/70 px-4 py-3.5">
                    <x-ui.pagination :current="$enrollments->currentPage()" :last="$enrollments->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>

        <div class="mt-6">
            <x-ui.alert variant="info">
                <p class="text-sm">
                    <strong class="font-bold">Catatan:</strong> "Rata-rata" adalah rata-rata nilai akhir semua mapel.
                    Predikat per mapel mengikuti skala: A ≥ 90 · B ≥ 80 · C ≥ 70 · D ≥ 60 · E &lt; 60.
                </p>
            </x-ui.alert>
        </div>
    </div>
</x-layouts.page>
