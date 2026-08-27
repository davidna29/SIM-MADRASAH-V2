<x-layouts.page
    :title="$editing ? 'Ubah Unit Kerja' : 'Tambah Unit Kerja'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="unit-kerja.edit">

    <div class="mx-auto max-w-xl">
        <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Unit Kerja' : 'Tambah Unit Kerja' }}</h1>

        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>@foreach ($errors->all() as $error) {{ $error }} @endforeach</x-ui.alert></div>
        @endif

        <form method="POST" action="{{ $editing ? route('unit-kerja.update', $unit) : route('unit-kerja.store') }}" class="mt-6">
            @csrf
            @if ($editing) @method('PUT') @endif
            <x-ui.form-section title="Data Unit">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Kode" required :error="$errors->first('code')">
                        <x-ui.input name="code" :value="old('code', $editing ? $unit->code : '')" placeholder="Mis. KURIKULUM" />
                    </x-ui.field>
                    <x-ui.field label="Nama Unit" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $editing ? $unit->name : '')" placeholder="Mis. Kurikulum" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>
            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('unit-kerja.index') }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">Simpan</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>