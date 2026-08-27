<x-layouts.page
    :title="'Input Nilai Ujian Lisan — '.$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[['label' => 'Akademik', 'href' => route('dashboard')], ['label' => 'Ujian PPI', 'href' => route('ujianppi.guru.index')], ['label' => 'Input Ujian Lisan']]"
    active-route="ujianppi.guru.index">

    <div class="mx-auto max-w-[1400px]">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Input Nilai Ujian Lisan</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Ruang <b>{{ $room->nama }}</b> — Anda menilai aspek {{ $urutan === 2 ? 'Penguji II' : ($urutan === 3 ? 'Penguji III' : 'Penguji I') }}.
                </p>
            </div>
            <x-ui.button variant="ghost" size="sm" icon="arrow-left" href="{{ route('ujianppi.guru.index') }}">Kembali</x-ui.button>
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

        <!-- Pilih ruang / posisi penguji (admin) -->
        @if ($isAdmin)
            <form method="GET" action="{{ route('ujianppi.guru.ujian', $periode) }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block pb-1.5 text-xs font-bold text-ink">Ruang</label>
                        <x-ui.select name="room" :full="false" class="w-48" :options="$rooms->pluck('nama', 'id')" :selected="$room->id" onchange="this.form.submit()" />
                    </div>
                    <div>
                        <label class="block pb-1.5 text-xs font-bold text-ink">Posisi Penguji</label>
                        <x-ui.select name="urutan" :full="false" class="w-40" :options="[1 => 'Penguji I', 2 => 'Penguji II', 3 => 'Penguji III']" :selected="$urutan" onchange="this.form.submit()" />
                    </div>
                </div>
            </form>
        @endif

        <div class="mt-6 overflow-x-auto rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
            <table class="w-full min-w-[900px] border-collapse text-sm">
                <thead>
                    <tr class="border-b-2 border-rule-strong bg-paper/50">
                        <th scope="col" class="sticky left-0 z-10 bg-paper px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">No</th>
                        <th scope="col" class="sticky left-12 z-10 bg-paper px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Siswa</th>
                        @foreach ($categories as $category)
                            <th scope="col" colspan="{{ $category->aspects->count() }}" class="border-l border-rule/60 bg-primary-soft/40 px-2 py-3 text-center text-[11px] font-bold uppercase tracking-wide text-primary-strong">{{ $category->nama }}</th>
                        @endforeach
                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Simpan</th>
                    </tr>
                    <tr class="border-b border-rule/60 bg-paper/30">
                        <td colspan="2"></td>
                        @foreach ($categories as $category)
                            @foreach ($category->aspects as $aspect)
                                <td class="px-2 py-2 text-center text-[11px] font-semibold text-ink-faint">{{ trim($category->kode.'.'.$aspect->kode, '.') }} · {{ $aspect->nama }}</td>
                            @endforeach
                        @endforeach
                        <td></td>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule/70">
                    @forelse ($participants as $p)
                        @php $formId = 'nilai-ujian-'.$p->id; @endphp
                        <tr class="transition hover:bg-paper/60">
                            <td class="sticky left-0 bg-sheet px-4 py-2.5 tabular font-mono text-xs font-semibold text-ink-faint">{{ $p->no_urut }}</td>
                            <td class="sticky left-12 bg-sheet px-4 py-2.5">
                                <span class="font-semibold text-ink">{{ $p->student?->name }}</span>
                                <span class="block text-[11px] text-ink-faint">NIS {{ $p->student?->nis }}</span>
                            </td>
                            @foreach ($categories as $category)
                                @foreach ($category->aspects as $aspect)
                                    <td class="px-1.5 py-2 text-center">
                                        <input type="number" name="nilai[{{ $aspect->id }}]" min="0" max="100" form="{{ $formId }}"
                                            value="{{ $scores[$p->id][$aspect->id]->nilai ?? '' }}" placeholder="—"
                                            title="{{ $aspect->nama }}"
                                            class="w-14 rounded-md bg-sheet px-2 py-1.5 text-center text-xs tabular text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                                    </td>
                                @endforeach
                            @endforeach
                            <td class="px-4 py-2.5 text-right">
                                <x-ui.button type="submit" form="{{ $formId }}" variant="secondary" size="sm" icon="check">Simpan</x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-10 text-center text-ink-faint">Belum ada peserta di ruang ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="mt-3 text-xs text-ink-faint">
            Kosongkan input lalu simpan = menghapus nilai aspek tersebut. Nilai 0–100; perhitungan otomatis dijalankan setiap penyimpanan.
        </p>
    </div>

    {{-- Form tersembunyi per peserta (atribut `form` pada input & tombol) --}}
    @foreach ($participants as $p)
        <form id="nilai-ujian-{{ $p->id }}" method="POST" action="{{ route('ujianppi.guru.ujian.store', [$periode, $p]) }}" class="hidden">
            @csrf
            <input type="hidden" name="room" value="{{ $room->id }}">
            <input type="hidden" name="urutan" value="{{ $urutan }}">
        </form>
    @endforeach
</x-layouts.page>