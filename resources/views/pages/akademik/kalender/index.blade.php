@php
    $first = \Carbon\CarbonImmutable::create($selectedYear, $selectedMonth, 1);
    $gridStart = $first->startOfWeek();
    $gridEnd = $first->endOfMonth()->endOfWeek();
    $eventsByDate = [];
    foreach ($events as $event) {
        $cursor = $event->start_date->toImmutable();
        while ($cursor->lessThanOrEqualTo($event->endDate())) {
            $eventsByDate[$cursor->toDateString()][] = $event;
            $cursor = $cursor->addDay();
        }
    }
    $weeks = [];
    for ($cursor = $gridStart; $cursor->lessThanOrEqualTo($gridEnd); $cursor = $cursor->addDay()) {
        $weeks[$cursor->format('o-W')][] = $cursor;
    }
    $monthLabels = collect($months)->mapWithKeys(fn ($m) => [$m['month'] => $m['label']])->all();
    $selectedLabel = $selected['label'] ?? $first->locale('id')->translatedFormat('F Y');
@endphp

<x-layouts.page :title="'Kalender Pendidikan'" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb" active-route="kalender.index">
    <div class="mx-auto max-w-7xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kalender Pendidikan</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Tahun Ajaran {{ $tahun->name }} — pekan dan hari efektif dihitung otomatis dari kegiatan serta hari libur.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button variant="secondary" icon="printer" href="{{ route('kalender.cetak') }}" target="_blank">Cetak</x-ui.button>
                @if ($canManage)
                    <x-ui.modal id="event-create" title="Tambah Kegiatan Kalender" confirm-text="Simpan Kegiatan">
                        <x-slot name="trigger">
                            <x-ui.button variant="primary" icon="plus">Tambah Kegiatan</x-ui.button>
                        </x-slot>

                        <form id="event-create-form" method="POST" action="{{ route('kalender.store') }}" class="space-y-4">
                            @csrf
                            <x-ui.field label="Judul kegiatan" required :error="$errors->first('title')">
                                <x-ui.input name="title" value="{{ old('title') }}" placeholder="Contoh: Penilaian Tengah Semester" required />
                            </x-ui.field>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <x-ui.field label="Kategori" required :error="$errors->first('category')">
                                    <x-ui.select name="category" :options="$categories" :selected="old('category', 'kegiatan')" required />
                                </x-ui.field>
                                <x-ui.field label="Keterangan" :error="$errors->first('description')">
                                    <x-ui.input name="description" value="{{ old('description') }}" placeholder="Opsional" />
                                </x-ui.field>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <x-ui.field label="Tanggal mulai" required :error="$errors->first('start_date')">
                                    <x-ui.input type="date" name="start_date" value="{{ old('start_date', $first->toDateString()) }}" required />
                                </x-ui.field>
                                <x-ui.field label="Tanggal selesai" hint="Kosongkan bila satu hari." :error="$errors->first('end_date')">
                                    <x-ui.input type="date" name="end_date" value="{{ old('end_date') }}" />
                                </x-ui.field>
                            </div>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm font-medium text-ink">
                                    <input type="checkbox" name="is_libur" value="1" @checked(old('is_libur')) class="size-4 rounded border-rule-strong text-primary focus:ring-primary">
                                    Hitung sebagai hari libur
                                </label>
                                <label class="flex items-center gap-2 text-sm font-medium text-ink">
                                    <input type="checkbox" name="is_efektif" value="1" @checked(old('is_efektif', true)) class="size-4 rounded border-rule-strong text-primary focus:ring-primary">
                                    Dihitung sebagai hari efektif pembelajaran
                                </label>
                            </div>
                        </form>

                        <x-slot name="footer">
                            <x-ui.button type="submit" form="event-create-form" variant="primary">Simpan Kegiatan</x-ui.button>
                        </x-slot>
                    </x-ui.modal>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error)
                        <span class="block">{{ $error }}</span>
                    @endforeach
                </x-ui.alert>
            </div>
        @endif

        <!-- Pemilih bulan -->
        <form method="GET" action="{{ route('kalender.index') }}" class="mt-6 flex flex-wrap items-end gap-3 rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
            <x-ui.field label="Bulan">
                <x-ui.select name="bulan" :full="false" class="min-w-48" :options="$monthLabels" :selected="$selectedMonth" />
            </x-ui.field>
            <x-ui.field label="Tahun">
                <x-ui.select name="tahun" :full="false" class="w-32" :options="collect($months)->pluck('start')->mapWithKeys(fn ($date) => [$date->year => $date->year])->all()" :selected="$selectedYear" />
            </x-ui.field>
            <x-ui.button type="submit" variant="secondary">Tampilkan</x-ui.button>
        </form>

        <!-- Ringkasan -->
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.kpi label="Pekan Efektif" :value="$selected['pekan_efektif']" suffix="pekan" :hint="$selectedLabel" icon="calendar-days" />
            <x-ui.kpi label="Hari Efektif" :value="$selected['hari_efektif']" suffix="hari" hint="Senin–Sabtu dikurangi libur" icon="check-circle" />
            <x-ui.kpi label="Hari Libur" :value="$selected['hari_libur']" suffix="hari" hint="Hari sekolah yang diliburkan" icon="x-circle" />
            <x-ui.kpi label="Minggu Tersedia" :value="$selected['minggu_tersedia']" suffix="pekan" hint="Seluruh pekan dalam periode" icon="calendar" />
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
            <!-- Grid kalender -->
            <x-ui.sheet>
                <div class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                    <h2 class="text-sm font-bold tracking-tight text-ink">{{ $selectedLabel }}</h2>
                    <span class="font-mono text-xs text-ink-faint">{{ $events->count() }} kegiatan</span>
                </div>
                <div class="overflow-x-auto p-3">
                    <table class="w-full min-w-[44rem] table-fixed border-separate border-spacing-1">
                        <thead>
                            <tr>
                                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                                    <th class="pb-1 text-center text-[11px] font-bold uppercase tracking-[0.08em] text-ink-faint">{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($weeks as $week)
                                <tr>
                                    @foreach ($week as $date)
                                        @php
                                            $inMonth = $date->month === $selectedMonth;
                                            $isSunday = $date->dayOfWeek === \Carbon\CarbonImmutable::SUNDAY;
                                            $dayEvents = $eventsByDate[$date->toDateString()] ?? [];
                                            $isToday = $date->isToday();
                                        @endphp
                                        <td class="align-top">
                                            <div class="min-h-[5.5rem] rounded-[var(--radius-control)] p-1.5 ring-1 ring-inset {{ $inMonth ? 'bg-paper ring-rule/60' : 'bg-sheet/40 ring-transparent' }} {{ $isSunday && $inMonth ? 'bg-paper-deep/60' : '' }}">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-mono text-xs font-bold tabular {{ $inMonth ? 'text-ink' : 'text-ink-faint/60' }} {{ $isToday ? 'rounded-full bg-primary px-1.5 text-white' : '' }}">{{ $date->day }}</span>
                                                    @if ($dayEvents !== [])
                                                        <span class="pin-dot size-1.5 bg-primary" aria-hidden="true"></span>
                                                    @endif
                                                </div>
                                                <div class="mt-1 space-y-0.5">
                                                    @foreach (array_slice($dayEvents, 0, 2) as $event)
                                                        <p class="truncate rounded px-1 py-0.5 text-[10px] font-semibold leading-tight {{ $event->is_libur ? 'bg-danger-soft text-danger' : 'bg-primary-soft text-primary-strong' }}" title="{{ $event->title }}">
                                                            {{ $event->title }}
                                                        </p>
                                                    @endforeach
                                                    @if (count($dayEvents) > 2)
                                                        <p class="px-1 text-[10px] font-semibold text-ink-faint">+{{ count($dayEvents) - 2 }} lagi</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.sheet>

            <!-- Daftar kegiatan + periode -->
            <div class="space-y-6">
                <x-ui.sheet :padding="false">
                    <div class="border-b border-rule/70 px-5 py-4">
                        <h2 class="text-sm font-bold tracking-tight text-ink">Kegiatan & Libur</h2>
                        <p class="mt-0.5 text-xs text-ink-faint">Bulan {{ $selectedLabel }}</p>
                    </div>

                    @if ($events->isEmpty())
                        <p class="px-5 py-8 text-center text-sm text-ink-faint">Belum ada kegiatan pada bulan ini.</p>
                    @else
                        <ul class="divide-y divide-rule/60">
                            @foreach ($events as $event)
                                <li class="px-5 py-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-ink">{{ $event->title }}</p>
                                            <p class="mt-1 text-xs text-ink-faint">
                                                {{ $event->start_date->locale('id')->isoFormat('D MMM Y') }}
                                                @if ($event->end_date && ! $event->end_date->equalTo($event->start_date))
                                                    – {{ $event->end_date->locale('id')->isoFormat('D MMM Y') }}
                                                @endif
                                            </p>
                                            @if ($event->description)
                                                <p class="mt-1 text-xs text-ink-soft">{{ $event->description }}</p>
                                            @endif
                                        </div>
                                        <x-ui.badge :variant="$event->color()" :dot="false">{{ $event->categoryLabel() }}</x-ui.badge>
                                    </div>

                                    @if ($canManage)
                                        <div class="mt-2 flex items-center gap-2">
                                            <x-ui.modal :id="'event-edit-'.$event->id" title="Ubah Kegiatan Kalender">
                                                <x-slot name="trigger">
                                                    <x-ui.button size="sm" variant="ghost" icon="pencil-square">Ubah</x-ui.button>
                                                </x-slot>

                                                <form id="event-edit-form-{{ $event->id }}" method="POST" action="{{ route('kalender.update', $event) }}" class="space-y-4">
                                                    @csrf
                                                    @method('PUT')
                                                    <x-ui.field label="Judul kegiatan" required>
                                                        <x-ui.input name="title" value="{{ $event->title }}" required />
                                                    </x-ui.field>
                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                        <x-ui.field label="Kategori" required>
                                                            <x-ui.select name="category" :options="$categories" :selected="$event->category" required />
                                                        </x-ui.field>
                                                        <x-ui.field label="Keterangan">
                                                            <x-ui.input name="description" value="{{ $event->description }}" />
                                                        </x-ui.field>
                                                    </div>
                                                    <div class="grid gap-4 sm:grid-cols-2">
                                                        <x-ui.field label="Tanggal mulai" required>
                                                            <x-ui.input type="date" name="start_date" value="{{ $event->start_date->toDateString() }}" required />
                                                        </x-ui.field>
                                                        <x-ui.field label="Tanggal selesai">
                                                            <x-ui.input type="date" name="end_date" value="{{ $event->end_date?->toDateString() }}" />
                                                        </x-ui.field>
                                                    </div>
                                                    <div class="space-y-2">
                                                        <label class="flex items-center gap-2 text-sm font-medium text-ink">
                                                            <input type="checkbox" name="is_libur" value="1" @checked($event->is_libur) class="size-4 rounded border-rule-strong text-primary focus:ring-primary">
                                                            Hitung sebagai hari libur
                                                        </label>
                                                        <label class="flex items-center gap-2 text-sm font-medium text-ink">
                                                            <input type="checkbox" name="is_efektif" value="1" @checked($event->is_efektif) class="size-4 rounded border-rule-strong text-primary focus:ring-primary">
                                                            Dihitung sebagai hari efektif pembelajaran
                                                        </label>
                                                    </div>
                                                </form>

                                                <x-slot name="footer">
                                                    <x-ui.button type="submit" form="event-edit-form-{{ $event->id }}" variant="primary">Simpan Perubahan</x-ui.button>
                                                </x-slot>
                                            </x-ui.modal>

                                            <form method="POST" action="{{ route('kalender.destroy', $event) }}" onsubmit="return confirm('Hapus kegiatan ini dari kalender?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                            </form>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.sheet>

                <x-ui.sheet>
                    <h2 class="text-sm font-bold tracking-tight text-ink">Periode Semester</h2>
                    <p class="mt-0.5 text-xs text-ink-faint">Angka dokumen kurikulum dapat disimpan sebagai pembanding hitungan otomatis.</p>

                    @foreach (['ganjil' => 'Semester Ganjil', 'genap' => 'Semester Genap'] as $semester => $label)
                        @php($period = $periods->get($semester))
                        <form method="POST" action="{{ route('kalender.periode') }}" class="mt-4 space-y-3 border-t border-rule/60 pt-4">
                            @csrf
                            <input type="hidden" name="semester" value="{{ $semester }}">
                            <p class="text-xs font-bold uppercase tracking-[0.08em] text-ink-faint">{{ $label }}</p>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <x-ui.field label="Mulai">
                                    <x-ui.input type="date" name="start_date" :value="$period?->start_date?->toDateString()" :disabled="! $canManage" />
                                </x-ui.field>
                                <x-ui.field label="Selesai">
                                    <x-ui.input type="date" name="end_date" :value="$period?->end_date?->toDateString()" :disabled="! $canManage" />
                                </x-ui.field>
                                <x-ui.field label="Pekan efektif (dokumen)">
                                    <x-ui.input type="number" name="effective_weeks" min="1" max="52" :value="$period?->effective_weeks" :disabled="! $canManage" />
                                </x-ui.field>
                                <x-ui.field label="Hari efektif (dokumen)">
                                    <x-ui.input type="number" name="effective_days" min="1" max="366" :value="$period?->effective_days" :disabled="! $canManage" />
                                </x-ui.field>
                            </div>
                            <div class="flex items-center justify-between gap-2 text-xs text-ink-faint">
                                <span>Hitungan otomatis: {{ $semesterSummary[$semester]['pekan_efektif'] }} pekan · {{ $semesterSummary[$semester]['hari_efektif'] }} hari</span>
                                @if ($canManage)
                                    <x-ui.button type="submit" size="sm" variant="secondary">Simpan</x-ui.button>
                                @endif
                            </div>
                        </form>
                    @endforeach
                </x-ui.sheet>
            </div>
        </div>
    </div>
</x-layouts.page>
