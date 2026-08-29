<x-layouts.page
    :title="'Nilai '.$enrollment->student->name"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="akademik.rapor.siswa">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $enrollment->student->name }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    NIS {{ $enrollment->student->nis }} · Kelas {{ $enrollment->classGroup->grade_level }} {{ $enrollment->classGroup->name }}
                    · Semester {{ ucfirst($semester) }} · {{ $tahun->name }}.
                </p>
            </div>
            <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('akademik.rapor.kelas', $enrollment->classGroup) }}">Kembali ke Kelas</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <div class="mt-6">
            <x-ui.sheet title="Nilai Akhir per Mata Pelajaran" :padding="false">
                @if ($scores->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-ink-faint">
                        Belum ada nilai untuk siswa ini pada semester {{ ucfirst($semester) }}.
                    </div>
                @else
                    <x-ui.table :headers="['Mata Pelajaran', 'Nilai Akhir', 'Predikat']">
                        <x-slot>
                            @foreach ($scores as $score)
                                @php
                                    $final = $score->score;
                                    $predikat = $final !== null ? App\Support\Rapor::predikat((int) $final) : null;
                                @endphp
                                <tr class="transition hover:bg-paper/60">
                                    <td class="px-4 py-3 font-semibold text-ink">
                                        {{ $score->subject->name }}
                                        <span class="block text-xs font-mono text-ink-faint">{{ $score->subject->code }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($final !== null)
                                            <span class="tabular font-mono text-sm font-bold text-ink">{{ $final }}</span>
                                        @else
                                            <span class="text-ink-faint">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($predikat !== null)
                                            <x-ui.badge>{{ $predikat }}</x-ui.badge>
                                        @else
                                            <span class="text-ink-faint">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-ui.table>
                @endif
            </x-ui.sheet>
        </div>

        @php
            $withComponents = $scores->filter(fn ($s) => $s->componentValues->isNotEmpty())->isNotEmpty();
        @endphp
        @if ($withComponents)
            <div class="mt-6">
                <x-ui.sheet title="Rincian Nilai per Komponen" :padding="false">
                    <x-ui.table :headers="['Mata Pelajaran', 'Komponen', 'Bobot', 'Nilai']">
                        <x-slot>
                            @foreach ($scores as $score)
                                @if ($score->componentValues->isNotEmpty())
                                    <tr class="bg-paper/40">
                                        <td class="px-4 py-3 font-semibold text-ink" rowspan="{{ $score->componentValues->count() + 1 }}">
                                            {{ $score->subject->name }}
                                        </td>
                                    </tr>
                                    @foreach ($score->componentValues as $value)
                                        <tr class="transition hover:bg-paper/60">
                                            <td class="px-4 py-3 text-ink-soft">{{ $value->scoreComponent->name }}</td>
                                            <td class="px-4 py-3 tabular font-mono text-sm text-ink-soft">{{ trim($value->scoreComponent->weight) }}%</td>
                                            <td class="px-4 py-3">
                                                @if ($value->value !== null)
                                                    <span class="tabular font-mono text-sm font-semibold text-ink">{{ $value->value }}</span>
                                                @else
                                                    <span class="text-ink-faint">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </x-slot>
                    </x-ui.table>
                </x-ui.sheet>
            </div>
        @endif

        <div class="mt-6">
            <x-ui.alert variant="info">
                <p class="text-sm">
                    <strong class="font-bold">Predikat:</strong> A ≥ 90 · B ≥ 80 · C ≥ 70 · D ≥ 60 · E &lt; 60.
                </p>
            </x-ui.alert>
        </div>
    </div>
</x-layouts.page>
