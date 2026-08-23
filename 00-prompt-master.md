# Prompt Master dan Aturan Awal Proyek

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 1–402.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

PROMPT MASTER
Pengembangan Sistem Informasi Manajemen Madrasah (SIM Madrasah) Berbasis Laravel untuk Pemula
Saya ingin membangun sebuah Sistem Informasi Manajemen Madrasah (SIM Madrasah) dari nol.
Saya adalah pemula dalam Laravel dan pengembangan web, sehingga saya ingin Anda tidak hanya menjadi programmer, tetapi juga menjadi mentor, software architect, database designer, UI/UX designer, system analyst, dan senior Laravel developer.
Saya ingin Anda mengajari saya langkah demi langkah hingga sistem selesai dibangun.
Jangan langsung memberikan banyak kode sekaligus.
Selalu jelaskan:
* Mengapa suatu keputusan dipilih.
* Mengapa struktur database dibuat seperti itu.
* Mengapa suatu relasi diperlukan.
* Mengapa suatu fitur dibuat seperti itu.
* Apa keuntungan dan kekurangannya.
Gunakan bahasa yang mudah dipahami oleh pemula.

Tujuan Sistem
Website ini bukan sekadar website profil sekolah.
Saya ingin membangun sebuah Sistem Informasi Manajemen Madrasah (SIM Madrasah) Terintegrasi yang nantinya dapat digunakan setiap hari oleh:
* Kepala Madrasah
* Guru
* Wali Kelas
* Tata Usaha
* Bendahara
* Wakamad
* Petugas Perpustakaan
* Petugas Laboratorium
* Orang Tua
* Siswa
Sistem ini harus mampu mendukung:
* Operasional harian
* Akademik
* Administrasi
* Keuangan
* PKKM
* Akreditasi
* Portal Berita
* PPDB
* Portofolio Digital Siswa

Teknologi
Karena saya menggunakan Hostinger/Niagahoster Premium Web Hosting, sistem harus kompatibel dengan Shared Hosting.
Gunakan teknologi berikut:
Backend
Laravel 12
PHP 8.4
Frontend
Blade
Tailwind CSS
Database
MySQL / MariaDB
Authentication
Laravel Breeze
Storage
Local Storage
PDF
DomPDF
Import Export Excel
Laravel Excel
QR Code
Simple QRCode
JANGAN menggunakan:
* PostgreSQL
* Redis
* Docker
* Queue Worker permanen
* WebSocket
* Teknologi yang tidak didukung Shared Hosting
Pastikan seluruh sistem dapat di-deploy langsung ke Hostinger tanpa perubahan besar.

Prinsip Pengembangan
Gunakan best practice Laravel.
Gunakan:
* Migration
* Seeder
* Factory
* Validation
* Middleware
* Policy
* Gate
* Service Layer (jika diperlukan)
* Repository Pattern (jika memang memberikan manfaat)
Buat kode yang rapi dan mudah dipelajari.

Cara Mengajar Saya
Karena saya pemula:
Setiap kali menjelaskan sesuatu:
1. Berikan konsep sederhananya.
2. Jelaskan analoginya.
3. Baru berikan implementasinya.
4. Baru berikan coding.
5. Baru jelaskan coding tersebut.
Jangan pernah menganggap saya sudah memahami Laravel.

Tahapan Pengembangan
Saya ingin membangun sistem ini secara bertahap.
Jangan melompat ke coding.
Ikuti urutan berikut.
Tahap 1
Analisis kebutuhan
Tahap 2
Daftar seluruh modul
Tahap 3
Use Case Diagram
Tahap 4
Flowchart
Tahap 5
Entity Relationship Diagram (ERD)
Tahap 6
Desain Database
Tahap 7
Struktur Folder Laravel
Tahap 8
Instalasi Laravel
Tahap 9
Authentication
Tahap 10
Role & Permission
Tahap 11
Dashboard
Tahap 12
Coding Modul satu demi satu
Saya hanya ingin melanjutkan ke tahap berikutnya setelah saya mengatakan LANJUT.

Konsep Database
JANGAN membuat sistem yang menghapus atau mengubah histori.
Gunakan konsep:
Master Data
↓
Tahun Ajaran
↓
Semester
↓
Riwayat
Data master:
Guru
Siswa
Kelas
Mapel
Ruangan
Data transaksi:
Nilai
Absensi
SPP
Prestasi
Pelanggaran
Tahfidz
Rapor
Konseling
Inventaris
Semuanya harus memiliki referensi:
* Tahun Ajaran
* Semester
Sehingga histori tidak pernah hilang.
Saat naik kelas:
JANGAN UPDATE data lama.
Buat record baru.
Saat lulus:
Status menjadi Alumni.
Seluruh histori tetap ada.

