<x-layouts.page
    :title="'Kehadiran Siswa'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="kehadiran.index">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kehadiran Siswa</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Catat kehadiran harian per rombel pada Tahun Ajaran {{ $tahun->name }}.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if ($date->isToday())
                    <x-ui.badge variant="primary" icon="check">Hari ini</x-ui.badge>
                @elseif ($editable)
                    <x-ui.badge variant="warning" icon="lock-open">Tanggal lampau · dibuka khusus</x-ui.badge>
                @else
                    <x-ui.badge variant="danger" icon="lock-closed">Terkunci</x-ui.badge>
                @endif
                <x-ui.button variant="secondary" size="sm" icon="chart-bar" href="{{ route('kehadiran.rekap', $selectedClass ? ['class_group_id' => $selectedClass->id] : []) }}">Rekap Bulanan</x-ui.button>
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
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <!-- Pilih kelas & tanggal -->
        <form method="GET" action="{{ route('kehadiran.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="class_group_id" class="block pb-1.5 text-xs font-bold text-ink">Rombel</label>
                    <x-ui.select name="class_group_id" :full="false" class="w-40" :options="$classes->pluck('name', 'id')" :selected="request('class_group_id')" placeholder="Pilih rombel…" />
                </div>
                <div>
                    <label for="date" class="block pb-1.5 text-xs font-bold text-ink">Tanggal</label>
                    @php $isPrivileged = in_array(auth()->user()->role, ['super_admin', 'kepala_madrasah', 'wakamad_kurikulum', 'wakamad_kesiswaan'], true); @endphp
                    <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}" @if (! $isPrivileged) min="{{ now()->format('Y-m-d') }}" @endif
                        class="rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <x-ui.button type="submit" variant="secondary" size="md">Muat</x-ui.button>
            </div>
        </form>

        @if ($selectedClass)
            <!-- Form absensi -->
            <form method="POST" action="{{ route('kehadiran.store') }}" class="mt-6">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $date->format('Y-m-d') }}">

                <x-ui.sheet
                    :title="'Absensi — Kelas ' . $selectedClass->name"
                    :subtitle="$date->isoFormat('dddd, D MMMM YYYY') . ' · ' . count($enrollments) . ' siswa aktif'"
                    pinned
                    :padding="false">
                    @if ($enrollments->isEmpty())
                        <div class="px-5 py-8 text-center text-sm text-ink-faint">Belum ada siswa aktif di kelas ini.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[640px] border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-rule-strong">
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">NIS</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Nama Siswa</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Hadir</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Izin</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Sakit</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Alpha</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rule/70">
                                    @foreach ($enrollments as $enrollment)
                                        @php
                                            $existing = $attendances->get($enrollment->id);
                                            $current = $existing?->status ?? 'hadir';
                                        @endphp
                                        <tr class="transition hover:bg-paper/60">
                                            <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $enrollment->student->nis }}</td>
                                            <td class="px-4 py-3 font-semibold text-ink">{{ $enrollment->student->displayName() }}</td>
                                            @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $val => $label)
                                                <td class="px-2 py-3 text-center">
                                                    <label class="inline-flex cursor-pointer items-center">
                                                        <input type="radio" name="attendances[{{ $enrollment->id }}][status]" value="{{ $val }}"
                                                            @checked($current === $val)
                                                            class="size-4 border-rule-strong text-primary focus:ring-primary"
                                                            aria-label="{{ $label }} {{ $enrollment->student->displayName() }}">
                                                    </label>
                                                </td>
                                            @endforeach
                                            <td class="px-4 py-3">
                                                <input type="text" name="attendances[{{ $enrollment->id }}][note]"
                                                    value="{{ $existing?->note }}"
                                                    placeholder="—"
                                                    class="w-full max-w-[160px] rounded-[var(--radius-control)] bg-sheet px-2.5 py-1.5 text-xs text-ink ring-1 ring-inset ring-rule-strong transition placeholder:text-ink-faint focus:outline-none focus:ring-2 focus:ring-primary">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule/70 px-5 py-4 sm:flex-row">
                            <p class="text-xs text-ink-faint">Status tidak diubah = Hadir. Menyimpan otomatis menandai hari ini sudah direview.</p>
                            <x-ui.button type="submit" variant="primary" icon="check">Simpan Kehadiran</x-ui.button>
                        </div>
                    @endif
                </x-ui.sheet>
            </form>
        @else
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-ink">Pilih rombel untuk mulai mencatat kehadiran.</p>
            </div>
        @endif
    </div>
</x-layouts.page>
