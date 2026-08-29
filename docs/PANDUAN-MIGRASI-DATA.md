# Panduan Migrasi Data dari Excel — SIM Madrasah

> Prosedur migrasi data dari sistem lama (Excel/Google Sheets) ke SIM Madrasah.
> Target: Data master awal + transaksi berjalan tanpa kehilangan histori.

---

## 1. Prinsip Migrasi

1. **Urutan Wajib:** Master → Transaksi → Histori
2. **Validasi Ketat:** Setiap baris dicek sebelum masuk database
3. **Preview & Approval:** Data ditampilkan sebelum konfirmasi
4. **Rollback Aman:** Bisa batalkan batch import tanpa merusak data lama
5. **Audit Trail:** Setiap import tercatat di activity log

---

## 2. Persiapan Data Sumber

### 2.1 Template Excel Standar

| Data | Template | Lokasi Download |
|------|----------|-----------------|
| **Guru & Pegawai** | `template-guru.xlsx` | Kepegawaian → Data Guru → Import |
| **Siswa** | `template-siswa.xlsx` | Akademik → Data Siswa → Import |
| **Mata Pelajaran** | `template-mapel.xlsx` | Akademik → Mata Pelajaran → Import |
| **Nilai** | `template-nilai.xlsx` | Akademik → Nilai → Import |
| **Prestasi** | `template-prestasi.xlsx` | Kesiswaan → Prestasi → Import |

### 2.2 Format Umum

- **Encoding:** UTF-8 (support karakter Indonesia: é, ŭ, dll)
- **Tanggal:** `YYYY-MM-DD` (contoh: `2018-05-20`) atau `DD/MM/YYYY`
- **Angka:** Tanpa separator ribuan (contoh: `150000` bukan `150.000`)
- **Gender:** `L` atau `P`
- **NIK/NISN:** 16 digit (NIK), 10 digit (NISN) tanpa spasi

### 2.3 Pembersihan Data (Pre-Cleaning)

**Sebelum Import:**

1. **Deduplikasi** → Hapus baris ganda (cek NIK, NISN, nama lengkap)
2. **Validasi Format** → Tanggal, angka, gender sesuai standar
3. **Konsistensi** → Nama kelas seragam (I-A, I-B, bukan "1A", "1 A")
4. **Relasi** → Pastikan NIS siswa ada di data master siswa (untuk nilai/prestasi)

---

## 3. Urutan Migrasi

### Fase 1: Fondasi (Wajib Pertama)

| No | Data | Ketergantungan | Estimasi Waktu |
|----|------|----------------|----------------|
| 1 | Tahun Ajaran & Semester | - | 5 menit (manual) |
| 2 | Unit Kerja & Jabatan | - | 10 menit (manual/import) |
| 3 | Mata Pelajaran | - | 10 menit (import) |
| 4 | Ruangan & Lab | - | 10 menit (import/manual) |
| 5 | Kategori Inventaris | - | 5 menit (manual) |
| 6 | Kategori Perpustakaan | - | 5 menit (manual) |

### Fase 2: Data Induk Orang

| No | Data | Ketergantungan | Estimasi Waktu |
|----|------|----------------|----------------|
| 7 | Guru & Pegawai | Unit Kerja, Jabatan | 30 menit (import) |
| 8 | Siswa | - | 45 menit (import) |
| 9 | Orang Tua/Wali | Siswa | 30 menit (import/manual) |

### Fase 3: Penugasan Akademik

| No | Data | Ketergantungan | Estimasi Waktu |
|----|------|----------------|----------------|
| 10 | Kelas & Rombel | Tahun Ajaran, Ruangan | 20 menit (manual/import) |
| 11 | Penempatan Siswa | Siswa, Kelas | 30 menit (import) |
| 12 | Wali Kelas | Guru, Kelas | 10 menit (manual) |
| 13 | Penugasan Mengajar | Guru, Mapel, Kelas | 20 menit (import/manual) |
| 14 | Jadwal Pelajaran | Penugasan, Model Jadwal | 30 menit (manual UI) |

### Fase 4: Transaksi Berjalan

| No | Data | Ketergantungan | Estimasi Waktu |
|----|------|----------------|----------------|
| 15 | Nilai Siswa | Penempatan, Mapel | 60 menit (import) |
| 16 | Kehadiran Siswa | Penempatan | 30 menit (import/manual) |
| 17 | Prestasi | Siswa | 20 menit (import) |
| 18 | Pelanggaran | Siswa | 20 menit (import/manual) |
| 19 | SPP & Pembayaran | Penempatan | 45 menit (import/manual) |

### Fase 5: Data Pendukung

