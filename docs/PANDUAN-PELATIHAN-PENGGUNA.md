# Panduan Pelatihan Pengguna — SIM Madrasah

> Modul panduan per peran untuk training pengguna internal madrasah.
> Format: cheatsheet 1-2 halaman per role.
> Target: Pengguna siap operasional ≤1 sesi pelatihan.

---

## 1. Tata Usaha / Admin

**Akses:** `super_admin`, `tata_usaha`, `kepala_madrasah`

### A. Pengaturan Awal (Pertama Kali)

1. **Login Pertama**
   - Username: `admin` (atau akun yang dibuat)
   - Password: `password` → **wajib ganti saat login pertama**
   - Akses: `/dashboard` (ringkasan madrasah)

2. **Setting Dasar**
   - Menu: **Sistem → Pengaturan Sistem**
   - Isi: Nama Madrasah, NSM, NPSN, Alamat, Logo
   - Simpan → reboot aplikasi untuk update nama

3. **Tahun Ajaran & Semester**
   - Menu: **Akademik → Tahun Ajaran & Semester**
   - Buat tahun baru: Contoh "2026/2027"
   - Set **Semester Aktif** (checkbox)
   - Wajib sebelum input data akademik lain

### B. Data Master (Setup Awal Tahun)

| Data | Lokasi | Catatan |
|------|--------|---------|
| **Guru & Pegawai** | Kepegawaian → Data Guru & Pegawai | Akun dibuat otomatis jika NIP/TTL lengkap |
| **Mata Pelajaran** | Akademik → Mata Pelajaran | Drag-drop untuk urutan rapor |
| **Kelas** | Akademik → Kelas & Penempatan | Buat per tingkat: I-A, I-B, dst |
| **Siswa Baru** | Akademik → Data Siswa → Tambah | Bisa import Excel (template di sistem) |
| **Siswa dari PPDB** | PPDB → Daftar → Terima | Data PPDD auto-convert ke master |

### C. Operasional Rutin

1. **Penempatan Kelas**
   - Akademik → Kelas & Penempatan
   - Pilih kelas → Tambah Siswa (pick dari dropdown)
   - Siswa ditampilkan di Penempatan Aktif

2. **Wali Kelas**
   - Di halaman detail Kelas → Sheet "Wali Kelas"
   - Pilih guru → Tetapkan
   - Ganti otomatis: set baru → lama otomatis selesai

3. **Mutasi Siswa**
   - **Masuk:** Akademik → Data Siswa → Mutasi Siswa Masuk
   - **Keluar:** Akademik → Data Siswa → Mutasi Siswa Keluar
   - Akun siswa nonaktif otomatis saat mutasi keluar

4. **Surat**
   - TU → Surat Masuk / Surat Keluar
   - Surat keluar auto-number: `001/TK/MM/YYYY`

5. **Backup Mingguan**
   - Pemeliharaan Sistem → Backup & Restore
   - Klik "Backup Database" + "Backup Files"
   - Download .sql & .zip → simpan di Google Drive

### D. Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Guru tidak bisa input nilai | Pastikan **Penugasan Mengajar** diisi (Akademik → Jadwal) |
| Siswa tidak muncul di kelas | Cek **Penempatan Aktif** (Akademik → Kelas & Penempatan) |
| Logo tidak berubah | Clear cache browser setelah upload logo |
| Export error | Pastikan folder `storage/app` writable |

---

## 2. Bendahara

**Akses:** `bendahara`, `tata_usaha`, `kepala_madrasah`

### A. Setup Awal Semester

1. **Setting Nominal SPP**
   - Keuangan → SPP → Nominal
   - Set nominal default per tahun ajaran
   - Contoh: 150.000 per bulan

2. **Keringanan Khusus**
   - Keuangan → SPP → Keringanan
   - Pilih siswa → atur nominal khusus
   - Bisa 0 (gratis) sampai potongan

### B. Input Pembayaran Bulanan

1. **Rekap Tunggakan**
   - Keuangan → SPP → Lihat rekap per kelas
   - Kolom status: Lunas / Cicilan / Belum

2. **Input Manual**
   - Keuangan → SPP → Bayar
   - Pilih: Siswa, Bulan (angka), Nominal
   - Status otomatis "Lunas" jika `tanggal_bayar` terisi

3. **Cicilan**
   - Input sebagian → status "Cicilan"
   - Input sisa di bulan sama → auto lunas

