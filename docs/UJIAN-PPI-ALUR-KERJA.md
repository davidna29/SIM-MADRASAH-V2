# UJIAN PPI — ALUR KERJA & PANDUAN TES MANUAL

> Modul **Ujian PPI Kelas VI (Munaqasah)** — `ppi_exam_*`. Event ujian akhir kelas VI, sekali per tahun ajaran:
> setoran hafalan → ujian lisan → perhitungan otomatis → dokumen (teks MC + berita acara) → koreksi (audit log) → arsip (import).
> Lokasi menu: **Akademik → Ujian PPI**.

---

## A. Persiapan (sekali)

```bash
php artisan migrate:fresh --seed   # reset DB + seluruh seeder (termasuk PpiExamSeeder)
composer serve                     # buka http://localhost:8000 (pakai composer serve, jangan php artisan serve polos)
```

Password semua akun demo: `password`.

| Role | Username | Peran di modul |
|---|---|---|
| Super Admin | `admin` | semua + **Buka Kunci** konfigurasi saat berlangsung |
| Guru (Penguji I Ruang 1 & Pembimbing Grup A) | `guru.umar` | input ujian (aspek Penguji I) + input setoran (Grup A) |
| Guru (Penguji II Ruang 1 & Pembimbing Grup B) | `guru.imam` | input ujian (aspek Penguji II) + input setoran (Grup B) |
| Guru (Penguji III Ruang 1) | `guru.nurul` | input ujian (aspek Penguji III) |
| Kepala Madrasah | `kepala` (BARU) | Rekap read-only + export |

> Catatan: Wakamad Kurikulum murni (role primer) tidak punya akun demo — RoleMiddleware membaca **role primer**
> (`users.role`). Untuk menguji role wakamad: **Pengguna & Role → Tambah Pengguna → role `wakamad_kurikulum`** (login sendiri),
> atau cukup uji lewat `admin` (Super Admin mencakup semua kemampuan wakamad + buka kunci).

### Data demo setelah `--seed`

