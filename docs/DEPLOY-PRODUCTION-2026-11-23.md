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
- [x] Publish halaman publik hasil `PublicPageSeeder` — **selesai** (7 halaman terisi & publik).
- [x] Aktifkan `show_on_website` untuk guru/pegawai agar `/profil/guru` terisi — **selesai** (31 pegawai).
- [x] `APP_NAME` diperbaiki menjadi “SIM Madrasah” — **selesai**.
- [x] Setujui media baru MIS NU — **selesai** (61 item approved).
- [ ] 464 siswa sisanya belum diimpor (pilot VI-A/VI-B saja).
- [ ] Verifikasi/isi SK pendirian & operasional (masih placeholder demo) serta email resmi.
- [ ] Push perubahan kode ke `origin/main` (deploy kali ini memakai sinkronisasi berkas langsung; auto-deploy Git berikutnya berisiko menimpa).
- [ ] Bersihkan `storage/logs/laravel.log` lama; isi Sentry DSN bila dipakai.

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


---

## 12. Onboarding Data MIS NU — 23 November 2026 (sore)

Deploy dilakukan **tanpa `git push`** (sesuai instruksi); kode dikirim lewat tar/ssh, database diubah lewat migrasi + seeder + perintah `misnu:*`.

### 12.1 Backup sebelum perubahan

- DB: `~/backup-sim/db/2026-11-23_082730.sql` (570 KB) + salinan lokal `~/Downloads/backup-sim-prod/pre-misnu-2026-11-23.sql`.
- Files: `~/backup-sim/files/2026-11-23_082730.zip` (15 MB).
- `.env`: `~/backup-sim/.env-pre-misnu-20261123-082731` (0600).
- Kode: `~/backup-sim/code/pre-misnu-code-20261123.tgz`.
- `public/build`: `~/backup-sim/public-build-pre-misnu-20261123.tgz`.
- Snapshot siswa demo: `~/backup-sim/students-before-purge-20261123-082927.csv`.
- Data sumber PII: `~/misnu-data/` (0700, berkas 0600).

### 12.2 Langkah yang dijalankan

```bash
composer2 dump-autoload -o
php artisan migrate --force           # 4 migrasi (mapel, pegawai, kalender, is_efektif)
php artisan db:seed --class=MisnuEmployeeSeeder --force
php artisan db:seed --class=MisnuClassGroupSeeder --force
php artisan db:seed --class=MisnuSubjectSeeder --force
php artisan db:seed --class=MisnuRoomSeeder --force
php artisan db:seed --class=MisnuHomeroomSeeder --force
php artisan db:seed --class=MisnuExtracurricularSeeder --force
php artisan db:seed --class=MisnuPublicPageSeeder --force
php artisan db:seed --class=MisnuCalendarSeeder --force
php artisan misnu:purge-demo --students --employees --force --production
php -d memory_limit=512M artisan misnu:import-siswa \
  --file=/home/u549935065/misnu-data/database-siswa-emis-2026-2027.xlsx \
  --rombel=VI-A,VI-B
php artisan misnu:import-galeri --source=.../dokumentasi --source=.../kegiatan --approve
php artisan misnu:import-prestasi --file=/home/u549935065/misnu-data/prestasi-misnu.csv
php artisan optimize
```

Catatan: `MisnuSettingSeeder` **tidak** dijalankan di production (agar logo `settings/logo.jpeg`, email resmi, dan pengaturan PPDB/mutasi tidak tertimpa). Identitas diperbarui selektif: nama, tagline, telepon, website, naungan, desa/kecamatan, koordinat, jam layanan.

### 12.3 Hasil verifikasi

| Data | Hasil |
|---|---|
| Siswa / enrollment | 57 / 57 (VI-A 28, VI-B 29) |
| Wali | 109 |
| Pegawai aktif | 31 (9 demo di-soft-delete) |
| Rombel | 18 |
| Mapel | 13 |
| Ruangan | 16 |
| Ekstrakurikuler | 9 |
| Wali kelas | 18 |
| Kalender | 2 periode, 26 kegiatan |
| Halaman publik | 7 publik |
| Galeri | 5 album, 64 item (61 approved) |
| Prestasi | 2 (sisanya alumni, `storage/app/reports/prestasi-unmatched.csv`) |

