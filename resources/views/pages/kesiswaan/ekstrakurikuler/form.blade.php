<x-layouts.page
    :title="$editing ? 'Ubah Ekskul' : 'Tambah Ekskul'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ekskul.edit">

    <div class="mx-auto max-w-3xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Ekstrakurikuler' : 'Tambah Ekstrakurikuler' }}</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Pembina dipilih dari pengguna ber-role Guru — pembina dapat mengelola anggota & presensi ekskulnya.
            </p>
        </div>

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <form method="POST"
            action="{{ $editing ? route('ekskul.update', $ekskul) : route('ekskul.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Detail Ekstrakurikuler">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-ui.field label="Nama" required :error="$errors->first('name')">
                            <x-ui.input name="name" :value="old('name', $editing ? $ekskul->name : '')" placeholder="Mis. Pramuka, Futsal, Hadroh" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Pembina" required :error="$errors->first('pembina_id')">
                        <x-ui.select name="pembina_id" :options="$pembinaOptions" :selected="old('pembina_id', $editing ? $ekskul->pembina_id : null)" placeholder="Pilih guru pembina…" />
                    </x-ui.field>
                    <x-ui.field label="Status" required :error="$errors->first('status')">
                        <x-ui.select name="status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" :selected="old('status', $editing ? $ekskul->status : 'aktif')" />
                    </x-ui.field>
                    <x-ui.field label="Hari" :error="$errors->first('hari')">
                        <x-ui.select name="hari" :options="collect($hariList)->mapWithKeys(fn ($h) => [$h => ucfirst($h)])->all()" :selected="old('hari', $editing ? $ekskul->hari : null)" placeholder="—" />
                    </x-ui.field>
                    <x-ui.field label="Waktu" :error="$errors->first('waktu')">
                        <x-ui.input type="time" name="waktu" :value="old('waktu', $editing && $ekskul->waktu ? substr($ekskul->waktu, 0, 5) : '')" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Lokasi" :error="$errors->first('lokasi')">
                            <x-ui.input name="lokasi" :value="old('lokasi', $editing ? $ekskul->lokasi : '')" placeholder="Mis. Lapangan utama" />
                        </x-ui.field>
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Deskripsi" :error="$errors->first('description')">
                            <textarea name="description" rows="3" placeholder="Keterangan singkat kegiatan…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description', $editing ? $ekskul->description : '') }}</textarea>
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('ekskul.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Sematkan & Simpan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
