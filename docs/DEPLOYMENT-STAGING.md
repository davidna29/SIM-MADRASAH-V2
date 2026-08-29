# Panduan Deployment Staging — SIM Madrasah

> Dokumentasi deployment ke lingkungan staging untuk validasi sebelum production.
> Target: Shared hosting (Hostinger/Niagahoster) dengan cPanel.

---

## 1. Prasyarat

### 1.1 Akses Hosting
- Panel cPanel dengan akses FTP/SFTP
- MySQL 8.0+ / MariaDB 10.3+
- PHP 8.4 (MultiPHP Manager)
- SSL certificate (Let's Encrypt via cPanel)
- Subdomain: `staging.domain-madrasah.sch.id`

### 1.2 Lokal
- Git repository up-to-date
- Composer 2.x
- Node.js 18+ & npm

---

## 2. Persiapan Lingkungan Staging

### 2.1 Konfigurasi PHP (cPanel)

**MultiPHP INI Editor** → pilih domain staging:

```ini
post_max_size = 64M
upload_max_filesize = 8M
max_execution_time = 300
memory_limit = 256M
```

### 2.2 Database Staging

1. **cPanel → MySQL Databases**
2. Buat database: `staging_sim_madrasah`
3. Buat user: `staging_sim` (password kuat, catat)
4. Assign user ke database dengan ALL PRIVILEGES
5. Catat:
   - DB_HOST (biasanya `localhost`)
   - DB_DATABASE: `staging_sim_madrasah`
   - DB_USERNAME: `staging_sim`
   - DB_PASSWORD: `[password yang dibuat]`

---

## 3. Upload Kode via Git (Rekomendasi)

### 3.1 Setup SSH di cPanel

1. **cPanel → Terminal**
2. Generate SSH key (jika belum):
   ```bash
   ssh-keygen -t ed25519 -C "staging@madrasah"
   cat ~/.ssh/id_ed25519.pub
   ```
3. Tambahkan public key ke GitHub → Settings → SSH Keys

### 3.2 Clone Repository

```bash
cd ~/public_html/staging  # atau subdomain folder
git clone git@github.com:davidna29/SIM-MADRASAH-V2.git .
git checkout main
```

**Alternatif FTP:** Upload semua file kecuali `.git/`, `node_modules/`, `vendor/`, `tests/`.

---

## 4. Instalasi Dependencies

### 4.1 Composer (via Terminal)

```bash
cd ~/public_html/staging
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Install dependencies (production only)
php composer.phar install --no-dev --optimize-autoloader
```

### 4.2 Node.js & Build Assets

**Di lokal** (shared hosting biasanya tidak punya npm):

```bash
npm install
npm run build
```

Upload folder `public/build/` hasil build ke server via FTP.

---

## 5. Konfigurasi Environment

### 5.1 File `.env`

```bash
cp .env.example .env
nano .env  # atau edit via cPanel File Manager
```

**Isi wajib:**

```ini
APP_NAME="SIM Madrasah (Staging)"
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging.domain-madrasah.sch.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=staging_sim_madrasah
DB_USERNAME=staging_sim
DB_PASSWORD=[password dari langkah 2.2]

# Generate key baru:
# php artisan key:generate --show
APP_KEY=base64:...

SESSION_DRIVER=database
QUEUE_CONNECTION=database

LOG_CHANNEL=daily
LOG_LEVEL=info
```

### 5.2 Generate Key

```bash
php artisan key:generate
```

---

## 6. Database Migration & Seeder

```bash
# Fresh install + data demo
php artisan migrate:fresh --seed --force

# Atau production (tanpa seed):
php artisan migrate --force
```

**PENTING:** `--force` wajib untuk environment non-local.

---

## 7. Storage & Permissions

```bash
# Link storage
php artisan storage:link

# Set permissions (jika perlu)
chmod -R 775 storage bootstrap/cache
```

Pastikan folder ini writable:
- `storage/app/`
- `storage/logs/`
- `bootstrap/cache/`

---

## 8. Konfigurasi Web Server (cPanel)

### 8.1 Document Root

**cPanel → Domains → Manage** → Ubah document root subdomain ke:

```
/home/[username]/public_html/staging/public
```

### 8.2 `.htaccess` (sudah ada di Laravel)

Verifikasi `/public/.htaccess` ada dan berisi Laravel rewrite rules.

---

## 9. Optimasi Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Clear cache (saat deploy ulang):**

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 10. Cron Job untuk Queue & Scheduler

**cPanel → Cron Jobs** → Add:

```
* * * * * cd /home/[username]/public_html/staging && php artisan schedule:run >> /dev/null 2>&1
```

Ini menjalankan:
- `queue:work --stop-when-empty` (via scheduler)
- `berita:publish-terjadwal` (artikel auto-publish)
- Job lain yang dijadwalkan

---

## 11. Testing Staging

### 11.1 Smoke Test

1. Buka `https://staging.domain-madrasah.sch.id`
2. Login `admin` / `password`
3. Ganti password (middleware wajib)
4. Uji alur:
   - Dashboard → muncul agregat
   - Data Siswa → CRUD
   - Jadwal Pelajaran → buat model
   - Rapor → terbit PDF
   - Portal Orang Tua → login `ibu.aisy`

### 11.2 Verifikasi Upload

- Upload foto prestasi (max 8MB)
- Upload dokumen PPDB (link Google Drive)
- Cetak PDF rapor (DomPDF)

### 11.3 Cek Log Error

```bash
tail -f storage/logs/laravel.log
```

---

## 12. Prosedur Deploy Update (setelah staging aktif)

```bash
# 1. Maintenance mode
php artisan down --message="Update sistem..." --retry=60

# 2. Pull update
git pull origin main

# 3. Update dependencies (jika ada)
php composer.phar install --no-dev --optimize-autoloader

# 4. Upload build baru (jika frontend berubah)
# → upload public/build/ dari lokal

# 5. Migrate (jika ada migration baru)
php artisan migrate --force

# 6. Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 7. Re-cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Up
php artisan up
```

---

## 13. Backup Sebelum Deploy Besar

**Manual via cPanel:**

1. **phpMyAdmin** → Export database → SQL (gzip)
2. **File Manager** → Compress `staging/` → Download

**Atau via terminal:**

```bash
# Database
php artisan backup:database

# File
php artisan backup:files
```

Simpan di luar server (Google Drive, lokal).

---

## 14. Rollback (jika deploy gagal)

```bash
# Git
git reset --hard [commit-hash-sebelumnya]

# Restore database dari backup
mysql -u staging_sim -p staging_sim_madrasah < backup.sql

# Clear semua cache
php artisan config:clear
php artisan cache:clear

php artisan up
```

---

## 15. Monitoring Staging

### 15.1 Log Harian

```bash
# Error log
tail -50 storage/logs/laravel-$(date +%Y-%m-%d).log

# Activity log
php artisan activity-log:summary
```

### 15.2 Disk Usage

```bash
du -sh storage/app/public/*
du -sh storage/logs/*
```

Hapus log lama (>30 hari) secara berkala.

---

## 16. Checklist Pre-Production

- [ ] Semua test otomatis lulus (`php artisan test`)
- [ ] Manual test 3 modul selesai (docs/TEST-MANUAL-3-MODUL.md)
- [ ] Backup staging berhasil & restore teruji
- [ ] UAT dengan pengguna nyata selesai (temuan dicatat)
- [ ] Performa acceptable (halaman list <3s, CRUD <2s)
- [ ] SSL aktif (https://)
- [ ] Cron job berjalan (cek `schedule:run` di log)
- [ ] Storage tidak penuh (<70% kapasitas)
- [ ] `.env` production: `APP_DEBUG=false`, `LOG_LEVEL=warning`

---

## 17. Kontak Dukungan

| Layanan | Kontak |
|---------|--------|
| Hosting (Hostinger) | https://hostinger.com/support |
| Hosting (Niagahoster) | https://niagahoster.co.id/livechat |
| GitHub Issues | https://github.com/davidna29/SIM-MADRASAH-V2/issues |
| Developer | [email admin teknis] |

---

## Lampiran: Troubleshooting Umum

### Error 500 setelah deploy
- Cek `storage/logs/laravel.log`
- Pastikan `.env` valid (APP_KEY terisi)
- Clear semua cache: `php artisan config:clear && php artisan cache:clear`

### Storage penuh
- Hapus log lama: `find storage/logs -name "*.log" -mtime +30 -delete`
- Kompres backup lama: `gzip storage/app/backups/db/*`

### Migration error "table exists"
- Jangan `migrate:fresh` di staging/production (data hilang)
- Rollback dulu: `php artisan migrate:rollback` lalu `migrate` ulang

### Queue job tidak jalan
- Cek cron job aktif: `crontab -l`
- Manual trigger: `php artisan schedule:run`
- Cek job tabel: `SELECT * FROM jobs LIMIT 10;`

---

**Revisi terakhir:** 29 Agustus 2026  
**Status:** Ready untuk staging deployment
