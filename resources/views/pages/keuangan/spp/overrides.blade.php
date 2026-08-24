<x-layouts.page
    :title="'Keringanan SPP'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="spp.overrides">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Keringanan SPP</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Setel nominal khusus per siswa (keringanan) untuk Tahun Ajaran {{ $tahun->name }}.
                    Nilai ini menggantikan nominal default saat mencatat pembayaran.
                </p>
            </div>
            <x-ui.badge variant="info" icon="calendar-days">{{ $tahun->name }}</x-ui.badge>
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

        <!-- Filter kelas -->
        <form method="GET" action="{{ route('spp.overrides') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas / Rombel</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-44" :options="$classes->pluck('name', 'id')" :selected="$classGroup?->id" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Tampilkan</x-ui.button>
            </div>
        </form>

        @if ($classGroup)
            <div class="mt-6 space-y-3">
                @forelse ($rows as $row)
                    @php
                        $override = $row['override'];
                    @endphp
                    <x-ui.sheet :title="$row['student']->displayName()" :subtitle="'NIS ' . $row['student']->nis" :pinned="$override !== null" :padding="false">
                        <form method="POST" action="{{ route('spp.overrides.store') }}" class="grid grid-cols-1 gap-4 px-5 py-4 sm:grid-cols-2 sm:px-6">
                            @csrf
                            <input type="hidden" name="student_enrollment_id" value="{{ $row['enrollment']->id }}">
                            <x-ui.field label="Nominal Khusus (Rp)" :error="$errors->first('nominal')">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-ink-faint">Rp</span>
                                    <input type="number" name="nominal" min="0" step="1" inputmode="numeric" required
                                        value="{{ old('nominal', $override?->nominal ?? '') }}"
                                        placeholder="Contoh: 50000"
                                        class="tabular w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 font-mono text-sm font-semibold text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                            </x-ui.field>
                            <x-ui.field label="Keterangan" :error="$errors->first('keterangan')">
                                <input type="text" name="keterangan" maxlength="255"
                                    value="{{ old('keterangan', $override?->keterangan ?? '') }}"
                                    placeholder="Alasan keringanan"
                                    class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            </x-ui.field>
                            <div class="sm:col-span-2 flex justify-end">
                                <x-ui.button type="submit" size="sm" :variant="$override ? 'secondary' : 'primary'" icon="check">
                                    {{ $override ? 'Perbarui Keringanan' : 'Set Keringanan' }}
                                </x-ui.button>
                            </div>
                        </form>
                    </x-ui.sheet>
                @empty
                    <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                        <p class="text-sm font-semibold text-ink">Tidak ada siswa aktif di kelas ini.</p>
                    </div>
                @endforelse
            </div>
        @else
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Pilih kelas untuk menampilkan daftar siswa.</p>
            </div>
        @endif
    </div>
</x-layouts.page>
