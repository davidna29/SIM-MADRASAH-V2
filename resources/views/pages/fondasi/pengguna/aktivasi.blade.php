<x-layouts.page
    :title="'Akun Menunggu Aktivasi'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pengguna.aktivasi.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Akun Menunggu Aktivasi</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Siswa aktif tahun ajaran {{ $tahun?->name }} yang belum punya akun login (Portal Siswa).
                    Aktifkan untuk membuat akun dengan username NISN/NIS dan password tanggal lahir.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>{{ $errors->first() }}</x-ui.alert></div>
        @endif

        {{-- Kredensial batch terakhir — tampil sekali, bisa diunduh CSV (lalu dibuang) --}}
        @if ($credentials)
            <div class="mt-6">
                <x-ui.sheet title="Daftar Akun Baru" subtitle="Ditampilkan sekali — unduh CSV lalu data dibuang (tidak disimpan plaintext)." pinned ruled>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[560px] border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-rule-strong">
                                    <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Username</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Password Awal</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Kelas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @foreach ($credentials as $c)
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold text-ink">{{ $c['nama'] }}</td>
                                        <td class="tabular px-4 py-2.5 font-mono text-xs font-semibold text-ink">{{ $c['username'] }}</td>
                                        <td class="tabular px-4 py-2.5 font-mono text-xs text-ink-soft">{{ $c['password'] }}</td>
                                        <td class="px-4 py-2.5 text-xs text-ink-soft">{{ $c['kelas'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <x-ui.button variant="secondary" size="sm" icon="arrow-down-tray" href="{{ route('pengguna.aktivasi.export') }}">Unduh CSV</x-ui.button>
                    </div>
                </x-ui.sheet>
            </div>
        @endif

        {{-- Ringkasan kegagalan batch --}}
        @if ($failed)
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">{{ count($failed) }} baris gagal dibuat:</strong>
                    <ul class="mt-1.5 list-disc list-inside space-y-1">
                        @foreach ($failed as $f)
                            <li>{{ $f['nama'] }} — {{ $f['alasan'] }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            </div>
        @endif

        {{-- Filter --}}
        <form method="GET" action="{{ route('pengguna.aktivasi.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="academic_year_id" class="block pb-1.5 text-xs font-bold text-ink">Tahun Ajaran</label>
                    <x-ui.select name="academic_year_id" :full="false" class="w-44"
                        :options="$years" :selected="request('academic_year_id')" placeholder="Semua tahun" />
                </div>
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-40"
                        :options="$classOptions" :selected="request('class_group_id')" placeholder="Semua kelas" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama / NISN / NIS…"
                        class="w-56 rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('pengguna.aktivasi.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        {{-- Form bulk activation + tab --}}
        <form method="POST" action="{{ route('pengguna.aktivasi.aktifkan') }}" class="mt-6"
            x-data="{ tab: 'lengkap', selected: [], allIds: @json($complete->pluck('student.id')->all()) }">
            @csrf

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex rounded-[var(--radius-control)] bg-paper-deep p-1 ring-1 ring-inset ring-rule/60">
                    <button type="button" @click="tab = 'lengkap'"
                        :class="tab === 'lengkap' ? 'bg-sheet shadow-sm text-ink' : 'text-ink-faint hover:text-ink'"
                        class="rounded-md px-4 py-1.5 text-xs font-bold transition">
                        Data Lengkap ({{ $complete->count() }})
                    </button>
                    <button type="button" @click="tab = 'tidak-lengkap'"
                        :class="tab === 'tidak-lengkap' ? 'bg-sheet shadow-sm text-ink' : 'text-ink-faint hover:text-ink'"
                        class="rounded-md px-4 py-1.5 text-xs font-bold transition">
                        Data Tidak Lengkap ({{ $incomplete->count() }})
                    </button>
                </div>
                <x-ui.button type="submit" variant="primary" size="sm" icon="user-plus"
                    x-bind:disabled="selected.length === 0"
                    x-bind:class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : ''">
                    Aktifkan Terpilih (<span x-text="selected.length">0</span>)
                </x-ui.button>
            </div>

            {{-- Tab: data lengkap --}}
            <div x-show="tab === 'lengkap'" class="mt-4">
                <x-ui.sheet :padding="false">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px] border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-rule-strong">
                                    <th class="w-10 px-4 py-3">
                                        <input type="checkbox" class="size-4 rounded border-rule-strong text-primary focus:ring-primary"
                                            x-on:change="selected = $el.checked ? [...allIds] : []"
                                            x-bind:checked="selected.length === allIds.length && allIds.length > 0"
                                            aria-label="Pilih semua">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">NISN / NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Kelas</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Username (preview)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @forelse ($complete as $row)
                                    @php $s = $row['student']; $enr = $s->enrollments->first(); @endphp
                                    <tr class="transition hover:bg-paper/60">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" name="student_ids[]" :value="{{ $s->id }}" x-model="selected"
                                                class="size-4 rounded border-rule-strong text-primary focus:ring-primary"
                                                aria-label="Pilih {{ $s->displayName() }}">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">{{ mb_substr($s->displayName(), 0, 1) }}</span>
                                                <span class="font-semibold text-ink">{{ $s->displayName() }}</span>
                                            </div>
                                        </td>
                                        <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $s->nisn ?? '—' }} / {{ $s->nis }}</td>
                                        <td class="px-4 py-3"><x-ui.badge variant="info">{{ $enr?->classGroup?->name ?? 'Tanpa rombel' }}</x-ui.badge></td>
                                        <td class="tabular px-4 py-3 font-mono text-xs text-ink-soft">{{ $row['username'] }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-xs text-ink-faint">Tidak ada siswa aktif yang belum punya akun.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-ui.sheet>
            </div>

            {{-- Tab: data tidak lengkap (tidak bisa dicentang) --}}
            <div x-show="tab === 'tidak-lengkap'" x-cloak class="mt-4">
                <x-ui.sheet :padding="false">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px] border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-rule-strong">
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">NISN / NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Kelas</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Status Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @forelse ($incomplete as $row)
                                    @php $s = $row['student']; $enr = $s->enrollments->first(); @endphp
                                    <tr class="opacity-70">
                                        <td class="px-4 py-3 font-semibold text-ink">{{ $s->displayName() }}</td>
                                        <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $s->nisn ?? '—' }} / {{ $s->nis ?? '—' }}</td>
                                        <td class="px-4 py-3"><x-ui.badge variant="info">{{ $enr?->classGroup?->name ?? 'Tanpa rombel' }}</x-ui.badge></td>
                                        <td class="px-4 py-3"><x-ui.badge variant="danger" :dot="false">Data Tidak Lengkap</x-ui.badge></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-4 py-8 text-center text-xs text-ink-faint">Tidak ada data tidak lengkap.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-ui.sheet>
            </div>
        </form>
    </div>
</x-layouts.page>