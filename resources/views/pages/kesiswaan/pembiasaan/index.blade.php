<x-layouts.page
    :title="$title"
    :roleLabel="$roleLabel"
    :breadcrumb="[['label' => 'Kesiswaan', 'href' => route('dashboard')], ['label' => $title]]"
    active-route="{{ $modul }}.index">

    @php
        $grade = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];
    @endphp

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $title }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Daftar siswa untuk input dan cetak nilai
                    {{ $modul === 'ppi' ? 'PPI (Praktek Pengamalan Ibadah)' : 'Tahfidz' }}.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <form method="GET" action="{{ route($modul.'.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-40" :options="$classes->pluck('name', 'id')" :selected="request('class_group_id')" placeholder="Semua kelas" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Nama / NIS" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Filter</x-ui.button>
            </div>
        </form>

        <div class="mt-6 overflow-x-auto rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
            <table class="w-full min-w-[640px] border-collapse text-sm">
                <thead>
                    <tr class="border-b border-rule-strong">
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">NIS</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama Siswa</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Kelas</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule/70">
                    @forelse ($students as $s)
                        <tr>
                            <td class="px-4 py-3">{{ $s->nis }}</td>
                            <td class="px-4 py-3">{{ $s->name }}</td>
                            <td class="px-4 py-3">{{ $s->activeClass ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <x-ui.button variant="secondary" size="sm" icon="pencil-square" href="{{ route($modul.'.input', $s) }}">Input</x-ui.button>
                                    <x-ui.button variant="ghost" size="sm" icon="printer" href="{{ route($modul.'.cetak', $s) }}">Cetak</x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-ink-faint">Belum ada siswa aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </div>
</x-layouts.page>
