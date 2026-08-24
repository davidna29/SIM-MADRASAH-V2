<x-layouts.page
    :title="$editing ? 'Ubah Agenda' : 'Tambah Agenda'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.agenda.edit">

    <div class="mx-auto max-w-3xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Agenda' : 'Tambah Agenda / Pengumuman' }}</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Agenda berisi tanggal/waktu & lokasi; pengumuman cukup judul dan isi.
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
            action="{{ $editing ? route('cms.agenda.update', $agenda) : route('cms.agenda.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Detail Agenda / Pengumuman">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-ui.field label="Judul" required :error="$errors->first('title')">
                            <x-ui.input name="title" :value="old('title', $editing ? $agenda->title : '')" placeholder="Judul kegiatan/pengumuman" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Jenis" required :error="$errors->first('jenis')">
                        <x-ui.select name="jenis" :options="['agenda' => 'Agenda Kegiatan', 'pengumuman' => 'Pengumuman']" :selected="old('jenis', $editing ? $agenda->jenis : null)" />
                    </x-ui.field>
                    <x-ui.field label="Status" required :error="$errors->first('status')">
                        <x-ui.select name="status" :options="['aktif' => 'Aktif', 'arsip' => 'Arsip']" :selected="old('status', $editing ? $agenda->status : 'aktif')" />
                    </x-ui.field>
                    <x-ui.field label="Tanggal" :error="$errors->first('tanggal')">
                        <x-ui.input type="date" name="tanggal" :value="old('tanggal', $editing && $agenda->tanggal ? $agenda->tanggal->format('Y-m-d') : '')" />
                    </x-ui.field>
                    <x-ui.field label="Waktu" :error="$errors->first('waktu')">
                        <x-ui.input type="time" name="waktu" :value="old('waktu', $editing && $agenda->waktu ? $agenda->waktu->format('H:i') : '')" />
                    </x-ui.field>
                    <x-ui.field label="Lokasi" :error="$errors->first('lokasi')">
                        <x-ui.input name="lokasi" :value="old('lokasi', $editing ? $agenda->lokasi : '')" placeholder="Mis. Aula Madrasah" />
                    </x-ui.field>
                    <x-ui.field label="Penanggung Jawab" :error="$errors->first('penanggung_jawab')">
                        <x-ui.input name="penanggung_jawab" :value="old('penanggung_jawab', $editing ? $agenda->penanggung_jawab : '')" placeholder="Nama / panitia" />
                    </x-ui.field>
                    <x-ui.field label="Target" required :error="$errors->first('target')">
                        <x-ui.select name="target" :options="['publik' => 'Publik', 'internal' => 'Internal']" :selected="old('target', $editing ? $agenda->target : 'publik')" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Isi" :error="$errors->first('isi')">
                            <textarea name="isi" rows="3" placeholder="Keterangan singkat…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('isi', $editing ? $agenda->isi : '') }}</textarea>
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Tampil Mulai" required hint="Masa tampil di website." :error="$errors->first('tampil_mulai')">
                        <x-ui.input type="date" name="tampil_mulai" :value="old('tampil_mulai', $editing ? $agenda->tampil_mulai->format('Y-m-d') : now()->format('Y-m-d'))" />
                    </x-ui.field>
                    <x-ui.field label="Tampil Sampai" hint="Kosongkan = tanpa batas." :error="$errors->first('tampil_selesai')">
                        <x-ui.input type="date" name="tampil_selesai" :value="old('tampil_selesai', $editing && $agenda->tampil_selesai ? $agenda->tampil_selesai->format('Y-m-d') : '')" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('cms.agenda.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Sematkan & Simpan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
