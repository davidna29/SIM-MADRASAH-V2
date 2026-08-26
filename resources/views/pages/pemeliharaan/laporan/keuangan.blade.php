<x-layouts.page title="Rekap Keuangan" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    <x-slot:actions>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.pdf', 'keuangan') }}">PDF</x-ui.button>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.csv', 'keuangan') }}">CSV</x-ui.button>
    </x-slot:actions>

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Total Terkumpul</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">Rp {{ number_format($data['total_terkumpul'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Persentase Lunas</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['persen_lunas_umum'] }}%</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Total Siswa</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['total_siswa'] }}</p>
            </div>
        </div>

        <x-ui.sheet title="Rekap SPP Per Kelas" subtitle="{{ $tahun->name }} · {{ ucfirst($tahun->semester) }}" :pinned="true" :padding="false">
            @if ($data['rows']->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-ink-soft">Belum ada data pembayaran.</div>
            @else
                <x-ui.table :headers="['Kelas', 'Siswa', 'Lunas (bulan)', 'Total (bulan)', 'Terkumpul', '% Lunas']">
                    @foreach ($data['rows'] as $row)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-ink">{{ $row['kelas'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular">{{ $row['jumlah_siswa'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular">{{ $row['lunas_bulan'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular">{{ $row['total_bulan'] }}</td>
                            <td class="px-4 py-3 font-mono text-right tabular">Rp {{ number_format($row['total_nominal'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <x-ui.badge variant="{{ $row['persen_lunas'] >= 90 ? 'success' : ($row['persen_lunas'] >= 70 ? 'info' : 'warning') }}">{{ $row['persen_lunas'] }}%</x-ui.badge>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.sheet>
    </div>
</x-layouts.page>
