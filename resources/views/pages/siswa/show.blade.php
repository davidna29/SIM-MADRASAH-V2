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

        <!-- Data lengkap (snapshot PPDB) -->
        @if ($student->profile)
            @php
                $p = $student->profile;
                $v = fn ($x) => ($x !== null && $x !== '' ? $x : '—');
                $identityRows = [
                    'NISN' => $p->nisn,
                    'Sekolah Asal' => $p->previous_school,
                    'Asal (NSM/NPSN)' => collect([$p->origin_school, $p->origin_nsm, $p->origin_npsn])->filter()->implode(' · '),
                    'Alamat Asal' => $p->origin_address,
                    'Tanggal Masuk' => $p->entry_date?->format('d/m/Y'),
                    'Anak Ke' => $p->child_order,
                    'Jumlah Saudara' => $p->sibling_count,
                    'Pernah TK' => $p->ever_tk,
                    'Pernah PAUD' => $p->ever_paud,
                    'Hobi' => $p->hobby,
                    'Cita-cita' => $p->ambition,
                ];
                $addressRows = [
                    'Tempat Tinggal' => $p->residence_type,
                    'Alamat' => collect([$p->address, $p->village, $p->district, $p->city, $p->province])->filter()->implode(', '),
                    'RT / RW' => collect([$p->rt, $p->rw])->filter()->implode(' / '),
                    'Kode Pos' => $p->postal_code,
                    'Jarak' => $p->distance,
                    'Transportasi' => $p->transport,
                    'Waktu Tempuh' => $p->commute_time,
                    'Telepon Rumah' => $p->home_phone,
                ];
                $familyRows = [
                    'No. KK' => $p->kk_number,
                    'Kepala Keluarga' => $p->kk_head_name,
                    'Ayah' => collect([$p->father_name, $p->father_job, $p->father_phone])->filter()->implode(' · '),
                    'Ayah (identitas)' => collect([$p->father_nik ? 'NIK '.$p->father_nik : null, $p->father_education, $p->father_income, $p->father_status])->filter()->implode(' · '),
                    'Ibu' => collect([$p->mother_name, $p->mother_job, $p->mother_phone])->filter()->implode(' · '),
                    'Ibu (identitas)' => collect([$p->mother_nik ? 'NIK '.$p->mother_nik : null, $p->mother_education, $p->mother_income, $p->mother_status])->filter()->implode(' · '),
                ];
                if ($p->guardian_name) {
                    $familyRows['Wali'] = collect([$p->guardian_name, $p->guardian_job, $p->guardian_phone])->filter()->implode(' · ');
                    $familyRows['Wali (identitas)'] = collect([$p->guardian_nik ? 'NIK '.$p->guardian_nik : null, $p->guardian_education, $p->guardian_income])->filter()->implode(' · ');
                }
                $socialRows = ['KKS' => $p->social_kks, 'PKH' => $p->social_pkh, 'KIP' => $p->social_kip];
                $parentAddrRows = [
                    'Status Rumah' => $p->parent_ownership,
                    'Alamat' => collect([$p->parent_address, $p->parent_village, $p->parent_district, $p->parent_city, $p->parent_province])->filter()->implode(', '),
                    'RT / RW' => collect([$p->parent_rt, $p->parent_rw])->filter()->implode(' / '),
                    'Kode Pos' => $p->parent_postal_code,
                ];
            @endphp
            <div class="mt-6">
                <x-ui.sheet title="Data Lengkap" subtitle="Snapshot dari pendaftaran PPDB — tidak hilang saat menjadi siswa" pinned ruled>
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
                            @php $imms = $p->immunizationsDone(); @endphp
                            @if ($imms)
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($imms as $imm) <x-ui.badge variant="success">{{ $imm }}</x-ui.badge> @endforeach
                                </div>
                            @else
                                <p class="text-sm text-ink-faint">Tidak ada imunisasi tercatat.</p>
                            @endif

                            <p class="mb-2 mt-5 text-xs font-bold uppercase tracking-wide text-ink-faint">Kebutuhan Khusus</p>
                            @php $diss = $p->disabilitiesList(); @endphp
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

                    @if ($p->documents)
                        <div class="mt-6">
                            <p class="mb-2 text-xs font-bold uppercase tracking-wide text-ink-faint">Dokumen</p>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @foreach (['kk' => 'Kartu Keluarga', 'kk_wali' => 'KK Wali', 'akta' => 'Akta Kelahiran', 'ijazah' => 'Ijazah / SKL', 'photo' => 'Foto'] as $key => $label)
                                    @if (! empty($p->documents[$key]))
                                        <a href="{{ $p->documents[$key] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-[var(--radius-control)] bg-paper px-3 py-2 text-xs font-semibold text-primary ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep">
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
