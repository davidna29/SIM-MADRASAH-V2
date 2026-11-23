# API v1 — SIM Madrasah

Referensi kontrak API untuk asisten/web client. Implementasi ada di `routes/api.php` dan `app/Http/Controllers/Api/V1`.

## Dasar

- Base URL: `{APP_URL}/api/v1`
- Autentikasi: Bearer token Laravel Sanctum, bukan session/cookie.
- Semua request dan respons JSON: kirim `Accept: application/json`.
- Token disimpan dalam bentuk hash, hanya plaintext token yang dikembalikan sekali saat login.
- Masa berlaku token mengikuti `SANCTUM_EXPIRATION` (default 43.200 menit / 30 hari).
- Rate limit: login 5/menit per IP+login, read 120/menit per user, write 30/menit per user.

## Login

`POST /api/v1/login`

```json
{
  "login": "editor.humas",
  "password": "password",
  "device_name": "asisten-madrasah",
  "scopes": ["website:read", "cms:write"]
}
```

- `login` menerima username atau email.
- `scopes` opsional; jika dikirim harus subset dari scope role. Default semua scope role.
- Akun dengan `is_active=false` ditolak `403 ACCOUNT_INACTIVE`.
- Akun `must_change_password=true` ditolak `403 PASSWORD_CHANGE_REQUIRED`.
- Role `orang_tua` dan `siswa` ditolak `403 ROLE_NOT_ALLOWED`.

Respons:

```json
{
  "data": {
    "token": "1|...",
    "token_type": "Bearer",
    "token_id": 12,
    "expires_at": "2026-12-23T00:00:00+00:00",
    "scopes": ["website:read", "cms:write"],
    "user": { "id": 7, "name": "Editor Humas", "role": "editor_berita" }
  },
  "warnings": [],
  "meta": { "request_id": "...", "api_version": "v1", "generated_at": "..." }
}
```

Gunakan header berikut untuk request terautentikasi:

```http
Authorization: Bearer 1|...
Accept: application/json
Idempotency-Key: 550e8400-e29b-41d4-a716-446655440000
```

`Idempotency-Key` wajib untuk semua POST write; opsional untuk PUT. Key harus 8–100 karakter.

## Logout dan profil token

- `POST /api/v1/logout` — mencabut token yang sedang dipakai.
- `POST /api/v1/logout` dengan body `{"all_devices": true}` — mencabut semua token user.
- `GET /api/v1/me` — profil user, role, scope token, dan masa berlaku token.

## Scope per role

| Role | website:read | cms:write | akademik:write | keuangan:write |
|---|---:|---:|---:|---:|
| super_admin | ya | ya | ya | ya |
| kepala_madrasah | ya | ya | ya | – |
| wakamad_humas, editor_berita | ya | ya | – | – |
| tata_usaha | ya | ya | – | ya |
| wakamad_kurikulum, wakamad_kesiswaan, wali_kelas | ya | – | ya | – |
| guru | ya | ya* | ya | – |
| bendahara | ya | – | – | ya |
| guru_bk, pustakawan | ya | – | – | – |

`*` Guru hanya dapat membuat/mengubah berita draft; Policy tetap menolak agenda/galeri untuk guru.

Scope bukan satu-satunya kontrol. Setiap endpoint tetap memeriksa Policy/role dan kepemilikan data.

## Endpoint baca — `website:read`

| Method | Path | Keterangan |
|---|---|---|
| GET | `/dashboard/ringkasan` | KPI, perlu tindakan, kehadiran rombel, tagihan terbaru, aktivitas |
| GET | `/siswa` | Daftar siswa aktif; filter `q`, `kelas` (ID atau nama), pagination |
| GET | `/siswa/{student}` | Detail terbatas; data identitas lengkap hanya dengan `akademik:write` |
| GET | `/jadwal` | Wajib `kelas`; opsional `hari=senin..sabtu` |
| GET | `/kehadiran-siswa` | Wajib `kelas`; opsional `bulan=YYYY-MM` |
| GET | `/nilai` | Wajib `kelas`; opsional `mapel` (ID/kode/nama) |
| GET | `/spp/tagihan` | Opsional `status=lunas\|belum_bayar`, `kelas`, `bulan=1..12` |
| GET | `/pegawai` | Filter `q`, `unit`, `jabatan`, `status`; tanpa NIK/kontak/alamat |
| GET | `/berita` | Default hanya berita publish; `status` bebas hanya dengan `cms:write` |
| GET | `/agenda` | Default agenda aktif + target publik; filter internal/arsip butuh `cms:write` |
| GET | `/galeri` | Default album publik + media approved; album privat butuh `cms:write` |

Pagination: `page`, `per_page` (default 20, maks 100). Respons memuat `meta.pagination`.

Catatan privasi:
- Daftar siswa tidak memuat NIK, NISN, KK, alamat, atau kontak.
- Detail siswa lengkap memerlukan `akademik:write` dan Policy siswa.
- Daftar pegawai tidak memuat NIK, tanggal lahir, alamat, telepon, atau email.
- Hanya nilai yang sudah tersimpan yang ditampilkan pada `/nilai`.

