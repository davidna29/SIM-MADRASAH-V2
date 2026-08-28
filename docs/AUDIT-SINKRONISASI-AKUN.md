# Audit Dampak Perubahan Sinkronisasi Akun

> **Tujuan:** pemetaan kode yang sudah ada sebelum implementasi fitur sinkronisasi akun
> (provisioning otomatis siswa/pegawai + deaktivasi otomatis).
> **Sifat:** read-only — tidak ada kode yang diubah saat audit ini dibuat.
> **Tanggal audit:** sesi ini · DB dev sudah `migrate:fresh --seed` (data seeder).

---

## 1. Akun yang sudah ada (existing users)

### 1.1 Kondisi data (dev DB)

- `users` total **10 baris** — semuanya dari seeder (admin, guru.umar, guru.imam, guru.nurul, bendahara, editor.humas, kepala, pustakawan, ibu.aisy, siswa.aisy). **Tidak ada** user hasil input manual selain lewat modul Pengguna & Role.
- Role `siswa`: **1 user** (`siswa.aisy`), `student_id` **terisi** → 0 akun siswa yatim.
- Role `guru`: **3 user**.
- `employees`: **39 baris**, hanya **3 yang punya `user_id` terisi** (emp#3, 6, 7 ↔ guru.umar, guru.imam, guru.nurul) → **36 employees "yatim"** (data pegawai ada, tidak ada link akun).
- `students`: **28 baris**, hanya **1 punya akun** (`siswa.aisy` → NIS 240101) → **27 siswa tanpa akun**.

### 1.2 Mekanisme pencocokan yang ada

- **Tidak ditemukan** logika auto-match (nama/email/NIK) di kode.
- Uji manual sederhana menunjukkan 3 user guru **cocok dengan employee via substring nama** (mis. "Bapak Umar Hakim" ↔ emp#3) → kandidat join key tersedia, belum dipakai otomatis.

### 1.3 Kolom yang menyimpan hubungan (ada, belum konsisten dipakai)

| Kolom | Sumber | Arah & cardinality |
|---|---|---|
| `users.student_id` | `2026_08_24_000015_create_student_id_to_users.php` | FK unique nullable · **1 akun per siswa** · user → student |
| `employees.user_id` | `2026_08_23_000004_create_kepegawaian_tables.php` | FK nullable · **bisa banyak employee per user** |
| `guardians.user_id` | `000029` (alter nullable) | FK nullable · ortu → akun |
| `people` | `000004` | basis identitas bersama (NIK unique 16 digit) |

**Fakta data:** 67 `people` = tepat 39 pegawai + 28 siswa → **tidak ada tumpang tindih person saat ini**.

---

## 2. Titik-titik yang mengubah status siswa/pegawai

### 2.1 Siswa

- `students` **tidak punya kolom status** — status siswa hidup di `student_enrollments.status` (`aktif` / `alumni`).
- `app/Http/Controllers/Akademik/ClassGroupController.php`
  - `place()` (±baris 188) → `['status' => 'aktif']` (create instance)
  - `unplace()` (baris **204**) → `$enrollment->update(['status' => 'alumni']);` → **instance Eloquent update** (Observer terpicu) ✅
- `app/Http/Controllers/Akademik/StudentController.php` `store()` (±95) → create enrollment `status='aktif'` (instance)
- `app/Support/PpdbService.php` `accept()` → **tidak membuat enrollment** (kelas diisi belakangan; `student_enrollments.class_group_id` NOT NULL)

### 2.2 Pegawai

- `app/Http/Controllers/Kepegawaian/EmployeeController.php`
  - `store()` baris 102–103 & `update()` baris 167–168 → set `status` (aktif/cuti/nonaktif) + `employee_status` (pns/pppk/honor) — **instance update** ✅
  - `destroy()` → `$employee->delete()` (model pakai `SoftDeletes`) — event terpicu ✅

### 2.3 Operasi massal / terjadwal

- **Tidak ditemukan** fitur "Kelulusan Massal", "Naik Kelas Massal", import status pegawai, atau job terjadwal pengubah status.
- Satu-satunya scheduled command: `routes/console.php` → `berita:publish-terjadwal` (artikel — tidak relevan).
- Satu-satunya mass update di codebase: `SubjectController.php:71` `Subject::whereKey($id)->update(['sort_order' => ...])` — pada `subjects`, bukan status siswa/pegawai.

> **Kesimpulan poin 2:** semua perubahan status saat ini lewat **instance model** → Eloquent Observer aman dipakai. Fitur sync baru **jangan** menambah mass update tipe `Model::where(...)->update([...])`, atau harus melewatkan event manual.

---

## 3. Modul/fitur lain yang bergantung pada users/students/employees

### 3.1 Autentikasi & otorisasi

- `app/Http/Controllers/AuthController.php`:
  - `login()` → cocokkan `email` ATAU `username` (`filter_var($login, FILTER_VALIDATE_EMAIL)`)
  - `redirectToRole()` switch `Auth::user()->role`: guru→`guru.penugasan`, guru_bk→`konseling.index`, orang_tua→`ortu.dashboard`, siswa→`siswa.dashboard`
- `app/Http/Middleware/RoleMiddleware.php`: cek **`Auth::user()->role` saja** (string kolom). **Multi-role via `user_roles` pivot TIDAK dilalui middleware.** Hanya sebagian Policy memakai `User::hasRole()` (mis. `PortofolioPolicy`).
  → **Gerbang akses efektif = kolom `users.role`**; provisioning wajib menulis role yang benar.
- Policy memuat role `wali_kelas` (StudentPolicy, AttendancePolicy, AchievementPolicy, OffensePolicy, ExtracurricularPolicy, PembiasaanMateriPolicy) — role **tidak dapat di-assign via UI** (tidak ada di `AVAILABLE_ROLES`) → dead role di data hari ini.
- `app/Http/Controllers/Siswa/PortalController.php`: `rapor()` **abort 404** bila `auth()->user()->student` null → akun siswa auto-provision tanpa `student_id` akan merusak portal siswa.
- Ortu: `auth()->user()->guardian?->students()` (`Ortu\DashboardController`, `Ortu\SppController`) — butuh `guardians.user_id` terisi.

### 3.2 Notifikasi

- **Tidak ditemukan** sistem notifikasi (tidak ada `Mail::`, WhatsApp, push). `User` hanya memakai trait bawaan `Illuminate\Notifications\Notifiable` (tidak dipakai mengirim apa pun).
- → Asumsi desain baru "notifikasi ke ortu lewat kontak" tidak bertabrakan dengan apa pun; infrastruktur masih kosong.

### 3.3 Audit log

- **spatie/laravel-activitylog v5** (`composer.json`).
- Tabel `activity_log` dibuat migration custom `2026_08_23_043809_create_activity_log_table.php` (nullableMorphs subject/causer, `log_name`, `event`, `properties`/`attribute_changes` json).
- Model yang memakai trait `LogsActivity`: Student, Employee, Room, Article, InventoryItem/Mutation/Maintenance, LibraryBook/Member/Category/Loan, MediaAlbum, TeachingJournal, ScheduleCell, ScheduleModel, Extracurricular, PpdbRegistration.
- Helper `activity('nama_log')` + halaman read-only `Pemeliharaan\ActivityLogController@index` (`/pemeliharaan/activity-log`, super_admin).

### 3.4 Role/permission

- **Custom, bukan Spatie Permission.** `users.role` string(30) + pivot `user_roles` (`2026_08_25_000022_create_user_roles_table.php`), `User::allRoles()` menggabungkan.
- Daftar role valid: `AVAILABLE_ROLES` di `UserController` & validasi `in:` di `StoreUserRequest` (13 role). **`pustakawan` & `wali_kelas` tidak ada** di daftar → tidak bisa dibuat via UI.
- Role "siswa" & "guru/pegawai" hanyalah string (bukan enum DB).

---

## 4. Constraint & validasi saat ini

### 4.1 Unique index (semua GLOBAL, bukan scoped per role)

| Kolom | Sumber | Catatan |
|---|---|---|
| `users.email` | `0001_01_01_000000_create_users_table.php` | unique wajib |
| `users.username` | `000001_add_role_to_users` | unique nullable |
| `users.student_id` | `000015` | unique nullable (1 akun/siswa) |
| `employees.nip` | `000004` | unique nullable |
| `people.nik` | `000004` | unique (16 digit) |

**⚠️ Trap soft-delete:** `users` dan `employees` memakai `SoftDeletes`. Index unique MySQL tetap memblokir reuse `email`/`username`/`nip` milik baris soft-deleted (hanya NULL yang aman). Desain provisioning harus menangani "reaktivasi vs baris baru".

### 4.2 Locking / race condition

- **Tidak ada `lockForUpdate`** di seluruh codebase.
- Pola concurrency yang dipakai & bisa ditiru: **`DB::transaction` + unique constraint DB + `updateOrCreate`/`firstOrCreate`**:
  - `Keuangan/TuitionController::pay()` — updateOrCreate idempotent
  - `Perpustakaan/LibraryController` pinjam — `DB::transaction` (baris ±205) + cek stok
  - `Services/PpiExamScoringService.php` ranking — `DB::transaction` (baris 175)
  - `Support/PpdbService::accept()` — `DB::transaction` + guard NIK duplikat (`people_nik_unique`) + guard registrasi accepted lain

---

## 5. Kasus dobel-role (pegawai sekaligus wali/ortu)

- **Struktural mungkin:** `employees.person_id → people`, guru punya `employees.user_id = U`, dan `guardians.user_id = U` → satu akun jadi guru sekaligus ortu. Pivot `user_roles` sudah mendukung multi-role (contoh nyata: `ibu.aisy` diberi role tambahan `tata_usaha` di `UserRoleSeeder`).
- **Namun `guardians` TIDAK punya `person_id` dan TIDAK menyimpan NIK/identitas ortu** (kolom: `id, user_id, name`) → mustahil mencocokkan guardian ke person/employee saat ini.
- `PpdbService::accept()` membuat Guardian `user_id=null` + nama saja → ortu dari PPDB yang sebenarnya pegawai tidak bisa dideteksi.
- Data saat ini: 67 people = tepat 39 pegawai + 28 siswa, tidak ada tumpang tindih; 1 guardian (ibu.aisy) terpisah dari semua pegawai.

---

## Lampiran — Implikasi untuk fitur sinkronisasi

1. **Observer aman dipakai** — belum ada mass update status; semua lewat instance. Jangan menambah mass update di fitur baru, atau trigger event manual.
2. **Kunci matching tersedia**: `people.nik` (unique 16 digit), substring nama (terbukti cocok untuk 3 guru), `users.email`/`username`. Target provisioning: **36 employee & 27 siswa tanpa akun**.
3. **`users.student_id` unique** → maksimal 1 akun/siswa; `employees.user_id` bisa 1:N.
4. **Gerbang akses = `users.role`** (RoleMiddleware) — provisioning wajib set role yang benar; `user_roles` hanya bonus.
5. **Deaktivasi**: siswa via `student_enrollments.status`, pegawai via `employees.status`; belum ada alur "nonaktifkan akun/login" selain soft-delete manual di `UserController::destroy`.
6. **Trap unique + soft delete** & **pola transaksi+updateOrCreate** harus diikuti supaya provisioning bebas race.
