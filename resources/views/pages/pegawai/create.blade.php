<x-layouts.page
    :title="$editing ? 'Ubah Guru/Pegawai' : 'Tambah Guru/Pegawai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pegawai.create">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                    {{ $editing ? 'Ubah Guru/Pegawai' : 'Tambah Guru/Pegawai' }}
                </h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Lembar data guru dan tenaga kependidikan — data inti dipakai lintas modul tanpa entri ulang.
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
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <form method="POST"
            action="{{ $editing ? route('pegawai.update', $employee) : route('pegawai.store') }}"
            class="mt-6 space-y-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Data Pribadi" description="Identitas sesuai akta/KK. NIK unik — digunakan lintas modul.">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Nama Lengkap" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $editing ? $employee->person->name : '')" placeholder="Nama sesuai akta" />
                    </x-ui.field>
                    <x-ui.field label="NIK" required hint="Nomor Induk Kependudukan — 16 digit." :error="$errors->first('nik')">
                        <x-ui.input name="nik" :value="old('nik', $editing ? $employee->person->nik : '')" placeholder="3508120503850001" maxlength="16" />
                    </x-ui.field>
                    <x-ui.field label="Tempat, Tanggal Lahir">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <x-ui.input name="birth_place" :value="old('birth_place', $editing ? $employee->person->birth_place : '')" placeholder="Tempat lahir" />
                            <x-ui.input name="birth_date" type="date" :value="old('birth_date', $editing && $employee->person->birth_date ? $employee->person->birth_date->format('Y-m-d') : '')" />
                        </div>
                    </x-ui.field>
                    <x-ui.field label="Jenis Kelamin" required :error="$errors->first('gender')">
                        <x-ui.select name="gender" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :selected="old('gender', $editing ? $employee->person->gender : null)" />
                    </x-ui.field>
                    <x-ui.field label="Agama" :error="$errors->first('religion')">
                        <x-ui.select name="religion" :options="['Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu']" :selected="old('religion', $editing ? $employee->person->religion : null)" />
                    </x-ui.field>
                    <x-ui.field label="Nomor HP" hint="Untuk kontak resmi madrasah." :error="$errors->first('phone')">
                        <x-ui.input name="phone" prefix="+62" :value="old('phone', $editing ? ltrim((string) $employee->person->phone, '0') : '')" />
                    </x-ui.field>
                    <x-ui.field label="Email" :error="$errors->first('email')">
                        <x-ui.input name="email" type="email" :value="old('email', $editing ? $employee->person->email : '')" placeholder="nama@madrasah.sch.id" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Foto Pegawai" hint="JPG/PNG/WebP, maks 2 MB. Tampil di halaman Guru dan Struktur bila publikasi website aktif." :error="$errors->first('photo')">
                            <div class="flex flex-wrap items-center gap-4">
                                @if ($editing && $employee->photoUrl())
                                    <img src="{{ $employee->photoUrl() }}" alt="Foto {{ $employee->person->name }}" class="size-20 rounded-control object-cover ring-1 ring-inset ring-rule-strong">
                                @else
                                    <span class="flex size-20 shrink-0 items-center justify-center rounded-control bg-primary-soft text-xl font-extrabold text-primary-strong">
                                        {{ mb_substr($editing ? $employee->person->name : '?', 0, 1) }}
                                    </span>
                                @endif
                                <div class="min-w-0 flex-1 space-y-2">
                                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition file:mr-3 file:rounded-md file:border-0 file:bg-primary-soft file:px-3 file:py-1 file:text-xs file:font-semibold file:text-primary-strong hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary">
                                    @if ($editing && $employee->photo_path)
                                        <label class="flex items-center gap-2 text-xs font-medium text-ink-soft">
                                            <input type="checkbox" name="remove_photo" value="1" class="size-4 rounded border-rule-strong text-primary focus:ring-primary">
                                            Hapus foto saat menyimpan
                                        </label>
                                    @endif
                                </div>
                            </div>
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Data Kepegawaian" description="Jabatan, unit, dan status kepegawaian — dasar otorisasi dan penugasan.">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="NIP" hint="Kosongkan jika bukan PNS/PPPK." :error="$errors->first('nip')">
                        <x-ui.input name="nip" :value="old('nip', $editing ? $employee->nip : '')" placeholder="198503122010011003" />
                    </x-ui.field>
                    <x-ui.field label="Status Pegawai" required :error="$errors->first('employee_status')">
                        <x-ui.select name="employee_status" :options="['pns' => 'PNS', 'pppk' => 'PPPK', 'honor' => 'Honor']" :selected="old('employee_status', $editing ? $employee->employee_status : null)" />
                    </x-ui.field>
                    <x-ui.field label="Jabatan" required :error="$errors->first('position_id')">
                        <x-ui.select name="position_id" :options="$positions->pluck('name', 'id')" :selected="old('position_id', $editing ? $employee->position_id : null)" />
                    </x-ui.field>
                    <x-ui.field label="Unit Kerja" required :error="$errors->first('organizational_unit_id')">
                        <x-ui.select name="organizational_unit_id" :options="$units->pluck('name', 'id')" :selected="old('organizational_unit_id', $editing ? $employee->organizational_unit_id : null)" />
                    </x-ui.field>
                    <x-ui.field label="TMT" hint="Terhitung mulai tanggal." :error="$errors->first('tmt')">
                        <x-ui.input name="tmt" type="date" :value="old('tmt', $editing && $employee->tmt ? $employee->tmt->format('Y-m-d') : '')" />
                    </x-ui.field>
                    <x-ui.field label="Status Keaktifan" :error="$errors->first('status')">
                        <x-ui.select name="status" :options="['aktif' => 'Aktif', 'cuti' => 'Cuti', 'nonaktif' => 'Nonaktif']" :selected="old('status', $editing ? $employee->status : null)" />
                    </x-ui.field>
                    <x-ui.field label="Publikasi Website" :error="$errors->first('show_on_website')">
                        <label class="flex min-h-11 items-start gap-3 rounded-control bg-paper/70 p-3 ring-1 ring-inset ring-rule-strong">
                            <input type="checkbox" name="show_on_website" value="1" @checked(old('show_on_website', $editing ? $employee->show_on_website : false)) class="mt-1 size-4 shrink-0 rounded border-rule-strong text-primary focus:ring-primary">
                            <span class="text-xs leading-relaxed text-ink-soft">Izinkan nama dan jabatan tampil di website publik. NIP, NIK, kontak, dan data pribadi tidak akan ditampilkan.</span>
                        </label>
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('pegawai.index') }}">Kembali ke Data Guru & Pegawai</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">
                    {{ $editing ? 'Sematkan & Simpan Perubahan' : 'Sematkan & Simpan' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
