<x-layouts.page
    :title="'Detail Konseling'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="konseling.index">

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Detail Sesi Konseling</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Catatan lengkap sesi bimbingan dan konseling siswa.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @can('update', $session)
                    <x-ui.button variant="secondary" icon="pencil-square" href="{{ route('konseling.edit', $session) }}">Ubah</x-ui.button>
                @endcan
                @can('delete', $session)
                    <form method="POST" action="{{ route('konseling.destroy', $session) }}" onsubmit="return confirm('Yakin ingin menghapus sesi konseling ini?')">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="danger" icon="trash">Hapus</x-ui.button>
                    </form>
                @endcan
            </div>
        </div>

        @php
            $cv = match($session->confidentiality_level) {
                'guru_bk_only' => ['danger', 'Hanya Guru BK'],
                'plus_kepala' => ['warning', 'Guru BK & Kepala Madrasah'],
                default => ['info', 'Guru BK, Kepala & Wali Kelas'],
            };
            $sv = match($session->status) {
                'aktif' => ['success', 'Aktif'],
                default => ['neutral', 'Ditutup'],
            };
        @endphp

        <div class="mt-6 space-y-4">
            <!-- Info Dasar -->
            <x-ui.sheet>
                <x-ui.form-section title="Informasi Sesi" description="Data dasar sesi konseling.">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Siswa</dt>
                            <dd class="mt-1 text-sm font-medium text-ink">{{ $session->enrollment->student->displayName() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Kelas</dt>
                            <dd class="mt-1 text-sm text-ink">{{ $session->enrollment->classGroup->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Tanggal</dt>
                            <dd class="mt-1 font-mono text-sm text-ink">{{ $session->session_date->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Jenis</dt>
                            <dd class="mt-1 text-sm text-ink">{{ $counselingTypes[$session->counseling_type] ?? ucfirst($session->counseling_type) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Topik</dt>
                            <dd class="mt-1 text-sm font-medium text-ink">{{ $session->topic }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Guru BK</dt>
                            <dd class="mt-1 text-sm text-ink">{{ $session->counselor->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Kerahasiaan</dt>
                            <dd class="mt-1"><x-ui.badge :variant="$cv[0]">{{ $cv[1] }}</x-ui.badge></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Status</dt>
                            <dd class="mt-1"><x-ui.badge :variant="$sv[0]">{{ $sv[1] }}</x-ui.badge></dd>
                        </div>
                    </dl>
                </x-ui.form-section>
            </x-ui.sheet>

            <!-- Isi Konseling -->
            <x-ui.sheet>
                <x-ui.form-section title="Isi Konseling" description="Detail catatan sesi konseling.">
                    <div class="space-y-4">
                        @if ($session->problem_description)
                            <div>
                                <h4 class="text-xs font-semibold text-ink-soft mb-1">Permasalahan</h4>
                                <p class="text-sm leading-relaxed text-ink whitespace-pre-line">{{ $session->problem_description }}</p>
                            </div>
                        @endif
                        @if ($session->assessment_result)
                            <div>
                                <h4 class="text-xs font-semibold text-ink-soft mb-1">Hasil Asesmen</h4>
                                <p class="text-sm leading-relaxed text-ink whitespace-pre-line">{{ $session->assessment_result }}</p>
                            </div>
                        @endif
                        @if ($session->action_taken)
                            <div>
                                <h4 class="text-xs font-semibold text-ink-soft mb-1">Tindakan</h4>
                                <p class="text-sm leading-relaxed text-ink whitespace-pre-line">{{ $session->action_taken }}</p>
                            </div>
                        @endif
                        @if ($session->follow_up_plan)
                            <div>
                                <h4 class="text-xs font-semibold text-ink-soft mb-1">Rencana Tindak Lanjut</h4>
                                <p class="text-sm leading-relaxed text-ink whitespace-pre-line">{{ $session->follow_up_plan }}</p>
                            </div>
                        @endif
                        @if (! $session->problem_description && ! $session->assessment_result && ! $session->action_taken && ! $session->follow_up_plan)
                            <p class="text-sm text-ink-faint">Belum ada detail catatan konseling.</p>
                        @endif
                    </div>
                </x-ui.form-section>
            </x-ui.sheet>

            <!-- Lampiran -->
            @if ($session->attachment_path)
                <x-ui.sheet>
                    <x-ui.form-section title="Lampiran" description="File lampiran sesi konseling.">
                        <div class="flex items-center gap-3">
                            <x-svg-paper-clip class="size-5 text-ink-faint" />
                            <div>
                                <p class="text-sm font-medium text-ink">{{ basename($session->attachment_path) }}</p>
                                <p class="text-xs text-ink-faint">Disimpan di penyimpanan privat</p>
                            </div>
                        </div>
                    </x-ui.form-section>
                </x-ui.sheet>
            @endif
        </div>
    </div>
</x-layouts.page>
