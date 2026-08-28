<x-layouts.publik :title="'Pendaftaran Pindahan Berhasil'">
    <div class="mx-auto max-w-lg py-10 text-center">
        <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-success/15 text-success">
            <x-svg-check-circle class="size-9" />
        </span>
        <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-ink">Pendaftaran Pindahan Berhasil</h1>
        <p class="mt-2 text-sm leading-relaxed text-ink-soft">
            Terima kasih. Data Anda telah kami terima dan akan diverifikasi oleh panitia.
        </p>

        @if ($registration)
            <div class="mt-6 rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60">
                <p class="text-xs font-semibold text-ink-soft">Nomor Pendaftaran Anda</p>
                <p class="tabular mt-1 font-mono text-2xl font-extrabold tracking-wide text-primary">{{ $registration->registration_no }}</p>
                <p class="mt-2 text-xs text-ink-soft">Simpan nomor ini untuk pengecekan status pendaftaran.</p>
            </div>
        @endif

        <a href="{{ route('pindahan.form') }}" class="mt-6 inline-flex items-center gap-2 rounded-[var(--radius-control)] bg-sheet px-5 py-2.5 text-sm font-bold text-ink ring-1 ring-inset ring-rule-strong transition hover:bg-paper-deep">
            <x-svg-arrow-left class="size-4" /> Kembali ke Beranda Mutasi
        </a>
    </div>
</x-layouts.publik>