# Deploy Production & Integrasi Hermes — 23 November 2026

Target: `https://misnupalangkaraya.sch.id`
Path app: `/home/u549935065/domains/misnupalangkaraya.sch.id/public_html/laravel_app`
Stack: PHP 8.4.28 · Laravel 13.26.1 · MariaDB 11.8.9 · DB `u549935065_simmadv2`

Dokumen ini mencatat deploy production dan integrasi asisten Hermes STB. **Jangan simpan token/password di dokumen ini.**

---

## 1. Ringkasan

- Situs production sebelumnya 500 di beranda/profil/kontak/video/sitemap karena 7 migrasi baru belum dijalankan setelah Git auto-pull.
- `composer install --no-dev`, migrasi, seeder halaman publik, dan optimasi cache sudah dijalankan.
- Semua halaman publik yang sebelumnya 500 sudah 200.
- Galeri dirapikan: album demo MPLS di-Privat, album “Siswa Berprestasi” publik + cover.
- API v1 + user/token Hermes sudah aktif dan lolos smoke test authenticated.
- Cron hPanel sudah dibuat, tetapi **belum terbukti eksekusi** (lihat §7).

---

## 2. Backup

Dibuat sebelum perubahan:

- Export SQL via hPanel → phpMyAdmin (format SQL + gzip), ±399 KB.
- Arsip `.env` + `storage/`:
  - `~/backup-sim/env-storage-2026-11-23-0224.tar.gz` (96 KB).

---

## 3. Dependency & Migrasi

```bash
cd /home/u549935065/domains/misnupalangkaraya.sch.id/public_html/laravel_app

php artisan optimize:clear
composer2 install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan db:seed --class=PublicPageSeeder --force
php artisan optimize
```

Catatan:

- `--no-dev` menghapus 37 paket dev, termasuk `laravel/boost` (middleware `InjectBoost` tidak lagi aktif di production).
- `migrate:fresh` **tidak** dipakai.
- Migrasi batch 11 yang dijalankan:
  - `2026_11_22_162436_create_public_pages_table`
  - `2026_11_22_162437_add_show_on_website_to_employees_table`
  - `2026_11_22_162438_add_privacy_consent_fields_to_registrations_tables`
  - `2026_11_22_163849_add_publication_approval_to_media_items_table`
  - `2026_11_23_003426_create_personal_access_tokens_table`
  - `2026_11_23_003451_create_api_idempotency_keys_table`
  - `2026_11_23_003452_add_external_reference_to_tuition_payments_table`

---

## 4. Konfigurasi Production

Nilai `.env` yang dipakai (tanpa secret):

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://misnupalangkaraya.sch.id
LOG_CHANNEL=daily
LOG_LEVEL=warning
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=database
DB_CONNECTION=mysql
```

`public/storage` sudah LINKED. `mysqldump` tersedia di `/usr/bin/mysqldump`.

Catatan: dengan `LOG_LEVEL=warning`, file `storage/logs/laravel-YYYY-MM-DD.log` baru dibuat saat ada warning/error. Tidak adanya file harian bukan berarti aplikasi error.

---

## 5. Verifikasi Halaman

Semua URL berikut diuji 200 setelah migrasi + cache:

- `/`
- `/profil/sejarah`
- `/profil/guru`
- `/kontak`
- `/video`
- `/sitemap.xml`
- `/login`
- `/ppdb`

---

## 6. Galeri

Akar masalah: album demo **“Dokumentasi MPLS 2026”** (id 1) berisi file `galeri/demo-mpls-*.png`. Kode `PublikGaleriController` menyembunyikan seluruh album yang punya item demo, sehingga foto baru di album itu muncul di beranda tetapi albumnya 404 di `/galeri`.

Perbaikan (data, tanpa ubah kode):

- Album id 1 “Dokumentasi MPLS 2026” → status **Privat**.
- Album id 3 “Siswa Berprestasi” (slug `siswa-berprestasi`) → status **Publik**, cover di-set.
- Item **“video testing”** dibiarkan sesuai permintaan.

Verifikasi:

- `/galeri` menampilkan “Siswa Berprestasi”.
- `/galeri/siswa-berprestasi` → 200.
- Foto di beranda “Potret Kegiatan” mengarah ke album baru yang valid.

---

## 7. Cron Hostinger

Job sudah dibuat di hPanel → Advanced → Cron Jobs:

```bash
cd /home/u549935065/domains/misnupalangkaraya.sch.id/public_html/laravel_app && /opt/alt/php84/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

Jadwal: `* * * * *` (setiap menit).

Tugas yang dijalankan:

- `berita:publish-terjadwal` — setiap menit.
- `api:prune-idempotency` — harian.

Status: **belum terbukti eksekusi**.

- `crontab -l | grep schedule:run` kosong. Di Hostinger, cron hPanel dapat dikelola terpisah dari crontab user, jadi ini belum tentu gagal.
- Cara verifikasi:
  1. Klik **Lihat Output** pada baris cron di hPanel.
  2. Uji artikel terjadwal 2 menit ke depan (UTC), tunggu 3 menit.
  3. Tunggu artikel id 4 “Tanamkan Nilai Kasih Sayang…” yang dijadwalkan **11:35 UTC = 18:35 WIB**.
  4. Sementara ubah redirect ke `storage/logs/schedule.log`, tunggu 2 menit, cek log, lalu kembalikan ke `/dev/null`.

Jika “Lihat Output” menampilkan `art: command not found`, command terpotong newline di tengah `artisan`; isi ulang satu baris.

---

## 8. Integrasi Hermes STB

STB: Armbian Linux, root, 24/7, gateway Telegram self-hosted. Secret store di `/root/.hermes/.env` (mode 600).

### 8.1 User & Token

