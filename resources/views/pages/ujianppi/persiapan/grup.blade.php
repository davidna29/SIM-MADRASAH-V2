<x-layouts.page
    :title="'Grup Setoran & Pembimbing — '.$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => $periode->judul, 'href' => route('ujianppi.periode.show', $periode)],
        ['label' => 'Grup & Pembimbing'],
    ]"
    active-route="ujianppi.periode.index">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Grup Setoran & Pembimbing</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Grup bimbingan hafalan (Fase 1) terpisah total dari pembagian ruang ujian.
                    Ideal 7–15 siswa per grup dengan <b>1 guru pembimbing</b>.
                </p>
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
                    Grup terkunci — periode sudah <b>{{ $periode->statusLabel() }}</b>.
                </x-ui.alert>
            </div>
        @endif

        @if ($editable)
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                <form method="POST" action="{{ route('ujianppi.persiapan.grup.store', $periode) }}" class="grid gap-3 sm:grid-cols-3 sm:items-end">
                    @csrf
                    <div>
                        <label class="block pb-1.5 text-xs font-bold text-ink">Nama Grup</label>
                        <x-ui.input name="nama" placeholder="mis. Grup A" :value="old('nama')" />
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block pb-1.5 text-xs font-bold text-ink">Guru Pembimbing</label>
                        <x-ui.select name="pembimbing_employee_id" :options="$employees->pluck('person.name', 'id')" placeholder="Pilih guru…" />
                    </div>
                    <div>
                        <x-ui.button type="submit" variant="primary" icon="plus">Tambah Grup</x-ui.button>
                    </div>
                </form>
            </div>
        @endif

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            @forelse ($groups as $group)
                @php
                    $memberCount = $group->participants->count();
                @endphp
                <x-ui.sheet :title="$group->nama" :subtitle="'Pembimbing: '.($group->pembimbing?->person?->name ?? '—').' · '.$memberCount.' siswa'">
                    @if ($editable)
                        <form method="POST" action="{{ route('ujianppi.persiapan.grup.update', [$periode, $group]) }}" class="grid gap-3 sm:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block pb-1.5 text-xs font-bold text-ink">Nama Grup</label>
                                <x-ui.input name="nama" :value="$group->nama" />
                            </div>
                            <div>
                                <label class="block pb-1.5 text-xs font-bold text-ink">Pembimbing</label>
                                <x-ui.select name="pembimbing_employee_id" :options="$employees->pluck('person.name', 'id')" :selected="$group->pembimbing_employee_id" />
                            </div>
                            <div class="sm:col-span-2 flex flex-wrap items-center gap-2">
                                <x-ui.button type="submit" variant="primary" size="sm" icon="check">Simpan</x-ui.button>
                                <form method="POST" action="{{ route('ujianppi.persiapan.grup.destroy', [$periode, $group]) }}"
                                    onsubmit="return confirm('Hapus grup {{ $group->nama }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="ghost" size="sm" icon="trash">Hapus</x-ui.button>
                                </form>
                            </div>
                        </form>
                    @else
                        <div class="flex flex-wrap gap-1.5">
                            @forelse ($group->participants as $p)
                                <x-ui.badge variant="neutral" :dot="false">{{ $p->student?->name }}</x-ui.badge>
                            @empty
                                <span class="text-sm text-ink-faint">Belum ada anggota.</span>
                            @endforelse
                        </div>
                    @endif
                </x-ui.sheet>
            @empty
                <div class="md:col-span-2 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center text-ink-faint">
                    Belum ada grup setoran.
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.page>