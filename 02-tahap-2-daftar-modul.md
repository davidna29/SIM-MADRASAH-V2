# Tahap 2 — Daftar Modul SIM Madrasah

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 1341–3090.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

=========================================================
TAHAP 2
DAFTAR SELURUH MODUL SIM MADRASAH
1. Konsep Sederhana
Modul adalah bagian sistem yang menangani satu kelompok pekerjaan tertentu.
Contohnya:
* Modul Akademik menangani jadwal, pembelajaran, nilai, dan rapor.
* Modul Keuangan menangani tagihan dan pembayaran.
* Modul Perpustakaan menangani buku dan peminjaman.
* Modul PPDB menangani penerimaan siswa baru.
Walaupun modul dipisahkan, semua modul tetap berada dalam satu aplikasi SIM Madrasah.
2. Analogi Sederhana
SIM Madrasah dapat dianalogikan sebagai satu gedung madrasah.
Di dalam gedung tersebut terdapat beberapa ruangan:
* Ruang kepala madrasah.
* Ruang guru.
* Ruang tata usaha.
* Ruang bendahara.
* Perpustakaan.
* Laboratorium.
* Ruang BK.
* Ruang kelas.
Setiap ruangan memiliki fungsi yang berbeda. Namun, seluruh ruangan tetap menjadi bagian dari satu madrasah.
Modul dalam aplikasi bekerja dengan cara yang sama.
Setiap modul:
* Memiliki tugas sendiri.
* Memiliki pengguna tertentu.
* Memiliki data tertentu.
* Dapat berhubungan dengan modul lain.
* Tetap memakai satu sistem login dan satu sumber data.

3. Tujuan Pembagian Modul
Pembagian modul bertujuan agar sistem:
1. Mudah dipahami.
2. Mudah dikembangkan.
3. Mudah diuji.
4. Mudah diperbaiki.
5. Tidak mencampurkan semua fungsi dalam satu bagian.
6. Dapat dibangun secara bertahap.
7. Memiliki hak akses yang jelas.
8. Mengurangi duplikasi data.
9. Mudah dikembangkan pada masa depan.

4. Kelompok Utama Modul
Seluruh modul SIM Madrasah dibagi menjadi delapan kelompok besar.
Kelompok	Isi Utama
A	Fondasi dan pengaturan sistem
B	Website publik, berita, dan PPDB
C	Data inti dan akademik
D	Kesiswaan dan portofolio siswa
E	Keuangan, tata usaha, dan kepegawaian
F	Sarana, inventaris, perpustakaan, dan laboratorium
G	PKKM, akreditasi, dan penjaminan mutu
H	Portal pengguna, pelaporan, audit, dan pemeliharaan sistem
KELOMPOK A
FONDASI DAN PENGATURAN SISTEM
MOD-001 Authentication
Fungsi utama
Mengelola proses masuk dan keluar dari aplikasi.
Fitur
* Login.
* Logout.
* Lupa password.
* Reset password.
* Ubah password.
* Verifikasi status akun.
* Pembatasan akun tidak aktif.
* Pencatatan waktu login terakhir.
* Pengamanan sesi pengguna.
Pengguna
Semua pengguna internal, orang tua, dan siswa.
Alasan modul ini penting
Semua akses sistem dimulai dari proses authentication. Tanpa authentication, sistem tidak dapat mengenali siapa pengguna yang sedang menggunakan aplikasi.

MOD-002 Manajemen Pengguna
Fungsi utama
Mengelola seluruh akun pengguna.
Fitur
* Membuat akun.
* Mengubah akun.
* Mengaktifkan akun.
* Menonaktifkan akun.
* Menghubungkan akun dengan guru.
* Menghubungkan akun dengan pegawai.
* Menghubungkan akun dengan siswa.
* Menghubungkan akun dengan orang tua.
* Reset password oleh administrator.
* Melihat riwayat status akun.
Pengguna utama
* Super Admin.
* Tata Usaha.
* Administrator yang diberikan izin.
Catatan penting
Data pengguna tidak sama dengan data guru atau siswa.
Pengguna adalah akun untuk masuk ke sistem.
Guru, siswa, pegawai, dan orang tua adalah identitas orang yang terhubung dengan akun tersebut.

MOD-003 Role dan Permission
Fungsi utama
Mengatur hak akses pengguna.
Fitur
* Membuat role.
* Mengubah role.
* Membuat permission.
* Menghubungkan permission dengan role.
* Memberikan beberapa role kepada satu pengguna.
* Memberikan permission khusus jika diperlukan.
* Melihat matriks role dan permission.
* Mencegah pengguna mengakses menu yang tidak diizinkan.
Contoh permission
* siswa.lihat
* siswa.tambah
* siswa.ubah
* siswa.hapus
* nilai.input
* nilai.verifikasi
* nilai.publish
* pembayaran.input
* pembayaran.koreksi
* berita.approve
Pengguna utama
Super Admin.
Catatan penting
Role memberikan kelompok akses.
Permission memberikan akses yang lebih rinci.

MOD-004 Struktur Organisasi dan Jabatan
Fungsi utama
Menyimpan struktur organisasi madrasah.
Fitur
* Data unit kerja.
* Data jabatan.
* Struktur organisasi.
* Masa jabatan.
* Riwayat pejabat.
* Surat keputusan jabatan.
* Hubungan atasan dan bawahan.
Contoh jabatan
* Kepala Madrasah.
* Wakamad Kurikulum.
* Wakamad Kesiswaan.
* Wakamad Sarpras.
* Wakamad Humas.
* Kepala Tata Usaha.
* Kepala Perpustakaan.
* Kepala Laboratorium.
Catatan penting
Jabatan tidak boleh langsung disamakan dengan role.
Seseorang dapat memiliki jabatan tertentu, tetapi hak aksesnya tetap diatur melalui role dan permission.

MOD-005 Pengaturan Sistem
Fungsi utama
Menyimpan konfigurasi umum aplikasi.
Fitur
* Nama madrasah.
* NSM.
* NPSN.
* Alamat.
* Logo.
* Kepala madrasah aktif.
* Zona waktu.
* Format tanggal.
* Format nomor surat.
* Format nomor kwitansi.
* Tema warna.
* Semester aktif.
* Tahun ajaran aktif.
* Batas ukuran upload.
* Jenis file yang diizinkan.
* Konfigurasi identitas rapor.
Pengguna utama
Super Admin dan administrator tertentu.

