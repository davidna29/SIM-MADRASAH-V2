<x-layouts.page
    :title="'Penugasan Mengajar'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="guru.penugasan">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Penugasan Mengajar</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Kelas dan mata pelajaran yang menjadi penugasan Anda pada Tahun Ajaran {{ $tahun->name }}.
                    Hanya penugasan ini yang boleh Anda isi nilainya.
                </p>
            </div>
            <x-ui.badge variant="info" icon="calendar-days">{{ $tahun->name }} · Semester {{ ucfirst($tahun->semester) }}</x-ui.badge>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($assignments as $assignment)
                @php
                    $terisi = $assignment->classGroup->enrollments()->where('academic_year_id', $tahun->id)->count();
                @endphp
                <x-ui.sheet
                    :title="$assignment->subject->name"
                    :subtitle="'Kelas ' . $assignment->classGroup->name . ' · Kode ' . $assignment->subject->code"
                    pinned
                    :actions="view('components.ui.button', ['variant' => 'primary', 'size' => 'sm', 'icon' => 'clipboard-document-list', 'href' => route('guru.nilai.edit', $assignment)])->withSlot('Input Nilai')->render()">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <x-ui.badge variant="info" icon="academic-cap">{{ $terisi }} siswa terdaftar</x-ui.badge>
                            <x-ui.badge variant="success" icon="check">Tahun ajaran aktif</x-ui.badge>
                        </div>
                        <form method="POST" action="{{ route('guru.nilai.terbitkan', $assignment) }}">
                            @csrf
                            <x-ui.button type="submit" variant="secondary" size="sm" icon="document-arrow-up">Terbitkan Rapor Kelas Ini</x-ui.button>
                        </form>
                    </div>
                </x-ui.sheet>
            @empty
                <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                    <p class="text-sm font-semibold text-ink">Belum ada penugasan untuk Anda.</p>
                    <p class="mt-1 text-xs text-ink-faint">Hubungi admin madrasah untuk membuat penugasan mengajar.</p>
                </div>
            @endforelse
        </div>

        @if ($reports->isNotEmpty())
            <div class="mt-8">
                <x-ui.sheet title="Rapor Terbit" subtitle="Snapshot yang tersimpan — versi baru ditambahkan, versi lama tidak pernah ditimpa" pinned ruled>
                    <ul class="divide-y divide-rule/70">
                        @foreach ($reports as $report)
                            <li class="flex flex-wrap items-center justify-between gap-3 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-success-soft text-[11px] font-extrabold text-success">
                                        {{ mb_substr(data_get($report->snapshot, 'siswa'), 0, 1) }}
                                    </span>
                                    <div>
                                        <p class="text-[13px] font-semibold text-ink">{{ data_get($report->snapshot, 'siswa') }}</p>
                                        <p class="tabular mt-0.5 font-mono text-[11px] text-ink-faint">
                                            NIS {{ data_get($report->snapshot, 'nis') }} · v{{ $report->version }}
                                            · {{ \Carbon\Carbon::parse(data_get($report->snapshot, 'terbit_pada'))->isoFormat('D MMM HH:mm') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <x-ui.button size="sm" variant="secondary" icon="eye" href="{{ route('guru.rapor', $report) }}">Lihat</x-ui.button>
                                    <x-ui.button size="sm" variant="ghost" icon="arrow-down-tray" href="{{ route('guru.rapor.unduh', $report) }}">PDF</x-ui.button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.sheet>
            </div>
        @endif
    </div>
</x-layouts.page>
