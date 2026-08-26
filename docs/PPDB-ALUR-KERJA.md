# Petunjuk Alur Kerja PPDB Daring — Calon Siswa → Siswa Baru

Panduan ini menjelaskan urutan penanganan pendaftar PPDB hingga menjadi siswa baru di
SIM Madrasah, serta hal-hal yang **tidak seharusnya** terjadi agar admin bisa
mengantisipasi kesalahan. Versi singkat dari panduan ini juga ditampilkan langsung
di laman admin PPDB (kartu "Alur Pengerjaan Admin").

---

## 1. Peran & Akses

| Peran | Akses | URL |
| --- | --- | --- |
| Publik (tanpa login) | Isi formulir pendaftaran | `/ppdb` |
| `super_admin`, `tata_usaha`, `kepala_madrasah` | Kelola pendaftar | `/ppdb/admin*` |

---

## 2. Alur Utama (berurutan)

1. **Pendaftaran (publik)** — calon siswa mengisi wizard 7 langkah di `/ppdb`.
   Status awal `submitted`, sistem memberi **No. Pendaftaran** (`PPDB-YYYY-NNN`).
   Dokumen berupa *link* Google Drive (bukan unggahan file).

2. **Review & Keputusan (admin)** — di `/ppdb/admin`, klik **Detail** lalu:
   - **Terima** → status `submitted` → `accepted`. Sistem langsung membuat
     `Person`, `Student` (tanpa NIS dulu), dan `Guardian`.
   - **Tolak** → status `rejected` (wajib isi alasan).

3. **Generate NIS (admin)** — di `/ppdb/admin/generate-nis`, operator melihat
   daftar calon `accepted` yang **belum punya NIS**, terurut **abjad**
   (`UPPER(name)`). Field "Acuan Nomor Urut Terakhir" menentukan titik awal
   penomoran (untuk siswa pindahan di luar alur PPDB). Klik **Finalisasi NIS**
   untuk menyimpan NIS massal: format `NSM(12) + Tahun(2) + Nomor Urut(4) = 18 digit`,
   counter `nis_counters` berlanjut. NIS juga disalin ke kolom `students.nis`.

4. **Penentuan Kelas (admin)** — di `/ppdb/admin/assign-class`, sebar calon siswa
   `accepted` ke rombel yang **sudah ada** di menu Kelas & Penempatan. Enrollment
   (`student_enrollments`) baru terbuat di tahap ini (karena `class_group_id` NOT NULL).

5. **Export Excel (admin)** — di `/ppdb/admin/export`, unduh rekap kolom
   EMIS-compatible **berurutan mengikuti form** plus 5 kolom *link* Google Drive
   (KK, KK Wali, Akta, Ijazah, Foto).

---

## 3. Aturan Mutlak (anti salah)

- **Terima hanya dari `submitted`** — pendaftar dengan status lain ditolak
  (`PpdbService::accept` memeriksa `status !== 'submitted'`). Tidak ada
  double-accept yang membuat `Person`/`Student` ganda.
- **Student / Guardian / Enrollment baru terbuat saat Terima** — NIS dan kelas
  menyusul di langkah 3 & 4.
- **Kelas wajib sudah ada** — dropdown diisi dari `ClassGroup`; kelas yang diketik
  bebas akan ditolak ("kelas belum ada, buat dulu di Kelas & Penempatan").
- **`class_group_id` NOT NULL** — enrollment siswa PPDB hanya lahir saat assign
  kelas, bukan saat accept.
- **NIS unik 18 digit** — di-generate dari counter berlanjut agar tidak bentrok.

---

## 4. Watchlist: "Tidak Seharusnya Terjadi" (risiko & antisipasi)

Temuan dari peninjauan kode — jadikan perhatian agar tidak terjadi data aneh:

1. **Menolak siswa yang sudah `accepted` + dapat kelas.**
   Belum ada guard yang melarang `reject` pada pendaftar yang sudah punya
   `Student`/`Guardian`/`StudentEnrollment`. Akibatnya data menggantung.
   *Antisipasi:* pastikan baru Tolak calon yang memang belum diproses; jika sudah
   lanjut ke kelas, batalkan via hapus enrollment dulu, bukan sekadar reject.

2. **Ganti Tahun Ajaran aktif di tengah proses.**
   Halaman Generate NIS & Assign Class memfilter `AcademicYear::active()`.
   Jika TA aktif diganti, calon `accepted` dari TA lama akan **hilang** dari
   kedua halaman tersebut. *Antisipasi:* selesaikan finalisasi NIS & penentuan
   kelas dalam satu TA, jangan ganti TA di tengah penerimaan.

3. **"Acuan Nomor Urut" tumpang tindih dengan NIS existing.**
   `students.nis` unik. Jika Acuan diatur sedemikian hingga NIS yang dihasilkan
   sama dengan NIS siswa lama, `commitNis` gagal. *Antisipasi:* pastikan Acuan
   lebih besar dari NIS terakhir yang sudah ada.

4. **Assign kelas tanpa batas kapasitas.**
   Tidak ada validasi kuota rombel; satu kelas bisa diisi melebihi kapasitas.
   *Antisipasi:* gunakan panel "Distribusi per Kelas" / fitur "Sebar Rata" dan
   periksa badge jumlah siswa sebelum menyimpan.

5. **Export ikut menyertakan status `rejected`.**
   Saat ini `PpdbExport` mengecualikan hanya `draft`, sehingga `rejected` ikut
   ter-export. *Antisipasi:* filter kolom `status` di export bila hanya ingin
   yang diterima; atau setujui pembatasan di masa depan.

6. **NIS belum difinalisasi tapi sudah dianggap siswa.**
   Setelah Terima, `students.nis` NULL sampai Finalisasi NIS. Fitur lain yang
   mengandalkan NIS (cetak, portal) sebaiknya menunggu tahap 3 selesai.
   *Antisipasi:* pastikan Finalisasi NIS dilakukan sebelum siswa dianggap resmi.

---

## 5. Manual Test Cepat di Browser

Gunakan akun `admin` (password `password`).

| # | URL / Aksi | Yang dicek |
| --- | --- | --- |
| 1 | `/ppdb` | Form 7 langkah tampil, validasi NIK 16 digit & RT/RW 3 digit jalan. |
| 2 | Submit form → `/ppdb/sukses` | Nomor pendaftaran `PPDB-…` tampil. |
| 3 | `/ppdb/admin` | Kartu "Alur Pengerjaan Admin" muncul; statistik & filter jalan. |
| 4 | Klik **Detail** calon → Terima | Status jadi `Diterima`; kartu stepper menyorot step aktif. |
| 5 | `/ppdb/admin/generate-nis` | Calon muncul urut abjad; "Acuan" tersimpan; **Finalisasi NIS** → NIS 18 digit muncul & tersalin ke Data Siswa. |
| 6 | `/ppdb/admin/assign-class` | Checklist + dropdown kelas; "Tetapkan Kelas Terpilih" & "Sebar Rata" jalan; badge jumlah siswa per kelas update. |
| 7 | `/ppdb/admin/export` | File `.xlsx` terunduh; kolom berurutan mengikuti form; 5 kolom link GDrive ada. |
| 8 | `/ppdb/admin/4` (contoh id Farhan) | Semua label pendidikan/pekerjaan/penghasilan tampil (bukan kode mentah). |
