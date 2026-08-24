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
  - Kehadiran Siswa
  - Jadwal Pelajaran (rombakan penuh — lihat §3)
- **Sisa MVP (PRD 8.1):** Jurnal Mengajar · Tagihan & Pembayaran · Portal Orang Tua/Siswa · Rapor multi-mapel.

## 2. Cara Menjalankan

```bash
# di folder proyek
php artisan serve            # buka http://localhost:8000
php artisan migrate:fresh --seed   # reset DB + data demo
php artisan test             # 46 test
npm run build                # asset produksi
```

**Akun demo (password semua `password`):**

| Role | Username |
|---|---|
| Super Admin | `admin` |
| Guru | `guru.umar` |
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
- **Design system:** semua komponen shared di `resources/views/components/ui/*` (`x-ui.button`, `x-ui.table`, `x-ui.sheet`, `x-ui.select`, `x-ui.badge`, dst). Jangan styling ad-hoc; gunakan komponen.
- **Sidebar** dikonfigurasi di `config/navigation.php`; mendukung item `children` (sub-menu). Peran difilter otomatis dari middleware route.
- **pagination**: `x-ui.pagination` pakai URL nyata (bukan `#`).
- **spatie/laravel-activitylog v5**: namespace `Spatie\Activitylog\Models\Concerns\LogsActivity`, method `dontLogEmptyChanges()` (bukan `dontSubmitEmptyLogs`).
- **DomPDF**: nama file unduhan tidak boleh mengandung `/` (tahun ajaran "2026/2027" → ganti `-`).
- **MySQL strict**: FK & unique aktif — data invalid ditolak (beda dari SQLite dev).
- **Validasi** memakai Form Request; otorisasi memakai Policy + `authorize()` (Controller base sudah pakai `AuthorizesRequests`).

## 5. Langkah Modul Berikutnya

Disiplin (PRD Bagian 16): **frontend → persetujuan pengguna → backend → test**. Mulai dari:

1. **Jurnal Mengajar** (guru mencatat jurnal per jadwal/kelas/mapel).
2. **Tagihan & Pembayaran** (status dihitung dari akumulasi, bukan manual).
3. **Portal Orang Tua/Siswa** (lihat nilai, kehadiran, tagihan, rapor).
4. **Rapor multi-mapel** (memakai data nilai per mata pelajaran).

## 6. Keputusan Terbuka (PRD Bagian 24)

- Q1 retensi data alumni/PPDB (UU PDP) · Q2 format rapor · Q3 ekspor EMIS/Dapodik · Q4 persetujuan publikasi foto siswa · Q5 kuota disk & arsip file.

## 7. Artefak

- `PRODUCT.md` · `DESIGN.md` (+ `.impeccable/design.json`) · surface briefs di `.impeccable/surfaces/` · screenshots review di `.impeccable/review/`.
- Git: semua di-commit & di-push ke `origin/main` (GitHub `davidna29/SIM-MADRASAH-V2`).
