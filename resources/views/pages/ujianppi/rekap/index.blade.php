<x-layouts.page
    :title="'Rekap Kelas VI — Ujian PPI'"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => 'Rekap Kelas VI'],
    ]"
    active-route="ujianppi.rekap.index">

    <div class="mx-auto max-w-[1600px]">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Rekap Kelas VI</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Tabel besar rekap seluruh aspek + setoran + hasil hitung otomatis. Admin/Wakamad Kurikulum bisa
                    mengoreksi nilai langsung (alasan wajib, tercatat di audit log).
                </p>
            </div>
            @if ($activePeriod)
                @php
                    $params = [
                        'periode' => $activePeriod->id,
                        'room_id' => request('room_id'),
                        'group_id' => request('group_id'),
                        'lulus' => request('lulus'),
                        'class_group_id' => request('class_group_id'),
                        'q' => request('q'),
                    ];
                @endphp
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.button variant="secondary" size="sm" icon="printer" href="{{ route('ujianppi.rekap.pdf', $params + ['periode' => $activePeriod->id]) }}">PDF</x-ui.button>
                    <x-ui.button variant="secondary" size="sm" icon="table-cells" href="{{ route('ujianppi.rekap.excel', $params + ['periode' => $activePeriod->id]) }}">Excel</x-ui.button>
                </div>
            @endif
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

        <!-- Pilih periode -->
        <form method="GET" action="{{ route('ujianppi.rekap.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="periode" class="block pb-1.5 text-xs font-bold text-ink">Periode Ujian</label>
                    <x-ui.select name="periode" :full="false" class="w-72" :options="$periods->mapWithKeys(fn ($p) => [$p->id => $p->judul.' ('.$p->academicYear?->name.')'])->all()" :selected="$activePeriod?->id" placeholder="Pilih periode…" onchange="this.form.submit()" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Muat</x-ui.button>
            </div>
        </form>

        @if ($activePeriod && $data)
            @php
                $rooms = $activePeriod->rooms()->get();
                $groups = $activePeriod->groups()->get();
                $rombel = $data['participants']->pluck('classGroup')->filter()->unique('id');
                $canEdit = auth()->user()->can('rekapEdit', $activePeriod);
            @endphp
            <!-- Filter -->
            <form method="GET" action="{{ route('ujianppi.rekap.index') }}" class="mt-4">
                <input type="hidden" name="periode" value="{{ $activePeriod->id }}">
                <div class="flex flex-wrap items-end gap-2">
                    <div>
                        <label class="block pb-1.5 text-xs font-bold text-ink">Ruang</label>
                        <x-ui.select name="room_id" :full="false" class="w-36" :options="$rooms->pluck('nama', 'id')" :selected="request('room_id')" placeholder="Semua ruang" />
                    </div>
                    <div>
                        <label class="block pb-1.5 text-xs font-bold text-ink">Grup</label>
                        <x-ui.select name="group_id" :full="false" class="w-32" :options="$groups->pluck('nama', 'id')" :selected="request('group_id')" placeholder="Semua grup" />
                    </div>
                    <div>
                        <label class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                        <x-ui.select name="lulus" :full="false" class="w-32" :options="['lulus' => 'Lulus', 'tidak' => 'Tidak Lulus']" :selected="request('lulus')" placeholder="Semua" />
                    </div>
                    <div>
                        <label class="block pb-1.5 text-xs font-bold text-ink">Rombel</label>
                        <x-ui.select name="class_group_id" :full="false" class="w-28" :options="$rombel->pluck('name', 'id')" :selected="request('class_group_id')" placeholder="Semua" />
                    </div>
                    <div>
                        <label class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                        <x-ui.input name="q" :value="request('q')" placeholder="Nama / NIS" />
                    </div>
                    <x-ui.button type="submit" variant="secondary" size="md">Filter</x-ui.button>
                    @if (request()->has('room_id') || request()->has('group_id') || request()->has('lulus') || request()->has('class_group_id') || request()->has('q'))
                        <x-ui.button variant="ghost" size="md" href="{{ route('ujianppi.rekap.index', ['periode' => $activePeriod->id]) }}">Reset</x-ui.button>
                    @endif
                </div>
            </form>

            <div class="mt-4 overflow-x-auto rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                <table class="w-full min-w-[2200px] border-collapse text-xs">
                    <thead>
                        <tr class="border-b-2 border-rule-strong bg-paper/50">
                            <th scope="col" class="sticky left-0 z-10 bg-paper px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider text-ink-soft">No</th>
                            <th scope="col" class="sticky left-10 z-10 bg-paper px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider text-ink-soft w-56">NISN & Nama</th>
                            <th scope="col" class="px-3 py-2.5 text-left text-[10px] font-bold uppercase tracking-wider text-ink-soft">Ruang</th>
                            @foreach ($data['categories'] as $category)
                                <th scope="col" colspan="{{ $category->aspects->count() }}" class="border-l border-rule/60 bg-primary-soft/40 px-3 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-primary-strong">
                                    {{ $category->nama }}
                                </th>
                            @endforeach
                            @foreach ($data['hafalanMateri'] as $materi)
                                <th scope="col" class="border-l border-rule/60 bg-primary-soft/40 px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-primary-strong">
                                    {{ $materi->nama }}
                                </th>
                            @endforeach
                            <th scope="col" class="border-l border-rule/60 px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Jml P1</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Rata P1</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Jml P2</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Rata P2</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Jml P3</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Rata P3</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Rata Hafalan</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Jumlah</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Rata</th>
                            <th scope="col" class="border-l border-rule/60 bg-success-soft/40 px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-success">Predikat</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Deskripsi</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Lulus</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">JK</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Ayah</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Grup</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Rank</th>
                            <th scope="col" class="px-2 py-2.5 text-center text-[10px] font-bold uppercase tracking-wider text-ink-soft">Rank Lokal</th>
                            @if ($canEdit)
                                <th scope="col" class="sticky right-0 bg-paper px-3 py-2.5 text-right text-[10px] font-bold uppercase tracking-wider text-ink-soft">Aksi</th>
                            @endif
                        </tr>
                        @if ($data['categories']->isNotEmpty())
                            <tr class="border-b border-rule/60 bg-paper/30 text-[10px] text-ink-faint">
                                <td colspan="4"></td>
                                @foreach ($data['categories'] as $category)
                                    @foreach ($category->aspects as $aspect)
                                        <td class="px-1.5 py-1.5 text-center font-semibold">{{ trim($category->kode.'.'.$aspect->kode, '.') }}</td>
                                    @endforeach
                                @endforeach
                                @foreach ($data['hafalanMateri'] as $materi)
                                    <td class="px-1.5 py-1.5 text-center font-semibold">{{ $loop->iteration }}</td>
                                @endforeach
                                <td colspan="25"></td>
                            </tr>
                        @endif
                    </thead>
                    <tbody class="divide-y divide-rule/70">
                        @forelse ($data['participants'] as $p)
                            <tr class="transition hover:bg-paper/60">
                                <td class="sticky left-0 bg-sheet px-3 py-2 tabular font-mono font-semibold text-ink-faint">{{ $p->no_urut }}</td>
                                <td class="sticky left-10 bg-sheet px-3 py-2 w-56">
                                    <span class="block truncate font-semibold text-ink">{{ $p->student?->name }}</span>
                                    <span class="block text-[10px] text-ink-faint">{{ $p->student ? $service->nisnOf($p->student) : '—' }} · {{ $p->student?->nis }}</span>
                                </td>
                                <td class="px-3 py-2 text-ink-soft">{{ $p->room?->nama }}</td>

                                @foreach ($data['categories'] as $category)
                                    @foreach ($category->aspects as $aspect)
                                        <td class="px-1.5 py-2 text-center tabular">
                                            {{ $data['scores'][$p->id][$aspect->id]->nilai ?? '' }}
                                        </td>
                                    @endforeach
                                @endforeach

                                @foreach ($data['hafalanMateri'] as $materi)
                                    <td class="px-1.5 py-2 text-center tabular">
                                        {{ $data['hafalanScores'][$p->id][$materi->id]->nilai ?? '' }}
                                    </td>
                                @endforeach

                                <td class="border-l border-rule/60 px-2 py-2 text-center tabular text-ink-soft">{{ $p->jumlah_p1 ?? '' }}</td>
                                <td class="px-2 py-2 text-center tabular font-semibold text-ink">{{ $service::fmt($p->rata_p1) }}</td>
                                <td class="px-2 py-2 text-center tabular text-ink-soft">{{ $p->jumlah_p2 ?? '' }}</td>
                                <td class="px-2 py-2 text-center tabular font-semibold text-ink">{{ $service::fmt($p->rata_p2) }}</td>
                                <td class="px-2 py-2 text-center tabular text-ink-soft">{{ $p->jumlah_p3 ?? '' }}</td>
                                <td class="px-2 py-2 text-center tabular font-semibold text-ink">{{ $service::fmt($p->rata_p3) }}</td>
                                <td class="px-2 py-2 text-center tabular font-semibold text-ink">{{ $service::fmt($p->rata_hafalan) }}</td>
                                <td class="px-2 py-2 text-center tabular text-ink-soft">{{ $p->jumlah_ujian_lisan ?? '' }}</td>
                                <td class="px-2 py-2 text-center tabular font-semibold text-ink">{{ $service::fmt($p->rata_ujian_lisan) }}</td>
                                <td class="border-l border-rule/60 px-2 py-2 text-center">
                                    @if ($p->predicateScale)
                                        <span class="inline-flex items-center justify-center rounded-full bg-primary-soft px-2 py-0.5 text-[11px] font-bold text-primary-strong">{{ $p->predicateScale->predikat }}</span>
                                    @else
                                        <span class="text-ink-faint">—</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-center text-ink-soft">{{ $p->predicateScale?->deskripsi ?? '—' }}</td>
                                <td class="px-2 py-2 text-center">
                                    @if ($p->status_lulus === null)
                                        <span class="text-ink-faint">—</span>
                                    @elseif ($p->status_lulus)
                                        <x-ui.badge variant="success" :dot="false">Lulus</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="danger" :dot="false">Tidak</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-center text-ink-soft">{{ $p->student?->gender ?? '—' }}</td>
                                <td class="px-2 py-2 text-center text-ink-soft">{{ $p->student ? $service->fatherName($p->student) : '—' }}</td>
                                <td class="px-2 py-2 text-center text-ink-soft">{{ $p->group?->nama ?? '—' }}</td>
                                <td class="px-2 py-2 text-center tabular font-semibold text-ink">{{ $p->rank_total ?? '' }}</td>
                                <td class="px-2 py-2 text-center tabular font-semibold text-ink">{{ $p->rank_lokal ?? '' }}</td>
                                @if ($canEdit)
                                    <td class="sticky right-0 bg-sheet px-3 py-2 text-right">
                                        <div x-data="{ open: false }" class="relative inline-block">
                                            <x-ui.button variant="secondary" size="sm" icon="pencil-square" @click="open = !open">Koreksi</x-ui.button>
                                            <div x-show="open" x-cloak @click.away="open = false" class="fixed inset-0 z-40 flex items-center justify-center p-4">
                                                <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]" @click="open = false"></div>
                                                <div class="relative max-h-[85vh] w-full max-w-3xl overflow-y-auto rounded-sheet bg-sheet p-5 shadow-sheet-raised ring-1 ring-inset ring-rule">
                                                    <div class="flex items-center justify-between border-b border-rule/70 pb-3">
                                                        <h3 class="text-sm font-bold text-ink">Koreksi — {{ $p->student?->name }}</h3>
                                                        <button type="button" @click="open = false" class="rounded-md p-1 text-ink-faint hover:bg-paper-deep hover:text-ink" aria-label="Tutup">
                                                            <x-svg-x-mark class="size-5" aria-hidden="true" />
                                                        </button>
                                                    </div>
                                                    <form method="POST" action="{{ route('ujianppi.rekap.koreksi', [$activePeriod, $p]) }}" class="mt-4 space-y-4">
                                                        @csrf
                                                        <div class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2 lg:grid-cols-3">
                                                            @foreach ($data['categories'] as $category)
                                                                @foreach ($category->aspects as $aspect)
                                                                    <label class="flex items-center justify-between gap-2 rounded-[var(--radius-control)] bg-paper px-2.5 py-1.5 ring-1 ring-inset ring-rule/60">
                                                                        <span class="truncate text-[11px] text-ink-soft">{{ $category->nama }} · {{ $aspect->nama }}</span>
                                                                        <input type="number" name="nilai[{{ $aspect->id }}]" min="0" max="100"
                                                                            value="{{ $data['scores'][$p->id][$aspect->id]->nilai ?? '' }}" placeholder="—"
                                                                            class="w-16 rounded-md bg-sheet px-2 py-1 text-center text-xs text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">
                                                                    </label>
                                                                @endforeach
                                                            @endforeach
                                                            @foreach ($data['hafalanMateri'] as $materi)
                                                                <label class="flex items-center justify-between gap-2 rounded-[var(--radius-control)] bg-paper px-2.5 py-1.5 ring-1 ring-inset ring-rule/60">
                                                                    <span class="truncate text-[11px] text-ink-soft">Hafalan · {{ $materi->nama }}</span>
                                                                    <input type="number" name="nilai_hafalan[{{ $materi->id }}]" min="0" max="100"
                                                                        value="{{ $data['hafalanScores'][$p->id][$materi->id]->nilai ?? '' }}" placeholder="—"
                                                                        class="w-16 rounded-md bg-sheet px-2 py-1 text-center text-xs text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        <div>
                                                            <label class="block pb-1.5 text-xs font-bold text-ink">Alasan Koreksi <span class="text-danger">*</span></label>
                                                            <textarea name="alasan" rows="2" required placeholder="mis. Salah input penguji — nilai asli 85"
                                                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                                                            <p class="mt-1 text-[11px] text-ink-faint">Wajib diisi — perubahan (nilai lama → baru) tercatat di Activity & Audit Log.</p>
                                                        </div>
                                                        <div class="flex justify-end gap-2">
                                                            <x-ui.button variant="ghost" size="md" @click="open = false">Batal</x-ui.button>
                                                            <x-ui.button type="submit" variant="primary" size="md" icon="check">Simpan Koreksi</x-ui.button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="30" class="px-4 py-10 text-center text-ink-faint">Tidak ada peserta dengan filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @elseif (! $activePeriod)
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Pilih periode ujian untuk menampilkan Rekap Kelas VI.</p>
            </div>
        @endif
    </div>
</x-layouts.page>