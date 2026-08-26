# Petunjuk Alur Kerja PPDB Daring — Calon Siswa → Siswa Baru

Panduan ini menjelaskan urutan penanganan pendaftar PPDB hingga menjadi siswa baru di
SIM Madrasah, serta hal-hal yang **tidak seharusnya** terjadi agar admin bisa
mengantisipasi kesalahan. Versi singkat dari panduan ini juga ditampilkan langsung
di laman admin PPDB (kartu "Alur Pengerjaan Admin").

---

## 1. Peran & Akses

| Peran | Akses | URL |
| --- | --- | --- |
| Publik (tanpa login) | Lihat landing page info / isi form pendaftaran | `/ppdb` |
| `super_admin`, `tata_usaha`, `kepala_madrasah` | Kelola pendaftar | `/ppdb/admin*` |
| `super_admin`, `tata_usaha`, `kepala_madrasah` | **Pengaturan buka/tutup + konten landing** | `/ppdb/admin/pengaturan` |

---

## 2. Buka / Tutup Pendaftaran (SAKLAR ADMIN)

Admin membuka/menutup pendaftaran via **Pengaturan PPDB** (`/ppdb/admin/pengaturan`),
saklar `Status Pendaftaran`:

- **Tutup** (default, `ppdb_status=closed`) → `/ppdb` publik menampilkan **landing page
  informasi**: jadwal/timeline, alur, syarat & ketentuan, jalur & biaya, kontak panitia,
  FAQ, countdown ke tanggal buka, dan **form pre-registrasi** (nama + WA). Form pendaftaran
  **tidak** muncul; `POST /ppdb` ditolak (pengaman lapisan kedua).
- **Buka** (`ppdb_status=open`) → `/ppdb` menampilkan form wizard 7 langkah seperti biasa.

Konten landing (tanggal, usia, dokumen, kuota, jalur, biaya, kontak, FAQ) diisi di halaman
pengaturan yang sama. Pre-registrasi disimpan ke tabel `ppdb_interests` (dedupe per nomor
WA) dan dilihat/dihapus di bagian bawah halaman pengaturan.

---

## 3. Alur Utama (berurutan) — SIMPLIFIKASI 2026

1. **Pendaftaran (publik)** — calon siswa mengisi wizard 7 langkah di `/ppdb`.
   Status awal `submitted`, sistem memberi **No. Pendaftaran** (`PPDB-YYYY-NNN`).
   Dokumen berupa *link* Google Drive (bukan unggahan file).

2. **Review & Keputusan (admin)** — di `/ppdb/admin`, klik **Detail** lalu:
   - **Terima** → status `submitted` → `accepted`. Sistem membuat
     `Person`, `Student` (**tanpa NIS**), dan `Guardian`.
   - **Tolak** → status `rejected` (wajib isi alasan).
   - **Edit** → perbaiki/perbarui seluruh field data calon, tersedia untuk
     semua status (via `/ppdb/admin/{id}/edit`).

3. **Lengkapi NIS & Kelas (di modul Data Siswa)** — PPDB **tidak lagi** mengatur
   NIS maupun kelas. Setelah diterima, operator membuka modul **Data Siswa** untuk
   melengkapi NIS dan menetapkan kelas/enrollment siswa baru.

4. **Export Excel (admin)** — di `/ppdb/admin/export`, unduh rekap kolom
   EMIS-compatible **berurutan mengikuti form** plus 5 kolom *link* Google Drive
   (KK, KK Wali, Akta, Ijazah, Foto).

---

## 4. Aturan Mutlak (anti salah)

- **Terima hanya dari `submitted`** — pendaftar dengan status lain ditolak
  (`PpdbService::accept` memeriksa `status !== 'submitted'`). Tidak ada
  double-accept yang membuat `Person`/`Student` ganda.
- **Student / Guardian baru terbuat saat Terima** — NIS & kelas dilengkapi
  belakangan di modul Data Siswa.
