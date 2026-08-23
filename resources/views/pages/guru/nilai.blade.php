<x-layouts.page
    :title="'Input Nilai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="guru.nilai.edit">

    <div class="mx-auto max-w-5xl">
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

        <form method="POST" action="{{ route('guru.nilai.update', $assignment) }}" class="mt-6">
            @csrf
            <x-ui.sheet title="Lembar Nilai" subtitle="Nilai 0–100. Kosongkan untuk menghapus nilai siswa." :padding="false">
                <x-ui.table :headers="['NIS', 'Nama Siswa', 'Nilai', 'Predikat']">
                    <x-slot name="emptySlot">Tidak ada siswa terdaftar di kelas ini.</x-slot>
                    <x-slot>
                        @foreach ($enrollments as $enrollment)
                            @php
                                $nilai = $scores->get($enrollment->id)?->score;
                                $predikat = match (true) {
                                    $nilai === null => '—',
                                    $nilai >= 90 => 'A',
                                    $nilai >= 80 => 'B',
                                    $nilai >= 70 => 'C',
                                    $nilai >= 60 => 'D',
                                    default => 'E',
                                };
                                $predikatClass = match (true) {
                                    $nilai === null => 'neutral',
                                    $nilai >= 90 => 'success',
                                    $nilai >= 70 => 'info',
                                    $nilai >= 60 => 'warning',
                                    default => 'danger',
                                };
                            @endphp
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $enrollment->student->nis }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">
                                            {{ mb_substr($enrollment->student->name, 0, 1) }}
                                        </span>
                                        <span class="font-semibold text-ink">{{ $enrollment->student->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <label class="sr-only" for="nilai-{{ $enrollment->id }}">Nilai {{ $enrollment->student->name }}</label>
                                    <input id="nilai-{{ $enrollment->id }}" type="number" name="scores[{{ $enrollment->id }}]"
                                        value="{{ $nilai }}" min="0" max="100" step="1" inputmode="numeric"
                                        placeholder="—"
                                        class="tabular w-20 rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-center font-mono text-sm font-semibold text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="$predikatClass">{{ $predikat }}</x-ui.badge>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
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
</x-layouts.page>
