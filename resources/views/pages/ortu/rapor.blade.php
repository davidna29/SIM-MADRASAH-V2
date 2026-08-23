<x-layouts.page
    :title="'Rapor Anak'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb">

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Rapor {{ $student->name }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Rapor yang diterbitkan madrasah untuk tahun ajaran berjalan.
                </p>
            </div>
            <x-ui.button variant="primary" icon="arrow-down-tray" href="{{ route('ortu.rapor.unduh', $student) }}">Unduh PDF</x-ui.button>
        </div>

        <div class="mt-6">
            <x-ui.sheet title="Lembar Hasil Belajar" pinned ruled>
                <div class="text-center">
                    <p class="text-sm font-bold text-ink">MADRASAH TSANAWIYAH AL-IKHLAS MULIA</p>
                    <p class="mt-0.5 text-xs text-ink-soft">Laporan Hasil Belajar Siswa</p>
                </div>

                <dl class="mt-6 grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">NIS</dt>
                        <dd class="tabular mt-0.5 font-mono text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'nis') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Nama Siswa</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'siswa') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Kelas</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'kelas') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Tahun Ajaran / Semester</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'tahun') }} · {{ ucfirst(data_get($report->snapshot, 'semester')) }}</dd>
                    </div>
                </dl>

                <div class="mt-6 overflow-x-auto rounded-[var(--radius-control)] ring-1 ring-inset ring-rule">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-rule-strong">
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Mata Pelajaran</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Nilai</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule/70">
                            @php
                                $score = (int) data_get($report->snapshot, 'score');
                                $predikat = match (true) {
                                    $score >= 90 => 'A',
                                    $score >= 80 => 'B',
                                    $score >= 70 => 'C',
                                    $score >= 60 => 'D',
                                    default => 'E',
                                };
                            @endphp
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3 font-semibold text-ink">{{ data_get($report->snapshot, 'mapel') }}</td>
                                <td class="tabular px-4 py-3 text-right font-mono font-semibold text-ink">{{ $score }}</td>
                                <td class="px-4 py-3 text-right"><x-ui.badge variant="success">{{ $predikat }}</x-ui.badge></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="mt-6 border-t border-rule/70 pt-4 text-xs text-ink-faint">
                    Diterbitkan pada {{ \Carbon\Carbon::parse(data_get($report->snapshot, 'terbit_pada'))->isoFormat('D MMM YYYY, HH:mm') }} oleh {{ data_get($report->snapshot, 'guru') }}.
                </p>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
