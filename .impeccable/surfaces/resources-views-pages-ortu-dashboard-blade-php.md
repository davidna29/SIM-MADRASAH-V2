---
version: 1
slug: "resources-views-pages-ortu-dashboard-blade-php"
primary_target: "resources/views/pages/ortu/dashboard.blade.php"
related_targets: []
---

# Surface brief — Portal Orang Tua (Tahap 12)

## Scope
Walking skeleton sisi orang tua: dashboard anak, lihat rapor, unduh PDF. Mode: Operate.

## Audience & job
Orang tua/wali melihat hasil belajar anak yang dihubungkan ke akunnya.

## Task
Buka "Anak Saya" → pilih anak → lihat rapor (nilai + predikat) → unduh PDF.

## Constraints
- Hanya data anak yang terhubung ke akun (student_guardians).
- Rapor hanya tampil bila status "terbit".
- Satu hijau primer per kartu; Bahasa Indonesia.

## Chosen direction
Dunia "Mading" (seed 75d536ba) — extension.

## Memorable moment
Lembar "Laporan Hasil Belajar" dengan kop madrasah yang bisa diunduh sebagai PDF snapshot persis.

## Unresolved
Multi-anak, filter per tahun ajaran, notifikasi tagihan (Tahap 13+).