MOD-006 Notifikasi Internal
Fungsi utama
Memberikan informasi kepada pengguna tentang aktivitas yang membutuhkan perhatian.
Fitur
* Notifikasi nilai belum lengkap.
* Notifikasi berita menunggu review.
* Notifikasi dokumen ditolak.
* Notifikasi pembayaran baru.
* Notifikasi buku terlambat.
* Notifikasi eviden belum lengkap.
* Notifikasi persetujuan.
* Status sudah dibaca atau belum dibaca.
Catatan teknis
Notifikasi versi awal menggunakan database dan ditampilkan saat pengguna membuka sistem.
Sistem tidak memerlukan WebSocket.

KELOMPOK B
WEBSITE PUBLIK, BERITA, DAN PPDB
MOD-007 Website Publik
Fungsi utama
Menampilkan informasi resmi madrasah kepada masyarakat.
Fitur
* Beranda.
* Profil madrasah.
* Sejarah.
* Visi dan misi.
* Struktur organisasi.
* Data guru yang boleh dipublikasikan.
* Sarana dan prasarana.
* Agenda.
* Pengumuman.
* Galeri.
* Video.
* Kontak.
* Lokasi madrasah.
* Tautan media sosial.
* Halaman kebijakan privasi.
Pengguna utama
* Pengunjung umum.
* Wakamad Humas.
* Editor Berita.
* Super Admin.

MOD-008 Portal Berita atau CMS
Fungsi utama
Mengelola berita dan publikasi madrasah.
Fitur konten
* Judul berita.
* Slug.
* Ringkasan.
* Isi berita.
* Kategori.
* Tag.
* Featured image.
* Multiple image.
* Video.
* Penulis.
* Editor.
* Meta title.
* Meta description.
* Kata kunci SEO.
* Jadwal publikasi.
* Status berita.
Workflow
1. Draft.
2. Diajukan.
3. Dalam review.
4. Memerlukan revisi.
5. Disetujui.
6. Dijadwalkan.
7. Dipublikasikan.
8. Diarsipkan.
Pengguna utama
* Guru atau kontributor.
* Editor Berita.
* Wakamad Humas.
* Kepala Madrasah.
* Super Admin.
Aturan penting
Kepala madrasah dapat menjadi pemberi persetujuan akhir jika aturan madrasah mengharuskannya.

MOD-009 Agenda dan Pengumuman
Fungsi utama
Mengelola informasi kegiatan dan pemberitahuan.
Fitur
* Agenda kegiatan.
* Tanggal dan waktu.
* Lokasi.
* Penanggung jawab.
* Pengumuman publik.
* Pengumuman internal.
* Target penerima.
* Lampiran.
* Masa tampil.
* Status aktif.
Pengguna utama
* Wakamad.
* Tata Usaha.
* Editor Berita.
* Kepala Madrasah.

MOD-010 Galeri dan Media
Fungsi utama
Mengelola dokumentasi foto dan video.
Fitur
* Album foto.
* Kategori album.
* Upload foto.
* Deskripsi foto.
* Thumbnail.
* Video internal.
* Tautan video eksternal.
* Dokumentasi kegiatan.
* Status publik atau privat.
Catatan penting
Video berukuran besar sebaiknya menggunakan tautan layanan video agar storage shared hosting tidak cepat penuh.

MOD-011 PPDB
Fungsi utama
Mengelola proses penerimaan peserta didik baru.
Tahapan proses
1. Informasi PPDB.
2. Pembuatan akun pendaftar.
3. Pengisian formulir.
4. Upload dokumen.
5. Pengiriman pendaftaran.
6. Verifikasi administrasi.
7. Seleksi.
8. Pengumuman.
9. Daftar ulang.
10. Konversi menjadi data siswa.
Fitur
* Periode PPDB.
* Jalur pendaftaran.
* Kuota.
* Nomor pendaftaran.
* Biodata calon siswa.
* Data orang tua.
* Asal sekolah.
* Upload dokumen.
* Checklist verifikasi.
* Catatan verifikator.
* Nilai seleksi.
* Status kelulusan.
* Cetak kartu peserta.
* Cetak bukti pendaftaran.
* Pengumuman hasil.
* Registrasi ulang.
Pengguna utama
* Calon siswa.
* Orang tua.
* Panitia PPDB.
* Tata Usaha.
* Kepala Madrasah.
Aturan penting
Calon siswa yang diterima tidak perlu diketik ulang. Data PPDB harus dapat dikonversi menjadi data siswa aktif.

KELOMPOK C
DATA INTI DAN AKADEMIK
MOD-012 Tahun Ajaran dan Semester
Fungsi utama
Menjadi dasar periode seluruh transaksi akademik.
Fitur
* Tahun ajaran.
* Tanggal mulai.
* Tanggal selesai.
* Semester ganjil.
* Semester genap.
* Semester aktif.
* Tahun ajaran aktif.
* Status dibuka atau ditutup.
* Penguncian periode lama.
Pengguna utama
* Super Admin.
* Wakamad Kurikulum.
Aturan penting
Data periode lama tidak boleh dihapus karena menjadi referensi histori.

MOD-013 Data Guru dan Tenaga Kependidikan
Fungsi utama
Menyimpan identitas guru dan pegawai.
Fitur data guru
* NIP.
* NUPTK.
* Nama.
* Gelar.
* Tempat lahir.
* Tanggal lahir.
* Jenis kelamin.
* Alamat.
* Kontak.
* Pendidikan terakhir.
* Status kepegawaian.
* TMT.
* Mata pelajaran utama.
* Foto.
* Dokumen kepegawaian.
* Status aktif.
Pengguna utama
* Tata Usaha.
* Super Admin.
* Kepala Madrasah.
* Bagian Kepegawaian.

