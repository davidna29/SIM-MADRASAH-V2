<x-layouts.publik :title="'Pendaftaran PPDB diterima'" :description="'Konfirmasi pengiriman formulir pendaftaran PPDB.'">
    <div class="mx-auto max-w-2xl py-8 sm:py-12">
        <div class="text-center">
            <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-success-soft text-success"><x-svg-check-circle class="size-9" aria-hidden="true" /></span>
            <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-ink">Formulir berhasil dikirim</h1>
            <p class="mx-auto mt-3 max-w-prose text-sm leading-relaxed text-ink-soft">{{ session('status') ?: 'Terima kasih. Panitia akan meninjau pendaftaran dan menghubungi orang tua/wali jika diperlukan.' }}</p>
        </div>

        @if ($registration)
            <section class="mt-8 rounded-sheet bg-sheet p-6 shadow-sheet ring-1 ring-inset ring-rule/60 sm:p-8" aria-labelledby="detail-pendaftaran-heading">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-rule/70 pb-4">
                    <div>
                        <h2 id="detail-pendaftaran-heading" class="text-lg font-extrabold text-ink">Simpan nomor pendaftaran</h2>
                        <p class="mt-1 text-sm text-ink-soft">Gunakan nomor ini saat menghubungi panitia.</p>
                    </div>
                    <x-ui.badge variant="warning">Menunggu verifikasi</x-ui.badge>
                </div>
                <dl class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div><dt class="text-xs font-semibold text-ink-faint">Nomor pendaftaran</dt><dd class="mt-1 font-mono text-lg font-extrabold tracking-wide text-primary-strong">{{ $registration->registration_no }}</dd></div>
                    <div><dt class="text-xs font-semibold text-ink-faint">Nama calon siswa</dt><dd class="mt-1 font-semibold text-ink">{{ $registration->name }}</dd></div>
                </dl>
            </section>
        @endif

        <div class="mt-7 flex flex-wrap justify-center gap-3">
            <x-ui.button variant="primary" icon="home" :href="route('publik.beranda')">Kembali ke Beranda</x-ui.button>
            <x-ui.button variant="secondary" icon="phone" :href="route('publik.kontak')">Kontak Madrasah</x-ui.button>
        </div>
    </div>
</x-layouts.publik>
