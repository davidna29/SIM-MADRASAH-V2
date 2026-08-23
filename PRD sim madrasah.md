# Product Requirements Document (PRD)
# Sistem Informasi Manajemen Madrasah (SIM Madrasah)

| | |
|---|---|
| **Nama Produk** | SIM Madrasah — Sistem Informasi Manajemen Madrasah Terintegrasi |
| **Versi Dokumen** | 2.1 |
| **Disusun berdasarkan** | Arsip Tahap 1–10 + sesi tinjauan/revisi strategi pengembangan |
| **Status Proyek** | Dalam pengembangan — Tahap 1–10 selesai, Tahap 11–12 direvisi (belum dimulai) |
| **Target Deployment** | Shared Hosting (Hostinger / Niagahoster Premium) |

---

## 0. Riwayat Perubahan dari Versi 1

Dokumen v1 ditinjau ulang dengan fokus pada efektivitas dan efisiensi proses pengembangan. Perubahan utama pada v2:

| Area | v1 | v2 |
|---|---|---|
| RBAC & Audit Log | Dibangun 100% custom dari nol | Direkomendasikan memakai package matang (`spatie/laravel-permission`, `spatie/laravel-activitylog`) untuk plumbing dasar, effort custom difokuskan ke lapisan penugasan yang memang spesifik madrasah |
| Urutan pembangunan | Layer-by-layer (semua data master → semua akademik → dst) | Walking skeleton dulu (1 alur tipis end-to-end), baru breadth ke modul lain |
| Frontend vs Backend | Tidak eksplisit diatur urutannya | Siklus per-modul: **frontend dengan data seeder → persetujuan → backend logic**, bukan "semua frontend dulu baru semua backend" |
| Gaya mengajar | Mendalam merata untuk semua modul | Mendalam untuk keputusan arsitektural; ringkas & mengikuti pola modul referensi untuk modul repetitif |
| Definition of Done | Belum ada | Ditambahkan sebagai checklist wajib per modul |
| Deployment & staging | Belum dibahas | Ditambahkan rencana lingkungan staging dan prosedur deploy |
| Kepatuhan data pribadi | Belum dibahas | Ditambahkan bagian kepatuhan UU PDP |
| Queue | Dilarang total | Diperbolehkan selama berbasis database + cron (bukan worker permanen) |

**Revisi v2.1:** melengkapi bagian yang belum tercakup pada v2 — strategi pengujian terstruktur (Bagian 25), rencana migrasi data & pelatihan pengguna (Bagian 26), pemeliharaan pasca-rilis (Bagian 27), daftar pertanyaan terbuka yang menunggu keputusan (Bagian 24), glosarium istilah (Bagian 28), serta penajaman kriteria keberhasilan dan kebutuhan non-fungsional agar terukur.

---

## 1. Ringkasan Eksekutif

SIM Madrasah adalah sistem informasi manajemen berbasis web untuk mengelola seluruh aktivitas operasional madrasah secara terintegrasi — akademik, kesiswaan, keuangan, kepegawaian, sarana-prasarana, hingga penjaminan mutu (PKKM dan akreditasi). Sistem ini melayani 16 jenis pengguna, dengan dua prinsip utama: **histori data tidak pernah hilang** dan **hak akses diatur granular melalui role & permission**.

Dibangun dengan **Laravel 12 + PHP 8.4 + Blade + Tailwind CSS + MySQL**, dirancang agar berjalan penuh di **shared hosting**. Arsitektur **modular monolith**: satu aplikasi Laravel, dipisah berdasarkan domain tanggung jawab.

Pada v2, proses pengembangan diperjelas menjadi: **(1)** validasi arsitektur lewat walking skeleton, **(2)** siklus per-modul "frontend disetujui dulu, baru backend", dan **(3)** memanfaatkan package teruji untuk masalah yang sudah solved (RBAC, audit log) agar waktu belajar & membangun difokuskan pada hal yang benar-benar spesifik kebutuhan madrasah.

---

## 2. Latar Belakang & Masalah

Data operasional madrasah saat ini tersebar di Excel, Word, buku administrasi manual, WhatsApp, komputer pribadi guru/TU, arsip cetak, dan Google Drive pribadi — menyebabkan data tidak konsisten, riwayat siswa sulit ditelusuri lintas tahun ajaran, administrasi tidak terdokumentasi rapi, pimpinan kesulitan mengambil keputusan berbasis data, pemetaan eviden PKKM/akreditasi manual dan berulang, serta tidak ada jejak audit atas perubahan data penting.

SIM Madrasah dibangun untuk menyatukan data dan proses tersebut dalam satu sistem, tanpa mengorbankan kompatibilitas dengan infrastruktur hosting terjangkau (shared hosting).

---

## 3. Tujuan Produk

| Tujuan | Penjelasan |
|---|---|
| **Integrasi data** | Menyatukan data yang tersebar ke satu sumber data. |
| **Menjaga histori** | Perjalanan siswa dari masuk sampai alumni tidak pernah ditimpa atau hilang. |
| **Mempermudah pekerjaan** | Setiap peran hanya melihat menu dan data sesuai tugasnya. |
| **Mengurangi input berulang** | Data master dipakai lintas modul tanpa entri ulang. |
| **Mendukung pengambilan keputusan** | Dashboard ringkas untuk pimpinan (kehadiran, nilai, keuangan, kelengkapan PKKM/akreditasi). |
| **Meningkatkan akuntabilitas** | Activity log dan audit log mencatat siapa mengubah apa, kapan, dan alasannya. |

