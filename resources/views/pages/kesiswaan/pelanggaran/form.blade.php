<x-layouts.page
    :title="$editing ? 'Ubah Pelanggaran' : 'Catat Pelanggaran'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pelanggaran.edit">

    <div class="mx-auto max-w-3xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Pelanggaran' : 'Catat Pelanggaran' }}</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Pelanggaran baru berstatus <strong>Proses</strong> hingga diselesaikan oleh wakamad kesiswaan / guru BK.
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
            action="{{ $editing ? route('pelanggaran.edit', $offense) : route('pelanggaran.create') }}"
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
            action="{{ $editing ? route('pelanggaran.update', $offense) : route('pelanggaran.store') }}"
            enctype="multipart/form-data"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Data Pelanggaran">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Siswa" required :error="$errors->first('student_id')">
                        @if ($students)
                            <x-ui.select name="student_id" :options="$students" :selected="old('student_id', $editing ? $offense->student_id : null)" placeholder="Pilih siswa…" />
                        @else
                            <p class="rounded-[var(--radius-control)] bg-paper px-3.5 py-2.5 text-sm text-ink-faint ring-1 ring-inset ring-rule-strong">Pilih kelas terlebih dahulu untuk memuat siswa.</p>
                        @endif
                    </x-ui.field>
                    <x-ui.field label="Kategori" required :error="$errors->first('kategori')">
                        <x-ui.input name="kategori" :value="old('kategori', $editing ? $offense->kategori : '')" placeholder="Mis. Terlambat, Membolos, Berkelahi" />
                    </x-ui.field>
                    <x-ui.field label="Tingkat" required :error="$errors->first('tingkat')">
                        <x-ui.select name="tingkat" :options="collect($tingkatList)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])->all()" :selected="old('tingkat', $editing ? $offense->tingkat : null)" placeholder="Pilih tingkat…" />
                    </x-ui.field>
                    <x-ui.field label="Poin" required :error="$errors->first('poin')">
                        <x-ui.input type="number" name="poin" min="0" max="100" :value="old('poin', $editing ? $offense->poin : '')" />
                    </x-ui.field>
                    <x-ui.field label="Tanggal Kejadian" required :error="$errors->first('tanggal_kejadian')">
                        <x-ui.input type="date" name="tanggal_kejadian" :value="old('tanggal_kejadian', $editing ? $offense->tanggal_kejadian->format('Y-m-d') : now()->format('Y-m-d'))" />
                    </x-ui.field>
                    <x-ui.field label="Pelapor" :error="$errors->first('pelapor')">
                        <x-ui.input name="pelapor" :value="old('pelapor', $editing ? $offense->pelapor : '')" placeholder="Nama pelapor / petugas" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Kronologi" required :error="$errors->first('kronologi')">
                            <textarea name="kronologi" rows="3" required placeholder="Uraian kejadian…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('kronologi', $editing ? $offense->kronologi : '') }}</textarea>
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Tindakan" :error="$errors->first('tindakan')">
                        <x-ui.input name="tindakan" :value="old('tindakan', $editing ? $offense->tindakan : '')" placeholder="Tindakan pembinaan" />
                    </x-ui.field>
                    <x-ui.field label="Surat Peringatan" :error="$errors->first('surat_peringatan')">
                        <x-ui.select name="surat_peringatan" :options="['sp1' => 'SP 1', 'sp2' => 'SP 2', 'sp3' => 'SP 3']" :selected="old('surat_peringatan', $editing ? $offense->surat_peringatan : null)" placeholder="Tidak ada" />
                    </x-ui.field>
                    <x-ui.field label="Status Penyelesaian" required :error="$errors->first('status_penyelesaian')">
                        <x-ui.select name="status_penyelesaian" :options="['proses' => 'Proses', 'selesai' => 'Selesai', 'dibebaskan' => 'Dibebaskan']" :selected="old('status_penyelesaian', $editing ? $offense->status_penyelesaian : 'proses')" />
                    </x-ui.field>
                    <x-ui.field label="Bukti" hint="PDF/gambar, maks. 2 MB." :error="$errors->first('bukti')">
                        <input type="file" name="bukti"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink file:mr-3 file:rounded file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-strong ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                    <label class="flex items-center gap-2 self-end rounded-[var(--radius-control)] bg-paper px-3.5 py-2.5 text-sm font-medium text-ink ring-1 ring-inset ring-rule-strong">
                        <input type="checkbox" name="pemanggilan_ortu" value="1"
                            @checked(old('pemanggilan_ortu', $editing ? $offense->pemanggilan_ortu : false))
                            class="size-4 border-rule-strong text-primary focus:ring-primary">
                        Pemanggilan orang tua
                    </label>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('pelanggaran.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Sematkan & Simpan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
