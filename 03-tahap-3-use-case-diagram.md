# Tahap 3 — Use Case Diagram

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 3091–4507.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

=========================================================
TAHAP 3
PERANCANGAN USE CASE DIAGRAM SIM MADRASAH
1. Konsep Sederhana
Use Case Diagram adalah diagram yang menunjukkan hubungan antara pengguna dan fungsi yang tersedia dalam sistem.
Use Case Diagram menjawab tiga pertanyaan utama:
1. Siapa yang menggunakan sistem?
2. Apa yang dapat dilakukan oleh setiap pengguna?
3. Bagian sistem mana yang dapat diakses oleh pengguna tersebut?
Pada tahap ini, kita belum membuat database, migration, controller, route, model, atau tampilan.
Kita hanya memetakan hubungan antara pengguna dan layanan yang disediakan SIM Madrasah.

2. Analogi Sederhana
SIM Madrasah dapat dianalogikan sebagai gedung madrasah yang memiliki banyak ruangan.
Setiap orang memiliki tugas dan izin yang berbeda.
Contohnya:
* Bendahara boleh masuk ke ruang keuangan.
* Guru boleh masuk ke ruang pembelajaran.
* Petugas perpustakaan boleh masuk ke perpustakaan.
* Kepala madrasah boleh melihat laporan dari banyak bagian.
* Siswa hanya boleh melihat data miliknya.
* Orang tua hanya boleh melihat data anak yang terhubung dengannya.
* Pengunjung umum hanya boleh melihat informasi yang dipublikasikan.
Dalam Use Case Diagram:
* Orang yang menggunakan sistem disebut aktor.
* Kegiatan yang dilakukan disebut use case.
* Garis antara aktor dan use case menunjukkan adanya interaksi.
* Kotak besar menunjukkan batas SIM Madrasah.
* Aktor berada di luar kotak.
* Fungsi sistem berada di dalam kotak.

3. Tujuan Perancangan Use Case Diagram
Use Case Diagram SIM Madrasah dibuat untuk:
1. Menentukan seluruh pengguna sistem.
2. Menentukan fungsi yang dapat digunakan setiap pengguna.
3. Mencegah tumpang tindih hak akses.
4. Menjadi dasar pembuatan flowchart.
5. Menjadi dasar penyusunan ERD.
6. Menjadi dasar penentuan role dan permission.
7. Menjadi dasar pembuatan menu berdasarkan role.
8. Menentukan batas tanggung jawab setiap modul.
9. Membantu proses pengujian sistem.
10. Mencegah fitur penting terlupakan.

4. Prinsip Pemodelan Use Case
4.1 Aktor tidak selalu berarti satu orang
Aktor menggambarkan peran seseorang saat menggunakan sistem.
Satu orang dapat bertindak sebagai beberapa aktor.
Contoh:
Seorang guru dapat memiliki peran sebagai:
* Guru Mata Pelajaran.
* Wali Kelas.
* Editor Berita.
* Pembina Ekstrakurikuler.
* Anggota Tim PKKM.
Sistem akan memberikan akses berdasarkan role, permission, jabatan, dan penugasan yang dimiliki pengguna.
4.2 Aktor bukan nama pegawai
Use Case Diagram tidak menggunakan nama orang tertentu.
Contoh yang benar:
* Bendahara.
* Wali Kelas.
* Kepala Madrasah.
Contoh yang tidak tepat:
* Bapak Ahmad.
* Ibu Siti.
* Pak Ali.
Nama pegawai akan disimpan dalam data pengguna dan data guru atau pegawai.
4.3 Use case menggambarkan tujuan
Nama use case harus menunjukkan tindakan yang dilakukan pengguna.
Contoh:
* Mengelola Data Siswa.
* Memasukkan Nilai.
* Memverifikasi Pembayaran.
* Menerbitkan Rapor.
* Mengunggah Eviden PKKM.
Nama seperti “Data Siswa” kurang jelas karena tidak menunjukkan tindakan.
4.4 Diagram tidak menggantikan permission
Garis antara aktor dan use case menunjukkan bahwa aktor dapat berinteraksi dengan fungsi tersebut.
Namun, akses sebenarnya tetap dikendalikan melalui:
* Role.
* Permission.
* Middleware.
* Gate.
* Policy.
* Penugasan.
* Relasi pengguna dengan data.
* Status tahun ajaran dan semester.
Contohnya, Guru Mata Pelajaran memiliki akses ke use case “Memasukkan Nilai”. Akan tetapi, guru tersebut hanya boleh memasukkan nilai untuk mata pelajaran dan kelas yang menjadi penugasannya.

5. Batas Sistem
Nama sistem yang berada di dalam batas Use Case Diagram adalah:
Sistem Informasi Manajemen Madrasah Terintegrasi
Seluruh fungsi berikut berada di dalam SIM Madrasah:
* Authentication.
* Pengguna.
* Role dan permission.
* Data master.
* Akademik.
* Kesiswaan.
* Keuangan.
* Tata usaha.
* Kepegawaian.
* Inventaris.
* Perpustakaan.
* Laboratorium.
* Website publik.
* Portal berita.
* PPDB.
* PKKM.
* Akreditasi.
* Portal siswa.
* Portal orang tua.
* Pelaporan.
* Audit.
* Backup.
* Pengaturan sistem.
Pihak atau pengguna yang berinteraksi dengan fungsi tersebut berada di luar batas sistem.

