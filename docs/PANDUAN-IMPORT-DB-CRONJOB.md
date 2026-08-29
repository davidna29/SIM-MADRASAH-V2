# Panduan Impor Database & Setup Cron Job — Hostinger

> Dokumen panduan langkah demi langkah untuk melakukan import database MySQL dan konfigurasi Cron Job otomatis pada Hostinger Shared Hosting.

---

## 1. Import Database ke MySQL Hostinger

### 1.1 Persiapan File Database
File dump database terbaru sudah tersedia di repository Anda:
- **Nama file:** `sim_madrasah_export.sql` (berada di root folder proyek)
- **Ukuran:** ~591 KB
- **Isi:** Seluruh struktur tabel + data seeder/demo lengkap.

---

### 1.2 Import via phpMyAdmin (Hostinger hPanel)

1. **Masuk ke hPanel Hostinger:**
   - Akses [hPanel Hostinger](https://hpanel.hostinger.com)
   - Buka menu **Databases** → **MySQL Databases**

2. **Buka phpMyAdmin:**
   - Cari database yang sudah dibuat untuk proyek ini (misal: `u123456789_simmadrasah`)
   - Klik tombol **Enter phpMyAdmin** di sebelah kanan database tersebut.

3. **Eksekusi Import:**
   - Di tampilan phpMyAdmin, pastikan Anda berada di dalam database `u123456789_simmadrasah` (lihat nama DB di pojok kiri atas).
   - Klik tab **Import** pada menu navigasi atas.
   - Klik **Choose File** (Pilih File) → pilih file `sim_madrasah_export.sql` dari komputer Anda (atau unduh dari repo GitHub jika melakukan dari server).
   - Biarkan opsi lainnya default (Format: SQL, Partial Import: Unchecked).
   - Gulir ke bawah lalu klik **Import** / **Go**.
   - Tunggu hingga muncul pesan sukses berwarna hijau: *"Import has been successfully finished..."*.

---

### 1.3 Alternative: Import via Terminal Hostinger (SSH)

Jika Anda lebih memilih menggunakan Terminal hPanel atau SSH:

```bash
# 1. Pindah ke folder tempat file sim_madrasah_export.sql berada
cd /home/u123456789/domains/nama-domain-anda.com/proyek_sim_madrasah

# 2. Jalankan perintah import MySQL
mysql -u u123456789_user -p u123456789_simmadrasah < sim_madrasah_export.sql
```
*Sistem akan meminta password database. Masukkan password yang Anda buat di Hostinger.*

---

## 2. Generate App Key & Optimasi Cache Laravel

Buka **Terminal** di hPanel Hostinger, lalu jalankan perintah berikut secara berurutan:

```bash
# Pindah ke direktori proyek Laravel
cd /home/u123456789/domains/nama-domain-anda.com/proyek_sim_madrasah

# 1. Generate APP_KEY untuk keamanan
php artisan key:generate

# 2. Buat symlink dari storage ke public
# (karena folder public dipindah ke public_html, buat link dari public_html)
ln -s /home/u123456789/domains/nama-domain-anda.com/proyek_sim_madrasah/storage/app/public /home/u123456789/domains/nama-domain-anda.com/public_html/storage

# 3. Optimasi & Cache Production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 3. Setup Cron Job di Hostinger

Cron Job digunakan untuk menjalankan otomatisasi Laravel seperti:
- Pemrosesan antrean (Queue jobs)
- Auto-publish berita/agenda yang dijadwalkan (`berita:publish-terjadwal`)
- Pembersihan berkala & pemeliharaan sistem

---

### 3.1 Langkah Konfigurasi Cron Job di hPanel

1. Di dashboard hPanel, cari menu **Advanced** → **Cron Jobs**.
2. Pada bagian **Create a Cron Job**, pilih jenis **Custom**.
3. **Command yang harus dimasukkan:**

   ```bash
   * * * * * cd /home/u123456789/domains/nama-domain-anda.com/proyek_sim_madrasah && php artisan schedule:run >> /dev/null 2>&1
   ```

   > **Penting:** Ganti `/home/u123456789/domains/nama-domain-anda.com/proyek_sim_madrasah` dengan path direktori absolut proyek Laravel Anda yang sebenarnya di Hostinger.

4. **Pengaturan Waktu (Frequency):**
   - Pilih preset **Once per minute** (`* * * * *`).
5. Klik **Save** / **Add Cron Job**.

---

### 3.2 Verifikasi Cron Job

1. Setelah Cron Job disimpan, tunggu 1-2 menit.
2. Di hPanel Cron Jobs, cek tabel **Cron Job Logs** di bagian bawah.
3. Pastikan status menunjukkan *Executed* tanpa error.

---

## 4. Verifikasi Akhir Deployment

Setelah seluruh langkah selesai, lakukan pengujian cepat di browser:

1. **Akses Website:** Buka `https://nama-domain-anda.com`
2. **Halaman Publik:** Cek Berita, Agenda, Galeri, dan PPDB Daring.
3. **Login Admin:**
   - URL: `https://nama-domain-anda.com/login`
   - Username: `admin`
   - Password: `password`
   - Pastikan berhasil masuk dan diarahkan ke halaman `/ubah-password` (fitur wajib ganti password).
4. **Cek Asset Frontend:** Pastikan CSS Tailwind dan komponen Alpine.js berjalan lancar tanpa tampilan berantakan (artinya `public/build` berhasil terbaca).

---

## 5. Ringkasan Checklist Go-Live Hostinger

- [x] Repository Git di-push penuh (`main` branch)
- [x] File `sim_madrasah_export.sql` tersedia
- [ ] Database MySQL Hostinger dibuat
- [ ] File `.sql` di-import ke MySQL Hostinger
- [ ] Subdomain / Domain utama menunjuk ke `public_html`
- [ ] File `.env` production disesuaikan di server
- [ ] `composer install --no-dev` dieksekusi di server
- [ ] `php artisan key:generate` dieksekusi
- [ ] Symlink storage dibuat (`public_html/storage`)
- [ ] Cache Laravel diaktifkan (`config`, `route`, `view`)
- [ ] Cron Job `schedule:run` aktif setiap menit

---

**Revisi:** 29 Agustus 2026  
**Status:** Siap Eksekusi Deployment