### C. Kwitansi & Laporan

1. **Cetak Kwitansi**
   - Setelah input → tombol "Cetak Kwitansi" otomatis
   - PDF dengan nomor transaksi & cap madrasah

2. **Laporan Per Kelas**
   - Pemeliharaan → Pusat Laporan → Keuangan
   - Filter: Tahun Ajaran, Kelas
   - Export Excel/PDF untuk arsip

### D. Tips

- **Waktu input:** Akhir bulan (25–30) untuk rekap bulan berikutnya
- **Verifikasi:** Portal orang tua bisa lihat riwayat pembayaran anak
- **Koreksi:** Hapus pembayaran salah → input ulang (audit log tercatat)

---

## 3. Guru Mata Pelajaran

**Akses:** `guru`, `wali_kelas`

### A. Penugasan Mengajar

1. **Lihat Penugasan**
   - Dashboard Guru → "Kelas yang Diajar"
   - Daftar: Kelas, Mapel, Jam per minggu

2. **Jadwal Mengajar**
   - Jadwal → Per Guru (PDF)
   - Cetak untuk tempel di kelas

### B. Input Akademik

1. **Kehadiran Siswa**
   - Kesiswaan → Kehadiran Siswa
   - Pilih kelas, tanggal hari ini
   - Input: H (Hadir), S (Sakit), I (Izin), A (Alpha)
   - **Lock:** Setelah jam tertentu hanya admin yang bisa edit

2. **Jurnal Mengajar**
   - Guru → Jurnal Mengajar
   - Pilih kelas+tanggal+jam ke
   - Isi: Materi, Tujuan, Metode, Catatan
   - Status: Draft → Submit → Terisi

3. **Input Nilai**
   - Akademik → Nilai
   - Pilih: Kelas, Mata Pelajaran (sesuai penugasan)
   - Input per siswa (0-100)
   - Status: Draft → Lengkap → Diverifikasi → Terbitkan Rapor

### C. Monitoring Siswa

1. **Per Siswa (Wali Kelas)**
   - Akademik → Data Siswa → detail siswa
   - Tab: Nilai, Kehadiran, Pelanggaran, Prestasi
   - **Portofolio:** Kesiswaan → Portofolio Digital (QR Code)

2. **Rapor**
   - Setelah semua nilai lengkap → Terbitkan Rapor
   - Rapor otomatis multi-mapel
   - Portal orang tua bisa lihat & download PDF

### D. Batasan Akses

| Yang BISA | Yang TIDAK BISA |
|-----------|-----------------|
| Input nilai kelas yang ditugaskan | Input nilai kelas lain |
| Lihat kehadiran kelas sendiri | Lihat kehadiran kelas lain |
| Edit jurnal sendiri | Edit jurnal guru lain |
| Lihat prestasi/pelanggaran siswa kelasnya | Lihat konseling BK |

---

## 4. Wali Kelas

**Akses:** `wali_kelas` (subset dari `guru`)

### A. Tugas Khusus

1. **Dashboard Kelas**
   - Buka: Akademik → Kelas & Penempatan → pilih kelas
   - Ringkasan: Jumlah siswa, Kehadiran bulan ini, Tunggakan SPP

2. **Monitoring Harian**
   - **Kehadiran:** Lihat rekap harian (H/S/I/A)
   - **Nilai:** Cek kelengkapan nilai per mapel
   - **Pelanggaran:** Input pelanggaran ringan-sedang
   - **Prestasi:** Input prestasi siswa

3. **Komunikasi Orang Tua**
   - **Portal Ortu:** Orang tua lihat data anak via login sendiri
   - **QR Code Portofolio:** Share ke WA grup kelas

### B. Rapor & Kenaikan Kelas

1. **Verifikasi Rapor**
   - Pastikan semua nilai mapel lengkap
   - Klik "Terbitkan Rapor" (bendera di dashboard)
   - Rapor terbit otomatis ke Portal Orang Tua

2. **Kenaikan Kelas (Akhir Tahun)**
   - Akademik → Kelas & Penempatan
   - Pilih siswa → Naik Kelas (buat enrollment baru)
   - **Histori:** Data lama tetap utuh, tidak dihapus

---

## 5. Kepala Madrasah

**Akses:** `kepala_madrasah`

### A. Dashboard Pimpinan

