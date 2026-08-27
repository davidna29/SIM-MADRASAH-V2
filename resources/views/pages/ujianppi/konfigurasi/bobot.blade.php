<x-layouts.page
    :title="'Bobot Penilaian — '.$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => $periode->judul, 'href' => route('ujianppi.periode.show', $periode)],
        ['label' => 'Bobot Penilaian'],
    ]"
    active-route="ujianppi.periode.index">

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Bobot Penilaian</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Persentase kontribusi tiap komponen ke nilai akhir — total wajib 100%.
                    Rumus: <code class="rounded bg-paper-deep px-1.5 py-0.5 text-xs">nilai_akhir = Σ(rata komponen × bobot / 100)</code>.
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow-left" href="{{ route('ujianppi.periode.show', $periode) }}">Kembali</x-ui.button>
        </div>
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

        @if (! $editable)
            <div class="mt-6">
                <x-ui.alert variant="warning" dismissible>
                    Bobot terkunci — periode sudah <b>{{ $periode->statusLabel() }}</b>.
                </x-ui.alert>
            </div>
        @endif

        <form method="POST" action="{{ route('ujianppi.konfigurasi.bobot.update', $periode) }}" class="mt-6"
            x-data="{ p1: {{ $periode->bobot_p1 }}, p2: {{ $periode->bobot_p2 }}, p3: {{ $periode->bobot_p3 }}, hafalan: {{ $periode->bobot_hafalan }} }">
            @csrf
            @method('PUT')

            <x-ui.sheet title="Komponen Nilai">
                <div class="space-y-4">
                    <div>
                        <label for="bobot_p1" class="block pb-1.5 text-xs font-bold text-ink">Bobot Penguji I</label>
                        <div class="flex items-center gap-3">
                            <x-ui.input type="number" id="bobot_p1" name="bobot_p1" min="0" max="100" x-model.number="p1" />
                            <span class="text-sm text-ink-soft">%</span>
                        </div>
                    </div>
                    <div>
                        <label for="bobot_p2" class="block pb-1.5 text-xs font-bold text-ink">Bobot Penguji II</label>
                        <div class="flex items-center gap-3">
                            <x-ui.input type="number" id="bobot_p2" name="bobot_p2" min="0" max="100" x-model.number="p2" />
                            <span class="text-sm text-ink-soft">%</span>
                        </div>
                    </div>
                    <div>
                        <label for="bobot_p3" class="block pb-1.5 text-xs font-bold text-ink">Bobot Penguji III</label>
                        <div class="flex items-center gap-3">
                            <x-ui.input type="number" id="bobot_p3" name="bobot_p3" min="0" max="100" x-model.number="p3" />
                            <span class="text-sm text-ink-soft">%</span>
                        </div>
                    </div>
                    <div>
                        <label for="bobot_hafalan" class="block pb-1.5 text-xs font-bold text-ink">Bobot Nilai Hafalan</label>
                        <div class="flex items-center gap-3">
                            <x-ui.input type="number" id="bobot_hafalan" name="bobot_hafalan" min="0" max="100" x-model.number="hafalan" />
                            <span class="text-sm text-ink-soft">%</span>
                        </div>
                    </div>
                </div>
            </x-ui.sheet>

            <div class="mt-4 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-ink">Total bobot</span>
                    <span class="tabular font-mono text-2xl font-bold" :class="(p1 + p2 + p3 + hafalan) === 100 ? 'text-success' : 'text-danger'"
                        x-text="(p1 + p2 + p3 + hafalan) + '%'"></span>
                </div>
                <p class="mt-1 text-xs text-ink-soft" x-show="(p1 + p2 + p3 + hafalan) !== 100" x-cloak>
                    Total harus tepat 100% agar periode bisa diubah ke status Berlangsung.
                </p>
            </div>

            <div class="mt-4 flex justify-end">
                <x-ui.button type="submit" variant="primary" icon="check" :disabled="! $editable">Simpan Bobot</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>