---

## 4. Ruang Lingkup

### 4.1 Dalam Lingkup — Versi Pertama

- Satu madrasah (belum multi-tenant/SaaS).
- Web responsif, tanpa aplikasi mobile native.
- Modul internal: manajemen pengguna, RBAC, data master, akademik, kesiswaan, keuangan siswa, tata usaha, kepegawaian, inventaris, perpustakaan, laboratorium, PKKM, akreditasi, audit, backup.
- Modul eksternal: website publik, portal berita (CMS), PPDB daring, portal orang tua, portal siswa, portofolio digital siswa dengan QR Code.
- Autentikasi akun lokal (Laravel Breeze), belum SSO/OAuth pihak ketiga.

### 4.2 Di Luar Lingkup — Versi Pertama

Aplikasi mobile native, WebSocket/realtime, Redis, Docker produksi, payment gateway otomatis, absensi biometrik, notifikasi WhatsApp otomatis, model multi-madrasah/SaaS.

> Queue **berbasis database + cron** diperbolehkan (lihat Bagian 6) — ini bukan pengecualian dari larangan "worker permanen", karena tidak butuh proses yang berjalan terus-menerus.

---

## 5. Target Pengguna & Peran

| No | Role | Fokus Kebutuhan Utama |
|---|---|---|
| 1 | Super Admin | Kelola pengguna, role/permission, konfigurasi sistem, backup/restore, log aktivitas |
| 2 | Kepala Madrasah | Dashboard pimpinan, laporan akademik/keuangan, persetujuan, monitoring PKKM/akreditasi |
| 3 | Wakamad Kurikulum | Kurikulum, jadwal, ATP, modul ajar, jurnal mengajar, nilai, rapor |
| 4 | Wakamad Kesiswaan | Data siswa, prestasi, pelanggaran, ekstrakurikuler, tahfidz, pembiasaan |
| 5 | Wakamad Sarpras | Barang, ruangan, laboratorium, peminjaman, pemeliharaan |
| 6 | Wakamad Humas | Berita, agenda, kerja sama, alumni, publikasi |
| 7 | Tata Usaha | Administrasi siswa/pegawai, surat masuk/keluar, SK, MOU, arsip |
| 8 | Bendahara | Tagihan, pembayaran, cicilan, kwitansi, rekap keuangan |
| 9 | Wali Kelas | Monitoring satu kelas: absensi, nilai, prestasi, pelanggaran, pembayaran, rapor |
| 10 | Guru Mata Pelajaran | Jadwal mengajar, jurnal, absensi pembelajaran, input nilai (sesuai penugasan) |
| 11 | Guru BK | Konseling, tindak lanjut, kerahasiaan catatan |
| 12 | Petugas Perpustakaan | Buku, anggota, peminjaman/pengembalian, ebook |
| 13 | Petugas Laboratorium | Alat, bahan, peminjaman, kerusakan, pemeliharaan |
| 14 | Editor Berita | Draft/edit berita, kategori/tag, SEO, penjadwalan publikasi |
| 15 | Orang Tua | Data anak: kehadiran, nilai, prestasi, pelanggaran, tagihan, rapor |
| 16 | Siswa | Profil, jadwal, nilai terpublikasi, absensi, portofolio |

**Role, jabatan, dan penugasan** adalah tiga konsep berbeda: **Role** = kelompok hak akses sistem; **Jabatan** = posisi struktural organisasi; **Penugasan** = tanggung jawab pada periode tertentu (mis. wali kelas VII-A tahun ajaran 2026/2027). Satu pengguna dapat memiliki lebih dari satu role dan penugasan sekaligus.

---

## 6. Arsitektur & Teknologi

| Aspek | Pilihan | Alasan |
|---|---|---|
| Arsitektur | Modular monolith | Mudah dipelajari pemula, deployment sederhana di shared hosting, satu autentikasi & satu database |
| Backend | Laravel 12, PHP 8.4 | Framework matang, ekosistem luas, kompatibel shared hosting |
| Frontend | Blade + Tailwind CSS, dibangun sebagai **design system** (komponen reusable) sejak awal | Ringan, konsisten lintas 68 modul, mempercepat pembuatan tampilan modul berikutnya |
| Database | MySQL 8.4 LTS / MariaDB | Didukung penuh shared hosting |
| Autentikasi | Laravel Breeze | Sederhana, sudah dikustomisasi (login username/email) |
| **RBAC & Permission** | **`spatie/laravel-permission`** sebagai fondasi (roles, permissions, cache), lapisan **penugasan** (wali kelas, guru mapel per kelas) tetap dibangun custom karena spesifik domain madrasah | Package ini mendukung penuh Laravel 12 & PHP 8.1–8.4, murni database (tidak butuh Redis), MIT license, teruji luas. Menghindari membangun ulang solved-problem; effort belajar dialihkan ke bagian yang memang unik (penugasan berbasis periode) |
| **Audit & Activity Log** | `spatie/laravel-activitylog` (atau setara) untuk pencatatan dasar, disesuaikan untuk mencatat nilai lama/baru pada data prioritas | Mengurangi kode boilerplate log manual di setiap modul |
| Storage | Local storage | Tidak bergantung cloud storage berbayar |
| PDF | DomPDF | Cetak rapor, kwitansi, laporan |
| Import/Export | Laravel Excel | Import massal dengan validasi & preview |
| QR Code | Simple QRCode | QR Code portofolio digital siswa |
| **Queue (proses berat)** | Driver **database**, dipicu **cron** shared hosting (`schedule:run` per menit → `queue:work --stop-when-empty`) | Bukan worker permanen, tetap kompatibel shared hosting; dipakai untuk generate rapor massal, import besar, notifikasi — tanpa Redis |
| **Eksplisit dihindari** | PostgreSQL, Redis, Docker (produksi), WebSocket, worker queue permanen | Tidak didukung/tidak diperlukan di shared hosting |

