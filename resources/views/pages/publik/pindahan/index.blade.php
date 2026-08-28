<x-layouts.publik :title="'Mutasi Masuk - Pendaftaran Pindahan'">
    @php
        $stepFields = [
            1 => ['name','nik','nisn','nis_asal','gender','religion','birth_place','birth_date','origin_school','origin_nsm','origin_npsn','origin_address','kelas_asal'],
            2 => ['kelas_tujuan','alasan_pindah','tanggal_mutasi','address','province','city','district','village','rt','rw','postal_code','student_phone','student_email'],
            3 => ['father_name','father_nik','father_job','father_phone','mother_name','mother_nik','mother_job','mother_phone','guardian_name','guardian_nik','guardian_phone'],
            4 => ['scanned_rekomendasi','scanned_rapor','scanned_kk','scanned_akta','scanned_photo'],
        ];
        $firstErrorStep = 1;
        if ($errors->any()) {
            foreach ($stepFields as $s => $fields) {
                foreach ($fields as $f) {
                    if ($errors->has($f)) { $firstErrorStep = $s; break 2; }
                }
            }
        }
    @endphp
    <div class="mx-auto max-w-4xl" x-data="{ step: {{ $firstErrorStep }}, totalSteps: 4 }">

        <div class="mb-8 text-center">
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pendaftaran Siswa Pindahan</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Isi formulir pendaftaran pindahan dengan lengkap. Setiap langkah ditandai bintang (<span class="text-danger">*</span>) wajib.
            </p>
            @if ($tahun)
                <p class="mt-1 text-xs font-semibold text-primary">{{ $tahun->name ?? $tahun->year ?? '' }}</p>
            @endif
        </div>

        {{-- Step Indicator --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <template x-for="i in totalSteps" :key="i">
                    <div class="flex flex-1 items-center">
                        <div class="flex flex-col items-center">
                            <div
                                class="flex size-8 items-center justify-center rounded-full text-xs font-bold transition duration-200"
                                :class="step === i ? 'bg-primary text-white shadow-sm' : step > i ? 'bg-success text-white' : 'bg-paper-deep text-ink-faint ring-1 ring-inset ring-rule'"
                            >
                                <template x-if="step > i">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <template x-if="step <= i"><span x-text="i"></span></template>
                            </div>
                        </div>
                        <div class="mx-2 hidden h-0.5 flex-1 rounded-full transition duration-200 sm:block"
                            :class="step > i ? 'bg-success' : 'bg-paper-deep'" x-show="i < totalSteps"></div>
                    </div>
                </template>
            </div>
            <div class="mt-2 text-center">
                <span class="text-xs font-semibold text-ink-soft">Langkah <span x-text="step"></span> / <span x-text="totalSteps"></span></span>
            </div>
        </div>

        @if ($errors->any())
            <x-ui.alert variant="danger" class="mb-6">
                <p class="font-semibold">Terdapat kesalahan pada pengisian formulir.</p>
                <ul class="mt-1 list-disc list-inside text-xs">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('pindahan.store') }}" enctype="application/x-www-form-urlencoded" novalidate>
            @csrf

            {{-- ══ STEP 1: Identitas & Asal ══ --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <x-ui.sheet title="A. Identitas Siswa & Asal" subtitle="Data pribadi beserta madrasah / sekolah asal">
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <x-ui.field label="Nama Lengkap" required :error="$errors->first('name')">
                                <x-ui.input name="name" placeholder="NAMA LENGKAP SESUAI AKTA" :value="old('name')" required maxlength="100" />
                            </x-ui.field>
                            <x-ui.field label="NIK" required hint="16 digit" :error="$errors->first('nik')">
                                <x-ui.input name="nik" placeholder="Nomor Induk Kependudukan" :value="old('nik')" required maxlength="16" />
                            </x-ui.field>
                            <x-ui.field label="NISN" :error="$errors->first('nisn')">
                                <x-ui.input name="nisn" placeholder="10 digit (opsional)" :value="old('nisn')" maxlength="10" />
                            </x-ui.field>
                            <x-ui.field label="NIS Asal" :error="$errors->first('nis_asal')">
                                <x-ui.input name="nis_asal" placeholder="NIS di madrasah asal" :value="old('nis_asal')" maxlength="20" />
                            </x-ui.field>
                            <x-ui.field label="Jenis Kelamin" required :error="$errors->first('gender')">
                                <x-ui.select name="gender" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :selected="old('gender')" placeholder="Pilih…" />
                            </x-ui.field>
                            <x-ui.field label="Agama" required :error="$errors->first('religion')">
                                <x-ui.select name="religion" :options="['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Khonghucu' => 'Konghucu']" :selected="old('religion', 'Islam')" />
                            </x-ui.field>
                            <x-ui.field label="Tempat Lahir" :error="$errors->first('birth_place')">
                                <x-ui.input name="birth_place" :value="old('birth_place')" maxlength="60" />
                            </x-ui.field>
                            <x-ui.field label="Tanggal Lahir" :error="$errors->first('birth_date')">
                                <x-ui.input type="date" name="birth_date" :value="old('birth_date')" />
                            </x-ui.field>
                        </div>
                        <p class="pt-2 text-xs font-bold uppercase tracking-wide text-ink-soft">Madrasah / Sekolah Asal</p>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <x-ui.field label="Nama Madrasah / Sekolah Asal" required :error="$errors->first('origin_school')">
                                <x-ui.input name="origin_school" placeholder="Mis. MTs Negeri 1 Kota" :value="old('origin_school')" required maxlength="100" />
                            </x-ui.field>
                            <x-ui.field label="NSM Asal" :error="$errors->first('origin_nsm')">
                                <x-ui.input name="origin_nsm" placeholder="12 digit (opsional)" :value="old('origin_nsm')" maxlength="12" />
                            </x-ui.field>
                            <x-ui.field label="NPSN Asal" :error="$errors->first('origin_npsn')">
                                <x-ui.input name="origin_npsn" placeholder="8 digit (opsional)" :value="old('origin_npsn')" maxlength="8" />
                            </x-ui.field>
                            <x-ui.field label="Kelas Asal" required :error="$errors->first('kelas_asal')">
                                <x-ui.input name="kelas_asal" placeholder="Mis. II-A" :value="old('kelas_asal')" required maxlength="20" />
                            </x-ui.field>
                            <div class="sm:col-span-2">
                                <x-ui.field label="Alamat Madrasah Asal" :error="$errors->first('origin_address')">
                                    <x-ui.input name="origin_address" :value="old('origin_address')" maxlength="255" />
                                </x-ui.field>
                            </div>
                        </div>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ══ STEP 2: Tujuan Mutasi & Alamat ══ --}}
            <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <x-ui.sheet title="B. Tujuan Mutasi & Alamat Tinggal">
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <x-ui.field label="Kelas Tujuan" required :error="$errors->first('kelas_tujuan')">
                                <x-ui.input name="kelas_tujuan" placeholder="Mis. III-A" :value="old('kelas_tujuan')" required maxlength="20" />
                            </x-ui.field>
                            <x-ui.field label="Tanggal Mutasi" :error="$errors->first('tanggal_mutasi')">
                                <x-ui.input type="date" name="tanggal_mutasi" :value="old('tanggal_mutasi')" />
                            </x-ui.field>
                        </div>
                        <x-ui.field label="Alasan Pindah" required :error="$errors->first('alasan_pindah')">
                            <textarea name="alasan_pindah" rows="3" required maxlength="1000"
                                placeholder="Jelaskan alasan pindah (mis. pindah domisili / orang tua bertugas)…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('alasan_pindah') }}</textarea>
                        </x-ui.field>
                        <p class="pt-2 text-xs font-bold uppercase tracking-wide text-ink-soft">Alamat Tinggal Sekarang</p>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <x-ui.field label="Alamat" required :error="$errors->first('address')">
                                    <x-ui.input name="address" :value="old('address')" required maxlength="255" />
                                </x-ui.field>
                            </div>
                            <x-ui.field label="Provinsi" required :error="$errors->first('province')">
                                <x-ui.input name="province" :value="old('province')" required maxlength="60" />
                            </x-ui.field>
                            <x-ui.field label="Kota/Kabupaten" required :error="$errors->first('city')">
                                <x-ui.input name="city" :value="old('city')" required maxlength="60" />
                            </x-ui.field>
                            <x-ui.field label="Kecamatan" required :error="$errors->first('district')">
                                <x-ui.input name="district" :value="old('district')" required maxlength="60" />
                            </x-ui.field>
                            <x-ui.field label="Kelurahan/Desa" :error="$errors->first('village')">
                                <x-ui.input name="village" :value="old('village')" maxlength="60" />
                            </x-ui.field>
                            <x-ui.field label="RT" :error="$errors->first('rt')">
                                <x-ui.input name="rt" :value="old('rt')" maxlength="3" />
                            </x-ui.field>
                            <x-ui.field label="RW" :error="$errors->first('rw')">
                                <x-ui.input name="rw" :value="old('rw')" maxlength="3" />
                            </x-ui.field>
                            <x-ui.field label="Kode Pos" :error="$errors->first('postal_code')">
                                <x-ui.input name="postal_code" :value="old('postal_code')" maxlength="5" />
                            </x-ui.field>
                            <x-ui.field label="Nomor HP / WA" required :error="$errors->first('student_phone')">
                                <x-ui.input name="student_phone" placeholder="08xxxxxxxxxx" :value="old('student_phone')" required maxlength="20" />
                            </x-ui.field>
                        </div>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ══ STEP 3: Orang Tua / Wali ══ --}}
            <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <x-ui.sheet title="C. Data Orang Tua / Wali">
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <p class="sm:col-span-2 pt-1 text-xs font-bold uppercase tracking-wide text-ink-soft">Ayah</p>
                            <x-ui.field label="Nama Ayah" required :error="$errors->first('father_name')">
                                <x-ui.input name="father_name" :value="old('father_name')" required maxlength="100" />
                            </x-ui.field>
                            <x-ui.field label="NIK Ayah" :error="$errors->first('father_nik')">
                                <x-ui.input name="father_nik" :value="old('father_nik')" maxlength="16" />
                            </x-ui.field>
                            <x-ui.field label="Pekerjaan" :error="$errors->first('father_job')">
                                <x-ui.input name="father_job" :value="old('father_job')" maxlength="30" />
                            </x-ui.field>
                            <x-ui.field label="Nomor HP Ayah" :error="$errors->first('father_phone')">
                                <x-ui.input name="father_phone" :value="old('father_phone')" maxlength="20" />
                            </x-ui.field>
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <p class="sm:col-span-2 pt-1 text-xs font-bold uppercase tracking-wide text-ink-soft">Ibu</p>
                            <x-ui.field label="Nama Ibu" required :error="$errors->first('mother_name')">
                                <x-ui.input name="mother_name" :value="old('mother_name')" required maxlength="100" />
                            </x-ui.field>
                            <x-ui.field label="NIK Ibu" :error="$errors->first('mother_nik')">
                                <x-ui.input name="mother_nik" :value="old('mother_nik')" maxlength="16" />
                            </x-ui.field>
                            <x-ui.field label="Pekerjaan" :error="$errors->first('mother_job')">
                                <x-ui.input name="mother_job" :value="old('mother_job')" maxlength="30" />
                            </x-ui.field>
                            <x-ui.field label="Nomor HP Ibu" :error="$errors->first('mother_phone')">
                                <x-ui.input name="mother_phone" :value="old('mother_phone')" maxlength="20" />
                            </x-ui.field>
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            <p class="sm:col-span-3 pt-1 text-xs font-bold uppercase tracking-wide text-ink-soft">Wali (apabila berbeda dengan orang tua)</p>
                            <x-ui.field label="Nama Wali" :error="$errors->first('guardian_name')">
                                <x-ui.input name="guardian_name" :value="old('guardian_name')" maxlength="100" />
                            </x-ui.field>
                            <x-ui.field label="NIK Wali" :error="$errors->first('guardian_nik')">
                                <x-ui.input name="guardian_nik" :value="old('guardian_nik')" maxlength="16" />
                            </x-ui.field>
                            <x-ui.field label="Nomor HP Wali" :error="$errors->first('guardian_phone')">
                                <x-ui.input name="guardian_phone" :value="old('guardian_phone')" maxlength="20" />
                            </x-ui.field>
                        </div>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ══ STEP 4: Dokumen & Kirim ══ --}}
            <div x-show="step === 4" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                <x-ui.sheet title="D. Dokumen" subtitle="Unggah tautan Google Drive berisi scan dokumen">
                    <div class="space-y-5">
                        <x-ui.field label="Surat Rekomendasi Madrasah" required hint="Wajib — rekomendasi dari madrasah asal untuk siswa bersangkutan." :error="$errors->first('scanned_rekomendasi')">
                            <x-ui.input name="scanned_rekomendasi" placeholder="https://drive.google.com/…" :value="old('scanned_rekomendasi')" required maxlength="500" />
                        </x-ui.field>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <x-ui.field label="Rapor / Transkrip Nilai" :error="$errors->first('scanned_rapor')">
                                <x-ui.input name="scanned_rapor" placeholder="https://drive.google.com/…" :value="old('scanned_rapor')" maxlength="500" />
                            </x-ui.field>
                            <x-ui.field label="Kartu Keluarga (KK)" :error="$errors->first('scanned_kk')">
                                <x-ui.input name="scanned_kk" placeholder="https://drive.google.com/…" :value="old('scanned_kk')" maxlength="500" />
                            </x-ui.field>
                            <x-ui.field label="Akta Kelahiran" :error="$errors->first('scanned_akta')">
                                <x-ui.input name="scanned_akta" placeholder="https://drive.google.com/…" :value="old('scanned_akta')" maxlength="500" />
                            </x-ui.field>
                            <x-ui.field label="Pas Foto" :error="$errors->first('scanned_photo')">
                                <x-ui.input name="scanned_photo" placeholder="https://drive.google.com/…" :value="old('scanned_photo')" maxlength="500" />
                            </x-ui.field>
                        </div>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- Navigasi langkah --}}
            <div class="mt-6 flex items-center justify-between gap-3">
                <x-ui.button variant="ghost" icon="arrow-left" type="button" x-show="step > 1" @click="step--">Sebelumnya</x-ui.button>
                <x-ui.button variant="primary" icon="arrow-right" iconRight="check" type="button" x-show="step < totalSteps" @click="step++">Lanjut</x-ui.button>
                <x-ui.button variant="success" icon="check" type="submit" x-show="step === totalSteps">Kirim Pendaftaran</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.publik>