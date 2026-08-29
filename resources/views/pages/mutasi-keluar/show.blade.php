@php $v = fn ($x) => ($x !== null && $x !== '' ? $x : '—'); @endphp
<x-layouts.page
    :title="'Detail Mutasi Keluar'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mutasi-keluar.show">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $mutation->student->displayName() }}</h1>
                <p class="mt-1 text-sm text-ink-soft">NIS {{ $v($mutation->student->nis) }} · mutasi keluar {{ $mutation->tanggal_mutasi?->format('d/m/Y') }}</p>
            </div>
            <div class="flex gap-2">
                <x-ui.button variant="secondary" size="sm" icon="pencil-square" href="{{ route('mutasi-keluar.edit', $mutation) }}">Ubah</x-ui.button>
                <form method="POST" action="{{ route('mutasi-keluar.destroy', $mutation) }}"
                    onsubmit="return confirm('Batalkan mutasi keluar ini? Status penempatan siswa akan dikembalikan ke aktif (bila masih keluar).');">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger" size="sm" icon="trash">Batalkan</x-ui.button>
                </form>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ui.sheet title="Siswa" pinned ruled>
                <dl class="space-y-2.5">
                    @foreach ([
                        'Nama' => $mutation->student->displayName(),
                        'NIS' => $mutation->student->nis,
                        'NISN' => $mutation->student->nisn,
                        'Jenis Kelamin' => $mutation->student->person?->gender === 'P' ? 'Perempuan' : 'Laki-laki',
                        'Agama' => $v($mutation->student->person?->religion),
                    ] as $label => $value)
                        <div class="flex items-start justify-between gap-4"><dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt><dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd></div>
                    @endforeach
                </dl>
            </x-ui.sheet>

            <x-ui.sheet title="Detail Mutasi" pinned ruled>
                <dl class="space-y-2.5">
                    @foreach ([
                        'Tanggal Mutasi' => $mutation->tanggal_mutasi?->format('d/m/Y'),
                        'Sekolah Tujuan' => $mutation->sekolah_tujuan,
                        'NSM Tujuan' => $mutation->tujuan_nsm,
                        'NPSN Tujuan' => $mutation->tujuan_npsn,
                        'Alasan Pindah' => $mutation->alasanLabel(),
                        'Nomor Surat' => $mutation->no_surat,
                        'Tahun Ajaran' => $mutation->academicYear?->name,
                        'Dicatat Oleh' => $mutation->creator?->name,
                    ] as $label => $value)
                        <div class="flex items-start justify-between gap-4"><dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt><dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd></div>
                    @endforeach
                </dl>
            </x-ui.sheet>
        </div>

        @if ($mutation->keterangan)
            <div class="mt-6">
                <x-ui.sheet title="Keterangan" pinned ruled>
                    <p class="text-sm leading-relaxed text-ink">{{ $mutation->keterangan }}</p>
                </x-ui.sheet>
            </div>
        @endif

        <div class="mt-6">
            <x-ui.alert variant="info" dismissible>
                <strong class="font-bold">Catatan:</strong> siswa ini sudah dilepas dari rombel aktif tahun berjalan (status <code>keluar</code>).
                Membatalkan mutasi akan mengembalikan status penempatan aktif bila masih berstatus <code>keluar</code>.
            </x-ui.alert>
        </div>
    </div>
</x-layouts.page>