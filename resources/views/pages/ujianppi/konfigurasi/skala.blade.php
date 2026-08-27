<x-layouts.page
    :title="'Skala Predikat — '.$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => $periode->judul, 'href' => route('ujianppi.periode.show', $periode)],
        ['label' => 'Skala Predikat'],
    ]"
    active-route="ujianppi.periode.index">

    <div class="mx-auto max-w-5xl">
        @php
            $variant = match ($periode->status) { 'draft' => 'neutral', 'setup' => 'info', 'berlangsung' => 'success', 'selesai' => 'warning', default => 'neutral' };
        @endphp
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Skala Predikat</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    {{ $periode->judul }} · <x-ui.badge :variant="$variant" :dot="false">{{ $periode->statusLabel() }}</x-ui.badge>
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="arrow-left" href="{{ route('ujianppi.periode.show', $periode) }}">Kembali</x-ui.button>
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
                    Skala terkunci — periode sudah <b>{{ $periode->statusLabel() }}</b>. Hanya Super Admin yang bisa
                    membuka kunci dari halaman detail periode.
                </x-ui.alert>
            </div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-ui.sheet :title="'Daftar Skala'" :subtitle="'Rentang tidak boleh tumpang tindih; urutan menentukan prioritas nilai akhir.'" :padding="false">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-rule-strong">
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft w-16">Urutan</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Predikat</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Rentang</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Deskripsi</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Tidak Lulus</th>
                                    @if ($editable)
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @forelse ($scales as $scale)
                                    <tr x-data="{ edit: false }" class="transition hover:bg-paper/60">
                                        <td class="px-4 py-3 tabular font-mono text-xs font-semibold text-ink-faint">{{ $scale->urutan }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center rounded-full bg-paper-deep px-2.5 py-0.5 text-xs font-bold text-ink ring-1 ring-inset ring-rule-strong">{{ $scale->predikat }}</span>
                                        </td>
                                        <td class="px-4 py-3 tabular text-ink">{{ $scale->nilai_min }} – {{ $scale->nilai_max }}</td>
                                        <td class="px-4 py-3 text-ink-soft">{{ $scale->deskripsi ?? '—' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($scale->is_tidak_lulus)
                                                <x-ui.badge variant="danger" :dot="false">Tidak Lulus</x-ui.badge>
                                            @else
                                                <span class="text-ink-faint">—</span>
                                            @endif
                                        </td>
                                        @if ($editable)
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex justify-end gap-2">
                                                    <x-ui.button variant="ghost" size="sm" @click="edit = !edit" x-text="edit ? 'Batal' : 'Ubah'"></x-ui.button>
                                                    <form method="POST" action="{{ route('ujianppi.konfigurasi.skala.destroy', [$periode, $scale]) }}"
                                                        onsubmit="return confirm('Hapus skala {{ $scale->predikat }}?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.button type="submit" variant="ghost" size="sm" icon="trash"></x-ui.button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif

                                        @if ($editable)
                                            <td colspan="6" x-show="edit" x-cloak class="bg-paper/60 px-4 py-4">
                                                <form method="POST" action="{{ route('ujianppi.konfigurasi.skala.update', [$periode, $scale]) }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                                                    @csrf
                                                    @method('PUT')
                                                    <div>
                                                        <label class="block pb-1.5 text-xs font-bold text-ink">Predikat</label>
                                                        <x-ui.input name="predikat" :value="$scale->predikat" class="!w-full" />
                                                    </div>
                                                    <div>
                                                        <label class="block pb-1.5 text-xs font-bold text-ink">Min</label>
                                                        <x-ui.input type="number" name="nilai_min" :value="$scale->nilai_min" min="0" max="100" />
                                                    </div>
                                                    <div>
                                                        <label class="block pb-1.5 text-xs font-bold text-ink">Max</label>
                                                        <x-ui.input type="number" name="nilai_max" :value="$scale->nilai_max" min="0" max="100" />
                                                    </div>
                                                    <div>
                                                        <label class="block pb-1.5 text-xs font-bold text-ink">Urutan</label>
                                                        <x-ui.input type="number" name="urutan" :value="$scale->urutan" min="1" />
                                                    </div>
                                                    <div class="lg:col-span-2">
                                                        <label class="block pb-1.5 text-xs font-bold text-ink">Deskripsi</label>
                                                        <x-ui.input name="deskripsi" :value="$scale->deskripsi" />
                                                    </div>
                                                    <label class="flex items-center gap-2 text-sm text-ink-soft">
                                                        <input type="checkbox" name="is_tidak_lulus" value="1" @checked($scale->is_tidak_lulus)
                                                            class="size-4 border-rule-strong text-danger focus:ring-danger">
                                                        Tandai TIDAK LULUS
                                                    </label>
                                                    <div class="lg:col-span-2">
                                                        <x-ui.button type="submit" variant="primary" size="sm" icon="check">Simpan</x-ui.button>
                                                    </div>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-10 text-center text-ink-faint">Belum ada skala predikat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-ui.sheet>
            </div>

            @if ($editable)
                <div>
                    <x-ui.sheet :title="'Tambah Skala'">
                        <form method="POST" action="{{ route('ujianppi.konfigurasi.skala.store', $periode) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block pb-1.5 text-xs font-bold text-ink">Predikat</label>
                                <x-ui.input name="predikat" placeholder="mis. A+" :value="old('predikat')" />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block pb-1.5 text-xs font-bold text-ink">Nilai Min</label>
                                    <x-ui.input type="number" name="nilai_min" value="0" min="0" max="100" />
                                </div>
                                <div>
                                    <label class="block pb-1.5 text-xs font-bold text-ink">Nilai Max</label>
                                    <x-ui.input type="number" name="nilai_max" value="59" min="0" max="100" />
                                </div>
                            </div>
                            <div>
                                <label class="block pb-1.5 text-xs font-bold text-ink">Urutan</label>
                                <x-ui.input type="number" name="urutan" :value="$scales->max('urutan') + 1 ?? 1" min="1" />
                            </div>
                            <div>
                                <label class="block pb-1.5 text-xs font-bold text-ink">Deskripsi</label>
                                <x-ui.input name="deskripsi" placeholder="mis. Sangat Baik" />
                            </div>
                            <label class="flex items-center gap-2 text-sm text-ink-soft">
                                <input type="checkbox" name="is_tidak_lulus" value="1"
                                    class="size-4 border-rule-strong text-danger focus:ring-danger">
                                Predikat ini = TIDAK LULUS
                            </label>
                            <x-ui.button type="submit" variant="primary" icon="plus" class="w-full">Tambah Skala</x-ui.button>
                        </form>
                    </x-ui.sheet>
                </div>
            @endif
        </div>
    </div>
</x-layouts.page>