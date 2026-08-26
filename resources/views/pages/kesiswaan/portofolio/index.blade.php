<x-layouts.page title="Portofolio Digital" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    <div class="mx-auto max-w-3xl">
        {{-- Pencarian --}}
        <x-ui.sheet title="Cari Siswa" subtitle="Masukkan NIS atau nama siswa untuk melihat portofolio" :pinned="true">
            <form method="GET" action="{{ route('portofolio.index') }}">
                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <x-ui.field label="NIS / Nama Siswa">
                            <x-ui.input name="q" value="{{ $search }}" placeholder="Contoh: 240101 atau Aisyah" autofocus />
                        </x-ui.field>
                    </div>
                    <x-ui.button type="submit" variant="primary" icon="magnifying-glass">Cari</x-ui.button>
                </div>
            </form>
        </x-ui.sheet>

        {{-- Hasil Pencarian --}}
        @if ($search)
            <div class="mt-6">
                <x-ui.sheet :title="'Hasil Pencarian'" subtitle="{{ $students->count() }} ditemukan" :pinned="true" :padding="false">
                    @if ($students->isEmpty())
                        <div class="flex flex-col items-center gap-3 px-6 py-12 text-center">
                            <x-svg-user class="size-12 text-ink-faint" />
                            <div>
                                <p class="font-semibold text-ink">Tidak ada siswa ditemukan</p>
                                <p class="mt-1 text-sm text-ink-soft">Coba kata kunci lain.</p>
                            </div>
                        </div>
                    @else
                        <ul class="divide-y divide-rule/70">
                            @foreach ($students as $siswa)
                                @php
                                    $enrollment = $siswa->enrollments->first();
                                    $kelas = $enrollment?->classGroup?->name ?? '–';
                                @endphp
                                <li class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-paper/60">
                                    <div class="flex items-center gap-4">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-sm font-bold text-primary-strong">
                                            {{ mb_strtoupper(mb_substr($siswa->name, 0, 2)) }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-bold text-ink">{{ $siswa->name }}</p>
                                            <p class="text-xs text-ink-soft">NIS: {{ $siswa->nis }} · Kelas: {{ $kelas }}</p>
                                        </div>
                                    </div>
                                    <x-ui.button size="sm" variant="secondary" icon="document-arrow-up" href="{{ route('portofolio.show', $siswa) }}">Lihat Portofolio</x-ui.button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.sheet>
            </div>
        @endif
    </div>
</x-layouts.page>
