<x-layouts.page title="Rekap Kehadiran" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    <x-slot:actions>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.pdf', 'kehadiran') }}">PDF</x-ui.button>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.csv', 'kehadiran') }}">CSV</x-ui.button>
    </x-slot:actions>

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Kehadiran Umum</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['persen_hadir_umum'] }}%</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Total Siswa</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['total_siswa'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Kelas Tertinggi</p>
                <p class="mt-1 text-lg font-bold text-ink">{{ $data['kelas_terbaik']['kelas'] ?? '–' }}</p>
                <p class="text-xs text-ink-soft">{{ $data['kelas_terbaik']['persen_hadir'] ?? '–' }}%</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Kelas Terendah</p>
                <p class="mt-1 text-lg font-bold text-ink">{{ $data['kelas_terendah']['kelas'] ?? '–' }}</p>
                <p class="text-xs text-ink-soft">{{ $data['kelas_terendah']['persen_hadir'] ?? '–' }}%</p>
            </div>
        </div>

        <x-ui.sheet title="Rekap Kehadiran Per Kelas" subtitle="{{ $tahun->name }} · {{ ucfirst($tahun->semester) }}" :pinned="true" :padding="false">
            @if ($data['rows']->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-ink-soft">Belum ada data kehadiran.</div>
            @else
                <x-ui.table :headers="['Kelas', 'Siswa', 'Hadir', 'Sakit', 'Izin', 'Alpha', '% Hadir']">
                    @foreach ($data['rows'] as $row)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-ink">{{ $row['kelas'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular">{{ $row['jumlah_siswa'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-success">{{ $row['hadir'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-warning">{{ $row['sakit'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-info">{{ $row['izin'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-danger">{{ $row['alpha'] }}</td>
                            <td class="px-4 py-3 text-right">
                                <x-ui.badge variant="{{ $row['persen_hadir'] >= 90 ? 'success' : ($row['persen_hadir'] >= 75 ? 'info' : 'warning') }}">{{ $row['persen_hadir'] }}%</x-ui.badge>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.sheet>
    </div>
</x-layouts.page>
