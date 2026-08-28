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

        <!-- Data asal PPDB -->
        @if ($student->ppdbRegistration)
            <div class="mt-6">
                <x-ui.sheet title="Data Asal PPDB" pinned ruled>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-ink-faint">No. Registrasi</p>
                            <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ $student->ppdbRegistration->registration_no }}</p>
                            <p class="mt-1 text-xs text-ink-soft">Status: {{ ucfirst($student->ppdbRegistration->status) }}</p>
                        </div>
                        @can('viewAny', \App\Models\PpdbRegistration::class)
                            <x-ui.button variant="secondary" size="sm" icon="arrow-top-right-on-square" href="{{ route('ppdb.show', $student->ppdbRegistration) }}">Lihat Detail</x-ui.button>
                        @endcan
                    </div>
                </x-ui.sheet>
            </div>
        @endif

        <!-- Data lengkap (dari master) -->
        @php
            $ayahG = $student->guardianByRelation('ayah');
            $ibuG = $student->guardianByRelation('ibu');
            $waliG = $student->guardianByRelation('wali');
        @endphp
        @if (collect([
                $student->nisn, $student->previous_school, $student->origin_school,
                $student->entry_date, $student->hobby, $student->child_order, $student->kk_number,
                $ayahG?->name, $ibuG?->name, $waliG?->name,
                $student->person?->address,
            ])->filter()->isNotEmpty())
            @php
                $v = fn ($x) => ($x !== null && $x !== '' ? $x : '—');
                $person = $student->person;
                $identityRows = [
                    'NISN' => $student->nisn,
                    'Sekolah Sebelumnya' => $student->previous_school,
                    'Asal (NSM/NPSN)' => collect([$student->origin_school, $student->origin_nsm, $student->origin_npsn])->filter()->implode(' · '),
                    'Alamat Asal' => $student->origin_address,
                    'Tanggal Masuk' => $student->entry_date?->format('d/m/Y'),
                    'Anak Ke' => $student->child_order,
                    'Jumlah Saudara' => $student->sibling_count,
                    'Pernah TK' => $student->ever_tk,
                    'Pernah PAUD' => $student->ever_paud,
                    'Hobi' => $student->hobby,
                    'Cita-cita' => $student->ambition,
                ];
                $addressRows = [
                    'Tempat Tinggal' => $student->residence_type,
                    'Alamat' => collect([$person?->address, $person?->village, $person?->district, $person?->city, $person?->province])->filter()->implode(', '),
                    'RT / RW' => collect([$person?->rt, $person?->rw])->filter()->implode(' / '),
                    'Kode Pos' => $person?->postal_code,
                    'Jarak' => $student->distance,
                    'Transportasi' => $student->transport,
                    'Waktu Tempuh' => $student->commute_time,
                    'Telepon Rumah' => $person?->home_phone,
                ];
                $guardianRow = function (?\App\Models\Guardian $g) {
                    if (! $g) {
                        return null;
                    }
                    return collect([$g->name, $g->job, $g->phone])->filter()->implode(' · ');
                };
                $guardianIdRow = function (?\App\Models\Guardian $g) {
                    if (! $g) {
                        return null;
                    }
                    return collect([$g->nik ? 'NIK '.$g->nik : null, $g->education, $g->income, $g->status])->filter()->implode(' · ');
                };
                $familyRows = [
                    'No. KK' => $student->kk_number,
                    'Kepala Keluarga' => $student->kk_head_name,
                    'Ayah' => $guardianRow($ayahG),
                    'Ayah (identitas)' => $guardianIdRow($ayahG),
                    'Ibu' => $guardianRow($ibuG),
                    'Ibu (identitas)' => $guardianIdRow($ibuG),
                ];
                if ($waliG) {
                    $familyRows['Wali'] = $guardianRow($waliG);
                    $familyRows['Wali (identitas)'] = $guardianIdRow($waliG);
                }
                $socialRows = ['KKS' => $student->social_kks, 'PKH' => $student->social_pkh, 'KIP' => $student->social_kip];
                $parentAddrRows = [
                    'Status Rumah' => $student->parent_ownership,
                    'Alamat' => collect([$student->parent_address, $student->parent_village, $student->parent_district, $student->parent_city, $student->parent_province])->filter()->implode(', '),
                    'RT / RW' => collect([$student->parent_rt, $student->parent_rw])->filter()->implode(' / '),
                    'Kode Pos' => $student->parent_postal_code,
                ];
                $immLabels = ['imm_hepb' => 'Hepatitis B', 'imm_polio' => 'Polio', 'imm_bcg' => 'BCG', 'imm_campak' => 'Campak', 'imm_dpt' => 'DPT-HB-HiB', 'imm_covid' => 'COVID'];
                $disLabels = [
                    'dis_deaf' => 'Tuna Rungu', 'dis_blind' => 'Tuna Netra', 'dis_disabled' => 'Tuna Daksa',
                    'dis_intellectual' => 'Tuna Grahita', 'dis_behavioral' => 'Tuna Laras', 'dis_slow_learner' => 'Lamban Belajar',
                    'dis_communication' => 'Gangguan Komunikasi', 'dis_gifted' => 'Bakat Luar Biasa',
                ];
                $imms = collect($immLabels)->filter(fn ($l, $k) => $student->{$k})->values()->all();
                $diss = collect($disLabels)->filter(fn ($l, $k) => $student->{$k})->values()->all();
            @endphp
            <div class="mt-6">
                <x-ui.sheet title="Data Lengkap" subtitle="Data lengkap tersimpan di master — dapat diubah di menu Data Siswa" pinned ruled>
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div>
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-ink-faint">Identitas & Asal</p>
                            <dl class="space-y-2.5">
                                @foreach ($identityRows as $label => $value)
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt>
                                        <dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                        <div>
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-ink-faint">Alamat Siswa</p>
                            <dl class="space-y-2.5">
                                @foreach ($addressRows as $label => $value)
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt>
                                        <dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="mb-2 text-xs font-bold uppercase tracking-wide text-ink-faint">Keluarga</p>
                        <dl class="space-y-2.5">
                            @foreach ($familyRows as $label => $value)
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt>
                                    <dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div>
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-ink-faint">Kesehatan & Imunisasi</p>
                            @if ($imms)
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($imms as $imm) <x-ui.badge variant="success">{{ $imm }}</x-ui.badge> @endforeach
                                </div>
                            @else
                                <p class="text-sm text-ink-faint">Tidak ada imunisasi tercatat.</p>
                            @endif

                            <p class="mb-2 mt-5 text-xs font-bold uppercase tracking-wide text-ink-faint">Kebutuhan Khusus</p>
                            @if ($diss)
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($diss as $dis) <x-ui.badge variant="warning">{{ $dis }}</x-ui.badge> @endforeach
                                </div>
                            @else
                                <p class="text-sm text-ink-faint">Tidak ada.</p>
                            @endif
                        </div>
                        <div>
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-ink-faint">Bantuan Sosial</p>
                            <dl class="space-y-2.5">
                                @foreach ($socialRows as $label => $value)
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt>
                                        <dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                            <p class="mb-2 mt-5 text-xs font-bold uppercase tracking-wide text-ink-faint">Alamat Orang Tua</p>
                            <dl class="space-y-2.5">
                                @foreach ($parentAddrRows as $label => $value)
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt>
                                        <dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>

                    @if (! empty($student->documents))
                        <div class="mt-6">
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-ink-faint">Dokumen</p>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @foreach (['kk' => 'Kartu Keluarga', 'kk_wali' => 'KK Wali', 'akta' => 'Akta Kelahiran', 'ijazah' => 'Ijazah / SKL', 'photo' => 'Foto'] as $key => $label)
                                    @if (! empty($student->documents[$key]))
                                        <a href="{{ $student->documents[$key] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-[var(--radius-control)] bg-paper px-3 py-2 text-xs font-semibold text-primary ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep">
                                            <x-dynamic-component :component="'svg-document-text'" class="size-4" /> {{ $label }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-ui.sheet>
            </div>
        @endif

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