MOD-014 Data Siswa
Fungsi utama
Menyimpan identitas utama siswa.
Fitur
* NIS.
* NISN.
* Nama.
* Tempat dan tanggal lahir.
* Jenis kelamin.
* Agama.
* Alamat.
* Kontak.
* Data orang tua.
* Data wali.
* Asal sekolah.
* Tanggal masuk.
* Status siswa.
* Foto.
* Dokumen siswa.
* Nomor identitas.
* Kebutuhan khusus jika diperlukan.
Pengguna utama
* Tata Usaha.
* Wakamad Kesiswaan.
* Wali Kelas.
* Super Admin.
Aturan penting
Data kelas tidak disimpan langsung sebagai satu-satunya kelas pada profil siswa. Riwayat kelas dikelola melalui modul penempatan siswa.

MOD-015 Data Orang Tua dan Wali
Fungsi utama
Menyimpan data orang tua atau wali siswa.
Fitur
* Data ayah.
* Data ibu.
* Data wali.
* Nomor kontak.
* Alamat.
* Pekerjaan.
* Pendidikan.
* Penghasilan jika diperlukan.
* Hubungan dengan siswa.
* Status sebagai kontak utama.
* Hubungan satu orang tua dengan beberapa siswa.
Pengguna utama
* Tata Usaha.
* Orang Tua.
* Super Admin.

MOD-016 Mata Pelajaran
Fungsi utama
Mengelola daftar mata pelajaran.
Fitur
* Kode mata pelajaran.
* Nama mata pelajaran.
* Kelompok mata pelajaran.
* Tingkat kelas.
* Kurikulum.
* Jam pelajaran.
* Status aktif.
* Urutan pada rapor.
Pengguna utama
* Wakamad Kurikulum.
* Super Admin.

MOD-017 Kelas dan Rombongan Belajar
Fungsi utama
Mengelola kelas dan rombongan belajar.
Fitur
* Tingkat kelas.
* Nama rombel.
* Program atau jurusan jika ada.
* Kapasitas.
* Ruangan.
* Tahun ajaran.
* Wali kelas.
* Status aktif.
* Daftar siswa.
Pengguna utama
* Wakamad Kurikulum.
* Tata Usaha.
* Wali Kelas.

MOD-018 Penempatan dan Riwayat Kelas Siswa
Fungsi utama
Mencatat posisi siswa pada setiap tahun ajaran dan semester.
Fitur
* Penempatan siswa.
* Kenaikan kelas.
* Tinggal kelas.
* Perpindahan kelas.
* Kelulusan.
* Riwayat kelas.
* Status penempatan.
* Tanggal perubahan.
* Catatan perubahan.
Pengguna utama
* Wakamad Kurikulum.
* Tata Usaha.
* Wali Kelas.
Aturan penting
Saat siswa naik kelas, data lama tidak diubah.
Sistem membuat penempatan baru untuk periode berikutnya.

MOD-019 Kurikulum
Fungsi utama
Mengelola struktur kurikulum yang digunakan madrasah.
Fitur
* Nama kurikulum.
* Tahun berlaku.
* Struktur mata pelajaran.
* Alokasi waktu.
* Tingkat kelas.
* Capaian pembelajaran.
* Status aktif.
* Dokumen kurikulum.
Pengguna utama
* Wakamad Kurikulum.
* Kepala Madrasah.

MOD-020 Kalender Pendidikan
Fungsi utama
Mengelola kalender kegiatan pendidikan.
Fitur
* Hari efektif.
* Hari libur.
* Awal semester.
* Akhir semester.
* Penilaian.
* Pembagian rapor.
* Kegiatan madrasah.
* Kegiatan nasional.
* Warna kategori kegiatan.
* Tampilan kalender.
Pengguna utama
* Wakamad Kurikulum.
* Tata Usaha.
* Guru.
* Siswa.
* Orang Tua.

MOD-021 Penugasan Mengajar
Fungsi utama
Menentukan guru yang mengajar kelas dan mata pelajaran tertentu.
Fitur
* Guru.
* Mata pelajaran.
* Kelas.
* Tahun ajaran.
* Semester.
* Jumlah jam.
* Status penugasan.
* Surat tugas.
* Riwayat penugasan.
Pengguna utama
* Wakamad Kurikulum.
* Kepala Madrasah.
Aturan penting
Guru hanya dapat memasukkan nilai dan jurnal pada penugasan mengajarnya.

MOD-022 Jadwal Pelajaran
Fungsi utama
Mengelola jadwal kegiatan belajar mengajar.
Fitur
* Hari.
* Jam pelajaran.
* Mata pelajaran.
* Guru.
* Kelas.
* Ruangan.
* Tahun ajaran.
* Semester.
* Pengecekan bentrok guru.
* Pengecekan bentrok ruangan.
* Pengecekan bentrok kelas.
* Cetak jadwal.
Pengguna utama
* Wakamad Kurikulum.
* Guru.
* Siswa.
* Orang Tua.

MOD-023 ATP dan Perangkat Pembelajaran
Fungsi utama
Mengelola perangkat pembelajaran guru.
Fitur
* ATP.
* Capaian pembelajaran.
* Tujuan pembelajaran.
* Modul ajar.
* Program tahunan.
* Program semester.
* Bahan ajar.
* Media pembelajaran.
* Upload dokumen.
* Status review.
* Persetujuan kurikulum.
Pengguna utama
* Guru Mata Pelajaran.
* Wakamad Kurikulum.
* Kepala Madrasah.

MOD-024 Jurnal Mengajar
Fungsi utama
Mencatat pelaksanaan kegiatan pembelajaran.
Fitur
* Tanggal.
* Jam pelajaran.
* Kelas.
* Mata pelajaran.
* Materi.
* Tujuan pembelajaran.
* Metode.
* Kehadiran siswa.
* Catatan kegiatan.
* Tindak lanjut.
* Lampiran.
* Status jurnal.
Pengguna utama
* Guru Mata Pelajaran.
* Wakamad Kurikulum.
* Kepala Madrasah.