| No | Data | Ketergantungan | Estimasi Waktu |
|----|------|----------------|----------------|
| 20 | Inventaris Barang | Kategori, Ruangan | 30 menit (import) |
| 21 | Katalog Perpustakaan | Kategori | 30 menit (import) |
| 22 | Anggota Perpustakaan | Siswa, Guru | 15 menit (import/manual) |
| 23 | Surat Masuk/Keluar | - | 30 menit (import/manual) |

---

## 4. Langkah Import per Data

### 4.1 Guru & Pegawai

**Menu:** `Kepegawaian → Data Guru & Pegawai → Import`

**Kolom Wajib:**
- `nip` (16-18 digit, boleh kosong untuk honorer)
- `nama` (lengkap tanpa gelar)
- `nik` (16 digit, wajib)
- `tanggal_lahir` (YYYY-MM-DD)
- `jenis_kelamin` (L/P)
- `status` (Aktif/Nonaktif/Cuti)
- `unit_kerja` (kode: `GURU`, `TU`, `KEPALA`, dll)
- `jabatan` (kode: `GURU_MAPEL`, `KEPALA`, `WAKAMAD_KURIKULUM`, dll)

**Proses:**
1. Download template → isi data
2. Upload → Preview (sistem validasi per baris)
3. **Error ditandai merah** → perbaiki di Excel, upload ulang
4. Valid → Klik "Simpan" → data masuk database
5. **Akun otomatis dibuat** jika NIP + TTL lengkap

**Catatan:**
- Jika NIK duplikat → error (cek data lama)
- Jika NIP duplikat → skip atau update (pilih saat preview)

### 4.2 Siswa

**Menu:** `Akademik → Data Siswa → Import`

**Kolom Wajib:**
- `nis` (unique, bisa NISM/ lokal)
- `nisn` (10 digit, boleh kosong untuk baru)
- `nama` (lengkap)
- `tanggal_lahir` (YYYY-MM-DD)
- `jenis_kelamin` (L/P)
- `kelas` (format: I-A, II-B, dst)
- `status` (Aktif/Alumni/Keluar)

**Proses:**
1. Download template → isi data
2. Upload → Preview
3. Validasi: NIS unik, format kelas benar
4. Simpan → **Person + Student + Enrollment** dibuat otomatis

**Catatan:**
- Kelas harus sudah ada di master (Fase 3)
- Jika kelas belum ada → error "Kelas tidak ditemukan"

### 4.3 Nilai Siswa

**Menu:** `Akademik → Nilai → Import`

**Kolom Wajib:**
- `nis` (siswa target)
- `mata_pelajaran` (kode: `PAI`, `PKn`, `MTK`, dst)
- `semester` (Ganjil/Genap)
- `tahun_ajaran` (contoh: `2025/2026`)
- `nilai` (0-100)

**Proses:**
1. Download template → isi nilai per siswa
2. Upload → Preview (cek NIS valid, mapel ada)
3. Simpan → **Score** dibuat, status `draft`

**Catatan:**
- Nilai bisa diimpor per kelas atau massal
- Status draft → bisa edit sebelum terbit rapor

---

## 5. Validasi & Quality Assurance

### 5.1 Validasi per Batch

Setelah import, jalankan validasi:

**Menu:** `Pemeliharaan → Pusat Laporan → Periksa Data`

| Cek | Hasil Yang Diharapkan |
|-----|----------------------|
| Jumlah siswa | Sama dengan Excel sumber |
| NIS duplikat | 0 |
| Siswa tanpa kelas | 0 (kecuali alumni/baru) |
| Nilai per mapel | Sesuai jumlah siswa × mapel |
| SPP total | Sesuai rekap Excel bendahara |

### 5.2 Reconciliation Report

Buat laporan perbandingan:

1. **Jumlah Record**
   - Excel: 250 siswa
   - Database: 250 siswa → ✅ Match

2. **Sample Check**
   - Ambil 10% data random
   - Bandingkan field per field dengan sumber
   - Tolerasi error: 0% untuk data kritis (NIK, NIS, nilai)

3. **Cross-Reference**
   - Siswa di kelas = siswa di enrollment
   - Nilai siswa = siswa di master
   - Pembayaran = siswa di enrollment

---

## 6. Rollback & Error Handling

### 6.1 Jika Import Gagal di Tengah Jalan

**Opsi 1: Batalkan Batch**
- Menu Import → Riwayat Import → Batalkan batch terakhir
- Data batch tersebut dihapus, data sebelumnya tetap

**Opsi 2: Restore Backup**
- Pemeliharaan → Backup & Restore
- Restore database dari backup sebelum import
- **Hati-hati:** Semua perubahan setelah backup akan hilang

### 6.2 Error Umum & Solusi

