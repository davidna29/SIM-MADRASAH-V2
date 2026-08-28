@php $v = fn ($x) => ($x !== null && $x !== '' ? $x : '—'); @endphp
<x-layouts.page
    :title="'Detail Pendaftar Pindah'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mutasi.show">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ strtoupper($registration->name) }}</h1>
                <p class="mt-1 text-sm text-ink-soft">{{ $registration->registration_no }} · {{ $registration->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2" x-data="{ open: false, acceptOpen: false }">
                @if ($registration->status === 'accepted')
                    <span class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-paper-deep px-3 py-2 text-xs font-semibold text-ink-soft">
                        <x-svg-lock-closed class="size-3.5" /> Terkunci (Master Data Siswa)
                    </span>
                @else
                    <a href="{{ route('mutasi.edit', $registration) }}"
                        class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-sheet px-3 py-2 text-xs font-semibold text-ink ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep">
                        <x-svg-pencil-square class="size-3.5" /> Edit
                    </a>
                @endif
                @if ($registration->status === 'submitted')
                    <button type="button" @click="acceptOpen = true" class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-success px-3 py-2 text-xs font-semibold text-white transition hover:brightness-95">
                        <x-svg-check-circle class="size-3.5" /> Terima
                    </button>
                    <button type="button" @click="open = true" class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-danger px-3 py-2 text-xs font-semibold text-white transition hover:brightness-95">
                        <x-svg-x-circle class="size-3.5" /> Tolak
                    </button>
                    {{-- Accept Modal --}}
                    <div x-show="acceptOpen" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" x-transition @keydown.escape.window="acceptOpen = false" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]"></div>
                        <div class="relative w-full max-w-lg rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule">
                            <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                                <h3 class="text-sm font-bold text-ink">Terima Pendaftar Pindah</h3>
                                <button type="button" @click="acceptOpen = false" class="rounded-md p-1 text-ink-faint hover:bg-paper-deep hover:text-ink"><x-svg-x-mark class="size-5" /></button>
                            </header>
                            <div class="px-5 py-5 text-sm leading-relaxed text-ink">
                                <p class="font-semibold">Menerima pendaftar ini akan <span class="text-danger font-extrabold">MENGUNCI seluruh pengeditan di modul mutasi</span>.</p>
                                <ul class="mt-3 list-disc list-inside space-y-1.5 text-ink-soft">
                                    <li>Seluruh data disalin <strong>persis</strong> ke Master Data Siswa.</li>
                                    <li>Edit di halaman ini dinonaktifkan; perubahan hanya di <strong>Data Siswa</strong>.</li>
                                    <li>NIS &amp; kelas dilengkapi di Data Siswa.</li>
                                </ul>
                            </div>
                            <form method="POST" action="{{ route('mutasi.accept', $registration) }}" class="flex justify-end gap-2 border-t border-rule/70 px-5 py-4">
                                @csrf
                                <x-ui.button variant="ghost" size="md" @click="acceptOpen = false" type="button">Batal</x-ui.button>
                                <x-ui.button type="submit" variant="success" size="md" icon="check-circle">Lanjutkan &amp; Terima</x-ui.button>
                            </form>
                        </div>
                    </div>
                    {{-- Reject Modal --}}
                    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" x-transition @keydown.escape.window="open = false" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]"></div>
                        <div class="relative w-full max-w-lg rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule">
                            <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                                <h3 class="text-sm font-bold text-ink">Tolak Pendaftaran Pindah</h3>
                                <button type="button" @click="open = false" class="rounded-md p-1 text-ink-faint hover:bg-paper-deep hover:text-ink"><x-svg-x-mark class="size-5" /></button>
                            </header>
                            <form method="POST" action="{{ route('mutasi.reject', $registration) }}">
                                @csrf
                                <div class="px-5 py-5">
                                    <label class="block pb-1.5 text-xs font-bold text-ink">Alasan Penolakan</label>
                                    <textarea name="rejection_reason" rows="3" required class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Tulis alasan…"></textarea>
                                </div>
                                <div class="flex justify-end gap-2 border-t border-rule/70 px-5 py-4">
                                    <x-ui.button variant="ghost" size="md" @click="open = false" type="button">Batal</x-ui.button>
                                    <x-ui.button type="submit" variant="danger" size="md">Tolak</x-ui.button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @include('pages.mutasi.partials.steps', ['active' => 'mutasi.show'])
        @if (session('status')) <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div> @endif

        {{-- Grid detail --}}
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ui.sheet title="Identitas Siswa & Asal" pinned ruled>
                <dl class="space-y-2.5">
                    @foreach ([
                        'Nama' => $registration->name,
                        'NIK' => $registration->nik,
                        'NISN' => $registration->nisn,
                        'NIS Asal' => $registration->nis_asal,
                        'Jenis Kelamin' => $registration->gender === 'P' ? 'Perempuan' : 'Laki-laki',
                        'Agama' => $registration->religion,
                        'Tempat, Tanggal Lahir' => collect([$registration->birth_place, $registration->birth_date?->format('d/m/Y')])->filter()->implode(', '),
                        'Hobi' => $v($registration->hobby),
                        'Cita-cita' => $v($registration->ambition),
                        'Anak Ke' => $v($registration->child_order),
                        'Jumlah Saudara' => $v($registration->sibling_count),
                        'Pernah TK' => $v($registration->ever_tk),
                        'Pernah PAUD' => $v($registration->ever_paud),
                        'Tanggal Masuk' => $registration->entry_date?->format('d/m/Y'),
                    ] as $label => $value)
                        <div class="flex items-start justify-between gap-4"><dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt><dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd></div>
                    @endforeach
                </dl>
            </x-ui.sheet>

            <x-ui.sheet title="Tujuan Mutasi & Alamat Siswa" pinned ruled>
                <dl class="space-y-2.5">
                    @foreach ([
                        'Kelas Tujuan' => $registration->kelas_tujuan,
                        'Tanggal Mutasi' => $registration->tanggal_mutasi?->format('d/m/Y'),
                        'Alasan Pindah' => $registration->alasan_pindah,
                        'Tinggal Bersama' => $registration->residence_type,
                        'Alamat' => collect([$registration->address, $registration->village, $registration->district, $registration->city, $registration->province])->filter()->implode(', '),
                        'RT / RW' => collect([$registration->rt, $registration->rw])->filter()->implode(' / '),
                        'Kode Pos' => $registration->postal_code,
                        'Jarak' => $registration->distance,
                        'Transportasi' => $registration->transport,
                        'Waktu Tempuh' => $registration->commute_time,
                        'Telepon Rumah' => $registration->home_phone,
                        'HP Siswa' => $registration->student_phone,
                        'Email Siswa' => $registration->student_email,
                    ] as $label => $value)
                        <div class="flex items-start justify-between gap-4"><dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt><dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd></div>
                    @endforeach
                </dl>
            </x-ui.sheet>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ui.sheet title="Keluarga & KK" pinned ruled>
                <dl class="space-y-2.5">
                    @foreach ([
                        'No. KK' => $registration->kk_number,
                        'Kepala Keluarga' => $registration->kk_head_name,
                        '—' => null,
                        'Nama Ayah' => $registration->father_name,
                        'Status Ayah' => $registration->father_status,
                        'NIK Ayah' => $registration->father_nik,
                        'TTL Ayah' => collect([$registration->father_birth_place, $registration->father_birth_date?->format('d/m/Y')])->filter()->implode(', '),
                        'Pendidikan Ayah' => $registration->father_education,
                        'Pekerjaan Ayah' => $registration->father_job,
                        'Penghasilan Ayah' => $registration->father_income,
                        'HP Ayah' => $registration->father_phone,
                        '—' => null,
                        'Nama Ibu' => $registration->mother_name,
                        'Status Ibu' => $registration->mother_status,
                        'NIK Ibu' => $registration->mother_nik,
                        'TTL Ibu' => collect([$registration->mother_birth_place, $registration->mother_birth_date?->format('d/m/Y')])->filter()->implode(', '),
                        'Pendidikan Ibu' => $registration->mother_education,
                        'Pekerjaan Ibu' => $registration->mother_job,
                        'Penghasilan Ibu' => $registration->mother_income,
                        'HP Ibu' => $registration->mother_phone,
                        '—' => null,
                        'Nama Wali' => $registration->guardian_name,
                        'NIK Wali' => $registration->guardian_nik,
                        'Pendidikan Wali' => $registration->guardian_education,
                        'HP Wali' => $registration->guardian_phone,
                    ] as $label => $value)
                        @if ($label === '—')
                            <div class="border-t border-rule/60 pt-2"></div>
                        @else
                            <div class="flex items-start justify-between gap-4"><dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt><dd class="text-right text-sm font-semibold text-ink">{{ $v($value) }}</dd></div>
                        @endif
                    @endforeach
                </dl>
            </x-ui.sheet>

            <div class="space-y-6">
                <x-ui.sheet title="Kesehatan & Kebutuhan Khusus" pinned ruled>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach (['imm_hepb' => 'Hep B', 'imm_polio' => 'Polio', 'imm_bcg' => 'BCG', 'imm_campak' => 'Campak', 'imm_dpt' => 'DPT', 'imm_covid' => 'COVID'] as $f => $l)
                            @if ($registration->$f)
                                <x-ui.badge variant="success">{{ $l }}</x-ui.badge>
                            @endif
                        @endforeach
                        @foreach (['dis_deaf' => 'Tuli', 'dis_blind' => 'Netra', 'dis_disabled' => 'Daksa', 'dis_intellectual' => 'Grahita', 'dis_behavioral' => 'Perilaku', 'dis_slow_learner' => 'Lamban', 'dis_communication' => 'Komunikasi', 'dis_gifted' => 'Istimewa'] as $f => $l)
                            @if ($registration->$f)
                                <x-ui.badge variant="warning">{{ $l }}</x-ui.badge>
                            @endif
                        @endforeach
                        @if (! collect(['imm_hepb','imm_polio','imm_bcg','imm_campak','imm_dpt','imm_covid','dis_deaf','dis_blind','dis_disabled','dis_intellectual','dis_behavioral','dis_slow_learner','dis_communication','dis_gifted'])->some(fn ($f) => $registration->$f))
                            <p class="text-xs text-ink-faint">Tidak ada data imunisasi / kebutuhan khusus.</p>
                        @endif
                    </div>
                    @if ($registration->social_kks || $registration->social_pkh || $registration->social_kip)
                        <div class="mt-3 border-t border-rule/60 pt-3">
                            <p class="mb-1 text-[10px] font-bold uppercase tracking-wide text-ink-faint">Bantuan Sosial</p>
                            <div class="flex flex-wrap gap-2">
                                @if ($registration->social_kks) <span class="text-xs font-semibold text-ink">KKS: {{ $registration->social_kks }}</span> @endif
                                @if ($registration->social_pkh) <span class="text-xs font-semibold text-ink">PKH: {{ $registration->social_pkh }}</span> @endif
                                @if ($registration->social_kip) <span class="text-xs font-semibold text-ink">KIP: {{ $registration->social_kip }}</span> @endif
                            </div>
                        </div>
                    @endif
                </x-ui.sheet>

                <x-ui.sheet title="Dokumen" pinned ruled>
                    <div class="space-y-2">
                        @foreach ([
                            'scanned_rekomendasi' => 'Surat Rekomendasi Madrasah',
                            'scanned_rapor' => 'Rapor / Transkrip',
                            'scanned_kk' => 'Kartu Keluarga',
                            'scanned_kk_wali' => 'KK Wali',
                            'scanned_akta' => 'Akta Kelahiran',
                            'scanned_ijazah' => 'Ijazah / SKL',
                            'scanned_photo' => 'Pas Foto',
                        ] as $key => $label)
                            <div class="flex items-center justify-between gap-3 rounded-[var(--radius-control)] bg-paper px-3 py-2 ring-1 ring-inset ring-rule-strong">
                                <span class="text-xs font-semibold text-ink">{{ $label }}</span>
                                @if ($registration->{$key})
                                    <a href="{{ $registration->{$key} }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline">
                                        <x-svg-arrow-top-right-on-square class="size-3.5" /> Buka
                                    </a>
                                @else
                                    <span class="text-xs text-ink-faint">—</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if ($registration->status === 'rejected' && $registration->rejection_reason)
                        <p class="mt-4 rounded-[var(--radius-control)] bg-danger/10 px-3 py-2 text-xs text-danger">
                            <strong>Alasan ditolak:</strong> {{ $registration->rejection_reason }}
                        </p>
                    @endif
                </x-ui.sheet>
            </div>
        </div>
    </div>
</x-layouts.page>