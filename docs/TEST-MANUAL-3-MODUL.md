# Panduan Test Manual - 3 Modul Baru

> **Prasyarat:**
> 1. Jalankan `composer serve` (buka http://localhost:8000)
> 2. Jalankan `php artisan migrate:fresh --seed` untuk DB bersih + data demo
> 3. Browser: Chrome/Edge terbaru

---

## 1. Kehadiran Guru & Pegawai (MOD-044)

### Login
| Aksi | URL | Akun | Password |
|------|-----|------|----------|
| Input kehadiran | http://localhost:8000/login | admin | password |
| Lihat saja (read-only) | http://localhost:8000/login | kepala | password |

### Test Case 1.1 - Input Harian (sebagai admin)
1. Login sebagai **admin**
2. Buka menu **Akademik - Kehadiran Guru & Pegawai** (atau langsung: http://localhost:8000/kepegawaian/kehadiran)
3. **Filter:** pilih "Unit Kerja" = Guru, atau ketik nama di kolom "Cari"
4. **Ubah tanggal** ke hari ini (atau hari lain)
5. **Ubah status** beberapa pegawai:
   - Pilih "Sakit" untuk 1 pegawai
   - Pilih "Izin" untuk 1 pegawai
   - Pilih "Terlambat" untuk 1 pegawai (isi jam masuk 07:45)
   - Biarkan sisanya "Hadir" (default)
6. **Isi jam masuk/keluar** untuk pegawai yang "Hadir" (mis. 07:15 / 13:45)
7. **Isi catatan** untuk yang "Sakit" (mis. "Sakit demam")
8. Klik **"Simpan Kehadiran"**
9. Harusnya: pesan sukses "Kehadiran guru & pegawai berhasil disimpan."

### Test Case 1.2 - Rekap Bulanan
1. Dari halaman input, klik tombol **"Rekap Bulanan"** (atas kanan)
2. Atau buka langsung: http://localhost:8000/kepegawaian/kehadiran/rekap-bulanan
3. **Filter:** pilih bulan yang sudah diisi datanya + unit "Guru"
4. Klik **"Tampilkan"**
5. Harusnya: tabel matrix pegawai x tanggal, dengan tanda: H (Hadir), I (Izin), S (Sakit), T (Terlambat), dash (belum ada catatan)
6. Harusnya: kolom "Jumlah" dan "% Hadir" terisi
7. Klik tombol **"Cetak"** -> print preview muncul

### Test Case 1.3 - Rekap Tahunan
1. Buka: http://localhost:8000/kepegawaian/kehadiran/rekap-tahunan
2. Pilih tahun **2026**
3. Klik **"Tampilkan"**
4. Harusnya: tabel pegawai x 12 bulan, sel menampilkan "Hadir/Hari Tercatat" (mis. "12/14")
5. Harusnya: kolom Total Hadir, Ketidakhadiran, % Kehadiran terisi

### Test Case 1.4 - RBAC (Kepala Madrasah)
1. Logout, login sebagai **kepala** (kepala / password)
2. Buka: http://localhost:8000/kepegawaian/kehadiran
3. Harusnya: halaman tampil (bisa lihat data)
4. Isi status pegawai, klik Simpan
5. Harusnya: **403 Forbidden** (kepala tidak bisa input)

### Test Case 1.5 - Koreksi Tanggal Lampau
1. Login sebagai **admin**
2. Buka http://localhost:8000/kepegawaian/kehadiran
3. Ubah tanggal ke **1 Januari 2025** (tanggal jauh lampau)
4. Isi kehadiran 1 pegawai, simpan
5. Harusnya: berhasil disimpan (TU/Admin bisa koreksi tanggal lampau)

---

## 2. Ruangan & Laboratorium (MOD-047/050)

### Login
| Aksi | URL | Akun | Password |
|------|-----|------|----------|
| Kelola ruangan | http://localhost:8000/login | admin | password |

### Test Case 2.1 - Lihat Daftar Ruangan
1. Login sebagai **admin**
2. Buka menu **Sarpras & Perpustakaan - Ruangan & Lab - Daftar Ruangan**
   Atau: http://localhost:8000/sarpras/ruangan?type=ruangan
3. Harusnya: tabel menampilkan ruangan dari seeder (R-001 sampai R-008)
4. Harusnya: kolom Kondisi menampilkan badge warna (hijau=Baik, kuning=Rusak Ringan, merah=Rusak Berat, biru=Dalam Perbaikan)

### Test Case 2.2 - Filter & Cari
1. Di halaman Daftar Ruangan, pilih **Gedung** = "Gedung Utama"
2. Klik **"Terapkan"**
3. Harusnya: hanya ruangan dari Gedung Utama yang tampil
4. Klik **"Hapus Filter"**
5. Harusnya: semua ruangan tampil lagi
6. Ketik "Lab" di kolom **Cari**, klik Terapkan
7. Harusnya: hanya ruangan yang namanya mengandung "Lab" tampil

### Test Case 2.3 - Tambah Ruangan Baru
1. Klik tombol **"Tambah Ruangan"**
2. Isi form:
   - Nama: "Ruang Musik"
   - Jenis: "Ruangan"
   - Gedung: "Gedung C"
   - Lantai: "Lantai 1"
   - Kapasitas: 20
   - Kondisi: "Baik"
3. Klik **"Simpan Ruangan"**
4. Harusnya: redirect ke halaman detail ruangan baru
5. Harusnya: kode otomatis "R-012" (lanjutan dari seeder)

### Test Case 2.4 - Lihat Detail Ruangan
1. Klik nama ruangan di tabel (mis. "Lab IPA")
2. Harusnya: halaman detail menampilkan: Kode, Jenis, Gedung, Lantai, Kapasitas, Penanggung Jawab, Kondisi

### Test Case 2.5 - Ubah Ruangan
1. Dari halaman detail, klik tombol **"Ubah"**
2. Ubah Kapasitas dari 30 -> 40
3. Klik **"Simpan Perubahan"**
4. Harusnya: pesan sukses, kapasitas ter-update

### Test Case 2.6 - Hapus Ruangan
1. Dari halaman detail, klik tombol **"Hapus"** (merah)
2. Harusnya: muncul modal konfirmasi "Apakah Anda yakin?"
3. Klik **"Hapus"** di modal
4. Harusnya: redirect ke index, ruangan tidak ada lagi

### Test Case 2.7 - Lihat Laboratorium
1. Buka menu **Sarpras & Perpustakaan - Ruangan & Lab - Laboratorium**
   Atau: http://localhost:8000/sarpras/ruangan?type=laboratorium
2. Harusnya: hanya ruangan tipe "Laboratorium" yang tampil (Lab IPA, Lab Komputer, Lab Bahasa)

### Test Case 2.8 - RBAC
1. Login sebagai **guru** (guru.umar / password)
2. Buka: http://localhost:8000/sarpras/ruangan
3. Harusnya: **403 Forbidden**

---

## 3. Struktur Organisasi (MOD-004)

### Login
| Aksi | URL | Akun | Password |
|------|-----|------|----------|
| Kelola struktur | http://localhost:8000/login | admin | password |
| Lihat saja | http://localhost:8000/login | kepala | password |

### Test Case 3.1 - Lihat Daftar Unit Kerja
1. Login sebagai **admin**
2. Buka menu **Sistem - Struktur Organisasi - Unit Kerja**
   Atau: http://localhost:8000/fondasi/unit-kerja
3. Harusnya: tabel menampilkan unit dari seeder: Pimpinan, Kurikulum, Kesiswaan, Sarpras, Humas, Tata Usaha, Guru, Perpustakaan, Laboratorium
4. Harusnya: kolom "Pegawai" menampilkan jumlah pegawai di tiap unit

### Test Case 3.2 - Tambah Unit Kerja
1. Klik **"Tambah Unit"**
2. Isi: Kode = "BENDAHARA", Nama = "Keuangan"
3. Klik **"Simpan"**
4. Harusnya: redirect ke index, unit baru muncul di tabel

### Test Case 3.3 - Detail Unit Kerja
1. Klik nama unit (mis. "Guru")
2. Harusnya: halaman detail menampilkan daftar pegawai di unit tersebut (nama, NIP, jabatan, status)

### Test Case 3.4 - Ubah Unit Kerja
1. Dari halaman detail unit, klik **"Ubah"**
2. Ubah nama unit
3. Klik **"Simpan"**
4. Harusnya: nama ter-update

### Test Case 3.5 - Hapus Unit (yang kosong)
1. Buat unit baru (mis. "TEST" / "Test Unit")
2. Pastikan tidak ada pegawai di unit itu
3. Klik tombol **"Hapus"** (ikon tempat sampah) di tabel
4. Harusnya: muncul modal konfirmasi, hapus berhasil

### Test Case 3.6 - Gagal Hapus Unit (yang punya pegawai)
1. Coba lihat unit "Guru" atau "Pimpinan" (pasti ada pegawai)
2. Harusnya: **tidak ada tombol hapus** (hidden untuk unit yang punya pegawai)

### Test Case 3.7 - Lihat Jabatan
1. Buka menu **Sistem - Struktur Organisasi - Jabatan**
   Atau: http://localhost:8000/fondasi/jabatan
2. Harusnya: tabel jabatan dari seeder: Kepala Madrasah, Wakamad Kurikulum, Guru Mata Pelajaran, dll
3. Klik nama jabatan -> halaman detail dengan daftar pegawai

### Test Case 3.8 - Tambah & Hapus Jabatan
1. Tambah jabatan: Kode = "OPERATOR", Nama = "Operator Komputer"
2. Harusnya: berhasil
3. Hapus jabatan yang baru dibuat (pasti kosong)
4. Harusnya: berhasil

### Test Case 3.9 - Struktur Organisasi (Read-only)
1. Buka menu **Sistem - Struktur Organisasi - Struktur**
   Atau: http://localhost:8000/fondasi/struktur
2. Harusnya: halaman menampilkan semua unit kerja, masing-masing di bawahnya daftar pegawai (nama, NIP, jabatan, status)
3. Harusnya: hanya unit yang punya pegawai aktif yang tampil

### Test Case 3.10 - RBAC
1. Login sebagai **kepala** (kepala / password)
2. Buka: http://localhost:8000/fondasi/unit-kerja
3. Harusnya: halaman tampil (bisa lihat)
4. Klik "Tambah Unit"
5. Harusnya: **403 Forbidden** (kepala tidak bisa create)
6. Login sebagai **guru** (guru.umar / password)
7. Buka: http://localhost:8000/fondasi/struktur
8. Harusnya: **403 Forbidden**

---

## Checklist Keseluruhan

| No | Modul | Test | Status |
|----|-------|------|--------|
| 1.1 | Kehadiran Pegawai | Input harian | [ ] |
| 1.2 | Kehadiran Pegawai | Rekap bulanan | [ ] |
| 1.3 | Kehadiran Pegawai | Rekap tahunan | [ ] |
| 1.4 | Kehadiran Pegawai | RBAC kepala | [ ] |
| 1.5 | Kehadiran Pegawai | Koreksi tanggal lampau | [ ] |
| 2.1 | Ruangan & Lab | Lihat daftar | [ ] |
| 2.2 | Ruangan & Lab | Filter & cari | [ ] |
| 2.3 | Ruangan & Lab | Tambah ruangan | [ ] |
| 2.4 | Ruangan & Lab | Detail ruangan | [ ] |
| 2.5 | Ruangan & Lab | Ubah ruangan | [ ] |
| 2.6 | Ruangan & Lab | Hapus ruangan | [ ] |
| 2.7 | Ruangan & Lab | Lihat lab | [ ] |
| 2.8 | Ruangan & Lab | RBAC guru | [ ] |
| 3.1 | Struktur Org | Lihat unit kerja | [ ] |
| 3.2 | Struktur Org | Tambah unit | [ ] |
| 3.3 | Struktur Org | Detail unit | [ ] |
| 3.4 | Struktur Org | Ubah unit | [ ] |
| 3.5 | Struktur Org | Hapus unit kosong | [ ] |
| 3.6 | Struktur Org | Gagal hapus unit berisi | [ ] |
| 3.7 | Struktur Org | Lihat jabatan | [ ] |
| 3.8 | Struktur Org | Tambah/hapus jabatan | [ ] |
| 3.9 | Struktur Org | Struktur read-only | [ ] |
| 3.10 | Struktur Org | RBAC | [ ] |