6. Daftar Aktor SIM Madrasah
ACT-001 Pengunjung Umum
Pengunjung umum adalah masyarakat yang membuka website madrasah tanpa login.
Aktivitas utama:
* Melihat profil madrasah.
* Melihat berita.
* Melihat agenda.
* Melihat pengumuman publik.
* Melihat galeri.
* Melihat informasi PPDB.
* Melihat data publik yang diizinkan.
ACT-002 Calon Siswa atau Orang Tua Pendaftar
Aktor ini menggunakan layanan PPDB.
Aktivitas utama:
* Membuat akun pendaftaran.
* Mengisi formulir PPDB.
* Mengunggah dokumen.
* Mengirim pendaftaran.
* Mencetak bukti pendaftaran.
* Melihat hasil seleksi.
* Melakukan daftar ulang.
ACT-003 Siswa
Aktivitas utama:
* Login.
* Melihat profil.
* Melihat jadwal.
* Melihat kehadiran.
* Melihat nilai yang telah dipublikasikan.
* Melihat rapor.
* Melihat prestasi.
* Melihat tahfidz.
* Melihat ekstrakurikuler.
* Melihat portofolio.
* Melihat tagihan dan pembayaran.
* Mengakses ebook.
ACT-004 Orang Tua atau Wali
Aktivitas utama:
* Login.
* Memilih anak yang terhubung.
* Melihat profil anak.
* Melihat kehadiran anak.
* Melihat nilai anak.
* Melihat rapor.
* Melihat prestasi.
* Melihat pelanggaran yang diizinkan.
* Melihat perkembangan tahfidz.
* Melihat tagihan.
* Melihat riwayat pembayaran.
* Mengunduh dokumen yang diizinkan.
ACT-005 Guru Mata Pelajaran
Aktivitas utama:
* Melihat jadwal mengajar.
* Melihat penugasan mengajar.
* Mengelola ATP.
* Mengunggah perangkat pembelajaran.
* Mengisi jurnal mengajar.
* Mencatat kehadiran pembelajaran.
* Memasukkan nilai.
* Mengelola remedial.
* Mengelola pengayaan.
* Melihat perkembangan siswa pada kelas yang diajar.
* Menjadi kontributor berita jika mendapatkan izin.
ACT-006 Wali Kelas
Aktivitas utama:
* Melihat daftar siswa kelas.
* Memantau kehadiran.
* Memantau nilai.
* Memantau prestasi.
* Memantau pelanggaran.
* Memantau pembayaran.
* Mengisi catatan wali kelas.
* Mengelola data pendukung rapor.
* Memproses rapor.
* Mengusulkan kenaikan kelas.
* Melihat portofolio siswa pada kelasnya.
ACT-007 Guru BK
Aktivitas utama:
* Mengelola layanan konseling.
* Mencatat hasil asesmen.
* Mencatat tindak lanjut.
* Melihat pelanggaran siswa.
* Mengatur tingkat kerahasiaan catatan.
* Membuat ringkasan konseling.
* Melihat perkembangan siswa sesuai kewenangan.
ACT-008 Petugas Perpustakaan
Aktivitas utama:
* Mengelola data buku.
* Mengelola anggota.
* Mencatat peminjaman.
* Mencatat pengembalian.
* Mencatat perpanjangan.
* Mengelola keterlambatan.
* Mengelola denda jika diterapkan.
* Mengelola ebook.
* Membuat laporan perpustakaan.
ACT-009 Petugas Laboratorium
Aktivitas utama:
* Mengelola laboratorium.
* Mengelola alat.
* Mengelola bahan.
* Mencatat stok.
* Mencatat peminjaman.
* Mencatat pengembalian.
* Mencatat kerusakan.
* Mencatat pemeliharaan.
* Membuat laporan penggunaan.
ACT-010 Editor Berita
Aktivitas utama:
* Membuat berita.
* Mengedit berita.
* Memeriksa naskah.
* Mengelola kategori dan tag.
* Mengelola gambar.
* Mengelola SEO.
* Mengajukan persetujuan.
* Menjadwalkan publikasi.
* Menerbitkan berita sesuai permission.
* Mengarsipkan berita.
ACT-011 Tata Usaha
Aktivitas utama:
* Mengelola data siswa.
* Mengelola data guru dan pegawai.
* Mengelola data orang tua.
* Mengelola akun tertentu.
* Mengelola surat masuk.
* Mengelola surat keluar.
* Mengelola SK.
* Mengelola MOU.
* Mengelola arsip.
* Mengelola dokumen administratif.
* Membuat laporan administrasi.
ACT-012 Bendahara
Aktivitas utama:
* Mengelola jenis tagihan.
* Membuat tagihan siswa.
* Mencatat pembayaran.
* Mencatat pembayaran cicilan.
* Memberikan potongan sesuai kewenangan.
* Mencatat keringanan.
* Mencetak kwitansi.
* Mengoreksi transaksi.
* Melihat tunggakan.
* Membuat laporan pembayaran.
ACT-013 Wakamad Kurikulum
Aktivitas utama:
* Mengelola tahun ajaran dan semester.
* Mengelola mata pelajaran.
* Mengelola kurikulum.
* Mengelola kelas.
* Mengelola penempatan siswa.
* Mengelola penugasan mengajar.
* Mengelola jadwal.
* Mereview perangkat pembelajaran.
* Memantau jurnal mengajar.
* Memverifikasi nilai.
* Memproses rapor.
ACT-014 Wakamad Kesiswaan
Aktivitas utama:
* Mengelola data kesiswaan.
* Mengelola prestasi.
* Mengelola pelanggaran.
* Mengelola ekstrakurikuler.
* Mengelola tahfidz.
* Mengelola pembiasaan.
* Mengelola organisasi siswa.
* Memantau portofolio siswa.
ACT-015 Wakamad Sarpras
Aktivitas utama:
* Mengelola ruangan.
* Mengelola inventaris.
* Mengelola mutasi barang.
* Mengelola pemeliharaan.
* Memantau laboratorium.
* Memantau kondisi fasilitas.
* Membuat laporan sarana dan prasarana.
ACT-016 Wakamad Humas
Aktivitas utama:
* Mengelola website publik.
* Mengelola publikasi.
* Memantau berita.
* Mengelola agenda.
* Mengelola pengumuman.
* Mengelola galeri.
* Mengelola kerja sama.
* Mengelola alumni.
* Memantau hubungan masyarakat.
ACT-017 Kepala Madrasah
Aktivitas utama:
* Melihat dashboard pimpinan.
* Melihat laporan akademik.
* Melihat laporan keuangan.
* Melihat laporan kehadiran.
* Melihat laporan kesiswaan.
* Melihat kondisi inventaris.
* Menyetujui berita jika diperlukan.
* Menyetujui rapor.
* Memantau PKKM.
* Memantau akreditasi.
* Memantau program kerja.
* Memberikan persetujuan administratif.
ACT-018 Super Admin
Aktivitas utama:
* Mengelola seluruh pengguna.
* Mengelola role.
* Mengelola permission.
* Mengelola konfigurasi sistem.
* Mengatur tahun ajaran aktif.
* Mengatur semester aktif.
* Mengelola integritas sistem.
* Melihat activity log.
* Melihat audit log.
* Menjalankan backup.
* Menjalankan restore.
* Mengakses seluruh modul sesuai kebutuhan administrasi sistem.
Super Admin tidak seharusnya mengubah transaksi operasional tanpa alasan dan jejak audit.
ACT-019 Panitia PPDB
Panitia PPDB bukan harus menjadi role permanen.
Panitia PPDB dapat diterapkan sebagai penugasan pada periode tertentu.
Aktivitas utama:
* Mengelola periode PPDB.
* Mengelola jalur pendaftaran.
* Mengelola kuota.
* Memverifikasi dokumen.
* Mengelola seleksi.
* Mengelola pengumuman.
* Memproses daftar ulang.
* Mengonversi calon siswa menjadi siswa aktif.
ACT-020 Tim PKKM dan Akreditasi
Tim PKKM dan akreditasi dapat diterapkan sebagai role tambahan atau penugasan periodik.
Aktivitas utama:
* Mengelola indikator.
* Mengunggah eviden.
* Memverifikasi eviden.
* Memberikan catatan.
* Memantau kelengkapan.
* Mengelola monitoring.
* Membuat laporan penilaian.

7. Perbedaan Aktor, Role, Jabatan, dan Penugasan
Konsep	Fungsi	Contoh
Aktor	Menggambarkan pihak yang berinteraksi dengan use case	Guru Mata Pelajaran
Role	Mengelompokkan permission dalam sistem	guru_mapel
Jabatan	Menunjukkan posisi dalam organisasi	Wakamad Kurikulum
Penugasan	Menunjukkan tanggung jawab pada periode tertentu	Mengajar Matematika VIII A
Permission	Memberikan izin tindakan tertentu	nilai.input
Policy	Membatasi akses terhadap record tertentu	Guru hanya mengubah nilai kelas yang diajar
Seorang pengguna dapat memiliki:
* Satu akun.
* Beberapa role.
* Satu atau beberapa jabatan.
* Beberapa penugasan.
* Permission yang berasal dari role.
* Pembatasan akses melalui Policy.

8. Tingkatan Use Case Diagram
Karena SIM Madrasah memiliki 68 modul, seluruh fungsi tidak akan ditempatkan dalam satu diagram.
Satu diagram yang berisi seluruh aktor dan seluruh modul akan terlalu padat dan sulit dibaca.
Use Case Diagram dibagi menjadi sembilan diagram.
Diagram 3.1 Use Case Konteks SIM Madrasah
Diagram ini menunjukkan hubungan umum antara aktor dan kelompok layanan sistem.
Kelompok layanan:
1. Mengakses Website Publik.
2. Mengelola PPDB.
3. Mengelola Pengguna dan Hak Akses.
4. Mengelola Data Master.
5. Mengelola Akademik.
6. Mengelola Kesiswaan.
7. Mengelola Keuangan.
8. Mengelola Tata Usaha dan Kepegawaian.
9. Mengelola Sarana dan Prasarana.
10. Mengelola Perpustakaan dan Laboratorium.
11. Mengelola PKKM dan Akreditasi.
12. Mengakses Portal Siswa dan Orang Tua.
13. Mengelola Laporan.
14. Mengelola Audit dan Backup.
Diagram 3.2 Fondasi dan Pengaturan Sistem
Mencakup MOD-001 sampai MOD-006.
Diagram 3.3 Website Publik, Berita, dan PPDB
Mencakup MOD-007 sampai MOD-011.
Diagram 3.4 Data Inti dan Akademik
Mencakup MOD-012 sampai MOD-026.
Diagram 3.5 Kesiswaan dan Portofolio Siswa
Mencakup MOD-027 sampai MOD-036.
Diagram 3.6 Keuangan, Tata Usaha, dan Kepegawaian
Mencakup MOD-037 sampai MOD-046.
Diagram 3.7 Sarana, Inventaris, Perpustakaan, dan Laboratorium
Mencakup MOD-047 sampai MOD-052.
Diagram 3.8 PKKM, Akreditasi, dan Penjaminan Mutu
Mencakup MOD-053 sampai MOD-056.
Diagram 3.9 Portal, Pelaporan, Audit, dan Pemeliharaan
Mencakup MOD-057 sampai MOD-068.

