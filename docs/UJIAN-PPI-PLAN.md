# Plan Modul: Ujian PPI Kelas VI (Munaqasah)

> Draf v2 — sudah mengikuti jawaban Anda atas pertanyaan terbuka v1.
> Berbeda dari modul **PPI (Pembiasaan)** (rutin, sepanjang tahun) dan **Tahfidz** (ranah beda, tidak dipakai di sini meski konsepnya mirip "setoran hafalan"). Modul ini **event ujian akhir kelas VI**, sekali per tahun ajaran, dengan dua fase (setoran hafalan → ujian lisan) dan kepanitiaan penuh (ruang, penguji, grup pembimbing).
> Lokasi sidebar diusulkan: **Akademik → Ujian PPI**.

---

## 1. Alur Bisnis (final, berdasarkan konfirmasi Anda)

**Fase 0 — Perencanaan (sebelum periode dibuka)**
Panitia (Admin/Wakamad Kurikulum) menyiapkan, untuk periode ujian tahun berjalan:
- **Skala predikat** (mis. A+ 90–100, A 80–89, B 70–79, C 60–69, D <60 — nilai & label ditentukan panitia sendiri, bukan hardcode sistem), termasuk **deskripsi otomatis per predikat** dan penanda **"predikat ini = TIDAK LULUS"** (default ke predikat terendah, mis. D — tapi panitia yang menentukan predikat mana). Skala dibuat **per periode**, dengan opsi **"Salin dari periode sebelumnya"** agar panitia tidak input ulang tiap tahun (hasil salinan tetap bisa diedit sebelum periode dikunci).
- **Struktur aspek penilaian**: induk (mis. "4. Wudhu") beserta anak-item (mis. "4.1 Niat Wudhu", "4.2 Praktik Wudhu"...), tiap **induk** di-assign ke **penguji ke berapa** (1/2/3), dan diberi **bobot** (persentase kontribusi ke nilai akhir) — bobot diatur di Fase 0 untuk tiap komponen: bobot Penguji I, bobot Penguji II, bobot Penguji III, dan bobot Nilai Hafalan (total wajib 100%). Ini menggantikan asumsi rata-rata sederhana sebelumnya.
- **Ruang ujian** + 3 penguji (guru) per ruang.
- **Grup setoran** (7–15 siswa/grup) + 1 guru pembimbing per grup — terpisah total dari pembagian ruang ujian.
- **Peserta**: seluruh siswa kelas VI, di-assign ke 1 grup setoran + 1 ruang ujian (rombel asal ikut tersimpan sebagai snapshot, untuk keperluan Rank Lokal). **No urut** default berdasarkan abjad nama siswa, admin bisa reorder manual (drag/edit angka) sebelum periode dikunci.

Begitu periode diubah status ke **"Berlangsung"**, konfigurasi skala predikat & struktur aspek **terkunci otomatis** (tidak bisa diubah lagi, demi stabilitas nilai yang sedang berjalan) — hanya Super Admin yang bisa membuka kunci ini secara eksplisit (tercatat di audit log) bila benar-benar diperlukan.

**Fase 1 — Setoran Hafalan (1–2 minggu)**
Materi: Juz 30 (Ad-Dhuha s.d. An-Nas), Yasin, Al-Waqiah — daftar surah adalah **master per periode**, editable di Fase 0.
Guru pembimbing grup login dengan akun guru masing-masing → hanya melihat siswa di grup binaannya → input nilai **per surah per siswa**.

**Fase 2 — Ujian Lisan (hari-H, per ruang)**
3 penguji per ruang, tiap penguji login akun sendiri → hanya melihat siswa di ruangnya, dan hanya aspek induk yang jadi tanggung jawabnya (sesuai assignment Fase 0).

**Fase 3 — Perhitungan Otomatis**
Sistem menghitung otomatis (bukan tombol "Tarik Data/Hitung Rata" manual): jumlah & rata-rata per penguji, rata-rata gabungan ujian lisan, rata-rata setoran hafalan, lalu **nilai akhir = (rata-rata Penguji I × bobot P1) + (rata-rata Penguji II × bobot P2) + (rata-rata Penguji III × bobot P3) + (rata-rata Hafalan × bobot Hafalan)** sesuai bobot yang diatur di Fase 0, predikat & deskripsi (dari skala Fase 0), status lulus/tidak, Rank Total (lintas ruang, se-kelas VI) & Rank Lokal (per rombel asal).

