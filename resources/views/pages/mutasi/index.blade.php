<x-layouts.page
    :title="'Pendaftar Pindah'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mutasi.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pendaftar Pindah</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Daftar calon siswa pindahan masuk (mutasi) beserta statusnya.
                </p>
            </div>
            <x-ui.button variant="secondary" size="sm" icon="cog-6-tooth" href="{{ route('mutasi.settings') }}">Pengaturan Mutasi</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>@foreach ($errors->all() as $error) {{ $error }} @endforeach</x-ui.alert></div>
        @endif

        {{-- Ringkasan statistik --}}
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <x-ui.kpi label="Total" :value="$stats['total']" icon="user-group" />
            <x-ui.kpi label="Menunggu" :value="$stats['submitted']" icon="clock" />
            <x-ui.kpi label="Diterima" :value="$stats['accepted']" icon="check-badge" />
            <x-ui.kpi label="Ditolak" :value="$stats['rejected']" icon="x-circle" />
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('mutasi.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="false" class="w-40"
                        :options="['submitted' => 'Menunggu', 'accepted' => 'Diterima', 'rejected' => 'Ditolak']"
                        :selected="request('status')" placeholder="Semua status" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama / No. pendaftaran / NIK…"
                        class="w-56 rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('mutasi.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        @include('pages.mutasi.partials.steps', ['active' => 'mutasi.index'])

        {{-- Tabel --}}
        <div class="mt-6">
            <x-ui.sheet :padding="false">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-rule-strong">
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">No. Registrasi</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Asal</th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Kelas Tujuan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule/70">
                            @forelse ($registrations as $reg)
                                <tr class="transition hover:bg-paper/60">
                                    <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $reg->registration_no }}</td>
                                    <td class="px-4 py-3 font-semibold text-ink">{{ $reg->name }}</td>
                                    <td class="px-4 py-3 text-xs text-ink-soft">{{ $reg->origin_school ?? '—' }}<br><span class="text-ink-faint">{{ $reg->kelas_asal ?? '' }}</span></td>
                                    <td class="px-4 py-3 text-center font-semibold text-ink">{{ $reg->kelas_tujuan ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $sv = match ($reg->status) { 'submitted' => 'warning', 'accepted' => 'success', 'rejected' => 'danger', default => 'neutral' };
                                            $sl = ucfirst($reg->status);
                                        @endphp
                                        <x-ui.badge :variant="$sv">{{ $sl }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <x-ui.button variant="ghost" size="sm" href="{{ route('mutasi.show', $reg) }}" icon="eye" aria-label="Lihat" />
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-8 text-center text-xs text-ink-faint">Belum ada pendaftar pindah.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($registrations->hasPages())
                    <div class="border-t border-rule/70 px-5 py-3">{{ $registrations->links() }}</div>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>