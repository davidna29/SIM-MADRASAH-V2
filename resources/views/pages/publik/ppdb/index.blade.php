<x-layouts.publik :title="'PPDB - Pendaftaran'">
    @php
        // Pemetaan field -> langkah wizard, untuk membuka langkah pertama yang berisi error validasi.
        $stepFields = [
            1 => ['name','nik','nisn','gender','religion','birth_place','birth_date','previous_school','hobby','ambition','child_order','sibling_count','ever_tk','ever_paud','entry_date','scanned_kk','scanned_kk_wali','scanned_akta','scanned_ijazah','scanned_photo'],
            2 => ['imm_hepb','imm_polio','imm_bcg','imm_campak','imm_dpt','imm_covid','dis_deaf','dis_blind','dis_disabled','dis_intellectual','dis_behavioral','dis_slow_learner','dis_communication','dis_gifted'],
            3 => ['residence_type','address','province','city','district','village','rt','rw','postal_code','distance','transport','commute_time','home_phone','student_phone','student_email'],
            4 => ['kk_number','kk_head_name','father_name','father_status','father_nik','father_birth_date','father_birth_place','father_education','father_job','father_income','father_phone','mother_name','mother_status','mother_nik','mother_birth_date','mother_birth_place','mother_education','mother_job','mother_income','mother_phone','guardian_name','guardian_nik','guardian_birth_date','guardian_birth_place','guardian_education','guardian_job','guardian_income','guardian_phone'],
            5 => ['parent_ownership','parent_address','parent_province','parent_city','parent_district','parent_village','parent_rt','parent_rw','parent_postal_code'],
            6 => ['origin_school','origin_nsm','origin_npsn','origin_address'],
        ];
        $firstErrorStep = 1;
        if ($errors->any()) {
            foreach ($stepFields as $s => $fields) {
                foreach ($fields as $f) {
                    if ($errors->has($f)) {
                        $firstErrorStep = $s;
                        break 2;
                    }
                }
            }
        }
    @endphp
    <div class="mx-auto max-w-4xl" x-data="{ step: {{ $firstErrorStep }}, totalSteps: 7 }">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pendaftaran PPDB</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Isi formulir pendaftaran dengan lengkap. Setiap langkah ditandai dengan bintang (<span class="text-danger">*</span>) wajib diisi.
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
                                <template x-if="step <= i">
                                    <span x-text="i"></span>
                                </template>
                            </div>
                        </div>
                        <div
                            class="mx-2 hidden h-0.5 flex-1 rounded-full transition duration-200 sm:block"
                            :class="step > i ? 'bg-success' : 'bg-paper-deep'"
                            x-show="i < totalSteps"
                        ></div>
                    </div>
                </template>
            </div>
            <div class="mt-2 text-center">
                <span class="text-xs font-semibold text-ink-soft">Langkah <span x-text="step"></span> / <span x-text="totalSteps"></span></span>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <x-ui.alert variant="danger" class="mb-6">
                <p class="font-semibold">Terdapat kesalahan pada pengisian formulir.</p>
                <ul class="mt-1 list-disc list-inside text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('ppdb.store') }}" enctype="application/x-www-form-urlencoded" novalidate>
            @csrf

            {{-- ═══════════════════════════════════════════ STEP 1: Data Siswa ═══════════════════════════════════════════ --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

                <x-ui.sheet title="A. Data Siswa" subtitle="Lengkapi data pribadi calon siswa baru">
                    <div class="space-y-5">
                        {{-- name --}}
                        <x-ui.field label="Nama Lengkap" required :error="$errors->first('name')">
                            <x-ui.input name="name" placeholder="NAMA LENGKAP SESUAI KTP/AKTA" :value="old('name')" required maxlength="100" />
                        </x-ui.field>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- nik --}}
                            <x-ui.field label="NIK" required :error="$errors->first('nik')">
                                <x-ui.input name="nik" placeholder="16 digit NIK" :value="old('nik')" required maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                            </x-ui.field>

                            {{-- nisn --}}
                            <x-ui.field label="NISN" :error="$errors->first('nisn')">
                                <x-ui.input name="nisn" placeholder="10 digit NISN (opsional)" :value="old('nisn')" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- gender --}}
                            <x-ui.field label="Jenis Kelamin" required :error="$errors->first('gender')">
                                <x-ui.select name="gender" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :selected="old('gender')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- religion --}}
                            <x-ui.field label="Agama" required :error="$errors->first('religion')">
                                <x-ui.select name="religion" :options="['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']" :selected="old('religion', 'Islam')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- birth_place --}}
                            <x-ui.field label="Tempat Lahir" required :error="$errors->first('birth_place')">
                                <x-ui.input name="birth_place" placeholder="Kota/Kabupaten" :value="old('birth_place')" required maxlength="60" />
                            </x-ui.field>

                            {{-- birth_date --}}
                            <x-ui.field label="Tanggal Lahir" required :error="$errors->first('birth_date')">
                                <x-ui.input type="date" name="birth_date" :value="old('birth_date')" required />
                            </x-ui.field>
                        </div>

                        {{-- previous_school --}}
                        <x-ui.field label="Asal Sekolah" :error="$errors->first('previous_school')">
                            <x-ui.input name="previous_school" placeholder="Nama sekolah sebelumnya (opsional)" :value="old('previous_school')" maxlength="100" />
                        </x-ui.field>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- hobby --}}
                            <x-ui.field label="Hobi" required :error="$errors->first('hobby')">
                                <x-ui.select name="hobby" :options="['Olah Raga' => 'Olah Raga', 'Kesenian' => 'Kesenian', 'Membaca' => 'Membaca', 'Menulis' => 'Menulis', 'Traveling' => 'Traveling', 'Lainnya' => 'Lainnya']" :selected="old('hobby')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- ambition --}}
                            <x-ui.field label="Cita-cita" required :error="$errors->first('ambition')">
                                <x-ui.select name="ambition" :options="['PNS' => 'PNS', 'TNI-Polri' => 'TNI-Polri', 'Guru-Dosen' => 'Guru/Dosen', 'Dokter' => 'Dokter', 'Politikus' => 'Politikus', 'Wiraswasta' => 'Wiraswasta', 'Pekerja Seni' => 'Pekerja Seni', 'Lainnya' => 'Lainnya']" :selected="old('ambition')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- child_order --}}
                            <x-ui.field label="Anak Ke-" required hint="Urutan dalam keluarga (1–9)" :error="$errors->first('child_order')">
                                <x-ui.input type="number" name="child_order" :value="old('child_order')" min="1" max="9" required />
                            </x-ui.field>

                            {{-- sibling_count --}}
                            <x-ui.field label="Jumlah Saudara Kandung" required hint="Termasuk diri sendiri" :error="$errors->first('sibling_count')">
                                <x-ui.input type="number" name="sibling_count" :value="old('sibling_count')" min="0" max="9" required />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- ever_tk --}}
                            <x-ui.field label="Pernah Masuk TK?" required :error="$errors->first('ever_tk')">
                                <x-ui.select name="ever_tk" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('ever_tk')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- ever_paud --}}
                            <x-ui.field label="Pernah Masuk PAUD?" required :error="$errors->first('ever_paud')">
                                <x-ui.select name="ever_paud" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('ever_paud')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        {{-- entry_date --}}
                        <x-ui.field label="Tanggal Masuk" :error="$errors->first('entry_date')">
                            <x-ui.input type="date" name="entry_date" :value="old('entry_date')" />
                        </x-ui.field>

                        {{-- Documents --}}
                        <div class="border-t border-rule/70 pt-5">
                            <h3 class="text-sm font-bold tracking-tight text-ink">Dokumen (Tautan Google Drive)</h3>
                            <p class="mt-1 text-xs text-ink-soft">Unggah file ke Google Drive dan tempelkan tautan berbagi di sini.</p>
                        </div>

                        <x-ui.field label="Scan Kartu Keluarga" required hint="Tautan Google Drive yang bisa diakses publik" :error="$errors->first('scanned_kk')">
                            <x-ui.input type="url" name="scanned_kk" placeholder="https://drive.google.com/..." :value="old('scanned_kk')" required maxlength="500" />
                        </x-ui.field>

                        <x-ui.field label="Scan Kartu Keluarga Wali (Opsional)" :error="$errors->first('scanned_kk_wali')">
                            <x-ui.input type="url" name="scanned_kk_wali" placeholder="https://drive.google.com/..." :value="old('scanned_kk_wali')" maxlength="500" />
                        </x-ui.field>

                        <x-ui.field label="Scan Akta Kelahiran" required :error="$errors->first('scanned_akta')">
                            <x-ui.input type="url" name="scanned_akta" placeholder="https://drive.google.com/..." :value="old('scanned_akta')" required maxlength="500" />
                        </x-ui.field>

                        <x-ui.field label="Scan Ijazah (Opsional)" :error="$errors->first('scanned_ijazah')">
                            <x-ui.input type="url" name="scanned_ijazah" placeholder="https://drive.google.com/..." :value="old('scanned_ijazah')" maxlength="500" />
                        </x-ui.field>

                        <x-ui.field label="Pas Foto (Opsional)" :error="$errors->first('scanned_photo')">
                            <x-ui.input type="url" name="scanned_photo" placeholder="https://drive.google.com/..." :value="old('scanned_photo')" maxlength="500" />
                        </x-ui.field>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ═══════════════════════════════════════════ STEP 2: Kesehatan & Berkebutuhan Khusus ═══════════════════════════════════════════ --}}
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

                {{-- Section B: Imunisasi --}}
                <x-ui.sheet title="B. Imunisasi" subtitle="Riwayat imunisasi calon siswa" class="mb-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <x-ui.field label="Hepatitis B" required :error="$errors->first('imm_hepb')">
                            <x-ui.select name="imm_hepb" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('imm_hepb')" placeholder="-- Pilih --" />
                        </x-ui.field>

                        <x-ui.field label="Polio" required :error="$errors->first('imm_polio')">
                            <x-ui.select name="imm_polio" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('imm_polio')" placeholder="-- Pilih --" />
                        </x-ui.field>

                        <x-ui.field label="BCG" required :error="$errors->first('imm_bcg')">
                            <x-ui.select name="imm_bcg" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('imm_bcg')" placeholder="-- Pilih --" />
                        </x-ui.field>

                        <x-ui.field label="Campak" required :error="$errors->first('imm_campak')">
                            <x-ui.select name="imm_campak" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('imm_campak')" placeholder="-- Pilih --" />
                        </x-ui.field>

                        <x-ui.field label="DPT" required :error="$errors->first('imm_dpt')">
                            <x-ui.select name="imm_dpt" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('imm_dpt')" placeholder="-- Pilih --" />
                        </x-ui.field>

                        <x-ui.field label="COVID-19" required :error="$errors->first('imm_covid')">
                            <x-ui.select name="imm_covid" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('imm_covid')" placeholder="-- Pilih --" />
                        </x-ui.field>
                    </div>
                </x-ui.sheet>

                {{-- Section C: Berkebutuhan Khusus --}}
                <x-ui.sheet title="C. Berkebutuhan Khusus" subtitle="Centang jika ada kondisi yang relevan (opsional)">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="dis_deaf" value="1" {{ old('dis_deaf') ? 'checked' : '' }} class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                            <span class="text-sm text-ink">Tuli (Deaf)</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="dis_blind" value="1" {{ old('dis_blind') ? 'checked' : '' }} class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                            <span class="text-sm text-ink">Tuna Netra (Blind)</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="dis_disabled" value="1" {{ old('dis_disabled') ? 'checked' : '' }} class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                            <span class="text-sm text-ink">Tuna Daksa (Disabled)</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="dis_intellectual" value="1" {{ old('dis_intellectual') ? 'checked' : '' }} class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                            <span class="text-sm text-ink">Tuna Grahita (Intellectual)</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="dis_behavioral" value="1" {{ old('dis_behavioral') ? 'checked' : '' }} class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                            <span class="text-sm text-ink">Berkebutuhan Perilaku (Behavioral)</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="dis_slow_learner" value="1" {{ old('dis_slow_learner') ? 'checked' : '' }} class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                            <span class="text-sm text-ink">Lambat Belajar (Slow Learner)</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="dis_communication" value="1" {{ old('dis_communication') ? 'checked' : '' }} class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                            <span class="text-sm text-ink">Gangguan Komunikasi</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="dis_gifted" value="1" {{ old('dis_gifted') ? 'checked' : '' }} class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                            <span class="text-sm text-ink">Berdaya Istimewa (Gifted)</span>
                        </label>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ═══════════════════════════════════════════ STEP 3: Alamat Siswa ═══════════════════════════════════════════ --}}
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

                <x-ui.sheet title="D. Alamat Siswa" subtitle="Lengkapi alamat domisili calon siswa">
                    <div class="space-y-5">
                        {{-- residence_type --}}
                        <x-ui.field label="Tinggal Bersama" required :error="$errors->first('residence_type')">
                            <x-ui.select name="residence_type" :options="['Tinggal dgn Ortu' => 'Tinggal dengan Orang Tua', 'Ikut Saudara' => 'Ikut Saudara', 'Asrama Madrasah' => 'Asrama Madrasah', 'Kontrak-Kost' => 'Kontrak/Kost', 'Asrama Pesantren' => 'Asrama Pesantren', 'Panti Asuhan' => 'Panti Asuhan', 'Rumah Singgah' => 'Rumah Singgah', 'Lainnya' => 'Lainnya']" :selected="old('residence_type')" placeholder="-- Pilih --" />
                        </x-ui.field>

                        {{-- address --}}
                        <x-ui.field label="Alamat Lengkap" required :error="$errors->first('address')">
                            <textarea name="address" rows="2" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Jalan, Nomor, RT/RW, dll." required maxlength="255">{{ old('address') }}</textarea>
                        </x-ui.field>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            {{-- province --}}
                            <x-ui.field label="Provinsi" required :error="$errors->first('province')">
                                <x-ui.input name="province" :value="old('province', 'Kalimantan Tengah')" required maxlength="60" />
                            </x-ui.field>

                            {{-- city --}}
                            <x-ui.field label="Kota/Kabupaten" required :error="$errors->first('city')">
                                <x-ui.input name="city" :value="old('city', 'Palangka Raya')" required maxlength="60" />
                            </x-ui.field>

                            {{-- district --}}
                            <x-ui.field label="Kecamatan" required :error="$errors->first('district')">
                                <x-ui.select name="district" :options="['Pahandut' => 'Pahandut', 'Bukit Batu' => 'Bukit Batu', 'Jekan Raya' => 'Jekan Raya', 'Sebangau' => 'Sebangau', 'Rakumpit' => 'Rakumpit']" :selected="old('district')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        {{-- village --}}
                        <x-ui.field label="Kelurahan/Desa" required :error="$errors->first('village')">
                            <x-ui.input name="village" placeholder="Nama kelurahan/desa" :value="old('village')" required maxlength="60" />
                        </x-ui.field>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            {{-- rt --}}
                            <x-ui.field label="RT" required :error="$errors->first('rt')">
                                <x-ui.input name="rt" placeholder="001" :value="old('rt')" required maxlength="3" pattern="[0-9]{3}" inputmode="numeric" />
                            </x-ui.field>

                            {{-- rw --}}
                            <x-ui.field label="RW" required :error="$errors->first('rw')">
                                <x-ui.input name="rw" placeholder="001" :value="old('rw')" required maxlength="3" pattern="[0-9]{3}" inputmode="numeric" />
                            </x-ui.field>

                            {{-- postal_code --}}
                            <x-ui.field label="Kode Pos" required :error="$errors->first('postal_code')">
                                <x-ui.input name="postal_code" placeholder="73211" :value="old('postal_code')" required maxlength="5" pattern="[0-9]{5}" inputmode="numeric" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            {{-- distance --}}
                            <x-ui.field label="Jarak ke Madrasah" required :error="$errors->first('distance')">
                                <x-ui.select name="distance" :options="['<5km' => '< 5 km', '5-10km' => '5 – 10 km', '11-20km' => '11 – 20 km', '21-30km' => '21 – 30 km', '>30km' => '> 30 km']" :selected="old('distance')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- transport --}}
                            <x-ui.field label="Kendaraan ke Madrasah" required :error="$errors->first('transport')">
                                <x-ui.select name="transport" :options="['Jalan Kaki' => 'Jalan Kaki', 'Sepeda' => 'Sepeda', 'Sepeda Motor' => 'Sepeda Motor', 'Mobil Pribadi' => 'Mobil Pribadi', 'Antar Jemput Sekolah' => 'Antar Jemput Sekolah', 'Angkot' => 'Angkot', 'Perahu' => 'Perahu', 'Kendaraan Pribadi' => 'Kendaraan Pribadi', 'Kereta Api' => 'Kereta Api', 'Ojek' => 'Ojek', 'Andong-Becak' => 'Andong/Becak', 'Lainnya' => 'Lainnya']" :selected="old('transport')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- commute_time --}}
                            <x-ui.field label="Waktu Tempuh" required :error="$errors->first('commute_time')">
                                <x-ui.select name="commute_time" :options="['1-10 menit' => '1 – 10 menit', '10-19 menit' => '10 – 19 menit', '20-29 menit' => '20 – 29 menit', '30-39 menit' => '30 – 39 menit', '1-2 jam' => '1 – 2 jam', '>2 jam' => '> 2 jam']" :selected="old('commute_time')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        <div class="border-t border-rule/70 pt-5">
                            <h3 class="text-sm font-bold tracking-tight text-ink">Kontak</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            {{-- home_phone --}}
                            <x-ui.field label="Telepon Rumah" :error="$errors->first('home_phone')">
                                <x-ui.input name="home_phone" placeholder="Nomor telepon rumah" :value="old('home_phone')" maxlength="20" />
                            </x-ui.field>

                            {{-- student_phone --}}
                            <x-ui.field label="Telepon Siswa" :error="$errors->first('student_phone')">
                                <x-ui.input name="student_phone" placeholder="Nomor HP siswa" :value="old('student_phone')" maxlength="20" />
                            </x-ui.field>

                            {{-- student_email --}}
                            <x-ui.field label="Email Siswa" :error="$errors->first('student_email')">
                                <x-ui.input type="email" name="student_email" placeholder="email@contoh.com" :value="old('student_email')" maxlength="100" />
                            </x-ui.field>
                        </div>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ═══════════════════════════════════════════ STEP 4: Data Orang Tua/Wali ═══════════════════════════════════════════ --}}
            <div x-show="step === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

                {{-- KK --}}
                <x-ui.sheet title="E. Data Orang Tua / Wali" subtitle="Data kartu keluarga dan orang tua/wali" class="mb-5">
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- kk_number --}}
                            <x-ui.field label="Nomor Kartu Keluarga" required :error="$errors->first('kk_number')">
                                <x-ui.input name="kk_number" placeholder="16 digit nomor KK" :value="old('kk_number')" required maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                            </x-ui.field>

                            {{-- kk_head_name --}}
                            <x-ui.field label="Nama Kepala Keluarga" required :error="$errors->first('kk_head_name')">
                                <x-ui.input name="kk_head_name" placeholder="Sesuai tertera di KK" :value="old('kk_head_name')" required maxlength="100" />
                            </x-ui.field>
                        </div>
                    </div>
                </x-ui.sheet>

                {{-- Ayah --}}
                <x-ui.sheet title="Data Ayah" class="mb-5">
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- father_name --}}
                            <x-ui.field label="Nama Ayah" required :error="$errors->first('father_name')">
                                <x-ui.input name="father_name" placeholder="Nama lengkap ayah" :value="old('father_name')" required maxlength="100" />
                            </x-ui.field>

                            {{-- father_status --}}
                            <x-ui.field label="Status Ayah" required :error="$errors->first('father_status')">
                                <x-ui.select name="father_status" :options="['Masih Hidup' => 'Masih Hidup', 'Sudah Meninggal' => 'Sudah Meninggal', 'Tanpa Keterangan' => 'Tanpa Keterangan']" :selected="old('father_status')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- father_nik --}}
                            <x-ui.field label="NIK Ayah" :error="$errors->first('father_nik')">
                                <x-ui.input name="father_nik" placeholder="16 digit NIK" :value="old('father_nik')" maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                            </x-ui.field>

                            {{-- father_birth_date --}}
                            <x-ui.field label="Tanggal Lahir Ayah" :error="$errors->first('father_birth_date')">
                                <x-ui.input type="date" name="father_birth_date" :value="old('father_birth_date')" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- father_birth_place --}}
                            <x-ui.field label="Tempat Lahir Ayah" :error="$errors->first('father_birth_place')">
                                <x-ui.input name="father_birth_place" placeholder="Kota/Kabupaten" :value="old('father_birth_place')" maxlength="60" />
                            </x-ui.field>

                            {{-- father_education --}}
                            <x-ui.field label="Pendidikan Ayah" :error="$errors->first('father_education')">
                                <x-ui.select name="father_education" :options="['0' => 'Tidak Sekolah', '1' => 'SD/Sederajat', '2' => 'SMP/Sederajat', '3' => 'SMA/Sederajat', '4' => 'D1', '5' => 'D2', '6' => 'D3', '7' => 'D4-S1', '8' => 'S2', '9' => 'S3']" :selected="old('father_education')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            {{-- father_job --}}
                            <x-ui.field label="Pekerjaan Ayah" :error="$errors->first('father_job')">
                                <x-ui.select name="father_job" :options="['01' => 'Tidak Bekerja', '02' => 'Pensiunan', '03' => 'PNS (Selain poin 05 dan 10)', '04' => 'TNI/Polisi', '05' => 'Guru/Dosen', '06' => 'Pegawai Swasta', '07' => 'Wiraswasta', '08' => 'Pengacara/Hakim/Jaksa/Notaris', '09' => 'Seniman/Pelukis/Artis/Sejenis', '10' => 'Dokter/Bidan/Perawat', '11' => 'Pilot/Pramugara', '12' => 'Pedagang', '13' => 'Petani/Peternak', '14' => 'Nelayan', '15' => 'Buruh (Tani/Pabrik/Bangunan)', '16' => 'Sopir/Masinis/Kondektur', '17' => 'Politikus', '18' => 'Lainnya']" :selected="old('father_job')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- father_income --}}
                            <x-ui.field label="Penghasilan Ayah" :error="$errors->first('father_income')">
                                <x-ui.select name="father_income" :options="['< Rp500rb' => '< Rp 500.000', 'Rp500rb – 1jt' => 'Rp 500.000 – 1.000.000', 'Rp1jt – 2jt' => 'Rp 1.000.000 – 2.000.000', 'Rp2jt – 3jt' => 'Rp 2.000.000 – 3.000.000', 'Rp3jt – 5jt' => 'Rp 3.000.000 – 5.000.000', '> Rp5jt' => '> Rp 5.000.000', 'Tidak ada' => 'Tidak ada']" :selected="old('father_income')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- father_phone --}}
                            <x-ui.field label="Telepon Ayah" :error="$errors->first('father_phone')">
                                <x-ui.input name="father_phone" placeholder="Nomor telepon" :value="old('father_phone')" maxlength="20" />
                            </x-ui.field>
                        </div>
                    </div>
                </x-ui.sheet>

                {{-- Ibu --}}
                <x-ui.sheet title="Data Ibu" class="mb-5">
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- mother_name --}}
                            <x-ui.field label="Nama Ibu" required :error="$errors->first('mother_name')">
                                <x-ui.input name="mother_name" placeholder="Nama lengkap ibu" :value="old('mother_name')" required maxlength="100" />
                            </x-ui.field>

                            {{-- mother_status --}}
                            <x-ui.field label="Status Ibu" required :error="$errors->first('mother_status')">
                                <x-ui.select name="mother_status" :options="['Masih Hidup' => 'Masih Hidup', 'Sudah Meninggal' => 'Sudah Meninggal', 'Tidak Diketahui' => 'Tidak Diketahui']" :selected="old('mother_status')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- mother_nik --}}
                            <x-ui.field label="NIK Ibu" required :error="$errors->first('mother_nik')">
                                <x-ui.input name="mother_nik" placeholder="16 digit NIK" :value="old('mother_nik')" required maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                            </x-ui.field>

                            {{-- mother_birth_place --}}
                            <x-ui.field label="Tempat Lahir Ibu" :error="$errors->first('mother_birth_place')">
                                <x-ui.input name="mother_birth_place" placeholder="Kota/Kabupaten" :value="old('mother_birth_place')" maxlength="60" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- mother_birth_date --}}
                            <x-ui.field label="Tanggal Lahir Ibu" required :error="$errors->first('mother_birth_date')">
                                <x-ui.input type="date" name="mother_birth_date" :value="old('mother_birth_date')" required />
                            </x-ui.field>

                            {{-- mother_education --}}
                            <x-ui.field label="Pendidikan Ibu" required :error="$errors->first('mother_education')">
                                <x-ui.select name="mother_education" :options="['0' => 'Tidak Sekolah', '1' => 'SD/Sederajat', '2' => 'SMP/Sederajat', '3' => 'SMA/Sederajat', '4' => 'D1', '5' => 'D2', '6' => 'D3', '7' => 'D4-S1', '8' => 'S2', '9' => 'S3']" :selected="old('mother_education')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            {{-- mother_job --}}
                            <x-ui.field label="Pekerjaan Ibu" required :error="$errors->first('mother_job')">
                                <x-ui.select name="mother_job" :options="['01' => 'Tidak Bekerja', '02' => 'Pensiunan', '03' => 'PNS (Selain poin 05 dan 10)', '04' => 'TNI/Polisi', '05' => 'Guru/Dosen', '06' => 'Pegawai Swasta', '07' => 'Wiraswasta', '08' => 'Pengacara/Hakim/Jaksa/Notaris', '09' => 'Seniman/Pelukis/Artis/Sejenis', '10' => 'Dokter/Bidan/Perawat', '11' => 'Pilot/Pramugara', '12' => 'Pedagang', '13' => 'Petani/Peternak', '14' => 'Nelayan', '15' => 'Buruh (Tani/Pabrik/Bangunan)', '16' => 'Sopir/Masinis/Kondektur', '17' => 'Politikus', '18' => 'Lainnya']" :selected="old('mother_job')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- mother_income --}}
                            <x-ui.field label="Penghasilan Ibu" required :error="$errors->first('mother_income')">
                                <x-ui.select name="mother_income" :options="['< Rp500rb' => '< Rp 500.000', 'Rp500rb – 1jt' => 'Rp 500.000 – 1.000.000', 'Rp1jt – 2jt' => 'Rp 1.000.000 – 2.000.000', 'Rp2jt – 3jt' => 'Rp 2.000.000 – 3.000.000', 'Rp3jt – 5jt' => 'Rp 3.000.000 – 5.000.000', '> Rp5jt' => '> Rp 5.000.000', 'Tidak ada' => 'Tidak ada']" :selected="old('mother_income')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- mother_phone --}}
                            <x-ui.field label="Telepon Ibu" :error="$errors->first('mother_phone')">
                                <x-ui.input name="mother_phone" placeholder="Nomor telepon" :value="old('mother_phone')" maxlength="20" />
                            </x-ui.field>
                        </div>
                    </div>
                </x-ui.sheet>

                {{-- Wali --}}
                <x-ui.sheet title="Data Wali" subtitle="Isi hanya jika siswa diasuh oleh wali (opsional)">
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- guardian_name --}}
                            <x-ui.field label="Nama Wali" :error="$errors->first('guardian_name')">
                                <x-ui.input name="guardian_name" placeholder="Nama lengkap wali" :value="old('guardian_name')" maxlength="100" />
                            </x-ui.field>

                            {{-- guardian_nik --}}
                            <x-ui.field label="NIK Wali" :error="$errors->first('guardian_nik')">
                                <x-ui.input name="guardian_nik" placeholder="16 digit NIK" :value="old('guardian_nik')" maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- guardian_birth_date --}}
                            <x-ui.field label="Tanggal Lahir Wali" :error="$errors->first('guardian_birth_date')">
                                <x-ui.input type="date" name="guardian_birth_date" :value="old('guardian_birth_date')" />
                            </x-ui.field>

                            {{-- guardian_birth_place --}}
                            <x-ui.field label="Tempat Lahir Wali" :error="$errors->first('guardian_birth_place')">
                                <x-ui.input name="guardian_birth_place" placeholder="Kota/Kabupaten" :value="old('guardian_birth_place')" maxlength="60" />
                            </x-ui.field>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            {{-- guardian_education --}}
                            <x-ui.field label="Pendidikan Wali" :error="$errors->first('guardian_education')">
                                <x-ui.select name="guardian_education" :options="['0' => 'Tidak Sekolah', '1' => 'SD/Sederajat', '2' => 'SMP/Sederajat', '3' => 'SMA/Sederajat', '4' => 'D1', '5' => 'D2', '6' => 'D3', '7' => 'D4-S1', '8' => 'S2', '9' => 'S3']" :selected="old('guardian_education')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- guardian_job --}}
                            <x-ui.field label="Pekerjaan Wali" :error="$errors->first('guardian_job')">
                                <x-ui.select name="guardian_job" :options="['01' => 'Tidak Bekerja', '02' => 'Pensiunan', '03' => 'PNS (Selain poin 05 dan 10)', '04' => 'TNI/Polisi', '05' => 'Guru/Dosen', '06' => 'Pegawai Swasta', '07' => 'Wiraswasta', '08' => 'Pengacara/Hakim/Jaksa/Notaris', '09' => 'Seniman/Pelukis/Artis/Sejenis', '10' => 'Dokter/Bidan/Perawat', '11' => 'Pilot/Pramugara', '12' => 'Pedagang', '13' => 'Petani/Peternak', '14' => 'Nelayan', '15' => 'Buruh (Tani/Pabrik/Bangunan)', '16' => 'Sopir/Masinis/Kondektur', '17' => 'Politikus', '18' => 'Lainnya']" :selected="old('guardian_job')" placeholder="-- Pilih --" />
                            </x-ui.field>

                            {{-- guardian_income --}}
                            <x-ui.field label="Penghasilan Wali" :error="$errors->first('guardian_income')">
                                <x-ui.select name="guardian_income" :options="['< Rp500rb' => '< Rp 500.000', 'Rp500rb – 1jt' => 'Rp 500.000 – 1.000.000', 'Rp1jt – 2jt' => 'Rp 1.000.000 – 2.000.000', 'Rp2jt – 3jt' => 'Rp 2.000.000 – 3.000.000', 'Rp3jt – 5jt' => 'Rp 3.000.000 – 5.000.000', '> Rp5jt' => '> Rp 5.000.000', 'Tidak ada' => 'Tidak ada']" :selected="old('guardian_income')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        {{-- guardian_phone --}}
                        <x-ui.field label="Telepon Wali" :error="$errors->first('guardian_phone')">
                            <x-ui.input name="guardian_phone" placeholder="Nomor telepon" :value="old('guardian_phone')" maxlength="20" />
                        </x-ui.field>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ═══════════════════════════════════════════ STEP 5: Alamat Orang Tua ═══════════════════════════════════════════ --}}
            <div x-show="step === 5" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

                <x-ui.sheet title="F. Alamat Orang Tua" subtitle="Alamat domisili orang tua/wali sesuai KK">
                    <div class="space-y-5">
                        {{-- parent_ownership --}}
                        <x-ui.field label="Kepemilikan Rumah" required :error="$errors->first('parent_ownership')">
                            <x-ui.select name="parent_ownership" :options="['Milik Sendiri' => 'Milik Sendiri', 'Milik Wali' => 'Milik Wali', 'Rumah Orang Tua' => 'Rumah Orang Tua', 'Rumah Saudara' => 'Rumah Saudara', 'Rumah Dinas' => 'Rumah Dinas', 'Sewa-Kontrak' => 'Sewa/Kontrak', 'Lainnya' => 'Lainnya']" :selected="old('parent_ownership')" placeholder="-- Pilih --" />
                        </x-ui.field>

                        {{-- parent_address --}}
                        <x-ui.field label="Alamat Lengkap" required :error="$errors->first('parent_address')">
                            <textarea name="parent_address" rows="2" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Jalan, Nomor, RT/RW, dll." required maxlength="255">{{ old('parent_address') }}</textarea>
                        </x-ui.field>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            {{-- parent_province --}}
                            <x-ui.field label="Provinsi" required :error="$errors->first('parent_province')">
                                <x-ui.input name="parent_province" :value="old('parent_province', 'Kalimantan Tengah')" required maxlength="60" />
                            </x-ui.field>

                            {{-- parent_city --}}
                            <x-ui.field label="Kota/Kabupaten" required :error="$errors->first('parent_city')">
                                <x-ui.input name="parent_city" :value="old('parent_city', 'Palangka Raya')" required maxlength="60" />
                            </x-ui.field>

                            {{-- parent_district --}}
                            <x-ui.field label="Kecamatan" required :error="$errors->first('parent_district')">
                                <x-ui.select name="parent_district" :options="['Pahandut' => 'Pahandut', 'Bukit Batu' => 'Bukit Batu', 'Jekan Raya' => 'Jekan Raya', 'Sebangau' => 'Sebangau', 'Rakumpit' => 'Rakumpit']" :selected="old('parent_district')" placeholder="-- Pilih --" />
                            </x-ui.field>
                        </div>

                        {{-- parent_village --}}
                        <x-ui.field label="Kelurahan/Desa" required :error="$errors->first('parent_village')">
                            <x-ui.input name="parent_village" placeholder="Nama kelurahan/desa" :value="old('parent_village')" required maxlength="60" />
                        </x-ui.field>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            {{-- parent_rt --}}
                            <x-ui.field label="RT" required :error="$errors->first('parent_rt')">
                                <x-ui.input name="parent_rt" placeholder="001" :value="old('parent_rt')" required maxlength="3" pattern="[0-9]{3}" inputmode="numeric" />
                            </x-ui.field>

                            {{-- parent_rw --}}
                            <x-ui.field label="RW" required :error="$errors->first('parent_rw')">
                                <x-ui.input name="parent_rw" placeholder="001" :value="old('parent_rw')" required maxlength="3" pattern="[0-9]{3}" inputmode="numeric" />
                            </x-ui.field>

                            {{-- parent_postal_code --}}
                            <x-ui.field label="Kode Pos" required :error="$errors->first('parent_postal_code')">
                                <x-ui.input name="parent_postal_code" placeholder="73211" :value="old('parent_postal_code')" required maxlength="5" pattern="[0-9]{5}" inputmode="numeric" />
                            </x-ui.field>
                        </div>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ═══════════════════════════════════════════ STEP 6: Sekolah Asal ═══════════════════════════════════════════ --}}
            <div x-show="step === 6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

                <x-ui.sheet title="G. Sekolah Asal" subtitle="Data sekolah sebelumnya">
                    <div class="space-y-5">
                        {{-- origin_school --}}
                        <x-ui.field label="Nama Sekolah Asal" required :error="$errors->first('origin_school')">
                            <x-ui.input name="origin_school" placeholder="Nama sekolah asal" :value="old('origin_school')" required maxlength="100" />
                        </x-ui.field>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- origin_nsm --}}
                            <x-ui.field label="NSM (Nomor Statistik Madrasah)" :error="$errors->first('origin_nsm')">
                                <x-ui.input name="origin_nsm" placeholder="12 digit NSM" :value="old('origin_nsm')" maxlength="12" pattern="[0-9]{12}" inputmode="numeric" />
                            </x-ui.field>

                            {{-- origin_npsn --}}
                            <x-ui.field label="NPSN (Nomor Pokok Sekolah Nasional)" :error="$errors->first('origin_npsn')">
                                <x-ui.input name="origin_npsn" placeholder="8 digit NPSN" :value="old('origin_npsn')" maxlength="8" pattern="[0-9]{8}" inputmode="numeric" />
                            </x-ui.field>
                        </div>

                        {{-- origin_address --}}
                        <x-ui.field label="Alamat Sekolah Asal" :error="$errors->first('origin_address')">
                            <textarea name="origin_address" rows="2" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Alamat lengkap sekolah asal (opsional)" maxlength="255">{{ old('origin_address') }}</textarea>
                        </x-ui.field>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ═══════════════════════════════════════════ STEP 7: Review & Submit ═══════════════════════════════════════════ --}}
            <div x-show="step === 7" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

                <x-ui.sheet title="Ringkasan Pendaftaran" subtitle="Periksa kembali data yang diisi sebelum mengirim">
                    <div class="space-y-6">

                        {{-- Summary: Data Siswa --}}
                        <div class="rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/60">
                            <div class="flex items-center justify-between border-b border-rule/70 px-4 py-3">
                                <h3 class="text-sm font-bold text-ink">A. Data Siswa</h3>
                                <button type="button" @click="step = 1" class="text-xs font-semibold text-primary hover:underline">Edit</button>
                            </div>
                            <div class="grid grid-cols-1 gap-x-6 gap-y-3 px-4 py-3 text-sm sm:grid-cols-2">
                                <div><span class="text-xs text-ink-faint">Nama</span><p class="font-medium text-ink" x-text="document.querySelector('[name=name]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">NIK</span><p class="font-medium text-ink" x-text="document.querySelector('[name=nik]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">NISN</span><p class="font-medium text-ink" x-text="document.querySelector('[name=nisn]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Jenis Kelamin</span><p class="font-medium text-ink" x-text="document.querySelector('[name=gender]')?.selectedOptions?.[0]?.text || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Agama</span><p class="font-medium text-ink" x-text="document.querySelector('[name=religion]')?.selectedOptions?.[0]?.text || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Tempat/Tanggal Lahir</span><p class="font-medium text-ink" x-text="(document.querySelector('[name=birth_place]')?.value || '—') + ', ' + (document.querySelector('[name=birth_date]')?.value || '—')"></p></div>
                                <div><span class="text-xs text-ink-faint">Asal Sekolah</span><p class="font-medium text-ink" x-text="document.querySelector('[name=previous_school]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Hobi</span><p class="font-medium text-ink" x-text="document.querySelector('[name=hobby]')?.selectedOptions?.[0]?.text || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Cita-cita</span><p class="font-medium text-ink" x-text="document.querySelector('[name=ambition]')?.selectedOptions?.[0]?.text || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Anak ke-</span><p class="font-medium text-ink" x-text="document.querySelector('[name=child_order]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Jumlah Saudara</span><p class="font-medium text-ink" x-text="document.querySelector('[name=sibling_count]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">TK / PAUD</span><p class="font-medium text-ink" x-text="(document.querySelector('[name=ever_tk]')?.selectedOptions?.[0]?.text || '—') + ' / ' + (document.querySelector('[name=ever_paud]')?.selectedOptions?.[0]?.text || '—')"></p></div>
                            </div>
                        </div>

                        {{-- Summary: Kesehatan --}}
                        <div class="rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/60">
                            <div class="flex items-center justify-between border-b border-rule/70 px-4 py-3">
                                <h3 class="text-sm font-bold text-ink">B+C. Kesehatan & Berkebutuhan Khusus</h3>
                                <button type="button" @click="step = 2" class="text-xs font-semibold text-primary hover:underline">Edit</button>
                            </div>
                            <div class="px-4 py-3 text-sm">
                                <p class="text-xs text-ink-faint">Imunisasi:</p>
                                <div class="mt-1 grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    <span class="text-ink">Hep B: <span class="font-medium" x-text="document.querySelector('[name=imm_hepb]')?.selectedOptions?.[0]?.text || '—'"></span></span>
                                    <span class="text-ink">Polio: <span class="font-medium" x-text="document.querySelector('[name=imm_polio]')?.selectedOptions?.[0]?.text || '—'"></span></span>
                                    <span class="text-ink">BCG: <span class="font-medium" x-text="document.querySelector('[name=imm_bcg]')?.selectedOptions?.[0]?.text || '—'"></span></span>
                                    <span class="text-ink">Campak: <span class="font-medium" x-text="document.querySelector('[name=imm_campak]')?.selectedOptions?.[0]?.text || '—'"></span></span>
                                    <span class="text-ink">DPT: <span class="font-medium" x-text="document.querySelector('[name=imm_dpt]')?.selectedOptions?.[0]?.text || '—'"></span></span>
                                    <span class="text-ink">COVID: <span class="font-medium" x-text="document.querySelector('[name=imm_covid]')?.selectedOptions?.[0]?.text || '—'"></span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Summary: Alamat Siswa --}}
                        <div class="rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/60">
                            <div class="flex items-center justify-between border-b border-rule/70 px-4 py-3">
                                <h3 class="text-sm font-bold text-ink">D. Alamat Siswa</h3>
                                <button type="button" @click="step = 3" class="text-xs font-semibold text-primary hover:underline">Edit</button>
                            </div>
                            <div class="grid grid-cols-1 gap-x-6 gap-y-3 px-4 py-3 text-sm sm:grid-cols-2">
                                <div class="sm:col-span-2"><span class="text-xs text-ink-faint">Alamat</span><p class="font-medium text-ink" x-text="document.querySelector('[name=address]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Provinsi</span><p class="font-medium text-ink" x-text="document.querySelector('[name=province]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Kota</span><p class="font-medium text-ink" x-text="document.querySelector('[name=city]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Kecamatan</span><p class="font-medium text-ink" x-text="document.querySelector('[name=district]')?.selectedOptions?.[0]?.text || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Kelurahan</span><p class="font-medium text-ink" x-text="document.querySelector('[name=village]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">RT/RW</span><p class="font-medium text-ink" x-text="(document.querySelector('[name=rt]')?.value || '—') + '/' + (document.querySelector('[name=rw]')?.value || '—')"></p></div>
                                <div><span class="text-xs text-ink-faint">Kode Pos</span><p class="font-medium text-ink" x-text="document.querySelector('[name=postal_code]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Jarak</span><p class="font-medium text-ink" x-text="document.querySelector('[name=distance]')?.selectedOptions?.[0]?.text || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Transportasi</span><p class="font-medium text-ink" x-text="document.querySelector('[name=transport]')?.selectedOptions?.[0]?.text || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Waktu Tempuh</span><p class="font-medium text-ink" x-text="document.querySelector('[name=commute_time]')?.selectedOptions?.[0]?.text || '—'"></p></div>
                            </div>
                        </div>

                        {{-- Summary: Orang Tua --}}
                        <div class="rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/60">
                            <div class="flex items-center justify-between border-b border-rule/70 px-4 py-3">
                                <h3 class="text-sm font-bold text-ink">E. Data Orang Tua/Wali</h3>
                                <button type="button" @click="step = 4" class="text-xs font-semibold text-primary hover:underline">Edit</button>
                            </div>
                            <div class="grid grid-cols-1 gap-x-6 gap-y-3 px-4 py-3 text-sm sm:grid-cols-2">
                                <div><span class="text-xs text-ink-faint">No. KK</span><p class="font-medium text-ink" x-text="document.querySelector('[name=kk_number]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Kepala KK</span><p class="font-medium text-ink" x-text="document.querySelector('[name=kk_head_name]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Ayah</span><p class="font-medium text-ink" x-text="document.querySelector('[name=father_name]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Ibu</span><p class="font-medium text-ink" x-text="document.querySelector('[name=mother_name]')?.value || '—'"></p></div>
                            </div>
                        </div>

                        {{-- Summary: Alamat Orang Tua --}}
                        <div class="rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/60">
                            <div class="flex items-center justify-between border-b border-rule/70 px-4 py-3">
                                <h3 class="text-sm font-bold text-ink">F. Alamat Orang Tua</h3>
                                <button type="button" @click="step = 5" class="text-xs font-semibold text-primary hover:underline">Edit</button>
                            </div>
                            <div class="grid grid-cols-1 gap-x-6 gap-y-3 px-4 py-3 text-sm sm:grid-cols-2">
                                <div class="sm:col-span-2"><span class="text-xs text-ink-faint">Alamat</span><p class="font-medium text-ink" x-text="document.querySelector('[name=parent_address]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Kecamatan</span><p class="font-medium text-ink" x-text="document.querySelector('[name=parent_district]')?.selectedOptions?.[0]?.text || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">Kelurahan</span><p class="font-medium text-ink" x-text="document.querySelector('[name=parent_village]')?.value || '—'"></p></div>
                            </div>
                        </div>

                        {{-- Summary: Sekolah Asal --}}
                        <div class="rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/60">
                            <div class="flex items-center justify-between border-b border-rule/70 px-4 py-3">
                                <h3 class="text-sm font-bold text-ink">G. Sekolah Asal</h3>
                                <button type="button" @click="step = 6" class="text-xs font-semibold text-primary hover:underline">Edit</button>
                            </div>
                            <div class="grid grid-cols-1 gap-x-6 gap-y-3 px-4 py-3 text-sm sm:grid-cols-2">
                                <div class="sm:col-span-2"><span class="text-xs text-ink-faint">Sekolah Asal</span><p class="font-medium text-ink" x-text="document.querySelector('[name=origin_school]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">NSM</span><p class="font-medium text-ink" x-text="document.querySelector('[name=origin_nsm]')?.value || '—'"></p></div>
                                <div><span class="text-xs text-ink-faint">NPSN</span><p class="font-medium text-ink" x-text="document.querySelector('[name=origin_npsn]')?.value || '—'"></p></div>
                            </div>
                        </div>

                        <x-ui.alert variant="warning" class="mb-2">
                            <p class="font-semibold">Konfirmasi Pendaftaran</p>
                            <p class="mt-1">Dengan menekan tombol "Kirim Pendaftaran", saya menyatakan bahwa seluruh data yang diisi adalah benar dan dapat dipertanggungjawabkan.</p>
                        </x-ui.alert>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- ═══════════════════════════════════════════ NAVIGATION ═══════════════════════════════════════════ --}}
            <div class="mt-6 flex items-center justify-between">
                <div>
                    <x-ui.button type="button" variant="secondary" icon="arrow-left" x-show="step > 1" @click="step--">
                        Sebelumnya
                    </x-ui.button>
                </div>

                <div>
                    <x-ui.button type="button" variant="primary" icon-right="arrow-right" x-show="step < 7" @click="step++">
                        Selanjutnya
                    </x-ui.button>

                    <x-ui.button type="submit" variant="success" icon="check-circle" x-show="step === 7">
                        Kirim Pendaftaran
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.publik>
