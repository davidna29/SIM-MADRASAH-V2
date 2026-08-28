@php
    $notes = $note ?? '';
@endphp
<div class="rounded-sheet bg-primary-soft/60 ring-1 ring-inset ring-primary/20 p-4">
    <p class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide text-primary-strong">
        <x-svg-queue-list class="size-4" /> Alur Pengerjaan Admin
    </p>
    <ol class="mt-2 space-y-1 text-xs leading-relaxed text-ink-soft">
        <li>1. Cek kelengkapan data & dokumen pendaftar pindah (termasuk <strong>Surat Rekomendasi Madrasah</strong>).</li>
        <li>2. Terima (data disalin persis ke Master Data Siswa) atau Tolak dengan alasan.</li>
        <li>3. Setelah diterima, edit di modul ini <strong>terkunci</strong> — NIS & kelas dilengkapi di menu Data Siswa.</li>
    </ol>
    @if ($notes)
        <p class="mt-2 border-t border-primary/20 pt-2 text-xs font-medium text-ink-faint">{{ $notes }}</p>
    @endif
</div>