**Struktur folder** tetap mengikuti konvensi standar Laravel (bukan folder `Modules/` terpisah) — pemisahan tanggung jawab dilakukan lewat folder domain di `app/Http/Controllers/<Domain>`, Form Request per aksi, Service Class untuk logika kompleks, dan Policy untuk otorisasi per data.

---

## 7. Prinsip Desain Data

1. **Data master vs transaksi vs riwayat** — transaksi (nilai, absensi, SPP, prestasi, pelanggaran, tahfidz, rapor, konseling, inventaris) wajib direferensikan ke Tahun Ajaran dan Semester.
2. **Tidak pernah menimpa histori** — naik kelas membuat baris baru di `student_enrollments`, bukan update baris lama. Lulus → status *Alumni*, riwayat tetap utuh.
3. **Identitas vs akun dipisah** — `people` (biodata inti), `users` (akun login), `employees`/`students`/`guardians` menghubungkan identitas ke peran spesifik.
4. **Role/jabatan/penugasan dipisah secara struktural** dalam skema database.
5. **Portofolio digital tidak menduplikasi data** — menggabungkan tampilan dari sumber asli.
6. **Snapshot untuk dokumen resmi** — rapor terbit disimpan sebagai versi agar bisa dicetak ulang persis walau data sumber berubah kemudian.

---

## 8. Modul Sistem

68 modul/submodul dalam 8 kelompok besar — tidak dibangun sebagai 68 aplikasi terpisah, sebagian disatukan dalam satu menu.

| Kelompok | Cakupan |
|---|---|
| A — Fondasi & Pengaturan Sistem | Authentication, Manajemen Pengguna, Role & Permission, Struktur Organisasi, Pengaturan Sistem, Notifikasi Internal |
| B — Website Publik, Berita & PPDB | Website Publik, Portal Berita/CMS, Agenda & Pengumuman, Galeri & Media, PPDB |
| C — Data Inti & Akademik | Tahun Ajaran/Semester, Data Guru, Data Siswa, Data Orang Tua, Mapel, Kelas, Riwayat Kelas, Kurikulum, Kalender Pendidikan, Penugasan Mengajar, Jadwal, ATP, Jurnal Mengajar, Penilaian, Rapor |
| D — Kesiswaan & Portofolio | Portofolio Digital, QR Code Siswa, Kehadiran Siswa, Prestasi, Pelanggaran, BK/Konseling, Ekstrakurikuler, Tahfidz, Pembiasaan, Organisasi Siswa |
| E — Keuangan, TU & Kepegawaian | Tagihan & Pembayaran, Rekap Keuangan, Surat Masuk/Keluar, Arsip Digital, SK, MOU, Kehadiran Pegawai, Kepegawaian, PKB/Workshop/Sertifikat |
| F — Sarpras, Inventaris & Perpustakaan | Ruangan, Inventaris Barang, Mutasi & Pemeliharaan, Laboratorium, Perpustakaan, Ebook |
| G — PKKM, Akreditasi & Mutu | PKKM Center, Akreditasi (8 SNP), Monitoring & Evaluasi, Rencana Kerja Madrasah |
| H — Portal, Pelaporan & Pemeliharaan | Dashboard, Portal Orang Tua, Portal Siswa, Portal Alumni/Humas, Pusat Dokumen, Pusat Laporan, Import/Export, Activity Log, Audit Log, Backup & Restore, Manajemen File, Sistem Bantuan |

### 8.1 Modul MVP (target versi pertama yang dapat dipakai)

Authentication • Pengguna • Role & Permission • Pengaturan Madrasah • Tahun Ajaran • Semester • Data Guru • Data Siswa • Data Orang Tua • Mata Pelajaran • Kelas • Riwayat Kelas • Penugasan Mengajar • Jadwal • Kehadiran Siswa • Jurnal Mengajar • Nilai • Rapor • Tagihan • Pembayaran • Dashboard • Portal Orang Tua • Portal Siswa • Activity Log • Backup

### 8.2 Modul yang Tidak Boleh Menduplikasi Data

Portofolio Digital, Dashboard, Portal Orang Tua, Portal Siswa, Pusat Laporan, Pusat Dokumen bersifat **read/aggregate-only**.

---