MOD-025 Penilaian
Fungsi utama
Mengelola seluruh komponen nilai siswa.
Fitur
* Jenis penilaian.
* Bobot penilaian.
* Nilai tugas.
* Nilai formatif.
* Nilai sumatif.
* Nilai praktik.
* Nilai proyek.
* Nilai ujian.
* Nilai remedial.
* Nilai pengayaan.
* Deskripsi capaian.
* Import nilai.
* Validasi nilai.
* Status draft.
* Status verifikasi.
* Status publikasi.
Pengguna utama
* Guru Mata Pelajaran.
* Wali Kelas.
* Wakamad Kurikulum.
* Kepala Madrasah.
Aturan penting
Nilai harus terhubung dengan:
* Siswa.
* Mata pelajaran.
* Guru.
* Kelas.
* Tahun ajaran.
* Semester.

MOD-026 Rapor
Fungsi utama
Menghasilkan laporan hasil belajar siswa.
Fitur
* Identitas siswa.
* Nilai mata pelajaran.
* Deskripsi capaian.
* Absensi.
* Ekstrakurikuler.
* Prestasi.
* Catatan wali kelas.
* Tahfidz.
* Pembiasaan.
* Status kenaikan kelas.
* Tanda tangan.
* Nomor rapor.
* Preview.
* Verifikasi.
* Publish.
* Cetak PDF.
* Arsip rapor.
Pengguna utama
* Guru Mata Pelajaran.
* Wali Kelas.
* Wakamad Kurikulum.
* Kepala Madrasah.
* Orang Tua.
* Siswa.

KELOMPOK D
KESISWAAN DAN PORTOFOLIO SISWA
MOD-027 Portofolio Digital Siswa
Fungsi utama
Menampilkan perjalanan siswa dalam satu profil permanen.
Isi portofolio
* Biodata.
* QR Code.
* Riwayat kelas.
* Kehadiran.
* Nilai.
* Rapor.
* Prestasi.
* Pelanggaran.
* Konseling.
* Ekstrakurikuler.
* Tahfidz.
* Pembiasaan.
* Pembayaran.
* Dokumen.
* Karya siswa.
* Sertifikat.
Pengguna utama
* Siswa.
* Orang Tua.
* Wali Kelas.
* Guru.
* Kepala Madrasah.
* Wakamad Kesiswaan.
* Tata Usaha.
Aturan penting
Portofolio mengambil data dari modul sumber.
Portofolio tidak membuat salinan nilai, absensi, atau pembayaran.

MOD-028 QR Code Siswa
Fungsi utama
Memberikan identitas digital unik kepada siswa.
Fitur
* QR Code unik.
* Token aman.
* Cetak kartu.
* Regenerasi QR Code jika diperlukan.
* Status aktif.
* Pengaturan halaman publik.
* Pembatasan informasi sensitif.
Aturan penting
Pemindaian QR Code tidak otomatis membuka seluruh informasi pribadi siswa.

MOD-029 Kehadiran Siswa
Fungsi utama
Mencatat kehadiran siswa.
Fitur
* Hadir.
* Sakit.
* Izin.
* Alfa.
* Terlambat.
* Pulang awal.
* Absensi harian.
* Absensi per pelajaran.
* Surat izin.
* Rekap bulanan.
* Rekap semester.
* Persentase kehadiran.
* Koreksi absensi.
* Riwayat perubahan.
Pengguna utama
* Guru.
* Wali Kelas.
* Wakamad Kesiswaan.
* Tata Usaha.
* Orang Tua.
* Siswa.

MOD-030 Prestasi Siswa
Fungsi utama
Mencatat prestasi akademik dan nonakademik.
Fitur
* Jenis prestasi.
* Tingkat lomba.
* Nama kegiatan.
* Penyelenggara.
* Tanggal.
* Peringkat.
* Pembimbing.
* Sertifikat.
* Foto.
* Status verifikasi.
* Status publikasi.
Pengguna utama
* Wakamad Kesiswaan.
* Wali Kelas.
* Guru.
* Siswa.

MOD-031 Pelanggaran dan Disiplin
Fungsi utama
Mencatat pelanggaran siswa dan tindak lanjutnya.
Fitur
* Kategori pelanggaran.
* Tingkat pelanggaran.
* Poin.
* Tanggal kejadian.
* Kronologi.
* Pelapor.
* Bukti.
* Tindakan.
* Pemanggilan orang tua.
* Surat peringatan.
* Status penyelesaian.
* Riwayat tindak lanjut.
Pengguna utama
* Wakamad Kesiswaan.
* Wali Kelas.
* Guru BK.
* Kepala Madrasah.

MOD-032 Bimbingan dan Konseling
Fungsi utama
Mencatat layanan konseling siswa.
Fitur
* Jenis konseling.
* Tanggal.
* Topik.
* Permasalahan.
* Hasil asesmen.
* Tindakan.
* Rencana tindak lanjut.
* Pertemuan lanjutan.
* Lampiran.
* Tingkat kerahasiaan.
* Pihak yang boleh melihat.
Pengguna utama
* Guru BK.
* Kepala Madrasah.
* Wali Kelas dengan akses terbatas.

MOD-033 Ekstrakurikuler
Fungsi utama
Mengelola kegiatan ekstrakurikuler.
Fitur
* Daftar ekstrakurikuler.
* Pembina.
* Anggota.
* Jadwal.
* Lokasi.
* Kehadiran.
* Penilaian.
* Prestasi.
* Dokumentasi.
* Catatan kegiatan.
Pengguna utama
* Wakamad Kesiswaan.
* Pembina.
* Siswa.
* Wali Kelas.

MOD-034 Tahfidz
Fungsi utama
Mencatat perkembangan hafalan siswa.
Fitur
* Target hafalan.
* Surah.
* Ayat.
* Juz.
* Setoran.
* Murajaah.
* Tanggal.
* Penguji.
* Nilai kelancaran.
* Nilai tajwid.
* Catatan.
* Rekap semester.
* Perkembangan grafik.
Pengguna utama
* Guru Tahfidz.
* Wali Kelas.
* Wakamad Kesiswaan.
* Orang Tua.
* Siswa.

