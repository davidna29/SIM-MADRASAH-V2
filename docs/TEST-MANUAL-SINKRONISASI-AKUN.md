# TEST MANUAL — MUTASI SISWA KELUAR & SINKRONISASI AKUN

> Panduan tes manual di browser untuk dua paket fitur:
> **1) Modul Mutasi Siswa Keluar** (submenu di bawah Data Siswa) dan
> **2) Sinkronisasi Akun dengan Data Master** (provisioning siswa/pegawai & deaktivasi otomatis).
> Panduan ini mengikuti alur kerja nyata: prasyarat → skenario per fitur → watchlist.
> Semua akun demo memakai kata sandi awal `password`.

---

## A. Persiapan (sekali)

```bash
php artisan migrate:fresh --seed   # reset DB + seluruh seeder
php artisan account:backfill-links # tautkan akun lama (WAJIB sebelum tes C-E)
composer serve                     # buka http://localhost:8000 (jangan php artisan serve polos)
```

> WARNING (perilaku baru): setelan `users.must_change_password` default `true`.
> Setelah `--seed`, SEMUA akun demo akan diarahkan ke halaman `/ubah-password` saat login
> pertama kali (termasuk `admin`). Ini sendiri adalah perilaku yang harus diuji (lihat G).
> Untuk mempercepat sesi tes: login tiap akun yang akan dipakai → ganti kata sandi → catat
> kata sandi baru Anda. Setelah `--seed`, tautan backfill ikut ter-reset, jadi jalankan
> `account:backfill-links` seperti di atas.

Hasil yang diharapkan dari kondisi awal (setelah seed + backfill):

| Data | Nilai (untuk verifikasi cepat) |
|---|---|
| Total user | ~10 (seeder) |
| Pegawai tertaut user | 3 (umar, imam, nurul via seeder) + 2 hasil backfill (bendahara → Ratna Dewi, kepala → Drs. H. Ahmad Fauzi) |
| Belum tertaut (review manual) | pustakawan — dilaporkan di terminal backfill, jangan dipaksa |
| Siswa berakun | 1 (siswa.aisy) |
| Siswa tanpa akun | puluhan — target halaman Akun Menunggu Aktivasi (F) |

Cek hasil backfill: **Pemeliharaan Sistem → Activity & Audit Log** → filter log `account_provisioning`
harus ada entri "Backfill: akun bendahara ditautkan ke pegawai ..." dan "... akun kepala ...".

---

## B. Navigasi & Struktur Menu (submenu Data Siswa)

Lokasi: sidebar **Akademik → Data Siswa** (kini parent, bukan leaf).

1. **Login** `admin` → lihat sidebar kiri: grup Akademik → parent **Data Siswa** punya 3 anak:
   **Data Siswa**, **Mutasi Siswa Masuk**, **Mutasi Siswa Keluar**. Parent auto-terbuka saat ada halaman aktif.
2. **Scoping role**: login sebagai `guru.umar` → hanya submenu **Data Siswa** yang tampil
   (submenu mutasi disembunyikan karena role `guru` tidak punya akses).
3. Klik **Mutasi Siswa Keluar** → `/akademik/mutasi-keluar` → halaman rekap terbuka (state kosong boleh).
4. Akses langsung sebagai non-admin: buka `/akademik/mutasi-keluar` saat login `guru.umar` → **403**.

---

## C. Mutasi Siswa Keluar — CRUD, Picker Cari, Undo

### C.1 Halaman rekap (`/akademik/mutasi-keluar`)
1. Verifikasi 3 kartu KPI (Total Mutasi / Tahun Berjalan / 3 Bulan Terakhir) + tombol **Catat Mutasi Keluar**.
2. Filter: Tahun Ajaran, pencarian nama/NIS → filter bekerja; tombol **Hapus Filter** mengosongkan.

