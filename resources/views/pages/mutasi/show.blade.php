<x-layouts.page
    :title="'Detail Pendaftar Pindah'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mutasi.show">

    <div class="mx-auto max-w-5xl">
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
                    <button type="button" @click="acceptOpen = true"
                        class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-success px-3 py-2 text-xs font-semibold text-white transition hover:brightness-95">
                        <x-svg-check-circle class="size-3.5" /> Terima
                    </button>
                    <button type="button" @click="open = true"
                        class="inline-flex items-center gap-1.5 rounded-[var(--radius-control)] bg-danger px-3 py-2 text-xs font-semibold text-white transition hover:brightness-95">
                        <x-svg-x-circle class="size-3.5" /> Tolak
                    </button>

                    {{-- Accept Modal --}}
                    <div x-show="acceptOpen" x-cloak
                        class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
                        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                        @keydown.escape.window="acceptOpen = false" role="dialog" aria-modal="true">
                        <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]"></div>
                        <div class="relative w-full max-w-lg rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule">
                            <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                                <h3 class="text-sm font-bold text-ink">Terima Pendaftar Pindah — Penting!</h3>
                                <button type="button" @click="acceptOpen = false" class="rounded-md p-1 text-ink-faint hover:bg-paper-deep hover:text-ink"><x-svg-x-mark class="size-5" /></button>
                            </header>
                            <div class="px-5 py-5 text-sm leading-relaxed text-ink">
                                <p class="font-semibold">Menerima pendaftar ini akan <span class="text-danger font-extrabold">MENGUNCI seluruh pengeditan di modul mutasi</span>.</p>
                                <ul class="mt-3 list-disc list-inside space-y-1.5 text-ink-soft">
                                    <li>Seluruh data disalin <strong>persis</strong> ke Master Data Siswa.</li>
                                    <li>Edit di halaman ini dinonaktifkan.</li>
                                    <li>Perubahan selanjutnya hanya di menu <strong>Data Siswa</strong>.</li>
                                    <li>NIS &amp; kelas (sesuai kelas tujuan) dilengkapi di Data Siswa.</li>
                                </ul>
                            </div>
                            <form method="POST" action="{{ route('mutasi.accept', $registration) }}">
                                @csrf
                                <footer class="flex justify-end gap-2 border-t border-rule/70 px-5 py-4">
                                    <x-ui.button variant="ghost" size="md" @click="acceptOpen = false" type="button">Batal</x-ui.button>
                                    <x-ui.button type="submit" variant="success" size="md" icon="check-circle">Lanjutkan &amp; Terima</x-ui.button>
                                </footer>
                            </form>
                        </div>
                    </div>

                    {{-- Reject Modal --}}
                    <div x-show="open" x-cloak
                        class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
                        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0"
                        @keydown.escape.window="open = false" role="dialog" aria-modal="true">
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

        @include('pages.mutasi.partials.steps', ['active' => 'mutasi.show'])

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>@foreach ($errors->all() as $error) {{ $error }} @endforeach</x-ui.alert></div>
        @endif

        {{-- Detail --}}
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ui.sheet title="Identitas & Asal" pinned ruled>
                <dl class="space-y-2.5">
                    @foreach ([
                        'Nama' => $registration->name,
                        'NIK' => $registration->nik,
                        'NISN' => $registration->nisn,
                        'NIS Asal' => $registration->nis_asal,
                        'Jenis Kelamin' => $registration->gender === 'P' ? 'Perempuan' : 'Laki-laki',
                        'Agama' => $registration->religion,
                        'Tempat, Tanggal Lahir' => collect([$registration->birth_place, $registration->birth_date?->format('d/m/Y')])->filter()->implode(', '),
                        'Madrasah Asal' => $registration->origin_school,
                        'NSM / NPSN Asal' => collect([$registration->origin_nsm, $registration->origin_npsn])->filter()->implode(' / '),
                        'Kelas Asal' => $registration->kelas_asal,
                    ] as $label => $value)
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt>
                            <dd class="text-right text-sm font-semibold text-ink">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-ui.sheet>

            <x-ui.sheet title="Tujuan Mutasi & Alamat" pinned ruled>
                <dl class="space-y-2.5">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Kelas Tujuan</dt>
                        <dd class="text-right text-sm font-bold text-primary">{{ $registration->kelas_tujuan ?? '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Tanggal Mutasi</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ $registration->tanggal_mutasi?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">HP / WA</dt>
                        <dd class="tabular text-right font-mono text-sm font-semibold text-ink">{{ $registration->student_phone ?? '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Alamat</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ collect([$registration->address, $registration->village, $registration->district, $registration->city, $registration->province])->filter()->implode(', ') ?: '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">RT / RW</dt>
                        <dd class="text-right text-sm font-semibold text-ink">{{ collect([$registration->rt, $registration->rw])->filter()->implode(' / ') ?: '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">Alasan Pindah</dt>
                        <dd class="max-w-[60%] text-right text-sm leading-relaxed text-ink">{{ $registration->alasan_pindah ?? '—' }}</dd>
                    </div>
                </dl>
            </x-ui.sheet>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ui.sheet title="Orang Tua / Wali" pinned ruled>
                <dl class="space-y-2.5">
                    @foreach ([
                        'Ayah' => collect([$registration->father_name, $registration->father_job])->filter()->implode(' · '),
                        'NIK Ayah' => $registration->father_nik,
                        'HP Ayah' => $registration->father_phone,
                        'Ibu' => collect([$registration->mother_name, $registration->mother_job])->filter()->implode(' · '),
                        'NIK Ibu' => $registration->mother_nik,
                        'HP Ibu' => $registration->mother_phone,
                        'Wali' => $registration->guardian_name,
                        'HP Wali' => $registration->guardian_phone,
                    ] as $label => $value)
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-xs font-bold uppercase tracking-wide text-ink-faint">{{ $label }}</dt>
                            <dd class="text-right text-sm font-semibold text-ink">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-ui.sheet>

            <x-ui.sheet title="Dokumen" pinned ruled>
                <div class="space-y-2">
                    @foreach ([
                        'scanned_rekomendasi' => 'Surat Rekomendasi Madrasah',
                        'scanned_rapor' => 'Rapor / Transkrip',
                        'scanned_kk' => 'Kartu Keluarga',
                        'scanned_akta' => 'Akta Kelahiran',
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
</x-layouts.page>