**Fase 4 — Dokumen & Pengumuman**
Teks pembawa acara & berita acara di-generate otomatis per siswa, siap cetak.

**Fase 5 — Koreksi (bila perlu)**
Admin/Wakamad Kurikulum bisa mengedit langsung nilai di tabel rekap keseluruhan bila ada salah input dari penguji — perubahan tercatat di **audit log** (nilai lama, nilai baru, siapa, kapan, alasan), sesuai standar proyek.

**Fase 6 — Arsip**
Setelah periode selesai, data tetap tersimpan sebagai **riwayat** (bukan dihapus). Data tahun-tahun sebelum sistem ini ada (mis. TP 2022/2023 dari spreadsheet lama) diarsipkan lewat **import** ke periode berstatus "Diarsipkan" — read-only, hanya untuk rekap historis (detail per-aspek boleh kosong bila datanya tidak tersedia, minimal simpan nilai akhir/predikat/kelulusan per siswa).

---

## 2. Struktur Data (revisi)

| Tabel | Fungsi |
|---|---|
| `ppi_exam_periods` | Per tahun ajaran. `academic_year_id`, judul, tanggal setoran mulai/selesai, tanggal ujian lisan, status (`draft`\|`setup`\|`berlangsung`\|`selesai`\|`diarsipkan`), `config_locked_at`, `bobot_p1`, `bobot_p2`, `bobot_p3`, `bobot_hafalan` (persen, total harus 100, diisi Fase 0). |
| `ppi_exam_predicate_scales` | Per periode. `predikat` (label), `nilai_min`, `nilai_max`, `deskripsi`, `is_tidak_lulus` (bool), `urutan`. Dikunci saat periode berlangsung. Bisa **disalin dari periode sebelumnya** (aksi "Salin Skala") saat setup periode baru, lalu tetap boleh diedit sebelum dikunci. |
| `ppi_exam_aspect_categories` | Induk aspek. `exam_period_id`, kode, nama, `penguji_urutan` (1/2/3), urutan. Dikunci saat periode berlangsung. |
| `ppi_exam_aspects` | Anak-item. `category_id`, kode, nama, urutan. |
| `ppi_exam_hafalan_materi` | Master surah/materi setoran per periode (Ad-Dhuha...An-Nas, Yasin, Al-Waqiah), urutan. |
| `ppi_exam_rooms` | Ruang ujian per periode. |
| `ppi_exam_examiners` | Pivot ruang × penguji: `exam_room_id`, `employee_id` (guru dari Data Guru/Kepegawaian), `urutan` (1/2/3). **Constraint**: `employee_id` unik per `exam_period_id` (1 guru = maksimal 1 ruang per periode, dicegah di level validasi & unique index gabungan periode+guru). |
| `ppi_exam_groups` | Grup setoran per periode: nama grup, `pembimbing_employee_id`. |
| `ppi_exam_participants` | `exam_period_id`, `student_id`, `exam_room_id`, `group_id`, `class_group_id` (snapshot rombel asal utk Rank Lokal), `no_urut` (default urut abjad nama saat di-assign, admin bisa reorder manual sebelum periode dikunci), status. **Constraint**: `student_id` unik per `exam_period_id` (1 siswa = 1 ruang ujian & 1 grup setoran per periode; siswa yang sudah ter-assign hilang dari daftar pilih ruang/grup lain). Field cache (dihitung ulang oleh service): `jumlah_p1/rata_p1`, `jumlah_p2/rata_p2`, `jumlah_p3/rata_p3`, `jumlah_ujian_lisan/rata_ujian_lisan`, `rata_hafalan`, `nilai_akhir` (hasil formula bobot), `predicate_scale_id`, `status_lulus`, `rank_total`, `rank_lokal`. |
| `ppi_exam_scores` | Nilai ujian lisan: `participant_id`, `aspect_id`, `nilai`, `examiner_employee_id`, `input_at`. Riwayat perubahan → audit log terpusat proyek. |
| `ppi_exam_hafalan_scores` | Nilai setoran: `participant_id`, `hafalan_materi_id`, `nilai`, `tanggal_setor`, `dinilai_oleh_employee_id`. |
| `ppi_exam_archives` *(atau cukup pakai `ppi_exam_periods` berstatus `diarsipkan`)* | Periode lama diimport dari Excel — hanya kolom rekap akhir (nilai per penguji jika ada, nilai akhir, predikat, lulus/tidak) tanpa perlu detail per-aspek. |