- User: `hermes.asisten`
- Role: `super_admin`
- Status: `is_active=1`, `must_change_password=0`
- Token: `hermes-stb`
- Scope: `website:read`, `cms:write`, `akademik:write`, `keuangan:write`
- Masa berlaku: **tanpa kedaluwarsa** (`expires_at=null`)

Token **tidak disimpan di dokumen ini**. Token asli disimpan di catatan pribadi admin dan di `/root/.hermes/.env` STB.

### 8.2 Kenapa `php -r`, bukan tinker

Hostinger menonaktifkan `shell_exec()`, sehingga `php artisan tinker` gagal dengan `Call to undefined function shell_exec()`. Bootstrap Laravel dilakukan lewat `php -r`.

Cek token (read-only):

```bash
cd /home/u549935065/domains/misnupalangkaraya.sch.id/public_html/laravel_app
php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = App\Models\User::where("username", "hermes.asisten")->firstOrFail();
foreach ($u->tokens as $t) {
    echo "token #{$t->id} | {$t->name} | ".implode(",", $t->abilities)." | expires: ".($t->expires_at ?? "null").PHP_EOL;
}
'
```

Jadikan token permanen (tanpa mengubah string token):

```bash
php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = App\Models\User::where("username", "hermes.asisten")->firstOrFail();
$t = $u->tokens()->where("name", "hermes-stb")->firstOrFail();
$t->forceFill(["expires_at" => null])->save();
echo "token #{$t->id} | {$t->name} | ".implode(",", $t->abilities)." | expires: ".($t->expires_at ?? "PERMANEN").PHP_EOL;
'
```

Buat token baru permanen (jika token lama hilang):

```bash
php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = App\Models\User::where("username", "hermes.asisten")->firstOrFail();
$u->tokens()->where("name", "hermes-stb")->delete();
$t = $u->createToken("hermes-stb", ["website:read", "cms:write", "akademik:write", "keuangan:write"], null);
echo $t->plainTextToken, PHP_EOL;
'
```

Revoke token:

```bash
php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = App\Models\User::where("username", "hermes.asisten")->firstOrFail();
$u->tokens()->where("name", "hermes-stb")->delete();
echo "token hermes-stb dicabut".PHP_EOL;
'
```

### 8.3 Penyimpanan di STB

`/root/.hermes/.env`:

```ini
MADRASAH_API_BASE_URL=https://misnupalangkaraya.sch.id/api/v1
MADRASAH_API_TOKEN=<token>
```

Langkah:

```bash
chmod 600 /root/.hermes/.env
systemctl --user restart hermes-gateway.service
systemctl --user status hermes-gateway.service --no-pager
```

Jika `systemctl --user` gagal di sesi SSH:

```bash
export XDG_RUNTIME_DIR=/run/user/$(id -u)
systemctl --user restart hermes-gateway.service
```

Verifikasi dari STB tanpa menampilkan token:

```bash
TOKEN=$(grep '^MADRASAH_API_TOKEN=' /root/.hermes/.env | cut -d= -f2-)
BASE=$(grep '^MADRASAH_API_BASE_URL=' /root/.hermes/.env | cut -d= -f2-)
curl -sS -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" "$BASE/me"
```

### 8.4 Hasil Smoke Test Hermes

- `GET /me` → 200, user `hermes.asisten` (`super_admin`), 4 scope, token tanpa kedaluwarsa.
- `GET /dashboard/ringkasan` → 200.
- `GET /berita` → 200.
- `POST /cms/berita` + `Idempotency-Key` → 201 draft.
- Retry dengan key + payload sama → 200, data sama, tidak duplikat.

Kontrak API lengkap: `docs/API-V1.md`.

---

## 9. Keamanan Token Permanen

Token permanen + 4 scope setara kredensial admin API. Wajib:

- `/root/.hermes/.env` mode `600`, owner root.
- SSH STB memakai key; password login dimatikan.
- Revoke token jika STB hilang/rusak/dipensiunkan.
- Hermes wajib meminta konfirmasi eksplisit sebelum menulis **SPP/keuangan**.
- Pantau `activity_log` (`log_name=api`) untuk write yang tidak diharapkan.
- Rate limit API write 30/menit; Hermes harus backoff saat 429.
- `Idempotency-Key` disimpan per aksi dan dipakai ulang saat retry.
- `external_reference` SPP harus unik dan tidak berubah saat retry.

---

## 10. Open Items

- [ ] Verifikasi cron benar-benar berjalan.
- [ ] Hapus draft tes id 5 “Tes API Hermes - draft, abaikan/hapus”.
- [ ] Putuskan timezone: tetap UTC atau ubah ke `Asia/Jakarta` + `locale=id` (perlu rencana konversi data).
- [ ] Uji manual upload foto ±4 MB, cetak PDF rapor, submit PPDB.
- [ ] Publish halaman publik hasil `PublicPageSeeder` (masih draft).
- [ ] Aktifkan `show_on_website` untuk guru/pegawai agar `/profil/guru` terisi.
- [ ] Setujui media lama yang belum punya `publication_approved_at`.
- [ ] Opsional: `APP_NAME` masih “Laravel”; bersihkan `storage/logs/laravel.log` lama; isi Sentry DSN bila dipakai.

---

## 11. Catatan Timezone

`config/app.php` masih:

```php
'timezone' => 'UTC',
'locale' => 'en',
```

Dampak:

- `scheduled_at`, `published_at`, presensi “hari ini”, dan jadwal berita memakai UTC.
- Artikel yang dijadwalkan “11:35” akan terbit pukul **18:35 WIB**.
- Mengubah timezone bukan sekadar ganti config: data lama dan jadwal yang sudah ada perlu diputuskan (konversi atau tidak). Kerjakan sebagai task terpisah dengan test regresi.
