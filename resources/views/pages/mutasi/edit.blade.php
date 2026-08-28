@php
    $r = $registration;
    $dokumenRows = [
        'scanned_rekomendasi' => 'Surat Rekomendasi Madrasah (wajib)',
        'scanned_rapor' => 'Rapor / Transkrip Nilai',
        'scanned_kk' => 'Scan Kartu Keluarga',
        'scanned_kk_wali' => 'Scan KK Wali (opsional)',
        'scanned_akta' => 'Scan Akta Kelahiran',
        'scanned_ijazah' => 'Scan Ijazah / SKL (opsional)',
        'scanned_photo' => 'Pas Foto (opsional)',
    ];
@endphp

<x-layouts.page
    :title="'Edit Pendaftar Pindah — '.$r->registration_no"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mutasi.index">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Edit Pendaftar Pindah</h1>
                <p class="mt-1 text-sm text-ink-soft">{{ $r->registration_no }} · {{ strtoupper($r->name) }}</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>
                <p class="font-semibold">Terdapat kesalahan pengisian.</p>
                <ul class="mt-1 list-disc list-inside text-xs">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </x-ui.alert></div>
        @endif

        <form method="POST" action="{{ route('mutasi.update', $r) }}" class="mt-6">
            @csrf
            @method('PUT')

            {{-- ══ A. Data Siswa ══ --}}
            <x-ui.sheet title="A. Data Siswa" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <x-ui.field label="Nama Lengkap" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $r->name)" required maxlength="100" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="NIK" required :error="$errors->first('nik')">
                            <x-ui.input name="nik" :value="old('nik', $r->nik)" required maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="NISN" :error="$errors->first('nisn')">
                            <x-ui.input name="nisn" :value="old('nisn', $r->nisn)" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="NIS Asal" :error="$errors->first('nis_asal')">
                            <x-ui.input name="nis_asal" :value="old('nis_asal', $r->nis_asal)" maxlength="20" />
                        </x-ui.field>
                        <x-ui.field label="Asal Sekolah" :error="$errors->first('previous_school')">
                            <x-ui.input name="previous_school" :value="old('previous_school', $r->previous_school)" maxlength="100" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Jenis Kelamin" required :error="$errors->first('gender')">
                            <x-ui.select name="gender" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :selected="old('gender', $r->gender)" />
                        </x-ui.field>
                        <x-ui.field label="Agama" required :error="$errors->first('religion')">
                            <x-ui.select name="religion" :options="['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']" :selected="old('religion', $r->religion)" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Tempat Lahir" :error="$errors->first('birth_place')">
                            <x-ui.input name="birth_place" :value="old('birth_place', $r->birth_place)" maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Tanggal Lahir" :error="$errors->first('birth_date')">
                            <x-ui.input type="date" name="birth_date" :value="old('birth_date', $r->birth_date?->format('Y-m-d'))" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Hobi" :error="$errors->first('hobby')">
                            <x-ui.select name="hobby" :options="['Olah Raga' => 'Olah Raga', 'Kesenian' => 'Kesenian', 'Membaca' => 'Membaca', 'Menulis' => 'Menulis', 'Traveling' => 'Traveling', 'Lainnya' => 'Lainnya']" :selected="old('hobby', $r->hobby)" />
                        </x-ui.field>
                        <x-ui.field label="Cita-cita" :error="$errors->first('ambition')">
                            <x-ui.select name="ambition" :options="['PNS' => 'PNS', 'TNI-Polri' => 'TNI-Polri', 'Guru/Dosen' => 'Guru/Dosen', 'Dokter' => 'Dokter', 'Wiraswasta' => 'Wiraswasta', 'Lainnya' => 'Lainnya']" :selected="old('ambition', $r->ambition)" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Anak Ke-" :error="$errors->first('child_order')">
                            <x-ui.input type="number" name="child_order" :value="old('child_order', $r->child_order)" min="0" max="99" />
                        </x-ui.field>
                        <x-ui.field label="Jumlah Saudara" :error="$errors->first('sibling_count')">
                            <x-ui.input type="number" name="sibling_count" :value="old('sibling_count', $r->sibling_count)" min="0" max="99" />
                        </x-ui.field>
                        <x-ui.field label="Pernah TK / PAUD" :error="$errors->first('ever_tk')">
                            <x-ui.select name="ever_tk" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('ever_tk', $r->ever_tk)" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Tanggal Masuk" :error="$errors->first('entry_date')">
                        <x-ui.input type="date" name="entry_date" :value="old('entry_date', $r->entry_date?->format('Y-m-d'))" />
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            {{-- ══ B. Kesehatan & Kebutuhan Khusus ══ --}}
            <x-ui.sheet title="B. Imunisasi & Berkebutuhan Khusus" pinned ruled class="mb-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach (['imm_hepb' => 'Hepatitis B', 'imm_polio' => 'Polio', 'imm_bcg' => 'BCG', 'imm_campak' => 'Campak', 'imm_dpt' => 'DPT', 'imm_covid' => 'COVID-19'] as $f => $l)
                        <label class="flex items-center gap-2 rounded-[var(--radius-control)] px-3 py-2.5 text-sm ring-1 ring-inset ring-rule-strong hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="{{ $f }}" value="1" @checked(old($f, $r->$f)) class="size-4 text-primary focus:ring-primary" />
                            <span class="text-ink">{{ $l }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach (['dis_deaf' => 'Tuli', 'dis_blind' => 'Tuna Netra', 'dis_disabled' => 'Tuna Daksa', 'dis_intellectual' => 'Tuna Grahita', 'dis_behavioral' => 'Berkebutuhan Perilaku', 'dis_slow_learner' => 'Lambat Belajar', 'dis_communication' => 'Gangguan Komunikasi', 'dis_gifted' => 'Berdaya Istimewa'] as $f => $l)
                        <label class="flex items-center gap-2 rounded-[var(--radius-control)] px-3 py-2 text-sm ring-1 ring-inset ring-rule-strong hover:bg-paper-deep cursor-pointer">
                            <input type="checkbox" name="{{ $f }}" value="1" @checked(old($f, $r->$f)) class="size-4 text-primary focus:ring-primary" />
                            <span class="text-ink">{{ $l }}</span>
                        </label>
                    @endforeach
                </div>
            </x-ui.sheet>

            {{-- ══ C. Tujuan Mutasi & Alamat Siswa ══ --}}
            <x-ui.sheet title="C. Tujuan Mutasi & Alamat Siswa" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Kelas Tujuan" required :error="$errors->first('kelas_tujuan')">
                            <x-ui.input name="kelas_tujuan" :value="old('kelas_tujuan', $r->kelas_tujuan)" required maxlength="20" />
                        </x-ui.field>
                        <x-ui.field label="Tanggal Mutasi" :error="$errors->first('tanggal_mutasi')">
                            <x-ui.input type="date" name="tanggal_mutasi" :value="old('tanggal_mutasi', $r->tanggal_mutasi?->format('Y-m-d'))" />
                        </x-ui.field>
                        <x-ui.field label="Tinggal Bersama" :error="$errors->first('residence_type')">
                            <x-ui.select name="residence_type" :options="['Tinggal dgn Ortu' => 'Tinggal dengan Orang Tua', 'Ikut Saudara' => 'Ikut Saudara', 'Asrama' => 'Asrama', 'Kontrak' => 'Kontrak/Kost', 'Lainnya' => 'Lainnya']" :selected="old('residence_type', $r->residence_type)" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Alasan Pindah" required :error="$errors->first('alasan_pindah')">
                        <textarea name="alasan_pindah" rows="3" required maxlength="1000"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">{{ old('alasan_pindah', $r->alasan_pindah) }}</textarea>
                    </x-ui.field>
                    <x-ui.field label="Alamat Lengkap" required :error="$errors->first('address')">
                        <textarea name="address" rows="2" required maxlength="255"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">{{ old('address', $r->address) }}</textarea>
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Provinsi" required :error="$errors->first('province')">
                            <x-ui.input name="province" :value="old('province', $r->province)" required maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Kota/Kabupaten" required :error="$errors->first('city')">
                            <x-ui.input name="city" :value="old('city', $r->city)" required maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Kecamatan" required :error="$errors->first('district')">
                            <x-ui.input name="district" :value="old('district', $r->district)" required maxlength="60" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Kelurahan/Desa" :error="$errors->first('village')">
                        <x-ui.input name="village" :value="old('village', $r->village)" maxlength="60" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="RT" :error="$errors->first('rt')">
                            <x-ui.input name="rt" :value="old('rt', $r->rt)" maxlength="3" pattern="[0-9]{1,3}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="RW" :error="$errors->first('rw')">
                            <x-ui.input name="rw" :value="old('rw', $r->rw)" maxlength="3" pattern="[0-9]{1,3}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="Kode Pos" :error="$errors->first('postal_code')">
                            <x-ui.input name="postal_code" :value="old('postal_code', $r->postal_code)" maxlength="5" pattern="[0-9]{5}" inputmode="numeric" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Jarak ke Madrasah" :error="$errors->first('distance')">
                            <x-ui.select name="distance" :options="['<5km' => '< 5 km', '5-10km' => '5–10 km', '11-20km' => '11–20 km', '21-30km' => '21–30 km', '>30km' => '> 30 km']" :selected="old('distance', $r->distance)" />
                        </x-ui.field>
                        <x-ui.field label="Kendaraan" :error="$errors->first('transport')">
                            <x-ui.select name="transport" :options="['Jalan Kaki' => 'Jalan Kaki', 'Sepeda' => 'Sepeda', 'Sepeda Motor' => 'Sepeda Motor', 'Mobil Pribadi' => 'Mobil Pribadi', 'Angkot' => 'Angkot', 'Lainnya' => 'Lainnya']" :selected="old('transport', $r->transport)" />
                        </x-ui.field>
                        <x-ui.field label="Waktu Tempuh" :error="$errors->first('commute_time')">
                            <x-ui.select name="commute_time" :options="['1-10 menit' => '1–10 menit', '10-19 menit' => '10–19 menit', '20-29 menit' => '20–29 menit', '30-39 menit' => '30–39 menit', '1-2 jam' => '1–2 jam', '>2 jam' => '> 2 jam']" :selected="old('commute_time', $r->commute_time)" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Telepon Rumah" :error="$errors->first('home_phone')">
                            <x-ui.input name="home_phone" :value="old('home_phone', $r->home_phone)" maxlength="20" />
                        </x-ui.field>
                        <x-ui.field label="HP Siswa" required :error="$errors->first('student_phone')">
                            <x-ui.input name="student_phone" :value="old('student_phone', $r->student_phone)" required maxlength="20" />
                        </x-ui.field>
                        <x-ui.field label="Email Siswa" :error="$errors->first('student_email')">
                            <x-ui.input type="email" name="student_email" :value="old('student_email', $r->student_email)" maxlength="100" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- ══ D. Orang Tua / Wali ══ --}}
            <x-ui.sheet title="D. Data Orang Tua / Wali" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="No. KK" :error="$errors->first('kk_number')">
                            <x-ui.input name="kk_number" :value="old('kk_number', $r->kk_number)" maxlength="16" pattern="[0-9]{16}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="Kepala Keluarga" :error="$errors->first('kk_head_name')">
                            <x-ui.input name="kk_head_name" :value="old('kk_head_name', $r->kk_head_name)" maxlength="100" />
                        </x-ui.field>
                    </div>
                    {{-- Ayah --}}
                    <div class="rounded-[var(--radius-control)] bg-paper p-4 ring-1 ring-inset ring-rule-strong">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-ink-soft">Data Ayah</p>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-ui.field label="Nama Ayah" required :error="$errors->first('father_name')">
                                <x-ui.input name="father_name" :value="old('father_name', $r->father_name)" required maxlength="100" />
                            </x-ui.field>
                            <x-ui.field label="Status" :error="$errors->first('father_status')">
                                <x-ui.select name="father_status" :options="['Masih Hidup' => 'Masih Hidup', 'Sudah Meninggal' => 'Sudah Meninggal']" :selected="old('father_status', $r->father_status)" />
                            </x-ui.field>
                            <x-ui.field label="NIK" :error="$errors->first('father_nik')">
                                <x-ui.input name="father_nik" :value="old('father_nik', $r->father_nik)" maxlength="16" />
                            </x-ui.field>
                            <x-ui.field label="Tempat Lahir" :error="$errors->first('father_birth_place')">
                                <x-ui.input name="father_birth_place" :value="old('father_birth_place', $r->father_birth_place)" maxlength="60" />
                            </x-ui.field>
                            <x-ui.field label="Tanggal Lahir" :error="$errors->first('father_birth_date')">
                                <x-ui.input type="date" name="father_birth_date" :value="old('father_birth_date', $r->father_birth_date?->format('Y-m-d'))" />
                            </x-ui.field>
                            <x-ui.field label="Pendidikan" :error="$errors->first('father_education')">
                                <x-ui.select name="father_education" :options="['0' => 'Tidak Sekolah', '1' => 'SD', '2' => 'SMP', '3' => 'SMA', '4' => 'D1', '5' => 'D2', '6' => 'D3', '7' => 'S1', '8' => 'S2', '9' => 'S3']" :selected="old('father_education', $r->father_education)" />
                            </x-ui.field>
                            <x-ui.field label="Pekerjaan" :error="$errors->first('father_job')">
                                <x-ui.select name="father_job" :options="['01' => 'Tidak Bekerja', '03' => 'PNS', '04' => 'TNI/Polri', '05' => 'Guru/Dosen', '06' => 'Swasta', '07' => 'Wiraswasta', '12' => 'Pedagang', '15' => 'Buruh', '18' => 'Lainnya']" :selected="old('father_job', $r->father_job)" />
                            </x-ui.field>
                            <x-ui.field label="Penghasilan" :error="$errors->first('father_income')">
                                <x-ui.select name="father_income" :options="['< Rp500rb' => '< Rp500rb', 'Rp500rb-1jt' => 'Rp500rb–1jt', 'Rp1jt-2jt' => 'Rp1jt–2jt', 'Rp2jt-3jt' => 'Rp2jt–3jt', 'Rp3jt-5jt' => 'Rp3jt–5jt', '> Rp5jt' => '> Rp5jt']" :selected="old('father_income', $r->father_income)" />
                            </x-ui.field>
                            <x-ui.field label="HP" :error="$errors->first('father_phone')">
                                <x-ui.input name="father_phone" :value="old('father_phone', $r->father_phone)" maxlength="20" />
                            </x-ui.field>
                        </div>
                    </div>
                    {{-- Ibu --}}
                    <div class="rounded-[var(--radius-control)] bg-paper p-4 ring-1 ring-inset ring-rule-strong">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-ink-soft">Data Ibu</p>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-ui.field label="Nama Ibu" required :error="$errors->first('mother_name')">
                                <x-ui.input name="mother_name" :value="old('mother_name', $r->mother_name)" required maxlength="100" />
                            </x-ui.field>
                            <x-ui.field label="Status" :error="$errors->first('mother_status')">
                                <x-ui.select name="mother_status" :options="['Masih Hidup' => 'Masih Hidup', 'Sudah Meninggal' => 'Sudah Meninggal']" :selected="old('mother_status', $r->mother_status)" />
                            </x-ui.field>
                            <x-ui.field label="NIK" :error="$errors->first('mother_nik')">
                                <x-ui.input name="mother_nik" :value="old('mother_nik', $r->mother_nik)" maxlength="16" />
                            </x-ui.field>
                            <x-ui.field label="Tempat Lahir" :error="$errors->first('mother_birth_place')">
                                <x-ui.input name="mother_birth_place" :value="old('mother_birth_place', $r->mother_birth_place)" maxlength="60" />
                            </x-ui.field>
                            <x-ui.field label="Tanggal Lahir" :error="$errors->first('mother_birth_date')">
                                <x-ui.input type="date" name="mother_birth_date" :value="old('mother_birth_date', $r->mother_birth_date?->format('Y-m-d'))" />
                            </x-ui.field>
                            <x-ui.field label="Pendidikan" :error="$errors->first('mother_education')">
                                <x-ui.select name="mother_education" :options="['0' => 'Tidak Sekolah', '1' => 'SD', '2' => 'SMP', '3' => 'SMA', '4' => 'D1', '5' => 'D2', '6' => 'D3', '7' => 'S1', '8' => 'S2', '9' => 'S3']" :selected="old('mother_education', $r->mother_education)" />
                            </x-ui.field>
                            <x-ui.field label="Pekerjaan" :error="$errors->first('mother_job')">
                                <x-ui.select name="mother_job" :options="['01' => 'Tidak Bekerja', '03' => 'PNS', '04' => 'TNI/Polri', '05' => 'Guru/Dosen', '06' => 'Swasta', '07' => 'Wiraswasta', '12' => 'Pedagang', '15' => 'Buruh', '18' => 'Lainnya']" :selected="old('mother_job', $r->mother_job)" />
                            </x-ui.field>
                            <x-ui.field label="Penghasilan" :error="$errors->first('mother_income')">
                                <x-ui.select name="mother_income" :options="['< Rp500rb' => '< Rp500rb', 'Rp500rb-1jt' => 'Rp500rb–1jt', 'Rp1jt-2jt' => 'Rp1jt–2jt', 'Rp2jt-3jt' => 'Rp2jt–3jt', 'Rp3jt-5jt' => 'Rp3jt–5jt', '> Rp5jt' => '> Rp5jt']" :selected="old('mother_income', $r->mother_income)" />
                            </x-ui.field>
                            <x-ui.field label="HP" :error="$errors->first('mother_phone')">
                                <x-ui.input name="mother_phone" :value="old('mother_phone', $r->mother_phone)" maxlength="20" />
                            </x-ui.field>
                        </div>
                    </div>
                    {{-- Wali --}}
                    <div class="rounded-[var(--radius-control)] bg-paper p-4 ring-1 ring-inset ring-rule-strong">
                        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-ink-soft">Data Wali (opsional)</p>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-ui.field label="Nama Wali" :error="$errors->first('guardian_name')">
                                <x-ui.input name="guardian_name" :value="old('guardian_name', $r->guardian_name)" maxlength="100" />
                            </x-ui.field>
                            <x-ui.field label="NIK" :error="$errors->first('guardian_nik')">
                                <x-ui.input name="guardian_nik" :value="old('guardian_nik', $r->guardian_nik)" maxlength="16" />
                            </x-ui.field>
                            <x-ui.field label="Tanggal Lahir" :error="$errors->first('guardian_birth_date')">
                                <x-ui.input type="date" name="guardian_birth_date" :value="old('guardian_birth_date', $r->guardian_birth_date?->format('Y-m-d'))" />
                            </x-ui.field>
                            <x-ui.field label="Pendidikan" :error="$errors->first('guardian_education')">
                                <x-ui.select name="guardian_education" :options="['0' => 'Tidak Sekolah', '1' => 'SD', '2' => 'SMP', '3' => 'SMA', '7' => 'S1', '8' => 'S2']" :selected="old('guardian_education', $r->guardian_education)" />
                            </x-ui.field>
                            <x-ui.field label="Pekerjaan" :error="$errors->first('guardian_job')">
                                <x-ui.input name="guardian_job" :value="old('guardian_job', $r->guardian_job)" maxlength="30" />
                            </x-ui.field>
                            <x-ui.field label="HP" :error="$errors->first('guardian_phone')">
                                <x-ui.input name="guardian_phone" :value="old('guardian_phone', $r->guardian_phone)" maxlength="20" />
                            </x-ui.field>
                        </div>
                    </div>
                    {{-- Bantuan Sosial --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <x-ui.field label="No. KKS" :error="$errors->first('social_kks')">
                            <x-ui.input name="social_kks" :value="old('social_kks', $r->social_kks)" maxlength="30" />
                        </x-ui.field>
                        <x-ui.field label="No. PKH" :error="$errors->first('social_pkh')">
                            <x-ui.input name="social_pkh" :value="old('social_pkh', $r->social_pkh)" maxlength="30" />
                        </x-ui.field>
                        <x-ui.field label="No. KIP" :error="$errors->first('social_kip')">
                            <x-ui.input name="social_kip" :value="old('social_kip', $r->social_kip)" maxlength="30" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- ══ E. Alamat Orang Tua ══ --}}
            <x-ui.sheet title="E. Alamat Orang Tua" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <x-ui.field label="Kepemilikan Rumah" :error="$errors->first('parent_ownership')">
                        <x-ui.select name="parent_ownership" :options="['Milik Sendiri' => 'Milik Sendiri', 'Milik Orang Tua' => 'Milik Orang Tua', 'Milik Saudara' => 'Milik Saudara', 'Rumah Dinas' => 'Rumah Dinas', 'Sewa/Kontrak' => 'Sewa/Kontrak', 'Lainnya' => 'Lainnya']" :selected="old('parent_ownership', $r->parent_ownership)" />
                    </x-ui.field>
                    <x-ui.field label="Alamat Lengkap" :error="$errors->first('parent_address')">
                        <textarea name="parent_address" rows="2" maxlength="255"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">{{ old('parent_address', $r->parent_address) }}</textarea>
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Provinsi" :error="$errors->first('parent_province')">
                            <x-ui.input name="parent_province" :value="old('parent_province', $r->parent_province)" maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Kota/Kabupaten" :error="$errors->first('parent_city')">
                            <x-ui.input name="parent_city" :value="old('parent_city', $r->parent_city)" maxlength="60" />
                        </x-ui.field>
                        <x-ui.field label="Kecamatan" :error="$errors->first('parent_district')">
                            <x-ui.input name="parent_district" :value="old('parent_district', $r->parent_district)" maxlength="60" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Kelurahan/Desa" :error="$errors->first('parent_village')">
                        <x-ui.input name="parent_village" :value="old('parent_village', $r->parent_village)" maxlength="60" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="RT" :error="$errors->first('parent_rt')">
                            <x-ui.input name="parent_rt" :value="old('parent_rt', $r->parent_rt)" maxlength="3" />
                        </x-ui.field>
                        <x-ui.field label="RW" :error="$errors->first('parent_rw')">
                            <x-ui.input name="parent_rw" :value="old('parent_rw', $r->parent_rw)" maxlength="3" />
                        </x-ui.field>
                        <x-ui.field label="Kode Pos" :error="$errors->first('parent_postal_code')">
                            <x-ui.input name="parent_postal_code" :value="old('parent_postal_code', $r->parent_postal_code)" maxlength="5" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- ══ F. Madrasah / Sekolah Asal ══ --}}
            <x-ui.sheet title="F. Madrasah / Sekolah Asal" pinned ruled class="mb-5">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Nama Madrasah Asal" required :error="$errors->first('origin_school')">
                            <x-ui.input name="origin_school" :value="old('origin_school', $r->origin_school)" required maxlength="100" />
                        </x-ui.field>
                        <x-ui.field label="Kelas Asal" required :error="$errors->first('kelas_asal')">
                            <x-ui.input name="kelas_asal" :value="old('kelas_asal', $r->kelas_asal)" required maxlength="20" />
                        </x-ui.field>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="NSM" :error="$errors->first('origin_nsm')">
                            <x-ui.input name="origin_nsm" :value="old('origin_nsm', $r->origin_nsm)" maxlength="12" pattern="[0-9]{12}" inputmode="numeric" />
                        </x-ui.field>
                        <x-ui.field label="NPSN" :error="$errors->first('origin_npsn')">
                            <x-ui.input name="origin_npsn" :value="old('origin_npsn', $r->origin_npsn)" maxlength="8" pattern="[0-9]{8}" inputmode="numeric" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Alamat Madrasah Asal" :error="$errors->first('origin_address')">
                        <textarea name="origin_address" rows="2" maxlength="255"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">{{ old('origin_address', $r->origin_address) }}</textarea>
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            {{-- ══ G. Dokumen ══ --}}
            <x-ui.sheet title="G. Dokumen (Tautan Google Drive)" pinned ruled class="mb-5">
                <div class="space-y-4">
                    @foreach ($dokumenRows as $key => $label)
                        <x-ui.field :label="$label" {{ $key === 'scanned_rekomendasi' ? 'required' : '' }} :error="$errors->first($key)">
                            <x-ui.input type="url" name="{{ $key }}" :value="old($key, $r->{$key})" maxlength="500"
                                {{ $key === 'scanned_rekomendasi' ? 'required' : '' }} />
                        </x-ui.field>
                    @endforeach
                </div>
            </x-ui.sheet>

            {{-- Tombol --}}
            <div class="flex justify-end gap-2">
                <x-ui.button variant="ghost" size="md" href="{{ route('mutasi.show', $r) }}">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" size="md" icon="check-circle">Simpan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>