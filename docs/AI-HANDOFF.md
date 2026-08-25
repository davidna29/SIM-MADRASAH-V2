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
- **Sisa MVP (PRD 8.1):** Perluasan tagihan non-SPP (opsional).

## 2. Cara Menjalankan

```bash
# di folder proyek
php artisan serve            # buka http://localhost:8000
php artisan migrate:fresh --seed   # reset DB + data demo
php artisan test             # 121 test
npm run build                # asset produksi
```

**Akun demo (password semua `password`):**

| Role | Username |
|---|---|
| Super Admin | `admin` |
| Guru | `guru.umar` |
| Bendahara | `bendahara` |
| Siswa | `siswa.aisy` |
| Editor Berita | `editor.humas` |
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
- **Design system:** semua komponen shared di `resources/views/components/ui/*` (`x-ui.button`, `x-ui.table`, `x-ui.sheet`, `x-ui.select`, `x-ui.badge`, dst). Jangan styling ad-hoc; gunakan komponen.
- **Sidebar** dikonfigurasi di `config/navigation.php`; mendukung item `children` (sub-menu). Peran difilter otomatis dari middleware route.
- **pagination**: `x-ui.pagination` pakai URL nyata (bukan `#`).
- **spatie/laravel-activitylog v5**: namespace `Spatie\Activitylog\Models\Concerns\LogsActivity`, method `dontLogEmptyChanges()` (bukan `dontSubmitEmptyLogs`).
- **DomPDF**: nama file unduhan tidak boleh mengandung `/` (tahun ajaran "2026/2027" → ganti `-`).
- **MySQL strict**: FK & unique aktif — data invalid ditolak (beda dari SQLite dev).
- **Validasi** memakai Form Request; otorisasi memakai Policy + `authorize()` (Controller base sudah pakai `AuthorizesRequests`).

## 5. Langkah Modul Berikutnya

Disiplin (PRD Bagian 16): **frontend → persetujuan pengguna → backend → test**. Mulai dari:

1. **Perluasan tagihan non-SPP** (uang gedung, seragam, dll — jika diperlukan).

## 6. Keputusan Terbuka (PRD Bagian 24)

- Q1 retensi data alumni/PPDB (UU PDP) · Q2 format rapor · Q3 ekspor EMIS/Dapodik · Q4 persetujuan publikasi foto siswa · Q5 kuota disk & arsip file.

## 7. Artefak

- `PRODUCT.md` · `DESIGN.md` (+ `.impeccable/design.json`) · surface briefs di `.impeccable/surfaces/` · screenshots review di `.impeccable/review/`.
- Git: semua di-commit & di-push ke `origin/main` (GitHub `davidna29/SIM-MADRASAH-V2`).
