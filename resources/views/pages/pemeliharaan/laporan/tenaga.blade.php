<x-layouts.page title="Rekap Tenaga" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    <x-slot:actions>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.pdf', 'tenaga') }}">PDF</x-ui.button>
        <x-ui.button variant="secondary" icon="document-arrow-down" href="{{ route('laporan.csv', 'tenaga') }}">CSV</x-ui.button>
    </x-slot:actions>

    <div class="mx-auto max-w-6xl space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Total Pegawai</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['total_pegawai'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Aktif</p>
                <p class="mt-1 text-2xl font-bold tabular text-success">{{ $data['total_aktif'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Guru</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">{{ $data['total_guru'] }}</p>
            </div>
            <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-bold text-ink-soft">Rasio Guru : Siswa</p>
                <p class="mt-1 text-2xl font-bold tabular text-ink">1 : {{ $data['rasio_guru_siswa'] }}</p>
            </div>
        </div>

        <x-ui.sheet title="Rekap Tenaga Per Role" :pinned="true" :padding="false">
            @if ($data['rows']->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-ink-soft">Belum ada data tenaga.</div>
            @else
                <x-ui.table :headers="['Role', 'Label', 'Total', 'Aktif', 'Nonaktif']">
                    @foreach ($data['rows'] as $row)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs tabular">{{ $row['role'] }}</td>
                            <td class="px-4 py-3 font-semibold text-ink">{{ $row['label'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular">{{ $row['total'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-success">{{ $row['aktif'] }}</td>
                            <td class="px-4 py-3 font-mono text-center tabular text-danger">{{ $row['nonaktif'] }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.sheet>
    </div>
</x-layouts.page>
