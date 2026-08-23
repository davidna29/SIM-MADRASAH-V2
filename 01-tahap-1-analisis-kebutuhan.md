# Tahap 1 — Analisis Kebutuhan SIM Madrasah

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 403–1340.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

=========================================================
TAHAP 1
ANALISIS KEBUTUHAN SIM MADRASAH
1. Konsep Sederhana
Analisis kebutuhan adalah proses memahami:
* Siapa yang akan menggunakan sistem.
* Masalah apa yang ingin diselesaikan.
* Data apa yang harus disimpan.
* Aktivitas apa yang harus didukung.
* Siapa yang boleh melihat atau mengubah data.
* Aturan apa yang tidak boleh dilanggar.
* Batas kemampuan teknologi yang digunakan.
Pada tahap ini, kita belum membuat database, migration, controller, atau tampilan.
Tujuannya adalah memastikan bahwa sistem yang dibangun benar-benar sesuai dengan kegiatan madrasah.
2. Analogi Sederhana
Membangun SIM Madrasah sama seperti membangun gedung madrasah.
Sebelum membuat fondasi, kita harus mengetahui:
* Berapa orang yang akan menggunakan gedung.
* Ruangan apa saja yang diperlukan.
* Siapa yang boleh memasuki setiap ruangan.
* Dokumen apa yang disimpan.
* Bagaimana alur pelayanan berjalan.
* Bagaimana gedung dapat dikembangkan.
Jika pembangunan langsung dimulai tanpa analisis, ruangan dapat salah ukuran, akses menjadi tidak teratur, dan biaya perbaikan menjadi besar.
Dalam pengembangan sistem:
* Analisis kebutuhan adalah gambar rencana.
* Database adalah fondasi.
* Laravel adalah struktur bangunan.
* Tampilan adalah bagian yang dilihat pengguna.
* Role dan permission adalah kunci setiap ruangan.
* Audit log adalah rekaman siapa yang masuk dan melakukan perubahan.

3. Definisi Sistem
SIM Madrasah adalah aplikasi web terintegrasi yang mengelola kegiatan akademik, administrasi, keuangan, kesiswaan, kepegawaian, dokumentasi, publikasi, dan pelayanan informasi madrasah.
Sistem ini bukan hanya website profil.
Sistem ini menjadi pusat pengelolaan data dan aktivitas harian madrasah.
Karakter utama sistem
1. Berbasis web.
2. Digunakan oleh banyak jenis pengguna.
3. Memiliki hak akses yang berbeda.
4. Menyimpan histori data siswa dan madrasah.
5. Mendukung kegiatan operasional setiap hari.
6. Menyediakan portal internal dan publik.
7. Dapat dijalankan pada shared hosting.
8. Menggunakan satu sumber data terintegrasi.
9. Menyimpan dokumen dan eviden digital.
10. Menyediakan pelacakan aktivitas pengguna.

4. Tujuan Utama Sistem
SIM Madrasah memiliki enam tujuan utama.
4.1 Mengintegrasikan data
Saat ini, data madrasah biasanya tersebar dalam:
* File Excel.
* Dokumen Word.
* Buku administrasi.
* Grup WhatsApp.
* Komputer guru.
* Komputer tata usaha.
* Arsip cetak.
* Google Drive pribadi.
SIM Madrasah akan menyatukan data tersebut dalam satu sistem.
4.2 Menjaga histori data
Sistem harus menyimpan perjalanan siswa sejak masuk sampai menjadi alumni.
Contohnya:
* Kelas yang pernah ditempati.
* Wali kelas setiap tahun.
* Nilai setiap semester.
* Kehadiran setiap semester.
* Prestasi.
* Pelanggaran.
* Konseling.
* Tahfidz.
* Pembayaran.
* Rapor.
* Dokumen.
Data lama tidak boleh diganti dengan data baru.
4.3 Mempermudah pekerjaan pengguna
Setiap pengguna hanya melihat menu yang berhubungan dengan tugasnya.
Contoh:
* Bendahara mengelola pembayaran.
* Guru mengelola pembelajaran dan nilai.
* Wali kelas memantau siswa dalam kelas.
* Kepala madrasah melihat laporan dan melakukan persetujuan.
* Orang tua melihat perkembangan anaknya.
* Siswa melihat jadwal, nilai, dan profilnya.
4.4 Mengurangi pengulangan input
Data siswa yang sudah tersimpan tidak perlu diketik berulang kali pada setiap modul.
Sebagai contoh, data siswa yang sama dapat digunakan untuk:
* Absensi.
* Nilai.
* Pembayaran.
* Pelanggaran.
* Prestasi.
* Konseling.
* Tahfidz.
* Rapor.
* Perpustakaan.
4.5 Mendukung pengambilan keputusan
Kepala madrasah dan wakamad membutuhkan informasi ringkas seperti:
* Persentase kehadiran.
* Perkembangan nilai.
* Kondisi pembayaran.
* Kelengkapan administrasi.
* Kelengkapan eviden PKKM.
* Kelengkapan akreditasi.
* Kondisi inventaris.
* Aktivitas guru.
4.6 Meningkatkan akuntabilitas
Sistem harus mencatat aktivitas penting.
Contohnya:
* Siapa yang memasukkan nilai.
* Siapa yang mengubah pembayaran.
* Siapa yang menyetujui berita.
* Siapa yang mengunggah eviden.
* Kapan data berubah.
* Data sebelum dan setelah perubahan.