**Service `PpiExamScoringService`**: hitung ulang seluruh angka cache di `ppi_exam_participants` setiap ada perubahan `ppi_exam_scores`/`ppi_exam_hafalan_scores` — termasuk Rank Total & Rank Lokal (recompute seluruh peserta 1 periode).

**Akses berbasis penugasan** (pola sama dengan wali kelas/guru mapel di PRD §207–208, bukan sekadar role):
- Penguji ujian lisan → hanya bisa input nilai untuk peserta di `exam_room_id` tempat dia terdaftar di `ppi_exam_examiners`, dan hanya `aspect_category` dengan `penguji_urutan` = urutannya.
- Pembimbing setoran → hanya bisa input nilai untuk peserta di `group_id` tempat dia jadi `pembimbing_employee_id`.
- Login pakai **akun guru yang sudah ada** (data dari Kepegawaian → Data Guru) — tidak perlu akun terpisah.

---

## 3. Usulan Layar

| Layar | Untuk siapa | Isi |
|---|---|---|
| **Periode Ujian** | Admin/Wakamad Kurikulum | Buat periode, atur tanggal, ubah status (draft→setup→berlangsung→selesai→diarsipkan). Status "berlangsung" mengunci konfigurasi. |
| **Skala Predikat** | Admin/WK Kurikulum | Atur predikat, rentang nilai, deskripsi, tandai predikat "tidak lulus". Tombol **"Salin dari Periode Sebelumnya"** saat setup periode baru. Terkunci saat berlangsung. |
| **Bobot Penilaian** | Admin/WK Kurikulum | Atur persentase bobot Penguji I, Penguji II, Penguji III, dan Nilai Hafalan untuk formula nilai akhir (total wajib 100%), diisi di Fase 0. Terkunci saat berlangsung. |
| **Struktur Aspek Penilaian** | Admin/WK Kurikulum | Kelola induk & anak aspek, assign penguji ke-1/2/3 per induk. Terkunci saat berlangsung. |
| **Materi Setoran Hafalan** | Admin/WK Kurikulum | Kelola daftar surah/materi Fase 1. |
| **Ruang & Penguji** | Admin/WK Kurikulum | Buat ruang, assign 3 guru penguji per ruang (pilih dari Data Guru). **Validasi**: 1 guru hanya boleh terdaftar sebagai penguji di 1 ruang per periode — sistem menolak/menyembunyikan guru yang sudah ter-assign di ruang lain agar tidak bentrok jadwal. |
| **Grup Setoran & Pembimbing** | Admin/WK Kurikulum | Buat grup (7–15 siswa), assign 1 guru pembimbing per grup. |
| **Peserta** | Admin/WK Kurikulum | Assign siswa kelas VI → ruang ujian + grup setoran (bulk assign). Rombel asal otomatis disnapshot. No urut default abjad nama, bisa direorder manual. **Validasi**: siswa yang sudah ter-assign ke suatu ruang/grup otomatis hilang dari daftar pilih di ruang/grup lain pada periode yang sama (1 siswa = 1 ruang ujian & 1 grup setoran, tidak boleh dobel). |
| **Input Nilai Setoran** (guru pembimbing) | Guru (akun sendiri) | Hanya siswa di grup binaannya, nilai per surah. |
| **Input Nilai Ujian Lisan** (guru penguji) | Guru (akun sendiri) | Hanya siswa di ruangnya, hanya aspek induk miliknya. |
| **Teks Pembawa Acara** (per siswa) | Admin/WK Kurikulum, Guru — ditugaskan penguji (ruang terkait) | Generate otomatis (pembuka, persilahan penguji, penutup), sisip nama siswa & tim penguji. Bisa dibuka oleh penguji di ruang tsb karena merangkap sebagai pembawa acara. |
| **Berita Acara** (per siswa) | Admin/WK Kurikulum, Guru — ditugaskan penguji (ruang terkait) | Identitas siswa, tim penguji, nilai per penguji, nilai akhir, predikat, deskripsi, status lulus, kolom TTD 3 penguji. Ditampilkan di laman (bukan hanya file PDF) agar mudah dibacakan langsung oleh penguji/pembawa acara saat pengumuman, dengan tombol **export PDF** terpisah untuk keperluan cetak/arsip. |
| **Rekap Kelas VI** | Admin/WK Kurikulum (edit+export), Kepala Madrasah (lihat) | Tabel besar: siswa × semua kode aspek + kolom ringkasan (Jumlah, Rata, Predikat, Deskripsi, Gender, Nama Ayah, per-penguji, No Urut, Rank Total, Rank Lokal, Grup Setoran, NISN). **Admin/WK Kurikulum bisa edit sel nilai langsung** di sini untuk koreksi (dengan audit log + alasan wajib diisi). Filter (ruang/grup/status lulus/rombel/pencarian nama). Export PDF & Excel (ikut filter aktif, atau seluruh data). |
| **Arsip Tahun Sebelumnya** | Admin/WK Kurikulum | List periode berstatus "diarsipkan", form import Excel (mapping kolom nilai akhir per siswa minimal), read-only setelah tersimpan. |

