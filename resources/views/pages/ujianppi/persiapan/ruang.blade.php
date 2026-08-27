<x-layouts.page
    :title="'Ruang & Penguji — '.$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => $periode->judul, 'href' => route('ujianppi.periode.show', $periode)],
        ['label' => 'Ruang & Penguji'],
    ]"
    active-route="ujianppi.periode.index">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Ruang & Penguji</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Satu ruang = 3 penguji (guru). <b>1 guru hanya boleh di 1 ruang per periode</b> — guru yang sudah
                    ter-assign di ruang lain disembunyikan dari pilihan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button variant="secondary" size="sm" icon="arrow-left" href="{{ route('ujianppi.periode.show', $periode) }}">Kembali</x-ui.button>
                @if ($editable)
                    <form method="POST" action="{{ route('ujianppi.persiapan.ruang.copy', $periode) }}" class="inline"
                        onsubmit="return confirm('Salin ruang & penguji dari periode sebelumnya? Data saat ini tidak ditimpa.');">
                        @csrf
                        <x-ui.button type="submit" variant="secondary" size="sm" icon="document-duplicate">Salin dari Periode Sebelumnya</x-ui.button>
                    </form>
                @endif
            </div>
        </div>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        @if (! $editable)
            <div class="mt-6">
                <x-ui.alert variant="warning" dismissible>
                    Ruang & penguji terkunci — periode sudah <b>{{ $periode->statusLabel() }}</b>.
                </x-ui.alert>
            </div>
        @endif

        @if ($editable)
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                <form method="POST" action="{{ route('ujianppi.persiapan.ruang.store', $periode) }}" class="flex flex-wrap items-end gap-2">
                    @csrf
                    <div class="min-w-[240px] flex-1">
                        <label class="block pb-1.5 text-xs font-bold text-ink">Nama Ruang</label>
                        <x-ui.input name="nama" placeholder="mis. Ruang 1" :value="old('nama')" />
                    </div>
                    <x-ui.button type="submit" variant="primary" icon="plus">Tambah Ruang</x-ui.button>
                </form>
            </div>
        @endif

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            @forelse ($rooms as $room)
                @php
                    $roomOptions = $employees->reject(fn ($e) => $assignedEmployeeIds->contains($e->id) && ! $room->examiners->contains('employee_id', $e->id));
                    $own = $room->examiners->keyBy('urutan');
                @endphp
                <x-ui.sheet :title="$room->nama" :subtitle="$room->participants->count().' peserta'" class="min-w-0">
                    <div class="space-y-3">
                        @foreach ([1, 2, 3] as $urutan)
                            <div class="rounded-[var(--radius-control)] bg-paper px-4 py-3 ring-1 ring-inset ring-rule/60">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-ink-soft">Penguji {{ $urutan }}</span>
                                    @if ($own[$urutan] ?? null)
                                        <x-ui.badge variant="success" :dot="false">{{ $own[$urutan]->employee?->person?->name ?? 'Guru #'.$own[$urutan]->employee_id }}</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" :dot="false">Belum diisi</x-ui.badge>
                                    @endif
                                </div>
                                @if ($editable)
                                    <form method="POST" action="{{ route('ujianppi.persiapan.ruang.update', [$periode, $room]) }}" class="mt-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="nama" value="{{ $room->nama }}">
                                        <input type="hidden" name="penguji_1" value="{{ $own[1]->employee_id ?? '' }}">
                                        <input type="hidden" name="penguji_2" value="{{ $own[2]->employee_id ?? '' }}">
                                        <input type="hidden" name="penguji_3" value="{{ $own[3]->employee_id ?? '' }}">
                                        <x-ui.select name="penguji_{{$urutan}}" :full="false" class="w-full"
                                            :options="$roomOptions->pluck('person.name', 'id')" :selected="$own[$urutan]->employee_id ?? null"
                                            placeholder="Pilih guru…">
                                        </x-ui.select>
                                        <div class="mt-2 flex justify-end">
                                            <x-ui.button type="submit" variant="primary" size="sm" icon="check">Simpan</x-ui.button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if ($editable)
                        <form method="POST" action="{{ route('ujianppi.persiapan.ruang.destroy', [$periode, $room]) }}" class="mt-4"
                            onsubmit="return confirm('Hapus ruang {{ $room->nama }}?');">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="ghost" size="sm" icon="trash">Hapus Ruang</x-ui.button>
                        </form>
                    @endif
                </x-ui.sheet>
            @empty
                <div class="md:col-span-2 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center text-ink-faint">
                    Belum ada ruang ujian.
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.page>