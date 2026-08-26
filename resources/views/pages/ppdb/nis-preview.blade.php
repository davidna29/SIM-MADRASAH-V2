<x-layouts.page
    :title="'Generate NIS'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ppdb.generate-nis">

    <div class="mx-auto max-w-4xl">
        <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Generate NIS / NISM</h1>
        <p class="mt-1.5 text-sm text-ink-soft">Preview NIS untuk siswa diterima yang belum memiliki NIS.</p>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        @if ($preview->isEmpty())
            <div class="mt-6 rounded-sheet bg-sheet p-8 text-center shadow-sheet ring-1 ring-inset ring-rule/60">
                <x-svg-check-circle class="mx-auto size-12 text-success/40" />
                <p class="mt-3 text-sm text-ink-faint">Semua siswa diterima sudah memiliki NIS.</p>
            </div>
        @else
            <div class="mt-6">
                <x-ui.sheet title="Preview NIS" subtitle="{{ $preview->count() }} siswa menunggu generate" :padding="false" pinned ruled>
                    <x-ui.table :headers="['No. Daftar', 'Nama Siswa', 'NIS Preview (18 digit)']">
                        @foreach ($preview as $item)
                            <tr class="hover:bg-paper-deep/50">
                                <td class="px-4 py-3 font-mono text-xs">{{ $item['registration_no'] }}</td>
                                <td class="px-4 py-3 text-sm font-bold">{{ strtoupper($item['name']) }}</td>
                                <td class="px-4 py-3 font-mono text-xs font-bold text-primary">{{ $item['preview_nis'] }}</td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                </x-ui.sheet>

                <div class="mt-4 flex justify-end">
                    <form method="POST" action="{{ route('ppdb.commit-nis') }}"
                        x-data="{ confirming: false }"
                        @submit="if(!confirming) { event.preventDefault(); confirming = true; }">
                        @csrf
                        <div x-show="confirming" class="mr-3 inline-flex items-center gap-2 text-xs text-danger font-semibold">
                            <x-svg-exclamation-triangle class="size-4" /> Yakin? NIS akan disimpan permanen.
                        </div>
                        <x-ui.button type="submit" variant="primary" size="md" icon="check-circle">
                            <span x-show="!confirming">Finalisasi NIS</span>
                            <span x-show="confirming" x-cloak>Ya, Simpan</span>
                        </x-ui.button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-layouts.page>