5. Ruang Lingkup Sistem
5.1 Ruang lingkup internal
Sistem internal mendukung:
* Manajemen pengguna.
* Manajemen akademik.
* Manajemen siswa.
* Manajemen guru dan pegawai.
* Manajemen keuangan.
* Manajemen administrasi.
* Manajemen inventaris.
* Manajemen perpustakaan.
* Pengelolaan PKKM.
* Pengelolaan akreditasi.
* Pelaporan.
* Audit aktivitas.
5.2 Ruang lingkup eksternal
Sistem eksternal mendukung:
* Website publik madrasah.
* Portal berita.
* PPDB daring.
* Portal orang tua.
* Portal siswa.
* Profil digital siswa melalui QR Code.
* Informasi publik yang telah disetujui.
5.3 Batas versi pertama
Untuk menjaga proyek tetap realistis, versi pertama menggunakan beberapa batas berikut:
1. Sistem digunakan oleh satu madrasah.
2. Sistem tidak menggunakan aplikasi mobile khusus.
3. Sistem menggunakan desain responsif agar dapat dibuka melalui ponsel.
4. Sistem tidak memakai WebSocket.
5. Sistem tidak membutuhkan server queue permanen.
6. Sistem tidak memakai Redis.
7. Sistem tidak memakai Docker pada server produksi.
8. Sistem tidak menggunakan penyimpanan cloud sebagai kebutuhan utama.
9. Sistem belum menggunakan pembayaran otomatis melalui payment gateway.
10. Sistem belum menggunakan mesin fingerprint atau perangkat absensi biometrik.
11. Sistem belum menggunakan notifikasi WhatsApp otomatis.
12. Sistem belum menjadi sistem multi-madrasah atau SaaS.
Batas tersebut dapat dikembangkan setelah sistem utama berjalan stabil.

6. Arah Arsitektur Sistem
6.1 Arsitektur yang dipilih
Sistem akan menggunakan pendekatan modular monolith.
Artinya, seluruh fitur berada dalam satu aplikasi Laravel, tetapi setiap bagian dipisahkan berdasarkan tanggung jawabnya.
Contoh pemisahan:
* Akademik.
* Kesiswaan.
* Keuangan.
* Kepegawaian.
* Perpustakaan.
* Inventaris.
* PKKM.
* Akreditasi.
* Berita.
* PPDB.
6.2 Mengapa modular monolith dipilih
Pendekatan ini cocok karena:
* Lebih mudah dipelajari oleh pemula.
* Lebih mudah dipasang pada shared hosting.
* Tidak membutuhkan banyak server.
* Deployment lebih sederhana.
* Database dapat dikelola dalam satu tempat.
* Authentication dapat digunakan bersama.
* Role dan permission lebih mudah dikendalikan.
* Biaya operasional lebih rendah.
6.3 Kekurangannya
Modular monolith juga memiliki kekurangan:
* Ukuran aplikasi akan semakin besar.
* Perubahan pada satu aplikasi dapat memengaruhi bagian lain.
* Struktur kode harus dijaga dengan disiplin.
* Pengujian harus dilakukan sebelum deployment.
* Controller dapat menjadi terlalu besar jika logika tidak dipisahkan.
Untuk mengurangi masalah tersebut, kita akan menggunakan:
* Folder berdasarkan domain.
* Form Request untuk validasi.
* Service Class untuk logika bisnis yang kompleks.
* Policy untuk akses data.
* Query yang efisien.
* Komponen Blade yang dapat digunakan kembali.
* Standar penamaan yang konsisten.
Repository Pattern tidak akan digunakan pada semua fitur secara otomatis. Kita hanya akan menggunakannya jika benar-benar memberi manfaat.

