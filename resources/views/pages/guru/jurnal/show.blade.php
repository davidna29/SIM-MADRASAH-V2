<x-layouts.page
    :title="'Jurnal Mengajar'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="guru.jurnal.show">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $assignment->subject->name }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Jurnal mengajar untuk kelas {{ $assignment->classGroup->name }}
                    · Semester {{ ucfirst($tahun->semester) }} {{ $tahun->name }}.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.badge variant="info" icon="building-library">{{ $assignment->classGroup->name }}</x-ui.badge>
                <x-ui.badge variant="primary" icon="book-open">{{ $assignment->subject->code }}</x-ui.badge>
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

        <div class="mt-6">
            @include('pages.guru.jurnal._form', ['assignment' => $assignment])
        </div>

        <div class="mt-6">
            <x-ui.sheet title="Riwayat Jurnal" :subtitle="$journals->total() . ' catatan' . ($journals->total() > 0 ? ' — draf belum terhitung sebagai pengisian penuh' : '')" pinned ruled>
                @if ($journals->isEmpty())
                    <div class="py-8 text-center">
                        <p class="text-sm font-semibold text-ink">Belum ada jurnal untuk penugasan ini.</p>
                        <p class="mt-1 text-xs text-ink-faint">Gunakan form di atas untuk mencatat pembelajaran pertama Anda.</p>
                    </div>
                @else
                    <ul class="divide-y divide-rule/70">
                        @foreach ($journals as $journal)
                            <li class="flex flex-wrap items-start justify-between gap-3 py-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="tabular text-[13px] font-bold text-ink">
                                            {{ $journal->journal_date->isoFormat('dddd, D MMM YYYY') }}
                                            @if ($journal->period_no)
                                                <span class="font-mono text-xs text-ink-faint">· jam ke-{{ $journal->period_no }}</span>
                                            @endif
                                        </p>
                                        @if ($journal->status === 'terisi')
                                            <x-ui.badge variant="success" icon="check">Terisi</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="warning" icon="document-text">Draf</x-ui.badge>
                                        @endif
                                    </div>
                                    <p class="mt-1.5 text-sm font-semibold leading-relaxed text-ink">{{ $journal->materi }}</p>
                                    @if ($journal->catatan)
                                        <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-ink-soft">{{ $journal->catatan }}</p>
                                    @endif
                                    @if ($journal->lampiran)
                                        <p class="mt-1.5">
                                            <a href="{{ route('guru.jurnal.lampiran', [$assignment, $journal]) }}"
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-primary underline-offset-2 hover:underline">
                                                <x-svg-paper-clip class="size-3.5" aria-hidden="true" />
                                                {{ basename($journal->lampiran) }}
                                            </a>
                                        </p>
                                    @endif
                                </div>
                                <div class="flex shrink-0 items-center gap-1.5">
                                    <x-ui.button size="sm" variant="secondary" icon="pencil-square" href="{{ route('guru.jurnal.edit', [$assignment, $journal]) }}">Ubah</x-ui.button>
                                    <form method="POST" action="{{ route('guru.jurnal.destroy', $journal) }}" onsubmit="return confirm('Hapus jurnal tanggal {{ $journal->journal_date->isoFormat('D MMM YYYY') }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" size="sm" variant="ghost" icon="trash">Hapus</x-ui.button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="border-t border-rule/70 px-1 py-3">
                        <x-ui.pagination :current="$journals->currentPage()" :last="$journals->lastPage()" />
                    </div>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