### C.2 Form catat (`/akademik/mutasi-keluar/tambah`) — uji picker pencarian siswa
1. Field **Siswa**: kolom pencarian tampil, **daftar siswa TIDAK muncul sebelum mengetik** (perilaku yang diminta).
2. Ketik nama/nama depan (mis. `Aisyah` atau NIS `240101`) → daftar terfilter muncul.
3. Klik siswa → search menghilang, muncul **"Terpilih: {nama} · {NIS} · {Kelas}"** + tombol ✕.
4. Klik ✕ → pilihan batal, kolom pencarian kembali.
5. Ketik teks tanpa hasil (mis. `zzzz`) → pesan **"Tidak ada hasil untuk ..."**.
6. Isi **Tanggal Mutasi**, **Sekolah Tujuan** (wajib), **Alasan Pindah** (wajib), NSM/NPSN opsional, Nomor Surat opsional, Keterangan opsional.
7. Wajib isi → kosongkan sekolah tujuan → **error validasi** muncul.

### C.3 Simpan → detail → status siswa lepas dari daftar aktif
1. Simpan formulir (pilih siswa yang **punya akun**, mis. `siswa.aisy`) → redirect ke halaman **detail** mutasi
   dengan pesan sukses; seluruh kolom detail tampil; badge "Catatan" memberitahu status penempatan `keluar`.
2. Buka **Akademik → Data Siswa** → `siswa.aisy` **tidak lagi tampil** di daftar aktif.
3. Buka **Akademik → Kelas & Penempatan → I-A** → `siswa.aisy` tidak ada di daftar anggota kelas.
4. Cek **Activity & Audit Log** → filter log `mutasi` → entri "Mutasi keluar: ...".

### C.4 Batal (undo) — kunci fitur deaktivasi (E akan menguji akunnya)
1. Kembali ke detail mutasi → klik **Batalkan** → konfirmasi → mutasi terhapus, pesan sukses.
2. Data Siswa → `siswa.aisy` **kembali tampil** (status penempatan kembali `aktif`).

### C.5 Ubah (edit)
1. Dari detail → **Ubah** → ganti Sekolah Tujuan/Alasan → Simpan → nilai berubah, status enrollment **tidak disentuh** (tetap `keluar`).

---

## D. Provisioning Pegawai Otomatis (EmployeeController::store)

Lokasi: **Kepegawaian → Data Guru & Pegawai → Tambah**.

### D.1 Kasus sukses (pegawai aktif + NIP + tanggal lahir)
1. Isi formulir: nama, NIK (16 digit), TTL **wajib diisi** (mis. `1995-03-12`), NIP (mis. `199503122019031001`),
   jabatan = **Guru Mata Pelajaran**, status = **Aktif** → Simpan.
2. Redirect ke detail → di kartu **Data Kepegawaian** muncul baris **Akun: {NIP}**.
3. Logout → login dengan **username = NIP**, **password = ddmmyyyy TTL** (contoh `12031995`) → harus diarahkan ke `/ubah-password` (G).
4. Ganti password → masuk; rol otomatis = **Guru** (dari jabatan GURU_MAPEL).

### D.2 Kasus nonaktif (tidak dibuatkan akun)
1. Tambah pegawai baru dengan **status = Nonaktif** (NIP tetap diisi) → Simpan.
2. Detail → **tidak ada** baris Akun; total pengguna tidak bertambah (verifikasi di Pengguna & Role).

### D.3 Varian NIP kosong → username dari NIK
1. Tambah pegawai aktif tanpa NIP (NIK wajib) → akun dibuat dengan **username = NIK**.

### Watchlist D
- Kalau tanggal lahir kosong → akun **tidak dibuat**, muncul peringatan kuning "Perhatian" di detail (bukan silent fail).
- Proses **tidak** membuat akun untuk pegawai berstatus nonaktif.

---

## E. Deaktivasi Otomatis (Observer)