7. Analisis Pengguna Sistem
7.1 Super Admin
Kebutuhan
* Mengelola seluruh pengguna.
* Mengelola role dan permission.
* Mengatur konfigurasi sistem.
* Melihat log aktivitas.
* Menjalankan backup dan restore.
* Mengakses seluruh modul.
Batas akses
Super Admin tidak seharusnya mengubah data transaksi tanpa alasan dan catatan yang jelas.

7.2 Kepala Madrasah
Kebutuhan
* Melihat dashboard pimpinan.
* Melihat laporan akademik.
* Melihat laporan keuangan.
* Melihat kehadiran guru dan siswa.
* Melihat kondisi inventaris.
* Memantau PKKM dan akreditasi.
* Memberikan persetujuan.
* Memantau kinerja setiap unit.
Karakter akses
Kepala madrasah lebih banyak membaca, memantau, dan menyetujui daripada memasukkan data harian.

7.3 Wakamad
Setiap wakamad memiliki wilayah kerja berbeda.
Wakamad Kurikulum
Membutuhkan akses terhadap:
* Kurikulum.
* Jadwal.
* Mata pelajaran.
* Pembagian tugas guru.
* ATP.
* Modul ajar.
* Jurnal mengajar.
* Nilai.
* Rapor.
* Kalender pendidikan.
Wakamad Kesiswaan
Membutuhkan akses terhadap:
* Data siswa.
* Prestasi.
* Pelanggaran.
* Ekstrakurikuler.
* Tahfidz.
* Pembiasaan.
* Organisasi siswa.
Wakamad Sarpras
Membutuhkan akses terhadap:
* Barang.
* Ruangan.
* Laboratorium.
* Kondisi fasilitas.
* Peminjaman barang.
* Pemeliharaan.
Wakamad Humas
Membutuhkan akses terhadap:
* Berita.
* Agenda.
* Kerja sama.
* Alumni.
* Publikasi.
* Dokumentasi.
* Hubungan masyarakat.

7.4 Tata Usaha
Kebutuhan
* Mengelola data administrasi siswa.
* Mengelola data guru dan pegawai.
* Mengelola surat masuk.
* Mengelola surat keluar.
* Mengelola SK.
* Mengelola MOU.
* Mengelola arsip.
* Mencetak laporan administratif.

7.5 Bendahara
Kebutuhan
* Menetapkan jenis tagihan.
* Mencatat pembayaran.
* Mencatat cicilan.
* Mencetak kwitansi.
* Melihat tunggakan.
* Membuat rekap per siswa.
* Membuat rekap per kelas.
* Membuat rekap bulanan dan tahunan.
* Melihat histori koreksi transaksi.

7.6 Wali Kelas
Kebutuhan
* Melihat siswa dalam kelas yang menjadi tanggung jawabnya.
* Memantau absensi.
* Memantau nilai.
* Memantau prestasi.
* Memantau pelanggaran.
* Memantau pembayaran.
* Memberikan catatan wali kelas.
* Memproses rapor sesuai hak akses.
Penugasan wali kelas harus terikat pada tahun ajaran dan kelas.

7.7 Guru Mata Pelajaran
Kebutuhan
* Melihat jadwal mengajar.
* Melihat kelas yang diajar.
* Mengelola jurnal mengajar.
* Mengelola absensi pembelajaran.
* Memasukkan nilai.
* Mengunggah ATP dan modul ajar.
* Melihat perkembangan siswa pada mata pelajarannya.
Guru hanya boleh mengubah nilai pada kelas dan mata pelajaran yang menjadi tugasnya.

7.8 Guru BK
Kebutuhan
* Mencatat konseling.
* Mencatat tindak lanjut.
* Melihat pelanggaran.
* Melihat perkembangan siswa.
* Membatasi data konseling yang bersifat rahasia.
Tidak semua catatan konseling boleh dilihat oleh semua guru.

7.9 Petugas Perpustakaan
Kebutuhan
* Mengelola buku.
* Mengelola anggota.
* Mengelola peminjaman.
* Mengelola pengembalian.
* Mengelola denda jika diterapkan.
* Mengelola ebook.
* Membuat laporan perpustakaan.

7.10 Petugas Laboratorium
Kebutuhan
* Mengelola alat laboratorium.
* Mengelola bahan.
* Mengelola peminjaman.
* Mencatat kerusakan.
* Mencatat pemeliharaan.
* Memantau stok.

