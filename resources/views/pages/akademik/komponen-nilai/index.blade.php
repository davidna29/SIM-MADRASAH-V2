<x-layouts.page
    :title="'Komponen Nilai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="akademik.komponen-nilai.index">

    <div x-data="componentPage()" class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Komponen Nilai</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Kelola komponen penilaian dan bobotnya untuk Tahun Ajaran <strong>{{ $tahun->name }}</strong> ·
                    Semester {{ ucfirst($tahun->semester) }}. Guru menginput nilai per komponen; nilai akhir dihitung dari bobot.
                </p>
            </div>
            @if ($totalWeight < 100)
                <x-ui.button variant="primary" icon="plus" @click="openCreate()">Tambah Komponen</x-ui.button>
            @endif
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <x-ui.badge :variant="$totalWeight == 100 ? 'success' : 'warning'" icon="scale">
                Total bobot: {{ $totalWeight }}%
            </x-ui.badge>
            @if ($totalWeight != 100)
                <span class="text-xs font-medium text-ink-soft">
                    Total bobot harus tepat 100% agar nilai akhir dapat dihitung secara proporsional.
                </span>
            @endif
        </div>

        <div class="mt-4">
            <x-ui.sheet :padding="false">
                <x-ui.table :headers="['Urutan', 'Komponen', 'Bobot', 'Status Pemakaian', '']">
                    <x-slot name="emptySlot">Belum ada komponen nilai untuk tahun ajaran ini.</x-slot>
                    <x-slot>
                        @forelse ($components as $komponen)
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular w-14 px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-semibold text-ink">{{ $komponen->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="tabular font-mono text-sm font-bold text-ink">{{ $komponen->weight }}%</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($komponen->values_count > 0)
                                        <x-ui.badge variant="info">Terpakai (ada nilai)</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral">Belum dipakai</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.button size="sm" variant="ghost" icon="pencil-square"
                                            @click="openEdit({{ $komponen->id }}, {{ json_encode($komponen->name) }}, {{ $komponen->weight }})">
                                            Ubah
                                        </x-ui.button>
                                        <form method="POST" action="{{ route('akademik.komponen-nilai.destroy', $komponen) }}"
                                            onsubmit="return confirm('Hapus komponen {{ $komponen->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-ink-faint">
                                    Belum ada komponen nilai. Tambahkan minimal satu komponen untuk mulai penilaian berbasis komponen.
                                </td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-ui.table>
            </x-ui.sheet>
        </div>

        <!-- Modal tambah / ubah -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
            @keydown.escape.window="modalOpen = false" role="dialog" aria-modal="true" aria-labelledby="komponen-modal-title">
            <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]" @click="modalOpen = false"></div>

            <div class="relative w-full max-w-md rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule"
                x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0 translate-y-3 scale-[0.98]">
                <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                    <h3 id="komponen-modal-title" class="text-sm font-bold tracking-tight text-ink" x-text="editing ? 'Ubah Komponen Nilai' : 'Tambah Komponen Nilai'"></h3>
                    <button type="button" @click="modalOpen = false" class="rounded-md p-1 text-ink-faint transition hover:bg-paper-deep hover:text-ink" aria-label="Tutup">
                        <x-svg-x-mark class="size-5" aria-hidden="true" />
                    </button>
                </header>

                <form :action="formAction" method="POST" class="px-5 py-5">
                    @csrf
                    <input type="hidden" name="_method" :value="editing ? 'PUT' : 'POST'">
                    <div class="grid grid-cols-1 gap-5">
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-bold text-ink">Nama Komponen <span class="text-danger" aria-hidden="true">*</span></label>
                            <input id="name" name="name" x-ref="nameInput" x-model="form.name" type="text" maxlength="60"
                                placeholder="Tugas / Penilaian Harian"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                        <div class="space-y-1.5">
                            <label for="weight" class="block text-xs font-bold text-ink">Bobot (%) <span class="text-danger" aria-hidden="true">*</span></label>
                            <input id="weight" name="weight" x-model="form.weight" type="number" min="0" max="100" step="0.01"
                                placeholder="40"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary" required>
                            <p class="text-xs text-ink-faint">Total semua bobot harus tepat 100%.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-2 border-t border-rule/70 pt-4">
                        <x-ui.button variant="ghost" size="sm" @click.prevent="modalOpen = false">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="sm" icon="check" x-text="editing ? 'Simpan Perubahan' : 'Tambahkan'"></x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function componentPage() {
            return {
                modalOpen: false,
                editing: false,
                formAction: '{{ route('akademik.komponen-nilai.store') }}',
                form: { name: '', weight: '' },
                openCreate() {
                    this.editing = false;
                    this.formAction = '{{ route('akademik.komponen-nilai.store') }}';
                    this.form = { name: '', weight: '' };
                    this.modalOpen = true;
                    this.$nextTick(() => this.$refs.nameInput?.focus());
                },
                openEdit(id, name, weight) {
                    this.editing = true;
                    this.formAction = '{{ url('/akademik/komponen-nilai') }}' + '/' + id;
                    this.form = { name, weight: String(weight) };
                    this.modalOpen = true;
                    this.$nextTick(() => this.$refs.nameInput?.focus());
                },
            };
        }
    </script>
</x-layouts.page>