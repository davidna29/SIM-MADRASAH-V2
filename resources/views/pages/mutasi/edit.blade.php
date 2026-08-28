<x-layouts.page
    :title="'Edit Pendaftar Pindah'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mutasi.edit">

    <div class="mx-auto max-w-3xl">
        <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Edit Pendaftar Pindah</h1>
        <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">{{ $registration->registration_no }} — perbaiki data bila diperlukan.</p>

        @include('pages.mutasi.partials.steps', ['active' => 'mutasi.edit'])

        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>@foreach ($errors->all() as $error) {{ $error }} @endforeach</x-ui.alert></div>
        @endif

        <form method="POST" action="{{ route('mutasi.update', $registration) }}" class="mt-6">
            @csrf
            @method('PUT')

            <x-ui.form-section title="Identitas & Asal">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Nama Lengkap" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $registration->name)" required maxlength="100" />
                    </x-ui.field>
                    <x-ui.field label="NIK" required :error="$errors->first('nik')">
                        <x-ui.input name="nik" :value="old('nik', $registration->nik)" required maxlength="16" />
                    </x-ui.field>
                    <x-ui.field label="NISN" :error="$errors->first('nisn')">
                        <x-ui.input name="nisn" :value="old('nisn', $registration->nisn)" maxlength="10" />
                    </x-ui.field>
                    <x-ui.field label="NIS Asal" :error="$errors->first('nis_asal')">
                        <x-ui.input name="nis_asal" :value="old('nis_asal', $registration->nis_asal)" maxlength="20" />
                    </x-ui.field>
                    <x-ui.field label="Jenis Kelamin" required :error="$errors->first('gender')">
                        <x-ui.select name="gender" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :selected="old('gender', $registration->gender)" />
                    </x-ui.field>
                    <x-ui.field label="Agama" required :error="$errors->first('religion')">
                        <x-ui.input name="religion" :value="old('religion', $registration->religion)" maxlength="20" />
                    </x-ui.field>
                    <x-ui.field label="Tempat Lahir" :error="$errors->first('birth_place')">
                        <x-ui.input name="birth_place" :value="old('birth_place', $registration->birth_place)" maxlength="60" />
                    </x-ui.field>
                    <x-ui.field label="Tanggal Lahir" :error="$errors->first('birth_date')">
                        <x-ui.input type="date" name="birth_date" :value="old('birth_date', $registration->birth_date?->format('Y-m-d'))" />
                    </x-ui.field>
                    <x-ui.field label="Madrasah Asal" required :error="$errors->first('origin_school')">
                        <x-ui.input name="origin_school" :value="old('origin_school', $registration->origin_school)" required maxlength="100" />
                    </x-ui.field>
                    <x-ui.field label="Kelas Asal" required :error="$errors->first('kelas_asal')">
                        <x-ui.input name="kelas_asal" :value="old('kelas_asal', $registration->kelas_asal)" required maxlength="20" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Tujuan Mutasi">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Kelas Tujuan" required :error="$errors->first('kelas_tujuan')">
                        <x-ui.input name="kelas_tujuan" :value="old('kelas_tujuan', $registration->kelas_tujuan)" required maxlength="20" />
                    </x-ui.field>
                    <x-ui.field label="Tanggal Mutasi" :error="$errors->first('tanggal_mutasi')">
                        <x-ui.input type="date" name="tanggal_mutasi" :value="old('tanggal_mutasi', $registration->tanggal_mutasi?->format('Y-m-d'))" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Alasan Pindah" required :error="$errors->first('alasan_pindah')">
                            <textarea name="alasan_pindah" rows="3" required maxlength="1000"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('alasan_pindah', $registration->alasan_pindah) }}</textarea>
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Dokumen (tautan Google Drive)">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Surat Rekomendasi Madrasah" required :error="$errors->first('scanned_rekomendasi')">
                        <x-ui.input name="scanned_rekomendasi" :value="old('scanned_rekomendasi', $registration->scanned_rekomendasi)" required maxlength="500" />
                    </x-ui.field>
                    <x-ui.field label="Rapor / Transkrip" :error="$errors->first('scanned_rapor')">
                        <x-ui.input name="scanned_rapor" :value="old('scanned_rapor', $registration->scanned_rapor)" maxlength="500" />
                    </x-ui.field>
                    <x-ui.field label="Kartu Keluarga" :error="$errors->first('scanned_kk')">
                        <x-ui.input name="scanned_kk" :value="old('scanned_kk', $registration->scanned_kk)" maxlength="500" />
                    </x-ui.field>
                    <x-ui.field label="Akta Kelahiran" :error="$errors->first('scanned_akta')">
                        <x-ui.input name="scanned_akta" :value="old('scanned_akta', $registration->scanned_akta)" maxlength="500" />
                    </x-ui.field>
                    <x-ui.field label="Pas Foto" :error="$errors->first('scanned_photo')">
                        <x-ui.input name="scanned_photo" :value="old('scanned_photo', $registration->scanned_photo)" maxlength="500" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('mutasi.show', $registration) }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Simpan Perubahan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>