- **Periode**: "Ujian PPI Kelas VI TP 2026/2027" — status **Berlangsung** (konfigurasi terkunci), bobot 25/25/25/25.
- **Skala**: A+ 90–100, A 80–89, B 70–79, C 60–69, D 0–59 (tidak lulus).
- **Aspek**: 7 induk / 62 item persis dokumen rencana (P1: Wudhu+Praktik Shalat, P2: Tilawah+Jenazah+Hadis, P3: Do'a Harian+Pengetahuan Agama); 24 materi hafalan.
- **Ruang**: Ruang 1 (Umar=1, Imam=2, Nurul=3) · Ruang 2 (Anwar Anas=1, Ibrahim=2, Mely Astuti=3).
- **Grup**: Grup A (pembimbing Umar), Grup B (pembimbing Imam).
- **Peserta**: Bintang (R1/GrupA/no.1), Citra (R1/GrupA/no.2), Yusuf (R2/GrupB/no.3), Zahra (R2/GrupB/no.4).
- **Sampel nilai**: Bintang & Citra **lengkap** (nilai akhir 85.75 → A Lulus, dan 75.75 → B Lulus); Yusuf & Zahra kosong (untuk dites).

---

## B. Skenario Tes Manual per Fase

### Fase 0 — Perencanaan (periode baru, status draft/setup)

Disarankan tes di **periode baru** (biarkan periode demo tetap berlangsung):

1. **Buat periode** — `admin` → Akademik → Ujian PPI → Periode Ujian → panel kanan "Buat Periode Baru" (pilih TA 2026/2027). → muncul di daftar status **Draft**.
2. **Detail periode** — klik Detail pada periode baru. Lihat kartu Konfigurasi Periode (Skala/Bobot/Aspek/Materi/Ruang/Grup/Peserta), ringkasan bobot 100%, badge "Konfigurasi terbuka".
3. **Salin skala** — tombol "Salin Skala" (hanya saat draft/setup) → skala dari periode demo tersalin; daftar skala terisi A+…D.
4. **Bobot** — ubah total ≠100 (mis. 25/25/25/0) → **ditolak**, ada pesan total aktual. Kembalikan 25/25/25/25 → tersimpan.
5. **Skala** — tambah predikat dengan rentang menimpa skala lain → **ditolak (422)**; urutan duplikat → ditolak.
6. **Aspek & Materi** — tambah induk aspek (kode 8, nama bebas, penguji 3), tambah item, ubah, hapus; tambah/hapus materi setoran.
7. **Ruang & Penguji** — tambah Ruang 3 → isi 3 slot penguji; coba masukkan **Umar** (sudah di Ruang 1 periode ini) → **ditolak/dihapus dari pilihan** dengan pesan bentrok ruang.
8. **Grup** — tambah Grup C + pembimbing (boleh guru yang sama dengan pembimbing grup lain — penugasan ganda diperbolehkan).
9. **Peserta** — assign siswa Kelas VI ke Ruang 3 + Grup C → no urut otomatis **abjad**; siswa ter-assign langsung hilang dari daftar pilih; coba assign ulang siswa yang sama → **ditolak**. Ubah no urut via tombol "Atur".
10. **Transisi** — ubah status ke **Setup** → (opsional lanjut) **Berlangsung**. Saat Berlangsung: badge "Konfigurasi terkunci".

### Kunci otomatis

- Setelah **Berlangsung**, coba edit skala/aspek/bobot/ruang/peserta sebagai `admin` → **403** (layar menampilkan banner "terkunci").
- Super Admin: Detail periode → tombol **"Buka Kunci"** → konfigurasi bisa diedit lagi (muncul badge "Konfigurasi terbuka") → **"Kunci Kembali"**. Cek **Pemeliharaan Sistem → Activity & Audit Log** ada entri buka kunci.
- Keluar dari Berlangsung ke Setup **hanya jika belum ada nilai** (lihat Fase 5/6 cek).

### Fase 1 — Setoran Hafalan (login guru pembimbing)

1. Login `guru.umar` → Akademik → Ujian PPI → **Beranda Guru Ujian** → badge "Penguji Ruang Ruang 1", "Pembimbing Grup Grup A".
2. **Input Setoran** → hanya grup **Grup A** (Bintang & Citra). Isi nilai per surah (0–100) + tanggal, klik Simpan per siswa.
3. Kosongkan satu nilai lalu simpan → nilai itu terhapus.
4. Cek `guru.imam` hanya melihat Grup B; `guru.nurul` **tidak** melihat menu setoran (bukan pembimbing) — halaman setoran → 403.

### Fase 2 — Ujian Lisan (login guru penguji)

1. `guru.umar` → **Input Ujian** → hanya **Ruang 1**, kolom = aspek **Penguji I** saja (Wudhu, Praktik Shalat).
2. Isi nilai Bintang di beberapa aspek (mis. 90) → Simpan → perhitungan otomatis berjalan (cek Rekap).
3. `guru.imam` (Penguji II) & `guru.nurul` (Penguji III) isi bagian mereka untuk **Yusuf & Zahra** sampai **semua 4 komponen lengkap** (3 penguji + hafalan) agar nilai akhir muncul.
4. Coba `guru.umar` membuka URL `Input Ujian` dengan param lain / mengubah nilai aspek Penguji II → ditolak (aspek bukan jatah/peserta bukan ruangnya → 403).

### Fase 3 — Perhitungan Otomatis

Buka **Rekap Kelas VI** (`admin`), pilih periode demo. Cek angka:

| Siswa | Rata P1 | Rata P2 | Rata P3 | Rata Hafalan | Nilai Akhir | Predikat | Lulus |
|---|---|---|---|---|---|---|---|
| Bintang | 80 | 85 | 90 | 88 | 85.75 | A | Lulus |
| Citra | 70 | 75 | 78 | 80 | 75.75 | B | Lulus |
| Yusuf (setelah dites isi 60) | 60 | 60 | 60 | 60 | 60 | C | Lulus |
| Zahra (setelah dites isi 40) | 40 | 40 | 40 | 40 | 40 | D | **Tidak Lulus** |

- Rumus: `nilai_akhir = (rataP1×25% + rataP2×25% + rataP3×25% + rataHafalan×25%)`.
- **Rank Total** 1–4 sesuai nilai akhir; **Rank Lokal** per rombel asal (Bintang & Citra rombel VI-B → rank lokal 1 & 2).
- Filter: ruang/grup/status lulus/rombel/q — tabel & **export PDF & Excel mengikuti filter aktif**.

### Fase 4 — Dokumen (Teks MC & Berita Acara)

1. `admin` atau `guru.umar` → halaman **Teks & Berita Acara** (dari detail periode → tombol "Dokumen" per siswa, atau Beranda Guru → Teks & BA).
2. Cek placeholder terisi: nama siswa, **nama ayah** (=, dari PPDB bila ada), 3 nama penguji, kota, tanggal, `{{NAMA_SISWA}}` dll tidak tersisa.
3. Ganti dropdown "Penguji Penutup" → nama di teks MC berubah (default Penguji III).
4. **Unduh Berita Acara (PDF)** → file PDF A4 berisi kop madrasah + teks BA.

### Fase 5 — Koreksi (admin/wakamad, dengan audit log)

1. Rekap → klik **Koreksi** di baris Yusuf → ubah 1 nilai → simpan **tanpa alasan** → **ditolak** ("alasan wajib").
2. Simpan dengan alasan → sukses; angka rata/nilai akhir berubah otomatis.
3. **Activity & Audit Log** (`Pemeliharaan Sistem → Activity & Audit Log`) → filter — entri `ujian_ppi_koreksi_nilai` menampilkan alasan & daftar perubahan (nilai lama → baru).
4. Login `kepala` → Rekap tampil **tanpa tombol Koreksi**; mencoba POST koreksi langsung → 403. Export PDF/Excel tetap bisa.

### Fase 6 — Arsip (import tahun lama)

1. **Unduh Template Excel** (di halaman Arsip).
2. Isi 2 baris contoh (satu valid, satu sengaja tanpa nama) → **Preview Import** → tabel menampilkan baris valid & error.
3. **Simpan N Baris** → periode "Arsip TP …" status **Diarsipkan** muncul; detail read-only.
4. Cek periode diarsipkan **tidak** bisa diubah status/konfigurasi.

### Otorisasi lain

- Login `kepala` → menu admin (Periode/Skala/…/Peserta) **tidak muncul** di sidebar; akses URL langsung → 403.
- Guru tanpa penugasan (tambah user role `guru`, tanpa tautan Data Guru) → Beranda Guru kosong, Input Ujian → 403 dengan pesan jelas.
- Guru yang dihapus tautan employee-nya → 403 (pesan "tidak terdaftar sebagai penguji").

---

## C. Watchlist & Jebakan

- **Route binding**: nama parameter rute harus persis variabel method (`{periode}` → `PpiExamPeriod $periode`, `{peserta}` → `PpiExamParticipant $peserta`) — jika beda, binding terlewat (model kosong).
- **RoleMiddleware** hanya membaca `users.role` (role primer) — role tambahan via `user_roles` pivot tidak membuka akses route.
- **Kunci konfigurasi**: otomatis saat status `berlangsung` (`config_locked_at`); hanya Super Admin bisa buka kunci (action di-log).
- **1 guru = 1 ruang per periode**: unique `(exam_period_id, employee_id)` di `ppi_exam_examiners` — validasi + sembunyikan dari pilihan.
- **Bobot wajib 100%** sebelum `berlangsung`; komponen berbobot 0 diabaikan (boleh ujian tanpa hafalan).
- **Nilai akhir** = null selama ada komponen berbobot>0 yang belum lengkap → predikat/rank menunggu kelengkapan.
- **NISN** di Rekap best-effort dari PPDB (cocok NIK) — kosong = `—`; **Nama Ayah** dari PPDB `father_name`, fallback guardian pertama.
- **Nama file PDF** tidak boleh mengandung `/` (tahun ajaran 2026/2027 → `2026-2027`).
- Hapus **ruang/grup ber-peserta** & **periode ber-nilai** → diblokir pesan ramah.