7.11 Editor Berita
Kebutuhan
* Membuat atau menerima naskah.
* Mengedit berita.
* Memeriksa gambar.
* Mengatur kategori dan tag.
* Mengelola SEO.
* Mengajukan berita untuk persetujuan.
* Menjadwalkan publikasi.

7.12 Orang Tua
Kebutuhan
* Melihat data anak.
* Melihat kehadiran.
* Melihat nilai.
* Melihat prestasi.
* Melihat pelanggaran sesuai aturan.
* Melihat tagihan.
* Melihat pembayaran.
* Mengunduh rapor yang sudah diterbitkan.
Satu orang tua dapat terhubung dengan lebih dari satu siswa.
Satu siswa juga dapat memiliki lebih dari satu wali atau orang tua.

7.13 Siswa
Kebutuhan
* Melihat profil.
* Melihat jadwal.
* Melihat nilai yang sudah dipublikasikan.
* Melihat absensi.
* Melihat prestasi.
* Melihat portofolio.
* Mengunduh dokumen yang diizinkan.
Siswa tidak boleh mengubah data akademik resmi.

8. Perbedaan Role, Jabatan, dan Penugasan
Ketiga konsep ini tidak boleh dicampur.
8.1 Role
Role menentukan kelompok hak akses dalam sistem.
Contoh:
* Guru Mata Pelajaran.
* Bendahara.
* Tata Usaha.
* Kepala Madrasah.
8.2 Jabatan
Jabatan menjelaskan posisi organisasi seseorang.
Contoh:
* Kepala Madrasah.
* Wakamad Kurikulum.
* Kepala Laboratorium.
* Kepala Perpustakaan.
8.3 Penugasan
Penugasan menjelaskan tanggung jawab pada periode tertentu.
Contoh:
* Guru A menjadi wali kelas VII A pada tahun ajaran 2026/2027.
* Guru B mengajar Matematika kelas VIII B pada semester ganjil.
* Guru C menjadi pembina Pramuka pada tahun ajaran tertentu.
Satu pengguna dapat memiliki beberapa role dan penugasan.
Contoh:
Seorang guru dapat menjadi:
* Guru Mata Pelajaran.
* Wali Kelas.
* Editor Berita.
* Pembina ekstrakurikuler.

9. Kebutuhan Fungsional Utama
Kebutuhan fungsional menjelaskan apa yang harus dapat dilakukan oleh sistem.
9.1 Pengelolaan pengguna
Sistem harus dapat:
* Membuat akun.
* Mengaktifkan dan menonaktifkan akun.
* Mengatur role.
* Mengatur permission.
* Menghubungkan akun dengan guru, siswa, pegawai, atau orang tua.
* Mengganti dan mereset kata sandi.
* Mencatat login dan aktivitas penting.
9.2 Pengelolaan data master
Sistem harus menyimpan data dasar yang digunakan oleh banyak bagian.
Contohnya:
* Siswa.
* Guru.
* Pegawai.
* Mata pelajaran.
* Kelas.
* Ruangan.
* Tahun ajaran.
* Semester.
9.3 Pengelolaan transaksi
Sistem harus mencatat aktivitas yang terjadi dari waktu ke waktu.
Contohnya:
* Nilai.
* Absensi.
* Pembayaran.
* Prestasi.
* Pelanggaran.
* Konseling.
* Tahfidz.
* Peminjaman.
* Jurnal mengajar.
9.4 Pengelolaan dokumen
Sistem harus mendukung:
* Upload dokumen.
* Pengelompokan dokumen.
* Pembatasan akses.
* Informasi pengunggah.
* Tanggal unggah.
* Deskripsi dokumen.
* Jenis dokumen.
* Ukuran file.
* Status verifikasi.
* Penggantian dokumen tanpa menghilangkan histori penting.
9.5 Pelaporan
Sistem harus menghasilkan laporan berdasarkan:
* Tahun ajaran.
* Semester.
* Kelas.
* Siswa.
* Guru.
* Periode tanggal.
* Status.
* Jenis transaksi.
Laporan dapat ditampilkan pada layar dan diekspor jika diperlukan.
9.6 Workflow persetujuan
Beberapa proses membutuhkan tahapan persetujuan.
Contohnya:
* Berita.
* Rapor.
* Dokumen tertentu.
* Eviden PKKM.
* Koreksi pembayaran.
* Pengajuan administrasi.
Setiap workflow harus memiliki status yang jelas.
Contoh status berita:
1. Draft.
2. Diajukan.
3. Dalam review.
4. Memerlukan revisi.
5. Disetujui.
6. Dijadwalkan.
7. Dipublikasikan.
8. Diarsipkan.

