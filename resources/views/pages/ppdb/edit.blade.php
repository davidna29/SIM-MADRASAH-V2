<x-layouts.page
    :title="'Edit - '.$registration->registration_no"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ppdb.index">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Edit Calon Siswa</h1>
                <p class="mt-1 text-sm text-ink-soft">{{ $registration->registration_no }} · {{ strtoupper($registration->name) }}</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <p class="font-semibold">Terdapat kesalahan pada pengisian formulir.</p>
                    <ul class="mt-1 list-disc list-inside text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            </div>
        @endif

        <form method="POST" action="{{ route('ppdb.update', $registration) }}" class="mt-6">
            @csrf
            @method('PUT')

            {{-- A. Data Siswa --}}
            <x-ui.sheet title="A. Data Siswa" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <x-ui.field label="Nama Lengkap" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $registration->name)" required maxlength="100" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="NIK" required :error="$errors->first('nik')">
                            <x-ui.input name="nik" :value="old('nik', $registration->nik)" required maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="NISN" :error="$errors->first('nisn')">
                            <x-ui.input name="nisn" :value="old('nisn', $registration->nisn)" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Jenis Kelamin" required :error="$errors->first('gender')">
                            <x-ui.select name="gender" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :selected="old('gender', $registration->gender)" />
                        </x-ui.field>
                        <x-ui.field label="Agama" required :error="$errors->first('religion')">
                            <x-ui.select name="religion" :options="['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']" :selected="old('religion', $registration->religion)" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Tempat Lahir" required :error="$errors->first('birth_place')">
                            <x-ui.input name="birth_place" :value="old('birth_place', $registration->birth_place)" required maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Tanggal Lahir" required :error="$errors->first('birth_date')">
                            <x-ui.input type="date" name="birth_date" :value="old('birth_date', $registration->birth_date?->format('Y-m-d'))" required />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Asal Sekolah" :error="$errors->first('previous_school')">
                        <x-ui.input name="previous_school" :value="old('previous_school', $registration->previous_school)" maxlength="100" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Hobi" required :error="$errors->first('hobby')">
                            <x-ui.select name="hobby" :options="['Olah Raga' => 'Olah Raga', 'Kesenian' => 'Kesenian', 'Membaca' => 'Membaca', 'Menulis' => 'Menulis', 'Traveling' => 'Traveling', 'Lainnya' => 'Lainnya']" :selected="old('hobby', $registration->hobby)" />
                        </x-ui.field>
                        <x-ui.field label="Cita-cita" required :error="$errors->first('ambition')">
                            <x-ui.select name="ambition" :options="['PNS' => 'PNS', 'TNI-Polri' => 'TNI-Polri', 'Guru-Dosen' => 'Guru/Dosen', 'Dokter' => 'Dokter', 'Politikus' => 'Politikus', 'Wiraswasta' => 'Wiraswasta', 'Pekerja Seni' => 'Pekerja Seni', 'Lainnya' => 'Lainnya']" :selected="old('ambition', $registration->ambition)" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Anak Ke-" required :error="$errors->first('child_order')">
                            <x-ui.input type="number" name="child_order" :value="old('child_order', $registration->child_order)" min="1" max="9" required />
                        </x-ui.field>
                        <x-ui.field label="Jumlah Saudara Kandung" required :error="$errors->first('sibling_count')">
                            <x-ui.input type="number" name="sibling_count" :value="old('sibling_count', $registration->sibling_count)" min="0" max="9" required />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Pernah Masuk TK?" required :error="$errors->first('ever_tk')">
                            <x-ui.select name="ever_tk" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('ever_tk', $registration->ever_tk)" />
                        </x-ui.field>
                        <x-ui.field label="Pernah Masuk PAUD?" required :error="$errors->first('ever_paud')">
                            <x-ui.select name="ever_paud" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('ever_paud', $registration->ever_paud)" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Tanggal Masuk" :error="$errors->first('entry_date')">
                        <x-ui.input type="date" name="entry_date" :value="old('entry_date', $registration->entry_date?->format('Y-m-d'))" />
                    </x-ui.field>

                    <div class="border-t border-rule/70 pt-5">
                        <h3 class="text-sm font-bold tracking-tight text-ink">Dokumen (Tautan Google Drive)</h3>
                    </div>
                    <x-ui.field label="Scan Kartu Keluarga" required :error="$errors->first('scanned_kk')">
                        <x-ui.input type="url" name="scanned_kk" :value="old('scanned_kk', $registration->scanned_kk)" required maxlength="500" />
                    </x-ui.field>
                    <x-ui.field label="Scan Kartu Keluarga Wali (Opsional)" :error="$errors->first('scanned_kk_wali')">
                        <x-ui.input type="url" name="scanned_kk_wali" :value="old('scanned_kk_wali', $registration->scanned_kk_wali)" maxlength="500" />
                    </x-ui.field>
                    <x-ui.field label="Scan Akta Kelahiran" required :error="$errors->first('scanned_akta')">
                        <x-ui.input type="url" name="scanned_akta" :value="old('scanned_akta', $registration->scanned_akta)" required maxlength="500" />
                    </x-ui.field>
                    <x-ui.field label="Scan Ijazah (Opsional)" :error="$errors->first('scanned_ijazah')">
                        <x-ui.input type="url" name="scanned_ijazah" :value="old('scanned_ijazah', $registration->scanned_ijazah)" maxlength="500" />
                    </x-ui.field>
                    <x-ui.field label="Pas Foto (Opsional)" :error="$errors->first('scanned_photo')">
                        <x-ui.input type="url" name="scanned_photo" :value="old('scanned_photo', $registration->scanned_photo)" maxlength="500" />
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            {{-- B+C. Kesehatan & Berkebutuhan Khusus --}}
            <x-ui.sheet title="B. Imunisasi & C. Berkebutuhan Khusus" pinned ruled class="mb-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        'imm_hepb' => 'Hepatitis B', 'imm_polio' => 'Polio', 'imm_bcg' => 'BCG',
                        'imm_campak' => 'Campak', 'imm_dpt' => 'DPT', 'imm_covid' => 'COVID-19',
                    ] as $field => $label)
                        <x-ui.field :label="$label" required :error="$errors->first($field)">
                            <x-ui.select :name="$field" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old($field, $registration->$field)" />
                        </x-ui.field>
                    @endforeach
                </div>
                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach ([
                        'dis_deaf' => 'Tuli (Deaf)', 'dis_blind' => 'Tuna Netra (Blind)',
                        'dis_disabled' => 'Tuna Daksa (Disabled)', 'dis_intellectual' => 'Tuna Grahita (Intellectual)',
                        'dis_behavioral' => 'Berkebutuhan Perilaku', 'dis_slow_learner' => 'Lambat Belajar',
                        'dis_communication' => 'Gangguan Komunikasi', 'dis_gifted' => 'Berdaya Istimewa',
                    ] as $field => $label)
                        <label class="flex items-center gap-3 rounded-[var(--radius-control)] px-3 py-2.5 ring-1 ring-inset ring-rule-strong hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="{{ $field }}" value="1" @checked((bool) old($field, $registration->$field)) class="size-4 rounded border-rule-strong text-primary focus:ring-primary" />
                            <span class="text-sm text-ink">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </x-ui.sheet>

            {{-- D. Alamat Siswa --}}
            <x-ui.sheet title="D. Alamat Siswa" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <x-ui.field label="Tinggal Bersama" required :error="$errors->first('residence_type')">
                        <x-ui.select name="residence_type" :options="['Tinggal dgn Ortu' => 'Tinggal dengan Orang Tua', 'Ikut Saudara' => 'Ikut Saudara', 'Asrama Madrasah' => 'Asrama Madrasah', 'Kontrak-Kost' => 'Kontrak/Kost', 'Asrama Pesantren' => 'Asrama Pesantren', 'Panti Asuhan' => 'Panti Asuhan', 'Rumah Singgah' => 'Rumah Singgah', 'Lainnya' => 'Lainnya']" :selected="old('residence_type', $registration->residence_type)" />
                    </x-ui.field>
                    <x-ui.field label="Alamat Lengkap" required :error="$errors->first('address')">
                        <textarea name="address" rows="2" required maxlength="255" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">{{ old('address', $registration->address) }}</textarea>
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Provinsi" required :error="$errors->first('province')">
                            <x-ui.input name="province" :value="old('province', $registration->province)" required maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Kota/Kabupaten" required :error="$errors->first('city')">
                            <x-ui.input name="city" :value="old('city', $registration->city)" required maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Kecamatan" required :error="$errors->first('district')">
                            <x-ui.select name="district" :options="['Pahandut' => 'Pahandut', 'Bukit Batu' => 'Bukit Batu', 'Jekan Raya' => 'Jekan Raya', 'Sebangau' => 'Sebangau', 'Rakumpit' => 'Rakumpit']" :selected="old('district', $registration->district)" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Kelurahan/Desa" required :error="$errors->first('village')">
                        <x-ui.input name="village" :value="old('village', $registration->village)" required maxlength="60" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="RT" required :error="$errors->first('rt')">
                            <x-ui.input name="rt" :value="old('rt', $registration->rt)" required maxlength="3" pattern="[0-9]{1,3}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="RW" required :error="$errors->first('rw')">
                            <x-ui.input name="rw" :value="old('rw', $registration->rw)" required maxlength="3" pattern="[0-9]{1,3}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="Kode Pos" required :error="$errors->first('postal_code')">
                            <x-ui.input name="postal_code" :value="old('postal_code', $registration->postal_code)" required maxlength="5" pattern="[0-9]{5}" inputmode="numeric" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Jarak ke Madrasah" required :error="$errors->first('distance')">
                            <x-ui.select name="distance" :options="['<5km' => '< 5 km', '5-10km' => '5 – 10 km', '11-20km' => '11 – 20 km', '21-30km' => '21 – 30 km', '>30km' => '> 30 km']" :selected="old('distance', $registration->distance)" />
                        </x-ui.field>
                        <x-ui.field label="Kendaraan" required :error="$errors->first('transport')">
                            <x-ui.select name="transport" :options="['Jalan Kaki' => 'Jalan Kaki', 'Sepeda' => 'Sepeda', 'Sepeda Motor' => 'Sepeda Motor', 'Mobil Pribadi' => 'Mobil Pribadi', 'Antar Jemput Sekolah' => 'Antar Jemput Sekolah', 'Angkot' => 'Angkot', 'Lainnya' => 'Lainnya']" :selected="old('transport', $registration->transport)" />
                        </x-ui.field>
                        <x-ui.field label="Waktu Tempuh" required :error="$errors->first('commute_time')">
                            <x-ui.select name="commute_time" :options="['1-10 menit' => '1 – 10 menit', '10-19 menit' => '10 – 19 menit', '20-29 menit' => '20 – 29 menit', '30-39 menit' => '30 – 39 menit', '1-2 jam' => '1 – 2 jam', '>2 jam' => '> 2 jam']" :selected="old('commute_time', $registration->commute_time)" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Telepon Rumah" :error="$errors->first('home_phone')">
                            <x-ui.input name="home_phone" :value="old('home_phone', $registration->home_phone)" maxlength="20" />
                        </x-ui.field>
                        <x-ui.field label="Telepon Siswa" :error="$errors->first('student_phone')">
                            <x-ui.input name="student_phone" :value="old('student_phone', $registration->student_phone)" maxlength="20" />
                        </x-ui.field>
                        <x-ui.field label="Email Siswa" :error="$errors->first('student_email')">
                            <x-ui.input type="email" name="student_email" :value="old('student_email', $registration->student_email)" maxlength="100" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- E. Orang Tua / Wali --}}
            <x-ui.sheet title="E. Data Orang Tua / Wali" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Nomor Kartu Keluarga" required :error="$errors->first('kk_number')">
                            <x-ui.input name="kk_number" :value="old('kk_number', $registration->kk_number)" required maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="Nama Kepala Keluarga" required :error="$errors->first('kk_head_name')">
                            <x-ui.input name="kk_head_name" :value="old('kk_head_name', $registration->kk_head_name)" required maxlength="100" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Nama Ayah" required :error="$errors->first('father_name')">
                            <x-ui.input name="father_name" :value="old('father_name', $registration->father_name)" required maxlength="100" />
                        </x-ui.field>
                        <x-ui.field label="Status Ayah" required :error="$errors->first('father_status')">
                            <x-ui.select name="father_status" :options="['Masih Hidup' => 'Masih Hidup', 'Sudah Meninggal' => 'Sudah Meninggal', 'Tanpa Keterangan' => 'Tanpa Keterangan']" :selected="old('father_status', $registration->father_status)" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="NIK Ayah" :error="$errors->first('father_nik')">
                            <x-ui.input name="father_nik" :value="old('father_nik', $registration->father_nik)" maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="Tanggal Lahir Ayah" :error="$errors->first('father_birth_date')">
                            <x-ui.input type="date" name="father_birth_date" :value="old('father_birth_date', $registration->father_birth_date?->format('Y-m-d'))" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Tempat Lahir Ayah" :error="$errors->first('father_birth_place')">
                            <x-ui.input name="father_birth_place" :value="old('father_birth_place', $registration->father_birth_place)" maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Pekerjaan Ayah" :error="$errors->first('father_job')">
                            <x-ui.select name="father_job" :options="['01' => 'Tidak Bekerja', '02' => 'Pensiunan', '03' => 'PNS (Selain poin 05 dan 10)', '04' => 'TNI/Polisi', '05' => 'Guru/Dosen', '06' => 'Pegawai Swasta', '07' => 'Wiraswasta', '12' => 'Pedagang', '15' => 'Buruh (Tani/Pabrik/Bangunan)', '18' => 'Lainnya']" :selected="old('father_job', $registration->father_job)" />
                        </x-ui.field>
                        <x-ui.field label="HP Ayah" :error="$errors->first('father_phone')">
                            <x-ui.input name="father_phone" :value="old('father_phone', $registration->father_phone)" maxlength="20" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Nama Ibu" required :error="$errors->first('mother_name')">
                            <x-ui.input name="mother_name" :value="old('mother_name', $registration->mother_name)" required maxlength="100" />
                        </x-ui.field>
                        <x-ui.field label="Status Ibu" required :error="$errors->first('mother_status')">
                            <x-ui.select name="mother_status" :options="['Masih Hidup' => 'Masih Hidup', 'Sudah Meninggal' => 'Sudah Meninggal', 'Tidak Diketahui' => 'Tidak Diketahui']" :selected="old('mother_status', $registration->mother_status)" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="NIK Ibu" required :error="$errors->first('mother_nik')">
                            <x-ui.input name="mother_nik" :value="old('mother_nik', $registration->mother_nik)" required maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="Tempat Lahir Ibu" :error="$errors->first('mother_birth_place')">
                            <x-ui.input name="mother_birth_place" :value="old('mother_birth_place', $registration->mother_birth_place)" maxlength="60" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Tanggal Lahir Ibu" required :error="$errors->first('mother_birth_date')">
                            <x-ui.input type="date" name="mother_birth_date" :value="old('mother_birth_date', $registration->mother_birth_date?->format('Y-m-d'))" required />
                        </x-ui.field>
                        <x-ui.field label="Pendidikan Ibu" required :error="$errors->first('mother_education')">
                            <x-ui.select name="mother_education" :options="['0' => 'Tidak Sekolah', '1' => 'SD/Sederajat', '2' => 'SMP/Sederajat', '3' => 'SMA/Sederajat', '4' => 'D1', '5' => 'D2', '6' => 'D3', '7' => 'D4-S1', '8' => 'S2', '9' => 'S3']" :selected="old('mother_education', $registration->mother_education)" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Pekerjaan Ibu" required :error="$errors->first('mother_job')">
                            <x-ui.select name="mother_job" :options="['01' => 'Tidak Bekerja', '02' => 'Pensiunan', '03' => 'PNS (Selain poin 05 dan 10)', '04' => 'TNI/Polisi', '05' => 'Guru/Dosen', '06' => 'Pegawai Swasta', '07' => 'Wiraswasta', '12' => 'Pedagang', '15' => 'Buruh (Tani/Pabrik/Bangunan)', '18' => 'Lainnya']" :selected="old('mother_job', $registration->mother_job)" />
                        </x-ui.field>
                        <x-ui.field label="Penghasilan Ibu" required :error="$errors->first('mother_income')">
                            <x-ui.select name="mother_income" :options="['< Rp500rb' => '< Rp 500.000', 'Rp500rb – 1jt' => 'Rp 500.000 – 1.000.000', 'Rp1jt – 2jt' => 'Rp 1.000.000 – 2.000.000', 'Rp2jt – 3jt' => 'Rp 2.000.000 – 3.000.000', 'Rp3jt – 5jt' => 'Rp 3.000.000 – 5.000.000', '> Rp5jt' => '> Rp 5.000.000']" :selected="old('mother_income', $registration->mother_income)" />
                        </x-ui.field>
                        <x-ui.field label="HP Ibu" :error="$errors->first('mother_phone')">
                            <x-ui.input name="mother_phone" :value="old('mother_phone', $registration->mother_phone)" maxlength="20" />
                        </x-ui.field>
                    </div>

                    <x-ui.field label="Nama Wali (Opsional)" :error="$errors->first('guardian_name')">
                        <x-ui.input name="guardian_name" :value="old('guardian_name', $registration->guardian_name)" maxlength="100" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="NIK Wali" :error="$errors->first('guardian_nik')">
                            <x-ui.input name="guardian_nik" :value="old('guardian_nik', $registration->guardian_nik)" maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="Telepon Wali" :error="$errors->first('guardian_phone')">
                            <x-ui.input name="guardian_phone" :value="old('guardian_phone', $registration->guardian_phone)" maxlength="20" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="No. KKS" :error="$errors->first('social_kks')">
                            <x-ui.input name="social_kks" :value="old('social_kks', $registration->social_kks)" maxlength="30" />
                        </x-ui.field>
                        <x-ui.field label="No. PKH" :error="$errors->first('social_pkh')">
                            <x-ui.input name="social_pkh" :value="old('social_pkh', $registration->social_pkh)" maxlength="30" />
                        </x-ui.field>
                        <x-ui.field label="No. KIP" :error="$errors->first('social_kip')">
                            <x-ui.input name="social_kip" :value="old('social_kip', $registration->social_kip)" maxlength="30" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- F. Alamat Orang Tua --}}
            <x-ui.sheet title="F. Alamat Orang Tua" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <x-ui.field label="Kepemilikan Rumah" required :error="$errors->first('parent_ownership')">
                        <x-ui.select name="parent_ownership" :options="['Milik Sendiri' => 'Milik Sendiri', 'Milik Wali' => 'Milik Wali', 'Rumah Orang Tua' => 'Rumah Orang Tua', 'Rumah Saudara' => 'Rumah Saudara', 'Rumah Dinas' => 'Rumah Dinas', 'Sewa-Kontrak' => 'Sewa/Kontrak', 'Lainnya' => 'Lainnya']" :selected="old('parent_ownership', $registration->parent_ownership)" />
                    </x-ui.field>
                    <x-ui.field label="Alamat Lengkap" required :error="$errors->first('parent_address')">
                        <textarea name="parent_address" rows="2" required maxlength="255" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">{{ old('parent_address', $registration->parent_address) }}</textarea>
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Provinsi" required :error="$errors->first('parent_province')">
                            <x-ui.input name="parent_province" :value="old('parent_province', $registration->parent_province)" required maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Kota/Kabupaten" required :error="$errors->first('parent_city')">
                            <x-ui.input name="parent_city" :value="old('parent_city', $registration->parent_city)" required maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Kecamatan" required :error="$errors->first('parent_district')">
                            <x-ui.select name="parent_district" :options="['Pahandut' => 'Pahandut', 'Bukit Batu' => 'Bukit Batu', 'Jekan Raya' => 'Jekan Raya', 'Sebangau' => 'Sebangau', 'Rakumpit' => 'Rakumpit']" :selected="old('parent_district', $registration->parent_district)" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Kelurahan/Desa" required :error="$errors->first('parent_village')">
                        <x-ui.input name="parent_village" :value="old('parent_village', $registration->parent_village)" required maxlength="60" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="RT" required :error="$errors->first('parent_rt')">
                            <x-ui.input name="parent_rt" :value="old('parent_rt', $registration->parent_rt)" required maxlength="3" pattern="[0-9]{1,3}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="RW" required :error="$errors->first('parent_rw')">
                            <x-ui.input name="parent_rw" :value="old('parent_rw', $registration->parent_rw)" required maxlength="3" pattern="[0-9]{1,3}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="Kode Pos" required :error="$errors->first('parent_postal_code')">
                            <x-ui.input name="parent_postal_code" :value="old('parent_postal_code', $registration->parent_postal_code)" required maxlength="5" pattern="[0-9]{5}" inputmode="numeric" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- G. Sekolah Asal --}}
            <x-ui.sheet title="G. Sekolah Asal" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <x-ui.field label="Nama Sekolah Asal" required :error="$errors->first('origin_school')">
                        <x-ui.input name="origin_school" :value="old('origin_school', $registration->origin_school)" required maxlength="100" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="NSM" :error="$errors->first('origin_nsm')">
                            <x-ui.input name="origin_nsm" :value="old('origin_nsm', $registration->origin_nsm)" maxlength="12" pattern="[0-9]{12}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="NPSN" :error="$errors->first('origin_npsn')">
                            <x-ui.input name="origin_npsn" :value="old('origin_npsn', $registration->origin_npsn)" maxlength="8" pattern="[0-9]{8}" inputmode="numeric" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Alamat Sekolah Asal" :error="$errors->first('origin_address')">
                        <textarea name="origin_address" rows="2" maxlength="255" class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">{{ old('origin_address', $registration->origin_address) }}</textarea>
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            <div class="flex justify-end gap-2">
                <x-ui.button variant="ghost" size="md" href="{{ route('ppdb.show', $registration) }}">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="md" icon="check-circle">Simpan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