## 9. Model Data & Relasi Inti (ERD)

**Alur akademik inti:**
```
Tahun Ajaran & Semester → Kelas → Penempatan Siswa (student_enrollments)
     → Penugasan Guru → Jadwal → Jurnal Mengajar → Nilai → Rapor
```

**Alur pembayaran:**
```
Siswa → Riwayat Kelas → Tagihan → Pembayaran → Kwitansi → Laporan → Portal Orang Tua
```

**Alur portofolio:**
```
Siswa → Absensi → Nilai → Prestasi → Pelanggaran → Tahfidz → Pembayaran → Portofolio Digital
```

Entitas fondasi kunci (Tahap 5–6): `madrasahs`, `settings`, `people`, `users`, `employees`, `students`, `guardians`, `student_guardians`, `organizational_units`, `positions`, `employee_position_histories`, `academic_years`, `semesters`, `grade_levels`, `rooms`, `class_groups`, `student_enrollments`, `student_status_histories`, `homeroom_assignments`. Tabel RBAC (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) mengikuti skema bawaan `spatie/laravel-permission`.

**Kenapa `student_enrollments` kunci:** mencatat satu baris per kombinasi siswa–tahun ajaran–semester–kelas, sehingga riwayat penempatan kelas tidak pernah hilang.

---

## 10. Standar Desain Database

| Standar | Ketentuan |
|---|---|
| Engine | InnoDB |
| Character set | `utf8mb4` / `utf8mb4_unicode_ci` |
| Penamaan tabel | Bahasa Inggris, snake_case, plural |
| Timestamp | `created_at`, `updated_at` di setiap tabel |
| Soft delete | Selektif, untuk data yang boleh "dihapus" tanpa kehilangan jejak |
| Tipe data uang | Bukan `FLOAT` — desimal/integer satuan terkecil |
| Status | `VARCHAR`/enum terdefinisi |
| Foreign key action | Kombinasi `RESTRICT`/`CASCADE`/`SET NULL` sesuai konteks; sebagian aturan ditegakkan di Service/Policy, bukan hanya foreign key |

---

## 11. Autentikasi & RBAC

- **Autentikasi**: Laravel Breeze, login username/email, penguncian akun 15 menit setelah 5 kali gagal, pencatatan waktu & IP login terakhir.
- **RBAC**: fondasi `roles`/`permissions` memakai `spatie/laravel-permission` (permission granular seperti `siswa.lihat`, `nilai.input`, `nilai.publish`, `pembayaran.koreksi`, `berita.approve`, diregistrasi ke Gate Laravel secara otomatis oleh package).
- **Lapisan penugasan** (custom, spesifik madrasah) tetap dibangun manual: guru hanya bisa mengubah nilai kelas/mapel yang menjadi penugasannya — ini **tidak** bisa digantikan package generik, karena aturannya berbasis relasi data (kelas + mapel + tahun ajaran), bukan sekadar role.
- Penegakan akses berlapis: Middleware (permission per route) → Gate/Policy (per aksi & data) → pembatasan penugasan (query scope).
- Perubahan role/permission tercatat di audit log. Super Admin tidak boleh menghapus dirinya sendiri jika satu-satunya Super Admin aktif.

---

## 12. Aturan Bisnis Utama

1. Tahun ajaran & semester wajib menjadi acuan seluruh data transaksi akademik.
2. Riwayat kelas dicatat sebagai baris baru, data lama tidak diubah.
3. Status siswa selalu disertai tanggal, alasan, pelaksana, catatan pendukung.
4. Koreksi transaksi tidak menghapus data asli — dicatat sebagai koreksi dengan alasan, pelaksana, dan jejak audit.
5. Status pembayaran **dihitung** dari akumulasi transaksi, bukan diisi manual.
6. Publikasi nilai/rapor berjenjang (draft → lengkap → diverifikasi → dipublikasikan).
7. Kerahasiaan konseling berlapis sesuai peran yang mengakses.
8. Eviden PKKM & akreditasi terhubung ke standar, komponen, indikator, tahun, penanggung jawab, status verifikasi.
9. Workflow berita: Guru/Kontributor → Editor → (opsional) Kepala Madrasah → Publish, 8 status baku.

---

## 13. Kebutuhan Non-Fungsional

| Kategori | Kebutuhan Kunci |
|---|---|
| Usability | Bahasa sederhana, menu sesuai peran, pesan error jelas, nyaman di ponsel, pencarian & filter |
| Performa | Pagination wajib, index database, query relasi dioptimalkan, dashboard dari data ringkas |
| Keamanan | Auth + Authorization berlapis, validasi server-side, CSRF, pembatasan file, password hashing, pencatatan aktivitas |
| Keandalan | Database transaction untuk proses kritikal, backup rutin, konfirmasi sebelum aksi berisiko |
| Maintainability | Penamaan konsisten, logika bisnis terpisah dari controller, komponen Blade reusable, migration & seeder terdokumentasi |
| **Observability (baru)** | Log channel Laravel dikonfigurasi rapi; notifikasi email ke Super Admin saat terjadi exception fatal di production; jadwal tinjau log mingguan |
| Kompatibilitas Hosting | PHP + MySQL/MariaDB murni, tanpa proses server permanen, file di local storage, cron job untuk queue & scheduler |
| **Target Performa Terukur (baru)** | Halaman list dengan pagination termuat ≤ 3 detik (p95) pada koneksi normal; aksi CRUD biasa ≤ 2 detik; proses berat (generate rapor massal, import besar) didelegasikan ke queue database+cron dengan indikator status proses; nyaman untuk ±50 pengguna aktif serentak (skala satu madrasah) |
| **Kebijakan Akun & Sesi (baru)** | Password minimal 8 karakter (hashing bcrypt bawaan Laravel); sesi idle kedaluwarsa setelah 120 menit (dapat diatur per madrasah); reset password via email dengan token berbatas waktu; pembatasan percobaan login sudah diatur (5× gagal → kunci 15 menit) |

