<x-layouts.page
    :title="$editing ? 'Ubah Jadwal' : 'Tambah Jadwal'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="jadwal.create">

    <div class="mx-auto max-w-xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                {{ $editing ? 'Ubah Jadwal' : 'Tambah Jadwal' }}
            </h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Pilih penugasan (guru–mapel–kelas), lalu tentukan hari dan jam mengajar.
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
            action="{{ $editing ? route('jadwal.update', $schedule) : route('jadwal.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Detail Jadwal">
                <div class="space-y-5">
                    <x-ui.field label="Penugasan (guru–mapel–kelas)" required :error="$errors->first('teacher_assignment_id')">
                        <x-ui.select name="teacher_assignment_id" :options="$assignments" :selected="old('teacher_assignment_id', $editing ? $schedule->teacher_assignment_id : null)" />
                    </x-ui.field>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <x-ui.field label="Hari" required :error="$errors->first('day')">
                            <x-ui.select name="day" :options="collect($days)->mapWithKeys(fn ($d) => [$d => ucfirst($d)])->all()" :selected="old('day', $editing ? $schedule->day : null)" />
                        </x-ui.field>
                        <x-ui.field label="Jam Mulai" required :error="$errors->first('start_time')">
                            <x-ui.input name="start_time" type="time" :value="old('start_time', $editing ? $schedule->start_time->format('H:i') : '07:00')" />
                        </x-ui.field>
                        <x-ui.field label="Jam Selesai" required :error="$errors->first('end_time')">
                            <x-ui.input name="end_time" type="time" :value="old('end_time', $editing ? $schedule->end_time->format('H:i') : '08:00')" />
                        </x-ui.field>
                    </div>
                    <x-ui.field label="Ruang" :error="$errors->first('room')">
                        <x-ui.input name="room" :value="old('room', $editing ? $schedule->room : '')" placeholder="Mis. Ruang 1" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="mt-6 flex items-center justify-between border-t border-rule-strong/60 pt-5">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('jadwal.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">
                    {{ $editing ? 'Sematkan & Simpan Perubahan' : 'Sematkan & Simpan' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
