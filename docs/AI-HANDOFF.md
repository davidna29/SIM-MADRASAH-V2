# AI HANDOFF — SIM Madrasah

> File ini adalah titik masuk untuk melanjutkan proyek. Baca penuh sebelum mulai.
> Dokumen otoritatif: `PRODUCT.md` (produk & keputusan) · `DESIGN.md` (sistem visual "Mading").
> PRD lengkap: `PRD sim madrasah.md`.

## 1. Status Proyek

- **Target:** Madrasah Ibtidaiyah (kelas I–VI).
- **Tahap selesai:** 1–10 (analisis → DB → auth → RBAC dasar, per PRD). **Tahap 11** (design system & shell) & **Tahap 12** (walking skeleton) selesai.
- **Tahap 13 (modul MVP)** sedang berjalan — modul yang sudah selesai penuh (frontend + backend + test):
  - Data Guru & Pegawai
  - Mata Pelajaran (urutan drag-drop, form modal)
  - Kelas & Penempatan (jenjang I–VI, filter per tingkat)
  - Data Siswa (biodata via `people`, riwayat kelas)
  - Kehadiran Siswa (input harian + rekap bulanan + lock review)
  - Jadwal Pelajaran (rombakan penuh — lihat §3)
  - Jurnal Mengajar (guru catat per penugasan + lampiran, monitor Wakamad Kurikulum/Kepala Madrasah)
  - Rapor multi-mapel (redesain — lihat §4)
  - SPP Bulanan (nominal default, keringanan, pembayaran manual per bulan, portal ortu)
  - Portal Orang Tua (ringkasan anak: nilai/rapor, kehadiran bulanan, SPP)
  - Portal Siswa (Data Saya: nilai/rapor + PDF, kehadiran, SPP — read-only)
  - Dashboard nyata (menggantikan data demo — agregat kondisi madrasah)
  - Berita & Agenda (CMS — alur 8 status + halaman publik)
  - Prestasi & Pelanggaran siswa (kesiswaan)
  - Ekstrakurikuler (anggota, presensi sesi, penilaian predikat A–D + rata-rata)
  - Activity & Audit Log (halaman lihat/filter jejak aktivitas)
  - Galeri & Media (album foto + video tautan eksternal, halaman publik)
  - **Pengguna & Role Management** (CRUD pengguna, multi-role via pivot table, sidebar navigation)
  - **Konseling (BK)** (catatan sesi konseling, 3 level kerahasiaan, lampiran privat, authorization berbasis record)
-   **Wali Kelas / Homeroom** (penugasan guru sebagai wali kelas per tahun ajaran, halaman kelas show, replace otomatis)
-   **Inventaris Barang** (sarpras: barang, kategori, mutasi dengan alur persetujuan, pemeliharaan)
  - **Perpustakaan** (katalog buku, anggota siswa/pegawai, peminjaman/pengembalian, kategori, ebook URL eksternal)
- **Sisa MVP (PRD 8.1):** Perluasan tagihan non-SPP dihapus dari proyek (sesuai keputusan — tidak akan dikerjakan).

## 2. Cara Menjalankan

```bash
# di folder proyek
composer serve               # buka http://localhost:8000 — menaikkan limit upload PHP
php artisan migrate:fresh --seed   # reset DB + data demo
php artisan test             # 289 test
npm run build                # asset produksi
```

> **Jangan jalankan `php artisan serve` polos** untuk fitur unggah: script `serve` di `composer.json` membawa `-d post_max_size=64M -d upload_max_filesize=8M` (unggahan galeri/prestasi bisa ±40 MB). Deploy shared hosting: samakan via cPanel *MultiPHP INI Editor* atau `.user.ini`.

**Akun demo (password semua `password`):**

| Role | Username |
|---|---|
| Super Admin | `admin` |
| Guru | `guru.umar` |
| Bendahara | `bendahara` |
| Siswa | `siswa.aisy` |
| Editor Berita | `editor.humas` |
| Petugas Perpustakaan | `pustakawan` |
| Orang Tua | `ibu.aisy` |

**Database:** MySQL 8.4 lokal — db `sim_madrasah`, user `sim_madrasah` / `SimMadrasah2026!`. DB test terpisah: `sim_madrasah_test` (diatur di `phpunit.xml`).

## 3. Modul Jadwal Pelajaran (rombakan penuh)

Arsitektur baru menggantikan jadwal lama:

- **`schedule_models`** — Model Jadwal fleksibel/custom: nama, tahun, jam mulai, maks jam/hari, is_active. Admin bebas menambah model (kurikulum berubah tiap tahun).
- **`schedule_model_grade_levels`** — tingkatan (I–VI) yang dicakup model; beberapa model boleh aktif asal **tidak tumpang tindih** tingkatan (validasi hard-block).
- **`schedule_model_slots`** — slot template: jam ke-, mulai/akhir, `is_break` (non-KBM: istirahat/upacara).
- **`schedule_cells`** — tabel master penyusunan: `(schedule_model_id, academic_year_id, class_group_id, day, period_no)` → `teacher_id` + `subject_id`. **Unique menyertakan `academic_year_id`** (data antar tahun bisa dibedakan/salin).