---

## 14. Standar UI/UX & Design System

- Identitas visual: hijau (utama), netral (latar), merah (error/berbahaya), kuning (peringatan/cicilan), hijau (berhasil/lunas), biru (informasi).
- Prinsip: minimalis, menu berbasis role, tabel dengan filter, form terbagi per bagian kecil, breadcrumb, konfirmasi tindakan, status ditampilkan visual, tombol utama mudah ditemukan.
- Acuan pengguna: guru/pegawai yang belum terbiasa aplikasi kompleks — hindari ikon tanpa teks, istilah teknis, form panjang, navigasi bertingkat dalam.
- **Design system dibangun sebagai fondasi, bukan per-modul**: komponen Blade reusable (tombol, tabel+pagination, badge status, form section, breadcrumb, sidebar navigasi per role, alert/toast, modal konfirmasi) dibuat sekali di awal Tahap 11, lalu dipakai ulang di seluruh modul. Ini mempercepat pembuatan tampilan modul berikutnya dan menjaga konsistensi visual tanpa perlu didesain ulang tiap kali.

---

## 15. Keamanan, Audit & Kerahasiaan Data

- **Activity log** — aktivitas pengguna (login, membuat berita, mengunggah dokumen, mencetak laporan).
- **Audit log** — perubahan data penting (nilai, pembayaran, absensi, rapor, status siswa, role/permission, berita, eviden PKKM/akreditasi, inventaris): nilai lama, nilai baru, pengguna, waktu, alasan, IP.
- **QR Code portofolio** tidak boleh langsung membuka data publik — harus melalui pemeriksaan izin akses, status login, cakupan data, validitas token.
- Data sensitif dilindungi kombinasi role, permission, policy, pembatasan relasi data, audit log, URL dokumen tidak mudah ditebak.

---

## 16. Metodologi Pengembangan: Frontend-First per Modul

Untuk menyeimbangkan keinginan memvalidasi tampilan lebih dulu dengan risiko ruang lingkup yang membesar, pengembangan **tidak** dilakukan sebagai dua fase besar ("semua frontend" lalu "semua backend"), melainkan sebagai **siklus kecil berulang per modul**:

**Langkah 0 — sekali di awal (Tahap 11):**
Bangun design system dan shell navigasi (Bagian 14) sebelum modul mana pun dikerjakan.

**Langkah per modul (diulang untuk tiap modul dalam cakupan MVP, Bagian 8.1):**

1. **Data skeleton** — buat migration + seeder/factory minimal untuk tabel yang relevan (struktur sudah final dari Tahap 5–6). Ini cepat karena hanya struktur, belum logika.
2. **Tampilan (frontend)** — bangun halaman Blade (list, detail, form) menggunakan **data hasil seeder sungguhan** lewat Eloquent — bukan array statis — supaya tidak ada kerja ganda saat nanti disambungkan ke logika sungguhan.
3. **Persetujuan ("sep")** — tinjau tampilan terhadap standar UI/UX (Bagian 14) dan kelengkapan field terhadap ERD. Ini titik paling murah untuk mengubah keputusan, sebelum logika bisnis ditulis.
4. **Backend (logika)** — baru implementasikan validasi (Form Request), aturan bisnis, Policy, dan pencatatan audit log untuk modul tersebut.
5. **Lanjut ke modul berikutnya**, memakai pola yang sama.

**Mengapa per-modul, bukan per-fase besar:**
- Mencegah tampilan menjadi "gunung" 200+ halaman statis yang belum tentu sesuai kebutuhan data sebenarnya.
- Data seeder yang terstruktur sesuai ERD membuat transisi ke backend hampir mekanis, bukan menulis ulang.
- Tetap memberi rasa progres visual yang cepat terlihat (tujuan awal permintaan "frontend dulu") tanpa menunda validasi arsitektur terlalu lama.

**Walking skeleton terlebih dahulu:** sebelum menggarap seluruh modul MVP, jalankan siklus di atas untuk **satu alur tipis end-to-end** lebih dulu — 1 kelas, 1 mata pelajaran, 1 siswa: login guru → input nilai → rapor PDF terbit → orang tua login dan melihat rapor. Ini memvalidasi design system, RBAC, DomPDF, dan portal sekaligus dalam skala kecil sebelum diperlebar ke seluruh modul MVP.