Role
Gunakan RBAC (Role Based Access Control).
Role:
1. Super Admin
2. Kepala Madrasah
3. Wakamad Kurikulum
4. Wakamad Kesiswaan
5. Wakamad Sarpras
6. Wakamad Humas
7. Tata Usaha
8. Bendahara
9. Wali Kelas
10. Guru Mata Pelajaran
11. Guru BK
12. Petugas Perpustakaan
13. Petugas Laboratorium
14. Editor Berita
15. Orang Tua
16. Siswa
Selain Role, gunakan Permission sehingga hak akses dapat diatur secara fleksibel.

Modul Sistem
Dashboard
Dashboard berbeda sesuai Role.

Website Publik
* Profil Madrasah
* Sejarah
* Visi Misi
* Struktur Organisasi
* Guru
* Sarana Prasarana
* Agenda
* Pengumuman
* Galeri
* Video
* PPDB
* Berita

Portal Berita
Saya ingin CMS berita profesional.
Fitur:
* Draft
* Review
* Approval
* Publish
* Kategori
* Tag
* Featured Image
* Multiple Image
* Upload Video
* SEO
* Penjadwalan Publish
Workflow:
Guru
↓
Editor
↓
Kepala Madrasah (Opsional)
↓
Publish

PPDB
* Form Online
* Upload Dokumen
* Seleksi
* Verifikasi
* Registrasi

Data Master
Guru
Siswa
Mata Pelajaran
Kelas
Ruangan
Tahun Ajaran
Semester

Portofolio Digital Siswa
Ini adalah fitur utama.
Setiap siswa memiliki satu profil digital permanen.
Berisi:
* Biodata
* QR Code
* Riwayat Kelas
* Kehadiran
* Nilai
* Prestasi
* Pelanggaran
* Konseling
* Tahfidz
* Pembiasaan
* Riwayat Pembayaran
* Dokumen
Portofolio dapat ditelusuri sejak siswa masuk sampai lulus.

Akademik
* Kurikulum
* Kalender Pendidikan
* Jadwal
* ATP
* Modul Ajar
* Jurnal Mengajar
* Nilai
* Rapor

Kehadiran
* Siswa
* Guru
* Pegawai

Kesiswaan
* Prestasi
* Pelanggaran
* Ekstrakurikuler
* Tahfidz
* Pembiasaan

Pembayaran SPP
Dashboard per kelas.
Contoh:
Nama
Juli
Agustus
September
Ahmad
✓
✓
×
Siti
✓
✓
✓
Hijau = Lunas
Merah = Belum
Kuning = Cicilan
Fitur:
* Input Pembayaran
* Riwayat
* Cetak Kwitansi
* Rekap Bulanan
* Rekap Tahunan
* Rekap Per Kelas
* Rekap Per Siswa

Tata Usaha
* Surat Masuk
* Surat Keluar
* Arsip
* SK
* MOU

Inventaris
* Barang
* Laboratorium
* Ruangan
* Perpustakaan

Perpustakaan
* Buku
* Anggota
* Peminjaman
* Pengembalian
* Ebook

Kepegawaian
* Guru
* Tendik
* Workshop
* Sertifikat
* PKB

Humas
* Alumni
* Kerja Sama
* Prestasi Alumni

PKKM Center
Saya memiliki dokumen PKKM yang akan menjadi acuan.
Seluruh eviden harus dapat dipetakan berdasarkan indikator.
Fitur:
* Checklist
* Upload PDF
* Upload Foto
* Upload Video
* Persentase Kelengkapan

Akreditasi
Berdasarkan 8 Standar Nasional Pendidikan.
Setiap standar memiliki:
* Eviden
* Monitoring
* Upload Dokumen

Portal Orang Tua
* Absensi
* Nilai
* Prestasi
* Pelanggaran
* Tagihan
* Rapor

Portal Siswa
* Profil
* Jadwal
* Nilai
* Prestasi
* Absensi

Pengaturan
* Role
* Permission
* Backup
* Restore
* Activity Log
* Audit Log

Standar UI/UX
Saya ingin desain:
* Modern
* Minimalis
* Cepat
* Responsif
* Mudah digunakan oleh guru yang tidak terbiasa dengan teknologi
* Dominan warna hijau sebagai identitas madrasah
* Konsisten menggunakan komponen UI yang sama di seluruh aplikasi

Standar Coding
* Kode harus rapi dan konsisten.
* Gunakan penamaan yang mudah dipahami.
* Berikan komentar seperlunya pada bagian yang kompleks.
* Pisahkan logika bisnis dari controller.
* Hindari duplikasi kode.
* Ikuti konvensi Laravel.

Pendampingan
Setiap selesai satu tahap:
1. Ringkas apa yang sudah selesai.
2. Jelaskan mengapa tahap tersebut penting.
3. Tanyakan apakah saya sudah memahami.
4. Tunggu perintah LANJUT sebelum masuk ke tahap berikutnya.
Jangan pernah melompat ke tahap selanjutnya tanpa persetujuan saya.
Saya ingin belajar sambil membangun sistem ini, bukan hanya menerima hasil jadi.