9. DIAGRAM 3.1 USE CASE KONTEKS SIM MADRASAH
9.1 Aktor dan layanan utama
Aktor	Layanan Utama
Pengunjung Umum	Website publik, berita, agenda, galeri, informasi PPDB
Calon Siswa atau Orang Tua Pendaftar	Pendaftaran PPDB
Siswa	Portal siswa dan portofolio
Orang Tua	Portal orang tua
Guru Mata Pelajaran	Akademik dan pembelajaran
Wali Kelas	Akademik, kesiswaan, rapor, dan pemantauan kelas
Guru BK	Konseling dan disiplin siswa
Petugas Perpustakaan	Perpustakaan dan ebook
Petugas Laboratorium	Laboratorium
Editor Berita	CMS berita dan publikasi
Tata Usaha	Data master, surat, arsip, dan administrasi
Bendahara	Tagihan, pembayaran, dan laporan
Wakamad Kurikulum	Akademik
Wakamad Kesiswaan	Kesiswaan
Wakamad Sarpras	Sarana dan inventaris
Wakamad Humas	Website publik, berita, alumni, dan kerja sama
Kepala Madrasah	Monitoring, laporan, dan persetujuan
Super Admin	Pengguna, akses, konfigurasi, log, backup, dan restore
Panitia PPDB	Verifikasi, seleksi, dan registrasi PPDB
Tim PKKM dan Akreditasi	Indikator, eviden, monitoring, dan laporan
9.2 Struktur diagram konteks
Pengunjung Umum
        |
        v
[Website Publik dan Informasi]

Calon Siswa / Orang Tua Pendaftar
        |
        v
[PPDB Online]

Pengguna Terautentikasi
        |
        +----> [Dashboard]
        +----> [Notifikasi]
        +----> [Ubah Password]

Guru dan Wali Kelas
        |
        +----> [Akademik]
        +----> [Kehadiran]
        +----> [Nilai dan Rapor]
        +----> [Kesiswaan]

Tata Usaha dan Bendahara
        |
        +----> [Administrasi]
        +----> [Data Master]
        +----> [Keuangan]

Wakamad dan Kepala Madrasah
        |
        +----> [Monitoring]
        +----> [Persetujuan]
        +----> [Laporan]

Petugas Unit
        |
        +----> [Perpustakaan]
        +----> [Laboratorium]
        +----> [Inventaris]

Super Admin
        |
        +----> [Pengguna]
        +----> [Role dan Permission]
        +----> [Pengaturan]
        +----> [Audit]
        +----> [Backup dan Restore]

10. DIAGRAM 3.2 FONDASI DAN PENGATURAN SISTEM
10.1 Aktor
* Semua Pengguna Terautentikasi.
* Super Admin.
* Tata Usaha.
* Kepala Madrasah.
10.2 Use case
ID	Use Case	Aktor Utama
UC-FND-001	Login	Semua pengguna
UC-FND-002	Logout	Semua pengguna
UC-FND-003	Mengubah Password	Semua pengguna
UC-FND-004	Meminta Reset Password	Semua pengguna
UC-FND-005	Mengelola Pengguna	Super Admin
UC-FND-006	Mengaktifkan atau Menonaktifkan Akun	Super Admin
UC-FND-007	Menghubungkan Akun dengan Profil	Super Admin, Tata Usaha
UC-FND-008	Mengelola Role	Super Admin
UC-FND-009	Mengelola Permission	Super Admin
UC-FND-010	Mengatur Role Pengguna	Super Admin
UC-FND-011	Mengelola Struktur Organisasi	Super Admin, Tata Usaha
UC-FND-012	Mengelola Jabatan	Super Admin, Tata Usaha
UC-FND-013	Mengelola Pengaturan Sistem	Super Admin
UC-FND-014	Melihat Notifikasi	Semua pengguna
UC-FND-015	Menandai Notifikasi Dibaca	Semua pengguna
10.3 Hubungan use case
* Login include Validasi Akun.
* Login include Validasi Password.
* Login include Pemeriksaan Status Aktif.
* Mengelola Pengguna include Menghubungkan Akun dengan Profil.
* Mengatur Role Pengguna include Memilih Role.
* Mengatur Role Pengguna include Validasi Permission.
* Reset Password oleh Admin extend Mengelola Pengguna.
* Menonaktifkan Akun extend Mengelola Pengguna.
10.4 Aturan akses
1. Pengguna nonaktif tidak dapat login.
2. Satu akun hanya boleh terhubung dengan identitas yang sesuai.
3. Satu pengguna dapat memiliki beberapa role.
4. Perubahan role dan permission harus masuk audit log.
5. Super Admin tidak boleh menghapus akunnya sendiri jika menjadi satu-satunya Super Admin.
6. Password disimpan menggunakan hashing.
7. Pengguna tidak dapat melihat menu yang tidak memiliki permission.

11. DIAGRAM 3.3 WEBSITE PUBLIK, BERITA, DAN PPDB
11.1 Aktor
* Pengunjung Umum.
* Calon Siswa atau Orang Tua Pendaftar.
* Guru atau Kontributor Berita.
* Editor Berita.
* Wakamad Humas.
* Kepala Madrasah.
* Panitia PPDB.
* Tata Usaha.
11.2 Website publik
ID	Use Case	Aktor
UC-PUB-001	Melihat Beranda	Pengunjung Umum
UC-PUB-002	Melihat Profil Madrasah	Pengunjung Umum
UC-PUB-003	Melihat Berita	Pengunjung Umum
UC-PUB-004	Melihat Agenda	Pengunjung Umum
UC-PUB-005	Melihat Pengumuman	Pengunjung Umum
UC-PUB-006	Melihat Galeri	Pengunjung Umum
UC-PUB-007	Melihat Informasi PPDB	Pengunjung Umum
UC-PUB-008	Mengelola Halaman Publik	Wakamad Humas
UC-PUB-009	Mengelola Agenda	Wakamad Humas, Tata Usaha
UC-PUB-010	Mengelola Galeri	Editor Berita, Wakamad Humas
11.3 Portal berita
ID	Use Case	Aktor
UC-CMS-001	Membuat Draft Berita	Kontributor, Editor
UC-CMS-002	Mengajukan Berita	Kontributor
UC-CMS-003	Mereview Berita	Editor
UC-CMS-004	Meminta Revisi	Editor
UC-CMS-005	Menyetujui Berita	Editor, Kepala Madrasah
UC-CMS-006	Menjadwalkan Publikasi	Editor
UC-CMS-007	Menerbitkan Berita	Editor
UC-CMS-008	Mengarsipkan Berita	Editor
UC-CMS-009	Mengelola Kategori dan Tag	Editor
UC-CMS-010	Mengelola SEO	Editor
11.4 Alur berita
Membuat Draft
      |
      v
Mengajukan Berita
      |
      v
Review Editor
      |
      +----> Memerlukan Revisi ----> Diperbaiki ----> Diajukan Kembali
      |
      v
Persetujuan Kepala Madrasah, jika diwajibkan
      |
      v
Dijadwalkan atau Langsung Dipublikasikan
      |
      v
Diarsipkan
11.5 PPDB
ID	Use Case	Aktor
UC-PPDB-001	Membuat Akun Pendaftar	Calon Siswa atau Orang Tua
UC-PPDB-002	Mengisi Formulir	Calon Siswa atau Orang Tua
UC-PPDB-003	Mengunggah Dokumen	Calon Siswa atau Orang Tua
UC-PPDB-004	Mengirim Pendaftaran	Calon Siswa atau Orang Tua
UC-PPDB-005	Mencetak Bukti Pendaftaran	Calon Siswa atau Orang Tua
UC-PPDB-006	Memverifikasi Administrasi	Panitia PPDB
UC-PPDB-007	Mengelola Seleksi	Panitia PPDB
UC-PPDB-008	Menetapkan Hasil	Panitia, Kepala Madrasah
UC-PPDB-009	Melihat Pengumuman	Pendaftar
UC-PPDB-010	Melakukan Daftar Ulang	Pendaftar
UC-PPDB-011	Mengonversi Data menjadi Siswa	Panitia, Tata Usaha
11.6 Hubungan use case PPDB
* Mengirim Pendaftaran include Validasi Formulir.
* Mengirim Pendaftaran include Validasi Dokumen Wajib.
* Memverifikasi Administrasi include Pemeriksaan Dokumen.
* Menetapkan Hasil include Pemeriksaan Hasil Seleksi.
* Mengonversi Data menjadi Siswa include Membuat Data Siswa.
* Mengonversi Data menjadi Siswa include Membuat Relasi Orang Tua.
* Mengonversi Data menjadi Siswa include Membuat Riwayat Status.
* Mengonversi Data menjadi Siswa include Memindahkan Dokumen.