**Modul referensi vs modul serupa:** modul CRUD data master yang polanya mirip (Guru, Mapel, Kelas, Ruangan, dst.) cukup dijelaskan mendalam sekali sebagai modul referensi; modul berikutnya yang sepola dikerjakan lebih cepat dengan penjelasan ringkas ("sama seperti modul Guru, bedanya kolom X dan validasi Y").

---

## 17. Definition of Done per Modul

Sebuah modul dianggap selesai jika seluruh berikut terpenuhi:

- [ ] Migration & seeder/factory tersedia dan sesuai ERD.
- [ ] Tampilan (list, form, detail) sudah "sep" sesuai standar UI/UX & design system.
- [ ] Hanya memakai komponen design system yang sudah ada; styling ad-hoc di luar komponen bersama tidak diperbolehkan.
- [ ] Form Request untuk validasi input.
- [ ] Policy untuk otorisasi per data (bukan hanya middleware permission).
- [ ] Aturan bisnis modul (jika ada) terimplementasi (mis. perhitungan status pembayaran, riwayat kelas).
- [ ] Transaksi penting tercatat di audit log.
- [ ] Modul yang menangani data pribadi sensitif memenuhi checklist Bagian 22 (consent, pembatasan akses, retensi).
- [ ] Minimal satu automated test (feature test) untuk jalur utama dan satu untuk batas akses (mis. role lain tidak bisa mengakses).
- [ ] Sudah diuji manual pada seeded data yang realistis.
- [ ] Dicommit ke Git dengan pesan yang jelas per checkpoint.

---

## 18. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Ruang lingkup terlalu besar → mandek | Walking skeleton dulu, siklus per-modul, Definition of Done, prioritas MVP |
| **Frontend menjadi "gunung" sendiri (baru)** | Siklus per-modul (bukan mock-up semua modul sekaligus); data skeleton pakai seeder sungguhan, bukan array statis |
| Hak akses terlalu kompleks | Fondasi RBAC pakai package teruji; lapisan penugasan diuji lewat feature test |
| Data tidak konsisten | Validation, unique constraint, foreign key, dropdown data master, template import + preview |
| Penyimpanan file membengkak | Batas ukuran/jenis file, kompresi gambar, video besar via tautan |
| Keterbatasan shared hosting | Hindari proses permanen, optimasi query, pagination, queue database+cron hanya bila perlu |
| Kerahasiaan data siswa | Role/permission/policy berlapis, audit log, URL dokumen tidak mudah ditebak |
| **Kepatuhan data pribadi (baru)** | Lihat Bagian 22 |

---

## 19. Kriteria Keberhasilan

1. Setiap pengguna login dengan akun masing-masing dan hanya melihat fitur sesuai kewenangannya.
2. Data siswa dapat ditelusuri utuh dari masuk sampai lulus tanpa kehilangan riwayat kelas.
3. Nilai dan absensi dapat difilter berdasarkan tahun ajaran & semester.
4. Pembayaran dapat dilacak per siswa maupun per kelas.
5. Kepala madrasah memperoleh ringkasan kondisi madrasah dari dashboard.
6. Orang tua hanya melihat data anak yang terhubung; guru hanya mengubah data sesuai penugasannya.
7. Eviden PKKM dan akreditasi terpetakan ke indikator.
8. Alur persetujuan berita berjalan sesuai status.
9. Sistem menghasilkan laporan dan mencatat audit trail.
10. Sistem berjalan mulus di shared hosting, dapat diakses dari komputer maupun ponsel.
11. Backup dapat dibuat dan dipulihkan dengan prosedur jelas.
12. Migrasi awal dari Excel selesai tanpa kehilangan data — jumlah record sumber sama dengan hasil import, diverifikasi lewat layar preview import.
13. Pengguna baru per peran dapat beroperasi mandiri setelah ≤ 1 sesi pelatihan singkat, berkat menu per-peran dan pusat bantuan dalam sistem.
14. Restore backup teruji minimal satu kali (drill di staging) dengan hasil database identik sebelum go-live.

---

## 20. Roadmap Pengembangan

| Tahap | Nama | Status |
|---|---|---|
| 1 | Analisis Kebutuhan | ✅ Selesai |
| 2 | Daftar Seluruh Modul | ✅ Selesai |
| 3 | Use Case Diagram | ✅ Selesai |
| 4 | Flowchart | ✅ Selesai |
| 5 | Entity Relationship Diagram (ERD) | ✅ Selesai |
| 6 | Desain Database | ✅ Selesai |
| 7 | Struktur Folder Laravel | ✅ Selesai |
| 8 | Instalasi Database & Laravel | ✅ Selesai |
| 9 | Authentication | ✅ Selesai |
| 10 | Role & Permission (fondasi RBAC) | ✅ Selesai — akan disesuaikan ke `spatie/laravel-permission` sebelum lanjut |
| **11** | **Design System & Navigasi Dasar** *(direvisi dari "Dashboard")* | ⏳ Belum dimulai |
| **12** | **Walking Skeleton** *(baru)* — satu alur tipis end-to-end (nilai → rapor → portal ortu) | ⏳ Belum dimulai |
| **13** | **Coding Modul MVP** *(direvisi dari "Coding Modul satu demi satu")* — siklus frontend→persetujuan→backend per modul, mengikuti Bagian 16 | ⏳ Belum dimulai |
| 14+ | Modul non-MVP (Prioritas 3–7 sesuai Tahap 2) | ⏳ Belum dimulai |

