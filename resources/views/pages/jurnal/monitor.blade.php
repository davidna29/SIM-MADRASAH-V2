<x-layouts.page
    :title="'Jurnal Mengajar'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="jurnal.admin.index">

    <div class="mx-auto max-w-6xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Jurnal Mengajar</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Pantau pelaksanaan pembelajaran seluruh guru pada Tahun Ajaran {{ $tahun->name }}.
                Jurnal berstatus "Draf" belum dianggap sebagai pengisian penuh.
            </p>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('jurnal.admin.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas</label>
                    <x-ui.select name="class_group_id" :full="true" :options="$classes->pluck('name', 'id')" :selected="request('class_group_id')" placeholder="Semua kelas" />
                </div>
                <div>
                    <label for="subject_id" class="block pb-1.5 text-xs font-bold text-ink">Mapel</label>
                    <x-ui.select name="subject_id" :full="true" :options="$subjects->pluck('name', 'id')" :selected="request('subject_id')" placeholder="Semua mapel" />
                </div>
                <div>
                    <label for="teacher_id" class="block pb-1.5 text-xs font-bold text-ink">Guru</label>
                    <x-ui.select name="teacher_id" :full="true" :options="$teachers->pluck('name', 'id')" :selected="request('teacher_id')" placeholder="Semua guru" />
                </div>
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="true" :options="['terisi' => 'Terisi', 'draft' => 'Draf']" :selected="request('status')" placeholder="Semua status" />
                </div>
                <div>
                    <label for="from" class="block pb-1.5 text-xs font-bold text-ink">Dari</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="to" class="block pb-1.5 text-xs font-bold text-ink">Sampai</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <x-ui.button type="submit" variant="secondary" size="md" icon="funnel">Terapkan Filter</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('jurnal.admin.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <!-- Tabel jurnal -->
        <div class="mt-6">
            <x-ui.sheet title="Catatan Jurnal" :subtitle="$journals->total() . ' catatan' . ($journals->total() > 0 ? ' ditemukan' : '')" pinned :padding="false">
                <x-ui.table :headers="['Tanggal', 'Kelas', 'Mapel', 'Guru', 'Jam', 'Materi', 'Status']">
                    <x-slot name="emptySlot">Tidak ada jurnal yang cocok dengan filter.</x-slot>
                    <x-slot>
                        @foreach ($journals as $journal)
                            <tr class="align-top transition hover:bg-paper/60">
                                <td class="whitespace-nowrap px-4 py-3">
                                    <p class="tabular text-xs font-bold text-ink">{{ $journal->journal_date->format('d M Y') }}</p>
                                    <p class="mt-0.5 text-[11px] text-ink-faint">{{ $journal->journal_date->isoFormat('dddd') }}</p>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-ink">{{ $journal->assignment->classGroup->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-ink">{{ $journal->assignment->subject->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $journal->assignment->teacher->name }}</td>
                                <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs text-ink-faint">
                                    {{ $journal->period_no ? 'Ke-'.$journal->period_no : '—' }}
                                </td>
                                <td class="max-w-[260px] px-4 py-3">
                                    <p class="line-clamp-2 text-xs leading-relaxed text-ink">{{ $journal->materi }}</p>
                                    @if ($journal->lampiran)
                                        <p class="mt-1 flex items-center gap-1 text-[11px] font-semibold text-primary">
                                            <x-svg-paper-clip class="size-3" aria-hidden="true" /> lampiran
                                        </p>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if ($journal->status === 'terisi')
                                        <x-ui.badge variant="success" icon="check">Terisi</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" icon="document-text">Draf</x-ui.badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3">
                    <x-ui.pagination :current="$journals->currentPage()" :last="$journals->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
