<x-layouts.page
    :title="'Tentukan Kelas'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ppdb.assign-class-page">

    <div class="mx-auto max-w-6xl">
        <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Tentukan Kelas / Rombel</h1>
        <p class="mt-1.5 text-sm text-ink-soft">Distribusikan siswa diterima ke tiap rombel.</p>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        {{-- Class Distribution Summary --}}
        @if ($classCounts->isNotEmpty())
            <div class="mt-6">
                <x-ui.sheet title="Distribusi per Kelas" pinned ruled>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($classCounts as $class => $count)
                            <x-ui.badge variant="primary" :dot="false">{{ $class }}: {{ $count }} siswa</x-ui.badge>
                        @endforeach
                    </div>
                </x-ui.sheet>
            </div>
        @endif

        {{-- Siswa Belum Dikelompokkan --}}
        <div class="mt-6">
            <x-ui.sheet title="Siswa Belum Dikelompokkan" :subtitle="$accepted->count() . ' siswa'" :padding="false" pinned ruled>
                @if ($accepted->isEmpty())
                    <div class="py-12 text-center">
                        <x-svg-check-circle class="mx-auto size-12 text-success/40" />
                        <p class="mt-3 text-sm text-ink-faint">Semua siswa sudah dikelompokkan.</p>
                    </div>
                @else
                    <x-ui.table :headers="['No. Daftar', 'Nama', 'NIS', 'Kelas', 'Rombel', 'Aksi']">
                        @foreach ($accepted as $reg)
                            <tr class="hover:bg-paper-deep/50" x-data="{ editing: false }">
                                <td class="px-4 py-3 font-mono text-xs">{{ $reg->registration_no }}</td>
                                <td class="px-4 py-3 text-sm font-bold">{{ strtoupper($reg->name) }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-primary">{{ $reg->nis_nism ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span x-show="!editing">{{ $reg->kelas ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span x-show="!editing">{{ $reg->rombel ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('ppdb.assign-class', $reg) }}" class="inline-flex items-center gap-2">
                                        @csrf
                                        <select name="kelas" required
                                            class="h-8 rounded-[var(--radius-control)] bg-sheet px-2 text-xs ring-1 ring-inset ring-rule-strong focus:ring-2 focus:ring-primary">
                                            <option value="">—</option>
                                            @foreach (['I','II','III','IV','V','VI'] as $k)
                                                <option value="{{ $k }}" {{ $reg->kelas === $k ? 'selected' : '' }}>{{ $k }}</option>
                                            @endforeach
                                        </select>
                                        <input name="rombel" value="{{ $reg->rombel }}" maxlength="3" placeholder="A" required
                                            class="h-8 w-16 rounded-[var(--radius-control)] bg-sheet px-2 text-xs ring-1 ring-inset ring-rule-strong focus:ring-2 focus:ring-primary">
                                        <x-ui.button type="submit" variant="primary" size="sm">Simpan</x-ui.button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
