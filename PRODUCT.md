# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

Greenfield — belum ada kode di repositori ini. Stack didefinisikan oleh PRD v2.1 (dokumen otoritatif proyek, bukan keputusan baru):

- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Blade + Tailwind CSS, dibangun sebagai design system (komponen reusable) sejak awal
- **Database:** MySQL 8.4 LTS / MariaDB (utf8mb4, InnoDB)
- **Autentikasi:** Laravel Breeze (login username/email)
- **RBAC:** `spatie/laravel-permission` (fondasi) + lapisan penugasan custom (spesifik madrasah)
- **Audit log:** `spatie/laravel-activitylog` (atau setara)
- **Pendukung:** DomPDF (rapor/kwitansi), Laravel Excel (import/export), Simple QRCode (portofolio siswa), queue database + cron
- **Deploy target:** shared hosting (Hostinger / Niagahoster Premium), storage lokal

## Users

**Primer:** 16 peran — Super Admin, Kepala Madrasah, 4 Wakamad (Kurikulum, Kesiswaan, Sarpras, Humas), Tata Usaha, Bendahara, Wali Kelas, Guru Mata Pelajaran, Guru BK, Petugas Perpustakaan, Petugas Laboratorium, Editor Berita, Orang Tua, Siswa. Mayoritas pengguna adalah guru/pegawai madrasah yang **belum terbiasa aplikasi kompleks** — situasi pemakaian: pekerjaan administratif harian (absensi, nilai, tagihan, surat, inventaris) dari komputer dan ponsel.

Satu orang bisa memegang lebih dari satu role dan penugasan sekaligus. Konsep **role** (hak akses sistem), **jabatan** (posisi struktural), dan **penugasan** (tanggung jawab periodik, mis. wali kelas VII-A 2026/2027) dipisah secara struktural.

## Product Purpose

SIM Madrasah adalah sistem informasi manajemen berbasis web yang menyatukan seluruh operasional madrasah dalam satu sumber data: akademik, kesiswaan, keuangan, kepegawaian, sarana-prasarana, perpustakaan, dan penjaminan mutu (PKKM/akreditasi). Data yang selama ini tersebar di Excel, Word, buku administrasi manual, WhatsApp, dan Google Drive pribadi dikonsolidasikan menjadi satu sistem dengan histori utuh, tanpa mengorbankan kompatibilitas shared hosting.

**Sukses berarti:** setiap peran hanya melihat menu dan data sesuai kewenangannya; riwayat siswa terlacak dari masuk sampai alumni; transaksi (nilai, absensi, SPP, rapor) dapat difilter per tahun ajaran/semester; pimpinan mendapat ringkasan kondisi madrasah; dan sistem berjalan mulus di shared hosting.

## Positioning

Dua prinsip yang membedakan SIM Madrasah dari sistem lain:

1. **Histori data tidak pernah hilang** — transaksi dicatat sebagai baris baru (`student_enrollments` per kombinasi siswa–tahun ajaran–semester–kelas), koreksi tidak menghapus data asli, dan rapor yang terbit disimpan sebagai snapshot agar bisa dicetak ulang persis.
2. **Hak akses granular melalui role & permission** — dibangun di atas `spatie/laravel-permission`, ditambah **lapisan penugasan berbasis periode** (wali kelas, guru mapel per kelas + tahun ajaran) yang memang spesifik domain madrasah dan tidak bisa digantikan package generik, karena aturannya berbasis relasi data, bukan sekadar role.

Versi pertama melayani satu madrasah (bukan multi-tenant/SaaS) dengan satu autentikasi lokal.

## Operating Context

- Satu madrasah per instalasi, ±50 pengguna aktif serentak; akses dari komputer maupun ponsel (responsif).
- 68 modul/submodul dalam 8 kelompok (A–H): fondasi sistem, website publik/berita/PPDB, data inti & akademik, kesiswaan & portofolio, keuangan/TU/kepegawaian, sarpras/inventaris/perpustakaan, PKKM/akreditasi/mutu, portal/pelaporan/pemeliharaan. Modul MVP terdaftar eksplisit (autentikasi, pengguna, RBAC, data guru/siswa/orang tua, mapel, kelas, riwayat kelas, penugasan, jadwal, kehadiran, jurnal, nilai, rapor, tagihan, pembayaran, dashboard, portal ortu, portal siswa, activity log, backup).
- Siklus pengembangan per modul: data skeleton (migration + seeder) → tampilan Blade → persetujuan → backend (Form Request, Policy, aturan bisnis, audit log). Walking skeleton (nilai → rapor PDF → portal ortu) dibangun lebih dulu untuk memvalidasi arsitektur.
- Sebagian modul bersifat read/aggregate-only dan tidak boleh menduplikasi data: Portofolio Digital, Dashboard, Portal Orang Tua, Portal Siswa, Pusat Laporan, Pusat Dokumen.
- Bahasa utama Bahasa Indonesia; nominal keuangan dalam Rupiah; zona waktu mengikuti pengaturan madrasah.
- Lingkungan staging di subdomain terpisah dengan database terpisah; deploy manual (maintenance → git pull → composer → migrate → build).

## Capabilities and Constraints