12. DIAGRAM 3.4 DATA INTI DAN AKADEMIK
12.1 Aktor
* Super Admin.
* Tata Usaha.
* Wakamad Kurikulum.
* Kepala Madrasah.
* Guru Mata Pelajaran.
* Wali Kelas.
* Siswa.
* Orang Tua.
12.2 Data inti
ID	Use Case	Aktor
UC-AKD-001	Mengelola Tahun Ajaran	Wakamad Kurikulum, Super Admin
UC-AKD-002	Mengelola Semester	Wakamad Kurikulum, Super Admin
UC-AKD-003	Mengaktifkan Periode	Wakamad Kurikulum, Super Admin
UC-AKD-004	Mengunci Periode Lama	Wakamad Kurikulum
UC-AKD-005	Mengelola Data Guru	Tata Usaha
UC-AKD-006	Mengelola Data Siswa	Tata Usaha
UC-AKD-007	Mengelola Data Orang Tua	Tata Usaha
UC-AKD-008	Mengelola Mata Pelajaran	Wakamad Kurikulum
UC-AKD-009	Mengelola Kelas dan Rombel	Wakamad Kurikulum
UC-AKD-010	Menempatkan Siswa ke Kelas	Wakamad Kurikulum, Tata Usaha
UC-AKD-011	Memproses Kenaikan Kelas	Wakamad Kurikulum, Wali Kelas
UC-AKD-012	Memproses Kelulusan	Wakamad Kurikulum, Kepala Madrasah
12.3 Pengelolaan akademik
ID	Use Case	Aktor
UC-AKD-013	Mengelola Kurikulum	Wakamad Kurikulum
UC-AKD-014	Mengelola Kalender Pendidikan	Wakamad Kurikulum
UC-AKD-015	Membuat Penugasan Mengajar	Wakamad Kurikulum
UC-AKD-016	Membuat Jadwal Pelajaran	Wakamad Kurikulum
UC-AKD-017	Memeriksa Bentrok Jadwal	Wakamad Kurikulum
UC-AKD-018	Mengunggah ATP	Guru
UC-AKD-019	Mengunggah Modul Ajar	Guru
UC-AKD-020	Mereview Perangkat Pembelajaran	Wakamad Kurikulum
UC-AKD-021	Mengisi Jurnal Mengajar	Guru
UC-AKD-022	Memantau Jurnal Mengajar	Wakamad Kurikulum
UC-AKD-023	Memasukkan Nilai	Guru
UC-AKD-024	Mengimpor Nilai	Guru
UC-AKD-025	Memverifikasi Nilai	Wakamad Kurikulum
UC-AKD-026	Menerbitkan Nilai	Wakamad Kurikulum
UC-AKD-027	Menyusun Rapor	Wali Kelas
UC-AKD-028	Memverifikasi Rapor	Wakamad Kurikulum
UC-AKD-029	Menyetujui Rapor	Kepala Madrasah
UC-AKD-030	Menerbitkan Rapor	Wali Kelas, Wakamad Kurikulum
UC-AKD-031	Mencetak Rapor	Wali Kelas
UC-AKD-032	Melihat Nilai	Siswa, Orang Tua
UC-AKD-033	Melihat Rapor	Siswa, Orang Tua
12.4 Hubungan use case
* Mengaktifkan Periode include Menonaktifkan Periode Sebelumnya.
* Menempatkan Siswa ke Kelas include Validasi Status Siswa.
* Menempatkan Siswa ke Kelas include Validasi Tahun Ajaran.
* Memproses Kenaikan Kelas include Membuat Penempatan Baru.
* Memproses Kenaikan Kelas tidak mengubah penempatan lama.
* Membuat Jadwal include Memeriksa Bentrok Guru.
* Membuat Jadwal include Memeriksa Bentrok Kelas.
* Membuat Jadwal include Memeriksa Bentrok Ruangan.
* Memasukkan Nilai include Validasi Penugasan Mengajar.
* Memverifikasi Nilai include Memeriksa Kelengkapan Nilai.
* Menyusun Rapor include Mengambil Nilai Terverifikasi.
* Menyusun Rapor include Mengambil Rekap Kehadiran.
* Menyusun Rapor include Mengambil Ekstrakurikuler.
* Menyusun Rapor include Mengambil Prestasi.
* Menerbitkan Rapor include Membuat Arsip PDF.
* Menerbitkan Rapor include Mengunci Snapshot Rapor.
12.5 Aturan akses akademik
1. Guru hanya dapat mengelola kelas yang menjadi penugasannya.
2. Guru tidak dapat menerbitkan nilai sendiri jika proses verifikasi diwajibkan.
3. Siswa dan orang tua hanya melihat nilai yang berstatus dipublikasikan.
4. Data tahun ajaran lama tidak boleh dihapus.
5. Rapor yang sudah diterbitkan harus memiliki arsip tetap.
6. Koreksi rapor harus mencatat alasan dan pengguna yang melakukan perubahan.
7. Perubahan kelas dilakukan melalui riwayat penempatan baru.

13. DIAGRAM 3.5 KESISWAAN DAN PORTOFOLIO SISWA
13.1 Aktor
* Siswa.
* Orang Tua.
* Wali Kelas.
* Guru.
* Guru BK.
* Wakamad Kesiswaan.
* Kepala Madrasah.
* Tata Usaha.
13.2 Use case
ID	Use Case	Aktor
UC-KSW-001	Melihat Portofolio Siswa	Siswa, Orang Tua, Wali Kelas
UC-KSW-002	Membuat QR Code Siswa	Tata Usaha, Super Admin
UC-KSW-003	Memindai QR Code	Pengguna yang diizinkan
UC-KSW-004	Mengatur Akses QR Code	Super Admin, Tata Usaha
UC-KSW-005	Mencatat Kehadiran Siswa	Guru, Wali Kelas
UC-KSW-006	Mengoreksi Kehadiran	Wali Kelas, Wakamad Kesiswaan
UC-KSW-007	Mengelola Prestasi	Wakamad Kesiswaan, Wali Kelas
UC-KSW-008	Memverifikasi Prestasi	Wakamad Kesiswaan
UC-KSW-009	Mencatat Pelanggaran	Guru, Wali Kelas, Guru BK
UC-KSW-010	Menindaklanjuti Pelanggaran	Guru BK, Wakamad Kesiswaan
UC-KSW-011	Mencatat Konseling	Guru BK
UC-KSW-012	Mengatur Kerahasiaan Konseling	Guru BK
UC-KSW-013	Mengelola Ekstrakurikuler	Wakamad Kesiswaan
UC-KSW-014	Mengelola Anggota Ekstrakurikuler	Pembina
UC-KSW-015	Mencatat Tahfidz	Guru Tahfidz
UC-KSW-016	Mencatat Pembiasaan	Guru, Wali Kelas
UC-KSW-017	Mengelola Organisasi Siswa	Wakamad Kesiswaan
UC-KSW-018	Melihat Perkembangan Anak	Orang Tua
13.3 Portofolio digital
Portofolio digital tidak menyimpan salinan data.
Use case “Melihat Portofolio Siswa” mengambil data dari:
* Data siswa.
* Riwayat kelas.
* Kehadiran.
* Nilai.
* Rapor.
* Prestasi.
* Pelanggaran.
* Konseling yang diizinkan.
* Ekstrakurikuler.
* Tahfidz.
* Pembiasaan.
* Pembayaran.
* Dokumen.
* Sertifikat.
* Karya siswa.
13.4 Hubungan use case
* Memindai QR Code include Memvalidasi Token.
* Memindai QR Code include Memeriksa Status QR.
* Memindai QR Code include Memeriksa Hak Akses.
* Menampilkan Profil Publik extend Memindai QR Code.
* Meminta Login extend Memindai QR Code.
* Mencatat Pelanggaran include Memilih Kategori Pelanggaran.
* Menindaklanjuti Pelanggaran include Mencatat Tindakan.
* Mencatat Konseling include Menentukan Tingkat Kerahasiaan.
* Melihat Portofolio include Memeriksa Hak Akses Data.
13.5 Aturan keamanan
1. QR Code tidak langsung membuka seluruh data siswa.
2. Catatan konseling rahasia tidak boleh muncul pada portofolio umum.
3. Orang tua hanya melihat anak yang terhubung dengan akunnya.
4. Siswa hanya melihat data miliknya.
5. Wali kelas hanya melihat siswa yang menjadi tanggung jawabnya.
6. Perubahan kehadiran harus dicatat dalam audit log.
7. Prestasi harus diverifikasi sebelum dipublikasikan.