MOD-035 Pembiasaan dan Karakter
Fungsi utama
Mencatat kegiatan pembiasaan dan perkembangan karakter.
Fitur
* Jenis pembiasaan.
* Salat berjamaah.
* Literasi.
* Kebersihan.
* Kedisiplinan.
* Sikap.
* Tanggung jawab.
* Catatan guru.
* Rekap harian.
* Rekap bulanan.
* Rekap semester.
Pengguna utama
* Guru.
* Wali Kelas.
* Wakamad Kesiswaan.
* Orang Tua.

MOD-036 Organisasi Siswa
Fungsi utama
Mengelola organisasi dan kepengurusan siswa.
Fitur
* Nama organisasi.
* Periode kepengurusan.
* Pembina.
* Struktur pengurus.
* Program kerja.
* Kegiatan.
* Laporan.
* Dokumentasi.
* Surat keputusan.
Pengguna utama
* Wakamad Kesiswaan.
* Pembina.
* Siswa.

KELOMPOK E
KEUANGAN, TATA USAHA, DAN KEPEGAWAIAN
MOD-037 Tagihan dan Pembayaran Siswa
Fungsi utama
Mengelola tagihan dan pembayaran siswa.
Fitur tagihan
* Jenis tagihan.
* Tahun ajaran.
* Bulan.
* Nominal.
* Kelas.
* Siswa.
* Jatuh tempo.
* Potongan.
* Keringanan.
* Pembebasan.
Fitur pembayaran
* Input pembayaran.
* Pembayaran penuh.
* Pembayaran cicilan.
* Nomor transaksi.
* Metode pembayaran.
* Tanggal pembayaran.
* Penerima pembayaran.
* Cetak kwitansi.
* Pembatalan dengan alasan.
* Koreksi transaksi.
* Riwayat pembayaran.
Status
* Belum bayar.
* Cicilan.
* Lunas.
* Dibebaskan.
* Dibatalkan.
Dashboard per kelas
Nama Siswa	Juli	Agustus	September
Ahmad	Lunas	Lunas	Belum
Siti	Lunas	Lunas	Lunas
Pengguna utama
* Bendahara.
* Kepala Madrasah.
* Wali Kelas dengan akses baca.
* Orang Tua.
* Siswa.

MOD-038 Rekap dan Laporan Keuangan Siswa
Fungsi utama
Menyajikan laporan pembayaran.
Fitur
* Rekap per siswa.
* Rekap per kelas.
* Rekap per bulan.
* Rekap per tahun.
* Daftar tunggakan.
* Rekap cicilan.
* Rekap potongan.
* Rekap pembebasan.
* Export Excel.
* Cetak PDF.

MOD-039 Surat Masuk
Fungsi utama
Mengelola surat yang diterima madrasah.
Fitur
* Nomor agenda.
* Nomor surat.
* Tanggal surat.
* Tanggal diterima.
* Pengirim.
* Perihal.
* Klasifikasi.
* Disposisi.
* Penerima disposisi.
* Lampiran.
* Status tindak lanjut.
* Arsip digital.
Pengguna utama
* Tata Usaha.
* Kepala Madrasah.
* Wakamad.

MOD-040 Surat Keluar
Fungsi utama
Mengelola surat resmi yang diterbitkan madrasah.
Fitur
* Nomor surat otomatis.
* Jenis surat.
* Tujuan.
* Perihal.
* Tanggal.
* Penandatangan.
* Template surat.
* Lampiran.
* Status draft.
* Status persetujuan.
* Cetak PDF.
* Arsip digital.
Pengguna utama
* Tata Usaha.
* Kepala Madrasah.
* Wakamad.

MOD-041 Arsip Digital
Fungsi utama
Menyimpan dokumen administrasi secara terstruktur.
Fitur
* Kategori arsip.
* Nomor arsip.
* Tahun.
* Unit pemilik.
* Tingkat kerahasiaan.
* File.
* Deskripsi.
* Retensi arsip.
* Status aktif.
* Pencarian dokumen.

MOD-042 Surat Keputusan
Fungsi utama
Mengelola dokumen keputusan madrasah.
Fitur
* Nomor SK.
* Judul.
* Tanggal.
* Masa berlaku.
* Penandatangan.
* Pihak terkait.
* File.
* Status aktif.
* Riwayat perubahan.

MOD-043 MOU dan Kerja Sama
Fungsi utama
Mengelola dokumen kerja sama.
Fitur
* Mitra.
* Nomor MOU.
* Ruang lingkup.
* Tanggal mulai.
* Tanggal berakhir.
* Penanggung jawab.
* File.
* Status.
* Pengingat masa berlaku.
* Hasil kerja sama.

MOD-044 Kehadiran Guru dan Pegawai
Fungsi utama
Mencatat kehadiran tenaga pendidik dan kependidikan.
Fitur
* Hadir.
* Izin.
* Sakit.
* Dinas luar.
* Cuti.
* Terlambat.
* Pulang awal.
* Jam masuk.
* Jam keluar.
* Rekap bulanan.
* Rekap tahunan.
* Dokumen pendukung.
* Koreksi absensi.

MOD-045 Kepegawaian
Fungsi utama
Mengelola perjalanan kepegawaian guru dan tenaga kependidikan.
Fitur
* Status kepegawaian.
* Riwayat pendidikan.
* Riwayat jabatan.
* Riwayat pangkat.
* Riwayat masa kerja.
* Riwayat penugasan.
* Dokumen kepegawaian.
* Sertifikasi.
* Penghargaan.
* Disiplin pegawai.

MOD-046 PKB, Workshop, dan Sertifikat
Fungsi utama
Mencatat pengembangan kompetensi guru dan pegawai.
Fitur
* Workshop.
* Pelatihan.
* Seminar.
* Webinar.
* Pendidikan dan pelatihan.
* Penyelenggara.
* Tanggal.
* Jumlah jam.
* Sertifikat.
* Kategori kompetensi.
* Laporan kegiatan.
* Riwayat pengembangan diri.

KELOMPOK F
SARANA, INVENTARIS, PERPUSTAKAAN, DAN LABORATORIUM
MOD-047 Ruangan
Fungsi utama
Mengelola seluruh ruangan madrasah.
Fitur
* Kode ruangan.
* Nama ruangan.
* Gedung.
* Lantai.
* Kapasitas.
* Penanggung jawab.
* Kondisi.
* Foto.
* Status penggunaan.

