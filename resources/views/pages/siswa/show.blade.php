<x-layouts.page
    :title="'Detail Siswa'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Detail Siswa</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Identitas, penempatan, dan riwayat kelas tercatat pada papan ini.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="pencil-square" href="{{ route('siswa.edit', $student) }}">Ubah Data</x-ui.button>
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

        <!-- Identitas -->
        <div class="mt-6">
            <x-ui.sheet title="Identitas" pinned>
                <div class="flex items-start gap-4">
                    <span class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-primary-soft text-lg font-extrabold text-primary-strong">
                        {{ mb_substr($student->displayName(), 0, 1) }}
                    </span>
                    <div>
                        <h2 class="text-lg font-extrabold tracking-tight text-ink">{{ $student->displayName() }}</h2>
                        <div class="mt-1.5 flex flex-wrap items-center gap-2">
                            @php
                                $activeEnrollment = $student->enrollments->where('status', 'aktif')->first();
                            @endphp
                            @if ($activeEnrollment?->classGroup)
                                <x-ui.badge variant="info" icon="academic-cap">{{ $activeEnrollment->classGroup->name }}</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral" :dot="false">Tanpa rombel</x-ui.badge>
                            @endif
                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                        </div>
                    </div>
                </div>
            </x-ui.sheet>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ui.sheet title="Data Siswa" pinned ruled>
                <dl class="space-y-3.5">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">NIS</dt>
                        <dd class="tabular text-right font-mono text-sm font-semibold text-ink">{{ $student->nis }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">NIK</dt>
                        <dd class="tabular text-right font-mono text-sm font-semibold text-ink">{{ $student->person?->nik ?? '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Tahun Ajaran</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ $tahun->name }} · {{ ucfirst($tahun->semester) }}</dd>
                    </div>
                </dl>
            </x-ui.sheet>

            <x-ui.sheet title="Data Pribadi & Kontak" pinned ruled>
                <dl class="space-y-3.5">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Tempat, Tanggal Lahir</dt>
                        <dd class="text-right text-sm font-semibold text-ink">
                            {{ collect([$student->person?->birth_place, $student->person?->birth_date?->format('d/m/Y')])->filter()->implode(', ') ?: '—' }}
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Jenis Kelamin</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ $student->person?->gender === 'P' ? 'Perempuan' : 'Laki-laki' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Agama</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ $student->person?->religion ?? '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Nomor HP</dt>
                        <dd class="tabular text-right font-mono text-sm font-semibold text-ink">{{ $student->person?->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Email</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ $student->person?->email ?? '—' }}</dd>
                    </div>
                </dl>
            </x-ui.sheet>
        </div>

        <!-- Riwayat penempatan -->
        <div class="mt-6">
            <x-ui.sheet title="Riwayat Kelas" subtitle="Penempatan per tahun ajaran — tidak pernah ditimpa" pinned ruled>
                <ol class="space-y-0">
                    @forelse ($student->enrollments as $enrollment)
                        <li class="flex items-start gap-3 py-2.5">
                            <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary/60" aria-hidden="true"></span>
                            <div class="min-w-0 flex-1 text-[13px] leading-relaxed">
                                <span class="font-bold text-ink">Kelas {{ $enrollment->classGroup?->name }}</span>
                                <span class="text-ink-soft"> · {{ $enrollment->academicYear?->name }}</span>
                            </div>
                            <x-ui.badge :variant="$enrollment->status === 'aktif' ? 'success' : 'neutral'">{{ $enrollment->status }}</x-ui.badge>
                        </li>
                    @empty
                        <li class="py-2 text-xs text-ink-faint">Belum ada riwayat penempatan.</li>
                    @endforelse
                </ol>
            </x-ui.sheet>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-rule-strong/60 pt-5">
            <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('siswa.index') }}">Kembali</x-ui.button>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('siswa.destroy', $student) }}" onsubmit="return confirm('Hapus data siswa ini?');">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger" icon="trash">Hapus</x-ui.button>
                </form>
                <x-ui.button variant="primary" icon="pencil-square" href="{{ route('siswa.edit', $student) }}">Ubah Data</x-ui.button>
            </div>
        </div>
    </div>
</x-layouts.page>
