<x-layouts.page
    :title="'Mata Pelajaran'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mapel.index">

    <div x-data="subjectPage()" x-init="$watch('modalOpen', v => { if (v) $nextTick(() => $refs.codeInput?.focus()); })" class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Mata Pelajaran</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Urutan daftar menentukan posisi mapel pada rapor dan laporan nilai — geser baris untuk mengubah urutan.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" @click="openCreate()">Tambah Mata Pelajaran</x-ui.button>
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

        <form method="GET" action="{{ route('mapel.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="w-64">
                    <div class="flex items-center gap-2 rounded-[var(--radius-control)] bg-paper px-3 ring-1 ring-inset ring-rule-strong transition focus-within:ring-2 focus-within:ring-primary">
                        <x-svg-magnifying-glass class="size-4 shrink-0 text-ink-faint" aria-hidden="true" />
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kode mapel…" class="w-full bg-transparent py-2.5 text-sm text-ink placeholder:text-ink-faint focus:outline-none" aria-label="Cari mapel">
                    </div>
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Cari</x-ui.button>
                @if (request('q'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('mapel.index') }}">Reset</x-ui.button>
                @endif
            </div>
        </form>

        <div x-data="sortableTable()" @reorder="persistOrder" class="mt-4">
            <x-ui.sheet :padding="false">
                <x-ui.table :headers="['', 'Urutan', 'Kode', 'Nama Mata Pelajaran', '']">
                    <x-slot name="emptySlot">Tidak ada mata pelajaran.</x-slot>
                    <x-slot>
                        @foreach ($subjects as $subject)
                            <tr data-id="{{ $subject->id }}" class="transition hover:bg-paper/60">
                                <td class="w-10 px-3 py-3">
                                    <button type="button" data-drag-handle class="cursor-grab rounded-md p-1 text-ink-faint transition hover:bg-paper-deep hover:text-ink active:cursor-grabbing" aria-label="Seret untuk mengubah urutan">
                                        <x-svg-ellipsis-vertical class="size-4" aria-hidden="true" />
                                    </button>
                                </td>
                                <td class="tabular w-12 px-3 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $loop->iteration }}</td>
                                <td class="tabular px-4 py-3 font-mono text-xs font-bold text-primary">{{ $subject->code }}</td>
                                <td class="px-4 py-3 font-semibold text-ink">{{ $subject->name }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.button size="sm" variant="ghost" icon="pencil-square" @click="openEdit({{ $subject->id }}, {{ json_encode($subject->code) }}, {{ json_encode($subject->name) }})">Ubah</x-ui.button>
                                        <form method="POST" action="{{ route('mapel.destroy', $subject) }}" onsubmit="return confirm('Hapus mata pelajaran ini?');">
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
                <div class="border-t border-rule/70 px-4 py-3.5">
                    <x-ui.pagination :current="$subjects->currentPage()" :last="$subjects->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>

        <!-- Modal tambah / ubah -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
            @keydown.escape.window="modalOpen = false" role="dialog" aria-modal="true" aria-labelledby="mapel-modal-title">
            <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]" @click="modalOpen = false"></div>

            <div class="relative w-full max-w-md rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule"
                x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0 translate-y-3 scale-[0.98]">
                <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                    <h3 id="mapel-modal-title" class="text-sm font-bold tracking-tight text-ink" x-text="editing ? 'Ubah Mata Pelajaran' : 'Tambah Mata Pelajaran'"></h3>
                    <button type="button" @click="modalOpen = false" class="rounded-md p-1 text-ink-faint transition hover:bg-paper-deep hover:text-ink" aria-label="Tutup">
                        <x-svg-x-mark class="size-5" aria-hidden="true" />
                    </button>
                </header>

                <form :action="formAction" method="POST" class="px-5 py-5">
                    @csrf
                    <input type="hidden" name="_method" :value="editing ? 'PUT' : 'POST'">
                    <div class="grid grid-cols-1 gap-5">
                        <div class="space-y-1.5">
                            <label for="code" class="block text-xs font-bold text-ink">Kode <span class="text-danger" aria-hidden="true">*</span></label>
                            <input id="code" name="code" x-ref="codeInput" x-model="form.code" type="text" maxlength="10" placeholder="MAT"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-bold text-ink">Nama Mata Pelajaran <span class="text-danger" aria-hidden="true">*</span></label>
                            <input id="name" name="name" x-model="form.name" type="text" placeholder="Matematika"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 hover:ring-ink-faint/60 focus:outline-none focus:ring-2 focus:ring-primary" required>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-2 border-t border-rule/70 pt-4">
                        <x-ui.button variant="ghost" size="sm" @click.prevent="modalOpen = false">Batal</x-ui.button>
                        <x-ui.button type="submit" variant="primary" size="sm" icon="check" x-text="editing ? 'Simpan Perubahan' : 'Sematkan & Simpan'"></x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function subjectPage() {
            return {
                modalOpen: false,
                editing: false,
                formAction: '{{ route('mapel.store') }}',
                form: { code: '', name: '' },
                openCreate() {
                    this.editing = false;
                    this.formAction = '{{ route('mapel.store') }}';
                    this.form = { code: '', name: '' };
                    this.modalOpen = true;
                },
                openEdit(id, code, name) {
                    this.editing = true;
                    this.formAction = '{{ url('/akademik/mata-pelajaran') }}' + '/' + id;
                    this.form = { code, name };
                    this.modalOpen = true;
                },
                async persistOrder(event) {
                    const order = event.detail.order;
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;
                    try {
                        await fetch('{{ route('mapel.reorder') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                            body: JSON.stringify({ order }),
                        });
                    } catch (e) {
                        console.error('Reorder failed', e);
                    }
                },
            };
        }
    </script>
</x-layouts.page>
