<x-layouts.page
    :title="'Peserta — '.$periode->judul"
    :roleLabel="'Akademik'"
    :breadcrumb="[
        ['label' => 'Akademik', 'href' => route('dashboard')],
        ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')],
        ['label' => $periode->judul, 'href' => route('ujianppi.periode.show', $periode)],
        ['label' => 'Peserta'],
    ]"
    active-route="ujianppi.periode.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Peserta Ujian</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Seluruh siswa Kelas VI TA {{ $periode->academicYear?->name }}. Setiap siswa = 1 ruang ujian +
                    1 grup setoran (dobel tidak boleh). No urut default abjad nama, bisa diatur ulang sebelum periode dikunci.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        @if (! $editable)
            <div class="mt-6">
                <x-ui.alert variant="warning" dismissible>
                    Peserta terkunci — periode sudah <b>{{ $periode->statusLabel() }}</b>.
                </x-ui.alert>
            </div>
        @endif

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="min-w-0 lg:col-span-1">
                @if ($editable)
                    <x-ui.sheet :title="'Assign Siswa'" :subtitle="count($available).' siswa Kelas VI tersedia (belum ter-assign)'">
                        @if ($available->isEmpty())
                            <p class="text-sm text-ink-faint">Semua siswa Kelas VI sudah ter-assign pada periode ini.</p>
                        @elseif ($rooms->isEmpty() || $groups->isEmpty())
                            <p class="text-sm text-ink-faint">Buat minimal 1 ruang dan 1 grup terlebih dahulu.</p>
                        @else
                            <form method="POST" action="{{ route('ujianppi.persiapan.peserta.assign', $periode) }}" class="space-y-4">
                                @csrf
                                <div class="max-h-72 space-y-1.5 overflow-y-auto pr-1">
                                    @foreach ($available as $student)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-[var(--radius-control)] bg-paper px-3 py-2 ring-1 ring-inset ring-rule/60 transition hover:ring-primary/40">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                class="size-4 border-rule-strong text-primary focus:ring-primary">
                                            <span class="min-w-0 flex-1">
                                                <span class="block truncate text-sm font-semibold text-ink">{{ $student->name }}</span>
                                                <span class="block text-xs text-ink-soft">
                                                    NIS {{ $student->nis }} · {{ $student->enrollments->first()?->classGroup?->name }}
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block pb-1.5 text-xs font-bold text-ink">Ruang</label>
                                        <x-ui.select name="exam_room_id" class="w-full" :options="$rooms->pluck('nama', 'id')" placeholder="Pilih ruang…" />
                                    </div>
                                    <div>
                                        <label class="block pb-1.5 text-xs font-bold text-ink">Grup</label>
                                        <x-ui.select name="group_id" class="w-full" :options="$groups->pluck('nama', 'id')" placeholder="Pilih grup…" />
                                    </div>
                                </div>
                                <x-ui.button type="submit" variant="primary" icon="user-plus" class="w-full">Assign Terpilih</x-ui.button>
                            </form>
                        @endif
                    </x-ui.sheet>
                @endif
            </div>

            <div class="min-w-0 lg:col-span-2">
                <x-ui.sheet :title="'Peserta Ter-assign ('.count($assigned).')'" :padding="false">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[640px] border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-rule-strong">
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft w-14">No</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Siswa</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Rombel Asal</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Ruang</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Grup</th>
                                    @if ($editable)
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-rule/70">
                                @forelse ($assigned as $p)
                                    <tr>
                                        <td class="px-4 py-3 tabular font-mono text-xs font-semibold text-ink-faint">{{ $p->no_urut }}</td>
                                        <td class="px-4 py-3">
                                            <span class="font-semibold text-ink">{{ $p->student?->name }}</span>
                                            <span class="block text-xs text-ink-soft">NIS {{ $p->student?->nis }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-ink-soft">{{ $p->classGroup?->name }}</td>
                                        <td class="px-4 py-3 text-ink-soft">{{ $p->room?->nama }}</td>
                                        <td class="px-4 py-3 text-ink-soft">{{ $p->group?->nama ?? '—' }}</td>
                                        @if ($editable)
                                            <td class="px-4 py-3 text-right">
                                                <div x-data="{ edit: false }" class="inline-block">
                                                    <x-ui.button variant="ghost" size="sm" @click="edit = !edit" x-text="edit ? 'Tutup' : 'Atur'"></x-ui.button>
                                                    <div x-show="edit" x-cloak class="absolute right-0 z-20 mt-1 w-64 rounded-sheet bg-sheet p-3 shadow-sheet-raised ring-1 ring-inset ring-rule">
                                                        <form method="POST" action="{{ route('ujianppi.persiapan.peserta.update', [$periode, $p]) }}" class="space-y-2">
                                                            @csrf
                                                            @method('PUT')
                                                            <div>
                                                                <label class="block pb-1 text-xs font-bold text-ink">No Urut</label>
                                                                <x-ui.input type="number" name="no_urut" :value="$p->no_urut" min="1" />
                                                            </div>
                                                            <div>
                                                                <label class="block pb-1 text-xs font-bold text-ink">Ruang</label>
                                                                <x-ui.select name="exam_room_id" class="w-full" :options="$rooms->pluck('nama', 'id')" :selected="$p->exam_room_id" />
                                                            </div>
                                                            <div>
                                                                <label class="block pb-1 text-xs font-bold text-ink">Grup</label>
                                                                <x-ui.select name="group_id" class="w-full" :options="$groups->pluck('nama', 'id')" :selected="$p->group_id" placeholder="—" />
                                                            </div>
                                                            <x-ui.button type="submit" variant="primary" size="sm" icon="check" class="w-full">Simpan</x-ui.button>
                                                        </form>
                                                        <form method="POST" action="{{ route('ujianppi.persiapan.peserta.destroy', [$periode, $p]) }}" class="mt-2"
                                                            onsubmit="return confirm('Lepas {{ $p->student?->name }} dari periode ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <x-ui.button type="submit" variant="ghost" size="sm" icon="trash" class="w-full">Lepas dari Periode</x-ui.button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-10 text-center text-ink-faint">Belum ada peserta.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-ui.sheet>
            </div>
        </div>
    </div>
</x-layouts.page>