Persetujuan eksplisit ("LANJUT") tetap diperlukan sebelum berpindah tahap, sesuai aturan awal proyek.

Estimasi ukuran effort (relatif, bukan tanggal): Tahap 11 = M, Tahap 12 = S, Tahap 13 = L — dipecah per modul dengan ukuran S/M/L saat perencanaan modul dimulai, agar progres tetap terukur tanpa janji tenggat yang tidak realistis.

---

## 21. Rencana Deployment & Staging (Baru)

- **Lingkungan staging**: subdomain terpisah di hosting yang sama (mis. `staging.domain-madrasah.sch.id`) dengan database terpisah dari production, digunakan untuk menguji migration dan fitur baru sebelum menyentuh data sungguhan.
- **Prosedur deploy** (manual, sesuai realitas shared hosting): aktifkan maintenance mode → `git pull` → `composer install --no-dev` → `php artisan migrate --force` → `npm run build` → nonaktifkan maintenance mode.
- **Rollback**: setiap migration destruktif (mengubah/menghapus kolom berisi data) wajib punya rencana mundur (`down()` yang benar, atau backup sebelum migrate) sebelum dijalankan di production.
- **Backup sebelum deploy besar**: backup database manual sebelum migration yang mengubah struktur tabel yang sudah berisi data produksi.
- **Jadwal backup rutin (baru)**: backup database otomatis harian via cron (disimpan 7 versi terakhir di server) + backup mingguan yang diunduh dan disimpan di luar server; arsip bulanan disimpan sampai akhir tahun ajaran terkait.
- **Restore drill (baru)**: uji pulihkan backup di staging minimal satu kali sebelum go-live, kemudian berulang setiap awal semester.

---

## 22. Kepatuhan Data Pribadi (UU PDP) (Baru)

Sistem menyimpan data pribadi sensitif (NIK, catatan konseling, data anak di bawah umur pada modul Siswa dan PPDB), sehingga perlu:

- **Kebijakan retensi data**: berapa lama data alumni/pendaftar PPDB yang tidak diterima disimpan sebelum dianonimkan/dihapus.
- **Persetujuan (consent)** eksplisit saat PPDB mengumpulkan data pribadi calon siswa dan orang tua, dicantumkan pada formulir pendaftaran daring.
- **Pembatasan akses data sensitif** secara eksplisit di level Policy (bukan hanya role) — mis. catatan konseling tidak otomatis terlihat oleh semua Guru BK.
- **Pemberitahuan** kepada pengguna internal tentang data apa yang dicatat sistem (activity log) sebagai bagian dari transparansi.

Ketentuan ini menjadi bagian dari Definition of Done untuk modul yang menangani data pribadi sensitif (PPDB, Data Siswa, Konseling).

---

## 23. Asumsi Proyek

- Digunakan oleh satu madrasah untuk versi pertama; satu pengguna bisa memiliki lebih dari satu role.
- Satu guru dapat memiliki beberapa penugasan; satu orang tua bisa memiliki beberapa anak; satu siswa bisa memiliki beberapa wali.
- Tahun ajaran memiliki semester; madrasah menentukan semester aktif.
- Nominal keuangan dalam Rupiah; bahasa utama Bahasa Indonesia; zona waktu sesuai pengaturan madrasah.
- Backup tetap diunduh dan disimpan di luar server hosting secara berkala.
- Format rapor & struktur indikator akreditasi difinalisasi saat modul terkait mulai dikembangkan.
- **(Baru)** Penggunaan `spatie/laravel-permission` dan `spatie/laravel-activitylog` disetujui sebagai fondasi RBAC/audit; keputusan ini dapat ditinjau ulang bila ditemukan keterbatasan spesifik di kemudian hari.

---

## 24. Pertanyaan Terbuka (Baru)

Keputusan berikut belum final dan wajib ditutup sebelum tahap terkait dimulai:

| ID | Pertanyaan | Dampak Jika Terlambat Diputuskan | Tenggat Keputusan |
|---|---|---|---|
| Q1 | Berapa lama retensi data alumni & pendaftar PPDB yang tidak diterima sebelum dianonimkan/dihapus? | Kebijakan UU PDP (Bagian 22) tidak bisa dieksekusi di modul PPDB & Siswa | Sebelum modul PPDB masuk pengembangan |
| Q2 | Format rapor mengikuti template Kemenag/EMIS atau custom madrasah? | Walking skeleton (Tahap 12) memakai rapor sebagai alur validasi — format harus diketahui lebih awal | Sebelum Tahap 12 dimulai |
| Q3 | Apakah ekspor data ke EMIS/Dapodik masuk lingkup v1 atau ditunda? | Berdampak pada struktur data induk (field wajib) bila tiba-tiba ditambahkan | Sebelum Tahap 13 modul Data Siswa/Guru |
| Q4 | Kebijakan publikasi foto & data siswa di website publik — apa bentuk persetujuan orang tuanya? | Modul Website Publik & Berita berisiko melanggar perlindungan data anak | Sebelum modul Kelompok B dikembangkan |
| Q5 | Berapa kuota disk hosting dan kebijakan arsip file lama (kwitansi, dokumen, foto)? | Penyimpanan membengkak tanpa batas eksplisit; perlu parameter konkret untuk aturan retensi file | Sebelum go-live MVP |

