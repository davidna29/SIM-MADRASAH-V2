<x-layouts.page
    :title="'Activity & Audit Log'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="activity-log.index">

    <div class="mx-auto max-w-6xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Activity & Audit Log</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Jejak aktivitas & perubahan data seluruh modul — siapa mengubah apa, kapan. Read-only.
            </p>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <!-- Filter -->
        <form method="GET" action="{{ route('activity-log.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label for="log_name" class="block pb-1.5 text-xs font-bold text-ink">Modul / Log</label>
                    <x-ui.select name="log_name" :full="true" :options="collect($logNames)->mapWithKeys(fn ($l) => [$l => ucfirst($l)])->all()" :selected="request('log_name')" placeholder="Semua log" />
                </div>
                <div>
                    <label for="user_id" class="block pb-1.5 text-xs font-bold text-ink">Pengguna</label>
                    <x-ui.select name="user_id" :full="true" :options="$users->pluck('name', 'id')" :selected="request('user_id')" placeholder="Semua pengguna" />
                </div>
                <div>
                    <label for="q" class="block pb-1.5 text-xs font-bold text-ink">Cari</label>
                    <x-ui.input name="q" :value="request('q')" placeholder="Deskripsi…" />
                </div>
                <div>
                    <label for="from" class="block pb-1.5 text-xs font-bold text-ink">Dari</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label for="to" class="block pb-1.5 text-xs font-bold text-ink">Sampai</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <x-ui.button type="submit" variant="secondary" size="md" icon="funnel">Terapkan Filter</x-ui.button>
                @if (request()->except('page'))
                    <x-ui.button variant="ghost" size="md" href="{{ route('activity-log.index') }}">Hapus Filter</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-6">
            <x-ui.sheet title="Jejak Aktivitas" :subtitle="$activities->total() . ' catatan'" pinned ruled>
                @if ($activities->isEmpty())
                    <p class="py-8 text-center text-sm text-ink-faint">Belum ada aktivitas tercatat.</p>
                @else
                    <ol class="space-y-0">
                        @foreach ($activities as $activity)
                            @php
                                $logVariant = match ($activity->log_name) {
                                    'akademik' => 'info',
                                    'kepegawaian' => 'primary',
                                    'kesiswaan' => 'success',
                                    'keuangan' => 'warning',
                                    default => 'neutral',
                                };
                                $subjectLabel = $activity->subject_type
                                    ? \Illuminate\Support\Str::afterLast($activity->subject_type, '\\').' #'.$activity->subject_id
                                    : null;
                                $props = $activity->properties?->toArray();
                                $hasProps = ! empty($props);
                            @endphp
                            <li x-data="{ open: false }" class="flex items-start gap-3 py-3">
                                <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary/60" aria-hidden="true"></span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($activity->log_name)
                                            <x-ui.badge :variant="$logVariant" :dot="false">{{ ucfirst($activity->log_name) }}</x-ui.badge>
                                        @endif
                                        <span class="text-[13px] font-bold text-ink">{{ $activity->causer?->name ?? 'Sistem' }}</span>
                                        <span class="text-[13px] text-ink-soft">{{ \App\Support\ActivityText::readable($activity->description) }}</span>
                                        @if ($subjectLabel)
                                            <span class="tabular font-mono text-[11px] text-ink-faint">{{ $subjectLabel }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-ink-faint">
                                        <time class="tabular font-mono">{{ $activity->created_at->isoFormat('D MMM YYYY, HH:mm:ss') }}</time>
                                        @if ($hasProps)
                                            <button type="button" @click="open = !open" class="ml-2 inline-flex items-center gap-1 font-semibold text-primary hover:underline">
                                                <span x-text="open ? 'Tutup perubahan' : 'Lihat perubahan'"></span>
                                                <x-svg-chevron-down class="size-3.5" aria-hidden="true" />
                                            </button>
                                        @endif
                                    </p>
                                    @if ($hasProps)
                                        <pre x-show="open" x-cloak x-transition
                                            class="mt-2 max-h-64 overflow-auto rounded-[var(--radius-control)] bg-paper px-3 py-2.5 text-[11px] leading-relaxed text-ink-soft ring-1 ring-inset ring-rule-strong">{{ json_encode($props, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                    <div class="border-t border-rule/70 px-1 pt-3">
                        <x-ui.pagination :current="$activities->currentPage()" :last="$activities->lastPage()" />
                    </div>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