---

## 4. Contoh Teks Template (Teks Pembawa Acara & Berita Acara)

Berikut draf template mengikuti isi dokumen lama Anda, dengan placeholder yang diisi otomatis dari data sistem (`{{...}}`). Panitia bisa mengedit kalimat baku ini di **Pengaturan Template** (bagian dari Periode Ujian), sementara bagian berplaceholder tetap otomatis tersisip per siswa.

### 4.1 Teks Pembawa Acara

```
TEKS PEMBAWA ACARA

1. PEMBUKAAN

Assalamu'alaikum Warahmatullahi wabarakatuh.

Alhamdulillah Asholatu wassalamu ala sayidina maulana muhammadin wa 'ala alihi
shohbihi ajma'in, amma ba'du

Sidang Asesmen PPI atas nama {{NAMA_SISWA}} Bin/Binti {{NAMA_AYAH}}

Secara resmi di buka dengan ucapan Basmalah

Kami persilahkan kepada Penguji Pertama untuk mengawali pertanyaan

2. PEMBACAAN BERITA ACARA (terlampir)

3. PENUTUP

Sebelum kita akhiri sidang Asesmen Praktek Pengamalan Ibadah

kami mohon kepada penguji {{NAMA_PENGUJI_PENUTUP}} untuk memberikan pesan/nasehat.

Kepada bapak/ibu {{NAMA_PENGUJI_PENUTUP}} dipersilahkan.

Demikian sidang asesmen PPI pada hari ini

apabila kami segenap penguji ada khilaf dalam ucapan dan perbuatan mohon di maafkan

wallahul muwafiq ila aqwamit thoriq, Wassalamu'alaikum Wr.Wb.
```

Catatan: `{{NAMA_PENGUJI_PENUTUP}}` default diisi Penguji III (atau bisa dipilih manual oleh pembawa acara/penguji saat membuka layar ini), karena di dokumen asli kolom ini memang diisi manual saat acara berlangsung — bukan hasil hitungan sistem.

### 4.2 Berita Acara

```
BERITA ACARA
ASESMEN PRAKTEK PENGAMALAN IBADAH (PPI)
SISWA KELAS VI
{{NAMA_MADRASAH}}
TAHUN PELAJARAN {{TAHUN_AJARAN}}

Dengan mengucap Bismillahirrahmanirrahim

Pada hari {{HARI}} tanggal {{TANGGAL}}
pukul {{JAM}} WIB. telah terlaksana Asesmen Praktek Pengamalan Ibadah (PPI)
atas nama {{NAMA_SISWA}}
bin/binti {{NAMA_AYAH}}

dengan Tim Penguji yang terdiri dari :

Penguji I  : {{NAMA_PENGUJI_1}}
Penguji II : {{NAMA_PENGUJI_2}}
Penguji III: {{NAMA_PENGUJI_3}}

Dari hasil beberapa pertanyaan dari tim penguji ananda
memperoleh sejumlah nilai sebagai berikut :

Penguji I   nilai rata-rata yang diperoleh  {{RATA_P1}}
Penguji II  nilai rata-rata yang diperoleh  {{RATA_P2}}
Penguji III nilai rata-rata yang diperoleh  {{RATA_P3}}

Dari ketiga penguji ditambah nilai hafalan surah-surah Yasin, Waqi'ah
dan surah-surah pendek sebelumnya (dihitung sesuai bobot masing-masing).
Maka ananda memperoleh nilai rata-rata akhir adalah {{NILAI_AKHIR}}
dan di nyatakan {{STATUS_LULUS}}
pada sidang Asesmen PPI ini dengan predikat {{PREDIKAT}}
dan dengan deskripsi {{DESKRIPSI}}

Di tetapkan di {{KOTA}} pada tanggal {{TANGGAL}}

   Penguji I                Penguji II                Penguji III


{{NAMA_PENGUJI_1}}        {{NAMA_PENGUJI_2}}        {{NAMA_PENGUJI_3}}
```