MOD-048 Inventaris Barang
Fungsi utama
Mengelola aset dan barang madrasah.
Fitur
* Kode barang.
* Nama barang.
* Kategori.
* Merek.
* Sumber dana.
* Tahun perolehan.
* Harga.
* Jumlah.
* Satuan.
* Lokasi.
* Kondisi.
* Penanggung jawab.
* Foto.
* QR Code barang.
* Status barang.
* Dokumen pembelian.

MOD-049 Mutasi dan Pemeliharaan Barang
Fungsi utama
Mencatat perpindahan dan perawatan inventaris.
Fitur
* Mutasi lokasi.
* Peminjaman.
* Pengembalian.
* Kerusakan.
* Perbaikan.
* Pemeliharaan rutin.
* Penghapusan.
* Berita acara.
* Riwayat kondisi barang.

MOD-050 Laboratorium
Fungsi utama
Mengelola laboratorium dan perlengkapannya.
Fitur
* Data laboratorium.
* Alat.
* Bahan.
* Stok.
* Peminjaman.
* Pengembalian.
* Jadwal penggunaan.
* Kerusakan.
* Pemeliharaan.
* Keselamatan kerja.
* Laporan penggunaan.
Pengguna utama
* Petugas Laboratorium.
* Wakamad Sarpras.
* Guru.

MOD-051 Perpustakaan
Fungsi utama
Mengelola koleksi dan layanan perpustakaan.
Fitur koleksi
* Buku.
* Kategori.
* Penulis.
* Penerbit.
* Tahun terbit.
* ISBN.
* Nomor klasifikasi.
* Rak.
* Stok.
* Cover.
* Kondisi.
Fitur layanan
* Anggota.
* Peminjaman.
* Pengembalian.
* Perpanjangan.
* Keterlambatan.
* Denda jika diterapkan.
* Riwayat peminjaman.
* Laporan buku populer.
* Laporan keterlambatan.

MOD-052 Ebook
Fungsi utama
Mengelola buku digital.
Fitur
* Judul.
* Penulis.
* Kategori.
* Cover.
* File.
* Tautan eksternal.
* Hak akses.
* Statistik akses.
* Status publikasi.
Catatan penting
Hak cipta ebook harus diperhatikan. Sistem tidak boleh membagikan file yang tidak memiliki izin distribusi.

KELOMPOK G
PKKM, AKREDITASI, DAN PENJAMINAN MUTU
MOD-053 PKKM Center
Fungsi utama
Mengelola pemetaan indikator dan eviden Penilaian Kinerja Kepala Madrasah.
Fitur
* Periode PKKM.
* Komponen.
* Indikator.
* Subindikator.
* Target.
* Penanggung jawab.
* Checklist.
* Upload PDF.
* Upload foto.
* Upload video atau tautan.
* Catatan.
* Status kelengkapan.
* Status verifikasi.
* Persentase kelengkapan.
* Riwayat eviden.
* Export laporan.
Pengguna utama
* Kepala Madrasah.
* Tim PKKM.
* Wakamad.
* Tata Usaha.
* Super Admin.
Catatan penting
Struktur indikator akan mengikuti dokumen PKKM resmi yang diberikan oleh madrasah.

MOD-054 Akreditasi
Fungsi utama
Mengelola data dan eviden akreditasi madrasah.
Kelompok awal
* Standar Kompetensi Lulusan.
* Standar Isi.
* Standar Proses.
* Standar Penilaian.
* Standar Pendidik dan Tenaga Kependidikan.
* Standar Sarana dan Prasarana.
* Standar Pengelolaan.
* Standar Pembiayaan.
Fitur
* Periode akreditasi.
* Standar.
* Komponen.
* Indikator.
* Eviden.
* Penanggung jawab.
* Status kelengkapan.
* Monitoring.
* Catatan evaluator.
* Skor internal.
* Persentase kelengkapan.
* Laporan.
Catatan penting
Struktur akhir harus menyesuaikan instrumen akreditasi yang digunakan pada saat implementasi.

MOD-055 Monitoring dan Evaluasi
Fungsi utama
Memantau program dan kegiatan madrasah.
Fitur
* Program kerja.
* Indikator kinerja.
* Target.
* Realisasi.
* Penanggung jawab.
* Periode.
* Kendala.
* Tindak lanjut.
* Status.
* Dokumen pendukung.
* Persentase capaian.

MOD-056 Rencana Kerja Madrasah
Fungsi utama
Mengelola rencana kerja jangka menengah dan tahunan.
Fitur
* Program.
* Kegiatan.
* Sasaran.
* Indikator.
* Target.
* Jadwal.
* Penanggung jawab.
* Anggaran.
* Realisasi.
* Evaluasi.
* Dokumen.

KELOMPOK H
PORTAL, PELAPORAN, AUDIT, DAN PEMELIHARAAN
MOD-057 Dashboard
Fungsi utama
Menampilkan ringkasan informasi berdasarkan role.
Dashboard Kepala Madrasah
* Jumlah siswa.
* Kehadiran.
* Kinerja akademik.
* Pembayaran.
* Kondisi inventaris.
* Kelengkapan PKKM.
* Kelengkapan akreditasi.
* Aktivitas guru.
* Berita menunggu persetujuan.
Dashboard Guru
* Jadwal mengajar.
* Jurnal belum diisi.
* Nilai belum lengkap.
* Kelas yang diajar.
* Notifikasi.
Dashboard Wali Kelas
* Jumlah siswa.
* Kehadiran.
* Nilai belum lengkap.
* Pelanggaran.
* Prestasi.
* Tunggakan pembayaran.
Dashboard Bendahara
* Penerimaan hari ini.
* Tunggakan.
* Pembayaran per kelas.
* Transaksi terbaru.
Dashboard Tata Usaha
* Surat masuk.
* Surat keluar.
* Data siswa.
* Dokumen belum lengkap.

MOD-058 Portal Orang Tua
Fungsi utama
Memberikan akses informasi kepada orang tua.
Fitur
* Data anak.
* Kehadiran.
* Nilai yang sudah dipublikasikan.
* Rapor.
* Prestasi.
* Pelanggaran yang diizinkan.
* Tagihan.
* Pembayaran.
* Jadwal.
* Pengumuman.
* Dokumen yang diizinkan.
Aturan penting
Orang tua hanya boleh melihat siswa yang terhubung dengan akunnya.

