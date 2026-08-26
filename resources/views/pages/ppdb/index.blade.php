<x-layouts.page
    :title="'PPDB Daring'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ppdb.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">PPDB Daring</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">Kelola pendaftaran calon peserta didik baru.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('ppdb.generate-nis') }}" class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-xs font-semibold text-ink ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep">
                    <x-svg-hashtag class="size-3.5" /> Generate NIS
                </a>
                <a href="{{ route('ppdb.assign-class-page') }}" class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-xs font-semibold text-ink ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep">
                    <x-svg-building-library class="size-3.5" /> Tentukan Kelas
                </a>
                <a href="{{ route('ppdb.export') }}" class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-xs font-semibold text-ink ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep">
                    <x-svg-arrow-down-tray class="size-3.5" /> Export Excel
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        {{-- Stats --}}
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach (['total' => ['Total', 'neutral'], 'submitted' => ['Menunggu', 'warning'], 'accepted' => ['Diterima', 'success'], 'rejected' => ['Ditolak', 'danger']] as $key => [$label, $variant])
                <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                    <p class="text-2xl font-extrabold tabular text-ink">{{ $stats[$key] }}</p>
                    <p class="text-xs text-ink-soft">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- Filter --}}
        <form method="GET" class="mt-6 flex flex-wrap gap-3">
            <x-ui.input name="q" :value="request('q')" placeholder="Cari nama / no. pendaftaran…" />
            <x-ui.select name="status" :options="['' => 'Semua Status', 'submitted' => 'Menunggu', 'accepted' => 'Diterima', 'rejected' => 'Ditolak']" :selected="request('status')" />
            <x-ui.button type="submit" variant="secondary" size="md" icon="funnel">Filter</x-ui.button>
        </form>

        {{-- Table --}}
        <div class="mt-4">
            <x-ui.sheet :padding="false" ruled>
                @if ($registrations->isEmpty())
                    <div class="py-12 text-center">
                        <x-svg-user-plus class="mx-auto size-12 text-ink-faint/40" />
                        <p class="mt-3 text-sm text-ink-faint">Belum ada pendaftar PPDB.</p>
                    </div>
                @else
                    <x-ui.table :headers="['No. Daftar', 'Nama Siswa', 'NIK', 'Status', 'Tanggal', 'Aksi']">
                        @foreach ($registrations as $reg)
                            <tr class="hover:bg-paper-deep/50">
                                <td class="px-4 py-3 font-mono text-xs text-ink">{{ $reg->registration_no }}</td>
                                <td class="px-4 py-3 text-sm font-bold text-ink">{{ strtoupper($reg->name) }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-ink-soft">{{ $reg->nik }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="match($reg->status) {
                                        'submitted' => 'warning',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        default => 'neutral',
                                    }">{{ ucfirst($reg->status) }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ $reg->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('ppdb.show', $reg) }}" class="text-xs font-semibold text-primary hover:underline">Detail →</a>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                    <div class="border-t border-rule/70 px-1 pt-3 pb-3">
                        <x-ui.pagination :current="$registrations->currentPage()" :last="$registrations->lastPage()" />
                    </div>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
