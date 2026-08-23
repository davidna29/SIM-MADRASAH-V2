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

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('siswa.index') }}">Kembali ke Data Siswa</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">
                    {{ $editing ? 'Sematkan & Simpan Perubahan' : 'Sematkan & Simpan' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