| Error | Penyebab | Solusi |
|-------|----------|--------|
| `NIK sudah ada` | Duplikat dengan data lama | Cek NIK di database, hapus/update di Excel |
| `Kelas tidak ditemukan` | Nama kelas belum dibuat | Buat kelas dulu di menu Kelas |
| `Format tanggal salah` | Tidak sesuai YYYY-MM-DD | Ubah format di Excel, upload ulang |
| `NIS duplikat` | Dua siswa punya NIS sama | Perbaiki NIS di Excel |
| `Foreign key constraint` | Relasi ke data belum ada | Import data master dulu |
| `Nilai di luar range` | Nilai < 0 atau > 100 | Koreksi nilai di Excel |

---

## 7. Timeline Migrasi (Saran)

### Minggu 1: Persiapan
- Hari 1-2: Audit data Excel (cleaning, deduplikasi)
- Hari 3-4: Setup staging, import master (Fase 1-2)
- Hari 5: Validasi data master

### Minggu 2: Data Akademik
- Hari 1-2: Import kelas, penempatan, penugasan (Fase 3)
- Hari 3-4: Import nilai, kehadiran (Fase 4)
- Hari 5: Validasi akademik

### Minggu 3: Transaksi & Pendukung
- Hari 1-2: Import SPP, prestasi, pelanggaran (Fase 4-5)
- Hari 3: Import inventaris, perpustakaan
- Hari 4-5: Reconciliation report, perbaikan

### Minggu 4: Paralel & Cut-Over
- Hari 1-3: Periode paralel (Excel + sistem, sistem sebagai sumber resmi)
- Hari 4: Training pengguna
- Hari 5: Go-live, monitoring

---

## 8. Checklist Cut-Over

**Sebelum Go-Live:**

- [ ] Semua data master terimport
- [ ] Nilai tahun berjalan lengkap
- [ ] SPP terkonfirmasi benar
- [ ] Backup database terakhir dibuat
- [ ] Pengguna sudah dilatih
- [ ] Portal orang tua/siswa diinformasikan
- [ ] Support channel aktif (WA grup, email)

**Saat Go-Live:**

- [ ] Maintenance mode ON
- [ ] Restore backup terakhir (jika perlu)
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Maintenance mode OFF
- [ ] Announcement ke semua pengguna

**Setelah Go-Live:**

- [ ] Monitoring log error (daily)
- [ ] Cek aktivitas pengguna (activity log)
- [ ] Siap siaga 1 minggu penuh
- [ ] Evaluasi 1 bulan pasca go-live

---

## 9. Prosedur Paralel (Transisi Aman)

**Durasi:** 2-4 minggu sebelum go-live

**Aturan:**
1. **Sistem resmi** → semua input wajib ke SIM
2. **Excel backup** → hanya untuk keadaan darurat
3. **Rekonsiliasi harian** → bandingkan total transaksi
4. **Error cepat diperbaiki** → koordinasi via WA grup

**Kapan hentikan paralel:**
- Tidak ada error kritis selama 1 minggu
- Pengguna sudah nyaman dengan sistem
- Rekonsiliasi 100% match 3 hari berturut-turut

---

## 10. Support Migrasi

### 10.1 Tim Internal

- **Koordinator:** TU/Kepala Madrasah
- **Data Entry:** Operator terlatih
- **Validasi:** Wakamad terkait

### 10.2 Bantuan Teknis

- **Issue:** Buat ticket di sistem atau email ke admin teknis
- **Data error:** Kirim screenshot + file Excel bermasalah
- **Training:** Minta sesi tambahan jika butuh

### 10.3 Dokumentasi

Simpan file berikut di Google Drive bersama:
- Template Excel (bersih)
- Mapping field (Excel → database)
- Riwayat import (batch ID, tanggal, jumlah record)
- Backup database (mingguan)

---

## Lampiran: Contoh Mapping Field

### Data Siswa

| Field Excel | Field Database | Validasi |
|-------------|----------------|----------|
| `NIS` | `students.nis` | required, unique |
| `NISN` | `students.nisn` | 10 digit, nullable |
| `Nama` | `students.name` | required |
| `Jenis Kelamin` | `people.gender` | L/P |
| `Tempat Lahir` | `people.birth_place` | - |
| `Tanggal Lahir` | `people.birth_date` | YYYY-MM-DD |
| `Agama` | `people.religion` | Islam/Kristen/... |
| `Alamat` | `people.address` | - |
| `Kelas` | `class_groups.name` | wajib ada di master |
| `Status` | `student_enrollments.status` | aktif/alumni/keluar |

### Data Nilai

| Field Excel | Field Database | Validasi |
|-------------|----------------|----------|
| `NIS` | `student_enrollment.student.nis` | must exist |
| `Mapel` | `subjects.code` | must exist |
| `Semester` | `scores.semester` | Ganjil/Genap |
| `Tahun Ajaran` | `academic_years.name` | must exist |
| `Nilai` | `scores.score` | 0-100 |

---

**Revisi:** 29 Agustus 2026  
**Estimasi Total Migrasi:** 3-4 minggu (termasuk paralel)
