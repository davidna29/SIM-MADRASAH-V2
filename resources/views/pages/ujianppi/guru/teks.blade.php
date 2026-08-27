<x-layouts.page
    :title="'Teks & Berita Acara — '.$peserta->student?->name"
    :roleLabel="'Akademik'"
    :breadcrumb="[['label' => 'Akademik', 'href' => route('dashboard')], ['label' => 'Ujian PPI', 'href' => route('ujianppi.guru.index')], ['label' => 'Teks & Berita Acara']]"
    active-route="ujianppi.guru.index">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Teks & Berita Acara</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Dokumen otomatis per siswa — semua placeholder terisi dari data sistem. Penguji dapat membacakan
                    langsung saat pengumuman; ekspor PDF untuk cetak/arsip.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button variant="secondary" size="sm" icon="printer" href="{{ route('ujianppi.guru.teks.pdf', [$periode, $peserta]) }}">Unduh Berita Acara (PDF)</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        <!-- Pemilih peserta & penguji penutup -->
        <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <form method="GET" action="{{ route('ujianppi.guru.teks', [$periode, $peserta]) }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block pb-1.5 text-xs font-bold text-ink">Peserta</label>
                    <x-ui.select name="peserta" :full="false" class="w-64"
                        :options="$periode->participants()->with('student')->get()->mapWithKeys(fn ($p) => [$p->id => $p->no_urut.'. '.($p->student?->name)])->all()"
                        :selected="$peserta->id" onchange="this.form.submit()" />
                </div>
                <div>
                    <label class="block pb-1.5 text-xs font-bold text-ink">Penguji Penutup ({{ $vars['NAMA_PENGUJI_PENUTUP'] }})</label>
                    <x-ui.select name="penutup" :full="false" class="w-40"
                        :options="[1 => 'Penguji I', 2 => 'Penguji II', 3 => 'Penguji III']" :selected="$penutup" onchange="this.form.submit()" />
                </div>
            </form>
        </div>

        <!-- Info kunci -->
        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-4">
            <x-ui.kpi label="Nomor Urut" :value="$peserta->no_urut" />
            <x-ui.kpi label="Ruang" :value="$peserta->room?->nama ?? '—'" />
            <x-ui.kpi label="Nilai Akhir" :value="$service::fmt($peserta->nilai_akhir)" />
            <x-ui.kpi label="Predikat" :value="$peserta->predicateScale?->predikat ?? '—'" />
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <x-ui.sheet :title="'Teks Pembawa Acara'" :subtitle="'{{NAMA_PENGUJI_PENUTUP}} bisa dipilih manual (default Penguji III).'">
                <pre class="whitespace-pre-wrap rounded-[var(--radius-control)] bg-paper p-4 font-serif text-sm leading-relaxed text-ink ring-1 ring-inset ring-rule/60">{{ $teks_mc }}</pre>
            </x-ui.sheet>

            <x-ui.sheet :title="'Berita Acara'" :subtitle="'Siap dibacakan — kolom TTD 3 penguji.'">
                <pre class="whitespace-pre-wrap rounded-[var(--radius-control)] bg-paper p-4 font-serif text-sm leading-relaxed text-ink ring-1 ring-inset ring-rule/60">{{ $teks_ba }}</pre>
                <div class="mt-4">
                    <x-ui.button variant="secondary" size="sm" icon="printer" href="{{ route('ujianppi.guru.teks.pdf', [$periode, $peserta]) }}">Unduh PDF</x-ui.button>
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>