Uji HTTP: `/`, `/profil*`, `/kontak`, `/galeri`, `/kalender`, `/sitemap.xml`, `/login`, `/ppdb` = 200. Uji internal (Auth CLI): dashboard, kalender, mapel, data siswa, data guru, import siswa, prestasi = 200. Tidak ada ERROR baru di `storage/logs/laravel-2026-11-23.log`.

### 12.4 Peringatan penting

- ~~Repo production dirty~~ — **diselesaikan** pada §13 (commit `3fa7d40` + reset keras; `git status` bersih).
- `public/build` diganti dengan hasil `npm run build` (hash baru) karena kelas Tailwind untuk view baru belum ada di build lama.
- Jangan jalankan `migrate:fresh` atau `misnu:purge-demo` lagi di production.


---

## 13. Rekonsiliasi Git, 521 Siswa, Legalitas, Bulk Approve, API Galeri — 23 November 2026

### 13.1 Rekonsiliasi Git (Opsi A)

1. Commit lokal `0cbb931` (fitur + build aset `public/build`) dan `3fa7d40` (bulk setujui draf), push ke `origin/main`.
2. Backup kode/build sebelum reset sudah tersedia (`~/backup-sim/code/…`, `public-build-…`).
3. Di server: `php artisan down`, `git fetch`, `git reset --hard origin/main`, `git clean -fd public/build`, `composer2 dump-autoload -o`, `php artisan migrate --force`, `php artisan optimize`, `php artisan up`.
4. `git status` production **bersih**; auto-deploy berikutnya aman (fast-forward).

### 13.2 Semua siswa (521)

- Dry-run: 521 diproses, 464 dibuat, 57 diperbarui, 0 dilewati, 832 wali baru, 1.034 penautan.
- Impor nyata dijalankan; hasil production: **521 siswa, 521 enrollment, 941 wali**, 0 person dipakai dua siswa.
- Sebaran: I 77 · II 100 · III 115 · IV 83 · V 89 · VI 57.
- Dua siswa berbagi NIK di workbook; siswa kedua otomatis memakai NIK sintetis.

### 13.3 Dokumen legalitas

- `storage/app/public/settings/legalitas/sertifikat-akreditasi.pdf` (2024–2029) dan `sk-pendirian.pdf` diunggah; setting `madrasah_file_akreditasi` & `madrasah_file_sk_pendirian` diisi.
- IJOP belum ada; kartu di `/profil` menampilkan **Menyusul**.
- Nilai akreditasi tetap `A` (93) sesuai keputusan.
- Kedua PDF dapat diunduh publik: `/storage/settings/legalitas/…` (HTTP 200).

### 13.4 Bulk setujui berita

- Di `/publikasi/berita`: checkbox per baris + “Pilih semua di halaman ini” + tombol “Setujui terpilih”.
- `draft` → `diajukan` → `disetujui`; `diajukan`/`review` → `disetujui`; status lain dilewati.
- Route `cms.berita.bulk-setujui`, hanya editor/humas/kepala/super admin; audit `berita_bulk_setujui`.
- 58 berita production masih `draft` — belum disetujui otomatis (menunggu tindakan operator).

### 13.5 API item galeri

- `POST /api/v1/cms/galeri/{album}/item` (scope `cms:write`, `Idempotency-Key` wajib).
- Foto: multipart `file` (jpg/jpeg/png/webp, maks 4 MB); video: JSON `video_url` **khusus YouTube** (`youtube.com/watch?v=`, `youtu.be/`).
- Album publik → 409; `file`/`video_url` kosong → 422; item baru `publication_approved_at=null`; audit `api_galeri_item_ditambah`.
- Sosial media lain (Facebook/Instagram/TikTok) **ditolak 422** — lihat `docs/API-V1.md`.
