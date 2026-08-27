<x-layouts.page
    :title="'Arsip Ujian PPI'"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => 'Arsip Tahun Sebelumnya'],
    ]"
    active-route="ujianppi.arsip.index">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Arsip Tahun Sebelumnya</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Import rekap lama (sebelum sistem ini ada) menjadi periode <b>Diarsipkan</b> — read-only, hanya
                    untuk rekap historis. Kolom nilai per-aspek boleh kosong; minimal nilai akhir/predikat/lulus.
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow-down-tray" href="{{ route('ujianppi.arsip.template') }}">Unduh Template Excel</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-ui.sheet :title="'Periode Diarsipkan'" :padding="false">
                    @if ($archived->isEmpty())
                        <div class="px-5 py-10 text-center text-sm text-ink-faint">Belum ada periode yang diarsipkan.</div>
                    @else
                        <ul class="divide-y divide-rule/70">
                            @foreach ($archived as $period)
                                <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                                    <div>
                                        <p class="text-sm font-bold text-ink">{{ $period->judul }}</p>
                                        <p class="text-xs text-ink-soft">
                                            {{ $period->academicYear?->name }} · {{ $period->archives->count() }} siswa diarsipkan · read-only
                                        </p>
                                    </div>
                                    <x-ui.badge variant="neutral" :dot="false">Diarsipkan</x-ui.badge>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.sheet>
            </div>

            <div>
                <x-ui.sheet :title="'Import Rekap Lama'" :subtitle="'Kolom template: NISN, Nama, Rata P1, Rata P2, Rata P3, Nilai Hafalan, Nilai Akhir, Predikat, Status Lulus, Rank, Rombel.'">
                    <form method="POST" action="{{ route('ujianppi.arsip.preview') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block pb-1.5 text-xs font-bold text-ink">Judul Periode Arsip</label>
                            <x-ui.input name="judul" placeholder="mis. Arsip TP 2022/2023" :value="old('judul')" />
                        </div>
                        <div>
                            <label class="block pb-1.5 text-xs font-bold text-ink">Tahun Ajaran</label>
                            <x-ui.select name="academic_year_id" :options="$years->pluck('name', 'id')" :selected="old('academic_year_id')" placeholder="Pilih tahun ajaran…" />
                        </div>
                        <div>
                            <label class="block pb-1.5 text-xs font-bold text-ink">Rombel (opsional)</label>
                            <x-ui.input name="rombel" placeholder="mis. VI-A" :value="old('rombel')" />
                        </div>
                        <div>
                            <label class="block pb-1.5 text-xs font-bold text-ink">File Excel</label>
                            <input type="file" name="file" accept=".xlsx,.xls"
                                class="block w-full text-sm text-ink-soft file:mr-3 file:rounded-[var(--radius-control)] file:border-0 file:bg-primary-soft file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-strong hover:file:bg-primary-soft/70">
                        </div>
                        <x-ui.button type="submit" variant="primary" icon="eye" class="w-full">Preview Import</x-ui.button>
                    </form>
                </x-ui.sheet>
            </div>
        </div>
    </div>
</x-layouts.page>