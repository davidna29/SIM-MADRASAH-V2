<x-layouts.page
    :title="'Import Prestasi'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="prestasi.import">

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Import Prestasi dari Excel</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Unggah file <strong>.xlsx</strong> berisi data prestasi. Data divalidasi lalu ditampilkan untuk preview sebelum disimpan.
                </p>
            </div>
            <x-ui.button variant="secondary" icon="arrow-down-tray" href="{{ route('prestasi.template') }}">Unduh Template</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <form method="POST" action="{{ route('prestasi.import.process') }}" enctype="multipart/form-data" class="mt-6">
            @csrf
            <x-ui.sheet title="File Excel" subtitle="Kolom template: NIS, Jenis, Nama Kegiatan, Tingkat, Penyelenggara, Tanggal, Peringkat, Pembimbing, Status Publikasi.">
                <div class="flex flex-col items-center gap-4 py-4">
                    <input type="file" name="file" accept=".xlsx,.xls" required
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink file:mr-3 file:rounded file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-strong ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    <div class="flex w-full flex-col-reverse items-center justify-between gap-3 sm:flex-row">
                        <p class="text-xs text-ink-faint">
                            NIS harus siswa aktif tahun ajaran berjalan. Baris duplikat & tidak valid akan ditandai di preview.
                        </p>
                        <x-ui.button type="submit" variant="primary" icon="arrow-up-tray">Unggah & Preview</x-ui.button>
                    </div>
                </div>
            </x-ui.sheet>
        </form>
    </div>
</x-layouts.page>