10. Aturan Bisnis Utama
10.1 Tahun ajaran dan semester
Seluruh data transaksi akademik harus mengacu pada:
* Tahun ajaran.
* Semester.
Contohnya:
* Penempatan kelas siswa.
* Jadwal.
* Nilai.
* Absensi.
* Jurnal mengajar.
* Rapor.
* Tahfidz.
* Pembiasaan.
* Prestasi tertentu.
* Pelanggaran tertentu.
Data yang tidak bersifat semester tetap dapat menggunakan tanggal atau tahun ajaran sesuai konteksnya.
10.2 Riwayat kelas siswa
Saat siswa naik kelas, sistem tidak mengubah data kelas sebelumnya.
Sistem membuat catatan penempatan kelas baru.
Contoh:
Tahun Ajaran	Semester	Kelas
2026/2027	Ganjil	VII A
2026/2027	Genap	VII A
2027/2028	Ganjil	VIII B
Dengan cara ini, sistem tetap mengetahui seluruh perjalanan kelas siswa.
10.3 Status siswa
Status siswa dapat berubah menjadi:
* Calon siswa.
* Aktif.
* Nonaktif.
* Pindah.
* Lulus.
* Alumni.
* Keluar.
* Meninggal dunia.
Perubahan status harus memiliki:
* Tanggal.
* Alasan.
* Pengguna yang mengubah.
* Catatan pendukung.
10.4 Koreksi data transaksi
Sistem tidak boleh menghapus transaksi penting secara sembarangan.
Jika terjadi kesalahan:
* Pengguna membuat koreksi.
* Sistem mencatat alasan.
* Sistem mencatat pelaksana koreksi.
* Sistem menyimpan waktu perubahan.
* Sistem mempertahankan jejak audit.
Contohnya berlaku untuk:
* Pembayaran.
* Nilai.
* Absensi.
* Rapor.
* Inventaris.
* Peminjaman.
10.5 Pembayaran cicilan
Satu tagihan dapat memiliki:
* Belum dibayar.
* Dibayar sebagian.
* Lunas.
* Dibatalkan.
* Mendapat keringanan.
* Mendapat pembebasan.
Status pembayaran harus dihitung dari total pembayaran, bukan hanya diisi manual.
10.6 Publikasi nilai dan rapor
Nilai yang baru dimasukkan guru tidak otomatis terlihat oleh siswa atau orang tua.
Sistem membutuhkan status seperti:
* Draft.
* Lengkap.
* Diverifikasi.
* Dipublikasikan.
10.7 Kerahasiaan konseling
Catatan konseling dapat memiliki tingkat akses.
Contoh:
* Hanya Guru BK.
* Guru BK dan Kepala Madrasah.
* Guru BK dan Wali Kelas.
* Ringkasan untuk orang tua.
* Catatan internal yang tidak tampil pada portal.
10.8 Eviden PKKM dan akreditasi
Setiap eviden harus terhubung dengan:
* Standar.
* Komponen.
* Indikator.
* Tahun penilaian.
* Penanggung jawab.
* Jenis file.
* Status kelengkapan.
* Status verifikasi.
* Catatan evaluator.

11. Prinsip Data Historis
11.1 Data master dan data riwayat
Data master menyimpan identitas utama.
Contoh data master siswa:
* NIS.
* NISN.
* Nama.
* Tempat lahir.
* Tanggal lahir.
* Jenis kelamin.
Data riwayat menyimpan kejadian yang berubah berdasarkan waktu.
Contoh:
* Riwayat kelas.
* Riwayat nilai.
* Riwayat pembayaran.
* Riwayat pelanggaran.
* Riwayat konseling.
* Riwayat status siswa.
11.2 Mengapa dipisahkan
Jika kelas disimpan langsung pada tabel siswa, sistem hanya mengetahui kelas siswa saat ini.
Sistem tidak mengetahui:
* Kelas sebelumnya.
* Wali kelas sebelumnya.
* Tahun ajaran penempatan.
* Riwayat perpindahan kelas.
Karena itu, identitas siswa dan riwayat penempatan kelas harus dipisahkan.
11.3 Prinsip permanensi
Profil siswa bersifat permanen.
Transaksi baru menambah perjalanan siswa tanpa menghilangkan data sebelumnya.
Ketika siswa lulus:
* Data siswa tetap ada.
* Akun dapat dibatasi.
* Status berubah menjadi alumni.
* Riwayat tetap dapat ditampilkan.
* Portofolio tetap dapat ditelusuri sesuai izin.

