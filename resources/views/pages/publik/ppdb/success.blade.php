<x-layouts.root :title="'PPDB - Berhasil'">
    <div class="flex min-h-screen flex-col items-center justify-center bg-paper px-4 py-10">
        <div class="w-full max-w-md text-center">
            <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-success-soft">
                <x-svg-check-circle class="size-8 text-success" />
            </div>

            <h1 class="mt-6 text-2xl font-extrabold tracking-tight text-ink">Pendaftaran Berhasil!</h1>

            @if (session('status'))
                <p class="mt-3 text-sm leading-relaxed text-ink-soft">{{ session('status') }}</p>
            @endif

            @if ($registration)
                <div class="mt-6 rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60 text-left">
                    <h2 class="text-sm font-bold text-ink">Detail Pendaftaran</h2>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-ink-soft">No. Pendaftaran</dt>
                            <dd class="font-mono font-bold text-primary">{{ $registration->registration_no }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-soft">Nama Siswa</dt>
                            <dd class="font-bold text-ink">{{ $registration->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-soft">Status</dt>
                            <dd>
                                <x-ui.badge variant="warning" :dot="true">Menunggu Verifikasi</x-ui.badge>
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="mt-4 rounded-sheet bg-info-soft p-4 text-left text-xs leading-relaxed text-info">
                    <strong>Catatan:</strong> Simpan nomor pendaftaran Anda. Panitia akan menghubungi via WhatsApp/telepon untuk proses verifikasi berikutnya.
                </div>
            @endif

            <div class="mt-8 flex flex-col gap-3">
                <a href="{{ route('ppdb.form') }}" class="inline-flex items-center justify-center rounded-[var(--radius-control)] bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-strong">
                    Kembali ke Formulir
                </a>
                <a href="{{ route('publik.berita.index') }}" class="text-xs text-ink-soft transition hover:text-ink">
                    ← Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-layouts.root>
