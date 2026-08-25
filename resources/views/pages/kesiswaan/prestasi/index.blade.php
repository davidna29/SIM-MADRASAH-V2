<x-layouts.page
    :title="'Prestasi Siswa'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="prestasi.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Prestasi Siswa</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Prestasi akademik & nonakademik — verifikasi oleh wakamad kesiswaan, publikasi di portofolio.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button variant="secondary" icon="arrow-down-tray" href="{{ route('prestasi.template') }}">Unduh Template</x-ui.button>
                <x-ui.button variant="secondary" icon="plus" href="{{ route('prestasi.create') }}">Catat Prestasi</x-ui.button>
                <x-ui.button variant="primary" icon="arrow-up-tray" href="{{ route('prestasi.import') }}">Import Excel</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('prestasi.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-40" :options="$classes->pluck('name', 'id')" :selected="request('class_group_id')" placeholder="Semua kelas" />
                </div>
                <div>
                    <label for="jenis" class="block pb-1.5 text-xs font-bold text-ink">Jenis</label>
                    <x-ui.select name="jenis" :full="false" class="w-40" :options="['akademik' => 'Akademik', 'nonakademik' => 'Nonakademik']" :selected="request('jenis')" placeholder="Semua jenis" />
                </div>
                <div>
                    <label for="tingkat" class="block pb-1.5 text-xs font-bold text-ink">Tingkat</label>
                    <x-ui.select name="tingkat" :full="false" class="w-44" :options="collect($tingkatList)->mapWithKeys(fn ($t) => [$t => ucfirst($t)])->all()" :selected="request('tingkat')" placeholder="Semua tingkat" />
                </div>
                <div>
                    <label for="status_verifikasi" class="block pb-1.5 text-xs font-bold text-ink">Verifikasi</label>
                    <x-ui.select name="status_verifikasi" :full="false" class="w-44" :options="['menunggu' => 'Menunggu', 'terverifikasi' => 'Terverifikasi', 'ditolak' => 'Ditolak']" :selected="request('status_verifikasi')" placeholder="Semua" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Nama / NIS" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('prestasi.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Daftar Prestasi" :subtitle="$achievements->total() . ' prestasi'" pinned :padding="false">
                <x-ui.table :headers="['NIS', 'Nama', 'Jenis', 'Kegiatan', 'Peringkat', 'Verifikasi', '']">
                    <x-slot name="emptySlot">Belum ada prestasi.</x-slot>
                    <x-slot>
                        @foreach ($achievements as $a)
                            @php
                                $var = match ($a->status_verifikasi) {
                                    'terverifikasi' => 'success',
                                    'ditolak' => 'danger',
                                    default => 'warning',
                                };
                            @endphp
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $a->student->nis }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-semibold text-ink">{{ $a->student->displayName() }}</td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ ucfirst($a->jenis) }}</td>
                                <td class="max-w-[260px] px-4 py-3">
                                    <p class="truncate text-[13px] font-semibold text-ink">{{ $a->nama_kegiatan }}</p>
                                    <p class="mt-0.5 text-[11px] text-ink-faint">{{ ucfirst($a->tingkat) }} @if ($a->penyelenggara) · {{ $a->penyelenggara }} @endif</p>
                                </td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ $a->peringkat ?: '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <form method="POST" action="{{ route('prestasi.verifikasi', $a) }}" class="flex items-center gap-1.5">
                                        @csrf
                                        <select name="status_verifikasi" onchange="this.form.submit()"
                                            class="rounded-[var(--radius-control)] bg-sheet px-2 py-1 text-xs font-semibold ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary">
                                            <option value="menunggu" @selected($a->status_verifikasi === 'menunggu')>Menunggu</option>
                                            <option value="terverifikasi" @selected($a->status_verifikasi === 'terverifikasi')>Terverifikasi</option>
                                            <option value="ditolak" @selected($a->status_verifikasi === 'ditolak')>Ditolak</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('prestasi.publikasi', $a) }}" title="Toggle publikasi">
                                            @csrf
                                            <input type="hidden" name="status_publikasi" value="{{ $a->status_publikasi === 'publik' ? 'internal' : 'publik' }}">
                                            <x-ui.button type="submit" size="sm" :variant="$a->status_publikasi === 'publik' ? 'info' : 'neutral'" :icon="$a->status_publikasi === 'publik' ? 'globe-alt' : 'eye-slash'">
                                                {{ $a->status_publikasi === 'publik' ? 'Publik' : 'Internal' }}
                                            </x-ui.button>
                                        </form>
                                        <x-ui.button size="sm" variant="secondary" icon="pencil-square" href="{{ route('prestasi.edit', $a) }}">Ubah</x-ui.button>
                                        <form method="POST" action="{{ route('prestasi.destroy', $a) }}" onsubmit="return confirm('Hapus prestasi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3">
                    <x-ui.pagination :current="$achievements->currentPage()" :last="$achievements->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