**Kapabilitas:** 16 role dengan menu per peran; RBAC granular (permission seperti `siswa.lihat`, `nilai.input`, `nilai.publish`); penegakan akses berlapis (middleware → Gate/Policy → pembatasan penugasan); aktivitas & audit log; QR Code portofolio dengan pemeriksaan izin; import/export Excel dengan preview per baris; PDF rapor/kwitansi; backup & restore; 8 status alur berita; pembayaran dihitung dari akumulasi transaksi, bukan diisi manual; publikasi nilai/rapor berjenjang (draft → lengkap → diverifikasi → publikasi).

**Konstrain teknis (v1):** web responsif tanpa aplikasi mobile native; autentikasi akun lokal (belum SSO/OAuth); bukan multi-tenant; tanpa WebSocket/realtime, Redis, Docker produksi, payment gateway otomatis, absensi biometrik, notifikasi WhatsApp otomatis, worker queue permanen — queue hanya berbasis database + cron.

**Konstrain non-fungsional:** halaman list dengan pagination ≤3 detik (p95); CRUD ≤2 detik; kenyamanan ±50 pengguna serentak; pagination wajib; password minimal 8 karakter; 5× gagal login → kunci 15 menit; sesi idle kedaluwarsa 120 menit; backup harian (7 versi) + mingguan di luar server; restore drill di staging.

**Kepatuhan UU PDP:** data pribadi sensitif (NIK, catatan konseling, data anak di bawah umur) wajib kebijakan retensi, consent eksplisit saat PPDB, pembatasan akses di level Policy (bukan hanya role), dan pemberitahuan transparansi internal — menjadi bagian dari Definition of Done modul PPDB, Data Siswa, Konseling.

**Belum diputuskan (terbuka dari PRD):** Q1 retensi data alumni/pendaftar PPDB yang tidak diterima; Q2 format rapor (template Kemenag/EMIS vs custom); Q3 ekspor ke EMIS/Dapodik masuk v1 atau ditunda; Q4 bentuk persetujuan publikasi foto & data siswa di website publik; Q5 kuota disk hosting & kebijakan arsip file lama.

**Istilah kunci:** ATP, BK, DoD, EMIS/Dapodik, PKKM, PPDB, RBAC, SNP, TU, Wakamad, walking skeleton, seeder/factory.

## Brand Commitments

- **Nama:** SIM Madrasah — Sistem Informasi Manajemen Madrasah.
- **Bahasa:** Bahasa Indonesia.
- **Identitas visual (batasan yang direkam, bukan diubah di sini):** hijau (utama), netral (latar), merah (error/berbahaya), kuning (peringatan/cicilan), hijau (sukses/lunas), biru (informasi).
- **Prinsip tampilan:** minimalis, menu berbasis role, tabel dengan filter, form terbagi per bagian kecil, breadcrumb, konfirmasi tindakan, status ditampilkan visual, tombol utama mudah ditemukan.
- **Acuan pengguna:** guru/pegawai yang belum terbiasa aplikasi kompleks — hindari ikon tanpa teks, istilah teknis, form panjang, navigasi bertingkat dalam.

## Evidence on Hand

- `PRD sim madrasah.md` (v2.1, dokumen otoritatif): seluruh latar belakang, ruang lingkup, 16 peran, arsitektur, ERD, aturan bisnis, non-fungsional, roadmap Tahap 1–14+, pengujian, migrasi data, kepatuhan UU PDP.
- Konfirmasi pengguna sesi init: repositori ini **fresh start** (belum ada kode, meskipun roadmap menandai Tahap 8–10 selesai); untuk sekarang cukup mencatat konteks produk, surface diputuskan kemudian.
- Tidak ada di tangan dan tidak boleh dibuat-buat: logo/aset merek, screenshot sistem lama, testimoni pengguna nyata, data produksi, hasil UAT.

## Product Principles

1. **Histori tidak pernah hilang.** Semua transaksi dan perpindahan status dicatat sebagai riwayat yang tidak ditimpa; dokumen resmi (rapor) disimpan sebagai snapshot.
2. **Satu sumber kebenaran.** Data master dipakai lintas modul tanpa entri ulang; modul agregasi hanya membaca, tidak menduplikasi.
3. **Akses granular dan berlapis.** Role/permission sebagai fondasi, Policy per data, dan penugasan berbasis relasi (kelas + mapel + tahun ajaran) sebagai lapisan madrasah.
4. **Sederhana untuk pemakainya.** Setiap peran hanya melihat menu dan data tugasnya; bahasa sederhana, ikon selalu berteks, form pendek, alur minim langkah.
5. **Kompatibel infrastruktur terjangkau.** Murni PHP + MySQL/MariaDB di shared hosting: tanpa proses permanen, query dioptimalkan, pagination wajib, pekerjaan berat via queue database + cron.
6. **Akuntabel dan patuh.** Perubahan penting tercatat di audit log; data pribadi dilindungi sesuai UU PDP (retensi, consent, pembatasan policy-level).

## Accessibility & Inclusion

- Nyaman diakses dari ponsel dan komputer.
- Bahasa sederhana dan pesan error jelas untuk pengguna yang belum terbiasa aplikasi kompleks.
- Menu, label, dan status disajikan dengan teks (bukan hanya ikon/warna) — warna hijau/kuning/merah/biru sebagai status visual tidak boleh menjadi satu-satunya pembawa makna.
