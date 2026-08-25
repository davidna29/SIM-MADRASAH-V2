<x-layouts.page
    :title="'Kelola Ekstrakurikuler'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ekskul.show">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $ekskul->name }}</h1>
                <p class="mt-1.5 text-xs text-ink-soft">
                    Pembina: <span class="font-semibold">{{ $ekskul->pembina?->name ?? '—' }}</span>
                    @if ($ekskul->hari)
                        · {{ ucfirst($ekskul->hari) }}
                        {{ $ekskul->waktu ? substr($ekskul->waktu, 0, 5) : '' }}
                    @endif
                    @if ($ekskul->lokasi) · {{ $ekskul->lokasi }} @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if (auth()->user()->can('update', $ekskul))
                    <x-ui.button variant="secondary" size="sm" icon="pencil-square" href="{{ route('ekskul.edit', $ekskul) }}">Ubah</x-ui.button>
                @endif
                <x-ui.badge :variant="$ekskul->status === 'aktif' ? 'success' : 'neutral'">{{ ucfirst($ekskul->status) }}</x-ui.badge>
            </div>
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

        @can('view', $ekskul)
            <!-- Rekap kehadiran & nilai -->
            <div class="mt-6">
                <x-ui.sheet title="Rekap Kehadiran & Penilaian" subtitle="Predikat per sesi: A Baik Sekali · B Baik · C Cukup Baik · D Perlu Bimbingan Lebih" pinned ruled :padding="false">
                    <x-ui.table :headers="['NIS', 'Nama', 'Hadir', 'Izin', 'Sakit', 'Alpha', 'Rata-rata', 'Predikat Akhir']">
                        <x-slot name="emptySlot">Belum ada anggota.</x-slot>
                        <x-slot>
                            @foreach ($rekap as $r)
                                @php
                                    $member = $r['member'];
                                    $siswa = $member->enrollment->student;
                                @endphp
                                <tr class="transition hover:bg-paper/60">
                                    <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $siswa->nis }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-ink">{{ $siswa->displayName() }}</td>
                                    <td class="tabular px-4 py-3 text-center font-mono text-success">{{ $r['H'] }}</td>
                                    <td class="tabular px-4 py-3 text-center font-mono text-ink">{{ $r['I'] }}</td>
                                    <td class="tabular px-4 py-3 text-center font-mono text-ink">{{ $r['S'] }}</td>
                                    <td class="tabular px-4 py-3 text-center font-mono text-ink">{{ $r['A'] }}</td>
                                    <td class="tabular px-4 py-3 text-center font-mono font-semibold text-ink">{{ $r['rata'] ?? '–' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($r['predikat'])
                                            <x-ui.badge variant="success">{{ $r['predikat'] }}</x-ui.badge>
                                        @else
                                            <span class="text-xs text-ink-faint">–</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-ui.table>
                    <div class="border-t border-rule/70 px-5 py-3 text-xs text-ink-faint">
                        Rata-rata dari poin predikat sesi bernilai (A=4, B=3, C=2, D=1). Predikat hanya dinilai saat Hadir.
                    </div>
                </x-ui.sheet>
            </div>

            @can('update', $ekskul)
                <!-- Presensi sesi -->
                <div class="mt-6">
                    <form method="GET" action="{{ route('ekskul.show', $ekskul) }}"
                        class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                        <div class="flex flex-wrap items-end gap-3">
                            <div>
                                <label for="tanggal" class="block pb-1.5 text-xs font-bold text-ink">Tanggal Sesi</label>
                                <input type="date" id="tanggal" name="tanggal"
                                    value="{{ \Illuminate\Support\Carbon::parse($tanggal)->format('Y-m-d') }}"
                                    class="rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <x-ui.button type="submit" variant="secondary" size="md" icon="calendar-days">Muat Sesi</x-ui.button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('ekskul.presensi', $ekskul) }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ \Illuminate\Support\Carbon::parse($tanggal)->format('Y-m-d') }}">
                        <x-ui.sheet title="Presensi Sesi" :subtitle="\Illuminate\Support\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM YYYY')" pinned :padding="false">
                            @if ($rekap->isEmpty())
                                <p class="px-5 py-8 text-center text-sm text-ink-faint">Belum ada anggota — tambahkan terlebih dahulu di bawah.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-[560px] border-collapse text-sm">
                                        <thead>
                                            <tr class="border-b border-rule-strong bg-paper-deep/60">
                                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Anggota</th>
                                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Hadir</th>
                                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Izin</th>
                                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Sakit</th>
                                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Alpha</th>
                                                <th class="px-3 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Predikat</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-rule/70">
                                            @foreach ($rekap as $r)
                                                @php
                                                    $member = $r['member'];
                                                    $eid = $member->student_enrollment_id;
                                                    $existing = $presensi->get($eid);
                                                    $cur = old('statuses.'.$eid.'.status', $existing?->status ?? 'hadir');
                                                    $curP = old('statuses.'.$eid.'.predikat', $existing?->predikat);
                                                @endphp
                                                <tr x-data="{ st: '{{ $cur }}' }" class="transition hover:bg-paper/60">
                                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-ink">
                                                        {{ $member->enrollment->student->displayName() }}
                                                    </td>
                                                    @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $val => $lbl)
                                                        <td class="px-3 py-3 text-center">
                                                            <input type="radio" name="statuses[{{ $eid }}][status]" value="{{ $val }}"
                                                                @checked($cur === $val) @change="st = '{{ $val }}'"
                                                                class="size-4 border-rule-strong text-primary focus:ring-primary"
                                                                aria-label="{{ $lbl }} {{ $member->enrollment->student->name }}">
                                                        </td>
                                                    @endforeach
                                                    <td class="px-3 py-3 text-center">
                                                        <select name="statuses[{{ $eid }}][predikat]" :disabled="st !== 'hadir'"
                                                            class="rounded-[var(--radius-control)] bg-sheet px-2 py-1.5 text-xs font-semibold text-ink ring-1 ring-inset transition focus:outline-none focus:ring-2 focus:ring-primary disabled:opacity-40 {{ $errors->has('statuses.*.predikat') ? 'ring-danger' : 'ring-rule-strong' }}">
                                                            <option value="">—</option>
                                                            @foreach (\App\Models\ExtracurricularAttendance::POINTS as $p => $pts)
                                                                <option value="{{ $p }}" @selected($curP === $p)>{{ $p }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule/70 px-5 py-4 sm:flex-row">
                                    <p class="text-xs text-ink-faint">Predikat hanya berlaku saat status Hadir — kolom lain otomatis dikosongkan.</p>
                                    <x-ui.button type="submit" variant="primary" icon="check">Simpan Presensi</x-ui.button>
                                </div>
                            @endif
                        </x-ui.sheet>
                    </form>
                </div>

                <!-- Kelola anggota -->
                <div class="mb-10 mt-6">
                    <form method="GET" action="{{ route('ekskul.show', $ekskul) }}" class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
                        <input type="hidden" name="tanggal" value="{{ request('tanggal', \Illuminate\Support\Carbon::parse($tanggal)->format('Y-m-d')) }}">
                        <div class="flex flex-wrap items-end gap-3">
                            <div>
                                <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Kelas / Rombel</label>
                                <x-ui.select name="class_group_id" :full="false" class="w-44" :options="$classes->pluck('name', 'id')" :selected="$selectedClassId" placeholder="Pilih kelas…" />
                            </div>
                            <x-ui.button type="submit" variant="secondary" size="md">Muat Siswa</x-ui.button>
                        </div>
                    </form>

                    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Tambah anggota -->
                        <form method="POST" action="{{ route('ekskul.member.store', $ekskul) }}"
                            class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-5">
                            @csrf
                            <p class="text-sm font-bold text-ink">Tambah Anggota</p>
                            @if ($candidates->isNotEmpty())
                                <div class="mt-4 space-y-1.5">
                                    <x-ui.field label="Siswa" required :error="$errors->first('student_enrollment_id')">
                                        <x-ui.select name="student_enrollment_id" :options="$candidates->mapWithKeys(fn ($e) => [$e->id => $e->student->displayName()])"
                                            :selected="old('student_enrollment_id')" placeholder="Pilih siswa…" />
                                    </x-ui.field>
                                    <x-ui.field label="Tanggal Bergabung" :error="$errors->first('tanggal_bergabung')">
                                        <x-ui.input type="date" name="tanggal_bergabung" :value="old('tanggal_bergabung', now()->format('Y-m-d'))" />
                                    </x-ui.field>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <x-ui.button type="submit" size="sm" variant="primary" icon="plus">Tambah</x-ui.button>
                                </div>
                            @else
                                <p class="pt-2 text-xs text-ink-faint">Pilih kelas untuk memuat kandidat siswa yang belum menjadi anggota.</p>
                            @endif
                        </form>

                        <!-- Daftar anggota -->
                        <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-5">
                            <p class="text-sm font-bold text-ink">Anggota ({{ $members->count() }})</p>
                            @if ($members->isEmpty())
                                <p class="pt-2 text-xs text-ink-faint">Belum ada anggota.</p>
                            @else
                                <ul class="mt-3 divide-y divide-rule/70">
                                    @foreach ($members as $m)
                                        <li class="flex items-center justify-between gap-3 py-2.5">
                                            <div class="min-w-0">
                                                <p class="truncate text-[13px] font-semibold text-ink">{{ $m->enrollment->student->displayName() }}</p>
                                                <p class="tabular mt-0.5 font-mono text-[11px] text-ink-faint">
                                                    NIS {{ $m->enrollment->student->nis }} · {{ $m->enrollment->classGroup?->name ?? '—' }}
                                                </p>
                                            </div>
                                            <form method="POST" action="{{ route('ekskul.member.destroy', [$ekskul, $m]) }}"
                                                onsubmit="return confirm('Keluarkan siswa ini beserta riwayat presensinya?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" size="sm" variant="ghost" icon="x-mark">Keluarkan</x-ui.button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endcan
        @endcan
    </div>
</x-layouts.page>