Route utama: `/akademik/jadwal-pelajaran/model` (Model), `/akademik/jadwal-pelajaran/penyusunan` (tabel master), `/akademik/jadwal-pelajaran/kelas/{class}` & `/guru/{guru}` (view turunan + cetak PDF).

Fitur penting:
- Tabel master **hari vertikal ke bawah** (SENIN → … → SABTU), kolom rombel, baris jam ke-.
- Sel diisi via **modal picker autocomplete** (guru & mapel); **Enter** untuk menyematkan; tombol **X** menghapus isi sel.
- **Validasi konflik guru hard-block**: guru yang sama di rombel lain pada hari+jam sama → ditolak dengan daftar bentrok.
- **Generate**: "Kerangka kosong" = **reset** isian + notif; "Salin tahun sebelumnya" = proteksi (tidak menimpa data yang ada).
- Cetak PDF per kelas & per guru (DomPDF).
- Halaman admin "Penugasan Mengajar" lama **dihapus** (redundant); model `TeacherAssignment` tetap dipakai alur guru input nilai.

## 4. Konvensi & Jebakan yang Sudah Terjadi

- **Route model binding:** nama route parameter **harus persis sama** dengan variabel method (`{employee}` → `Employee $employee`, `{model}` → `ScheduleModel $model`, `{student}` → `Student $student`). Jika beda, Laravel skip binding → model kosong (bug yang pernah muncul berkali-kali).
- **RoleMiddleware** mendukung beberapa role dipisah `|` (`role:super_admin|wakamad_kurikulum`) — pemecahan baru ditambahkan di middleware (bukan hanya di parser sidebar). Route lintas-role yang bukan super_admin **jangan** ditaruh di dalam group `role:super_admin` (middleware berlapis = AND).
- **Kehadiran Siswa:** rute `kehadiran.index/store/rekap` kini di group 6 role (`super_admin|wakamad_kesiswaan|wali_kelas|guru|kepala_madrasah|wakamad_kurikulum`) — sebelumnya super_admin-only. **Lock tanggal:** `AttendanceController::assertDateEditable()` — non-privileged hanya boleh tanggal hari ini (index & store abort 403 untuk tanggal lain). **Review:** `attendance_reviews` (unique `class_group_id`+`attendance_date`) di-`updateOrCreate` saat "Simpan Kehadiran"; `class_group_id` diturunkan dari enrollment payload (bukan field form). **Rekap bulanan** (`kehadiran.rekap`, `/kesiswaan/kehadiran/rekap-bulanan`): tabel siswa × tanggal, `•`/S/I/A, hari belum direview = `–` (bukan Alpha), hari efektif = jumlah tanggal direview (pembagi %), Jumlah=S+I+A, % Hadir per siswa, footer ringkasan kelas (total S/I/A, jumlah ketidakhadiran, % ketidakhadiran & % kehadiran).
- **Modul Jurnal Mengajar:** `teaching_journals` (per `teacher_assignment_id` + tanggal + jam ke opsional; unik dicek di controller karena kolom jam nullable). Guru: `/guru/jurnal*` (index → show + form → edit). Monitor: `/akademik/jurnal-mengajar` (Wakamad Kurikulum/Kepala Madrasah). Status `draft`/`terisi` via tombol submit dua. **Jurnal Mingguan** (role `guru|tata_usaha|wakamad_kurikulum|kepala_madrasah|super_admin`): dua tampilan agregat read-only meniru formulir fisik — `/akademik/jurnal-mengajar/mingguan` (per Kelas: filter kelas+Senin, header bulan/rentang/kelas-semester/jumlah L-P dari enrollment aktif, kolom Guru=`recorder`) dan `/akademik/jurnal-mengajar/mingguan-guru` (per Guru: filter guru+Senin, header beban penugasan "X rombel · Y mapel", kolom Kelas). Keduanya filter `assignment.*`, hanya status `terisi`, hari kosong ditampilkan "Belum ada jurnal terisi", urut tanggal→`period_no`, tombol Cetak `window.print()` (chrome app disembunyikan via class `app-sidebar/app-topbar/app-footer` di CSS `@media print`).
- **Rapor multi-mapel (redesain):** `reports` = parent, **1 per siswa+tahun+semester** (unique `report_unique` dikembalikan — dulu di-drop di 000003 karena tiap terbit versi baru; sekarang `version` tetap 1, idempotent). Detail nilai di tabel baru **`report_items`** (unique `report_id`+`subject_code`, snapshot `subject_name/class_name/teacher_name/score/sort_order`). `NilaiController::terbitkan()` → `Report::firstOrCreate` + `items()->updateOrCreate`, idempotent (republish memperbarui, bukan menambah baris). Migrasi **000013 konsolidasi/backfill**: salin item dari snapshot single-mapel lama + gabungkan rapor terfragmentasi jadi satu parent + pasang unique. `penugasan()`/`isClassReport()` filter via `whereHas('items')`. View guru/ortu/PDF rapor loop `$report->subjectItems()` (fallback ke snapshot lama bila items kosong); predikat via `App\Support\Rapor::predikat()`. Catatan: PRD 7.6 "versioning rapor" sengaja ditunda (snapshot tetap, versi tunggal).
- **Modul SPP Bulanan:** tabel baru `tuition_settings` (nominal default, unique per tahun ajaran), `tuition_overrides` (keringanan per siswa, unique `student_enrollment_id`+`academic_year_id`), `tuition_payments` (unique `student_enrollment_id`+`academic_year_id`+`bulan`; bulan = angka kalender asli; nominal unsignedInteger Rupiah). Input **manual** per bulan oleh `bendahara|tata_usaha|super_admin` (tidak ada generate massal). Route: `/keuangan/spp` (index rekap, role 4 — `kepala_madrasah` read-only), `/keuangan/spp/nominal` & `/keringanan` & POST `/bayar` (role 3). Group middleware **terpisah** (bukan dalam `role:super_admin`). `TuitionController::pay()` pakai `updateOrCreate` idempotent; status `lunas` otomatis bila `tanggal_bayar` terisi. Portal ortu: `/ortu/spp` + `/ortu/spp/{student}` (read-only, `owns()` via guardian). Role `bendahara` & `tata_usaha` adalah string di kolom `users.role` (bukan enum DB) — sudah dipakai di `config/navigation.php`; user demo `bendahara` di seeder. Item sidebar placeholder **"Tagihan & Pembayaran" dihapus** (SPP menggantikannya). Nominal mendukung **0** (gratis/keringanan penuh) — frontend `min="0" step="1"` & backend `min:0` di `pay()`/`overridesStore()`.
- **Portal Orang Tua (ringkasan anak):** `Ortu\DashboardController@ringkasan` → `GET /ortu/anak/{student}` (`ortu.ringkasan`, group `role:orang_tua`, `owns()` via guardian). Halaman `pages/ortu/ringkasan.blade.php` merangkum **Nilai/Rapor** (`report->subjectItems()`, placeholder bila belum terbit), **Kehadiran bulanan** (`Attendance` per enrollment dikelompokkan per bulan → H/S/I/A), dan **SPP** (jumlah lunas dari 6 bulan + pembayaran terakhir). Dashboard ortu kini punya tombol "Buka Ringkasan" per anak. Murni agregat read-only (PRD 8.2) — tanpa data baru.
- **Portal Siswa:** role `siswa` (string) + relasi **`users.student_id`** (FK unique nullable, migration 000015) → `User::student()`. Group `role:siswa`, prefix `/siswa`: `siswa.dashboard` (Data Saya — ringkasan nilai/rapor, kehadiran, SPP), `siswa.rapor` (+ `rapor.unduh` PDF reuse `pdf.rapor`), `siswa.spp`. Logika agregat dipindah ke **`App\Support\RingkasanSiswa::build()`** — dipakai bersama `Ortu\DashboardController@ringkasan` (DRY). `AuthController::redirectToRole` → `siswa.dashboard`. Akun demo `siswa.aisy` (terhubung Aisyah NIS 240101). Rapor/spp siswa 404 bila data belum ada.
- **Dashboard nyata:** `App\Support\DashboardData` (KPI: siswa aktif, guru/pegawai aktif, SPP terkumpul semester, % kehadiran hari ini; Perlu Tindakan: rombel belum review hari ini, SPP belum lunas bulan berjalan, rapor belum terbit; Kehadiran per Rombel; 6 tagihan lunas terakhir; 8 `activity_log` terakhir dengan deskripsi dipetakan ke Bahasa Indonesia). `DashboardController@index` menggantikan closure; route `/dashboard` pindah ke group 6 role admin (`super_admin|kepala_madrasah|wakamad_kurikulum|wakamad_kesiswaan|bendahara|tata_usaha`), nav "Dashboard" tidak lagi `['*']`. Tabel tagihan pakai `:empty="$tagihan->isEmpty()"` (bukan hanya `emptySlot`).
- **Berita & Agenda (CMS):** tabel `articles` (8 status: draft→diajukan→review→revisi→disetujui→dijadwalkan→publish→arsip; transisi divalidasi `Article::transitions()`) & `agenda` (agenda/pengumuman, target publik/internal, masa tampil). Role string: kontributor `guru` + `editor_berita|wakamad_humas|kepala_madrasah|tata_usaha|super_admin`. Admin: `/publikasi/berita*` & `/publikasi/agenda*` (group `cms.`), policy `ArticlePolicy`/`AgendaPolicy`. **Auto-publish**: command `berita:publish-terjadwal` dijadwalkan `everyMinute()` di `routes/console.php` (butuh cron `schedule:run`). Halaman publik tanpa auth: `/berita` (hanya `publish`), `/agenda` (aktif, target publik, dalam masa tampil), layout `x-layouts.publik`. `storage:link` sudah dibuat (featured image). User demo `editor.humas`.
- **Prestasi & Pelanggaran (kesiswaan):** tabel `achievements` (jenis akademik/nonakademik, tingkat sekolah…internasional, status_verifikasi menunggu/terverifikasi/ditolak, status_publikasi publik/internal) & `offenses` (tingkat ringan/sedang/berat, poin 0–100, pemanggilan_ortu, surat_peringatan sp1–3, status_penyelesaian proses/selesai/dibebaskan). Route `/kesiswaan/prestasi*` & `/kesiswaan/pelanggaran*` (group role `super_admin|wakamad_kesiswaan|wali_kelas|guru|guru_bk|kepala_madrasah`; Policy: `kepala_madrasah` hanya lihat, `guru_bk` hanya pelanggaran, wali/guru hanya prestasi, hapus hanya wakamad/super_admin). Form memuat siswa per kelas via `class_group_id`. Nav "Prestasi & Pelanggaran" kini parent. Seeder: contoh terverifikasi & selesai. **Import Excel prestasi** (paket baru `maatwebsite/excel` v4): `App\Exports\PrestasiTemplateExport` (unduh template .xlsx, FromArray+WithHeadings), `App\Imports\PrestasiImport` (ToArray+WithHeadingRow — catatan v4: `array()` return `void`, data dibaca `Excel::toArray`). Alur: `GET /prestasi/template` · `GET /prestasi/import` · `POST /import/preview` (parse+validasi, simpan ke session `prestasi_import`) · `GET /import/preview` (tabel valid/error) · `POST /import/simpan` (insert hanya valid, cek duplikat `student+nama_kegiatan`, `DB::transaction`) · `POST /import/batal`. Validasi per baris: NIS harus di enrollment aktif, jenis/tingkat/status publikasi valid, tanggal dinormalisasi (serial Excel/string).
- **Activity & Audit Log:** halaman read-only `Pemeliharaan\ActivityLogController@index` → `/pemeliharaan/activity-log` (role `super_admin`). Query `Spatie\Activitylog\Models\Activity` (tanpa tabel baru), filter `log_name` (distinct), `user_id` (causer), `q` (deskripsi), rentang `from`/`to`; urut desc + paginate 25. View menampilkan badge log_name, causer, deskripsi **dipetakan ke Bahasa Indonesia** via `App\Support\ActivityText::readable()` (dipakai juga dashboard — `DashboardData::aktivitasText` kini delegasi ke sana), subjek, dan panel expand `properties`/`attribute_changes`. Nav "Activity & Audit Log" kini menunjuk route (bukan placeholder). Empty state saat tabel kosong.
- **Galeri & Media (MOD-010):** tabel `media_albums` (slug unique, status publik/**privat**, cover_image) & `media_items` (tipe foto/video, file_path, video_url, caption, sort_order). Pengelola: `super_admin|wakamad_humas|editor_berita|kepala_madrasah|tata_usaha` (`MediaAlbumPolicy`, tanpa guru). Admin `/publikasi/galeri*`: CRUD album + kelola isi album (**multi-upload foto** maks 10×4 MB, tambah **video via URL eksternal** — sesuai catatan PRD agar storage tidak penuh, hapus item, jadikan cover; cover otomatis dari foto pertama bila kosong). Publik: `/galeri` + `/galeri/{album:slug}` (hanya status `publik`; video YouTube otomatis di-embed). Layout publik punya link "Galeri". Seeder: album publik berisi placeholder PNG + album privat.
- **Ekstrakurikuler (MOD-033):** tabel `extracurriculars` (pembina_id = user role `guru` yang ditunjuk, hari/waktu/lokasi, status aktif/nonaktif), `extracurricular_members` (unique ekskul+enrollment — anggota terikat tahun ajaran), `extracurricular_attendances` (unique ekskul+enrollment+tanggal; `predikat` A/B/C/D nullable hanya saat Hadir; poin A=4…D=1). Route `/kesiswaan/ekstrakurikuler*` (`ekskul.*`, group role union sama prestasi/pelanggaran). **Policy:** super_admin/wakamad kelola semua, **pembina hanya ekskul miliknya**, kepala/wali/guru lain lihat saja. Rekap per anggota: hitungan H/I/S/A + rata-rata poin otomatis → predikat akhir via `ExtracurricularAttendance::predicateFromAverage()` (≥3.5 A / ≥2.5 B / ≥1.5 C). Form presensi: tanggal via filter GET terpisah (bukan submit POST prematur); kolom predikat disabled kecuali status Hadir (Alpine per baris).
- **Design system:** semua komponen shared di `resources/views/components/ui/*` (`x-ui.button`, `x-ui.table`, `x-ui.sheet`, `x-ui.select`, `x-ui.badge`, dst). Jangan styling ad-hoc; gunakan komponen.
- **Sidebar** dikonfigurasi di `config/navigation.php`; mendukung item `children` (sub-menu). Peran difilter otomatis dari middleware route. **Item yang belum dibangun ditandai `'placeholder' => true`** — sidebar menyembunyikannya otomatis (klik menu mati membingungkan pengguna); ketika modul selesai, cukup hapus flag tersebut. Grup & parent kini **akordeon** (state tersimpan di localStorage `sim-nav-open`; grup/parent yang berisi halaman aktif auto-terbuka via slug di `x-data.open`). Ada **pencarian menu** di atas nav (Alpine `query`, item diberi `data-nav-match` = label+parent+grup). Ikon grup via `'icon'` di level grup. Label leaf tampil di mobile. **Jebakan Blade+Alpine:** (1) `:class="expr"` pada tag komponen (`x-svg-*`) dikompilasi server-side → untuk class reaktif gunakan `x-bind:class`; (2) jangan sisipkan `@json` di dalam atribut HTML/x-data (tanda kutip ganda memotong atribut) — taruh di `<script type="application/json" id="...">` lalu baca via `document.getElementById(...).textContent`; (3) `this.$el` di dalam method/getter x-data saat dievaluasi dari direktif menunjuk **elemen direktif**, bukan root komponen — jangan bergantung padanya di dalam ekspresi `x-show` (pakai magic `$root` inline untuk query DOM lintas elemen).
- **pagination**: `x-ui.pagination` pakai URL nyata (bukan `#`).
- **spatie/laravel-activitylog v5**: namespace `Spatie\Activitylog\Models\Concerns\LogsActivity`, method `dontLogEmptyChanges()` (bukan `dontSubmitEmptyLogs`).
- **DomPDF**: nama file unduhan tidak boleh mengandung `/` (tahun ajaran "2026/2027" → ganti `-`).
- **MySQL strict**: FK & unique aktif — data invalid ditolak (beda dari SQLite dev).
- **Validasi** memakai Form Request; otorisasi memakai Policy + `authorize()` (Controller base sudah pakai `AuthorizesRequests`).
- **Modul Pengguna & Role:** tabel `user_roles` (pivot multi-role, unique `user_id`+`role`), `UserController` di `Fondasi/` (super_admin only), sidebar "Pengguna & Role" navigasi ke `/fondasi/pengguna`. Role utama di kolom `users.role`, role tambahan di tabel `user_roles`. `User::allRoles()` menggabungkan keduanya. Soft deletes diaktifkan untuk users (proteksi self-delete + last super admin di Policy).
- **Modul Konseling (BK):** tabel `counseling_sessions` (FK `student_enrollment_id` + `counselor_user_id`, 3 level kerahasiaan: `guru_bk_only`, `plus_kepala`, `plus_wali_kelas`). Policy record-level: Guru BK lihat semua sesi (termasuk yang dibuat admin/ lain); Kepala Madrasah lihat `plus_kepala` & `plus_wali_kelas`; Wali Kelas hanya `plus_wali_kelas`. Lampiran disimpan di `storage/app/private/counseling/` (disk `local`). Scope `visibleTo()` di model untuk filter query. Route `/kesiswaan/konseling*`, sidebar "Konseling (BK)" untuk role `super_admin|guru_bk|kepala_madrasah`.
- **Modul Wali Kelas (Homeroom):** tabel `homeroom_assignments` (unique per class+year, replace otomatis: lama → `selesai`). Relasi `ClassGroup::homeroom()` returns `HasOne` aktif tahun berjalan. Controller `Akademik\HomeroomController` (store + destroy). Routes di middleware `super_admin|wakamad_kurikulum`. Tampilan di halaman `kelas/show.blade.php` sebagai sheet "Wali Kelas". Guru BK login redirect ke `konseling.index`.
- **Modul Perpustakaan:** tabel `library_categories`, `library_books` (auto-code `BUK-YYYYMM-NNN`, `total_qty`/`available_qty`, `is_ebook`+`ebook_url`), `library_members` (siswa/pegawai, auto-no `ANG-YYYY-001`, snapshot nama), `library_loans` (pinjam/kembali/terlambat, decrement/increment stok). Route `/perpustakaan*` (group `super_admin|pustakawan|kepala_madrasah`; kepala read-only via Policy). Policy per model: `LibraryBookPolicy`, `LibraryMemberPolicy`, `LibraryCategoryPolicy` — pustakawan/super_admin kelola penuh, kepala lihat saja. Akun demo `pustakawan`. Seeder: 5 kategori, 8 buku (1 ebook), 2 anggota, 2 contoh peminjaman. Sidebar "Perpustakaan" parent (Katalog Buku + Anggota + Kategori). Filter katalog (kategori/status/is_ebook/q) & modal Tambah Anggota/Kategori memakai komponen `x-ui.modal`; picker anggota memuat siswa aktif per rombel TA berjalan + pencarian nama/NIS, pegawai aktif + pencarian (pola Alpine JSON ala `jadwal/penyusunan`). `loanStore` menolak pinjam bila anggota (siswa/pegawai) sudah punya pinjaman aktif (`status=dipinjam`) untuk buku yang sama — cegah stok berkurang ganda. `update` sinkron `available_qty` otomatis berdasarkan selisih `total_qty` (menambah stok = menambah tersedia; mengurangi stok ditolak bila melewati jumlah dipinjam). Test: 23 feature test di `PerpustakaanModuleTest` (setUp membuat AcademicYear aktif — `AcademicYear::active()` dipakai controller).
- **Modul Surat Masuk/Keluar:** tabel `letters` (type masuk/keluar, nomor surat, tanggal, dari/ke, perihal, status diterima/diproses/selesai/arsip, prioritas biasa/penting/segera/rahasia, kategori, disposisi ke/catatan, file lampiran) & `letter_categories` (kategori surat). Route `/tu/surat*` (group `super_admin|tata_usaha`). Policy `LetterPolicy` — super_admin & tata_usaha CRUD, super_admin saja disposisi. Sidebar "Surat Masuk / Keluar" di group "Keuangan & TU" dengan children (Surat Masuk + Surat Keluar). Auto-number surat keluar: `001/TK/MM/YYYY`. Filter (status/kategori/prioritas/tanggal/search). Seeder: 9 kategori, 5 surat masuk, 4 surat keluar. Test: 10 feature test di `LetterModuleTest`.

- **Modul Portofolio Digital:** read-only aggregate dari tabel yang sudah ada — tidak ada migrasi baru. `PortofolioService` menggabungkan: rapor (`Report`+`ReportItem`), kehadiran (`Attendance`), prestasi (`Achievement` — via `student_id`), pelanggaran (`Offense` — via `student_id`), ekstrakurikuler (`ExtracurricularMember`+`ExtracurricularAttendance`), SPP (`TuitionPayment`). Route `/kesiswaan/portofolio*` (group `super_admin|wakamad_kesiswaan|wali_kelas|guru_bk|kepala_madrasah`). QR Code via `simplesoftwareio/simple-qrcode` (signed URL token, expiry 30 hari). Cetak PDF via DomPDF. Policy via Gate abilities (`portfolio.viewAny`, `portfolio.view`) — wali kelas hanya lihat siswa rombelnya. Verifikasi publik `/portofolio/{token}` (harus login). Test: 10 feature test di `PortofolioModuleTest`.

- **Modul Pusat Laporan:** read-only aggregate dari tabel yang sudah ada — tidak ada migrasi baru. `LaporanService` menyediakan 6 jenis laporan: rekap akademik (rapor per kelas), rekap kehadiran (H/S/I/A per kelas), rekap keuangan (SPP per kelas), rekap kesiswaan (prestasi + pelanggaran), rekap tenaga (guru/pegawai per role), rekap perpustakaan (buku, pinjaman, anggota). Route `/pemeliharaan/laporan*` (group `super_admin|kepala_madrasah|wakamad_kurikulum|wakamad_kesiswaan|bendahara`). Export PDF (DomPDF) & CSV (streaming dengan BOM UTF-8). Test: 10 feature test di `LaporanModuleTest`.

- **Modul Backup & Restore:** file-based — tidak ada migrasi baru. `BackupService` (`app/Support/BackupService.php`) menangani: backup database via `mysqldump` → `storage/app/backups/db/YYYY-MM-DD_HHMMSS.sql`, backup file storage via `ZipArchive` → `storage/app/backups/files/`, upload `.sql`/`.zip` → `storage/app/backups/uploads/`. Route `/pemeliharaan/backup*` (group `super_admin` only). Controller `Pemeliharaan\BackupController`: index (daftar backup + KPI), storeDb, storeFiles, download (URL-encoded filename), upload, restore (konfirmasi ketik "RESTORE"), destroy. Sidebar "Backup & Restore" di group "Pemeliharaan Sistem" (placeholder dihapus). View menampilkan: 3 KPI card (total backup, total ukuran, backup terakhir), tombol backup database & file, form upload, tabel daftar backup dengan aksi download/restore/delete. Restore: `SET FOREIGN_KEY_CHECKS=0` → drop semua tabel (kecuali `migrations`) → `DB::unprepared(sql)` → `migrate:force`. Activity log tercatat via `activity('pemeliharaan')`. Test: 16 feature test di `BackupModuleTest`. Filename di-route di-`urlencode()` untuk mengakomodasi `/` dalam path.

- **Modul Pengaturan Sistem:** tabel `settings` (key-value, unique key). Model `Setting` dengan static helpers `get()`, `set()`, `getAll()`, `setMany()`. Route `/fondasi/pengaturan` (group `super_admin`). Controller `Fondasi\PengaturanController`: index (form identitas lembaga), update (validasi + upload logo). Sidebar "Pengaturan Sistem" di group "Sistem" (placeholder dihapus). View menampilkan 6 sections: Data Utama (nama, NSM, NPSN, jenjang RA/MI/MTs/MA, status negeri/swasta, tahun berdiri), Alamat & Lokasi (jalan, desa, kecamatan, kabupaten, provinsi, kode pos, lat/lng), Kontak (telepon, email, website), Legalitas (SK pendirian + tanggal, SK izin operasional), Akreditasi & Naungan, Logo (upload JPG/PNG max 2MB). Default settings di-seed via `SettingSeeder`. Hardcoded "MTs Al-Ikhlas Mulia" di layout app, publik, login diganti `Setting::get('madrasah_name')`. Test: 8 feature test di `PengaturanModuleTest`.

- **Modul PPDB Daring:** tabel `ppdb_registrations` (~90 kolom: data siswa A, kesehatan B, berkebutuhan khusus C, alamat siswa D, orang tua E, alamat orang tua F, sekolah asal G, admin-only: kelas/rombel/NIS). Multi-step wizard publik (7 step via Alpine.js) di `/ppdb` tanpa auth — form pendaftaran lengkap dengan validasi (Form Request `PpdbRegistrationRequest` dipakai publik & admin edit). Admin routes `/ppdb/admin*` (group `super_admin|tata_usaha|kepala_madrasah`). Controller `Publik\PpdbController` (index + store) dan `Ppdb\AdminPpdbController` (index, show, edit, update, accept, reject, exportExcel). Accept workflow: buat Person → Student (tanpa NIS) → Guardian → update status; **NIS & kelas diisi belakangan di modul Data Siswa** (PPDB tidak lagi mengatur NIS/kelas). Export Excel via `Maatwebsite\Excel` mapping kolom EMIS-compatible + 5 kolom link GDrive. Enum reusable: `Pendidikan`, `Pekerjaan`, `Penghasilan`, `Kecamatan`. Migration `guardians.user_id` nullable. Google Drive links untuk dokumen. Fix: label pendidikan/pekerjaan/penghasilan di detail admin. **Simplifikasi (2026):** fitur Generate NIS & Tentukan Kelas **dihapus** (route + method + view + tabel `nis_counters` di-drop via migrasi `000032`); tambah tombol Edit (semua field, semua status); fix tombol Tolak (modal berbagi scope Alpine). Test: **26 feature test** di `PpdbModuleTest`. Sidebar "PPDB Daring" di group "Publikasi". Link PPDB di navigasi publik.

### 4.1 PPDB — Catatan Progress Sementara (sesi berjalan)

Status: **SELESAI (final)** — semua umpan balik user dari sesi observasi sudah dikerjakan & ter-test (28 test PPDB, 297 test total hijau). Lihat `docs/PPDB-ALUR-KERJA.md` untuk petunjuk alur + watchlist.

Yang SUDAH dikerjakan (akumulasi sesi):

1. **Form publik** — wizard 7 langkah lengkap (~90 field), validasi ketat, dokumen via link Google Drive.
2. **Opsi Pendidikan/Pekerjaan** dikoreksi ke format EMIS + label di form/detail/enum disinkronkan.
3. **Urutan field** — Tanggal Lahir Ayah & Wali setelah NIK.
4. **Detail admin** menampilkan label (bukan kode mentah) + Tempat Lahir Ibu.
5. **Generate NIS** — field "Acuan Nomor Urut Terakhir" + counter per tahun ajaran.
6. **Penentuan kelas** — dropdown kelas existing + validasi + jumlah siswa per kelas.
7. **Seeder** — `PpdbDemoSeeder` (default `??=`, record lengkap Farhan).
8. **(BARU) NIS ditunda dari Accept** — user tidak suka NIS otomatis saat Terima. Sekarang `PpdbService::accept()` membuat `Person`+`Student`(tanpa NIS)+`Guardian`, status `accepted`; NIS diberikan massal di `/ppdb/admin/generate-nis` (urut abjad, counter berlanjut). `students.nis` dijadikan **nullable** (migration `000031`). `batchGenerateNis()` juga menyinkronkan `students.nis`. Pesan sukses accept diubah ("Tetapkan NIS di menu Generate NIS").
9. **(BARU) Penentuan kelas massal** — `assignClassBulk` (checkbox + 1 kelas) & `assignClassDistribute` (sebar rata per tingkat) via route `ppdb.assign-class-bulk` / `ppdb.assign-class-distribute` (didefinisikan SEBELUM `{registration}`). View `assign-class.blade.php` dapat checkbox, panel "Tetapkan Kelas Terpilih" & "Sebar Rata per Tingkat" (Alpine).
10. **(BARU) Export Excel** — `exportMapping()` diurutkan mengikuti urutan field form (Langkah 1–7) + ditambah 5 kolom link GDrive (`scanned_kk`, `scanned_kk_wali`, `scanned_akta`, `scanned_ijazah`, `scanned_photo`).
11. **(BARU) Petunjuk alur di UI** — partial `pages/ppdb/partials/steps.blade.php` (kartu "Alur Pengerjaan Admin" + catatan guardrail per halaman) disertakan di `index`, `show`, `nis-preview`, `assign-class`.

**Hasil observasi user (sesi lalu) — semua terpenuhi:**
- [x] `/ppdb/admin/4` tampil benar dengan semua label.
- [x] Flow NIS: tidak lagi otomatis saat accept; calon `accepted` masuk ke Generate NIS, diberi NIS urut abjad (rekomendasi diterima).
- [x] Flow penentuan kelas: ditambah bulk + sebar rata agar ratusan siswa tak dipilih satu-satu.
- [x] Field form sudah sesuai Google Form asli (tanpa perubahan).
- [x] Export: kolom diurutkan + link GDrive disertakan.

**Jebakan yang perlu diingat:**
- `class_group_id` di `student_enrollments` NOT NULL → enrollment PPDB hanya dibuat saat assign kelas.
- `guardians.user_id` nullable (orang tua PPDB belum punya akun).
- `students.nis` **nullable** sejak `000031` — PPDB menunda NIS. Jalur buat siswa manual (StoreStudentRequest) tetap `required`.
- Jangan pakai `{{ }}` di dalam tag komponen Blade — pakai `:prop` (PHP) / `x-bind:` (Alpine).
- Route PPDB fixed HARUS didefinisikan SEBELUM `{registration}` wildcard.
- **Watchlist (risiko, lihat `docs/PPDB-ALUR-KERJA.md` §4):** jangan tolak siswa yg sudah diterima+koba kelas (data menggantung); jangan ganti TA aktif sebelum finalisasi NIS; "Acuan" bisa bentrok unique `students.nis`; assign kelas belum ada batas kapasitas; export ikut menyertakan `rejected`.
- **(BARU) Guard NIK duplikat saat accept:** `PpdbService::accept()` menolak (ValidationException → pesan ramah, bukan 500) bila NIK sudah ada sebagai `Person` (`people_nik_unique`) atau dipakai registrasi lain yang `accepted`. Catatan: data PPDB yang NIK-nya duplikat (mis. registrasi id 5 = NIK `6172010101010001` yang sama dengan id 1) memang tak bisa diterima — operator harus perbaiki NIK-nya.
- **(BARU) Simplifikasi alur PPDB:** Generate NIS & Tentukan Kelas **dihapus** (route/method/view dihapus, tabel `nis_counters` di-drop `000032`). Alur baru: daftar → Terima/Tolak → Edit (opsional) → Export; **NIS & kelas dilengkapi di modul Data Siswa**. Tombol Edit di detail (semua field, semua status, via `ppdb.edit`/`ppdb.update` + `PpdbRegistrationRequest`). **Fix tombol Tolak:** sebelumnya tombol & modal pakai `x-data` terpisah sehingga modal tak pernah terbuka — kini keduanya berbagi scope `x-data="{ open:false }"` di parent div.

## 5. Langkah Modul Berikutnya

Disiplin (PRD Bagian 16): **frontend → persetujuan pengguna → backend → test**. Mulai dari:

1. **Perluasan tagihan non-SPP** — dihapus dari proyek (keputusan pengguna).
2. **Backup & Restore** — selesai.

## 6. Keputusan Terbuka (PRD Bagian 24)

- Q1 retensi data alumni/PPDB (UU PDP) · Q2 format rapor · Q3 ekspor EMIS/Dapodik · Q4 persetujuan publikasi foto siswa · Q5 kuota disk & arsip file.

## 7. Artefak

- `PRODUCT.md` · `DESIGN.md` (+ `.impeccable/design.json`) · surface briefs di `.impeccable/surfaces/` · screenshots review di `.impeccable/review/`.
- Git: semua di-commit & di-push ke `origin/main` (GitHub `davidna29/SIM-MADRASAH-V2`).
