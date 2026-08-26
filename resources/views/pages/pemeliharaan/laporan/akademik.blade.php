<x-layouts.page title="Rekap Akademik" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    <x-slot:actions>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.pdf', 'akademik') }}">PDF</x-ui.button>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.csv', 'akademik') }}">CSV</x-ui.button>
    </x-slot:actions>

    <div class="mx-auto max-w-6xl space-y-6">
        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Total Siswa</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['total_siswa'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Rata-rata Umum</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['rata_rata_umum'] ?? '–' }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Kelas Terbaik</p>
                <p class="mt-1 text-lg font-bold text-ink">{{ $data['kelas_terbaik']['kelas'] ?? '–' }}</p>
                <p class="text-xs text-ink-soft">Rata-rata {{ $data['kelas_terbaik']['rata_rata'] ?? '–' }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Kelas Terendah</p>
                <p class="mt-1 text-lg font-bold text-ink">{{ $data['kelas_terendah']['kelas'] ?? '–' }}</p>
                <p class="text-xs text-ink-soft">Rata-rata {{ $data['kelas_terendah']['rata_rata'] ?? '–' }}</p>
            </div>
        </div>

        {{-- Tabel --}}
        <x-ui.sheet title="Rekap Akademik Per Kelas" subtitle="{{ $tahun->name }} · {{ ucfirst($tahun->semester) }}" :pinned="true" :padding="false">
            @if ($data['rows']->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-ink-soft">Belum ada data rapor terbit.</div>
            @else
                <x-ui.table :headers="['Kelas', 'Siswa', 'Rapor', 'Rata-rata', 'Pred. A', 'Pred. B', 'Pred. C', 'Pred. D']">
                    @foreach ($data['rows'] as $row)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-ink">{{ $row['kelas'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular">{{ $row['jumlah_siswa'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular">{{ $row['jumlah_rapor'] }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($row['rata_rata'])
                                    <x-ui.badge variant="{{ $row['rata_rata'] >= 85 ? 'success' : ($row['rata_rata'] >= 70 ? 'info' : 'warning') }}">{{ $row['rata_rata'] }}</x-ui.badge>
                                @else
                                    –
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-center tabular text-success">{{ $row['predikat_a'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-info">{{ $row['predikat_b'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-warning">{{ $row['predikat_c'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-danger">{{ $row['predikat_d'] }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.sheet>
    </div>
</x-layouts.page>
