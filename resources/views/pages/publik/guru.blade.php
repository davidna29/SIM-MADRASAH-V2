<x-layouts.publik :title="'Guru & Tenaga Kependidikan'" :description="'Kenali guru dan tenaga kependidikan yang memberi izin untuk diperkenalkan di website.'">
    <div class="mx-auto max-w-6xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Profil', 'href' => route('publik.profil')], ['label' => 'Guru & Tenaga Kependidikan']]" />
        <header class="mt-8 max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Guru & Tenaga Kependidikan</h1>
            <p class="mt-3 text-base leading-relaxed text-ink-soft">Kenali para pendidik dan tenaga kependidikan yang berperan dalam kegiatan belajar di madrasah.</p>
        </header>

        @if ($employees->isNotEmpty())
            <ul class="mt-8 grid grid-cols-2 gap-4 sm:gap-5 lg:grid-cols-4">
                @foreach ($employees as $employee)
                    <li class="group overflow-hidden rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                        <div class="relative aspect-square overflow-hidden bg-paper-deep">
                            @if ($employee->photoUrl())
                                <img src="{{ $employee->photoUrl() }}" alt="Foto {{ $employee->person?->name }}"
                                    class="size-full object-cover transition duration-500 group-hover:scale-[1.02]" loading="lazy" decoding="async">
                            @else
                                <span class="flex size-full items-center justify-center bg-board text-3xl font-extrabold text-board-ink" aria-hidden="true">
                                    {{ mb_substr($employee->person?->name ?? '?', 0, 1) }}
                                </span>
                            @endif
                            <span class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-board-deep/55 to-transparent" aria-hidden="true"></span>
                        </div>
                        <div class="p-4">
                            <h2 class="text-sm font-extrabold leading-snug text-ink">{{ $employee->person?->name }}</h2>
                            <p class="mt-1 text-xs font-semibold text-primary-strong">{{ $employee->position?->name ?: 'Tenaga Kependidikan' }}</p>
                            @if ($employee->organizationalUnit?->name)
                                <p class="mt-1 text-xs text-ink-faint">{{ $employee->organizationalUnit->name }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="mt-8 rounded-sheet bg-sheet p-7 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="font-bold text-ink">Informasi tenaga pendidik sedang disiapkan.</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Nama hanya akan ditampilkan apabila status pegawai aktif dan izin publikasi diaktifkan oleh pengelola.</p>
            </div>
        @endif
    </div>
</x-layouts.publik>
