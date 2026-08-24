# SIM Madrasah

Sistem Informasi Manajemen Madrasah Terintegrasi — Laravel 12+ · Blade · Tailwind CSS v4 · MySQL.

Aplikasi web untuk mengelola operasional madrasah (MI kelas I–VI): akademik, kesiswaan, keuangan, kepegawaian, sarana-prasarana, hingga penjaminan mutu (PKKM/akreditasi), dengan dua prinsip utama: **histori data tidak pernah hilang** dan **hak akses granular berbasis role & permission**.

## Memulai

Persyaratan: PHP 8.3+, Composer, Node.js, MySQL 8.4.

```bash
composer install
npm install
cp .env.example .env        # isi kredensial database
php artisan key:generate
php artisan migrate --seed   # skema + data demo
npm run build
php artisan serve            # http://localhost:8000
```

### Akun demo

| Role | Username | Password |
|---|---|---|
| Super Admin | `admin` | `password` |
| Guru | `guru.umar` | `password` |
| Orang Tua | `ibu.aisy` | `password` |

### Test

```bash
php artisan test
```

> Test memakai database terpisah `sim_madrasah_test` (lihat `phpunit.xml`).

## Dokumentasi

- **`docs/AI-HANDOFF.md`** — titik masuk melanjutkan proyek: status, cara jalan, konvensi, modul berikutnya.
- **`PRODUCT.md`** — produk, keputusan, prinsip.
- **`DESIGN.md`** — sistem visual "Mading" (papan pengumuman madrasah).
- **`PRD sim madrasah.md`** — Product Requirements Document lengkap.

## Modul yang sudah dibangun (Tahap 13 — MVP)

Data Guru & Pegawai · Mata Pelajaran · Kelas & Penempatan · Penugasan Mengajar · Data Siswa · Kehadiran Siswa · Jadwal Pelajaran (Model Jadwal fleksibel, tabel master penyusunan hari-vertikal, validasi konflik guru, generate lintas tahun ajaran, cetak PDF).

## Stack

Laravel · Blade + Tailwind CSS v4 · MySQL 8.4/MariaDB · Laravel Breeze · spatie/laravel-permission & laravel-activitylog · DomPDF · Laravel Excel · Simple QRCode.
