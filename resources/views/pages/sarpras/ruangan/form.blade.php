@php
    $employeeOptions = $employees->mapWithKeys(fn ($e) => [$e->id => $e->person->name . ($e->nip ? ' (' . $e->nip . ')' : '')]);
@endphp

<x-layouts.page
    :title="$editing ? 'Ubah Ruangan' : 'Tambah Ruangan'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ruangan.edit">

    <div class="mx-auto max-w-3xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $editing ? 'Ubah Ruangan' : 'Tambah Ruangan' }}</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Kode ruangan dibuat otomatis (R-001, R-002, …).
            </p>
        </div>

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <form method="POST"
            action="{{ $editing ? route('ruangan.update', $room) : route('ruangan.store') }}"
            class="mt-6">
            @csrf
            @if ($editing)
                @method('PUT')
            @endif

            <x-ui.form-section title="Identitas Ruangan">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Nama Ruangan" required :error="$errors->first('name')">
                        <x-ui.input name="name" :value="old('name', $editing ? $room->name : '')" placeholder="Mis. Ruang Guru, Lab IPA" />
                    </x-ui.field>
                    <x-ui.field label="Jenis" required :error="$errors->first('type')">
                        <x-ui.select name="type" :options="$types" :selected="old('type', $editing ? $room->type : 'ruangan')" />
                    </x-ui.field>
                    <x-ui.field label="Gedung" :error="$errors->first('building')">
                        <x-ui.input name="building" :value="old('building', $editing ? $room->building : '')" placeholder="Mis. Gedung Utama" />
                    </x-ui.field>
                    <x-ui.field label="Lantai" :error="$errors->first('floor')">
                        <x-ui.input name="floor" :value="old('floor', $editing ? $room->floor : '')" placeholder="Mis. Lantai 1" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Kapasitas & Kondisi">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <x-ui.field label="Kapasitas (orang)" :error="$errors->first('capacity')">
                        <x-ui.input type="number" name="capacity" min="0" max="9999" :value="old('capacity', $editing ? $room->capacity : 0)" />
                    </x-ui.field>
                    <x-ui.field label="Penanggung Jawab" :error="$errors->first('employee_id')">
                        <x-ui.select name="employee_id" :options="$employeeOptions->prepend('— Pilih pegawai —', '')" :selected="old('employee_id', $editing ? $room->employee_id : null)" />
                    </x-ui.field>
                    <x-ui.field label="Kondisi" required :error="$errors->first('condition')">
                        <x-ui.select name="condition" :options="$conditions" :selected="old('condition', $editing ? $room->condition : 'baik')" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Deskripsi">
                <x-ui.field label="Keterangan" :error="$errors->first('description')">
                    <textarea name="description" rows="3" placeholder="Keterangan tambahan tentang ruangan ini…"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">{{ old('description', $editing ? $room->description : '') }}</textarea>
                </x-ui.field>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('ruangan.index', ['type' => $editing ? $room->type : 'ruangan']) }}">Kembali</x-ui.button>
                <x-ui.button type="submit" variant="primary" icon="check">{{ $editing ? 'Simpan Perubahan' : 'Simpan Ruangan' }}</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.page>