12. Portofolio Digital Siswa
Portofolio digital menjadi pusat informasi perjalanan siswa.
12.1 Identitas portofolio
Setiap siswa memiliki:
* Identitas unik.
* Halaman profil internal.
* QR Code unik.
* Status akses.
* Riwayat lengkap.
12.2 Isi portofolio
Portofolio menggabungkan informasi dari berbagai bagian sistem.
Contohnya:
* Biodata.
* Riwayat kelas.
* Absensi.
* Nilai.
* Rapor.
* Prestasi.
* Pelanggaran.
* Konseling.
* Tahfidz.
* Pembiasaan.
* Ekstrakurikuler.
* Pembayaran.
* Dokumen.
Portofolio tidak perlu menyimpan ulang semua data tersebut.
Portofolio mengambil data dari sumber aslinya.
Pendekatan ini mencegah duplikasi dan perbedaan data.
12.3 Keamanan QR Code
QR Code tidak boleh langsung membuka seluruh data siswa secara publik.
QR Code harus diarahkan ke halaman yang memeriksa:
* Apakah halaman boleh diakses publik.
* Apakah pengguna sudah login.
* Data apa yang boleh ditampilkan.
* Apakah tautan masih aktif.
* Apakah token valid.
Informasi sensitif tidak boleh ditampilkan hanya karena seseorang memindai QR Code.

13. Kebutuhan Nonfungsional
Kebutuhan nonfungsional menjelaskan kualitas sistem.
13.1 Kemudahan penggunaan
Sistem harus:
* Menggunakan bahasa yang sederhana.
* Menampilkan menu sesuai tugas pengguna.
* Mengurangi jumlah langkah.
* Menggunakan tombol dan warna secara konsisten.
* Memberikan pesan kesalahan yang jelas.
* Memiliki tampilan yang nyaman pada ponsel.
* Memiliki fitur pencarian dan filter.
13.2 Performa
Sistem harus:
* Menggunakan pagination.
* Menghindari pemuatan seluruh data sekaligus.
* Menggunakan index database.
* Mengoptimalkan query relasi.
* Membatasi ukuran file.
* Mengompresi gambar jika diperlukan.
* Menampilkan dashboard berdasarkan data ringkas.
13.3 Keamanan
Sistem harus:
* Menggunakan authentication.
* Menggunakan authorization.
* Menggunakan validasi server.
* Melindungi form dengan CSRF.
* Membatasi tipe file.
* Membatasi ukuran file.
* Menggunakan password hashing.
* Mencatat aktivitas penting.
* Mencegah akses data melalui perubahan URL.
* Menjaga data sensitif siswa.
13.4 Keandalan
Sistem harus:
* Menyimpan data secara konsisten.
* Menggunakan database transaction untuk proses penting.
* Memiliki mekanisme backup.
* Mencegah duplikasi transaksi.
* Memberikan konfirmasi sebelum tindakan berisiko.
* Menangani kegagalan upload secara jelas.
13.5 Maintainability
Sistem harus:
* Menggunakan penamaan yang konsisten.
* Memisahkan logika bisnis dari controller.
* Menggunakan komponen tampilan berulang.
* Menggunakan migration dan seeder.
* Memiliki struktur folder yang jelas.
* Memiliki dokumentasi fitur.
* Menggunakan konfigurasi melalui file environment.
13.6 Kompatibilitas shared hosting
Sistem harus:
* Berjalan menggunakan PHP dan MySQL atau MariaDB.
* Tidak membutuhkan proses server permanen.
* Tidak bergantung pada Redis.
* Tidak bergantung pada WebSocket.
* Menyimpan file pada local storage.
* Menggunakan cron job hanya jika tersedia dan diperlukan.
* Dapat dipasang melalui struktur hosting yang tersedia.
* Memiliki strategi deployment tanpa Docker.
Kompatibilitas versi setiap package akan kita verifikasi kembali saat masuk ke tahap instalasi.

14. Kebutuhan UI/UX
14.1 Identitas visual
Sistem menggunakan:
* Warna hijau sebagai warna utama.
* Warna netral untuk latar belakang.
* Warna merah untuk kesalahan atau kondisi berbahaya.
* Warna kuning untuk peringatan.
* Warna hijau untuk kondisi berhasil atau lunas.
* Warna biru untuk informasi tertentu.
14.2 Prinsip antarmuka
Antarmuka harus:
* Minimalis.
* Tidak menampilkan terlalu banyak informasi dalam satu layar.
* Menggunakan menu berdasarkan role.
* Menggunakan tabel yang dapat difilter.
* Menggunakan form yang terbagi dalam bagian kecil.
* Memiliki breadcrumb.
* Memiliki konfirmasi tindakan.
* Menampilkan status secara visual.
* Memiliki tombol utama yang mudah ditemukan.
14.3 Pengguna yang menjadi acuan
Desain harus cocok untuk guru dan pegawai yang belum terbiasa menggunakan aplikasi kompleks.
Karena itu, sistem tidak boleh bergantung pada:
* Ikon tanpa teks.
* Istilah teknis.
* Form terlalu panjang.
* Navigasi bertingkat terlalu dalam.
* Tabel tanpa pencarian.
* Pesan kesalahan yang sulit dipahami.

