---
version: 1
slug: "resources-views-components-layouts-app-blade-php"
primary_target: "resources/views/components/layouts/app.blade.php"
related_targets: []
---

# Surface brief — Shell Aplikasi & Design System (Tahap 11)

## Scope
Design system (komponen Blade reusable) + shell navigasi aplikasi SIM Madrasah: sidebar papan per role, topbar, konten kertas, footer. Mode: Operate.

## Audience & job
Guru/pegawai madrasah (16 role) menyelesaikan tugas administratif harian (nilai, tagihan, absensi, surat). Super Admin memimpin shell ini.

## Action / task
Buka dashboard → sematkan persetujuan "Perlu Tindakan" (interaksi tanda tangan pin/unpin) → telusuri data via tabel berfilter & form.

## Constraints
- Dasar kertas terang & sederhana; hindari ikon tanpa teks, istilah teknis, form panjang.
- Status selalu pin berwarna + label teks.
- Bahasa Indonesia. Data demo ditandai "Data demo — bukan data riil".

## Chosen direction
Dunia "Mading" (papan pengumuman madrasah), kandidat 4 daftar grounded, seed 75d536ba — code-led, tanpa komp.

## Memorable moment
"Sematkan & Setujui": menyetujui sebuah lembar memunculkan pin hijau dengan animasi scale-in, baris meredup, dan toast muncul — satu-satunya momen gerak terorchestrasi.

## Unresolved
Backend/DB modul belum dibangun (Tahap 13); route & data saat ini demo via DemoData. Format rapor (Q2) & retensi data (Q1) menunggu keputusan PRD.
