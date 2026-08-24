<x-layouts.page
    :title="'Jurnal Mengajar'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="guru.jurnal.index">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Jurnal Mengajar</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Catat pelaksanaan pembelajaran per penugasan pada Tahun Ajaran {{ $tahun->name }}.
                    Jurnal yang disematkan menjadi bahan pemantauan wakamad kurikulum.
                </p>
            </div>
            <x-ui.badge variant="info" icon="calendar-days">{{ $tahun->name }} · Semester {{ ucfirst($tahun->semester) }}</x-ui.badge>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($assignments as $assignment)
                <x-ui.sheet
                    :title="$assignment->subject->name"
                    :subtitle="'Kelas ' . $assignment->classGroup->name . ' · Kode ' . $assignment->subject->code"
                    pinned
                    :actions="view('components.ui.button', ['variant' => 'primary', 'size' => 'sm', 'icon' => 'clipboard-document-list', 'href' => route('guru.jurnal.show', $assignment)])->withSlot('Buka Jurnal')->render()">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.badge variant="info" icon="academic-cap">{{ $assignment->journals_count }} jurnal tercatat</x-ui.badge>
                            @if ($assignment->journals_count === 0)
                                <x-ui.badge variant="warning" icon="exclamation-triangle">Belum ada catatan</x-ui.badge>
                            @else
                                <x-ui.badge variant="success" icon="check">Sudah ada catatan</x-ui.badge>
                            @endif
                        </div>
                    </div>
                </x-ui.sheet>
            @empty
                <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center">
                    <p class="text-sm font-semibold text-ink">Belum ada penugasan untuk Anda.</p>
                    <p class="mt-1 text-xs text-ink-faint">Hubungi admin madrasah untuk membuat penugasan mengajar.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.page>