15. Kebutuhan Audit dan Pelacakan
Sistem perlu membedakan dua jenis pencatatan.
15.1 Activity log
Activity log mencatat aktivitas pengguna.
Contoh:
* Pengguna login.
* Pengguna membuat berita.
* Pengguna mengunggah dokumen.
* Pengguna mencetak laporan.
* Pengguna mengubah role.
15.2 Audit log
Audit log mencatat perubahan data penting.
Contoh:
* Nilai berubah dari 75 menjadi 85.
* Pembayaran berubah dari Rp200.000 menjadi Rp250.000.
* Status berita berubah dari review menjadi publish.
* Status siswa berubah dari aktif menjadi pindah.
Audit log idealnya mencatat:
* Nama tabel atau jenis objek.
* ID data.
* Nilai lama.
* Nilai baru.
* Pengguna.
* Waktu.
* Alamat IP jika tersedia.
* Alasan perubahan untuk tindakan tertentu.

16. Asumsi Proyek
Untuk tahap awal, kita menggunakan asumsi berikut:
1. Sistem digunakan oleh satu madrasah.
2. Satu pengguna dapat memiliki lebih dari satu role.
3. Guru dapat memiliki beberapa penugasan.
4. Orang tua dapat memiliki beberapa anak.
5. Siswa dapat memiliki beberapa wali.
6. Tahun ajaran memiliki semester.
7. Madrasah menentukan semester aktif.
8. Data lama tidak ditimpa oleh data periode baru.
9. File disimpan pada server hosting.
10. Sistem menggunakan Bahasa Indonesia sebagai bahasa utama.
11. Sistem menggunakan zona waktu Indonesia yang ditentukan madrasah.
12. Semua nominal keuangan menggunakan rupiah.
13. Sistem berjalan melalui browser.
14. Sistem memiliki satu database utama.
15. Super Admin mengelola konfigurasi awal.
16. Kepala madrasah dapat memiliki fungsi persetujuan.
17. Format rapor dapat disesuaikan pada tahap pengembangan modul rapor.
18. Dokumen PKKM akan dianalisis saat file acuannya tersedia.
19. Dokumen akreditasi akan mengikuti struktur indikator yang berlaku saat modul tersebut dikembangkan.
20. Backup tetap perlu diunduh dan disimpan di luar hosting secara berkala.

17. Risiko Proyek dan Mitigasi
17.1 Ruang lingkup terlalu besar
Risiko
Sistem memiliki sangat banyak fitur. Pembangunan sekaligus dapat menimbulkan:
* Kode tidak teratur.
* Banyak bug.
* Proyek berhenti di tengah.
* Pengguna kesulitan belajar.
* Fitur selesai tetapi tidak stabil.
Mitigasi
Kita akan membangun sistem per tahap dan per modul.
Setiap modul harus melalui:
1. Analisis.
2. Desain data.
3. Desain alur.
4. Implementasi.
5. Pengujian.
6. Perbaikan.
7. Dokumentasi.
17.2 Hak akses terlalu kompleks
Risiko
Kesalahan permission dapat membuat pengguna melihat data yang tidak seharusnya.
Mitigasi
Kita akan membuat:
* Daftar role.
* Daftar permission.
* Matriks role dan permission.
* Policy untuk akses per record.
* Pengujian hak akses.
17.3 Data tidak konsisten
Risiko
Guru atau petugas dapat memasukkan data ganda atau salah.
Mitigasi
Sistem akan menggunakan:
* Validation.
* Unique constraint.
* Foreign key.
* Dropdown dari data master.
* Import template.
* Preview sebelum import.
* Laporan kesalahan import.
17.4 Penyimpanan file membesar
Risiko
Foto, video, PDF, dan dokumen dapat memenuhi kapasitas hosting.
Mitigasi
Sistem perlu menetapkan:
* Batas ukuran file.
* Jenis file yang diizinkan.
* Kompresi gambar.
* Kebijakan video.
* Pembersihan file sementara.
* Monitoring penggunaan storage.
Untuk video berukuran besar, sistem dapat menyimpan tautan video daripada mengunggah file langsung.
17.5 Keterbatasan shared hosting
Risiko
Shared hosting memiliki keterbatasan sumber daya dan konfigurasi.
Mitigasi
Kita akan:
* Menghindari proses permanen.
* Mengoptimalkan query.
* Menggunakan pagination.
* Membatasi proses berat.
* Menjalankan import secara terkendali.
* Menggunakan cron job hanya jika diperlukan.
* Menghindari teknologi yang tidak didukung.
17.6 Kerahasiaan data siswa
Risiko
Data akademik, konseling, pembayaran, dan identitas siswa termasuk data sensitif.
Mitigasi
Kita akan menggunakan:
* Role dan permission.
* Policy.
* Pembatasan data berdasarkan relasi.
* Audit log.
* Validasi akses file.
* URL dokumen yang tidak mudah ditebak.
* Pengaturan data publik dan privat.

