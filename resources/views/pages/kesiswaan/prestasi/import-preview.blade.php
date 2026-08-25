<x-layouts.page
    :title="'Preview Import'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="prestasi.import.preview">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Preview Import</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Periksa data sebelum disimpan. Hanya baris <strong>valid</strong> yang akan diimport.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.badge variant="success" icon="check">{{ $ok }} valid</x-ui.badge>
                <x-ui.badge :variant="$err > 0 ? 'danger' : 'neutral'" icon="exclamation-triangle">{{ $err }} gagal</x-ui.badge>
            </div>
        </div>

        @if ($rows->isEmpty())
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Tidak ada data untuk diimport.</p>
                <p class="mt-1 text-xs text-ink-faint">Kembali dan unggah file terlebih dahulu.</p>
                <div class="mt-4">
                    <x-ui.button variant="secondary" icon="arrow-left" href="{{ route('prestasi.import') }}">Kembali</x-ui.button>
                </div>
            </div>
        @else
            <div class="mt-6">
                <x-ui.sheet title="Data yang Terbaca" :subtitle="$rows->count() . ' baris'" pinned :padding="false">
                    <x-ui.table :headers="['NIS', 'Nama', 'Jenis', 'Nama Kegiatan', 'Tingkat', 'Tanggal', 'Status']">
                        <x-slot>
                            @foreach ($rows as $row)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $row['nis'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-xs font-semibold text-ink">{{ $row['nama'] ?: '—' }}</td>
                                    <td class="px-4 py-3 text-xs text-ink-soft">{{ ucfirst($row['data']['jenis'] ?? '') }}</td>
                                    <td class="max-w-[240px] px-4 py-3 text-[13px] font-semibold text-ink">{{ $row['nama_kegiatan'] }}</td>
                                    <td class="px-4 py-3 text-xs text-ink-soft">{{ ucfirst($row['tingkat']) }}</td>
                                    <td class="tabular px-4 py-3 font-mono text-xs text-ink-soft">{{ $row['tanggal'] ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($row['error'])
                                            <x-ui.badge variant="danger" icon="x-circle">{{ $row['error'] }}</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="success" icon="check">Valid</x-ui.badge>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-ui.table>
                    <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule/70 px-5 py-4 sm:flex-row">
                        <form method="POST" action="{{ route('prestasi.import.batal') }}">
                            @csrf
                            <x-ui.button type="submit" variant="ghost" icon="x-mark">Batal</x-ui.button>
                        </form>
                        @if ($ok > 0)
                            <form method="POST" action="{{ route('prestasi.import.simpan') }}">
                                @csrf
                                <x-ui.button type="submit" variant="primary" icon="check">Sematkan {{ $ok }} Baris Valid</x-ui.button>
                            </form>
                        @endif
                    </div>
                </x-ui.sheet>
            </div>
        @endif
    </div>
</x-layouts.page>
