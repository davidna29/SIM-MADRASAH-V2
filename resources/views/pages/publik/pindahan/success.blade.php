<x-layouts.publik :title="'Pendaftaran Pindahan diterima'" :description="'Konfirmasi pengiriman formulir siswa pindahan.'">
    <div class="mx-auto max-w-2xl py-8 sm:py-12">
        <div class="text-center">
            <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-success-soft text-success"><x-svg-check-circle class="size-9" aria-hidden="true" /></span>
            <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-ink">Formulir pindahan berhasil dikirim</h1>
            <p class="mx-auto mt-3 max-w-prose text-sm leading-relaxed text-ink-soft">{{ session('status') ?: 'Terima kasih. Panitia akan meninjau pendaftaran dan menghubungi orang tua/wali jika diperlukan.' }}</p>
        </div>

        @if ($registration)
            <section class="mt-8 rounded-sheet bg-sheet p-6 text-center shadow-sheet ring-1 ring-inset ring-rule/60 sm:p-8">
                <p class="text-xs font-semibold text-ink-faint">Nomor pendaftaran</p>
                <p class="tabular mt-2 font-mono text-2xl font-extrabold tracking-wide text-primary-strong">{{ $registration->registration_no }}</p>
                <p class="mt-3 text-sm leading-relaxed text-ink-soft">Simpan nomor ini untuk komunikasi dengan panitia penerimaan.</p>
            </section>
        @endif

        <div class="mt-7 flex flex-wrap justify-center gap-3">
            <x-ui.button variant="primary" icon="home" :href="route('publik.beranda')">Kembali ke Beranda</x-ui.button>
            <x-ui.button variant="secondary" icon="phone" :href="route('publik.kontak')">Kontak Madrasah</x-ui.button>
        </div>
    </div>
</x-layouts.publik>
