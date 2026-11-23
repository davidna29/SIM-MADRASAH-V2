<x-layouts.publik :title="'Struktur Organisasi'" :description="'Struktur organisasi dan penanggung jawab yang diizinkan untuk ditampilkan.'">
    <div class="mx-auto max-w-6xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Profil', 'href' => route('publik.profil')], ['label' => 'Struktur Organisasi']]" />
        <header class="mt-8 max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Struktur Organisasi</h1>
            <p class="mt-3 text-base leading-relaxed text-ink-soft">Pengenalan peran dan unit kerja madrasah berdasarkan informasi yang diizinkan untuk dipublikasikan.</p>
        </header>

        @forelse ($units as $unitName => $employees)
            <section class="mt-10" aria-labelledby="unit-{{ $loop->index }}">
                <div class="flex items-end justify-between gap-4 border-b border-rule-strong/70 pb-3">
                    <h2 id="unit-{{ $loop->index }}" class="text-xl font-extrabold tracking-tight text-ink">{{ $unitName }}</h2>
                    <span class="font-mono text-xs text-ink-faint">{{ $employees->count() }} orang</span>
                </div>
                <ul class="mt-2 divide-y divide-rule/70">
                    @foreach ($employees as $employee)
                        <li class="flex items-center gap-4 py-4">
                            @if ($employee->photoUrl())
                                <img src="{{ $employee->photoUrl() }}" alt="{{ $employee->person?->name }}" class="size-12 shrink-0 rounded-full object-cover ring-2 ring-sheet shadow-sm">
                            @else
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-primary-soft text-base font-extrabold text-primary-strong" aria-hidden="true">{{ mb_substr($employee->person?->name ?? '?', 0, 1) }}</span>
                            @endif
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-ink">{{ $employee->person?->name }}</p>
                                <p class="mt-1 text-sm text-ink-soft">{{ $employee->position?->name ?: 'Tenaga Kependidikan' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @empty
            <div class="mt-8 rounded-sheet bg-sheet p-7 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="font-bold text-ink">Informasi struktur belum tersedia.</p>
                <p class="mt-2 text-sm leading-relaxed text-ink-soft">Nama pegawai hanya ditampilkan setelah pengelola memberi izin publikasi pada data masing-masing.</p>
            </div>
        @endforelse
    </div>
</x-layouts.publik>
