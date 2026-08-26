<x-layouts.page
    :title="'Pengaturan Sistem'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pengaturan.index">

    <div class="mx-auto max-w-4xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pengaturan Sistem</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Kelola identitas dan pengaturan lembaga madrasah. Perubahan berlaku di seluruh sistem.
            </p>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    {{ $errors->first() }}
                </x-ui.alert>
            </div>
        @endif

        <form method="POST" action="{{ route('pengaturan.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Data Utama --}}
            <x-ui.sheet title="Data Utama" subtitle="Identitas dasar madrasah" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-ui.field label="Nama Madrasah" :error="$errors->first('madrasah_name')" required>
                        <x-ui.input name="madrasah_name" :value="old('madrasah_name', $settings->get('madrasah_name', ''))" placeholder="MTs Al-Ikhlas Mulia" required />
                    </x-ui.field>

                    <x-ui.field label="NSM (Nomor Statistik Madrasah)" :error="$errors->first('madrasah_nsm')">
                        <x-ui.input name="madrasah_nsm" :value="old('madrasah_nsm', $settings->get('madrasah_nsm', ''))" placeholder="11111111" />
                    </x-ui.field>

                    <x-ui.field label="NPSN" :error="$errors->first('madrasah_npsn')">
                        <x-ui.input name="madrasah_npsn" :value="old('madrasah_npsn', $settings->get('madrasah_npsn', ''))" placeholder="12345678" />
                    </x-ui.field>

                    <x-ui.field label="Jenjang" :error="$errors->first('madrasah_jenjang')" required>
                        <x-ui.select name="madrasah_jenjang" :full="true"
                            :options="['RA' => 'RA (Raudhatul Athfal)', 'MI' => 'MI (Madrasah Ibtidaiyah)', 'MTs' => 'MTs (Madrasah Tsanawiyah)', 'MA' => 'MA (Madrasah Aliyah)']"
                            :selected="old('madrasah_jenjang', $settings->get('madrasah_jenjang', 'MTs'))" />
                    </x-ui.field>

                    <x-ui.field label="Status" :error="$errors->first('madrasah_status')" required>
                        <x-ui.select name="madrasah_status" :full="true"
                            :options="['negeri' => 'Negeri', 'swasta' => 'Swasta']"
                            :selected="old('madrasah_status', $settings->get('madrasah_status', 'swasta'))" />
                    </x-ui.field>

                    <x-ui.field label="Tahun Berdiri" :error="$errors->first('madrasah_tahun_berdiri')">
                        <x-ui.input name="madrasah_tahun_berdiri" :value="old('madrasah_tahun_berdiri', $settings->get('madrasah_tahun_berdiri', ''))" placeholder="2000" maxlength="4" />
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            {{-- Alamat & Lokasi --}}
            <x-ui.sheet title="Alamat & Lokasi" subtitle="Alamat lengkap dan koordinat madrasah" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-ui.field label="Jalan" :error="$errors->first('madrasah_jalan')">
                            <x-ui.input name="madrasah_jalan" :value="old('madrasah_jalan', $settings->get('madrasah_jalan', ''))" placeholder="Jl. Pendidikan No. 123" />
                        </x-ui.field>
                    </div>

                    <x-ui.field label="Desa / Kelurahan" :error="$errors->first('madrasah_desa')">
                        <x-ui.input name="madrasah_desa" :value="old('madrasah_desa', $settings->get('madrasah_desa', ''))" placeholder="Kel. Ilmu" />
                    </x-ui.field>

                    <x-ui.field label="Kecamatan" :error="$errors->first('madrasah_kecamatan')">
                        <x-ui.input name="madrasah_kecamatan" :value="old('madrasah_kecamatan', $settings->get('madrasah_kecamatan', ''))" placeholder="Kec. Semangat" />
                    </x-ui.field>

                    <x-ui.field label="Kabupaten / Kota" :error="$errors->first('madrasah_kabupaten')">
                        <x-ui.input name="madrasah_kabupaten" :value="old('madrasah_kabupaten', $settings->get('madrasah_kabupaten', ''))" placeholder="Kota Cerdas" />
                    </x-ui.field>

                    <x-ui.field label="Provinsi" :error="$errors->first('madrasah_provinsi')">
                        <x-ui.input name="madrasah_provinsi" :value="old('madrasah_provinsi', $settings->get('madrasah_provinsi', ''))" placeholder="Jawa Barat" />
                    </x-ui.field>

                    <x-ui.field label="Kode Pos" :error="$errors->first('madrasah_kode_pos')">
                        <x-ui.input name="madrasah_kode_pos" :value="old('madrasah_kode_pos', $settings->get('madrasah_kode_pos', ''))" placeholder="40123" maxlength="5" />
                    </x-ui.field>

                    <x-ui.field label="Latitude (Lintang)" :error="$errors->first('madrasah_latitude')">
                        <x-ui.input name="madrasah_latitude" :value="old('madrasah_latitude', $settings->get('madrasah_latitude', ''))" placeholder="-6.9175" />
                    </x-ui.field>

                    <x-ui.field label="Longitude (Bujur)" :error="$errors->first('madrasah_longitude')">
                        <x-ui.input name="madrasah_longitude" :value="old('madrasah_longitude', $settings->get('madrasah_longitude', ''))" placeholder="107.6191" />
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            {{-- Kontak --}}
            <x-ui.sheet title="Kontak" subtitle="Nomor telepon, email, dan website" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-ui.field label="Nomor Telepon" :error="$errors->first('madrasah_phone')">
                        <x-ui.input name="madrasah_phone" :value="old('madrasah_phone', $settings->get('madrasah_phone', ''))" placeholder="(022) 1234567" />
                    </x-ui.field>

                    <x-ui.field label="Email" :error="$errors->first('madrasah_email')">
                        <x-ui.input name="madrasah_email" type="email" :value="old('madrasah_email', $settings->get('madrasah_email', ''))" placeholder="info@alikhlas.sch.id" />
                    </x-ui.field>

                    <div class="sm:col-span-2">
                        <x-ui.field label="Website" :error="$errors->first('madrasah_website')">
                            <x-ui.input name="madrasah_website" type="url" :value="old('madrasah_website', $settings->get('madrasah_website', ''))" placeholder="https://alikhlas.sch.id" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- Legalitas --}}
            <x-ui.sheet title="Legalitas" subtitle="SK pendirian dan izin operasional" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-ui.field label="No. SK Pendirian" :error="$errors->first('madrasah_sk_pendirian')">
                        <x-ui.input name="madrasah_sk_pendirian" :value="old('madrasah_sk_pendirian', $settings->get('madrasah_sk_pendirian', ''))" placeholder="001/SK/2000" />
                    </x-ui.field>

                    <x-ui.field label="Tanggal SK Pendirian" :error="$errors->first('madrasah_tgl_sk_pendirian')">
                        <input type="date" name="madrasah_tgl_sk_pendirian"
                            value="{{ old('madrasah_tgl_sk_pendirian', $settings->get('madrasah_tgl_sk_pendirian', '')) }}"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>

                    <div class="sm:col-span-2">
                        <x-ui.field label="SK Izin Operasional" :error="$errors->first('madrasah_sk_operasional')">
                            <x-ui.input name="madrasah_sk_operasional" :value="old('madrasah_sk_operasional', $settings->get('madrasah_sk_operasional', ''))" placeholder="002/SK/2000" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- Akreditasi & Naungan --}}
            <x-ui.sheet title="Akreditasi & Naungan" subtitle="Status akreditasi dan lembaga naungan" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <x-ui.field label="Status Akreditasi" :error="$errors->first('madrasah_akreditasi')" required>
                        <x-ui.select name="madrasah_akreditasi" :full="true"
                            :options="['terakreditasi' => 'Terakreditasi', 'belum' => 'Belum Terakreditasi']"
                            :selected="old('madrasah_akreditasi', $settings->get('madrasah_akreditasi', 'belum'))" />
                    </x-ui.field>

                    <x-ui.field label="Nilai Akreditasi" :error="$errors->first('madrasah_nilai_akreditasi')">
                        <x-ui.select name="madrasah_nilai_akreditasi" :full="true"
                            :options="['' => '—', 'A' => 'A', 'B' => 'B', 'C' => 'C']"
                            :selected="old('madrasah_nilai_akreditasi', $settings->get('madrasah_nilai_akreditasi', ''))" />
                    </x-ui.field>

                    <x-ui.field label="Naungan" :error="$errors->first('madrasah_naungan')">
                        <x-ui.input name="madrasah_naungan" :value="old('madrasah_naungan', $settings->get('madrasah_naungan', ''))" placeholder="Kementerian Agama" />
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            {{-- Logo --}}
            <x-ui.sheet title="Logo Madrasah" subtitle="Logo tampil di sidebar, login, dan cetakan" pinned ruled>
                <div class="flex flex-wrap items-start gap-6">
                    <div class="flex-1 min-w-[200px]">
                        <label for="madrasah_logo" class="block pb-1.5 text-xs font-bold text-ink">Upload Logo</label>
                        <input type="file" name="madrasah_logo" id="madrasah_logo" accept="image/png,image/jpeg"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition file:mr-3 file:rounded-[var(--radius-control)] file:border-0 file:bg-primary-soft file:px-3 file:py-1 file:text-xs file:font-semibold file:text-primary hover:file:bg-primary/20 focus:outline-none focus:ring-2 focus:ring-primary">
                        <p class="mt-1 text-xs text-ink-faint">Format: JPG/PNG, maks 2MB.</p>
                    </div>

                    @if ($settings->get('madrasah_logo'))
                        <div class="flex flex-col items-center gap-2">
                            <div class="size-24 overflow-hidden rounded-[var(--radius-control)] ring-1 ring-inset ring-rule-strong">
                                <img src="{{ Storage::disk('public')->url($settings->get('madrasah_logo')) }}" alt="Logo"
                                    class="size-full object-contain">
                            </div>
                            <span class="text-xs text-ink-faint">Logo saat ini</span>
                        </div>
                    @endif
                </div>
            </x-ui.sheet>

            {{-- Submit --}}
            <div class="flex justify-end gap-3">
                <x-ui.button type="submit" variant="primary" size="lg" icon="check-circle">Simpan Pengaturan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