14. DIAGRAM 3.6 KEUANGAN, TATA USAHA, DAN KEPEGAWAIAN
14.1 Aktor
* Bendahara.
* Tata Usaha.
* Kepala Madrasah.
* Wali Kelas.
* Orang Tua.
* Siswa.
* Wakamad.
* Super Admin.
14.2 Keuangan siswa
ID	Use Case	Aktor
UC-KEU-001	Mengelola Jenis Tagihan	Bendahara
UC-KEU-002	Membuat Tagihan Siswa	Bendahara
UC-KEU-003	Mencatat Pembayaran	Bendahara
UC-KEU-004	Mencatat Cicilan	Bendahara
UC-KEU-005	Memberikan Potongan	Bendahara, Kepala Madrasah
UC-KEU-006	Memberikan Pembebasan	Bendahara, Kepala Madrasah
UC-KEU-007	Membatalkan Transaksi	Bendahara dengan izin
UC-KEU-008	Mengoreksi Transaksi	Bendahara dengan izin
UC-KEU-009	Mencetak Kwitansi	Bendahara
UC-KEU-010	Melihat Dashboard Pembayaran	Bendahara, Kepala Madrasah
UC-KEU-011	Melihat Tunggakan Kelas	Wali Kelas
UC-KEU-012	Melihat Tagihan	Siswa, Orang Tua
UC-KEU-013	Melihat Riwayat Pembayaran	Siswa, Orang Tua
UC-KEU-014	Membuat Laporan Keuangan Siswa	Bendahara
14.3 Hubungan use case pembayaran
* Mencatat Pembayaran include Memilih Tagihan.
* Mencatat Pembayaran include Memvalidasi Nominal.
* Mencatat Pembayaran include Menghitung Sisa Tagihan.
* Mencatat Pembayaran include Menghitung Status Pembayaran.
* Mencatat Pembayaran include Membuat Nomor Transaksi.
* Mencetak Kwitansi extend Mencatat Pembayaran.
* Membatalkan Transaksi include Mengisi Alasan.
* Mengoreksi Transaksi include Mengisi Alasan.
* Mengoreksi Transaksi include Mencatat Nilai Lama dan Nilai Baru.
Status pembayaran dihitung oleh sistem:
* Belum bayar.
* Cicilan.
* Lunas.
* Dibebaskan.
* Dibatalkan.
14.4 Tata usaha
ID	Use Case	Aktor
UC-TU-001	Mengelola Surat Masuk	Tata Usaha
UC-TU-002	Membuat Disposisi	Kepala Madrasah, Tata Usaha
UC-TU-003	Mengelola Surat Keluar	Tata Usaha
UC-TU-004	Mengajukan Persetujuan Surat	Tata Usaha
UC-TU-005	Menyetujui Surat	Kepala Madrasah
UC-TU-006	Mengelola Arsip Digital	Tata Usaha
UC-TU-007	Mengelola Surat Keputusan	Tata Usaha
UC-TU-008	Mengelola MOU	Tata Usaha, Wakamad Humas
UC-TU-009	Mencari Dokumen	Pengguna yang diizinkan
UC-TU-010	Mencetak Surat	Tata Usaha
14.5 Kepegawaian
ID	Use Case	Aktor
UC-PEG-001	Mengelola Data Kepegawaian	Tata Usaha
UC-PEG-002	Mengelola Riwayat Pendidikan	Tata Usaha
UC-PEG-003	Mengelola Riwayat Jabatan	Tata Usaha
UC-PEG-004	Mengelola Sertifikasi	Tata Usaha
UC-PEG-005	Mengelola Kehadiran Pegawai	Tata Usaha
UC-PEG-006	Mengoreksi Kehadiran Pegawai	Tata Usaha dengan izin
UC-PEG-007	Mengelola Workshop dan Pelatihan	Tata Usaha, Pegawai
UC-PEG-008	Mengunggah Sertifikat	Guru, Pegawai
UC-PEG-009	Melihat Laporan Kepegawaian	Kepala Madrasah
15. DIAGRAM 3.7 SARANA, INVENTARIS, PERPUSTAKAAN, DAN LABORATORIUM
15.1 Aktor
* Wakamad Sarpras.
* Petugas Laboratorium.
* Petugas Perpustakaan.
* Guru.
* Siswa.
* Kepala Madrasah.
* Tata Usaha.
15.2 Sarana dan inventaris
ID	Use Case	Aktor
UC-SRP-001	Mengelola Ruangan	Wakamad Sarpras
UC-SRP-002	Mengelola Data Barang	Wakamad Sarpras
UC-SRP-003	Membuat QR Code Barang	Wakamad Sarpras
UC-SRP-004	Mencatat Mutasi Barang	Wakamad Sarpras
UC-SRP-005	Mencatat Peminjaman Barang	Wakamad Sarpras
UC-SRP-006	Mencatat Pengembalian Barang	Wakamad Sarpras
UC-SRP-007	Mencatat Kerusakan	Wakamad Sarpras
UC-SRP-008	Mencatat Pemeliharaan	Wakamad Sarpras
UC-SRP-009	Mengusulkan Penghapusan	Wakamad Sarpras
UC-SRP-010	Melihat Laporan Inventaris	Kepala Madrasah
15.3 Laboratorium
ID	Use Case	Aktor
UC-LAB-001	Mengelola Data Laboratorium	Petugas Laboratorium
UC-LAB-002	Mengelola Alat	Petugas Laboratorium
UC-LAB-003	Mengelola Bahan	Petugas Laboratorium
UC-LAB-004	Mengelola Stok	Petugas Laboratorium
UC-LAB-005	Mengelola Jadwal Penggunaan	Petugas Laboratorium
UC-LAB-006	Mencatat Peminjaman Alat	Petugas Laboratorium
UC-LAB-007	Mencatat Pengembalian Alat	Petugas Laboratorium
UC-LAB-008	Mencatat Kerusakan	Petugas Laboratorium
UC-LAB-009	Membuat Laporan Laboratorium	Petugas Laboratorium
15.4 Perpustakaan
ID	Use Case	Aktor
UC-PUS-001	Mengelola Koleksi Buku	Petugas Perpustakaan
UC-PUS-002	Mengelola Anggota	Petugas Perpustakaan
UC-PUS-003	Mencatat Peminjaman	Petugas Perpustakaan
UC-PUS-004	Mencatat Pengembalian	Petugas Perpustakaan
UC-PUS-005	Memperpanjang Peminjaman	Petugas Perpustakaan
UC-PUS-006	Menghitung Keterlambatan	Sistem
UC-PUS-007	Mengelola Denda	Petugas Perpustakaan
UC-PUS-008	Mengelola Ebook	Petugas Perpustakaan
UC-PUS-009	Mengakses Ebook	Siswa, Guru
UC-PUS-010	Membuat Laporan Perpustakaan	Petugas Perpustakaan
15.5 Hubungan use case perpustakaan
* Mencatat Peminjaman include Memvalidasi Anggota.
* Mencatat Peminjaman include Memeriksa Ketersediaan Buku.
* Mencatat Pengembalian include Menghitung Keterlambatan.
* Mengelola Denda extend Mencatat Pengembalian.
* Memperpanjang Peminjaman include Memeriksa Status Buku.
* Mengakses Ebook include Memeriksa Hak Akses.

