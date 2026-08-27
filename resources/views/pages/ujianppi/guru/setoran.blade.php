<x-layouts.page
    :title="'Input Nilai Setoran — '.$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[['label' => 'Akademik', 'href' => route('dashboard')], ['label' => 'Ujian PPI', 'href' => route('ujianppi.guru.index')], ['label' => 'Input Setoran']]"
    active-route="ujianppi.guru.index">

    <div class="mx-auto max-w-[1400px]">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Input Nilai Setoran Hafalan</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Grup <b>{{ $group->nama }}</b> — nilai per surah per siswa pada Fase 1 (setoran).
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

        @if ($isAdmin)
            <form method="GET" action="{{ route('ujianppi.guru.setoran', $periode) }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block pb-1.5 text-xs font-bold text-ink">Grup Setoran</label>
                        <x-ui.select name="group" :full="false" class="w-48" :options="$groups->pluck('nama', 'id')" :selected="$group->id" onchange="this.form.submit()" />
                    </div>
                </div>
            </form>
        @endif

        <div class="mt-6 overflow-x-auto rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
            <table class="w-full min-w-[1200px] border-collapse text-sm">
                <thead>
                    <tr class="border-b-2 border-rule-strong bg-paper/50">
                        <th scope="col" class="sticky left-0 z-10 bg-paper px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">No</th>
                        <th scope="col" class="sticky left-12 z-10 bg-paper px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Siswa</th>
                        @foreach ($materi as $item)
                            <th scope="col" class="border-l border-rule/60 bg-primary-soft/40 px-2 py-3 text-center text-[11px] font-bold uppercase tracking-wide text-primary-strong">{{ $item->nama }}</th>
                        @endforeach
                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Simpan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule/70">
                    @forelse ($participants as $p)
                        @php $formId = 'nilai-setoran-'.$p->id; @endphp
                        <tr class="transition hover:bg-paper/60">
                            <td class="sticky left-0 bg-sheet px-4 py-2.5 tabular font-mono text-xs font-semibold text-ink-faint">{{ $p->no_urut }}</td>
                            <td class="sticky left-12 bg-sheet px-4 py-2.5">
                                <span class="font-semibold text-ink">{{ $p->student?->name }}</span>
                                <span class="block text-[11px] text-ink-faint">NIS {{ $p->student?->nis }}</span>
                            </td>
                            @foreach ($materi as $item)
                                <td class="border-l border-rule/50 px-1.5 py-2 text-center">
                                    <input type="number" name="nilai[{{ $item->id }}]" min="0" max="100" form="{{ $formId }}"
                                        value="{{ $scores[$p->id][$item->id]->nilai ?? '' }}" placeholder="—"
                                        title="{{ $item->nama }}"
                                        class="w-14 rounded-md bg-sheet px-2 py-1.5 text-center text-xs tabular text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                                </td>
                            @endforeach
                            <td class="px-4 py-2.5 text-right">
                                <input type="date" name="tanggal_setor" form="{{ $formId }}" value="{{ ($scores[$p->id] ?? collect())->first()?->tanggal_setor?->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                                    class="mr-2 rounded-[var(--radius-control)] bg-sheet px-2.5 py-1.5 text-xs text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">
                                <x-ui.button type="submit" form="{{ $formId }}" variant="secondary" size="sm" icon="check">Simpan</x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-10 text-center text-ink-faint">Belum ada peserta di grup ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="mt-3 text-xs text-ink-faint">
            Kosongkan input lalu simpan = menghapus nilai setoran tersebut. Tanggal setor default hari ini.
        </p>
    </div>

    @foreach ($participants as $p)
        <form id="nilai-setoran-{{ $p->id }}" method="POST" action="{{ route('ujianppi.guru.setoran.store', [$periode, $p]) }}" class="hidden">
            @csrf
            <input type="hidden" name="group" value="{{ $group->id }}">
        </form>
    @endforeach
</x-layouts.page>