## Endpoint tulis — CMS (`cms:write`)

| Method | Path | Perilaku |
|---|---|---|
| POST | `/cms/berita` | Selalu membuat `status=draft`; slug unik; upload `featured_image` |
| PUT | `/cms/berita/{article}` | Draft/revisi untuk edit; kirim `status=publish` untuk menerbitkan (hanya dari `disetujui`/`dijadwalkan`); `published_at` opsional (≤ now, default now); berita publish → 409 |
| POST | `/cms/agenda` | Selalu `status=arsip`; publikasi lewat web |
| PUT | `/cms/agenda/{agenda}` | Hanya agenda arsip; status diprohibisi |
| POST | `/cms/galeri` | Selalu `status=privat`; upload `cover_image` |
| POST | `/cms/galeri/{album}/item` | Tambah item ke album **privat**: `title`, `deskripsi`, `tipe=foto\|video`, `file` (foto, maks 4 MB) **atau** `video_url` (khusus YouTube). `Idempotency-Key` wajib; item baru `publication_approved_at=null` |
| PUT | `/cms/galeri/{album}/item/{item}` | Ubah `title`, `deskripsi`, `sort_order` pada item album **privat** yang belum disetujui; `video_url` immutable (beda → 422); `Idempotency-Key` wajib; album publik/item approved → 409 |
| PUT | `/cms/galeri/{album}` | Hanya album privat; status diprohibisi |

API tidak menyediakan DELETE item/album. Publikasi berita dilakukan lewat `PUT /cms/berita/{article}` dengan `status=publish`; persetujuan media/agenda tetap lewat web.

Contoh tambah foto galeri (multipart):

```bash
curl -X POST https://misnupalangkaraya.sch.id/api/v1/cms/galeri/12/item \
  -H "Authorization: Bearer $TOKEN" \
  -H "Idempotency-Key: galeri-foto-001" \
  -F "title=Foto Kegiatan" \
  -F "deskripsi=Dokumentasi kegiatan madrasah" \
  -F "tipe=foto" \
  -F "file=@foto.jpg"
```

Contoh tambah video YouTube (JSON):

```json
{
  "title": "Video Profil Madrasah",
  "deskripsi": "Profil singkat.",
  "tipe": "video",
  "video_url": "https://www.youtube.com/watch?v=dQw4w9WgXcQ"
}
```

Aturan `video_url`: hanya `youtube.com/watch?v=…` (termasuk `www.`/`m.`) atau `youtu.be/…` dengan ID 11 karakter. URL Facebook/Instagram/TikTok/ lainnya ditolak **422**. Album publik → **409**; salah satu `file`/`video_url` wajib (kosong → **422**). Audit: `api_galeri_item_ditambah`.

Contoh publikasi berita dengan tanggal terbit manual (maks. sekarang):

```json
{
  "title": "Kegiatan MPLS",
  "body": "Isi berita...",
  "status": "publish",
  "published_at": "2026-03-01T14:15:00+07:00"
}
```

Contoh berita:

```json
{
  "title": "Kegiatan MPLS",
  "body": "Isi berita...",
  "summary": "Ringkasan...",
  "category": "Kegiatan",
  "tags": "mpls,kegiatan"
}
```

Kirim `multipart/form-data` bila menyertakan `featured_image` (JPG/PNG/WebP, maks. 2 MB).

## Endpoint tulis — absensi (`akademik:write`)

`POST /api/v1/kehadiran-siswa`

```json
{
  "attendance_date": "2026-11-23",
  "class_group_id": 3,
  "attendances": {
    "12": { "status": "hadir", "note": null },
    "13": { "status": "sakit", "note": "Surat dokter" }
  }
}
```

- Semua enrollment harus aktif pada tahun ajaran aktif dan berada di kelas yang sama.
- Guru hanya boleh kelas yang diampu; wali kelas hanya kelas binaannya.
- Tanggal lampau hanya untuk role privileged; guru mendapat `422 ATTENDANCE_DATE_LOCKED`.
- `updateOrCreate` per enrollment+tanggal; review kelas dibuat otomatis.
- 201 jika ada baris baru, 200 jika seluruhnya update.

## Endpoint tulis — nilai (`akademik:write`)

`POST /api/v1/nilai`

Tanpa komponen:

```json
{
  "teacher_assignment_id": 7,
  "scores": { "45": 85, "46": 90 },
  "dry_run": false
}
```

Dengan komponen:

```json
{
  "teacher_assignment_id": 7,
  "values": {
    "45": { "2": 80, "3": 90 }
  },
  "dry_run": true
}
```

- Hanya guru pemilik `teacher_assignment`.
- Assignment harus tahun ajaran aktif.
- Jika rapor semester sudah `terbit` → `409 RECORD_ALREADY_PUBLISHED`.
- `dry_run=true` mengembalikan `before`, `after`, `status` (`changed`/`unchanged`/`cleared`), dan tidak menulis DB/audit.
- `dry_run=false` menyimpan dan mencatat `activity('api')`.

