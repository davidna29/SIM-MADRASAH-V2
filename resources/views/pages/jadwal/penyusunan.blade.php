<x-layouts.page
    :title="'Penyusunan Jadwal'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="jadwal.penyusunan">

    <div x-data="penyusunan()" class="mx-auto max-w-[1400px]">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Penyusunan Jadwal</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Tabel master penyusunan — hari disusun vertikal ke bawah. Klik sel untuk mengisi guru & mata pelajaran.
                </p>
            </div>
            <form method="GET" action="{{ route('jadwal.penyusunan') }}" class="flex items-end gap-2">
                <div>
                    <label for="model" class="block pb-1.5 text-xs font-bold text-ink">Model Jadwal</label>
                    <div class="flex items-center gap-2">
                        <x-ui.select name="model" :full="false" class="w-64" :options="$models->pluck('name', 'id')" :selected="$model?->id" placeholder="Pilih model…" onchange="this.form.submit()" />
                        <x-ui.button type="submit" variant="secondary" size="md">Muat</x-ui.button>
                    </div>
                </div>
            </form>
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

        @if ($model)
            @php
                $rombel = \App\Models\ClassGroup::whereIn('grade_level', $model->gradeLevels())
                    ->orderByRaw("FIELD(grade_level,'I','II','III','IV','V','VI')")
                    ->orderBy('name')
                    ->get();
                $cells = $model->cells;
                $teacherOptions = $teachers->pluck('name', 'id')->all();
                $subjectOptions = $subjects->pluck('name', 'id')->all();
            @endphp

            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                <div class="flex flex-wrap items-center gap-3">
                    <x-ui.badge variant="info" :dot="false">Model: {{ $model->name }}</x-ui.badge>
                    <x-ui.badge variant="neutral" :dot="false">{{ count($rombel) }} rombel</x-ui.badge>
                    <x-ui.badge variant="neutral" :dot="false">{{ $model->slots->count() }} jam/hari</x-ui.badge>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-rule/70 pt-3">
                    <form method="POST" action="{{ route('jadwal.generate', $model) }}" x-data="{ mode: 'blank' }"
                        onsubmit="return confirm('Generate jadwal untuk model ini? Kerangka kosong akan mereset isian yang sudah ada.');"
                        class="flex flex-wrap items-end gap-2">
                        @csrf
                        <div>
                            <label class="block pb-1.5 text-xs font-bold text-ink">Mode Generate</label>
                            <select name="mode" x-model="mode" class="w-44 rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="blank">Kerangka kosong</option>
                                <option value="copy">Salin tahun sebelumnya</option>
                            </select>
                        </div>
                        <div x-show="mode === 'copy'" x-cloak class="transition">
                            <label class="block pb-1.5 text-xs font-bold text-ink">Tahun Sumber</label>
                            <x-ui.select name="source_academic_year_id" :full="false" class="w-44" :options="$years->pluck('name', 'id')" />
                        </div>
                        <x-ui.button type="submit" variant="primary" size="md" icon="sparkles">Generate Jadwal</x-ui.button>
                    </form>
                    <span class="mx-1 h-4 w-px bg-rule-strong/70" aria-hidden="true"></span>
                    <span class="text-xs text-ink-faint">Cetak tersedia di tampilan per-kelas & per-guru.</span>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                <table class="w-full min-w-[900px] border-collapse text-sm">
                    <thead>
                        <tr class="border-b-2 border-rule-strong bg-paper/50">
                            <th scope="col" class="px-3 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft w-28">Jam</th>
                            @foreach ($rombel as $class)
                                <th scope="col" class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink">{{ $class->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($days as $day)
                            @php
                                $dayCells = $cells->where('day', $day);
                                $periodNumbers = $model->slots->pluck('period_no');
                            @endphp
                            <!-- Baris header hari -->
                            <tr class="bg-board text-board-ink">
                                <td colspan="{{ count($rombel) + 1 }}" class="px-3 py-2 text-xs font-extrabold uppercase tracking-[0.12em]">
                                    {{ ucfirst($day) }}
                                </td>
                            </tr>
                            @foreach ($model->slots as $slot)
                                <tr class="border-b border-rule/70 {{ $slot->is_break ? 'bg-paper/60' : '' }}">
                                    <td class="px-3 py-1.5 align-middle">
                                        @if ($slot->is_break)
                                            <span class="text-[11px] font-bold text-warning">{{ $slot->label ?: 'Istirahat' }}</span>
                                        @else
                                            <span class="tabular block font-mono text-[11px] font-semibold text-ink-faint">{{ $slot->period_no }}</span>
                                            <span class="tabular block font-mono text-[10px] text-ink-faint">{{ $slot->start_time->format('H:i') }}–{{ $slot->end_time->format('H:i') }}</span>
                                        @endif
                                    </td>
                                    @foreach ($rombel as $class)
                                        <td class="px-1.5 py-1.5 align-middle text-center">
                                            @if ($slot->is_break)
                                                <span class="block text-center text-[11px] text-ink-faint">—</span>
                                            @else
                                                @php
                                                    $cell = $dayCells->first(fn ($c) => $c->class_group_id === $class->id && $c->period_no === $slot->period_no);
                                                @endphp
                                                <div class="flex items-stretch gap-1">
                                                    <button type="button"
                                                        @click="openCell('{{ $class->id }}', '{{ $day }}', {{ $slot->period_no }}, {{ $cell?->teacher_id ?? 'null' }}, {{ $cell?->subject_id ?? 'null' }}, '{{ $class->name }}')"
                                                        class="group/btn block flex-1 rounded-[var(--radius-control)] px-2 py-1.5 text-left transition hover:bg-primary-soft focus:outline-none focus:ring-2 focus:ring-primary"
                                                        :class="selectedKey === '{{ $class->id }}-{{ $day }}-{{ $slot->period_no }}' ? 'bg-primary-soft ring-2 ring-primary' : ''">
                                                        @if ($cell && $cell->teacher_id && $cell->subject_id)
                                                            <span class="block text-[12px] font-semibold leading-tight text-ink">{{ $cell->teacher?->name }}</span>
                                                            <span class="block text-[11px] text-primary-strong">{{ $cell->subject?->name }}</span>
                                                        @else
                                                            <span class="block text-center text-[11px] text-ink-faint">+ isi</span>
                                                        @endif
                                                    </button>
                                                    @if ($cell && $cell->teacher_id && $cell->subject_id)
                                                        <form method="POST" action="{{ route('jadwal.penyusunan.store', $model) }}" class="shrink-0"
                                                            onsubmit="return confirm('Kosongkan sel ini?');">
                                                            @csrf
                                                            <input type="hidden" name="cells[0][class_group_id]" value="{{ $class->id }}">
                                                            <input type="hidden" name="cells[0][day]" value="{{ $day }}">
                                                            <input type="hidden" name="cells[0][period_no]" value="{{ $slot->period_no }}">
                                                            <input type="hidden" name="cells[0][teacher_id]" value="">
                                                            <input type="hidden" name="cells[0][subject_id]" value="">
                                                            <button type="submit" class="flex h-full items-center rounded-[var(--radius-control)] px-1.5 text-ink-faint transition hover:bg-danger-soft hover:text-danger" aria-label="Hapus isi sel">
                                                                <x-svg-x-mark class="size-3.5" aria-hidden="true" />
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal picker sel -->
            <div x-show="cellModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                @keydown.escape.window="cellModalOpen = false" role="dialog" aria-modal="true" aria-labelledby="cell-modal-title">
                <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]" @click="cellModalOpen = false"></div>
                <div class="relative w-full max-w-md rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule"
                    @keydown.enter.prevent="submitCell"
                    x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                    <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                        <h3 id="cell-modal-title" class="text-sm font-bold tracking-tight text-ink">
                            Isi Jadwal — <span x-text="cellLabel"></span>
                        </h3>
                        <button type="button" @click="cellModalOpen = false" class="rounded-md p-1 text-ink-faint transition hover:bg-paper-deep hover:text-ink" aria-label="Tutup">
                            <x-svg-x-mark class="size-5" aria-hidden="true" />
                        </button>
                    </header>

                    <div class="space-y-4 px-5 py-5">
                        <div>
                            <label class="block text-xs font-bold text-ink">Guru</label>
                            <div class="relative mt-1.5">
                                <x-svg-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-ink-faint" aria-hidden="true" />
                                <input type="text" x-model="teacherQuery" placeholder="Ketik nama guru…"
                                    class="w-full rounded-[var(--radius-control)] bg-sheet py-2.5 pl-9 pr-3 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div class="mt-1.5 max-h-44 overflow-y-auto rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/70">
                                <template x-for="t in filteredTeachers" :key="t.id">
                                    <button type="button" @click="selectedTeacher = t.id; teacherQuery = ''"
                                        class="block w-full px-3 py-2 text-left text-[13px] text-ink transition hover:bg-primary-soft"
                                        :class="selectedTeacher === t.id ? 'bg-primary-soft font-semibold' : ''"
                                        x-text="t.name"></button>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-ink">Mata Pelajaran</label>
                            <div class="relative mt-1.5">
                                <x-svg-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-ink-faint" aria-hidden="true" />
                                <input type="text" x-model="subjectQuery" placeholder="Ketik nama mapel…"
                                    class="w-full rounded-[var(--radius-control)] bg-sheet py-2.5 pl-9 pr-3 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div class="mt-1.5 max-h-44 overflow-y-auto rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/70">
                                <template x-for="s in filteredSubjects" :key="s.id">
                                    <button type="button" @click="selectedSubject = s.id; subjectQuery = ''"
                                        class="block w-full px-3 py-2 text-left text-[13px] text-ink transition hover:bg-primary-soft"
                                        :class="selectedSubject === s.id ? 'bg-primary-soft font-semibold' : ''"
                                        x-text="s.name"></button>
                                </template>
                            </div>
                        </div>
                        <p class="text-xs text-ink-faint" x-show="selectedTeacher && selectedSubject">
                            Terpilih: <span class="font-semibold text-ink" x-text="selectedTeacherName + ' · ' + selectedSubjectName"></span>
                        </p>
                    </div>

                    <footer class="flex items-center justify-end gap-2 border-t border-rule/70 px-5 py-4">
                        <x-ui.button variant="ghost" size="sm" @click="cellModalOpen = false">Batal</x-ui.button>
                        <form method="POST" :action="'{{ route('jadwal.penyusunan.store', $model) }}'" @submit.prevent="submitCell">
                            @csrf
                            <input type="hidden" name="cells[0][class_group_id]" :value="cellClassId">
                            <input type="hidden" name="cells[0][day]" :value="cellDay">
                            <input type="hidden" name="cells[0][period_no]" :value="cellPeriod">
                            <input type="hidden" name="cells[0][teacher_id]" :value="selectedTeacher ?? ''">
                            <input type="hidden" name="cells[0][subject_id]" :value="selectedSubject ?? ''">
                            <x-ui.button type="submit" variant="primary" size="sm" icon="check">Sematkan ke Sel</x-ui.button>
                        </form>
                    </footer>
                </div>
            </div>
        @else
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Pilih model jadwal untuk mulai menyusun.</p>
                <p class="mt-1 text-xs text-ink-faint">Belum ada model jadwal pada tahun ini.</p>
            </div>
        @endif
    </div>

    <script>
        function penyusunan() {
            return {
                cellModalOpen: false,
                selectedKey: null,
                cellLabel: '',
                cellClassId: null,
                cellDay: '',
                cellPeriod: null,
                teacherQuery: '',
                subjectQuery: '',
                selectedTeacher: null,
                selectedSubject: null,
                teachers: @json($teachers->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])),
                subjects: @json($subjects->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])),
                get filteredTeachers() {
                    const q = this.teacherQuery.toLowerCase();
                    return this.teachers.filter(t => !q || t.name.toLowerCase().includes(q));
                },
                get filteredSubjects() {
                    const q = this.subjectQuery.toLowerCase();
                    return this.subjects.filter(s => !q || s.name.toLowerCase().includes(q));
                },
                get selectedTeacherName() {
                    return this.teachers.find(t => t.id === this.selectedTeacher)?.name || '';
                },
                get selectedSubjectName() {
                    return this.subjects.find(s => s.id === this.selectedSubject)?.name || '';
                },
                openCell(classId, day, period, teacherId, subjectId, label) {
                    this.selectedKey = classId + '-' + day + '-' + period;
                    this.cellClassId = classId;
                    this.cellDay = day;
                    this.cellPeriod = period;
                    this.cellLabel = label + ' · ' + day.toUpperCase() + ' jam ke-' + period;
                    this.selectedTeacher = teacherId;
                    this.selectedSubject = subjectId;
                    this.teacherQuery = '';
                    this.subjectQuery = '';
                    this.cellModalOpen = true;
                },
                submitCell() {
                    // Submit form per-sel; backend memvalidasi konflik guru (hard-block)
                    this.$root.querySelector('[role="dialog"] form')?.submit();
                },
            };
        }
    </script>
</x-layouts.page>