16. DIAGRAM 3.8 PKKM, AKREDITASI, DAN PENJAMINAN MUTU
16.1 Aktor
* Kepala Madrasah.
* Tim PKKM.
* Tim Akreditasi.
* Wakamad.
* Tata Usaha.
* Penanggung Jawab Indikator.
* Super Admin.
16.2 Use case PKKM
ID	Use Case	Aktor
UC-PKKM-001	Mengelola Periode PKKM	Tim PKKM
UC-PKKM-002	Mengelola Komponen	Tim PKKM
UC-PKKM-003	Mengelola Indikator	Tim PKKM
UC-PKKM-004	Menentukan Penanggung Jawab	Kepala Madrasah, Tim PKKM
UC-PKKM-005	Mengunggah Eviden	Penanggung Jawab
UC-PKKM-006	Memverifikasi Eviden	Tim PKKM
UC-PKKM-007	Meminta Perbaikan Eviden	Tim PKKM
UC-PKKM-008	Melihat Persentase Kelengkapan	Kepala Madrasah
UC-PKKM-009	Membuat Laporan PKKM	Tim PKKM
16.3 Use case akreditasi
ID	Use Case	Aktor
UC-AKR-001	Mengelola Periode Akreditasi	Tim Akreditasi
UC-AKR-002	Mengelola Standar	Tim Akreditasi
UC-AKR-003	Mengelola Komponen dan Indikator	Tim Akreditasi
UC-AKR-004	Menentukan Penanggung Jawab	Kepala Madrasah
UC-AKR-005	Mengunggah Eviden	Penanggung Jawab
UC-AKR-006	Memverifikasi Eviden	Tim Akreditasi
UC-AKR-007	Memberikan Catatan Evaluasi	Tim Akreditasi
UC-AKR-008	Memberikan Skor Internal	Tim Akreditasi
UC-AKR-009	Melihat Kelengkapan	Kepala Madrasah
UC-AKR-010	Membuat Laporan Akreditasi	Tim Akreditasi
16.4 Monitoring dan rencana kerja
ID	Use Case	Aktor
UC-MON-001	Mengelola Program Kerja	Kepala Madrasah, Wakamad
UC-MON-002	Menentukan Indikator Kinerja	Kepala Madrasah
UC-MON-003	Menentukan Target	Kepala Madrasah, Wakamad
UC-MON-004	Mencatat Realisasi	Penanggung Jawab
UC-MON-005	Mencatat Kendala	Penanggung Jawab
UC-MON-006	Mencatat Tindak Lanjut	Penanggung Jawab
UC-MON-007	Melakukan Evaluasi	Kepala Madrasah
UC-MON-008	Melihat Persentase Capaian	Kepala Madrasah
16.5 Hubungan use case
* Mengunggah Eviden include Memilih Indikator.
* Mengunggah Eviden include Memilih Jenis Eviden.
* Mengunggah Eviden include Validasi File.
* Memverifikasi Eviden include Memeriksa Kelengkapan.
* Meminta Perbaikan extend Memverifikasi Eviden.
* Menghitung Persentase include Membaca Status Setiap Indikator.
* Membuat Laporan include Mengambil Eviden Terverifikasi.

17. DIAGRAM 3.9 PORTAL, PELAPORAN, AUDIT, DAN PEMELIHARAAN
17.1 Aktor
* Seluruh Pengguna Terautentikasi.
* Siswa.
* Orang Tua.
* Kepala Madrasah.
* Wakamad.
* Bendahara.
* Tata Usaha.
* Super Admin.
* Auditor internal yang diberi izin.
17.2 Dashboard dan portal
ID	Use Case	Aktor
UC-PRT-001	Melihat Dashboard Sesuai Role	Semua pengguna
UC-PRT-002	Melihat Portal Siswa	Siswa
UC-PRT-003	Melihat Portal Orang Tua	Orang Tua
UC-PRT-004	Memilih Anak	Orang Tua
UC-PRT-005	Melihat Portal Alumni	Alumni
UC-PRT-006	Melihat Pusat Dokumen	Pengguna yang diizinkan
UC-PRT-007	Mencari Dokumen	Pengguna yang diizinkan
UC-PRT-008	Mengunduh Dokumen	Pengguna yang diizinkan
17.3 Laporan
ID	Use Case	Aktor
UC-LAP-001	Memilih Jenis Laporan	Pengguna yang diizinkan
UC-LAP-002	Memfilter Laporan	Pengguna yang diizinkan
UC-LAP-003	Melihat Laporan	Pengguna yang diizinkan
UC-LAP-004	Mengekspor Excel	Pengguna yang diizinkan
UC-LAP-005	Mencetak PDF	Pengguna yang diizinkan
UC-LAP-006	Melihat Rekap Pimpinan	Kepala Madrasah
UC-LAP-007	Melihat Rekap Periode	Wakamad, Kepala Madrasah
17.4 Import dan export
ID	Use Case	Aktor
UC-IMP-001	Mengunduh Template	Pengguna yang diizinkan
UC-IMP-002	Mengunggah File Import	Pengguna yang diizinkan
UC-IMP-003	Melihat Preview	Pengguna yang diizinkan
UC-IMP-004	Memvalidasi Data	Sistem
UC-IMP-005	Menjalankan Import	Pengguna yang diizinkan
UC-IMP-006	Melihat Data Gagal	Pengguna yang diizinkan
UC-IMP-007	Mengunduh Laporan Kesalahan	Pengguna yang diizinkan
UC-IMP-008	Melihat Riwayat Import	Super Admin
17.5 Audit dan pemeliharaan
ID	Use Case	Aktor
UC-AUD-001	Melihat Activity Log	Super Admin
UC-AUD-002	Melihat Audit Log	Super Admin, Auditor
UC-AUD-003	Memfilter Audit Log	Super Admin, Auditor
UC-AUD-004	Membuat Backup	Super Admin
UC-AUD-005	Mengunduh Backup	Super Admin
UC-AUD-006	Menghapus Backup Lama	Super Admin
UC-AUD-007	Menjalankan Restore	Super Admin Terbatas
UC-AUD-008	Mengelola File	Super Admin
UC-AUD-009	Mengakses Panduan	Semua pengguna
UC-AUD-010	Melaporkan Masalah	Semua pengguna
17.6 Aturan penting
1. Dashboard hanya menampilkan data sesuai role.
2. Dashboard tidak menyimpan salinan data transaksi.
3. Portal orang tua memeriksa relasi orang tua dan siswa.
4. Portal siswa memeriksa identitas siswa yang login.
5. Import selalu melalui preview dan validasi.
6. Restore hanya dapat dilakukan oleh pengguna terbatas.
7. Restore harus dicatat dalam log.
8. File privat hanya dapat diunduh setelah permission diperiksa.
9. Audit log tidak boleh dapat diubah oleh pengguna biasa.

18. JENIS HUBUNGAN DALAM USE CASE DIAGRAM
18.1 Association
Association adalah garis yang menghubungkan aktor dengan use case.
Contoh:
Guru Mata Pelajaran -------- Memasukkan Nilai
Artinya, Guru Mata Pelajaran berinteraksi dengan fungsi Memasukkan Nilai.
18.2 Include
include digunakan jika satu proses selalu membutuhkan proses lain.
Contoh:
Mencatat Pembayaran
        |
        | <<include>>
        v
Menghitung Status Pembayaran
Setiap pembayaran harus menghitung status tagihan.
18.3 Extend
extend digunakan untuk proses tambahan yang hanya terjadi pada kondisi tertentu.
Contoh:
Mencatat Pengembalian Buku
        ^
        | <<extend>>
Menghitung Denda
Denda hanya dihitung jika buku terlambat dan madrasah menerapkan denda.
18.4 Generalization
Generalization digunakan jika satu aktor merupakan bentuk khusus dari aktor lain.
Contoh:
Pengguna Terautentikasi
        ^
        |
   -------------
   |           |
 Guru       Siswa
Guru dan siswa sama-sama merupakan pengguna terautentikasi, tetapi memiliki hak akses berbeda.

19. SPESIFIKASI USE CASE UTAMA
UC-001 Login
Aktor utama: Semua pengguna.
Tujuan: Masuk ke sistem menggunakan akun yang valid.
Prasyarat:
* Pengguna memiliki akun.
* Akun berstatus aktif.
* Sistem dapat mengakses database.
Alur utama:
1. Pengguna membuka halaman login.
2. Pengguna memasukkan email atau username.
3. Pengguna memasukkan password.
4. Sistem memvalidasi data.
5. Sistem memeriksa status akun.
6. Sistem membuat sesi login.
7. Sistem mencatat waktu login.
8. Sistem menampilkan dashboard sesuai role.
Alur alternatif:
* Jika password salah, sistem menampilkan pesan kesalahan.
* Jika akun nonaktif, sistem menolak login.
* Jika akun tidak memiliki role, sistem menampilkan pesan untuk menghubungi administrator.
* Jika terlalu banyak percobaan, sistem membatasi login sementara.
Hasil akhir:
Pengguna berhasil masuk dan hanya melihat menu sesuai kewenangannya.

UC-002 Mengatur Role dan Permission
Aktor utama: Super Admin.
Tujuan: Memberikan hak akses kepada pengguna.
Prasyarat:
* Super Admin sudah login.
* Role dan permission tersedia.
Alur utama:
1. Super Admin membuka data pengguna.
2. Super Admin memilih pengguna.
3. Sistem menampilkan role yang tersedia.
4. Super Admin memilih satu atau beberapa role.
5. Sistem menampilkan permission yang berasal dari role.
6. Super Admin menyimpan perubahan.
7. Sistem mencatat perubahan dalam audit log.
Alur alternatif:
* Sistem menolak jika perubahan menyebabkan tidak ada Super Admin aktif.
* Sistem menolak permission yang tidak valid.
* Sistem meminta konfirmasi untuk permission berisiko tinggi.
Hasil akhir:
Pengguna memperoleh akses sesuai role dan permission.

