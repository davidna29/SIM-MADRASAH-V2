<x-layouts.page
    :title="$editing ? 'Ubah Prestasi' : 'Catat Prestasi'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="prestasi.edit">

    <div class="mx-auto max-w-3xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Prestasi' : 'Catat Prestasi' }}</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Prestasi otomatis berstatus <strong>Menunggu verifikasi</strong>.
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

        <!-- Pilih kelas untuk memuat daftar siswa -->
        <form method="GET"
            action="{{ $editing ? route('prestasi.edit', $achievement) : route('prestasi.create') }}"
            class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas / Rombel</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-44" :options="$classes->pluck('name', 'id')" :selected="$selectedClassId" placeholder="Pilih kelas…" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Muat Siswa</x-ui.button>
            </div>
        </form>

        <form method="POST"
            action="{{ $editing ? route('prestasi.update', $achievement) : route('prestasi.store') }}"
            enctype="multipart/form-data"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Data Prestasi">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Siswa" required :error="$errors->first('student_id')">
                        @if ($students)
                            <x-ui.select name="student_id" :options="$students" :selected="old('student_id', $editing ? $achievement->student_id : null)" placeholder="Pilih siswa…" />
                        @else
                            <p class="rounded-[var(--radius-control)] bg-paper px-3.5 py-2.5 text-sm text-ink-faint ring-1 ring-inset ring-rule-strong">Pilih kelas terlebih dahulu untuk memuat siswa.</p>
                        @endif
                    </x-ui.field>
                    <x-ui.field label="Jenis" required :error="$errors->first('jenis')">
                        <x-ui.select name="jenis" :options="['akademik' => 'Akademik', 'nonakademik' => 'Nonakademik']" :selected="old('jenis', $editing ? $achievement->jenis : null)" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Nama Kegiatan" required :error="$errors->first('nama_kegiatan')">
                            <x-ui.input name="nama_kegiatan" :value="old('nama_kegiatan', $editing ? $achievement->nama_kegiatan : '')" placeholder="Mis. Lomba Pidato Bahasa Arab" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Tingkat" required :error="$errors->first('tingkat')">
                        <x-ui.select name="tingkat" :options="collect($tingkatList)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])->all()" :selected="old('tingkat', $editing ? $achievement->tingkat : null)" placeholder="Pilih tingkat…" />
                    </x-ui.field>
                    <x-ui.field label="Peringkat" :error="$errors->first('peringkat')">
                        <x-ui.input name="peringkat" :value="old('peringkat', $editing ? $achievement->peringkat : '')" placeholder="Mis. Juara 1" />
                    </x-ui.field>
                    <x-ui.field label="Penyelenggara" :error="$errors->first('penyelenggara')">
                        <x-ui.input name="penyelenggara" :value="old('penyelenggara', $editing ? $achievement->penyelenggara : '')" placeholder="Pihak penyelenggara" />
                    </x-ui.field>
                    <x-ui.field label="Tanggal" :error="$errors->first('tanggal')">
                        <x-ui.input type="date" name="tanggal" :value="old('tanggal', $editing && $achievement->tanggal ? $achievement->tanggal->format('Y-m-d') : '')" />
                    </x-ui.field>
                    <x-ui.field label="Pembimbing" :error="$errors->first('pembimbing')">
                        <x-ui.input name="pembimbing" :value="old('pembimbing', $editing ? $achievement->pembimbing : '')" />
                    </x-ui.field>
                    <x-ui.field label="Status Publikasi" required :error="$errors->first('status_publikasi')">
                        <x-ui.select name="status_publikasi" :options="['publik' => 'Publik', 'internal' => 'Internal']" :selected="old('status_publikasi', $editing ? $achievement->status_publikasi : 'publik')" />
                    </x-ui.field>
                    <x-ui.field label="Sertifikat" hint="PDF/gambar, maks. 2 MB." :error="$errors->first('sertifikat')">
                        <input type="file" name="sertifikat"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink file:mr-3 file:rounded file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-strong ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                    <x-ui.field label="Foto" hint="Gambar kegiatan, maks. 2 MB." :error="$errors->first('foto')">
                        <input type="file" name="foto"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink file:mr-3 file:rounded file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-strong ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('prestasi.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Sematkan & Simpan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
