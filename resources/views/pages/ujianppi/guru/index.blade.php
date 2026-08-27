<x-layouts.page
    :title="'Beranda Guru Ujian PPI'"
    :roleLabel="'Akademik'"
    :breadcrumb="[['label' => 'Akademik', 'href' => route('dashboard')], ['label' => 'Ujian PPI', 'href' => route('ujianppi.periode.index')], ['label' => 'Beranda Guru']]"
    active-route="ujianppi.guru.index">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Beranda Guru Ujian</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Periode ujian di mana Anda terlibat sebagai <b>Penguji</b> (ruang) atau <b>Pembimbing Setoran</b>
                    (grup). Masuk sesuai penugasan Anda.
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

        @if ($periods->isEmpty())
            <div class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-12 text-center">
                <p class="text-sm font-semibold text-ink">Belum ada periode ujian yang menugaskan Anda.</p>
                <p class="mt-1 text-xs text-ink-soft">
                    Admin/Wakamad Kurikulum menetapkan penguji & pembimbing melalui menu Ruang & Penguji / Grup & Pembimbing.
                </p>
            </div>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($periods as $period)
                    @php
                        $firstParticipant = $period->participants()->first();
                    @endphp
                    <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-ink">{{ $period->judul }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <x-ui.badge variant="{{ $period->status === 'berlangsung' ? 'success' : 'warning' }}" :dot="false">{{ $period->statusLabel() }}</x-ui.badge>
                                    <x-ui.badge variant="neutral" :dot="false">{{ $period->academicYear?->name }}</x-ui.badge>
                                    @foreach ($period->peran as $peran)
                                        <x-ui.badge variant="info" :dot="false">{{ $peran }}</x-ui.badge>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                @if ($period->status === 'berlangsung')
                                    <x-ui.button variant="primary" size="sm" icon="pencil-square" href="{{ route('ujianppi.guru.ujian', $period) }}">Input Ujian</x-ui.button>
                                    <x-ui.button variant="secondary" size="sm" icon="book-open" href="{{ route('ujianppi.guru.setoran', $period) }}">Input Setoran</x-ui.button>
                                @endif
                                @if (in_array($period->status, ['berlangsung', 'selesai'], true) && $firstParticipant)
                                    <x-ui.button variant="ghost" size="sm" icon="document-text" href="{{ route('ujianppi.guru.teks', ['periode' => $period->id, 'peserta' => $firstParticipant->id]) }}">Teks & BA</x-ui.button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.page>