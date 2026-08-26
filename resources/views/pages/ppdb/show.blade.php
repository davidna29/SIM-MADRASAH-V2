<x-layouts.page
    :title="'PPDB - '.$registration->registration_no"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ppdb.index">

    <div class="mx-auto max-w-4xl">
        @php
            $pendidikanOpts = \App\Enums\Pendidikan::options();
            $pekerjaanOpts = \App\Enums\Pekerjaan::options();
            $penghasilanOpts = [
                '< Rp500rb' => '< Rp 500.000',
                'Rp500rb – 1jt' => 'Rp 500.000 – 1.000.000',
                'Rp1jt – 2jt' => 'Rp 1.000.000 – 2.000.000',
                'Rp2jt – 3jt' => 'Rp 2.000.000 – 3.000.000',
                'Rp3jt – 5jt' => 'Rp 3.000.000 – 5.000.000',
                '> Rp5jt' => '> Rp 5.000.000',
                'Tidak ada' => 'Tidak ada',
            ];
            $mapOpt = fn ($opts, $val) => $val !== null && $val !== '' && isset($opts[$val]) ? $opts[$val] : $val;
        @endphp

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ strtoupper($registration->name) }}</h1>
                <p class="mt-1 text-sm text-ink-soft">{{ $registration->registration_no }} · {{ $registration->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2">
                @if ($registration->status === 'submitted')
                    <form method="POST" action="{{ route('ppdb.accept', $registration) }}" x-data="{ confirming: false }"
                        @submit="if(!confirming) { event.preventDefault(); confirming = true; }">
                        @csrf
                        <button type="submit" @click="confirming = true"
                            class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-success px-3 py-2 text-xs font-semibold text-white transition hover:brightness-95">
                            <x-svg-check-circle class="size-3.5" /> Terima
                        </button>
                    </form>
                    <button type="button" x-data="{ open: false }" @click="open = true"
                        class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-danger px-3 py-2 text-xs font-semibold text-white transition hover:brightness-95">
                        <x-svg-x-circle class="size-3.5" /> Tolak
                    </button>

                    {{-- Reject Modal --}}
                    <div x-data="{ open: false }" x-show="open" x-cloak
                        class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
                        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                        @keydown.escape.window="open = false" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]"></div>
                        <div class="relative w-full max-w-lg rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule">
                            <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                                <h3 class="text-sm font-bold text-ink">Tolak Pendaftaran</h3>
                                <button type="button" @click="open = false" class="rounded-md p-1 text-ink-faint hover:bg-paper-deep hover:text-ink"><x-svg-x-mark class="size-5" /></button>
                            </header>
                            <form method="POST" action="{{ route('ppdb.reject', $registration) }}">
                                @csrf
                                <div class="px-5 py-5">
                                    <label class="block pb-1.5 text-xs font-bold text-ink">Alasan Penolakan</label>
                                    <textarea name="rejection_reason" rows="3" required
                                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary"
                                        placeholder="Tulis alasan penolakan…"></textarea>
                                </div>
                                <footer class="flex justify-end gap-2 border-t border-rule/70 px-5 py-4">
                                    <x-ui.button variant="ghost" size="md" @click="open = false" type="button">Batal</x-ui.button>
                                    <x-ui.button type="submit" variant="danger" size="md">Tolak</x-ui.button>
                                </footer>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        {{-- Status Badge --}}
        <div class="mt-4">
            <x-ui.badge :variant="match($registration->status) {
                'submitted' => 'warning',
                'accepted' => 'success',
                'rejected' => 'danger',
                default => 'neutral',
            }" :dot="true">{{ ucfirst($registration->status) }}</x-ui.badge>
            @if ($registration->rejection_reason)
                <p class="mt-1 text-xs text-danger">{{ $registration->rejection_reason }}</p>
            @endif
            @if ($registration->nis_nism)
                <p class="mt-1 text-xs text-ink-soft">NIS: <span class="font-mono font-bold">{{ $registration->nis_nism }}</span></p>
            @endif
        </div>

        {{-- A. Data Siswa --}}
        <x-ui.sheet title="A. Data Siswa" class="mt-6" pinned ruled>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                @foreach ([
                    'Nama' => strtoupper($registration->name),
                    'NIK' => $registration->nik,
                    'NISN' => $registration->nisn ?? '—',
                    'Jenis Kelamin' => $registration->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                    'Agama' => $registration->religion,
                    'Tempat Lahir' => $registration->birth_place ?? '—',
                    'Tanggal Lahir' => $registration->birth_date?->format('d/m/Y') ?? '—',
                    'Sekolah Asal' => $registration->previous_school ?? '—',
                    'Hobi' => $registration->hobby ?? '—',
                    'Cita-cita' => $registration->ambition ?? '—',
                    'Anak Ke' => $registration->child_order ?? '—',
                    'Jumlah Saudara' => $registration->sibling_count ?? '—',
                    'Pernah TK' => $registration->ever_tk,
                    'Pernah PAUD' => $registration->ever_paud,
                    'Tanggal Masuk' => $registration->entry_date?->format('d/m/Y') ?? '—',
                ] as $label => $value)
                    <div class="flex justify-between gap-2 py-1 border-b border-rule/40">
                        <dt class="text-ink-soft">{{ $label }}</dt>
                        <dd class="text-right font-medium text-ink">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.sheet>

        {{-- Dokumen --}}
        <x-ui.sheet title="Dokumen (Google Drive)" class="mt-4" pinned ruled>
            <dl class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
                @foreach ([
                    'Scan KK' => $registration->scanned_kk,
                    'Scan KK Wali' => $registration->scanned_kk_wali,
                    'Scan Akte' => $registration->scanned_akta,
                    'Scan Ijazah' => $registration->scanned_ijazah,
                    'Foto' => $registration->scanned_photo,
                ] as $label => $url)
                    <div class="flex items-center gap-2 py-1">
                        <dt class="text-ink-soft">{{ $label }}:</dt>
                        @if ($url)
                            <a href="{{ $url }}" target="_blank" class="text-primary hover:underline text-xs truncate max-w-[200px]">{{ $url }}</a>
                        @else
                            <dd class="text-xs text-ink-faint">—</dd>
                        @endif
                    </div>
                @endforeach
            </dl>
        </x-ui.sheet>

        {{-- B+C. Kesehatan & BK --}}
        <x-ui.sheet title="B. Kesehatan & C. Berkebutuhan Khusus" class="mt-4" pinned ruled>
            <div class="grid grid-cols-2 gap-2 text-sm sm:grid-cols-3">
                @foreach ([
                    'Hepatitis B' => $registration->imm_hepb,
                    'Polio' => $registration->imm_polio,
                    'BCG' => $registration->imm_bcg,
                    'Campak' => $registration->imm_campak,
                    'DPT-HB-HiB' => $registration->imm_dpt,
                    'COVID' => $registration->imm_covid,
                ] as $label => $val)
                    <div class="py-1"><span class="text-ink-soft">{{ $label }}:</span> <span class="font-bold">{{ $val }}</span></div>
                @endforeach
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2 text-sm sm:grid-cols-4">
                @foreach ([
                    'Tuna Rungu' => $registration->dis_deaf,
                    'Tuna Netra' => $registration->dis_blind,
                    'Tuna Daksa' => $registration->dis_disabled,
                    'Tuna Grahita' => $registration->dis_intellectual,
                    'Tuna Laras' => $registration->dis_behavioral,
                    'Lamban Belajar' => $registration->dis_slow_learner,
                    'Gangguan Komunikasi' => $registration->dis_communication,
                    'Bakat Luar Biasa' => $registration->dis_gifted,
                ] as $label => $val)
                    <div class="py-1"><span class="text-ink-soft">{{ $label }}:</span> <span class="font-bold">{{ $val ? 'Ya' : 'Tidak' }}</span></div>
                @endforeach
            </div>
        </x-ui.sheet>

        {{-- D. Alamat Siswa --}}
        <x-ui.sheet title="D. Alamat Siswa" class="mt-4" pinned ruled>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                @foreach ([
                    'Jenis Tempat Tinggal' => $registration->residence_type,
                    'Alamat' => $registration->address,
                    'Provinsi' => $registration->province,
                    'Kota/Kab' => $registration->city,
                    'Kecamatan' => $registration->district,
                    'Kelurahan' => $registration->village,
                    'RT' => $registration->rt,
                    'RW' => $registration->rw,
                    'Kode Pos' => $registration->postal_code,
                    'Jarak' => $registration->distance,
                    'Transportasi' => $registration->transport,
                    'Waktu Tempuh' => $registration->commute_time,
                ] as $label => $value)
                    <div class="flex justify-between gap-2 py-1 border-b border-rule/40">
                        <dt class="text-ink-soft">{{ $label }}</dt>
                        <dd class="text-right font-medium text-ink">{{ $value ?? '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.sheet>

        {{-- E. Orang Tua --}}
        <x-ui.sheet title="E. Data Orang Tua / Wali" class="mt-4" pinned ruled>
            <h4 class="text-xs font-bold text-ink-soft mb-2">Kartu Keluarga</h4>
            <dl class="grid grid-cols-2 gap-2 text-sm mb-4">
                <div>No. KK: <span class="font-mono font-bold">{{ $registration->kk_number }}</span></div>
                <div>Kepala Keluarga: <span class="font-bold">{{ $registration->kk_head_name }}</span></div>
            </dl>

            <h4 class="text-xs font-bold text-ink-soft mb-2">Ayah</h4>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-1 text-sm sm:grid-cols-2 mb-4">
                @foreach ([
                    'Nama' => $registration->father_name,
                    'Status' => $registration->father_status,
                    'NIK' => $registration->father_nik,
                    'Tgl Lahir' => $registration->father_birth_date?->format('d/m/Y'),
                    'Tempat Lahir' => $registration->father_birth_place,
                    'Pendidikan' => $mapOpt($pendidikanOpts, $registration->father_education),
                    'Pekerjaan' => $mapOpt($pekerjaanOpts, $registration->father_job),
                    'Penghasilan' => $mapOpt($penghasilanOpts, $registration->father_income),
                    'HP' => $registration->father_phone,
                ] as $label => $value)
                    <div class="py-1 border-b border-rule/40"><span class="text-ink-soft">{{ $label }}:</span> <span class="font-medium">{{ $value ?? '—' }}</span></div>
                @endforeach
            </dl>

            <h4 class="text-xs font-bold text-ink-soft mb-2">Ibu</h4>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-1 text-sm sm:grid-cols-2 mb-4">
                @foreach ([
                    'Nama' => $registration->mother_name,
                    'Status' => $registration->mother_status,
                    'NIK' => $registration->mother_nik,
                    'Tgl Lahir' => $registration->mother_birth_date?->format('d/m/Y'),
                    'Tempat Lahir' => $registration->mother_birth_place,
                    'Pendidikan' => $mapOpt($pendidikanOpts, $registration->mother_education),
                    'Pekerjaan' => $mapOpt($pekerjaanOpts, $registration->mother_job),
                    'Penghasilan' => $mapOpt($penghasilanOpts, $registration->mother_income),
                    'HP' => $registration->mother_phone,
                ] as $label => $value)
                    <div class="py-1 border-b border-rule/40"><span class="text-ink-soft">{{ $label }}:</span> <span class="font-medium">{{ $value ?? '—' }}</span></div>
                @endforeach
            </dl>

            @if ($registration->guardian_name)
                <h4 class="text-xs font-bold text-ink-soft mb-2">Wali</h4>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-1 text-sm sm:grid-cols-2">
                    @foreach ([
                        'Nama' => $registration->guardian_name,
                        'NIK' => $registration->guardian_nik,
                        'Tgl Lahir' => $registration->guardian_birth_date?->format('d/m/Y'),
                        'Tempat Lahir' => $registration->guardian_birth_place,
                        'Pendidikan' => $mapOpt($pendidikanOpts, $registration->guardian_education),
                        'Pekerjaan' => $mapOpt($pekerjaanOpts, $registration->guardian_job),
                        'HP' => $registration->guardian_phone,
                    ] as $label => $value)
                        <div class="py-1 border-b border-rule/40"><span class="text-ink-soft">{{ $label }}:</span> <span class="font-medium">{{ $value ?? '—' }}</span></div>
                    @endforeach
                </dl>
            @endif

            @if ($registration->social_kks || $registration->social_pkh || $registration->social_kip)
                <h4 class="text-xs font-bold text-ink-soft mb-2 mt-4">Bantuan Sosial</h4>
                <dl class="grid grid-cols-3 gap-2 text-sm">
                    <div>KKS: {{ $registration->social_kks ?? '—' }}</div>
                    <div>PKH: {{ $registration->social_pkh ?? '—' }}</div>
                    <div>KIP: {{ $registration->social_kip ?? '—' }}</div>
                </dl>
            @endif
        </x-ui.sheet>

        {{-- F. Alamat Orang Tua --}}
        <x-ui.sheet title="F. Alamat Orang Tua" class="mt-4" pinned ruled>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                @foreach ([
                    'Status Rumah' => $registration->parent_ownership,
                    'Alamat' => $registration->parent_address,
                    'Provinsi' => $registration->parent_province,
                    'Kota/Kab' => $registration->parent_city,
                    'Kecamatan' => $registration->parent_district,
                    'Kelurahan' => $registration->parent_village,
                    'RT' => $registration->parent_rt,
                    'RW' => $registration->parent_rw,
                    'Kode Pos' => $registration->parent_postal_code,
                ] as $label => $value)
                    <div class="flex justify-between gap-2 py-1 border-b border-rule/40">
                        <dt class="text-ink-soft">{{ $label }}</dt>
                        <dd class="text-right font-medium text-ink">{{ $value ?? '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.sheet>

        {{-- G. Sekolah Asal --}}
        <x-ui.sheet title="G. Sekolah Asal" class="mt-4" pinned ruled>
            <dl class="grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                @foreach ([
                    'Nama Sekolah' => $registration->origin_school,
                    'NSM' => $registration->origin_nsm,
                    'NPSN' => $registration->origin_npsn,
                    'Alamat' => $registration->origin_address,
                ] as $label => $value)
                    <div class="flex justify-between gap-2 py-1 border-b border-rule/40">
                        <dt class="text-ink-soft">{{ $label }}</dt>
                        <dd class="text-right font-medium text-ink">{{ $value ?? '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-ui.sheet>

        {{-- Admin: Assign Class --}}
        @if ($registration->status === 'accepted')
            <x-ui.sheet title="Tentukan Kelas / Rombel" class="mt-4" pinned ruled>
                <form method="POST" action="{{ route('ppdb.assign-class', $registration) }}" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <x-ui.field label="Kelas" :error="$errors->first('kelas')">
                        <x-ui.select name="kelas" :options="['I'=>'I','II'=>'II','III'=>'III','IV'=>'IV','V'=>'V','VI'=>'VI']" :selected="$registration->kelas" />
                    </x-ui.field>
                    <x-ui.field label="Rombel" :error="$errors->first('rombel')">
                        <x-ui.input name="rombel" :value="$registration->rombel" placeholder="A" maxlength="3" />
                    </x-ui.field>
                    <x-ui.button type="submit" variant="primary" size="md">Simpan</x-ui.button>
                </form>
            </x-ui.sheet>
        @endif
    </div>
</x-layouts.page>