### E.1 Pegawai → nonaktif mematikan akun; cuti tidak; balik aktif menghidupkan
Pakai pegawai hasil D.1 (yang sudah punya akun):
1. **Ubah Data** pegawai → status **Nonaktif** → Simpan.
2. Logout → login akun NIP tsb → **ditolak**: "Akun Anda telah dinonaktifkan..." (wajib diuji).
3. Login `admin` → Ubah Data → status **Cuti** → Simpan → login akun NIP tsb → **bisa masuk** (cuti ≠ nonaktif).
4. Ubah Data → status **Aktif** → Simpan → login akun NIP tsb → **bisa masuk** lagi.

### E.2 Siswa → mutasi keluar mematikan akun (regresi)
1. Pastikan `siswa.aisy` punya akun dan ter-enroll aktif.
2. **Akademik → Data Siswa → Mutasi Siswa Keluar → Catat Mutasi Keluar** untuk `siswa.aisy`.
3. Logout → login `siswa.aisy` → **ditolak** (akun nonaktif).
4. Login `admin` → detail mutasi → **Batalkan** → login `siswa.aisy` → **berhasil**, masuk Portal Siswa.

### E.3 Enrollment tahun lalu tidak memicu deaktivasi (guard tahun berjalan)
> Tidak ada UI untuk ini — catatan saja: observer hanya memproses enrollment tahun ajaran **aktif**;
> perubahan status enrollment tahun lalu tidak menyentuh akun (di-cover unit test).

### Watchlist E
- `alumni` (keluarkan dari kelas di **Kelas & Penempatan**) juga mematikan akun siswa — konsisten dengan config.
- Status `keluar`/`alumni` pada **tahun lalu** tidak memicu apa pun.

---

## F. Akun Menunggu Aktivasi (bulk activation siswa)

Lokasi: **Sistem → Pengguna & Role → Akun Menunggu Aktivasi** (`/fondasi/pengguna/aktivasi`)
atau tombol **Akun Menunggu Aktivasi** di header halaman Pengguna & Role.

### F.1 Halaman & filter
1. Dua tab: **Data Lengkap** (bisa dicentang) & **Data Tidak Lengkap** (disable, tidak bisa dicentang).
2. Kolom tabel: checkbox, Nama, NISN/NIS, Kelas, **Username (preview)**.
3. Filter Tahun Ajaran / Kelas + cari nama/NISN/NIS → bekerja; **Hapus Filter** mereset.
4. Tab Tidak Lengkap: baris siswa dengan NISN & NIS kosong → badge "Data Tidak Lengkap", tanpa checkbox.

### F.2 Siapkan data TTL (pra-syarat password default)
> Password default = tanggal lahir (`ddmmyyyy`). Siswa hasil seeder umumnya **belum punya tanggal
> lahir** → baris akan gagal dengan "Tanggal lahir siswa kosong". Ini **by design**, bukan bug.
> Supaya happy-path bisa diuji: **Akademik → Data Siswa → Ubah** salah satu siswa (mis. `Bilal Ramadhan`)
> → isi **Tanggal Lahir** (mis. `2016-08-20`) → Simpan. Siswa tsb lalu siap diaktifkan.

### F.3 Bulk activation
1. Centang **Pilih semua** (header) atau beberapa siswa di tab Lengkap → tombol jadi **"Aktifkan Terpilih (n)"**.
2. Klik Aktifkan → pesan sukses jumlah akun + jika ada yang gagal, daftar **Nama — alasan** di alert merah.
3. Muncul sheet **"Daftar Akun Baru"** menampilkan **Nama / Username / Password Awal / Kelas** (sekali saja).
4. Klik **Unduh CSV** → file `akun-siswa-{timestamp}.csv` terunduh (Nama, Username, Password, Kelas, UTF-8 BOM).
5. **Unduh lagi** → ditolak "Tidak ada daftar akun baru untuk diunduh" (data sudah dibuang).
6. Refresh halaman → sheet kredensial tidak muncul lagi.