- **NIK unik** — `PpdbService::accept` menolak (pesan ramah) bila NIK sudah
  ada sebagai `Person` atau dipakai registrasi lain yang `accepted`.

---

## 5. Watchlist: "Tidak Seharusnya Terjadi" (risiko & antisipasi)

Temuan dari peninjauan kode — jadikan perhatian agar tidak terjadi data aneh:

1. **Menolak siswa yang sudah `accepted` + dapat kelas (di Data Siswa).**
   Belum ada guard yang melarang `reject` pada pendaftar yang sudah punya
   `Student`/`Guardian`. Akibatnya data bisa menggantung.
   *Antisipasi:* pastikan baru Tolak calon yang memang belum diproses.

2. **NIS/kelas siswa `accepted` kosong sampai diisi di Data Siswa.**
   PPDB kini tidak mengatur NIS/kelas. Setelah Terima, `students.nis` NULL dan
   belum ada enrollment sampai operator melengkapinya di modul Data Siswa.
   *Antisipasi:* pastikan operator menyelesaikan data siswa di Data Siswa sebelum
   siswa dianggap resmi (cetak/portal).

3. **NIK duplikat.** Sudah dijaga: `PpdbService::accept` menolak bila NIK sudah
   ada sebagai `Person` atau dipakai registrasi lain yang `accepted`. Data NIK
   ganda tidak bisa diterima — perbaiki NIK-nya.

4. **Assign kelas tanpa batas kapasitas (di Data Siswa).**
   Tidak ada validasi kuota rombel; satu kelas bisa diisi melebihi kapasitas.

5. **Export ikut menyertakan status `rejected`.**
   Saat ini `PpdbExport` mengecualikan hanya `draft`, sehingga `rejected` ikut
   ter-export. *Antisipasi:* filter kolom `status` di export bila hanya ingin
   yang diterima; atau setujui pembatasan di masa depan.

6. **Lupa membuka pendaftaran.** `/ppdb` tampil sebagai landing page (bukan form)
   selama `ppdb_status=closed`. *Antisipasi:* cek saklar di Pengaturan PPDB sebelum
   masa pendaftaran; jadwal di landing hanya informasi, bukan saklar otomatis.

---

## 6. Manual Test Cepat di Browser

Gunakan akun `admin` (password `password`).

| # | URL / Aksi | Yang dicek |
| --- | --- | --- |
| 1 | `/ppdb` (saat status `closed`) | Landing page info tampil (timeline, alur, syarat, FAQ), form wizard **tidak** muncul. |
| 2 | Isi pre-registrasi (nama + WA) → submit | Flash sukses; data masuk tabel minat. |
| 3 | `/ppdb/admin/pengaturan` → set `Buka` → Simpan | Saklar tersimpan; badge "Sedang Dibuka". |
| 4 | `/ppdb` (saat status `open`) | Form wizard 7 langkah tampil kembali. |
| 5 | Submit form → `/ppdb/sukses` | Nomor pendaftaran `PPDB-…` tampil. |
| 6 | `/ppdb/admin` | Kartu "Alur Pengerjaan Admin" muncul; statistik & filter jalan; tombol "Pengaturan" ada. |
| 7 | Klik **Detail** calon → Terima | Status jadi `Diterima`; kartu stepper menyorot step aktif. |
| 8 | Klik **Edit** di detail → ubah data → **Simpan** | Form edit tampil (semua field), data tersimpan; status tetap. |
| 9 | Klik **Tolak** di detail | Modal "Tolak Pendaftaran" terbuka; isi alasan → status jadi `Ditolak`. |
| 10 | `/ppdb/admin/export` | File `.xlsx` terunduh; kolom berurutan mengikuti form; 5 kolom link GDrive ada. |
| 11 | `/ppdb/admin/4` (contoh id Farhan) | Semua label pendidikan/pekerjaan/penghasilan tampil (bukan kode mentah). |
| 12 | `/ppdb/admin/pengaturan` → minat → Hapus | Minat terhapus; tabel berkurang. |