## 25. Strategi Pengujian (Baru)

- **Feature test (wajib, sesuai DoD Bagian 17)** — setiap modul punya minimal: satu test jalur utama (happy path) dan satu test batas akses (peran lain tidak bisa mengakses/beraksi).
- **Unit test** — untuk kalkulasi murni yang rawan salah: perhitungan status pembayaran, rekap nilai → rapor, akumulasi absensi.
- **Smoke test end-to-end** — alur walking skeleton (login guru → input nilai → rapor PDF → portal orang tua) dijalankan manual ulang setiap kali sebelum deploy besar.
- **UAT per modul** — modul MVP diuji penerimaan oleh pengguna sebenarnya (wali kelas, TU, bendahara) di staging dengan data seeder realistis; temuan dicatat sebagai daftar prioritas sebelum modul dinyatakan selesai.
- **Regression manual** — checklist alur inti dijalankan sebelum rilis; otomatisasi regression penuh bukan target v1.

## 26. Migrasi Data, Pelatihan & Adopsi (Baru)

- **Migrasi dari sistem lama**: template import Laravel Excel untuk Guru, Siswa, Orang Tua — kolom wajib divalidasi dengan layar preview per baris (baris error ditandai, tidak menggagalkan seluruh import). Migrasi dilakukan bertahap: data master dulu, baru transaksi berjalan.
- **Cut-over**: mulai di awal tahun ajaran; periode paralel 2–4 minggu di mana Excel masih boleh dipakai untuk hal darurat, namun sistem menjadi sumber data resmi sejak hari pertama.
- **Pelatihan**: materi ringkas per peran (cheatsheet 1–2 halaman), sesi langsung untuk TU/bendahara/wakamad (pengguna tersibuk), dan modul Sistem Bantuan sebagai rujukan mandiri di dalam aplikasi.
- **Evaluasi adopsi**: satu bulan pasca-go-live — activity log dipakai sebagai indikator pemakaian nyata per peran; kendala dikumpulkan menjadi backlog perbaikan.

## 27. Pemeliharaan & Dukungan Pasca-Rilis (Baru)

- **Kanal pelaporan**: form internal sederhana untuk lapor masalah, dicatat dengan prioritas. Ketentuan informal: gangguan login/kehilangan data ditangani hari yang sama; permintaan fitur baru masuk backlog roadmap, bukan dikerjakan langsung.
- **Rutinitas**: tinjau log mingguan (Bagian 13), update dependensi minor bulanan lewat staging dahulu, evaluasi upgrade mayor Laravel satu kali per tahun ajaran.
- **Penanggung jawab**: minimal satu orang ditunjuk sebagai admin teknis (terpisah atau bersamaan dengan Super Admin fungsional), dengan akses ke panel hosting.
- **Dokumentasi operasional**: langkah deploy, prosedur restore, dan kontak dukungan hosting disimpan di repo (`docs/operasional/`) agar tidak bergantung pada ingatan satu orang.

## 28. Glosarium (Baru)

| Istilah | Arti |
|---|---|
| ATP | Alur Tujuan Pembelajaran — urutan capaian pembelajaran per mapel |
| BK | Bimbingan Konseling |
| DoD | Definition of Done — checklist kelulusan sebuah modul (Bagian 17) |
| EMIS / Dapodik | Sistem informasi manajemen pendidikan Kemenag / data pokok pendidikan Kemendikbud |
| PKKM | Penjaminan Kualitas & Peningkatan Mutu Madrasah |
| PPDB | Penerimaan Peserta Didik Baru |
| RBAC | Role-Based Access Control — pengaturan hak akses berbasis role & permission |
| SNP | Standar Nasional Pendidikan (8 standar, acuan akreditasi) |
| TU | Tata Usaha |
| UAT | User Acceptance Test — pengujian penerimaan oleh pengguna sebenarnya |
| UU PDP | Undang-Undang Perlindungan Data Pribadi |
| Wakamad | Wakil Kepala Madrasah (bidang kurikulum/kesiswaan/sarpras/humas) |
| Walking skeleton | Satu alur tipis end-to-end yang dibangun lebih dulu untuk memvalidasi arsitektur |
| Seeder / Factory | Data contoh terprogram untuk mengisi database saat pengembangan/pengujian |

## 29. Referensi Dokumen Sumber

- `00-prompt-master.md` — Prompt master & aturan awal proyek
- `01-tahap-1-analisis-kebutuhan.md`
- `02-tahap-2-daftar-modul.md`
- `03-tahap-3-use-case-diagram.md`
- `04-tahap-4-flowchart.md`
- `05-tahap-5-erd.md`
- `06-tahap-6-desain-database.md`
- `07-tahap-7-struktur-folder-laravel.md`
- `08-tahap-8-instalasi-database-laravel.md`
- `09-tahap-9-authentication.md`
- `10-tahap-10-rbac.md`
- Sesi tinjauan strategi pengembangan (dasar perubahan v1 → v2)

Untuk detail teknis lengkap per tahap, rujuk langsung ke dokumen sumber terkait di atas.
