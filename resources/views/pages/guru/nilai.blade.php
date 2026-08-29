@php
    $useComponents = $components->isNotEmpty();
    $componentIds = $components->pluck('id')->map(fn ($id) => (string) $id)->all();
    $componentWeights = $components->mapWithKeys(fn ($c) => [(string) $c->id => (string) $c->weight])->all();
@endphp
<x-layouts.page
    :title="'Input Nilai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="guru.nilai.edit">

    <div x-data="nilaiPage({{ json_encode($componentIds) }}, {{ json_encode($componentWeights) }})" class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $assignment->subject->name }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Input nilai {{ $assignment->subject->name }} kelas {{ $assignment->classGroup->name }}
                    · Semester {{ ucfirst($tahun->semester) }} {{ $tahun->name }}.
                </p>
            </div>
            <x-ui.badge variant="info" icon="building-library">{{ $assignment->classGroup->name }}</x-ui.badge>
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

        @if ($useComponents)
            <div class="mt-6">
                <x-ui.alert variant="info" dismissible>
                    <p class="text-sm">
                        <strong class="font-bold">Penilaian berbasis komponen.</strong>
                        <span class="block mt-1 text-ink-soft">
                            Nilai akhir dihitung otomatis dari bobot komponen:
                            @foreach ($components as $component)
                                <strong>{{ $component->name }}</strong> {{ trim($component->weight) }}%@if (! $loop->last) · @endif
                            @endforeach
                        </span>
                    </p>
                </x-ui.alert>
            </div>
        @endif

        <form method="POST" action="{{ route('guru.nilai.update', $assignment) }}" class="mt-6">
            @csrf
            <x-ui.sheet :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-rule/70 bg-paper/40">
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-ink-faint">NIS</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-ink-faint">Nama Siswa</th>
                                @forelse ($components as $component)
                                    <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-ink-faint">
                                        <span class="block">{{ $component->name }}</span>
                                        <span class="block font-mono font-semibold text-primary">{{ trim($component->weight) }}%</span>
                                    </th>
                                @empty
                                    <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-ink-faint">Nilai</th>
                                @endforelse
                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-ink-faint">Nilai Akhir</th>
                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wider text-ink-faint">Predikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($enrollments as $enrollment)
                                @php
                                    $score = $scores->get($enrollment->id);
                                    $nilaiAkhir = $score?->score;
                                    $componentValues = collect($score?->componentValues ?? [])
                                        ->keyBy('score_component_id')
                                        ->map(fn ($v) => (string) $v->value)
                                        ->all();
                                @endphp
                                <tr class="border-b border-rule/50 transition hover:bg-paper/60" data-row>
                                    <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $enrollment->student->nis }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">
                                                {{ mb_substr($enrollment->student->name, 0, 1) }}
                                            </span>
                                            <span class="font-semibold text-ink">{{ $enrollment->student->name }}</span>
                                        </div>
                                    </td>
                                    @if ($useComponents)
                                        @foreach ($components as $component)
                                            <td class="px-3 py-3 text-center">
                                                <label class="sr-only" for="nilai-{{ $enrollment->id }}-{{ $component->id }}">{{ $component->name }} {{ $enrollment->student->name }}</label>
                                                <input id="nilai-{{ $enrollment->id }}-{{ $component->id }}"
                                                    type="number" min="0" max="100" step="1" inputmode="numeric" placeholder="—"
                                                    name="values[{{ $enrollment->id }}][{{ $component->id }}]"
                                                    value="{{ $componentValues[$component->id] ?? '' }}"
                                                    @change="recalcRow($el.closest('[data-row]'))"
                                                    class="tabular w-16 rounded-[var(--radius-control)] bg-sheet px-2 py-2 text-center font-mono text-sm font-semibold text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                                            </td>
                                        @endforeach
                                    @else
                                        <td class="px-3 py-3 text-center">
                                            <label class="sr-only" for="nilai-{{ $enrollment->id }}">Nilai {{ $enrollment->student->name }}</label>
                                            <input id="nilai-{{ $enrollment->id }}" type="number" name="scores[{{ $enrollment->id }}]"
                                                value="{{ $nilaiAkhir }}" min="0" max="100" step="1" inputmode="numeric" placeholder="—"
                                                class="tabular w-16 rounded-[var(--radius-control)] bg-sheet px-2 py-2 text-center font-mono text-sm font-semibold text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                                        </td>
                                    @endif
                                    <td class="px-3 py-3 text-center">
                                        <span class="tabular font-mono text-sm font-bold text-ink" data-final
                                            @if ($useComponents) x-text="finalFor({{ $enrollment->id }})" @endif>
                                            @if (! $useComponents) {{ $nilaiAkhir ?? '—' }} @endif
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span data-predikat>
                                            @if ($useComponents)
                                                <span class="tabular font-mono text-sm font-semibold text-ink-soft" x-text="predikatFor({{ $enrollment->id }})">—</span>
                                            @else
                                                @if ($nilaiAkhir !== null)
                                                    <x-ui.badge :variant="App\Support\Rapor::predikat((int) $nilaiAkhir) >= 'D' ? 'danger' : (App\Support\Rapor::predikat((int) $nilaiAkhir) === 'C' ? 'warning' : 'success')">{{ App\Support\Rapor::predikat((int) $nilaiAkhir) }}</x-ui.badge>
                                                @else
                                                    <span class="text-ink-faint">—</span>
                                                @endif
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule/70 px-5 py-4 sm:flex-row">
                    <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('guru.penugasan') }}">Kembali</x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check">Simpan Nilai</x-ui.button>
                </div>
            </x-ui.sheet>
        </form>

        <div class="mt-6">
            <x-ui.alert variant="info" dismissible>
                <strong class="font-bold">Catatan akses:</strong> Anda hanya dapat mengubah nilai kelas dan mapel yang menjadi
                penugasan Anda — aturan ini ditegakkan pada lapisan penugasan, bukan sekadar role.
            </x-ui.alert>
        </div>
    </div>

    <script>
        function nilaiPage(componentIds, componentWeights) {
            return {
                componentIds: componentIds || [],
                componentWeights: componentWeights || {},
                inputsFor(row) {
                    return Array.from(row.querySelectorAll('input[type="number"]'));
                },
                compute(row) {
                    const values = this.inputsFor(row).map(input => {
                        const raw = input.value.trim();
                        if (raw === '') return null;
                        const num = Number(raw);
                        if (num < 0 || num > 100) return null;
                        return num;
                    });
                    let sum = 0;
                    let weightSum = 0;
                    this.componentIds.forEach((id, i) => {
                        const v = values[i];
                        if (v !== null) {
                            sum += v * Number(this.componentWeights[id]);
                            weightSum += Number(this.componentWeights[id]);
                        }
                    });
                    if (weightSum === 0) return null;
                    return Math.round(sum / weightSum);
                },
                recalcRow(row) {
                    const final = this.compute(row);
                    this.updateRow(row, final);
                },
                updateRow(row, final) {
                    const finalEl = row.querySelector('[data-final]');
                    const predikatEl = row.querySelector('[data-predikat]');
                    if (finalEl) finalEl.textContent = final === null ? '—' : final;
                    if (predikatEl) {
                        if (final === null) {
                            predikatEl.textContent = '—';
                        } else {
                            const p = this.predikat(final);
                            predikatEl.textContent = p;
                        }
                    }
                },
                finalFor(enrollmentId) {
                    const row = this.$el.closest('[data-row]');
                    if (row) return this.compute(row) ?? '—';
                    return '—';
                },
                predikatFor(enrollmentId) {
                    const row = this.$el.closest('[data-row]');
                    if (row) {
                        const final = this.compute(row);
                        return final === null ? '—' : this.predikat(final);
                    }
                    return '—';
                },
                predikat(score) {
                    if (score >= 90) return 'A';
                    if (score >= 80) return 'B';
                    if (score >= 70) return 'C';
                    if (score >= 60) return 'D';
                    return 'E';
                },
            };
        }
    </script>
</x-layouts.page>