## Endpoint tulis — SPP (`keuangan:write`)

`POST /api/v1/spp/bayar`

```json
{
  "student_enrollment_id": 45,
  "bulan": 11,
  "tanggal_bayar": "2026-11-23",
  "metode": "transfer",
  "catatan": "Transfer BSI",
  "external_reference": "TRX-20261123-001"
}
```

- `nominal` **dilarang** dikirim; server menghitung override siswa → nominal setting tahun ajaran.
- Jika nominal belum diatur → `422 SPP_NOMINAL_NOT_SET`.
- `external_reference` wajib dan unik.
- Pembayaran bulan yang sudah lunas dengan referensi berbeda → `409 ALREADY_PAID`.
- Retry dengan `Idempotency-Key` dan payload sama → 200 + header `Idempotent-Replayed: true`.
- 201 pembayaran baru, 200 update/retry.

Respons memuat `payment_id`, `external_reference`, `status`, `nominal`, `bulan`, `tanggal_bayar`, siswa, dan kelas.

## Idempotency

- Header wajib untuk semua POST write.
- Key sama + payload/hash sama → respons tersimpan dikembalikan dengan status 200.
- Key sama + payload berbeda → `409 IDEMPOTENCY_KEY_REUSED`.
- Key sama sedang diproses → `409 IDEMPOTENCY_IN_PROGRESS`.
- Respons 2xx disimpan di `api_idempotency_keys` selama 24 jam.
- Retry tidak mengulang write dan tidak menambah audit log.
- Pembersihan: command `api:prune-idempotency` setiap hari melalui scheduler.

## Format respons

Sukses:

```json
{
  "data": {},
  "warnings": [],
  "meta": {
    "request_id": "...",
    "api_version": "v1",
    "generated_at": "...",
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 10,
      "last_page": 1,
      "from": 1,
      "to": 10
    }
  }
}
```

Error:

```json
{
  "message": "Data yang dikirim tidak valid.",
  "errors": { "field": ["Pesan validasi."] },
  "code": "VALIDATION_ERROR"
}
```

Kode error utama:

| HTTP | Code |
|---:|---|
| 401 | `UNAUTHENTICATED`, `INVALID_CREDENTIALS` |
| 403 | `ACCOUNT_INACTIVE`, `PASSWORD_CHANGE_REQUIRED`, `ROLE_NOT_ALLOWED`, `SCOPE_NOT_ALLOWED`, `SCOPE_MISSING`, `FORBIDDEN` |
| 404 | `NOT_FOUND` |
| 409 | `IDEMPOTENCY_KEY_REUSED`, `IDEMPOTENCY_IN_PROGRESS`, `EXTERNAL_REFERENCE_DUPLICATE`, `ALREADY_PAID`, `RECORD_ALREADY_PUBLISHED`, `NO_ACTIVE_ACADEMIC_YEAR` |
| 422 | `VALIDATION_ERROR`, `BUSINESS_RULE_VIOLATION`, `ATTENDANCE_DATE_LOCKED`, `SPP_NOMINAL_NOT_SET`, `IDEMPOTENCY_KEY_REQUIRED` |
| 429 | `RATE_LIMITED` |
| 500 | `SERVER_ERROR` |

`X-Request-Id` dikirim pada setiap respons dan dicatat pada audit write.

## Audit

Semua write sukses dicatat ke `activity_log`:

- `log_name`: `api`
- `description`: `api_login`, `api_logout`, `api_kehadiran_disimpan`, `api_nilai_disimpan`, `api_spp_dibayar`, `api_berita_dibuat`, dan seterusnya.
- `properties`: `request_id`, `token_id`, `token_name`, `scopes`, `ip`, `user_agent`, serta data spesifik aksi.

Retry idempoten dan `dry_run` tidak menambah audit write.

## Deploy dan operasional

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan route:clear
php artisan config:cache
php artisan route:cache
```

Pastikan:

- `APP_URL` benar dan HTTPS.
- `SANCTUM_EXPIRATION` di `.env` sesuai kebijakan (default 43200 menit).
- Cron `php artisan schedule:run` aktif agar `api:prune-idempotency` berjalan.
- Database memiliki tabel `personal_access_tokens`, `api_idempotency_keys`, dan kolom `tuition_payments.external_reference`.
- Jika deploy memakai dump database lama, jalankan migrasi API sebelum menerima token/pembayaran.
- Jangan menaruh token di URL, log, atau repositori. Cabut token yang bocor melalui logout/`all_devices`.

## Di luar scope API

Tidak tersedia dan tidak boleh ditambahkan lewat token asisten:

- DELETE permanen data domain.
- Backup/restore.
- Mengubah role atau password user lain.
- Mengubah setting global.
- Menerbitkan/mengedit rapor yang sudah terbit.
- Mengubah periode pajak yang terkunci.
- Publikasi agenda/album langsung dari API (berita boleh via `PUT status=publish`).