1. **Ringkasan Madrasah**
   - Siswa aktif, Guru/pegawai aktif
   - SPP terkumpul semester ini
   - % Kehadiran hari ini
   - Rombel belum review kehadiran
   - SPP belum lunas bulan berjalan

2. **Monitoring**
   - **Akademik:** Nilai belum lengkap, Rapor belum terbit
   - **Kesiswaan:** Prestasi & pelanggaran terbaru
   - **Keuangan:** Transaksi SPP terakhir
   - **SDM:** Kehadiran pegawai bulan ini

### B. Persetujuan & Review

1. **Berita & Agenda**
   - Publikasi → Berita → Review artikel
   - Approve/reject dengan komentar
   - Workflow: Draft → Review → Kepala → Publish

2. **Surat Penting**
   - TU → Surat Masuk/Keluar
   - Disposisi ke wakamad terkait

3. **Eviden PKKM** (jika modul aktif)
   - PKKM → Monitoring
   - Verifikasi kelengkapan per indikator

### C. Akses Read-Only

| Modul | Akses |
|-------|-------|
| Data Guru & Pegawai | Lihat detail, tidak bisa edit |
| Kehadiran Guru | Lihat rekap, tidak bisa input |
| Inventaris | Lihat daftar, tidak bisa mutasi |
| Perpustakaan | Lihat katalog & peminjaman |
| Konseling BK | Hanya sesi dengan level `plus_kepala` |

### D. Export Laporan

- **Bulanan:** Pemeliharaan → Pusat Laporan → pilih jenis
- **Rapat:** Export PDF rekap akademik/keuangan
- **Dinas:** Export Excel data siswa/guru (lengkap)

---

## 6. Wakil Kepala Madrasah

**Akses:** `wakamad_kurikulum`, `wakamad_kesiswaan`, `wakamad_sarpras`, `wakamad_humas`

### A. Wakamad Kurikulum

1. **Kurikulum & Jadwal**
   - Buat **Model Jadwal** baru (jika kurikulum berubah)
   - Susun jadwal master di **Penyusunan Jadwal**
   - Validasi konflik guru

2. **Monitoring Pembelajaran**
   - Akademik → Jurnal Mengajar (monitor semua guru)
   - Akademik → Jurnal Mingguan (per kelas / per guru)
   - Verifikasi nilai sebelum rapor terbit

3. **Ujian & Assesmen**
   - Ujian PPI Kelas VI: Setup periode, ruang, penguji
   - PPI & Tahfidz: Konfigurasi materi aktif per kelas

### B. Wakamad Kesiswaan

1. **Data Siswa**
   - Monitoring status siswa (aktif, mutasi, alumni)
   - Verifikasi prestasi & pelanggaran

2. **Kegiatan Siswa**
   - Ekstrakurikuler: Kelola pembina & anggota
   - Tahfidz: Monitoring setoran per siswa
   - PPI: Input nilai pembiasaan

3. **Portofolio Digital**
   - Generate QR Code per siswa
   - Verifikasi data yang tampil di portofolio

### C. Wakamad Sarpras

1. **Inventaris**
   - Kelola barang: tambah, mutasi, pemeliharaan
   - Approve mutasi barang antar ruang

2. **Ruangan & Lab**
   - Update kondisi ruangan (baik/rusak)
   - Penanggung jawab per ruang/lab

3. **Perpustakaan**
   - Monitoring peminjaman & keterlambatan
   - Approve penghapusan buku rusak

### D. Wakamad Humas

1. **Publikasi**
   - Berita & Agenda: Editor utama
   - Approve artikel sebelum publish
   - Jadwalkan publikasi

2. **Galeri & Media**
   - Upload album kegiatan madrasah
   - Atur status publik/privat

3. **PPDB**
   - Monitoring pendaftar baru
   - Approve/reject calon siswa
   - Setting buka/tutup pendaftaran

---

## 7. Petugas Khusus

### A. Guru BK (`guru_bk`)

1. **Konseling**
   - Kesiswaan → Konseling (BK) → Catat sesi
   - Level kerahasiaan: guru_bk_only / plus_kepala / plus_wali
   - Lampiran privat (hanya BK lihat)

2. **Monitoring**
   - Lihat pelanggaran semua siswa
   - Filter siswa butuh follow-up

### B. Petugas Perpustakaan (`pustakawan`)

1. **Katalog**
   - Tambah buku baru (auto-code BUK-YYYYMM-NNN)
   - Update stok tersedia

