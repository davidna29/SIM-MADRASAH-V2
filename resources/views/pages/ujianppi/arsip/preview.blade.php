<x-layouts.page
    :title="'Preview Import Arsip'"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => 'Arsip', 'href' => route('ujianppi.arsip.index')],
        ['label' => 'Preview Import'],
    ]"
    active-route="ujianppi.arsip.index">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Preview Import Arsip</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    {{ $meta['judul'] ?? '—' }} — hanya baris <b>valid</b> yang disimpan saat konfirmasi.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('ujianppi.arsip.batal') }}">
                    @csrf
                    <x-ui.button variant="ghost" size="md" icon="x-mark">Batal</x-ui.button>
                </form>
                <form method="POST" action="{{ route('ujianppi.arsip.simpan') }}">
                    @csrf
                    <x-ui.button type="submit" variant="primary" size="md" icon="check">Simpan {{ $ok }} Baris</x-ui.button>
                </form>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 sm:max-w-md">
            <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-4 py-3">
                <div class="text-xs font-semibold text-ink-soft">Valid</div>
                <div class="tabular font-mono text-2xl font-bold text-success">{{ $ok }}</div>
            </div>
            <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-4 py-3">
                <div class="text-xs font-semibold text-ink-soft">Error</div>
                <div class="tabular font-mono text-2xl font-bold {{ $err > 0 ? 'text-danger' : 'text-ink-faint' }}">{{ $err }}</div>
            </div>
        </div>

        <div class="mt-6 overflow-x-auto rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
            <table class="w-full min-w-[760px] border-collapse text-sm">
                <thead>
                    <tr class="border-b border-rule-strong">
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">NISN</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Rata P1</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Rata P2</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Rata P3</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Hafalan</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Nilai Akhir</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Predikat</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Lulus</th>
                        <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule/70">
                    @forelse ($rows as $row)
                        <tr class="{{ empty($row['error']) ? '' : 'bg-danger-soft/20' }}">
                            <td class="px-4 py-2.5 font-mono text-xs text-ink-soft">{{ $row['nisn'] }}</td>
                            <td class="px-4 py-2.5 font-medium text-ink">{{ $row['nama'] }}</td>
                            <td class="px-4 py-2.5 text-center tabular">{{ $row['data']['rata_p1'] ?? '' }}</td>
                            <td class="px-4 py-2.5 text-center tabular">{{ $row['data']['rata_p2'] ?? '' }}</td>
                            <td class="px-4 py-2.5 text-center tabular">{{ $row['data']['rata_p3'] ?? '' }}</td>
                            <td class="px-4 py-2.5 text-center tabular">{{ $row['data']['rata_hafalan'] ?? '' }}</td>
                            <td class="px-4 py-2.5 text-center tabular">{{ $row['data']['nilai_akhir'] ?? '' }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['data']['predikat'] ?? '' }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['data']['status_lulus'] ?? '' }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $row['data']['rank'] ?? '' }}</td>
                            <td class="px-4 py-2.5">
                                @if (empty($row['error']))
                                    <x-ui.badge variant="success" :dot="false">Valid</x-ui.badge>
                                @else
                                    <span class="text-xs font-semibold text-danger">{{ $row['error'] }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-10 text-center text-ink-faint">Tidak ada data untuk preview.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.page>