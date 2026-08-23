<x-layouts.page
    :title="$editing ? 'Ubah Kelas' : 'Tambah Kelas'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="kelas.create">

    <div class="mx-auto max-w-xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                {{ $editing ? 'Ubah Kelas' : 'Tambah Kelas' }}
            </h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Nama kelas unik (mis. VII-A) — dijadikan rujukan penempatan siswa dan penugasan guru.
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
            action="{{ $editing ? route('kelas.update', $classGroup) : route('kelas.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Data Kelas">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Nama Kelas" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $editing ? $classGroup->name : '')" placeholder="VII-A" />
                    </x-ui.field>
                    <x-ui.field label="Tingkat" required :error="$errors->first('grade_level')">
                        <x-ui.select name="grade_level" :options="['VII' => 'VII', 'VIII' => 'VIII', 'IX' => 'IX']" :selected="old('grade_level', $editing ? $classGroup->grade_level : null)" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="mt-6 flex items-center justify-between border-t border-rule-strong/60 pt-5">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('kelas.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">
                    {{ $editing ? 'Sematkan & Simpan Perubahan' : 'Sematkan & Simpan' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