18. Kriteria Keberhasilan Sistem
SIM Madrasah dianggap berhasil jika:
1. Setiap pengguna dapat masuk menggunakan akun masing-masing.
2. Pengguna hanya melihat fitur sesuai kewenangannya.
3. Data siswa dapat ditelusuri dari masuk sampai lulus.
4. Riwayat kelas tidak hilang saat siswa naik kelas.
5. Nilai dan absensi dapat dilihat berdasarkan tahun ajaran dan semester.
6. Pembayaran dapat dilacak per siswa dan per kelas.
7. Kepala madrasah dapat melihat ringkasan kondisi madrasah.
8. Orang tua hanya dapat melihat data anak yang terhubung.
9. Guru hanya dapat mengubah data sesuai penugasannya.
10. Dokumen PKKM dan akreditasi dapat dipetakan ke indikator.
11. Berita melewati workflow yang telah ditentukan.
12. Sistem dapat menghasilkan laporan.
13. Sistem mencatat perubahan data penting.
14. Sistem dapat dijalankan pada shared hosting.
15. Pengguna dapat mengoperasikan sistem melalui komputer dan ponsel.
16. Backup dapat dibuat dan dipulihkan dengan prosedur yang jelas.

19. Keputusan Awal yang Sudah Ditetapkan
Berdasarkan kebutuhan proyek, keputusan awalnya adalah:
Area	Keputusan
Jenis aplikasi	Sistem informasi web terintegrasi
Arsitektur	Modular monolith
Backend	Laravel 12
PHP	PHP 8.4
Tampilan	Blade dan Tailwind CSS
Database	MySQL atau MariaDB
Authentication	Laravel Breeze
Hosting	Shared hosting Hostinger atau Niagahoster
Penyimpanan	Local storage
Hak akses	RBAC dengan role dan permission
Histori	Tidak menimpa transaksi periode sebelumnya
Akademik	Berbasis tahun ajaran dan semester
Sistem pengguna	Multi-role
Cakupan organisasi	Satu madrasah untuk versi pertama
Aplikasi mobile	Belum dibuat, menggunakan desain responsif
Realtime	Tidak menggunakan WebSocket
Queue permanen	Tidak digunakan
Audit	Activity log dan audit log
Pengembangan	Bertahap per modul
Dokumentasi	Dijelaskan untuk pemula
20. Hasil Tahap 1
Tahap Analisis Kebutuhan telah menghasilkan:
1. Definisi SIM Madrasah.
2. Tujuan sistem.
3. Ruang lingkup internal dan eksternal.
4. Batas versi pertama.
5. Arah arsitektur.
6. Analisis kebutuhan setiap pengguna.
7. Perbedaan role, jabatan, dan penugasan.
8. Kebutuhan fungsional.
9. Aturan bisnis utama.
10. Prinsip histori data.
11. Konsep portofolio siswa.
12. Kebutuhan keamanan.
13. Kebutuhan UI/UX.
14. Kebutuhan audit.
15. Asumsi proyek.
16. Risiko dan mitigasi.
17. Kriteria keberhasilan.
18. Keputusan teknologi awal.
Mengapa tahap ini penting
Tahap ini menjadi dasar untuk menentukan:
* Modul yang benar-benar diperlukan.
* Alur kerja setiap pengguna.
* Struktur database.
* Hubungan antardata.
* Hak akses.
* Urutan pembangunan.
* Batas sistem.
* Standar keamanan.
Tanpa analisis kebutuhan, kita berisiko membuat fitur yang tidak sesuai dengan kegiatan madrasah atau membuat database yang sulit dikembangkan.