UC-003 Mengaktifkan Tahun Ajaran dan Semester
Aktor utama: Wakamad Kurikulum atau Super Admin.
Tujuan: Menetapkan periode aktif yang digunakan sistem.
Prasyarat:
* Tahun ajaran sudah dibuat.
* Semester sudah dibuat.
Alur utama:
1. Pengguna memilih tahun ajaran.
2. Pengguna memilih semester.
3. Sistem memeriksa periode aktif sebelumnya.
4. Pengguna memberikan konfirmasi.
5. Sistem menonaktifkan periode sebelumnya.
6. Sistem mengaktifkan periode baru.
7. Sistem mencatat perubahan.
Hasil akhir:
Seluruh transaksi baru menggunakan periode aktif.

UC-004 Menempatkan Siswa ke Kelas
Aktor utama: Wakamad Kurikulum atau Tata Usaha.
Tujuan: Mencatat kelas siswa pada periode tertentu.
Prasyarat:
* Siswa berstatus aktif.
* Kelas tersedia.
* Tahun ajaran dan semester tersedia.
Alur utama:
1. Pengguna memilih periode.
2. Pengguna memilih kelas.
3. Pengguna memilih siswa.
4. Sistem memeriksa penempatan sebelumnya.
5. Sistem memeriksa kemungkinan duplikasi.
6. Pengguna menyimpan penempatan.
7. Sistem membuat record riwayat baru.
Aturan penting:
Record lama tidak diubah atau dihapus.
Hasil akhir:
Riwayat kelas siswa bertambah tanpa menghilangkan histori sebelumnya.

UC-005 Membuat Penugasan Mengajar
Aktor utama: Wakamad Kurikulum.
Tujuan: Menentukan guru yang mengajar mata pelajaran dan kelas tertentu.
Prasyarat:
* Guru aktif.
* Mata pelajaran tersedia.
* Kelas tersedia.
* Periode tersedia.
Alur utama:
1. Pengguna memilih guru.
2. Pengguna memilih mata pelajaran.
3. Pengguna memilih kelas.
4. Pengguna menentukan jumlah jam.
5. Sistem memeriksa duplikasi penugasan.
6. Sistem menyimpan penugasan.
Hasil akhir:
Guru memperoleh akses untuk mengelola jurnal, absensi, dan nilai pada penugasan tersebut.

UC-006 Memasukkan dan Menerbitkan Nilai
Aktor utama: Guru Mata Pelajaran, Wakamad Kurikulum.
Tujuan: Mencatat nilai dan menerbitkannya kepada siswa serta orang tua.
Prasyarat:
* Guru memiliki penugasan.
* Periode akademik masih dibuka.
* Siswa terdaftar pada kelas.
Alur utama:
1. Guru memilih penugasan mengajar.
2. Guru memilih jenis penilaian.
3. Sistem menampilkan daftar siswa.
4. Guru memasukkan nilai.
5. Sistem memvalidasi rentang nilai.
6. Guru menyimpan sebagai draft.
7. Guru mengajukan verifikasi.
8. Wakamad Kurikulum memeriksa kelengkapan.
9. Nilai diverifikasi.
10. Nilai dipublikasikan.
Alur alternatif:
* Jika nilai belum lengkap, status dikembalikan untuk diperbaiki.
* Jika guru tidak memiliki penugasan, sistem menolak akses.
* Jika periode terkunci, perubahan memerlukan permission khusus.
Hasil akhir:
Nilai yang dipublikasikan dapat dilihat oleh siswa dan orang tua.

UC-007 Menerbitkan Rapor
Aktor utama: Wali Kelas, Wakamad Kurikulum, Kepala Madrasah.
Tujuan: Menghasilkan rapor resmi siswa.
Prasyarat:
* Nilai telah diverifikasi.
* Rekap kehadiran tersedia.
* Data ekstrakurikuler tersedia.
* Catatan wali kelas tersedia.
Alur utama:
1. Wali kelas memilih siswa.
2. Sistem mengambil seluruh komponen rapor.
3. Wali kelas memeriksa data.
4. Wali kelas mengisi catatan.
5. Rapor diajukan untuk verifikasi.
6. Wakamad Kurikulum memverifikasi.
7. Kepala Madrasah menyetujui jika diwajibkan.
8. Sistem membuat PDF.
9. Sistem menyimpan arsip rapor.
10. Rapor dipublikasikan.
Hasil akhir:
Rapor tersedia pada portal siswa dan orang tua.

UC-008 Mencatat Pembayaran Siswa
Aktor utama: Bendahara.
Tujuan: Mencatat pembayaran penuh atau cicilan.
Prasyarat:
* Siswa memiliki tagihan.
* Tagihan belum dibatalkan.
* Bendahara sudah login.
Alur utama:
1. Bendahara mencari siswa.
2. Sistem menampilkan tagihan.
3. Bendahara memilih tagihan.
4. Bendahara memasukkan nominal.
5. Sistem menghitung sisa tagihan.
6. Sistem menentukan status pembayaran.
7. Sistem membuat nomor transaksi.
8. Sistem menyimpan pembayaran.
9. Sistem menawarkan pencetakan kwitansi.
10. Sistem mencatat aktivitas.
Hasil akhir:
Riwayat pembayaran bertambah dan status tagihan diperbarui melalui perhitungan.

UC-009 Mengonversi Pendaftar Menjadi Siswa
Aktor utama: Panitia PPDB atau Tata Usaha.
Tujuan: Mengubah calon siswa yang diterima menjadi siswa aktif tanpa mengetik ulang.
Prasyarat:
* Pendaftar dinyatakan diterima.
* Daftar ulang telah diverifikasi.
* Data wajib lengkap.
Alur utama:
1. Pengguna memilih pendaftar.
2. Sistem memvalidasi status kelulusan.
3. Sistem memeriksa NISN dan data identitas.
4. Sistem membuat data siswa.
5. Sistem membuat hubungan orang tua.
6. Sistem memindahkan referensi dokumen.
7. Sistem membuat riwayat status siswa.
8. Sistem menandai pendaftar telah dikonversi.
Hasil akhir:
Data calon siswa menjadi data siswa aktif tanpa duplikasi input.

UC-010 Mengunggah dan Memverifikasi Eviden PKKM
Aktor utama: Penanggung Jawab Indikator dan Tim PKKM.
Tujuan: Menyimpan eviden sesuai indikator PKKM.
Prasyarat:
* Periode PKKM tersedia.
* Indikator tersedia.
* Penanggung jawab telah ditentukan.
Alur utama:
1. Penanggung jawab memilih indikator.
2. Penanggung jawab memilih jenis eviden.
3. Penanggung jawab mengunggah file atau tautan.
4. Sistem memvalidasi file.
5. Eviden disimpan dengan status menunggu verifikasi.
6. Tim PKKM memeriksa eviden.
7. Tim menerima atau meminta perbaikan.
8. Sistem menghitung ulang persentase kelengkapan.
Hasil akhir:
Status dan histori eviden tercatat berdasarkan indikator.

UC-011 Melihat Data Anak pada Portal Orang Tua
Aktor utama: Orang Tua atau Wali.
Tujuan: Melihat perkembangan anak yang terhubung dengan akun.
Prasyarat:
* Orang tua sudah login.
* Relasi orang tua dan siswa tersedia.
* Siswa berstatus dapat diakses.
Alur utama:
1. Orang tua membuka portal.
2. Sistem mengambil daftar anak yang terhubung.
3. Orang tua memilih anak.
4. Sistem memvalidasi relasi.
5. Sistem menampilkan informasi yang diizinkan.
Informasi yang dapat ditampilkan:
* Kehadiran.
* Nilai terpublikasi.
* Rapor.
* Prestasi.
* Tahfidz.
* Tagihan.
* Riwayat pembayaran.
* Pengumuman.
Hasil akhir:
Orang tua melihat data anak tanpa dapat mengakses siswa lain.

UC-012 Mengakses Profil melalui QR Code
Aktor utama: Pengguna yang memindai QR Code.
Tujuan: Mengakses informasi siswa sesuai tingkat izin.
Prasyarat:
* QR Code aktif.
* Token valid.
* Siswa tersedia.
Alur utama:
1. Pengguna memindai QR Code.
2. Sistem membaca token.
3. Sistem memvalidasi token.
4. Sistem memeriksa status QR Code.
5. Sistem menentukan jenis akses.
6. Sistem menampilkan informasi publik atau meminta login.
7. Setelah login, sistem memeriksa permission dan relasi.
Hasil akhir:
Data siswa ditampilkan sesuai hak akses tanpa membuka informasi sensitif secara bebas.