MOD-059 Portal Siswa
Fungsi utama
Memberikan akses layanan mandiri kepada siswa.
Fitur
* Profil.
* Jadwal.
* Kehadiran.
* Nilai.
* Rapor.
* Prestasi.
* Tahfidz.
* Ekstrakurikuler.
* Portofolio.
* Pengumuman.
* Ebook.
Aturan penting
Siswa hanya boleh melihat data miliknya sendiri.

MOD-060 Portal Alumni dan Humas
Fungsi utama
Mengelola hubungan dengan alumni.
Fitur
* Data alumni.
* Tahun lulus.
* Riwayat pendidikan.
* Riwayat pekerjaan.
* Prestasi alumni.
* Tracer study.
* Kegiatan alumni.
* Berita alumni.
* Kerja sama.
* Kontak alumni.

MOD-061 Pusat Dokumen
Fungsi utama
Memberikan tempat terpusat untuk mencari dokumen.
Fitur
* Kategori dokumen.
* Pemilik dokumen.
* Modul asal.
* Tingkat akses.
* Tanggal upload.
* Versi dokumen.
* Status verifikasi.
* Pencarian.
* Filter.
* Preview.
* Download.
* Riwayat dokumen.
Catatan penting
Modul ini tidak menggantikan dokumen pada setiap modul.
Modul ini menjadi pusat pencarian dokumen dari berbagai bagian sistem.

MOD-062 Pusat Laporan
Fungsi utama
Menyediakan laporan lintas modul.
Fitur
* Laporan siswa.
* Laporan guru.
* Laporan akademik.
* Laporan kehadiran.
* Laporan pembayaran.
* Laporan kesiswaan.
* Laporan perpustakaan.
* Laporan inventaris.
* Laporan PKKM.
* Laporan akreditasi.
* Export Excel.
* Cetak PDF.
* Filter periode.

MOD-063 Import dan Export Data
Fungsi utama
Mempermudah pemindahan data dari dan ke Excel.
Fitur
* Download template.
* Import siswa.
* Import guru.
* Import nilai.
* Import pembayaran.
* Import inventaris.
* Preview data.
* Validasi data.
* Laporan baris gagal.
* Export Excel.
* Riwayat import.
Aturan penting
Import tidak boleh langsung memasukkan data tanpa validasi dan preview.

MOD-064 Activity Log
Fungsi utama
Mencatat aktivitas pengguna.
Contoh
* Login.
* Logout.
* Membuka laporan.
* Mengunggah dokumen.
* Membuat berita.
* Mencetak kwitansi.
* Menjalankan import.

MOD-065 Audit Log
Fungsi utama
Mencatat perubahan data penting.
Informasi yang dicatat
* Pengguna.
* Waktu.
* Jenis data.
* ID data.
* Nilai lama.
* Nilai baru.
* Alasan perubahan.
* Alamat IP jika tersedia.
Data prioritas audit
* Nilai.
* Pembayaran.
* Absensi.
* Rapor.
* Status siswa.
* Role dan permission.
* Berita.
* Eviden PKKM.
* Eviden akreditasi.
* Inventaris.

MOD-066 Backup dan Restore
Fungsi utama
Mengamankan data sistem.
Fitur
* Backup database.
* Backup file tertentu.
* Download backup.
* Daftar backup.
* Catatan waktu backup.
* Penghapusan backup lama.
* Restore dengan pembatasan.
* Log restore.
Aturan penting
Backup sebaiknya disimpan juga di luar server hosting.
Restore hanya boleh dilakukan oleh pengguna yang sangat terbatas.

MOD-067 Manajemen File
Fungsi utama
Mengatur seluruh file yang diunggah.
Fitur
* Validasi ekstensi.
* Validasi MIME type.
* Batas ukuran.
* Penamaan file aman.
* Folder berdasarkan modul.
* Folder berdasarkan tahun.
* Penghapusan file tidak terpakai.
* Informasi pemilik file.
* Status publik atau privat.
* Download melalui pemeriksaan permission.

MOD-068 Sistem Bantuan
Fungsi utama
Membantu pengguna memahami sistem.
Fitur
* Panduan penggunaan.
* Pertanyaan umum.
* Panduan per role.
* Video tutorial.
* Informasi versi aplikasi.
* Kontak administrator.
* Pelaporan masalah sederhana.

5. Ringkasan Jumlah Modul
SIM Madrasah memiliki 68 modul dan submodul utama.
Jumlah ini bukan berarti kita akan membangun 68 aplikasi terpisah.
Beberapa modul dapat ditempatkan dalam satu kelompok menu dan dikembangkan secara bersamaan.
Contoh:
Menu Akademik dapat memuat:
* Kurikulum.
* Kalender Pendidikan.
* Penugasan Mengajar.
* Jadwal.
* ATP.
* Jurnal Mengajar.
* Penilaian.
* Rapor.
Menu Kesiswaan dapat memuat:
* Prestasi.
* Pelanggaran.
* Konseling.
* Ekstrakurikuler.
* Tahfidz.
* Pembiasaan.
* Organisasi Siswa.

6. Hubungan Antar Modul
Modul tidak berdiri sendiri.
Contoh hubungan akademik
Tahun Ajaran dan Semester ↓ Kelas ↓ Penempatan Siswa ↓ Penugasan Guru ↓ Jadwal ↓ Jurnal Mengajar ↓ Nilai ↓ Rapor
Contoh hubungan pembayaran
Siswa ↓ Riwayat Kelas ↓ Tagihan ↓ Pembayaran ↓ Kwitansi ↓ Laporan ↓ Portal Orang Tua
Contoh hubungan portofolio
Siswa ↓ Absensi ↓ Nilai ↓ Prestasi ↓ Pelanggaran ↓ Tahfidz ↓ Pembayaran ↓ Portofolio Digital
Contoh hubungan berita
Kontributor ↓ Draft Berita ↓ Editor ↓ Kepala Madrasah jika diperlukan ↓ Publish ↓ Website Publik

