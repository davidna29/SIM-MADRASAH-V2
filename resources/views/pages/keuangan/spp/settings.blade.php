<x-layouts.page
    :title="'Nominal SPP'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="spp.settings">

    <div class="mx-auto max-w-3xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Nominal SPP Default</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Nominal default SPP per tahun ajaran. Nilai ini menjadi acuan saat bendahara mencatat
                pembayaran, kecuali siswa memiliki keringanan khusus.
            </p>
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

        <form method="POST" action="{{ route('spp.settings.store') }}" class="mt-6">
            @csrf
            <x-ui.sheet title="Nominal per Tahun Ajaran" subtitle="Nominal dalam Rupiah — kosongkan untuk melewatkan tahun tersebut." :padding="false">
                <div class="divide-y divide-rule/70">
                    @foreach ($years as $year)
                        @php $setting = $settings->get($year->id); @endphp
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 sm:px-6">
                            <div>
                                <p class="text-sm font-bold text-ink">{{ $year->name }}</p>
                                <p class="mt-0.5 text-xs text-ink-faint">Semester {{ ucfirst($year->semester) }} · {{ $year->is_active ? 'Aktif' : 'Arsip' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-ink-faint">Rp</span>
                                <input type="number" name="nominal[{{ $year->id }}]" min="0" step="1000" inputmode="numeric"
                                    value="{{ old('nominal.'.$year->id, $setting?->nominal ?? '') }}"
                                    placeholder="—"
                                    class="tabular w-48 rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 font-mono text-sm font-semibold text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-end border-t border-rule/70 px-5 py-4 sm:px-6">
                    <x-ui.button type="submit" variant="primary" icon="check">Simpan Nominal SPP</x-ui.button>
                </div>
            </x-ui.sheet>
        </form>
    </div>
</x-layouts.page>
