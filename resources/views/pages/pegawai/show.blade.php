<x-layouts.page
    :title="'Detail Pegawai'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Detail Pegawai</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Identitas, kepegawaian, dan riwayat jabatan tercatat pada papan ini.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="pencil-square" href="{{ route('pegawai.edit', $employee) }}">Ubah Data</x-ui.button>
                <x-ui.button variant="primary" icon="arrow-down-tray">Cetak Kartu</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if (session('warning'))
            <div class="mt-6">
                <x-ui.alert variant="warning" dismissible>
                    <strong class="font-bold">Perhatian:</strong> {{ session('warning') }}
                </x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>{{ $errors->first() }}</x-ui.alert>
            </div>
        @endif

        <!-- Identitas -->
        <div class="mt-6">
            <x-ui.sheet title="Identitas" pinned>
                <div class="flex items-start gap-4">
                    <span class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-primary-soft text-lg font-extrabold text-primary-strong">
                        {{ mb_substr($employee->person->name, 0, 1) }}
                    </span>
                    <div>
                        <h2 class="text-lg font-extrabold tracking-tight text-ink">{{ $employee->person->name }}</h2>
                        <div class="mt-1.5 flex flex-wrap items-center gap-2">
                            <x-ui.badge variant="primary" :dot="false">{{ $employee->positionLabel() }}</x-ui.badge>
                            @php
                                $st = match ($employee->status) {
                                    'aktif' => ['Aktif', 'success'],
                                    'cuti' => ['Cuti', 'warning'],
                                    default => ['Nonaktif', 'danger'],
                                };
                            @endphp
                            <x-ui.badge :variant="$st[1]">{{ $st[0] }}</x-ui.badge>
                        </div>
                    </div>
                </div>
            </x-ui.sheet>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ui.sheet title="Data Kepegawaian" pinned ruled>
                <dl class="space-y-3.5">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">NIP</dt>
                        <dd class="tabular text-right font-mono text-sm font-semibold text-ink">{{ $employee->nip ?? '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">NIK</dt>
                        <dd class="tabular text-right font-mono text-sm font-semibold text-ink">{{ $employee->person->nik }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Status Pegawai</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ strtoupper($employee->employee_status) }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">TMT</dt>
                        <dd class="tabular text-right font-mono text-sm font-semibold text-ink">{{ $employee->tmt?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Unit Kerja</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ $employee->organizationalUnit?->name }}</dd>
                    </div>
                    @if ($employee->user)
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Akun</dt>
                            <dd class="text-right text-sm font-semibold text-ink">
                                {{ $employee->user->username }}
                                @unless ($employee->user->is_active)
                                    <x-ui.badge variant="danger" :dot="false">Nonaktif</x-ui.badge>
                                @endunless
                            </dd>
                        </div>
                    @endif
                </dl>
            </x-ui.sheet>

            <x-ui.sheet title="Data Pribadi & Kontak" pinned ruled>
                <dl class="space-y-3.5">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Tempat, Tanggal Lahir</dt>
                        <dd class="text-right text-sm font-semibold text-ink">
                            {{ collect([$employee->person->birth_place, $employee->person->birth_date?->format('d/m/Y')])->filter()->implode(', ') ?: '—' }}
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Jenis Kelamin</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ $employee->person->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Agama</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ $employee->person->religion }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Nomor HP</dt>
                        <dd class="tabular text-right font-mono text-sm font-semibold text-ink">{{ $employee->person->phone ?? '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Email</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ $employee->person->email ?? '—' }}</dd>
                    </div>
                </dl>
            </x-ui.sheet>
        </div>

        <!-- Riwayat jabatan -->
        <div class="mt-6">
            <x-ui.sheet title="Riwayat Jabatan" subtitle="Pengangkatan dan mutasi tercatat sebagai lembar terpisah — tidak pernah ditimpa" pinned ruled>
                <ol class="space-y-0">
                    @forelse ($employee->positionHistories as $h)
                        <li class="flex items-start gap-3 py-2.5">
                            <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary/60" aria-hidden="true"></span>
                            <div class="min-w-0 flex-1 text-[13px] leading-relaxed">
                                <span class="font-bold text-ink">{{ $h->position?->name }}</span>
                                <span class="text-ink-soft"> · {{ $h->organizationalUnit?->name }}</span>
                            </div>
                            <time class="tabular shrink-0 font-mono text-xs text-ink-faint">
                                {{ $h->started_at->format('d M Y') }}{{ $h->ended_at ? ' — '.$h->ended_at->format('d M Y') : '' }}
                            </time>
                        </li>
                    @empty
                        <li class="py-2 text-xs text-ink-faint">Belum ada riwayat jabatan.</li>
                    @endforelse
                </ol>
            </x-ui.sheet>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-rule-strong/60 pt-5">
            <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('pegawai.index') }}">Kembali</x-ui.button>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('pegawai.destroy', $employee) }}" onsubmit="return confirm('Hapus data pegawai ini? (soft delete)');">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger" icon="trash">Hapus</x-ui.button>
                </form>
                <x-ui.button variant="primary" icon="pencil-square" href="{{ route('pegawai.edit', $employee) }}">Ubah Data</x-ui.button>
            </div>
        </div>
    </div>
</x-layouts.page>
