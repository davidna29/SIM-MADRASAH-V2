<x-layouts.page
    :title="'Rapor Siswa'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb">

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Rapor Terbit</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Snapshot rapor yang tersimpan persis saat diterbitkan — dapat dicetak ulang tanpa berubah.
                </p>
            </div>
            <x-ui.button variant="primary" icon="arrow-down-tray" href="{{ route('guru.rapor.unduh', $report) }}">Unduh PDF</x-ui.button>
        </div>

        <div class="mt-6">
            <x-ui.sheet title="Rapor {{ data_get($report->snapshot, 'siswa') }}" pinned ruled>
                <dl class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
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
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Mata Pelajaran</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'mapel') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Guru Pengampu</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-ink">{{ data_get($report->snapshot, 'guru') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Nilai</dt>
                        <dd class="tabular mt-0.5 font-mono text-2xl font-bold text-ink">{{ data_get($report->snapshot, 'score') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Status</dt>
                        <dd class="mt-1.5"><x-ui.badge variant="success">Terbit</x-ui.badge></dd>
                    </div>
                </dl>
                <p class="mt-6 border-t border-rule/70 pt-4 text-xs text-ink-faint">
                    Diterbitkan pada {{ \Carbon\Carbon::parse(data_get($report->snapshot, 'terbit_pada'))->isoFormat('D MMM YYYY, HH:mm') }}.
                </p>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
