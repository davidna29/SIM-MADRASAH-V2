<x-layouts.page
    :title="$editing ? 'Ubah Siswa' : 'Tambah Siswa'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="siswa.create">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                    {{ $editing ? 'Ubah Siswa' : 'Tambah Siswa' }}
                </h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Lembar data siswa baru — data inti dipakai lintas modul tanpa entri ulang.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <form method="POST"
            action="{{ $editing ? route('siswa.update', $student) : route('siswa.store') }}"
            class="mt-6 space-y-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Data Inti" description="Identitas dasar siswa. NIK, nama, dan tanggal lahir sesuai akta.">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Nama Lengkap" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $editing ? $student->displayName() : '')" placeholder="Nama sesuai akta" />
                    </x-ui.field>
                    <x-ui.field label="NIS / NISN" required :error="$errors->first('nis')">
                        <x-ui.input name="nis" :value="old('nis', $editing ? $student->nis : '')" placeholder="Masukkan NIS" />
                    </x-ui.field>
                    <x-ui.field label="NIK" required hint="Nomor Induk Kependudukan — 16 digit." :error="$errors->first('nik')">
                        <x-ui.input name="nik" :value="old('nik', $editing && $student->person ? $student->person->nik : '')" placeholder="3508120503850001" maxlength="16" />
                    </x-ui.field>
                    <x-ui.field label="Tempat, Tanggal Lahir">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <x-ui.input name="birth_place" :value="old('birth_place', $editing && $student->person ? $student->person->birth_place : '')" placeholder="Tempat lahir" />
                            <x-ui.input name="birth_date" type="date" :value="old('birth_date', $editing && $student->person?->birth_date ? $student->person->birth_date->format('Y-m-d') : '')" />
                        </div>
                    </x-ui.field>
                    <x-ui.field label="Jenis Kelamin" required :error="$errors->first('gender')">
                        <x-ui.select name="gender" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :selected="old('gender', $editing ? $student->person?->gender : null)" />
                    </x-ui.field>
                    <x-ui.field label="Agama">
                        <x-ui.select name="religion" :options="['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Khonghucu' => 'Konghucu']" :selected="old('religion', $editing && $student->person ? $student->person->religion : null)" />
                    </x-ui.field>
                    <x-ui.field label="Nomor HP Orang Tua" hint="Untuk notifikasi kehadiran dan tagihan." :error="$errors->first('phone')">
                        <x-ui.input name="phone" prefix="+62" :value="old('phone', $editing && $student->person ? ltrim((string) $student->person->phone, '0') : '')" />
                    </x-ui.field>
                    <x-ui.field label="Email" :error="$errors->first('email')">
                        <x-ui.input name="email" type="email" :value="old('email', $editing && $student->person ? $student->person->email : '')" placeholder="nama@example.com" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Penempatan" description="Kelas pada tahun ajaran berjalan. Penempatan lama tercatat di papan riwayat.">
                <x-ui.field label="Kelas" :error="$errors->first('class_group_id')">
                    <x-ui.select name="class_group_id" :options="$classes->pluck('name', 'id')" :selected="old('class_group_id', $editing ? $student->enrollments->first()?->class_group_id : null)" placeholder="Pilih kelas (opsional)" />
                </x-ui.field>
            </x-ui.form-section>

            @if ($editing)
                @php
                    $ayahF = $student->guardianByRelation('ayah');
                    $ibuF = $student->guardianByRelation('ibu');
                    $waliF = $student->guardianByRelation('wali');
                    $docs = $student->documents ?? [];
                @endphp

                <x-ui.form-section title="Identitas & Asal" description="NISN, sekolah asal, dan riwayat pendidikan sebelum masuk.">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="NISN" :error="$errors->first('nisn')">
                            <x-ui.input name="nisn" :value="old('nisn', $student->nisn)" />
                        </x-ui.field>
                        <x-ui.field label="Sekolah Sebelumnya" :error="$errors->first('previous_school')">
                            <x-ui.input name="previous_school" :value="old('previous_school', $student->previous_school)" />
                        </x-ui.field>
                        <x-ui.field label="Tanggal Masuk" :error="$errors->first('entry_date')">
                            <x-ui.input type="date" name="entry_date" :value="old('entry_date', $student->entry_date?->format('Y-m-d'))" />
                        </x-ui.field>
                        <x-ui.field label="Sekolah Asal" :error="$errors->first('origin_school')">
                            <x-ui.input name="origin_school" :value="old('origin_school', $student->origin_school)" />
                        </x-ui.field>
                        <x-ui.field label="NSM Asal" :error="$errors->first('origin_nsm')">
                            <x-ui.input name="origin_nsm" :value="old('origin_nsm', $student->origin_nsm)" />
                        </x-ui.field>
                        <x-ui.field label="NPSN Asal" :error="$errors->first('origin_npsn')">
                            <x-ui.input name="origin_npsn" :value="old('origin_npsn', $student->origin_npsn)" />
                        </x-ui.field>
                        <div class="sm:col-span-3">
                            <x-ui.field label="Alamat Sekolah Asal" :error="$errors->first('origin_address')">
                                <x-ui.input name="origin_address" :value="old('origin_address', $student->origin_address)" />
                            </x-ui.field>
                        </div>
                        <x-ui.field label="Anak Ke" :error="$errors->first('child_order')">
                            <x-ui.input type="number" name="child_order" min="0" max="99" :value="old('child_order', $student->child_order)" />
                        </x-ui.field>
                        <x-ui.field label="Jumlah Saudara" :error="$errors->first('sibling_count')">
                            <x-ui.input type="number" name="sibling_count" min="0" max="99" :value="old('sibling_count', $student->sibling_count)" />
                        </x-ui.field>
                        <x-ui.field label="Pernah TK">
                            <x-ui.select name="ever_tk" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('ever_tk', $student->ever_tk)" placeholder="—" />
                        </x-ui.field>
                        <x-ui.field label="Pernah PAUD">
                            <x-ui.select name="ever_paud" :options="['PERNAH' => 'PERNAH', 'TIDAK' => 'TIDAK']" :selected="old('ever_paud', $student->ever_paud)" placeholder="—" />
                        </x-ui.field>
                        <x-ui.field label="Hobi" :error="$errors->first('hobby')">
                            <x-ui.input name="hobby" :value="old('hobby', $student->hobby)" />
                        </x-ui.field>
                        <x-ui.field label="Cita-cita" :error="$errors->first('ambition')">
                            <x-ui.input name="ambition" :value="old('ambition', $student->ambition)" />
                        </x-ui.field>
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Alamat Siswa" description="Alamat tinggal & akses ke madrasah.">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                        <x-ui.field label="Jenis Tempat Tinggal" :error="$errors->first('residence_type')">
                            <x-ui.input name="residence_type" :value="old('residence_type', $student->residence_type)" />
                        </x-ui.field>
                        <div class="sm:col-span-3">
                            <x-ui.field label="Alamat" :error="$errors->first('address')">
                                <x-ui.input name="address" :value="old('address', $student->person?->address)" />
                            </x-ui.field>
                        </div>
                        <x-ui.field label="Provinsi" :error="$errors->first('province')">
                            <x-ui.input name="province" :value="old('province', $student->person?->province)" />
                        </x-ui.field>
                        <x-ui.field label="Kota/Kab" :error="$errors->first('city')">
                            <x-ui.input name="city" :value="old('city', $student->person?->city)" />
                        </x-ui.field>
                        <x-ui.field label="Kecamatan" :error="$errors->first('district')">
                            <x-ui.input name="district" :value="old('district', $student->person?->district)" />
                        </x-ui.field>
                        <x-ui.field label="Kelurahan" :error="$errors->first('village')">
                            <x-ui.input name="village" :value="old('village', $student->person?->village)" />
                        </x-ui.field>
                        <x-ui.field label="RT" :error="$errors->first('rt')">
                            <x-ui.input name="rt" :value="old('rt', $student->person?->rt)" />
                        </x-ui.field>
                        <x-ui.field label="RW" :error="$errors->first('rw')">
                            <x-ui.input name="rw" :value="old('rw', $student->person?->rw)" />
                        </x-ui.field>
                        <x-ui.field label="Kode Pos" :error="$errors->first('postal_code')">
                            <x-ui.input name="postal_code" :value="old('postal_code', $student->person?->postal_code)" />
                        </x-ui.field>
                        <x-ui.field label="Telepon Rumah" :error="$errors->first('home_phone')">
                            <x-ui.input name="home_phone" :value="old('home_phone', $student->person?->home_phone)" />
                        </x-ui.field>
                        <x-ui.field label="Jarak" :error="$errors->first('distance')">
                            <x-ui.input name="distance" :value="old('distance', $student->distance)" />
                        </x-ui.field>
                        <x-ui.field label="Transportasi" :error="$errors->first('transport')">
                            <x-ui.input name="transport" :value="old('transport', $student->transport)" />
                        </x-ui.field>
                        <x-ui.field label="Waktu Tempuh" :error="$errors->first('commute_time')">
                            <x-ui.input name="commute_time" :value="old('commute_time', $student->commute_time)" />
                        </x-ui.field>
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Keluarga" description="Orang tua / wali; bila NIK terisi, data otomatis dipakai juga oleh anak lain.">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="No. KK" :error="$errors->first('kk_number')">
                            <x-ui.input name="kk_number" :value="old('kk_number', $student->kk_number)" />
                        </x-ui.field>
                        <x-ui.field label="Kepala Keluarga" :error="$errors->first('kk_head_name')">
                            <x-ui.input name="kk_head_name" :value="old('kk_head_name', $student->kk_head_name)" />
                        </x-ui.field>
                    </div>
                    <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-3">
                        @foreach (['ayah' => [$ayahF, 'father', 'Ayah'], 'ibu' => [$ibuF, 'mother', 'Ibu'], 'wali' => [$waliF, 'guardian', 'Wali']] as [$guardian, $prefix, $title])
                            @php
                                $hasStatus = $title !== 'Wali';
                            @endphp
                            <div class="rounded-[var(--radius-control)] bg-paper p-4 ring-1 ring-inset ring-rule-strong">
                                <input type="hidden" name="{{ $prefix }}_id" value="{{ $guardian?->id }}">
                                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-ink-soft">{{ $title }}</p>
                                <div class="space-y-3">
                                    <x-ui.field label="Nama" :error="$errors->first($prefix.'_name')">
                                        <x-ui.input name="{{ $prefix }}_name" :value="old($prefix.'_name', $guardian?->name)" />
                                    </x-ui.field>
                                    @if ($hasStatus)
                                        <x-ui.field label="Status" :error="$errors->first($prefix.'_status')">
                                            <x-ui.input name="{{ $prefix }}_status" :value="old($prefix.'_status', $guardian?->status)" />
                                        </x-ui.field>
                                    @endif
                                    <x-ui.field label="NIK" :error="$errors->first($prefix.'_nik')">
                                        <x-ui.input name="{{ $prefix }}_nik" :value="old($prefix.'_nik', $guardian?->nik)" maxlength="16" />
                                    </x-ui.field>
                                    <x-ui.field label="Tempat, Tanggal Lahir">
                                        <div class="grid grid-cols-1 gap-2">
                                            <x-ui.input name="{{ $prefix }}_birth_place" :value="old($prefix.'_birth_place', $guardian?->birth_place)" placeholder="Tempat lahir" />
                                            <x-ui.input type="date" name="{{ $prefix }}_birth_date" :value="old($prefix.'_birth_date', $guardian?->birth_date?->format('Y-m-d'))" />
                                        </div>
                                    </x-ui.field>
                                    <x-ui.field label="Pendidikan" :error="$errors->first($prefix.'_education')">
                                        <x-ui.input name="{{ $prefix }}_education" :value="old($prefix.'_education', $guardian?->education)" />
                                    </x-ui.field>
                                    <x-ui.field label="Pekerjaan" :error="$errors->first($prefix.'_job')">
                                        <x-ui.input name="{{ $prefix }}_job" :value="old($prefix.'_job', $guardian?->job)" />
                                    </x-ui.field>
                                    <x-ui.field label="Penghasilan" :error="$errors->first($prefix.'_income')">
                                        <x-ui.input name="{{ $prefix }}_income" :value="old($prefix.'_income', $guardian?->income)" />
                                    </x-ui.field>
                                    <x-ui.field label="Nomor HP" :error="$errors->first($prefix.'_phone')">
                                        <x-ui.input name="{{ $prefix }}_phone" :value="old($prefix.'_phone', $guardian?->phone)" />
                                    </x-ui.field>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Bantuan Sosial & Alamat Orang Tua">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="No. KKS" :error="$errors->first('social_kks')">
                            <x-ui.input name="social_kks" :value="old('social_kks', $student->social_kks)" />
                        </x-ui.field>
                        <x-ui.field label="No. PKH" :error="$errors->first('social_pkh')">
                            <x-ui.input name="social_pkh" :value="old('social_pkh', $student->social_pkh)" />
                        </x-ui.field>
                        <x-ui.field label="No. KIP" :error="$errors->first('social_kip')">
                            <x-ui.input name="social_kip" :value="old('social_kip', $student->social_kip)" />
                        </x-ui.field>
                        <x-ui.field label="Status Rumah" :error="$errors->first('parent_ownership')">
                            <x-ui.input name="parent_ownership" :value="old('parent_ownership', $student->parent_ownership)" />
                        </x-ui.field>
                        <x-ui.field label="Alamat Orang Tua" :error="$errors->first('parent_address')">
                            <x-ui.input name="parent_address" :value="old('parent_address', $student->parent_address)" />
                        </x-ui.field>
                        <x-ui.field label="Provinsi OT" :error="$errors->first('parent_province')">
                            <x-ui.input name="parent_province" :value="old('parent_province', $student->parent_province)" />
                        </x-ui.field>
                        <x-ui.field label="Kota OT" :error="$errors->first('parent_city')">
                            <x-ui.input name="parent_city" :value="old('parent_city', $student->parent_city)" />
                        </x-ui.field>
                        <x-ui.field label="Kecamatan OT" :error="$errors->first('parent_district')">
                            <x-ui.input name="parent_district" :value="old('parent_district', $student->parent_district)" />
                        </x-ui.field>
                        <x-ui.field label="Kelurahan OT" :error="$errors->first('parent_village')">
                            <x-ui.input name="parent_village" :value="old('parent_village', $student->parent_village)" />
                        </x-ui.field>
                        <x-ui.field label="RT / RW OT">
                            <div class="grid grid-cols-2 gap-2">
                                <x-ui.input name="parent_rt" :value="old('parent_rt', $student->parent_rt)" placeholder="RT" />
                                <x-ui.input name="parent_rw" :value="old('parent_rw', $student->parent_rw)" placeholder="RW" />
                            </div>
                        </x-ui.field>
                        <x-ui.field label="Kode Pos OT" :error="$errors->first('parent_postal_code')">
                            <x-ui.input name="parent_postal_code" :value="old('parent_postal_code', $student->parent_postal_code)" />
                        </x-ui.field>
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Kesehatan & Kebutuhan Khusus">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach (['imm_hepb' => 'Hepatitis B', 'imm_polio' => 'Polio', 'imm_bcg' => 'BCG', 'imm_campak' => 'Campak', 'imm_dpt' => 'DPT-HB-HiB', 'imm_covid' => 'COVID'] as $key => $label)
                            <label class="flex items-center gap-2 text-sm text-ink">
                                <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, (bool) $student->{$key})) class="size-4 border-rule-strong text-primary focus:ring-primary">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mb-2 mt-5 text-xs font-bold uppercase tracking-wide text-ink-soft">Kebutuhan Khusus</p>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        @foreach (['dis_deaf' => 'Tuna Rungu', 'dis_blind' => 'Tuna Netra', 'dis_disabled' => 'Tuna Daksa', 'dis_intellectual' => 'Tuna Grahita', 'dis_behavioral' => 'Tuna Laras', 'dis_slow_learner' => 'Lamban Belajar', 'dis_communication' => 'Gangguan Komunikasi', 'dis_gifted' => 'Bakat Luar Biasa'] as $key => $label)
                            <label class="flex items-center gap-2 text-sm text-ink">
                                <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, (bool) $student->{$key})) class="size-4 border-rule-strong text-primary focus:ring-primary">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Dokumen" description="Tautan Google Drive: KK, akta, ijazah, foto.">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.field label="Link Kartu Keluarga" :error="$errors->first('scanned_kk')">
                            <x-ui.input name="scanned_kk" :value="old('scanned_kk', $docs['kk'] ?? '')" placeholder="https://drive.google.com/…" />
                        </x-ui.field>
                        <x-ui.field label="Link KK Wali" :error="$errors->first('scanned_kk_wali')">
                            <x-ui.input name="scanned_kk_wali" :value="old('scanned_kk_wali', $docs['kk_wali'] ?? '')" placeholder="https://drive.google.com/…" />
                        </x-ui.field>
                        <x-ui.field label="Link Akta Kelahiran" :error="$errors->first('scanned_akta')">
                            <x-ui.input name="scanned_akta" :value="old('scanned_akta', $docs['akta'] ?? '')" placeholder="https://drive.google.com/…" />
                        </x-ui.field>
                        <x-ui.field label="Link Ijazah / SKL" :error="$errors->first('scanned_ijazah')">
                            <x-ui.input name="scanned_ijazah" :value="old('scanned_ijazah', $docs['ijazah'] ?? '')" placeholder="https://drive.google.com/…" />
                        </x-ui.field>
                        <x-ui.field label="Link Foto Siswa" :error="$errors->first('scanned_photo')">
                            <x-ui.input name="scanned_photo" :value="old('scanned_photo', $docs['photo'] ?? '')" placeholder="https://drive.google.com/…" />
                        </x-ui.field>
                    </div>
                </x-ui.form-section>
            @endif

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('siswa.index') }}">Kembali ke Data Siswa</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">
                    {{ $editing ? 'Sematkan & Simpan Perubahan' : 'Sematkan & Simpan' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