Semua placeholder di atas terisi dari data `ppi_exam_participants` hasil hitungan §2 (rata per penguji, nilai akhir, predikat, deskripsi, status lulus) — panitia/penguji tidak perlu mengetik ulang nilai secara manual seperti di spreadsheet lama. Kop surat (logo madrasah kiri, logo yayasan kanan) mengikuti **Pengaturan Madrasah** yang sudah ada di sistem.

---

## 5. Usulan Otorisasi

| Role/Penugasan | Setup Periode/Skala/Aspek | Ruang/Grup/Peserta | Input Nilai Ujian Lisan | Input Nilai Setoran | Berita Acara & Teks MC | Rekap: Lihat | Rekap: Edit Koreksi | Export | Arsip Import |
|---|---|---|---|---|---|---|---|---|---|
| Super Admin | ✅ (+buka kunci) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Wakamad Kurikulum | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Guru — ditugaskan penguji (ruang X) | ❌ | ❌ | ✅ (khusus ruang & aspek miliknya) | ❌ | ✅* | ❌ | ❌ | ❌ | ❌ |
| Guru — ditugaskan pembimbing (grup Y) | ❌ | ❌ | ❌ | ✅ (khusus grup miliknya) | ❌ | ❌ | ❌ | ❌ | ❌ |
| Kepala Madrasah | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ (read-only) | ❌ | ✅ | ❌ |

*(Satu guru bisa punya penugasan ganda — jadi penguji di satu ruang sekaligus pembimbing di satu grup — karena ini penugasan per periode, bukan role permanen.)*

\* Guru penguji hanya bisa membuka **Teks Pembawa Acara** dan **Berita Acara** untuk ruang tempat ia bertugas (karena merangkap pembawa acara/pembaca berita acara); tidak bisa mengedit atau mengexport dari Rekap Kelas VI.

---

## 6. Data Default Aspek Penilaian (seed awal, tetap configurable via §Struktur Aspek Penilaian)

Ini akan jadi **data awal (seed)** saat periode dibuat — panitia tetap bisa mengubah sebelum periode dikunci (status "Berlangsung"). Urutan di bawah ini **menentukan urutan kolom di Rekap Kelas VI**.

### Ujian Lisan — Penguji I

**1. Wudhu**
1. Niat Wudhu
2. Praktik Wudhu
3. Do'a Sesudah Wudhu
4. Niat Tayamum

**2. Praktik Shalat**
1. Lafaz azan
2. Lafaz iqamah
3. Do'a sesudah azan
4. Do'a sesudah iqamah
5. Niat shalat subuh
6. Niat shalat zuhur
7. Niat shalat asar
8. Niat shalat magrib
9. Niat shalat isya
10. Do'a iftitah
11. Al-fatihah
12. Bacaan ruku'
13. Bacaan i'tidal
14. Do'a Qunut
15. Bacaan sujud
16. Bacaan duduk antara 2 sujud
17. Bacaan tahiyat awal
18. Bacaan tahiyat akhir
19. Salam
20. Do'a sebelum salam
21. Wirid / Dzikir Pendek bada shalat
22. Do'a selamat

### Ujian Lisan — Penguji II

**3. Tilawatil Qur'an**
1. Makhorijul huruf
2. Hukum Bacaan
3. Kelancaran

**4. Shalat Jenazah**
1. Niat salat Jenazah untuk laki-laki Dewasa
2. Niat salat Jenazah untuk Perempuan Dewasa
3. Niat Salat Jenazah untuk Anak laki-laki
4. Niat Salat Jenazah Untuk Anak Perempuan
5. Bacaan Takbir Pertama
6. Bacaan Takbir Kedua
7. Bacaan Takbir Ketiga
8. Bacaan Takbir Keempat

**5. Hafalan Hadis**
1. Hadis tentang amal Shaleh
2. Hadis tentang keutamaan memberi

### Ujian Lisan — Penguji III

**6. Do'a-Do'a Harian**
1. Do'a Senandung Al-Qur'an
2. Do'a mau Belajar
3. Do'a Mau makan
4. Do'a sesudah makan
5. Do'a masuk WC
6. Do'a keluar WC
7. Do'a Masuk rumah
8. Do'a Keluar rumah
9. Do'a Mau tidur
10. Do'a bangun tidur
11. Do'a masuk mesjid
12. Do'a Keluar mesjid
13. Do'a untuk Kedua Orang Tua
14. Niat Puasa Ramadhan
15. Do'a Berbuka Puasa
16. Do'a bercermin
17. Do'a Naik Kendaraan Darat
18. Do'a Naik Kendaraan Air

