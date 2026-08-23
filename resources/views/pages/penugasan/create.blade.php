<x-layouts.page
    :title="$editing ? 'Ubah Penugasan' : 'Tambah Penugasan'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="penugasan.create">

    <div class="mx-auto max-w-2xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                {{ $editing ? 'Ubah Penugasan' : 'Tambah Penugasan' }}
            </h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Satu guru mengampu satu mapel di satu kelas per tahun ajaran — kombinasi tidak boleh duplikat.
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
            action="{{ $editing ? route('penugasan.update', $assignment) : route('penugasan.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Penugasan Mengajar">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Guru" required :error="$errors->first('user_id')">
                        <x-ui.select name="user_id" :options="$teachers" :selected="old('user_id', $editing ? $assignment->user_id : null)" />
                    </x-ui.field>
                    <x-ui.field label="Kelas" required :error="$errors->first('class_group_id')">
                        <x-ui.select name="class_group_id" :options="$classes->pluck('name', 'id')" :selected="old('class_group_id', $editing ? $assignment->class_group_id : null)" />
                    </x-ui.field>
                    <x-ui.field label="Mata Pelajaran" required :error="$errors->first('subject_id')">
                        <x-ui.select name="subject_id" :options="$subjects->pluck('name', 'id')" :selected="old('subject_id', $editing ? $assignment->subject_id : null)" />
                    </x-ui.field>
                    <x-ui.field label="Tahun Ajaran" required :error="$errors->first('academic_year_id')">
                        <x-ui.select name="academic_year_id" :options="[$tahun->id => $tahun->name]" :selected="old('academic_year_id', $editing ? $assignment->academic_year_id : $tahun->id)" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="mt-6 flex items-center justify-between border-t border-rule-strong/60 pt-5">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('penugasan.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">
                    {{ $editing ? 'Sematkan & Simpan Perubahan' : 'Sematkan & Simpan' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
