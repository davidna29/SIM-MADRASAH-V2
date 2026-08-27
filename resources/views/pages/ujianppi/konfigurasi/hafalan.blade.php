<x-layouts.page
    :title="'Materi Setoran — '.$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => $periode->judul, 'href' => route('ujianppi.periode.show', $periode)],
        ['label' => 'Materi Setoran'],
    ]"
    active-route="ujianppi.periode.index">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Materi Setoran Hafalan</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Daftar surah Fase 1 (Juz 30, Yasin, Al-Waqiah). Guru pembimbing menilai per surah per siswa.
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
                    Materi setoran terkunci — periode sudah <b>{{ $periode->statusLabel() }}</b>.
                </x-ui.alert>
            </div>
        @endif

        @if ($editable)
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                <form method="POST" action="{{ route('ujianppi.konfigurasi.hafalan.store', $periode) }}" class="flex flex-wrap items-end gap-2">
                    @csrf
                    <div class="min-w-[240px] flex-1">
                        <label class="block pb-1.5 text-xs font-bold text-ink">Nama Surah / Materi</label>
                        <x-ui.input name="nama" placeholder="mis. Yaasin" :value="old('nama')" />
                    </div>
                    <x-ui.button type="submit" variant="primary" icon="plus">Tambah Materi</x-ui.button>
                </form>
            </div>
        @endif

        <div class="mt-6">
            <x-ui.sheet :title="'Daftar Materi ('.count($materi).')'" :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[480px] border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-rule-strong">
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft w-16">Urutan</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama Materi</th>
                                @if ($editable)
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule/70">
                            @forelse ($materi as $item)
                                <tr x-data="{ edit: false }" class="transition hover:bg-paper/60">
                                    <td class="px-4 py-3 tabular font-mono text-xs font-semibold text-ink-faint">{{ $item->urutan }}</td>
                                    <td class="px-4 py-3 font-medium text-ink">{{ $item->nama }}</td>
                                    @if ($editable)
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex justify-end gap-2">
                                                <x-ui.button variant="ghost" size="sm" @click="edit = !edit" x-text="edit ? 'Tutup' : 'Ubah'"></x-ui.button>
                                                <form method="POST" action="{{ route('ujianppi.konfigurasi.hafalan.destroy', [$periode, $item]) }}"
                                                    onsubmit="return confirm('Hapus materi {{ $item->nama }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-ui.button type="submit" variant="ghost" size="sm" icon="trash"></x-ui.button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                    @if ($editable)
                                        <td colspan="3" x-show="edit" x-cloak class="bg-paper/60 px-4 py-3">
                                            <form method="POST" action="{{ route('ujianppi.konfigurasi.hafalan.update', [$periode, $item]) }}" class="flex flex-wrap items-end gap-2">
                                                @csrf
                                                @method('PUT')
                                                <div class="min-w-[240px] flex-1">
                                                    <label class="block pb-1 text-xs font-bold text-ink">Nama Materi</label>
                                                    <x-ui.input name="nama" :value="$item->nama" />
                                                </div>
                                                <x-ui.button type="submit" variant="primary" size="sm" icon="check">Simpan</x-ui.button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-10 text-center text-ink-faint">Belum ada materi setoran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>