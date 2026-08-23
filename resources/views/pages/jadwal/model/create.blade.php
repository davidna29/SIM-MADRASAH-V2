<x-layouts.page
    :title="$editing ? 'Ubah Model Jadwal' : 'Tambah Model Jadwal'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="jadwal.model.create">

    <div class="mx-auto max-w-xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                {{ $editing ? 'Ubah Model Jadwal' : 'Tambah Model Jadwal' }}
            </h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Pilih tingkatan yang dicakup model ini. Tingkatan tidak boleh tumpang tindih dengan model aktif lain.
            </p>
        </div>

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <form method="POST"
            action="{{ $editing ? route('jadwal.model.update', $model) : route('jadwal.model.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Identitas Model">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Nama Model" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $editing ? $model->name : '')" placeholder="Mis. Kurikulum Kelas V–VI" />
                    </x-ui.field>
                    <x-ui.field label="Tahun Ajaran">
                        <x-ui.select name="academic_year_id" :options="[$tahun->id => $tahun->name]" :selected="$tahun->id" />
                    </x-ui.field>
                    <x-ui.field label="Jam Mulai Hari" required :error="$errors->first('start_time')">
                        <x-ui.input name="start_time" type="time" :value="old('start_time', $editing ? $model->start_time->format('H:i') : '07:00')" />
                    </x-ui.field>
                    <x-ui.field label="Maks Jam per Hari" required :error="$errors->first('max_hours_per_day')">
                        <x-ui.input name="max_hours_per_day" type="number" min="1" max="12" :value="old('max_hours_per_day', $editing ? $model->max_hours_per_day : 6)" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Tingkatan yang Dicakup" description="Centang tingkat yang memakai model ini. Tidak boleh tumpang tindih dengan model aktif lain.">
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @php
                        $selectedLevels = (array) old('grade_levels', $editing ? $model->gradeLevels() : []);
                    @endphp
                    @foreach ($gradeOptions as $value => $label)
                        <label class="flex cursor-pointer items-center gap-2 rounded-[var(--radius-control)] border border-rule/70 px-3 py-2.5 transition hover:bg-paper-deep">
                            <input type="checkbox" name="grade_levels[]" value="{{ $value }}"
                                @checked(in_array($value, $selectedLevels, true))
                                class="size-4 rounded border-rule-strong text-primary focus:ring-primary">
                            <span class="text-[13px] font-semibold text-ink">Tingkat {{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('grade_levels')
                    <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
                @enderror
            </x-ui.form-section>

            @if (! $editing)
                <x-ui.form-section title="Slot Template" description="Jam ke- KBM otomatis dibuat dari jam mulai + durasi.">
                    <x-ui.field label="Durasi per Jam (menit)" required :error="$errors->first('slot_duration')">
                        <x-ui.input name="slot_duration" type="number" min="15" max="120" step="5" :value="old('slot_duration', 45)" />
                    </x-ui.field>
                </x-ui.form-section>
            @endif

            <div class="mt-6 flex items-center justify-between border-t border-rule-strong/60 pt-5">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('jadwal.model.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">
                    {{ $editing ? 'Sematkan & Simpan Perubahan' : 'Sematkan & Simpan' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
