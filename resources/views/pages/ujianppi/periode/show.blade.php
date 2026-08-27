<x-layouts.page
    :title="$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => 'Periode Ujian', 'href' => route('ujianppi.periode.index')],
        ['label' => $periode->judul],
    ]"
    active-route="ujianppi.periode.index">

    @php
        $variant = match ($periode->status) {
            'draft' => 'neutral',
            'setup' => 'info',
            'berlangsung' => 'success',
            'selesai' => 'warning',
            default => 'neutral',
        };
        $isSuperAdmin = auth()->user()->role === 'super_admin';
        $editable = in_array($periode->status, ['draft', 'setup'], true) || ($isSuperAdmin && ! $periode->isLocked());
        $configRoutes = [
            ['konfigurasi.skala', 'Skala Predikat', 'list-bullet', 'Atur predikat, rentang nilai, deskripsi, tanda tidak lulus.'],
            ['konfigurasi.bobot', 'Bobot Penilaian', 'calculator', 'Bobot Penguji I/II/III & Hafalan — total wajib 100%.'],
            ['konfigurasi.aspek', 'Struktur Aspek', 'list-bullet', 'Induk & anak aspek, assign ke penguji 1/2/3.'],
            ['konfigurasi.hafalan', 'Materi Setoran', 'book-open', 'Daftar surah Fase 1 (Yaasin … An-Naas).'],
            ['persiapan.ruang', 'Ruang & Penguji', 'building-library', 'Ruang ujian + 3 penguji (1 guru = 1 ruang).'],
            ['persiapan.grup', 'Grup & Pembimbing', 'user-group', 'Grup setoran 7–15 siswa + 1 pembimbing.'],
        ];
    @endphp

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $periode->judul }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <x-ui.badge :variant="$variant" :dot="! in_array($periode->status, ['draft', 'diarsipkan'], true)">{{ $periode->statusLabel() }}</x-ui.badge>
                    <x-ui.badge variant="neutral" :dot="false">{{ $periode->academicYear?->name }}</x-ui.badge>
                    @if ($periode->isLocked())
                        <x-ui.badge variant="danger" icon="lock-closed">Konfigurasi terkunci</x-ui.badge>
                    @else
                        <x-ui.badge variant="success" icon="lock-open">Konfigurasi terbuka</x-ui.badge>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Transisi status -->
                @foreach (\App\Models\PpiExamPeriod::transitions()[$periode->status] ?? [] as $target)
                    <form method="POST" action="{{ route('ujianppi.periode.status', $periode) }}"
                        @if (in_array($target, ['berlangsung', 'selesai'], true) || $target === 'diarsipkan')
                            onsubmit="return confirm('Ubah status periode menjadi {{ \App\Models\PpiExamPeriod::statusLabels()[$target] }}?');"
                        @endif>
                        @csrf
                        <input type="hidden" name="status" value="{{ $target }}">
                        <x-ui.button type="submit" variant="{{ $target === 'berlangsung' ? 'primary' : 'secondary' }}" size="sm">
                            → {{ \App\Models\PpiExamPeriod::statusLabels()[$target] }}
                        </x-ui.button>
                    </form>
                @endforeach

                @if (in_array($periode->status, ['setup', 'draft'], true))
                    <form method="POST" action="{{ route('ujianppi.periode.salin-skala', $periode) }}">
                        @csrf
                        <x-ui.button type="submit" variant="secondary" size="sm" icon="document-duplicate">Salin Skala</x-ui.button>
                    </form>
                @endif

                @if ($isSuperAdmin && $periode->status === 'berlangsung')
                    @if ($periode->isLocked())
                        <form method="POST" action="{{ route('ujianppi.periode.buka-kunci', $periode) }}"
                            onsubmit="return confirm('Buka kunci konfigurasi periode yang sedang berlangsung? Perubahan dicatat di audit log.');">
                            @csrf
                            <x-ui.button type="submit" variant="warning" size="sm" icon="lock-open">Buka Kunci</x-ui.button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('ujianppi.periode.kunci', $periode) }}">
                            @csrf
                            <x-ui.button type="submit" variant="secondary" size="sm" icon="lock-closed">Kunci Kembali</x-ui.button>
                        </form>
                    @endif
                @endif

                <x-ui.button variant="ghost" size="sm" icon="table-cells" href="{{ route('ujianppi.rekap.index', ['periode' => $periode->id]) }}">Rekap</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <!-- Ringkasan angka -->
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <x-ui.kpi label="Skala Predikat" :value="$periode->scales->count()" />
            <x-ui.kpi label="Induk Aspek" :value="$periode->categories->count()" />
            <x-ui.kpi label="Item Aspek" :value="$periode->categories->sum(fn ($c) => $c->aspects->count())" />
            <x-ui.kpi label="Materi Setoran" :value="$periode->hafalanMateri->count()" />
            <x-ui.kpi label="Ruang" :value="$periode->rooms->count()" />
            <x-ui.kpi label="Peserta" :value="$periode->participants->count()" />
        </div>

        @if (! $periode->bobotValid())
            <div class="mt-4">
                <x-ui.alert variant="warning" dismissible>
                    Total bobot penilaian saat ini <b>{{ $periode->bobotTotal() }}%</b> — wajib 100% sebelum periode
                    diubah ke <b>Berlangsung</b>. Perbaiki di menu Bobot Penilaian.
                </x-ui.alert>
            </div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <!-- Konfigurasi -->
            <div class="min-w-0 space-y-4 lg:col-span-2">
                <x-ui.sheet :title="'Konfigurasi Periode'" :subtitle="$periode->isLocked() ? 'Terkunci otomatis saat Berlangsung — buka kunci hanya oleh Super Admin bila perlu.' : 'Terbuka — semua pengaturan bisa diubah sebelum periode dikunci.'">
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($configRoutes as [$routeName, $label, $icon, $desc])
                            <a href="{{ route('ujianppi.'.$routeName, $periode) }}"
                                class="group flex items-start gap-3 rounded-[var(--radius-control)] bg-paper p-4 ring-1 ring-inset ring-rule/60 transition hover:ring-primary/40">
                                <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                    <x-dynamic-component :component="'svg-'.$icon" class="size-5" aria-hidden="true" />
                                </span>
                                <span>
                                    <span class="block text-sm font-bold text-ink group-hover:text-primary">{{ $label }}</span>
                                    <span class="mt-0.5 block text-xs leading-relaxed text-ink-soft">{{ $desc }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('ujianppi.persiapan.peserta', $periode) }}"
                            class="group flex items-start gap-3 rounded-[var(--radius-control)] bg-paper p-4 ring-1 ring-inset ring-rule/60 transition hover:ring-primary/40">
                            <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                <x-dynamic-component component="svg-user-plus" class="size-5" aria-hidden="true" />
                            </span>
                            <span>
                                <span class="block text-sm font-bold text-ink group-hover:text-primary">Peserta</span>
                                <span class="mt-0.5 block text-xs leading-relaxed text-ink-soft">
                                    Assign siswa Kelas VI ke ruang & grup; atur no urut (default abjad).
                                </span>
                            </span>
                        </a>
                        <a href="{{ route('ujianppi.arsip.index') }}"
                            class="group flex items-start gap-3 rounded-[var(--radius-control)] bg-paper p-4 ring-1 ring-inset ring-rule/60 transition hover:ring-primary/40">
                            <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary-soft text-primary">
                                <x-dynamic-component component="svg-archive-box" class="size-5" aria-hidden="true" />
                            </span>
                            <span>
                                <span class="block text-sm font-bold text-ink group-hover:text-primary">Arsip Tahun Lalu</span>
                                <span class="mt-0.5 block text-xs leading-relaxed text-ink-soft">Import rekap lama (read-only).</span>
                            </span>
                        </a>
                    </div>
                </x-ui.sheet>

                <!-- Edit periode -->
                @if ($editable)
                    <x-ui.sheet :title="'Ubah Info Periode'">
                        <form method="POST" action="{{ route('ujianppi.periode.update', $periode) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label for="judul" class="block pb-1.5 text-xs font-bold text-ink">Judul</label>
                                <x-ui.input name="judul" :value="old('judul', $periode->judul)" />
                            </div>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label for="tanggal_setoran_mulai" class="block pb-1.5 text-xs font-bold text-ink">Setoran Mulai</label>
                                    <input type="date" name="tanggal_setoran_mulai" value="{{ $periode->tanggal_setoran_mulai?->format('Y-m-d') }}"
                                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label for="tanggal_setoran_selesai" class="block pb-1.5 text-xs font-bold text-ink">Setoran Selesai</label>
                                    <input type="date" name="tanggal_setoran_selesai" value="{{ $periode->tanggal_setoran_selesai?->format('Y-m-d') }}"
                                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label for="tanggal_ujian" class="block pb-1.5 text-xs font-bold text-ink">Ujian Lisan</label>
                                    <input type="date" name="tanggal_ujian" value="{{ $periode->tanggal_ujian?->format('Y-m-d') }}"
                                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                            </div>
                            <x-ui.button type="submit" variant="primary" icon="check">Simpan Info</x-ui.button>
                        </form>
                    </x-ui.sheet>
                @endif
            </div>

            <!-- Sidebar: bobot & peserta -->
            <div class="min-w-0 space-y-4">
                <x-ui.sheet :title="'Bobot Penilaian'">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-ink-soft">Penguji I</dt><dd class="font-bold text-ink">{{ $periode->bobot_p1 }}%</dd></div>
                        <div class="flex justify-between"><dt class="text-ink-soft">Penguji II</dt><dd class="font-bold text-ink">{{ $periode->bobot_p2 }}%</dd></div>
                        <div class="flex justify-between"><dt class="text-ink-soft">Penguji III</dt><dd class="font-bold text-ink">{{ $periode->bobot_p3 }}%</dd></div>
                        <div class="flex justify-between"><dt class="text-ink-soft">Nilai Hafalan</dt><dd class="font-bold text-ink">{{ $periode->bobot_hafalan }}%</dd></div>
                        <div class="flex justify-between border-t border-rule/70 pt-2">
                            <dt class="text-ink-soft">Total</dt>
                            <dd class="font-extrabold {{ $periode->bobotValid() ? 'text-success' : 'text-danger' }}">{{ $periode->bobotTotal() }}%</dd>
                        </div>
                    </dl>
                </x-ui.sheet>

                <x-ui.sheet :title="'Peserta ('.count($periode->participants).')'" :padding="false">
                    @if ($periode->participants->isEmpty())
                        <div class="px-5 py-8 text-center text-sm text-ink-faint">Belum ada peserta.</div>
                    @else
                        <ul class="divide-y divide-rule/70">
                            @foreach ($periode->participants as $p)
                                <li class="flex items-center justify-between gap-3 px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="tabular font-mono text-xs font-semibold text-ink-faint">{{ $p->no_urut }}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-ink">{{ $p->student?->name }}</p>
                                            <p class="text-xs text-ink-soft">{{ $p->room?->nama }} · {{ $p->group?->nama ?? 'Tanpa grup' }}</p>
                                        </div>
                                    </div>
                                    <x-ui.button variant="ghost" size="sm" href="{{ route('ujianppi.guru.teks', [$periode, $p]) }}">Dokumen</x-ui.button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.sheet>
            </div>
        </div>
    </div>
</x-layouts.page>