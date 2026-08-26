<x-layouts.page title="Rekap Kesiswaan" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    <x-slot:actions>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.pdf', 'kesiswaan') }}">PDF</x-ui.button>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.csv', 'kesiswaan') }}">CSV</x-ui.button>
    </x-slot:actions>

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Total Prestasi</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['total_prestasi'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Total Pelanggaran</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['total_pelanggaran'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Total Poin Pelanggaran</p>
                <p class="mt-1 text-2xl font-bold tabular {{ $data['total_poin'] > 100 ? 'text-danger' : 'text-ink' }}">{{ $data['total_poin'] }}</p>
            </div>
        </div>

        <x-ui.sheet title="Rekap Prestasi & Pelanggaran Per Kelas" subtitle="{{ $tahun->name }} · {{ ucfirst($tahun->semester) }}" :pinned="true" :padding="false">
            @if ($data['rows']->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-ink-soft">Belum ada data kesiswaan.</div>
            @else
                <x-ui.table :headers="['Kelas', 'Siswa', 'Prestasi', 'Pelanggaran', 'Poin']">
                    @foreach ($data['rows'] as $row)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-ink">{{ $row['kelas'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular">{{ $row['jumlah_siswa'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-success">{{ $row['prestasi'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-danger">{{ $row['pelanggaran'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular">{{ $row['poin_pelanggaran'] }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.sheet>
    </div>
</x-layouts.page>
