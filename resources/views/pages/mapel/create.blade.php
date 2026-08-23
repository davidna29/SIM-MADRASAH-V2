<x-layouts.page
    :title="$editing ? 'Ubah Mata Pelajaran' : 'Tambah Mata Pelajaran'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mapel.create">

    <div class="mx-auto max-w-xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                {{ $editing ? 'Ubah Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
            </h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Kode mapel unik — dipakai sebagai acuan pada penugasan mengajar dan penilaian.
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
            action="{{ $editing ? route('mapel.update', $subject) : route('mapel.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Data Mata Pelajaran">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-[180px_1fr]">
                    <x-ui.field label="Kode" required :error="$errors->first('code')">
                        <x-ui.input name="code" :value="old('code', $editing ? $subject->code : '')" placeholder="MAT" maxlength="10" />
                    </x-ui.field>
                    <x-ui.field label="Nama Mata Pelajaran" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $editing ? $subject->name : '')" placeholder="Matematika" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="mt-6 flex items-center justify-between border-t border-rule-strong/60 pt-5">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('mapel.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">
                    {{ $editing ? 'Sematkan & Simpan Perubahan' : 'Sematkan & Simpan' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
