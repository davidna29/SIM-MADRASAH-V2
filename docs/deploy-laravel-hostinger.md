# Panduan Deploy Laravel ke Hostinger Shared Hosting

Catatan pribadi berdasarkan pengalaman deploy SIM Madrasah V2 ke `misnupalangkaraya.sch.id` (Hostinger Premium Web Hosting, PHP 8.4, via Git auto-deploy).

---

## 0. Prasyarat

- [ ] Database MySQL sudah dibuat di hPanel → Database
- [ ] Repo GitHub proyek siap (private lebih aman kalau menyimpan hasil build)
- [ ] Cek apakah paket hosting punya akses SSH (Premium ke atas biasanya ada)
- [ ] Cek versi PHP aktif di SSH: `php -v` (bisa beda dari default, gunakan path lengkap kalau perlu, mis. `/opt/alt/php84/usr/bin/php`)
- [ ] Cek Composer: `composer2 --version` (Hostinger pakai `composer2` untuk PHP 8+)

---

## 1. Hubungkan GitHub ke Hostinger (Git Auto Deploy)

Di dashboard Website Hostinger → connect ke repo GitHub → pilih branch `main` → deploy.

**Catatan penting:** kalau **"Direktori root" tidak bisa diubah** ke `public/` (limitasi paket tertentu), seluruh isi repo (termasuk `app/`, `.env`, `vendor/`) akan berada di `public_html/<nama-folder-repo>/` — ini **tidak otomatis aman**, perlu langkah 2.

---

## 2. Amankan document root dengan `.htaccess`

Karena document root Apache adalah `public_html` (bukan folder Laravel `public/`), buat `.htaccess` di **root `public_html`** (satu level DI ATAS folder hasil clone repo, bukan di dalamnya):

```apache
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/<nama-folder-repo>/public/
RewriteRule ^(.*)$ <nama-folder-repo>/public/$1 [L]
```

Ganti `<nama-folder-repo>` sesuai nama folder deploy (misal `laravel_app`). Ini membuat semua request ke domain otomatis diarahkan ke `public/` Laravel, sementara folder `app/`, `.env`, `vendor/` tetap tidak bisa diakses langsung browser (request ke situ akan 404 karena tidak match pola redirect).

Hapus/rename file `default.php` bawaan Hostinger di `public_html` kalau ada, supaya tidak mengganggu.

---

## 3. Install dependencies via SSH

```bash
cd domains/<domain>/public_html/<nama-folder-repo>
composer2 install --no-dev --optimize-autoloader
```

---

## 4. Buat `.env` manual

`.env` selalu di-`.gitignore`, jadi tidak ikut ter-deploy dari GitHub. Buat manual lewat SSH (`nano .env`) atau File Manager, isi sesuai kredensial DB dari hPanel, dan set:
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<domain>
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

---

## 5. Generate key, permission, storage link, cache

```bash
php artisan key:generate
chmod -R 755 storage bootstrap/cache
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 6. Export & import database dengan benar

**Export dari lokal** — hindari flag yang bikin warning ikut masuk ke file `.sql`:
```bash
mysqldump --set-gtid-purged=OFF --no-tablespaces --single-transaction \
  -u root -p nama_db > dump.sql
```
- `--set-gtid-purged=OFF` → hindari error `SET @@GLOBAL.GTID_PURGED` di MariaDB
- `--no-tablespaces` → hindari error `TABLESPACE` di shared hosting
- Jangan gabungkan `2>&1` saat export — itu bikin teks warning ikut masuk ke isi file `.sql` dan bikin import gagal (`#1064 syntax error`)

**Import** lewat hPanel → Database → phpMyAdmin → pilih DB → Import → upload file.

Kalau database sudah diisi lewat dump (schema + data lengkap), **jangan** jalankan `php artisan migrate` lagi — bisa konflik dengan tabel yang sudah ada.

---

## 7. Build asset frontend (Vite)

Folder `public/build/` (hasil `npm run build`) biasanya di-`.gitignore`, jadi tidak ikut ter-push → muncul error `ViteManifestNotFoundException`.

**Opsi A — build di server** (kalau Node/npm tersedia via SSH):
```bash
npm install
npm run build
```

**Opsi B — build di lokal, commit hasilnya:**
```bash
npm run build
# hapus/comment baris "/public/build" di .gitignore
git add -f public/build
git commit -m "Include built frontend assets"
git push
```
Aman untuk repo **private**. Cek `vite.config.js` pastikan `sourcemap` tidak `true` untuk production. Jangan taruh secret backend dengan prefix `VITE_` di `.env` — nilainya ikut ter-bundle ke JS yang publik.

Ingat: kalau frontend diupdate lagi, harus build ulang & commit ulang folder `build/` sebelum push (server tidak build otomatis dari source Vite).

---

## 8. Debugging kalau masih blank/error

```bash
tail -n 50 storage/logs/laravel.log
```
Kalau file belum ada dan halaman masih blank/nunjukin halaman default Hostinger → kemungkinan masalah di `.htaccess` (langkah 2), bukan di Laravel. Cek `mod_rewrite` aktif dan `.htaccess` ada di lokasi yang benar (root `public_html`, bukan di dalam folder repo).

Boleh set `APP_DEBUG=true` sementara untuk lihat error detail — **wajib balikin ke `false`** setelah selesai debug.

---

## 9. Setelah live

- [ ] Login pakai akun admin default, langsung ganti password
- [ ] Set cron job scheduler kalau proyek pakai `php artisan schedule:run`:
  ```
  * * * * * cd /home/<user>/domains/<domain>/public_html/<folder-repo> && php artisan schedule:run >> /dev/null 2>&1
  ```
- [ ] Pastikan `APP_DEBUG=false` di `.env` production