**7. Pengetahuan Agama**
1. Rukun islam
2. Rukun iman
3. Rukun wudhu
4. Rukun shalat
5. Shalat Sunnah

> Catatan: Anda menuliskan urutan penomoran induk agak berbeda antar dua sumber (mis. Tilawah sempat disebut "1." dan "3." di pesan berbeda). Saya kelompokkan **per penguji** (I → II → III) sesuai kode kolom asli di spreadsheet Anda (4.x/5.x = Penguji I, 1.x/6.x/8.x = Penguji II, 3.x/7.x = Penguji III), supaya urutan kolom rekap mengikuti urutan penguji yang menilai, bukan urutan penyebutan pesan. Kalau Anda mau urutan lain (mis. semua sesuai nomor asli Anda: Tilawah, Do'a Harian, Wudhu, Praktik Shalat, Shalat Jenazah, Pengetahuan Agama, Hafalan Hadis), tinggal beri tahu — tinggal atur ulang di §Struktur Aspek Penilaian, tidak mengubah struktur data.

### Setoran Hafalan (Fase 1, terpisah dari ujian lisan)

1. Yaasin
2. Al-Waqi'ah
3. Ad-Dhuha
4. Al-Insyirah
5. At-Tiin
6. Al-`Alaq
7. Al-Qadar
8. Al-Bayyinah
9. Al-Zalzalah
10. Al-`Adiyat
11. Al-Qari'ah
12. At-Takasur
13. Al-`Ashr
14. Al-Humazah
15. Al-Fiil
16. Al-Quraisy
17. Al-Ma`un
18. Al-Kausar
19. Al-Kafirun
20. An-Nasr
21. Al-Lahab
22. Al-Ikhlas
23. Al-Falaq
24. An-Naas

### Kolom Rekap Kelas VI (urutan final)

`No Urut` → `NISN` → `Nama Siswa` → `Ruang` → *(kolom tiap aspek Wudhu → Praktik Shalat → Tilawatil Qur'an → Shalat Jenazah → Hafalan Hadis → Do'a Harian → Pengetahuan Agama, sesuai urutan seed di atas)* → *(kolom tiap materi Setoran Hafalan: Yaasin → ... → An-Naas)* → `Jumlah P1` → `Rata P1` → `Jumlah P2` → `Rata P2` → `Jumlah P3` → `Rata P3` → `Rata Hafalan` → `Jumlah` → `Rata` → `Predikat` → `Deskripsi` → `Status Lulus` → `Gender` → `Nama Ayah` → `Grup Setoran` → `Rank Total` → `Rank Lokal`.

---

## 7. Keputusan Final (§7 sebelumnya, sudah dikonfirmasi)

1. **Skala predikat**: per periode, dengan opsi **"Salin dari Periode Sebelumnya"** ✅ — sudah diterapkan di §2/§3.
2. **Nilai akhir gabungan**: **bukan** rata-rata sederhana — dipakai **field bobot per komponen** (Penguji I, Penguji II, Penguji III, Nilai Hafalan), diatur panitia di Fase 0, total harus 100%. Formula: `nilai_akhir = Σ(rata_komponen × bobot_komponen)`. ✅ — sudah diterapkan di §1 (Fase 0 & Fase 3), §2, §3 (layar baru "Bobot Penilaian").
3. **Import arsip**: pakai **template import standar** (kolom: NISN, Nama, Rata P1/P2/P3, Nilai Hafalan, Nilai Akhir, Predikat, Status Lulus, Rank — panitia menyesuaikan data lama ke template ini sebelum upload). ✅ — tetap seperti usulan di §3 "Arsip Tahun Sebelumnya", tidak perlu mapping kolom bebas.
4. **No urut**: default **abjad nama**, admin bisa reorder manual. ✅ — sudah diterapkan di §2/§3.

Semua poin di atas sudah tercermin di bagian-bagian sebelumnya dokumen ini. Modul siap lanjut ke tahap **desain wireframe/frontend per layar** — beri tahu kalau ada layar prioritas yang ingin dikerjakan lebih dulu (mis. Input Nilai Ujian Lisan, karena paling sering dipakai penguji), atau saya mulai dari alur Fase 0 (Periode → Skala Predikat → Bobot → Struktur Aspek) secara berurutan.
