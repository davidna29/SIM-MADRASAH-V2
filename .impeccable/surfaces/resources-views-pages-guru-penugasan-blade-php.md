---
version: 1
slug: "resources-views-pages-guru-penugasan-blade-php"
primary_target: "resources/views/pages/guru/penugasan.blade.php"
related_targets: []
---

# Surface brief — Guru: Penugasan → Nilai → Rapor (Tahap 12)

## Scope
Walking skeleton sisi guru: daftar penugasan mengajar, input nilai per kelas/mapel, terbitkan rapor, lihat & unduh snapshot. Mode: Operate.

## Audience & job
Guru mata pelajaran mengisi nilai kelas/mapel yang menjadi penugasannya dan menerbitkan rapor.

## Task
Pilih penugasan → input nilai 0–100 (predikat A–E otomatis) → simpan → terbitkan rapor → lihat/unduh PDF.

## Constraints
- Hanya kelas & mapel penugasan sendiri (lapisan penugasan, bukan sekadar role).
- Snapshot rapor tidak pernah ditimpa — tiap penerbitan = versi baru.
- Satu hijau primer per kartu; angka tabular mono; Bahasa Indonesia.

## Chosen direction
Dunia "Mading" (seed 75d536ba) — extension, bukan dunia baru.

## Memorable moment
"Terbitkan Rapor Kelas Ini" memunculkan lembar rapor terbit (v1, v2, …) di papan "Rapor Terbit" yang bisa dilihat & diunduh.

## Unresolved
Rapor penuh multi-mapel & kenaikan kelas (Tahap 13); jadwal/jurnal belum dibangun.