2. **Peminjaman**
   - Scan QR anggota / cari nama
   - Pilih buku → Pinjam
   - Pengembalian: scan/tap buku

3. **Ebook**
   - Tambah ebook (link eksternal)
   - Atur hak akses per siswa/pegawai

### C. Editor Berita (`editor_berita`)

1. **Workflow Artikel**
   - Draft → Ajukan → Review → Revisi → Publish
   - **Auto-publish:** Artikel terjadwal otomatis

2. **SEO & Kategori**
   - Meta title, description, keywords
   - Kategori & tag untuk organisasi

---

## 8. Pengguna Eksternal

### A. Orang Tua (`orang_tua`)

1. **Login**
   - Username: email/nomor HP (tergantung registrasi)
   - Password: default `password` → wajib ganti

2. **Data Anak**
   - Dashboard → pilih anak (jika punya >1)
   - Ringkasan: Nilai/Rapor terakhir, Kehadiran bulan ini, SPP

3. **Detail**
   - **Nilai:** Per mata pelajaran + predikat
   - **Rapor:** Download PDF (snapshot tetap)
   - **Kehadiran:** Bulanan (H/S/I/A)
   - **SPP:** Status lunas/cicilan, riwayat pembayaran

### B. Siswa (`siswa`)

1. **Portal Siswa**
   - Data Saya: Ringkasan mirip orang tua
   - **Batasan:** Hanya lihat data sendiri

2. **Fitur**
   - Jadwal Pelajaran (PDF)
   - Nilai & Rapor (read-only)
   - Kehadiran diri sendiri
   - Portofolio digital QR Code

---

## 9. Alur Darurat & Support

### A. Lupa Password

1. **Login page** → "Lupa kata sandi?"
2. Input email/username terdaftar
3. Cek email → klik link reset
4. Set password baru

### B. Akun terkunci (5x gagal login)

- Tunggu 15 menit → coba lagi
- Atau minta admin reset password

### C. Lapor Bug / Masalah

1. **Internal:** Form di Sistem → Bantuan
2. **Email:** ke admin teknis
3. **Screenshot:** sertakan URL & pesan error

### D. Backup Data (Admin)

1. **Rutin:** Setiap Jumat sore
2. **Database:** `Backup Database` → download .sql
3. **File:** `Backup Files` → download .zip
4. **Simpan:** Google Drive + harddisk eksternal

---

## 10. Tips Efisiensi

### Untuk Semua Pengguna

1. **Bookmark** halaman yang sering diakses
2. **Filter & Search** gunakan fitur pencarian di setiap tabel
3. **Export** data ke Excel jika butuh olah manual
4. **Clear cache** browser jika tampilan aneh

### Untuk Admin

1. **Seeder** gunakan `--seed` untuk data demo
2. **Activity Log** cek rutin untuk audit
3. **Cron Job** pastikan jalan (`schedule:run`)
4. **Disk usage** monitor storage setiap bulan

---

## Lampiran: Akses Cepat per Role

| Role | Menu Prioritas | URL Contoh |
|------|----------------|------------|
| Super Admin | Dashboard, Pengguna, Backup | `/dashboard`, `/fondasi/pengguna` |
| Tata Usaha | Data Siswa, Surat, SPP | `/akademik/data-siswa`, `/tu/surat` |
| Bendahara | SPP, Laporan Keuangan | `/keuangan/spp`, `/pemeliharaan/laporan` |
| Guru | Penugasan, Nilai, Jurnal | `/guru/penugasan`, `/akademik/nilai` |
| Wali Kelas | Kelas, Kehadiran, Rapor | `/akademik/kelas`, `/kesiswaan/kehadiran` |
| Kepala Madrasah | Dashboard, Berita, Laporan | `/dashboard`, `/publikasi/berita` |
| Wakamad Kurikulum | Jadwal, Jurnal, Ujian | `/akademik/jadwal-pelajaran` |
| Wakamad Kesiswaan | Prestasi, Pelanggaran, Portofolio | `/kesiswaan/prestasi` |
| Orang Tua | Ringkasan Anak, SPP | `/ortu/anak`, `/ortu/spp` |
| Siswa | Data Saya, Jadwal, Rapor | `/siswa/dashboard`, `/siswa/rapor` |

---

**Revisi:** 29 Agustus 2026  
**Target:** 1 sesi pelatihan ≤2 jam per role
