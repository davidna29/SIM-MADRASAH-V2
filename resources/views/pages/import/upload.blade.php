<x-layouts.page :title="$config['title']" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb" :active-route="$config['index']">
    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $config['title'] }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">{{ $config['description'] }}</p>
            </div>
            <x-ui.button variant="secondary" icon="arrow-down-tray" href="{{ route($module.'.import.template') }}">Unduh Template</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error)
                        <span class="block">{{ $error }}</span>
                    @endforeach
                </x-ui.alert>
            </div>
        @endif

        <x-ui.sheet class="mt-6">
            <form method="POST" action="{{ route($module.'.import.process') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <x-ui.field label="Berkas Excel" required hint="Format .xlsx, .xls, atau .csv — maksimal 5 MB." :error="$errors->first('file')">
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition file:mr-3 file:rounded-md file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-primary-strong hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary">
                </x-ui.field>

                <div class="rounded-sheet bg-paper p-4 ring-1 ring-inset ring-rule/60">
                    <p class="text-xs font-bold uppercase tracking-[0.08em] text-ink-faint">Kolom template</p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @foreach ($config['headings'] as $heading)
                            <x-ui.badge variant="neutral" :dot="false">{{ $heading }}</x-ui.badge>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs leading-relaxed text-ink-faint">
                        Baris pertama wajib berisi heading persis seperti di atas. Baris kedua dan seterusnya adalah data.
                        Setelah diunggah, data akan divalidasi per baris dan ditampilkan sebelum disimpan.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <x-ui.button variant="ghost" href="{{ route($config['index']) }}">Batal</x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="arrow-up-tray">Unggah & Validasi</x-ui.button>
                </div>
            </form>
        </x-ui.sheet>
    </div>
</x-layouts.page>
