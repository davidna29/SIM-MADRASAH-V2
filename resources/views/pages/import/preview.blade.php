@php
    $valid = collect($rows)->whereNull('error')->count();
    $invalid = collect($rows)->whereNotNull('error')->count();
@endphp

<x-layouts.page :title="'Pratinjau '.$config['title']" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb" :active-route="$config['index']">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pratinjau Import</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">{{ $config['title'] }} — tinjau hasil validasi sebelum menyimpan.</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route($module.'.import.batal') }}">
                    @csrf
                    <x-ui.button type="submit" variant="ghost">Batalkan</x-ui.button>
                </form>
                <form method="POST" action="{{ route($module.'.import.simpan') }}">
                    @csrf
                    <x-ui.button type="submit" variant="primary" icon="check" :disabled="$valid === 0">Simpan {{ $valid }} Baris Valid</x-ui.button>
                </form>
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <x-ui.kpi label="Total Baris" :value="count($rows)" icon="table-cells" />
            <x-ui.kpi label="Siap Disimpan" :value="$valid" icon="check-circle" />
            <x-ui.kpi label="Perlu Perbaikan" :value="$invalid" icon="exclamation-triangle" />
        </div>

        <div class="mt-4">
            <x-ui.sheet :padding="false">
                <x-ui.table :headers="['Baris', 'Data', 'Status']" :empty="$rows === []">
                    <x-slot name="emptySlot">Tidak ada data pada berkas.</x-slot>
                    <x-slot>
                        @foreach ($rows as $row)
                            <tr class="{{ $row['error'] ? 'bg-danger-soft/40' : '' }}">
                                <td class="tabular px-4 py-3 font-mono text-xs text-ink-faint">{{ $row['baris'] }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-ink">{{ $row['label'] }}</p>
                                    @if ($row['error'])
                                        <p class="mt-1 text-xs font-medium text-danger">{{ $row['error'] }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($row['error'])
                                        <x-ui.badge variant="danger">Perbaiki</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="success">Valid</x-ui.badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
            </x-ui.sheet>
        </div>

        <p class="mt-4 text-xs text-ink-faint">
            Baris yang ditandai merah tidak akan disimpan. Perbaiki berkas lalu unggah ulang bila diperlukan.
        </p>
    </div>
</x-layouts.page>