7. Modul Inti yang Harus Dibangun Lebih Dahulu
Semua modul penting, tetapi tidak semuanya dapat dibangun pada waktu yang sama.
Prioritas 1: Fondasi sistem
1. Authentication.
2. Manajemen Pengguna.
3. Role dan Permission.
4. Pengaturan Sistem.
5. Tahun Ajaran dan Semester.
6. Struktur Organisasi.
7. Data Guru.
8. Data Siswa.
9. Data Orang Tua.
10. Mata Pelajaran.
11. Kelas.
12. Riwayat Kelas.
Tanpa bagian tersebut, modul lain tidak memiliki dasar data.
Prioritas 2: Operasional akademik
1. Penugasan Mengajar.
2. Jadwal Pelajaran.
3. Jurnal Mengajar.
4. Kehadiran Siswa.
5. Penilaian.
6. Rapor.
7. Dashboard.
8. Portal Siswa.
9. Portal Orang Tua.
Prioritas 3: Kesiswaan dan keuangan
1. Prestasi.
2. Pelanggaran.
3. Konseling.
4. Tahfidz.
5. Pembiasaan.
6. Ekstrakurikuler.
7. Tagihan.
8. Pembayaran.
9. Portofolio Digital Siswa.
Prioritas 4: Administrasi dan sumber daya
1. Surat Masuk.
2. Surat Keluar.
3. Arsip.
4. Kepegawaian.
5. Inventaris.
6. Laboratorium.
7. Perpustakaan.
Prioritas 5: Publikasi dan penerimaan siswa
1. Website Publik.
2. Portal Berita.
3. Galeri.
4. Agenda.
5. PPDB.
6. Alumni.
Prioritas 6: Penjaminan mutu
1. PKKM Center.
2. Akreditasi.
3. Monitoring dan Evaluasi.
4. Rencana Kerja Madrasah.
Prioritas 7: Pemeliharaan dan penguatan
1. Activity Log.
2. Audit Log.
3. Backup.
4. Restore.
5. Pusat Laporan.
6. Pusat Dokumen.
7. Import dan Export.
8. Sistem Bantuan.

8. Modul untuk Versi Minimum yang Dapat Digunakan
Versi minimum atau Minimum Viable Product bukan berarti sistem dibuat asal jadi.
Versi minimum berarti sistem sudah dapat dipakai untuk kegiatan utama, tetapi belum memuat seluruh fitur lanjutan.
Modul versi minimum
1. Authentication.
2. Pengguna.
3. Role dan Permission.
4. Pengaturan Madrasah.
5. Tahun Ajaran.
6. Semester.
7. Data Guru.
8. Data Siswa.
9. Data Orang Tua.
10. Mata Pelajaran.
11. Kelas.
12. Riwayat Kelas.
13. Penugasan Mengajar.
14. Jadwal.
15. Kehadiran Siswa.
16. Jurnal Mengajar.
17. Nilai.
18. Rapor.
19. Tagihan.
20. Pembayaran.
21. Dashboard.
22. Portal Orang Tua.
23. Portal Siswa.
24. Activity Log.
25. Backup.
Setelah versi minimum stabil, modul lain dapat ditambahkan.

9. Modul yang Tidak Boleh Menduplikasi Data
Beberapa modul hanya menampilkan data dari modul lain.
Portofolio Digital
Tidak menyimpan ulang nilai, absensi, prestasi, atau pembayaran.
Dashboard
Tidak menyimpan ulang jumlah siswa atau total pembayaran.
Dashboard menghitung dan merangkum data sumber.
Portal Orang Tua
Tidak membuat salinan data siswa.
Portal hanya menampilkan data anak yang diizinkan.
Portal Siswa
Tidak membuat database akademik terpisah.
Portal menampilkan data siswa yang sedang login.
Pusat Laporan
Tidak menjadi tempat input transaksi.
Pusat laporan hanya mengolah data dari modul lain.
Pusat Dokumen
Tidak menggandakan semua file.
Pusat dokumen menjadi indeks dan pencarian terpusat.

10. Keuntungan Pembagian Modul Ini
1. Struktur sistem menjadi jelas.
2. Pengembangan dapat dilakukan bertahap.
3. Setiap modul memiliki tanggung jawab.
4. Hak akses lebih mudah diatur.
5. Kesalahan lebih mudah ditemukan.
6. Modul dapat diuji secara terpisah.
7. Pengguna tidak melihat menu yang tidak dibutuhkan.
8. Sistem lebih mudah dikembangkan.
9. Duplikasi data dapat dikurangi.
10. Dokumentasi sistem lebih mudah dibuat.

11. Kekurangan dan Tantangannya
11.1 Jumlah modul cukup banyak
Solusinya adalah membangun berdasarkan prioritas.
11.2 Hubungan antarmodul kompleks
Solusinya adalah membuat Use Case Diagram, flowchart, ERD, dan desain database sebelum coding.
11.3 Hak akses dapat menjadi rumit
Solusinya adalah menggunakan role, permission, policy, dan pengujian akses.
11.4 Dashboard dapat menghasilkan query berat
Solusinya adalah menggunakan query ringkas, pagination, index, dan perhitungan yang efisien.
11.5 Penyimpanan file dapat membesar
Solusinya adalah membatasi ukuran, jenis, dan jumlah file serta menggunakan tautan untuk video besar.

12. Hasil Tahap 2
Pada tahap ini kita telah menetapkan:
1. Kelompok utama sistem.
2. Daftar seluruh modul.
3. Fungsi setiap modul.
4. Pengguna utama setiap modul.
5. Hubungan antarmodul.
6. Urutan prioritas pengembangan.
7. Modul versi minimum.
8. Modul yang hanya menampilkan data.
9. Batas tanggung jawab setiap bagian sistem.
Tahap ini penting karena daftar modul akan menjadi dasar untuk membuat Use Case Diagram pada tahap berikutnya.
Use Case Diagram akan menjelaskan:
* Siapa yang menggunakan sistem.
* Aktivitas apa yang dapat dilakukan.
* Modul apa yang digunakan.
* Batas hak akses setiap aktor.