20. MATRIKS AKSES UTAMA
Keterangan:
* K berarti kelola.
* L berarti lihat.
* V berarti verifikasi.
* A berarti menyetujui.
* T berarti tidak memiliki akses utama.
Modul	Super Admin	Kepala	Wakamad	TU	Bendahara	Guru	Wali Kelas	BK	Orang Tua	Siswa
Pengguna	K	L	T	K terbatas	T	T	T	T	T	T
Role dan Permission	K	L	T	T	T	T	T	T	T	T
Data Siswa	K	L	K	K	L	L terbatas	L	L terbatas	L anak	L diri
Akademik	K	L	K/V	L	T	K	K/V	L terbatas	L	L
Nilai	K terbatas	L	V	T	T	K	L/K terbatas	L terbatas	L publikasi	L publikasi
Rapor	K terbatas	A/L	V	T	T	K komponen	K	L terbatas	L publikasi	L publikasi
Kesiswaan	K	L	K	L	T	K terbatas	K	K	L terbatas	L diri
Pembayaran	K terbatas	L	L	T	K	T	L	T	L anak	L diri
Surat dan Arsip	K	A/L	L	K	T	T	T	T	T	T
Inventaris	K	L	K	L	T	L terbatas	T	T	T	T
Perpustakaan	K	L	L	T	T	L	L	T	T	L
PKKM dan Akreditasi	K	L/A	K/V	K terbatas	T	K jika ditugaskan	K jika ditugaskan	K jika ditugaskan	T	T
Audit Log	K/L	L terbatas	T	T	T	T	T	T	T	T
Backup dan Restore	K	T	T	T	T	T	T	T	T	T
Matriks ini masih berupa matriks konseptual.
Matriks permission rinci akan dibuat saat memasuki Tahap 10, yaitu Role dan Permission.

21. ATURAN UMUM USE CASE
21.1 Pembatasan berdasarkan periode
Use case yang berhubungan dengan transaksi akademik harus memeriksa:
* Tahun ajaran.
* Semester.
* Status periode.
* Status penguncian.
21.2 Pembatasan berdasarkan penugasan
Guru hanya dapat mengakses data berdasarkan:
* Kelas yang diajar.
* Mata pelajaran yang diajar.
* Tahun ajaran.
* Semester.
* Penugasan aktif.
21.3 Pembatasan berdasarkan relasi
Orang tua hanya dapat melihat siswa yang terhubung melalui relasi orang tua dan siswa.
Siswa hanya dapat melihat data miliknya sendiri.
Wali kelas hanya dapat melihat siswa pada kelas yang menjadi tanggung jawabnya.
21.4 Pembatasan berdasarkan status
Data tertentu hanya dapat diakses setelah memiliki status tertentu.
Contoh:
* Nilai harus dipublikasikan.
* Rapor harus diterbitkan.
* Berita harus dipublikasikan.
* Prestasi harus diverifikasi.
* Dokumen harus disetujui.
* Eviden harus memiliki status akses yang sesuai.
21.5 Audit perubahan penting
Use case berikut harus menghasilkan audit log:
* Mengubah nilai.
* Mengoreksi absensi.
* Mengubah pembayaran.
* Membatalkan transaksi.
* Mengubah status siswa.
* Mengubah role.
* Mengubah permission.
* Menerbitkan rapor.
* Menerbitkan berita.
* Memverifikasi eviden.
* Mengubah data inventaris.
* Menjalankan restore.

22. KEPUTUSAN DESAIN USE CASE
Berdasarkan analisis, keputusan yang ditetapkan adalah:
Area	Keputusan
Jumlah diagram	Sembilan diagram
Diagram utama	Diagram konteks
Diagram rinci	Berdasarkan kelompok modul
Aktor pengguna	Mengikuti role dan penugasan
Pengguna multi-role	Diizinkan
Panitia PPDB	Penugasan periodik
Tim PKKM	Role tambahan atau penugasan
Tim Akreditasi	Role tambahan atau penugasan
Hak akses rinci	Permission dan Policy
Data siswa	Dibatasi berdasarkan relasi
Data guru	Dibatasi berdasarkan penugasan
Transaksi lama	Tidak ditimpa
Publikasi data	Menggunakan status
Perubahan sensitif	Masuk audit log
QR Code	Menggunakan token dan validasi akses
Diagram besar tunggal	Tidak digunakan karena sulit dibaca
23. KEUNTUNGAN DESAIN USE CASE INI
1. Setiap aktor memiliki batas tanggung jawab yang jelas.
2. Diagram lebih mudah dibaca karena dibagi berdasarkan domain.
3. Seluruh 68 modul tetap terwakili.
4. Role dan permission dapat dirancang secara sistematis.
5. Risiko kebocoran data dapat dikurangi.
6. Alur persetujuan dapat ditentukan sebelum coding.
7. Flowchart dapat dibuat berdasarkan use case utama.
8. ERD dapat disusun berdasarkan data yang dibutuhkan setiap use case.
9. Pengujian dapat disusun berdasarkan aktor dan skenario.
10. Pengembangan modul dapat mengikuti prioritas yang telah ditetapkan.

24. KEKURANGAN DAN TANTANGAN
24.1 Jumlah aktor cukup banyak
Satu pengguna dapat memiliki beberapa role sehingga hubungan aktor dapat menjadi kompleks.
Solusinya:
* Gunakan akun tunggal.
* Izinkan multi-role.
* Gunakan permission.
* Gunakan penugasan berbasis periode.
* Gunakan Policy untuk akses per record.
24.2 Beberapa use case saling berhubungan
Contohnya, rapor membutuhkan data dari:
* Nilai.
* Kehadiran.
* Ekstrakurikuler.
* Prestasi.
* Tahfidz.
* Pembiasaan.
Solusinya adalah tidak menduplikasi data. Modul rapor mengambil data dari modul sumber.
24.3 Hak akses tidak cukup diatur melalui menu
Menyembunyikan menu tidak cukup untuk mengamankan sistem.
Sistem tetap harus memeriksa akses melalui:
* Middleware.
* Gate.
* Policy.
* Query berdasarkan relasi.
* Validation.
* Permission.
24.4 Workflow membutuhkan status yang jelas
Berita, nilai, rapor, dokumen, pembayaran, dan eviden memiliki alur yang berbeda.
Setiap workflow harus menggunakan status yang jelas dan dapat dilacak.

25. HASIL TAHAP 3
Pada Tahap 3 telah ditetapkan:
1. Definisi Use Case Diagram.
2. Tujuan Use Case Diagram.
3. Batas SIM Madrasah.
4. Daftar 20 aktor utama dan pendukung.
5. Perbedaan aktor, role, jabatan, penugasan, dan permission.
6. Diagram konteks SIM Madrasah.
7. Diagram fondasi dan pengaturan.
8. Diagram website publik, berita, dan PPDB.
9. Diagram data inti dan akademik.
10. Diagram kesiswaan dan portofolio.
11. Diagram keuangan, tata usaha, dan kepegawaian.
12. Diagram sarana, inventaris, perpustakaan, dan laboratorium.
13. Diagram PKKM, akreditasi, dan penjaminan mutu.
14. Diagram portal, laporan, audit, dan pemeliharaan.
15. Hubungan association, include, extend, dan generalization.
16. Dua belas spesifikasi use case utama.
17. Matriks akses tingkat konseptual.
18. Aturan akses berdasarkan periode, penugasan, relasi, dan status.
19. Aturan audit untuk perubahan data penting.
20. Keputusan desain use case.
Mengapa Tahap 3 Penting
Tahap ini menjadi penghubung antara kebutuhan sistem dan desain teknis.
Use Case Diagram akan menjadi dasar untuk:
* Membuat flowchart pada Tahap 4.
* Membuat ERD pada Tahap 5.
* Membuat desain database pada Tahap 6.
* Menyusun struktur menu.
* Menyusun role dan permission.
* Membuat skenario pengujian.
* Menentukan urutan implementasi modul.
Tanpa Use Case Diagram, pengembangan berisiko menghasilkan hak akses yang tidak konsisten, alur kerja yang tidak jelas, dan fitur yang tidak sesuai dengan tanggung jawab pengguna.