### F.4 Verifikasi & idempoten
1. **Pengguna & Role** → akun baru tampil role `siswa`, username = NISN (fallback NIS).
2. Aktifkan **ulang** siswa yang sama → tetap **1 akun** (unique `users.student_id`, `updateOrCreate`).
   Akun siswa yang sudah aktif otomatis hilang dari antrian.
3. Login akun siswa baru (username NISN, password `20160820` dalam contoh) → `/ubah-password` → ganti →
   **Portal Siswa → Data Saya** menampilkan data siswa.

### F.5 Role & keamanan
1. Halaman hanya admin: login `guru.umar` → buka `/fondasi/pengguna/aktivasi` → **403**.
2. Cek **Activity & Audit Log** → filter `account_provisioning` → entri "Akun siswa diaktifkan: {username}".

---

## G. Wajib Ganti Password (EnsurePasswordChanged)

1. Setelah `--seed` (atau akun hasil provisioning), login akun mana pun → semua halaman selain
   `/ubah-password` di-redirect ke **/ubah-password** (halaman ganti password & logout dikecualikan).
2. Isi **Kata Sandi Saat Ini salah** → error "Kata sandi saat ini tidak sesuai.".
3. Password baru < 8 karakter → error validasi `min:8`.
4. Password baru & konfirmasi beda → error `confirmed`.
5. Isi benar → **Simpan** → redirect ke halaman awal sesuai role (admin → Dashboard, guru → Penugasan)
   dengan pesan sukses. Setelah ini, navigasi normal **tanpa** redirect paksa.
6. Logout → login ulang dengan password baru → langsung masuk (tidak diminta ganti lagi).

---

## H. Login Akun Nonaktif (ringkas)

- Semua skenario login "ditolak" di E juga memvalidasi blok login: akun `is_active=false` tidak bisa
  masuk walau kata sandi benar, dengan pesan jelas dan sesi dibersihkan.

---

## I. Watchlist — Hal yang TIDAK Seharusnya Terjadi

- Duplikat akun per siswa: dua user dengan `student_id` sama (unique constraint + updateOrCreate mencegah).
- Username bentrok dibiarkan: kalau NISN bertabrakan, sistem memakai prefix `s-`/`p-` HANYA saat bentrok
  dan mencatat collision ke activity log — cek log, jangan dianggap selesai tanpa review.
- Password bocor ke log: activity log hanya berisi nama/username/label — tidak pernah payload password.
- Mutasi status via mass update: `MutasiKeluarService` memakai update per-instance agar observer
  deaktivasi selalu terpicu (tidak ada jalur mass `where(...)->update()` yang tersisa untuk status keluar).
- Progress batch hilang karena satu baris gagal: bulk activation mengumpulkan kegagalan per-baris.
- Pegawai nonaktif tiba-tiba dapat akun: provisioning hanya untuk status `aktif`.
- Deaktivasi dari tahun ajaran lama: perubahan status enrollment tahun lalu tidak menyentuh akun.

---

## J. Daftar URL Cepat

| Fitur | URL | Role |
|---|---|---|
| Data Siswa (parent submenu) | `/akademik/data-siswa` | 5 role |
| Mutasi Siswa Masuk (admin) | `/akademik/mutasi-masuk/admin` | super_admin · tata_usaha · kepala_madrasah |
| Mutasi Siswa Keluar | `/akademik/mutasi-keluar` | super_admin · tata_usaha · kepala_madrasah |
| Akun Menunggu Aktivasi | `/fondasi/pengguna/aktivasi` | super_admin |
| Ganti Kata Sandi | `/ubah-password` | semua role (auth) |
| Data Guru & Pegawai | `/kepegawaian/data-guru` | super_admin · tata_usaha · kepala_madrasah |
| Activity & Audit Log | `/pemeliharaan/activity-log` | super_admin |
