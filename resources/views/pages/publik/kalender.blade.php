<x-layouts.publik :title="'Kalender Pendidikan'" :description="'Kalender pendidikan, hari efektif, dan jadwal libur madrasah tahun ajaran berjalan.'">
    <div class="mx-auto max-w-6xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Kalender Pendidikan']]" />

        <header class="mt-8 border-b border-rule-strong/70 pb-6">
            <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Kalender Pendidikan</h1>
            <p class="mt-2 max-w-prose text-sm leading-relaxed text-ink-soft">
                Tahun Ajaran {{ $tahun->name }} — kegiatan madrasah, hari libur, asesmen, dan pembagian rapor.
            </p>
        </header>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            @foreach (['ganjil' => 'Semester Ganjil', 'genap' => 'Semester Genap'] as $semester => $label)
                @php($period = $periods->get($semester))
                @if ($period)
                    <div class="rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60">
                        <p class="text-xs font-bold uppercase tracking-[0.08em] text-ink-faint">{{ $label }}</p>
                        <p class="mt-2 text-sm font-semibold text-ink">
                            {{ $period->start_date->isoFormat('D MMMM Y') }} – {{ $period->end_date->isoFormat('D MMMM Y') }}
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <x-ui.badge variant="primary" :dot="false">{{ $period->effective_weeks ?? '—' }} pekan efektif</x-ui.badge>
                            <x-ui.badge variant="success" :dot="false">{{ $period->effective_days ?? '—' }} hari efektif</x-ui.badge>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-8 space-y-8">
            @foreach ($months as $month)
                <section class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-rule/70 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-extrabold tracking-tight text-ink">{{ $month['summary']['label'] }}</h2>
                            <p class="mt-0.5 text-xs text-ink-faint">{{ ucfirst($month['semester']) }} · {{ $month['summary']['pekan_efektif'] }} pekan efektif · {{ $month['summary']['hari_efektif'] }} hari efektif</p>
                        </div>
                        <x-ui.badge variant="info" :dot="false">{{ $month['events']->count() }} kegiatan</x-ui.badge>
                    </div>

                    @if ($month['events']->isEmpty())
                        <p class="px-5 py-6 text-sm text-ink-faint">Belum ada kegiatan pada bulan ini.</p>
                    @else
                        <ul class="divide-y divide-rule/60">
                            @foreach ($month['events'] as $event)
                                <li class="flex flex-wrap items-start gap-4 px-5 py-4">
                                    <div class="w-16 shrink-0 rounded-control bg-board px-2 py-2 text-center text-board-ink">
                                        <span class="block font-mono text-xl font-extrabold leading-none tabular">{{ $event->start_date->format('d') }}</span>
                                        <span class="mt-1 block text-[10px] font-bold uppercase">{{ $event->start_date->locale('id')->isoFormat('MMM') }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-sm font-bold text-ink">{{ $event->title }}</h3>
                                            <x-ui.badge :variant="$event->color()" :dot="false">{{ $event->categoryLabel() }}</x-ui.badge>
                                        </div>
                                        <p class="mt-1 text-xs text-ink-faint">
                                            {{ $event->start_date->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                            @if ($event->end_date && ! $event->end_date->equalTo($event->start_date))
                                                – {{ $event->end_date->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                            @endif
                                        </p>
                                        @if ($event->description)
                                            <p class="mt-1 text-sm text-ink-soft">{{ $event->description }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.publik>
