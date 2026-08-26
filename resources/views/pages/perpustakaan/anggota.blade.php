<x-layouts.page
    :title="'Anggota Perpustakaan'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="perpustakaan.anggota.index">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Anggota Perpustakaan</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Daftar siswa & pegawai yang terdaftar sebagai anggota perpustakaan.
                </p>
            </div>
            @can('create', \App\Models\LibraryMember::class)
                <x-ui.modal id="modal-tambah-anggota" title="Tambah Anggota Perpustakaan">
                    <x-slot:trigger>
                        <x-ui.button variant="primary" icon="plus">Tambah Anggota</x-ui.button>
                    </x-slot:trigger>

                    <form method="POST" action="{{ route('perpustakaan.anggota.store') }}" x-data="libraryMemberPicker()" x-init="type = $el.querySelector('select[name=member_type]').value">
                        @csrf

                        <x-ui.field label="Jenis Anggota" required :error="$errors->first('member_type')">
                            <x-ui.select name="member_type"
                                :options="['siswa' => 'Siswa', 'pegawai' => 'Pegawai']"
                                :selected="old('member_type')"
                                placeholder="Pilih…"
                                x-model="type"
                                x-on:change="reset(); rombelId = ''" />
                        </x-ui.field>

                        <div x-show="type === 'siswa'" class="mt-4" x-cloak>
                            <x-ui.field label="Filter Rombel" hint="Opsional — persempit daftar sebelum mencari nama.">
                                <x-ui.select :options="$rombels" x-model="rombelId" placeholder="Semua rombel" x-on:change="pickedId = null" />
                            </x-ui.field>
                        </div>

                        <div x-show="type !== ''" class="mt-4" x-cloak>
                            <label class="block pb-1.5 text-xs font-bold text-ink">
                                Pilih <span x-text="type === 'pegawai' ? 'pegawai' : 'siswa'"></span>
                                <span class="text-danger" aria-hidden="true">*</span>
                            </label>
                            <div class="relative mt-1.5" x-show="pool.length > 0 || query !== ''">
                                <x-svg-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-ink-faint" aria-hidden="true" />
                                <input type="text" x-model="query" placeholder="Ketik nama untuk mencari…"
                                    class="w-full rounded-[var(--radius-control)] bg-sheet py-2.5 pl-9 pr-3 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>

                            <div class="mt-1.5 max-h-44 overflow-y-auto rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/70"
                                x-show="pool.length > 0">
                                <template x-for="person in pool" :key="person.id">
                                    <button type="button" x-on:click="pickedId = person.id; query = ''"
                                        class="flex w-full items-center justify-between gap-3 px-3 py-2 text-left text-[13px] text-ink transition hover:bg-primary-soft"
                                        :class="pickedId === person.id ? 'bg-primary-soft font-semibold' : ''">
                                        <span class="min-w-0 truncate" x-text="person.label"></span>
                                        <span class="shrink-0 text-[11px] tabular text-ink-faint"
                                            x-text="person.rombel ?? person.nip ?? ''"></span>
                                    </button>
                                </template>
                            </div>
                            <p class="mt-1.5 rounded-[var(--radius-control)] bg-paper-deep px-3 py-2 text-xs text-ink-faint"
                                x-show="pool.length === 0 && type !== ''" x-cloak
                                x-text="query ? 'Tidak ada hasil untuk “' + query + '”.' : 'Belum ada data ' + (type === 'pegawai' ? 'pegawai aktif' : 'siswa aktif') + '.'"></p>

                            <input type="hidden" name="student_id" :value="type === 'siswa' ? pickedId ?? '' : ''">
                            <input type="hidden" name="employee_id" :value="type === 'pegawai' ? pickedId ?? '' : ''">

                            <p class="mt-3 flex items-center gap-1.5 text-xs text-ink-faint" x-show="picked" x-cloak>
                                Terpilih:
                                <span class="font-semibold text-ink" x-text="picked?.label"></span>
                                <span class="tabular" x-text="picked?.rombel ?? picked?.nip ?? ''"></span>
                            </p>
                            @if ($pickerError = $errors->first('student_id') ?? $errors->first('employee_id'))
                                <p class="mt-2 flex items-center gap-1 text-xs font-medium text-danger">
                                    <x-svg-exclamation-circle class="size-3.5" aria-hidden="true" />
                                    {{ $pickerError }}
                                </p>
                            @endif
                        </div>
                    </form>

                    <x-slot:footer>
                        <x-ui.button type="button" variant="ghost" x-on:click="open = false">Batal</x-ui.button>
                        <x-ui.button type="button" variant="primary" x-on:click="$root.querySelector('[role=dialog] form').requestSubmit()">Simpan</x-ui.button>
                    </x-slot:footer>
                </x-ui.modal>
            @endcan
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('perpustakaan.anggota.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="type" class="block pb-1.5 text-xs font-bold text-ink">Jenis</label>
                    <x-ui.select name="type" :full="false" class="w-36" :options="['siswa' => 'Siswa', 'pegawai' => 'Pegawai']" :selected="request('type')" placeholder="Semua" />
                </div>
                <div>
                    <label for="status" class="block pb-1.5 text-xs font-bold text-ink">Status</label>
                    <x-ui.select name="status" :full="false" class="w-36" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" :selected="request('status')" placeholder="Semua" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Nama / no. anggota…" />
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('perpustakaan.anggota.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Daftar Anggota" :subtitle="$members->total() . ' anggota'" pinned :padding="false">
                <x-ui.table :headers="['No. Anggota', 'Nama', 'Jenis', 'Terkait', 'Status', 'Gabung', '']">
                    <x-slot name="emptySlot">Belum ada anggota terdaftar.</x-slot>
                    <x-slot>
                        @foreach ($members as $member)
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular whitespace-nowrap px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $member->member_no }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-ink">{{ $member->name }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <x-ui.badge :variant="$member->member_type === 'siswa' ? 'info' : 'success'">{{ ucfirst($member->member_type) }}</x-ui.badge>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">
                                    @if ($member->student)
                                        {{ $member->student->name }} ({{ $member->student->nis }})
                                    @elseif ($member->employee)
                                        {{ $member->employee->person?->name ?? '—' }} · {{ $member->employee->nip ?? '' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <x-ui.badge :variant="$member->status === 'aktif' ? 'success' : 'neutral'">{{ ucfirst($member->status) }}</x-ui.badge>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-soft">{{ $member->joined_at?->format('d/m/Y') ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    @can('delete', $member)
                                        <form method="POST" action="{{ route('perpustakaan.anggota.destroy', $member) }}" onsubmit="return confirm('Hapus anggota ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-5 py-3">
                    <x-ui.pagination :current="$members->currentPage()" :last="$members->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>
    </div>

    <script>
        function libraryMemberPicker() {
            return {
                type: '',
                rombelId: '',
                query: '',
                pickedId: null,
                students: @json($studentPool),
                employees: @json($employeePool),
                get pool() {
                    let base = this.type === 'pegawai'
                        ? this.employees
                        : this.students.filter(s => !this.rombelId || s.rombel_id === Number(this.rombelId));
                    const needle = this.query.trim().toLowerCase();
                    return needle
                        ? base.filter(p => p.label.toLowerCase().includes(needle) || String(p.nis ?? '').toLowerCase().includes(needle))
                        : base;
                },
                get picked() {
                    return [...this.students, ...this.employees].find(p => p.id === this.pickedId);
                },
                reset() {
                    this.query = '';
                    this.pickedId = null;
                },
            };
        }
    </script>
</x-layouts.page>
