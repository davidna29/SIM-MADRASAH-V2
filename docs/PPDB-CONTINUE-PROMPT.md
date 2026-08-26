# Prompt Lanjutan — Modul PPDB Daring (Sesi Berikutnya)

> Salin pesan di bawah ini ke sesi baru (jangan pindah dari proyek ini).
> Setelah ditempel, beri tahu asisten hasil pengamatan Anda (bagian yang dicentang/beri masukan).

---

lanjutkan proyek SIM Madrasah. Baca penuh `docs/AI-HANDOFF.md` terutama **section 4.1 PPDB — Catatan Progress Sementara** sebelum mulai.

Kita akan lanjut menyempurnakan **modul PPDB Daring** berdasarkan pengamatan saya di sesi kemarin. Modul ini sudah berfungsi (form publik wizard 7 langkah, admin kelola pendaftar, accept/reject, generate NIS dengan acuan nomor urut, penentuan kelas via dropdown, export Excel), tapi masih perlu penyesuaian sesuai kebutuhan nyata.

## Hasil pengamatan saya sesi kemarin (dibaca & dijadikan acuan perbaikan)

[ISI DI SINI: tempel hasil pengamatan Anda di sesi ini — misal:
- `/ppdb/admin/4` tampil benar/tidak, label sudah sesuai atau belum
- Flow generate NIS (atur acuan → preview → finalisasi) mudah dipakai atau perlu diubah
- Flow penentuan kelas (buat kelas dulu → dropdown) sudah intuitif atau perlu penyesuaian
- Field form sudah cocok dengan formulir Google Form PPDB asli atau ada yang kurang/berlebih
- Hal lain yang Anda perhatikan saat mencoba alur nyata]

## Konteks yang sudah ada (JANGAN diubah tanpa alasan kuat)

- **Form publik** `/ppdb`: wizard 7 langkah (~90 field), validasi ketat, dokumen via link Google Drive.
- **Admin** `/ppdb/admin`: daftar, detail (menampilkan label pendidikan/pekerjaan/penghasilan), accept → buat Person+Student+Guardian, reject, assign kelas (dropdown kelas yang SUDAH ada di `ClassGroup`), generate NIS, export Excel.
- **Generate NIS**: NSM(12)+Tahun(2)+Nomor Urut(4) = 18 digit; ada field "Acuan Nomor Urut Terakhir" di `nis_counters`; preview sebelum finalisasi.
- **Penentuan kelas**: dropdown kelas yang sudah dibuat di Kelas & Penempatan; validasi tolak jika kelas belum ada; tampilkan jumlah siswa per kelas.
- **20 test** di `PpdbModuleTest` (total 289 test) semua passing.

## Yang perlu Anda lakukan di sesi ini

1. Baca hasil pengamatan saya (bagian di atas) — jangan berasumsi.
2. Kerjakan perbaikan/peningkatan sesuai pengamatan tersebut (frontend → persetujuan → backend → test).
3. Tunjukkan **manual test di browser** untuk setiap perubahan yang Anda buat.
4. Update `docs/AI-HANDOFF.md` section 4.1 (centang item yang sudah beres, catat temuan baru).
5. Push ke `origin/main` setelah selesai.

## Prinsip wajib (dari AGENTS.md & AI-HANDOFF)

- Disiplin modul: frontend → persetujuan → backend → test. Jangan tulis backend sebelum tampilan disetujui.
- Semua perubahan harus punya test (buat/update test, jalankan test terkait).
- Pakai komponen design system (`x-ui.*`), jangan styling ad-hoc.
- Jebakan Blade: jangan `{{ }}` di dalam tag komponen (pakai `:prop` PHP / `x-bind` Alpine); route fixed sebelum `{registration}` wildcard; `class_group_id` NOT NULL (enrollment hanya saat assign kelas); `guardians.user_id` nullable.
- Cek `.ai/rules` sebelum edit file.
