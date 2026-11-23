# Onboarding Data MIS Nahdlatul Ulama Palangka Raya

Log kronologis penerapan data madrasah ke SIM Madrasah V2.

## 1. Sumber data

- `DATABASE SISWA EMIS MIS NU (2026-2027).xlsx` — 521 siswa, 18 rombel (I 77, II 100, III 115, IV 83, V 89, VI 57), 98 kolom EMIS.
- `PROFIL MIS NU TAHUN 2026-2027.docx` — identitas, sejarah, visi/misi, sarpras, 152 baris prestasi.
- `KM MI NU TP. 2026-2027 REVISI2.docx` — kalender, JP mapel, ekstrakurikuler.
- Gambar `DATA PENDIDIK & TENAGA KEPENDIDIKAN`, `Struktur Organisasi`, `Struktur TUGAS TAMBAHAN` — 31 pegawai, 18 wali kelas, tugas tambahan.
- `SERTIFIKAT_AKREDITASI_PERPANJANGAN MIS NU 2024-2029.pdf` dan `OneDrive_1_11-23-2026/SK pendirian.pdf` — dokumen legalitas (hanya dilampirkan; isi tidak diparsing).
- IJOP — belum tersedia, ditampilkan “Menyusul”.

## 2. Keputusan

- Struktur & field pegawai mengikuti gambar; sumber siswa mengikuti xlsx.
- Data siswa awalnya pilot VI-A/VI-B, lalu diperluas ke **seluruh 521 siswa**.
- Tidak membuat akun login siswa.
- Foto galeri dan dokumen legalitas ditampilkan **publik**.
- Video galeri via API hanya menerima **YouTube** (Facebook/Instagram/TikTok ditolak).

## 3. Lokal (`sim_madrasah`)

- Identitas, logo, 31 pegawai, 18 rombel, 13 mapel, 16 ruang, 18 wali kelas, 9 ekskul, 7 halaman publik, kalender 2026/2027 (2 periode, 26 kegiatan).
- 57 siswa pilot VI-A/VI-B diimpor lebih dulu; kemudian **521 siswa + 941 wali** setelah perbaikan idempotensi.
- 57 foto galeri; 2 prestasi siswa aktif (sisanya alumni → `storage/app/reports/prestasi-unmatched.csv`).
- **31 foto guru** (700×700) di `/profil/guru` & `/profil/struktur-organisasi`; **hero slider** beranda berisi foto Kamad (Pramuka), guru bersama, dan album **Sorotan Madrasah** (20 gambar termasuk poster).
- Foto Kepala Madrasah (Erna, S.Ag.) di halaman `/profil/sambutan` — berkas `storage/app/public/halaman/kepala-madrasah-erna.png`, caption “Kepala Madrasah” (tanda tangan teks di body dihapus agar tidak ganda).
- Perbaikan data: NISN 9 digit dipulihkan nol depan; tanggal invalid dikosongkan + laporan; NIK kosong/ganda → NIK sintetis + flag `documents.nik_sintetis`; nilai EMIS berpenomoran (`15.Buruh …`) dinormalisasi agar muat kolom (30 karakter).
- NIK ganda di workbook: `AINUN HAYATI` (256510) & `AKHMAD BADALI` (256503) → siswa kedua memakai NIK sintetis.

## 4. Production (`misnupalangkaraya.sch.id`)

- Backup: `~/backup-sim/db/2026-11-23_082730.sql`, `files/…zip`, `.env-…`, `code/…tgz`, `public-build-…tgz`, snapshot siswa demo.
- Deploy awal lewat sinkronisasi berkas (bukan Git) → repo server dirty.
- 4 migrasi + seeder master + purge demo + 57 siswa pilot + galeri.
- Rekonsiliasi Git (Opsi A): commit & push lokal, lalu `git fetch` + `git reset --hard origin/main` + `git clean -fd public/build`; build aset ikut di-commit.
- Setelah reset (commit `3fa7d40`): **521 siswa + 941 wali** production, PDF akreditasi & SK pendirian terunggah dan tampil di `/profil` (IJOP “Menyusul”). Repo server bersih.

## 5. Perintah penting

```bash
php artisan misnu:import-siswa --rombel=all --dry-run   # tinjau laporan
php -d memory_limit=512M artisan misnu:import-siswa --rombel=all
php artisan misnu:import-galeri --approve
php artisan misnu:import-prestasi
```

Production memakai `--file=/home/u549935065/misnu-data/database-siswa-emis-2026-2027.xlsx`.

## 6. Sisa pekerjaan

- IJOP: unggah saat berkas diterima.
- Nomor/tanggal SK pendirian di field teks: cukup lampiran PDF untuk saat ini.
- 58 artikel production masih `draft`; gunakan “Setujui terpilih” (bulk; draft otomatis diajukan lalu disetujui).
- Jadwal pelajaran & penugasan mengajar 2026/2027 belum disusun.
- Email resmi masih `admin@misnu.sch.id` (domain `misnu.sch.id`, bukan `misnupalangkaraya.sch.id`).
