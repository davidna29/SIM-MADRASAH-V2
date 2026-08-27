<x-layouts.page
    :title="'Periode Ujian PPI'"
    :roleLabel="'Akademik'"
    :breadcrumb="[['label' => 'Akademik', 'href' => route('dashboard')], ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')], ['label' => 'Periode Ujian']]"
    active-route="ujianppi.periode.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Periode Ujian PPI</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Event ujian akhir Kelas VI (munaqasah) — sekali per tahun ajaran. Satu periode = satu rangkaian
                    penuh: setoran hafalan, ujian lisan, perhitungan otomatis, dokumen, dan koreksi.
                </p>
            </div>
            <x-ui.button variant="secondary" size="md" icon="arrow-path" href="{{ route('ujianppi.guru.index') }}">Beranda Guru Ujian</x-ui.button>
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

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="min-w-0 lg:col-span-2">
                <x-ui.sheet :title="'Daftar Periode'" :subtitle="'Urut terbaru — klik periode untuk membuka konfigurasi dan status.'">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-rule-strong">
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Judul</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">TA</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Status</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Peserta</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @forelse ($periods as $period)
                                    @php
                                        $variant = match ($period->status) {
                                            'draft' => 'neutral',
                                            'setup' => 'info',
                                            'berlangsung' => 'success',
                                            'selesai' => 'warning',
                                            default => 'neutral',
                                        };
                                    @endphp
                                    <tr class="transition hover:bg-paper/60">
                                        <td class="px-4 py-3 font-semibold text-ink">{{ $period->judul }}</td>
                                        <td class="px-4 py-3 text-ink-soft">{{ $period->academicYear?->name }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <x-ui.badge :variant="$variant" :dot="! in_array($period->status, ['draft', 'diarsipkan'], true)">{{ $period->statusLabel() }}</x-ui.badge>
                                        </td>
                                        <td class="px-4 py-3 text-center tabular text-ink-soft">{{ $period->participants_count }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex justify-end gap-2">
                                                <x-ui.button variant="secondary" size="sm" icon="eye" href="{{ route('ujianppi.periode.show', $period) }}">Detail</x-ui.button>
                                                @if (in_array($period->status, ['draft', 'setup'], true))
                                                    <form method="POST" action="{{ route('ujianppi.periode.destroy', $period) }}"
                                                        onsubmit="return confirm('Hapus periode ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-ui.button type="submit" variant="ghost" size="sm" icon="trash">Hapus</x-ui.button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-ink-faint">
                                            Belum ada periode ujian. Buat periode pertama di panel kanan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $periods->links() }}
                    </div>
                </x-ui.sheet>
            </div>

            <div class="min-w-0">
                <x-ui.sheet :title="'Buat Periode Baru'" :subtitle="'Lengkapi konfigurasi setelah periode dibuat (Skala → Bobot → Aspek → Materi → Ruang/Grup/Peserta).'">
                    <form method="POST" action="{{ route('ujianppi.periode.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="academic_year_id" class="block pb-1.5 text-xs font-bold text-ink">Tahun Ajaran</label>
                            <x-ui.select name="academic_year_id" :options="$years->pluck('name', 'id')" :selected="old('academic_year_id')" placeholder="Pilih tahun ajaran…" />
                        </div>
                        <div>
                            <label for="judul" class="block pb-1.5 text-xs font-bold text-ink">Judul Periode</label>
                            <x-ui.input name="judul" placeholder="mis. Ujian PPI Kelas VI TP 2026/2027" :value="old('judul')" />
                        </div>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label for="tanggal_setoran_mulai" class="block pb-1.5 text-xs font-bold text-ink">Setoran Mulai</label>
                                <input type="date" name="tanggal_setoran_mulai" value="{{ old('tanggal_setoran_mulai') }}"
                                    class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label for="tanggal_setoran_selesai" class="block pb-1.5 text-xs font-bold text-ink">Setoran Selesai</label>
                                <input type="date" name="tanggal_setoran_selesai" value="{{ old('tanggal_setoran_selesai') }}"
                                    class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label for="tanggal_ujian" class="block pb-1.5 text-xs font-bold text-ink">Tanggal Ujian Lisan</label>
                                <input type="date" name="tanggal_ujian" value="{{ old('tanggal_ujian') }}"
                                    class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        <x-ui.button type="submit" variant="primary" icon="plus" class="w-full">Buat Periode</x-ui.button>
                    </form>
                </x-ui.sheet>
            </div>
        </div>
    </div>
</x-layouts.page>