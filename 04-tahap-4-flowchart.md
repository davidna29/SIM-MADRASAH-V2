# Tahap 4 — Flowchart

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 4508–6133.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

=========================================================
TAHAP 4
PERANCANGAN FLOWCHART SIM MADRASAH
1. Konsep Sederhana
Flowchart adalah diagram yang menunjukkan urutan proses di dalam sistem.
Flowchart menjelaskan:
1. Dari mana suatu proses dimulai.
2. Data apa yang dimasukkan.
3. Pemeriksaan apa yang dilakukan sistem.
4. Keputusan apa yang harus diambil.
5. Apa yang terjadi jika data benar.
6. Apa yang terjadi jika data salah.
7. Data apa yang disimpan.
8. Siapa yang melakukan proses.
9. Kapan proses berakhir.
Use Case Diagram pada Tahap 3 menjelaskan siapa melakukan apa.
Flowchart pada Tahap 4 menjelaskan bagaimana suatu proses berjalan dari awal sampai akhir.

2. Analogi Sederhana
Flowchart dapat dianalogikan sebagai prosedur pelayanan di kantor tata usaha.
Contohnya, saat siswa menyerahkan dokumen:
1. Petugas menerima dokumen.
2. Petugas memeriksa kelengkapan.
3. Jika lengkap, dokumen disimpan.
4. Jika tidak lengkap, dokumen dikembalikan.
5. Petugas mencatat hasil pemeriksaan.
6. Proses selesai.
Alur tersebut dapat digambarkan dengan simbol dan panah.
Dalam sistem:
* Pengguna memulai proses.
* Sistem menerima input.
* Sistem memeriksa data.
* Sistem mengambil keputusan.
* Sistem menyimpan data.
* Sistem memberikan hasil.

3. Tujuan Perancangan Flowchart
Flowchart SIM Madrasah dibuat untuk:
1. Menjelaskan urutan proses sistem.
2. Menentukan titik keputusan.
3. Menentukan kondisi berhasil dan gagal.
4. Menentukan proses yang membutuhkan validasi.
5. Menentukan proses yang membutuhkan persetujuan.
6. Menentukan data yang harus disimpan.
7. Menentukan proses yang menghasilkan audit log.
8. Menjadi dasar pembuatan ERD.
9. Menjadi dasar desain database.
10. Menjadi dasar pembuatan controller dan service.
11. Menjadi dasar pengujian sistem.
12. Mencegah proses bisnis terlewat saat coding.

4. Perbedaan Use Case Diagram dan Flowchart
Aspek	Use Case Diagram	Flowchart
Fokus	Hubungan aktor dengan fungsi	Urutan langkah suatu proses
Pertanyaan utama	Siapa dapat melakukan apa?	Bagaimana proses berjalan?
Komponen utama	Aktor dan use case	Proses, keputusan, input, output
Contoh	Guru memasukkan nilai	Langkah guru memasukkan sampai memublikasikan nilai
Digunakan untuk	Menentukan ruang lingkup akses	Menentukan logika proses
Dasar tahap berikutnya	Flowchart dan permission	ERD, database, controller, service
5. Simbol Flowchart yang Digunakan
5.1 Terminator
Bentuk: oval.
Fungsi:
* Menandai awal proses.
* Menandai akhir proses.
Contoh:
* Mulai.
* Selesai.
5.2 Process
Bentuk: persegi panjang.
Fungsi:
* Menunjukkan suatu aktivitas.
* Menunjukkan proses yang dilakukan pengguna atau sistem.
Contoh:
* Pengguna memasukkan email.
* Sistem menyimpan pembayaran.
5.3 Decision
Bentuk: belah ketupat.
Fungsi:
* Menunjukkan keputusan.
* Memiliki minimal dua kemungkinan hasil.
Contoh:
* Apakah password benar?
* Apakah data lengkap?
5.4 Input atau Output
Bentuk: jajar genjang.
Fungsi:
* Menunjukkan data yang dimasukkan.
* Menunjukkan hasil yang ditampilkan.
Contoh:
* Input nilai.
* Tampilkan kwitansi.
5.5 Database
Bentuk: silinder.
Fungsi:
* Menunjukkan penyimpanan atau pengambilan data.
Contoh:
* Simpan data siswa.
* Ambil data tagihan.
5.6 Document
Bentuk: dokumen.
Fungsi:
* Menunjukkan dokumen yang dibuat atau digunakan.
Contoh:
* Rapor PDF.
* Kwitansi.
* Surat keluar.
5.7 Arrow
Bentuk: panah.
Fungsi:
* Menunjukkan arah proses.
* Menghubungkan satu langkah dengan langkah lain.

6. Prinsip Perancangan Flowchart
Flowchart SIM Madrasah mengikuti prinsip berikut.
6.1 Satu proses harus memiliki awal dan akhir
Setiap alur harus menunjukkan:
* Titik mulai.
* Titik selesai.
* Hasil akhir yang jelas.
6.2 Setiap keputusan memiliki cabang
Contohnya:
* Ya.
* Tidak.
Keputusan tidak boleh berakhir tanpa arah lanjutan.
6.3 Validasi dilakukan sebelum penyimpanan
Data tidak langsung disimpan.
Urutan yang benar:
1. Pengguna mengisi data.
2. Sistem memvalidasi.
3. Sistem memeriksa kewenangan.
4. Sistem memeriksa aturan bisnis.
5. Sistem menyimpan data.
6.4 Proses penting menggunakan database transaction
Database transaction diperlukan untuk proses yang mengubah beberapa tabel sekaligus.
Contohnya:
* Konversi calon siswa menjadi siswa.
* Pembuatan tagihan massal.
* Pencatatan pembayaran.
* Penerbitan rapor.
* Kenaikan kelas.
* Restore database.
Jika salah satu langkah gagal, seluruh proses harus dibatalkan agar data tetap konsisten.
6.5 Proses sensitif menghasilkan audit log
Contohnya:
* Koreksi nilai.
* Koreksi pembayaran.
* Perubahan status siswa.
* Perubahan role.
* Pembatalan transaksi.
* Restore database.
6.6 Data histori tidak ditimpa
Untuk data berbasis periode, sistem membuat record baru.
Contohnya:
* Penempatan kelas.
* Penugasan guru.
* Nilai.
* Rapor.
* Jabatan.
* Status siswa.
* Pembayaran.

7. Tingkatan Flowchart
Flowchart SIM Madrasah dibagi menjadi tiga tingkat.
7.1 Flowchart Level 0
Menjelaskan alur umum SIM Madrasah.
7.2 Flowchart Level 1
Menjelaskan proses utama setiap kelompok modul.
7.3 Flowchart Level 2
Menjelaskan proses rinci pada fitur penting.
Pada Tahap 4 ini, flowchart yang dirancang meliputi:
1. Alur umum SIM Madrasah.
2. Login dan dashboard.
3. Pemeriksaan hak akses.
4. Tahun ajaran dan semester.
5. Penempatan siswa.
6. Penugasan guru dan jadwal.
7. Jurnal mengajar dan kehadiran.
8. Penilaian.
9. Rapor.
10. Tagihan dan pembayaran.
11. PPDB.
12. Portal berita.
13. Portofolio dan QR Code.
14. Konseling.
15. Perpustakaan.
16. Inventaris.
17. PKKM dan akreditasi.
18. Import data.
19. Akses dokumen.
20. Backup dan restore.
21. Audit log.

8. FLOWCHART 4.1 ALUR UMUM SIM MADRASAH
8.1 Tujuan
Menjelaskan alur pengguna mulai dari membuka sistem sampai menggunakan modul sesuai kewenangan.
flowchart TD
    A([Mulai]) --> B[Buka SIM Madrasah]
    B --> C{Pengguna membuka halaman publik?}

    C -- Ya --> D[Tampilkan website publik]
    D --> E{Ingin masuk ke portal internal?}
    E -- Tidak --> Z([Selesai])
    E -- Ya --> F[Halaman login]

    C -- Tidak --> F
    F --> G[Masukkan identitas dan password]
    G --> H[Validasi akun]

    H --> I{Akun valid dan aktif?}
    I -- Tidak --> J[Tampilkan pesan gagal]
    J --> F

    I -- Ya --> K[Ambil role, permission, dan penugasan]
    K --> L[Tampilkan dashboard sesuai akses]
    L --> M[Pilih modul]
    M --> N[Periksa permission dan hak atas data]

    N --> O{Akses diizinkan?}
    O -- Tidak --> P[Tampilkan akses ditolak]
    P --> L

    O -- Ya --> Q[Jalankan fungsi modul]
    Q --> R[Validasi data dan aturan bisnis]
    R --> S{Data valid?}

    S -- Tidak --> T[Tampilkan kesalahan]
    T --> Q

    S -- Ya --> U[Simpan atau tampilkan data]
    U --> V{Aktivitas perlu dicatat?}

    V -- Ya --> W[Simpan activity log atau audit log]
    V -- Tidak --> X[Tampilkan hasil]
    W --> X

    X --> Y{Melakukan aktivitas lain?}
    Y -- Ya --> L
    Y -- Tidak --> AA[Logout]
    AA --> Z
8.2 Penjelasan
1. Pengunjung dapat membuka website publik tanpa login.
2. Modul internal membutuhkan login.
3. Setelah login, sistem mengambil role, permission, jabatan, dan penugasan.
4. Sistem membangun menu berdasarkan hak akses.
5. Setiap permintaan tetap diperiksa kembali.
6. Sistem tidak hanya menyembunyikan menu.
7. Aktivitas penting dicatat.
8. Pengguna keluar melalui proses logout.

9. FLOWCHART 4.2 LOGIN DAN DASHBOARD
9.1 Tujuan
Memastikan hanya pengguna yang sah dan aktif dapat masuk ke sistem.
flowchart TD
    A([Mulai]) --> B[Buka halaman login]
    B --> C[Masukkan email atau username]
    C --> D[Masukkan password]
    D --> E[Klik tombol masuk]
    E --> F[Validasi format input]

    F --> G{Format input valid?}
    G -- Tidak --> H[Tampilkan pesan validasi]
    H --> B

    G -- Ya --> I[Cari akun pengguna]
    I --> J{Akun ditemukan?}
    J -- Tidak --> K[Tambah hitungan login gagal]
    K --> L[Tampilkan identitas atau password salah]
    L --> B

    J -- Ya --> M{Akun aktif?}
    M -- Tidak --> N[Tolak login]
    N --> O[Tampilkan akun tidak aktif]
    O --> Z([Selesai])

    M -- Ya --> P[Verifikasi password]
    P --> Q{Password benar?}
    Q -- Tidak --> K

    Q -- Ya --> R{Terlalu banyak percobaan gagal?}
    R -- Ya --> S[Blokir sementara]
    S --> Z

    R -- Tidak --> T[Buat session]
    T --> U[Catat waktu login]
    U --> V[Simpan activity log]
    V --> W[Ambil role dan permission]
    W --> X[Ambil penugasan aktif]
    X --> Y[Tampilkan dashboard sesuai role]
    Y --> Z
9.2 Aturan bisnis
1. Akun nonaktif tidak dapat login.
2. Password harus diverifikasi menggunakan hashing.
3. Sistem membatasi percobaan login.
4. Login berhasil dicatat dalam activity log.
5. Dashboard menyesuaikan role.
6. Pengguna multi-role dapat memperoleh gabungan menu.
7. Data yang ditampilkan tetap dibatasi melalui Policy.

10. FLOWCHART 4.3 PEMERIKSAAN HAK AKSES
10.1 Tujuan
Memastikan pengguna hanya mengakses fitur dan data yang sesuai kewenangannya.
flowchart TD
    A([Permintaan akses]) --> B[Periksa status login]
    B --> C{Sudah login?}

    C -- Tidak --> D[Arahkan ke login]
    D --> Z([Selesai])

    C -- Ya --> E[Periksa permission]
    E --> F{Memiliki permission?}

    F -- Tidak --> G[Tampilkan 403 akses ditolak]
    G --> H[Simpan activity log akses ditolak]
    H --> Z

    F -- Ya --> I[Periksa Policy]
    I --> J{Boleh mengakses record ini?}

    J -- Tidak --> G

    J -- Ya --> K{Perlu pemeriksaan penugasan?}
    K -- Ya --> L[Periksa kelas, mapel, periode, dan penugasan]
    L --> M{Penugasan sesuai?}
    M -- Tidak --> G
    M -- Ya --> N[Izinkan akses]

    K -- Tidak --> N
    N --> O[Jalankan proses]
    O --> Z
10.2 Contoh pemeriksaan
Guru memasukkan nilai
Sistem memeriksa:
* Guru memiliki permission nilai.input.
* Guru memiliki penugasan mengajar.
* Mata pelajaran sesuai.
* Kelas sesuai.
* Semester sesuai.
* Periode belum dikunci.
Orang tua melihat nilai
Sistem memeriksa:
* Orang tua sudah login.
* Akun terhubung dengan siswa.
* Nilai sudah dipublikasikan.
* Siswa yang dibuka adalah anak yang terhubung.

11. FLOWCHART 4.4 TAHUN AJARAN DAN SEMESTER
11.1 Tujuan
Mengelola periode akademik tanpa menghapus data lama.
flowchart TD
    A([Mulai]) --> B[Buka pengaturan periode]
    B --> C[Pilih tambah tahun ajaran]
    C --> D[Masukkan nama dan tanggal periode]
    D --> E[Tambahkan semester ganjil dan genap]
    E --> F[Validasi periode]

    F --> G{Periode valid?}
    G -- Tidak --> H[Tampilkan kesalahan]
    H --> D

    G -- Ya --> I[Simpan tahun ajaran]
    I --> J[Simpan semester]
    J --> K{Aktifkan sekarang?}

    K -- Tidak --> L[Simpan sebagai belum aktif]
    L --> Z([Selesai])

    K -- Ya --> M[Periksa periode aktif]
    M --> N{Ada periode aktif sebelumnya?}

    N -- Ya --> O[Nonaktifkan periode sebelumnya]
    N -- Tidak --> P[Aktifkan periode baru]

    O --> P
    P --> Q[Catat perubahan periode]
    Q --> R[Gunakan sebagai default transaksi baru]
    R --> Z
11.2 Aturan bisnis
1. Satu madrasah hanya memiliki satu semester aktif.
2. Tahun ajaran lama tidak boleh dihapus.
3. Semester lama dapat dikunci.
4. Penguncian tidak menghapus data.
5. Koreksi pada periode terkunci membutuhkan permission khusus.
6. Perubahan periode aktif dicatat dalam audit log.

12. FLOWCHART 4.5 PENEMPATAN DAN RIWAYAT KELAS SISWA
12.1 Tujuan
Mencatat kelas siswa untuk setiap periode tanpa menimpa histori.
flowchart TD
    A([Mulai]) --> B[Pilih tahun ajaran dan semester]
    B --> C[Pilih kelas tujuan]
    C --> D[Pilih siswa]
    D --> E[Periksa status siswa]

    E --> F{Siswa aktif?}
    F -- Tidak --> G[Tolak penempatan]
    G --> Z([Selesai])

    F -- Ya --> H[Periksa penempatan pada periode yang sama]
    H --> I{Sudah memiliki penempatan?}

    I -- Tidak --> J[Validasi kapasitas kelas]
    I -- Ya --> K{Pindah kelas?}

    K -- Tidak --> L[Tampilkan data sudah tersedia]
    L --> Z

    K -- Ya --> M[Masukkan alasan perpindahan]
    M --> N[Tutup status penempatan sebelumnya]
    N --> J

    J --> O{Kapasitas tersedia?}
    O -- Tidak --> P[Tampilkan kelas penuh]
    P --> Z

    O -- Ya --> Q[Buat record penempatan baru]
    Q --> R[Simpan tanggal dan pengguna]
    R --> S[Simpan audit log]
    S --> T[Tampilkan riwayat kelas]
    T --> Z
12.2 Proses kenaikan kelas
flowchart TD
    A([Mulai kenaikan kelas]) --> B[Pilih kelas asal]
    B --> C[Pilih tahun ajaran berikutnya]
    C --> D[Tampilkan daftar siswa]
    D --> E[Tentukan status setiap siswa]

    E --> F{Status siswa}
    F -- Naik kelas --> G[Pilih kelas tujuan]
    F -- Tinggal kelas --> H[Pilih kelas tingkat yang sama]
    F -- Lulus --> I[Ubah status menjadi lulus]
    F -- Pindah atau keluar --> J[Catat status dan alasan]

    G --> K[Buat penempatan baru]
    H --> K
    I --> L[Buat riwayat status siswa]
    J --> L

    K --> M[Simpan histori]
    L --> M
    M --> N[Data lama tetap dipertahankan]
    N --> Z([Selesai])

13. FLOWCHART 4.6 PENUGASAN GURU DAN JADWAL
13.1 Penugasan mengajar
flowchart TD
    A([Mulai]) --> B[Pilih periode]
    B --> C[Pilih guru]
    C --> D[Pilih mata pelajaran]
    D --> E[Pilih kelas]
    E --> F[Masukkan jumlah jam]
    F --> G[Periksa status guru]

    G --> H{Guru aktif?}
    H -- Tidak --> I[Tolak penugasan]
    I --> Z([Selesai])

    H -- Ya --> J[Periksa duplikasi penugasan]
    J --> K{Sudah ada?}

    K -- Ya --> L[Tampilkan penugasan sudah tersedia]
    L --> Z

    K -- Tidak --> M[Simpan penugasan]
    M --> N[Berikan akses jurnal, absensi, dan nilai]
    N --> O[Simpan riwayat penugasan]
    O --> Z
13.2 Penyusunan jadwal
flowchart TD
    A([Mulai]) --> B[Pilih periode]
    B --> C[Pilih penugasan mengajar]
    C --> D[Pilih hari]
    D --> E[Pilih jam pelajaran]
    E --> F[Pilih ruangan]

    F --> G[Periksa bentrok guru]
    G --> H{Guru bentrok?}
    H -- Ya --> I[Tampilkan konflik guru]
    I --> D

    H -- Tidak --> J[Periksa bentrok kelas]
    J --> K{Kelas bentrok?}
    K -- Ya --> L[Tampilkan konflik kelas]
    L --> D

    K -- Tidak --> M[Periksa bentrok ruangan]
    M --> N{Ruangan bentrok?}
    N -- Ya --> O[Tampilkan konflik ruangan]
    O --> F

    N -- Tidak --> P[Simpan jadwal]
    P --> Q[Publikasikan jadwal]
    Q --> R[Tampilkan pada portal guru dan siswa]
    R --> Z([Selesai])

14. FLOWCHART 4.7 JURNAL MENGAJAR DAN KEHADIRAN
14.1 Jurnal mengajar
flowchart TD
    A([Mulai]) --> B[Guru login]
    B --> C[Pilih jadwal mengajar]
    C --> D[Periksa penugasan]
    D --> E{Penugasan valid?}

    E -- Tidak --> F[Tolak akses]
    F --> Z([Selesai])

    E -- Ya --> G[Masukkan materi pembelajaran]
    G --> H[Masukkan tujuan dan metode]
    H --> I[Masukkan catatan kegiatan]
    I --> J[Unggah lampiran jika ada]
    J --> K[Validasi data]

    K --> L{Data lengkap?}
    L -- Tidak --> M[Tampilkan kesalahan]
    M --> G

    L -- Ya --> N[Simpan jurnal]
    N --> O[Ubah status jurnal menjadi terisi]
    O --> P[Simpan activity log]
    P --> Z
14.2 Kehadiran siswa
flowchart TD
    A([Mulai]) --> B[Pilih kelas atau jadwal]
    B --> C[Tampilkan daftar siswa]
    C --> D[Masukkan status kehadiran]
    D --> E[Hadir, sakit, izin, alfa, terlambat, atau pulang awal]
    E --> F[Tambahkan keterangan jika diperlukan]
    F --> G[Validasi data]

    G --> H{Semua siswa sudah diberi status?}
    H -- Tidak --> I[Tandai data belum lengkap]
    I --> D

    H -- Ya --> J[Simpan kehadiran]
    J --> K[Hitung rekap]
    K --> L[Kirim notifikasi internal jika diperlukan]
    L --> M[Simpan activity log]
    M --> Z([Selesai])
14.3 Koreksi kehadiran
1. Pengguna memilih data kehadiran.
2. Sistem memeriksa permission koreksi.
3. Pengguna memasukkan status baru.
4. Pengguna wajib memasukkan alasan.
5. Sistem menyimpan nilai lama.
6. Sistem menyimpan nilai baru.
7. Sistem mencatat audit log.

15. FLOWCHART 4.8 PENILAIAN
15.1 Tujuan
Mengelola nilai mulai dari input sampai dipublikasikan.
flowchart TD
    A([Mulai]) --> B[Guru memilih penugasan]
    B --> C[Periksa kelas, mapel, dan periode]
    C --> D{Penugasan valid?}

    D -- Tidak --> E[Tolak akses]
    E --> Z([Selesai])

    D -- Ya --> F[Pilih jenis penilaian]
    F --> G[Masukkan nama dan bobot penilaian]
    G --> H[Tampilkan daftar siswa]
    H --> I[Masukkan nilai]
    I --> J[Validasi rentang nilai]

    J --> K{Nilai valid?}
    K -- Tidak --> L[Tandai nilai bermasalah]
    L --> I

    K -- Ya --> M[Simpan sebagai draft]
    M --> N{Nilai sudah lengkap?}

    N -- Tidak --> O[Tetap berstatus draft]
    O --> Z

    N -- Ya --> P[Ajukan verifikasi]
    P --> Q[Wakamad memeriksa nilai]
    Q --> R{Nilai disetujui?}

    R -- Tidak --> S[Kembalikan untuk revisi]
    S --> I

    R -- Ya --> T[Ubah status menjadi terverifikasi]
    T --> U{Publikasikan sekarang?}

    U -- Tidak --> V[Simpan belum dipublikasikan]
    V --> Z

    U -- Ya --> W[Ubah status menjadi dipublikasikan]
    W --> X[Tampilkan pada portal siswa dan orang tua]
    X --> Y[Simpan audit log status]
    Y --> Z
15.2 Koreksi nilai setelah publikasi
flowchart TD
    A([Permintaan koreksi]) --> B[Periksa permission koreksi]
    B --> C{Diizinkan?}

    C -- Tidak --> D[Tolak koreksi]
    D --> Z([Selesai])

    C -- Ya --> E[Masukkan alasan koreksi]
    E --> F[Masukkan nilai baru]
    F --> G[Validasi nilai]
    G --> H[Simpan nilai lama]
    H --> I[Simpan nilai baru]
    I --> J[Ubah status ke menunggu verifikasi]
    J --> K[Simpan audit log]
    K --> L[Verifikasi ulang]
    L --> Z

16. FLOWCHART 4.9 RAPOR
16.1 Tujuan
Menghasilkan rapor resmi berdasarkan data yang sudah diverifikasi.
flowchart TD
    A([Mulai]) --> B[Wali kelas memilih periode]
    B --> C[Pilih kelas dan siswa]
    C --> D[Ambil nilai terverifikasi]
    D --> E[Ambil rekap kehadiran]
    E --> F[Ambil ekstrakurikuler]
    F --> G[Ambil prestasi]
    G --> H[Ambil tahfidz dan pembiasaan]
    H --> I[Wali kelas mengisi catatan]
    I --> J[Validasi kelengkapan rapor]

    J --> K{Rapor lengkap?}
    K -- Tidak --> L[Tampilkan bagian belum lengkap]
    L --> I

    K -- Ya --> M[Ajukan verifikasi]
    M --> N[Wakamad Kurikulum memeriksa]
    N --> O{Disetujui Wakamad?}

    O -- Tidak --> P[Kembalikan untuk perbaikan]
    P --> I

    O -- Ya --> Q{Persetujuan kepala diperlukan?}
    Q -- Ya --> R[Kepala Madrasah memeriksa]
    R --> S{Disetujui?}
    S -- Tidak --> P
    S -- Ya --> T[Terbitkan rapor]

    Q -- Tidak --> T
    T --> U[Buat snapshot rapor]
    U --> V[Generate PDF]
    V --> W[Simpan arsip rapor]
    W --> X[Kunci rapor terbit]
    X --> Y[Tampilkan pada portal]
    Y --> Z([Selesai])
16.2 Mengapa menggunakan snapshot rapor
Rapor harus menyimpan keadaan data saat diterbitkan.
Contohnya, jika nilai kemudian dikoreksi:
* Rapor lama tetap dapat dilacak.
* Rapor revisi memiliki versi baru.
* Sistem dapat mengetahui rapor mana yang resmi.
* Histori tidak hilang.

17. FLOWCHART 4.10 TAGIHAN DAN PEMBAYARAN
17.1 Pembuatan tagihan
flowchart TD
    A([Mulai]) --> B[Bendahara memilih jenis tagihan]
    B --> C[Pilih tahun ajaran dan periode]
    C --> D[Pilih target tagihan]
    D --> E{Target tagihan}

    E -- Seluruh siswa --> F[Ambil seluruh siswa aktif]
    E -- Per kelas --> G[Pilih kelas]
    E -- Per siswa --> H[Pilih siswa]

    F --> I[Tentukan nominal]
    G --> I
    H --> I

    I --> J[Tentukan jatuh tempo]
    J --> K[Periksa tagihan ganda]
    K --> L{Ada duplikasi?}

    L -- Ya --> M[Tampilkan daftar duplikasi]
    M --> N[Pilih lewati atau perbaiki]
    N --> K

    L -- Tidak --> O[Buat tagihan]
    O --> P[Simpan dalam database transaction]
    P --> Q[Tampilkan hasil pembuatan tagihan]
    Q --> Z([Selesai])
17.2 Pencatatan pembayaran
flowchart TD
    A([Mulai]) --> B[Cari siswa]
    B --> C[Tampilkan daftar tagihan]
    C --> D[Pilih tagihan]
    D --> E[Tampilkan total, pembayaran, dan sisa]
    E --> F[Masukkan nominal pembayaran]
    F --> G[Pilih metode pembayaran]
    G --> H[Validasi nominal]

    H --> I{Nominal valid?}
    I -- Tidak --> J[Tampilkan kesalahan]
    J --> F

    I -- Ya --> K[Mulai database transaction]
    K --> L[Buat record pembayaran]
    L --> M[Hitung total pembayaran]
    M --> N[Hitung sisa tagihan]
    N --> O[Tentukan status]

    O --> P{Status pembayaran}
    P -- Belum lunas --> Q[Status cicilan]
    P -- Sama dengan tagihan --> R[Status lunas]
    P -- Lebih besar --> S[Tolak atau minta koreksi]

    S --> T[Rollback transaction]
    T --> F

    Q --> U[Buat nomor transaksi]
    R --> U
    U --> V[Commit transaction]
    V --> W[Simpan activity log]
    W --> X[Tawarkan cetak kwitansi]
    X --> Z([Selesai])
17.3 Koreksi atau pembatalan pembayaran
1. Bendahara memilih transaksi.
2. Sistem memeriksa permission.
3. Pengguna wajib memasukkan alasan.
4. Sistem menyimpan nilai lama.
5. Sistem membuat transaksi koreksi atau pembatalan.
6. Sistem menghitung ulang tagihan.
7. Sistem menyimpan audit log.
8. Sistem tidak menghapus jejak transaksi lama.

18. FLOWCHART 4.11 PPDB
18.1 Pendaftaran
flowchart TD
    A([Mulai]) --> B[Pendaftar membuka informasi PPDB]
    B --> C[Pilih periode dan jalur]
    C --> D[Buat akun pendaftar]
    D --> E[Verifikasi akun jika diperlukan]
    E --> F[Login pendaftar]
    F --> G[Isi biodata calon siswa]
    G --> H[Isi data orang tua]
    H --> I[Isi asal sekolah]
    I --> J[Unggah dokumen]
    J --> K[Simpan sebagai draft]
    K --> L{Kirim pendaftaran?}

    L -- Tidak --> M[Simpan untuk dilanjutkan]
    M --> Z([Selesai])

    L -- Ya --> N[Validasi seluruh data]
    N --> O{Data lengkap?}

    O -- Tidak --> P[Tampilkan data yang belum lengkap]
    P --> G

    O -- Ya --> Q[Buat nomor pendaftaran]
    Q --> R[Ubah status menjadi diajukan]
    R --> S[Cetak bukti pendaftaran]
    S --> Z
18.2 Verifikasi dan seleksi
flowchart TD
    A([Mulai]) --> B[Panitia membuka pendaftaran]
    B --> C[Periksa biodata dan dokumen]
    C --> D{Dokumen lengkap?}

    D -- Tidak --> E[Status perlu perbaikan]
    E --> F[Kirim catatan kepada pendaftar]
    F --> Z([Selesai])

    D -- Ya --> G[Status administrasi terverifikasi]
    G --> H[Masukkan nilai seleksi jika ada]
    H --> I[Proses pemeringkatan]
    I --> J[Periksa kuota]
    J --> K[Tentukan hasil]

    K --> L{Hasil}
    L -- Diterima --> M[Status diterima]
    L -- Cadangan --> N[Status cadangan]
    L -- Tidak diterima --> O[Status tidak diterima]

    M --> P[Publikasikan hasil]
    N --> P
    O --> P
    P --> Z
18.3 Konversi menjadi siswa
flowchart TD
    A([Mulai]) --> B[Pilih pendaftar diterima]
    B --> C[Periksa status daftar ulang]
    C --> D{Daftar ulang lengkap?}

    D -- Tidak --> E[Tolak konversi]
    E --> Z([Selesai])

    D -- Ya --> F[Periksa NISN dan identitas]
    F --> G{Data siswa sudah ada?}

    G -- Ya --> H[Tampilkan potensi duplikasi]
    H --> Z

    G -- Tidak --> I[Mulai database transaction]
    I --> J[Buat master siswa]
    J --> K[Buat relasi orang tua]
    K --> L[Pindahkan referensi dokumen]
    L --> M[Buat riwayat status aktif]
    M --> N[Buat akun jika diperlukan]
    N --> O[Tandai pendaftar telah dikonversi]
    O --> P[Commit transaction]
    P --> Q[Simpan audit log]
    Q --> Z

19. FLOWCHART 4.12 PORTAL BERITA
flowchart TD
    A([Mulai]) --> B[Kontributor membuat berita]
    B --> C[Isi judul, ringkasan, dan isi]
    C --> D[Pilih kategori dan tag]
    D --> E[Unggah gambar]
    E --> F[Isi data SEO]
    F --> G[Simpan sebagai draft]
    G --> H{Ajukan review?}

    H -- Tidak --> Z([Selesai])

    H -- Ya --> I[Status diajukan]
    I --> J[Editor melakukan review]
    J --> K{Hasil review}

    K -- Revisi --> L[Editor memberikan catatan]
    L --> M[Status memerlukan revisi]
    M --> B

    K -- Disetujui --> N{Persetujuan kepala diperlukan?}
    N -- Ya --> O[Kepala Madrasah memeriksa]
    O --> P{Disetujui kepala?}
    P -- Tidak --> L
    P -- Ya --> Q[Pilih jadwal publikasi]

    N -- Tidak --> Q
    Q --> R{Publikasi langsung?}
    R -- Ya --> S[Status dipublikasikan]
    R -- Tidak --> T[Status dijadwalkan]

    T --> U[Scheduler memeriksa waktu publikasi]
    U --> V{Waktu sudah tiba?}
    V -- Belum --> U
    V -- Ya --> S

    S --> W[Tampilkan pada website publik]
    W --> X[Simpan waktu dan pengguna publikasi]
    X --> Z
19.1 Catatan teknis shared hosting
Penjadwalan publikasi dapat menggunakan:
* Cron job Laravel Scheduler jika tersedia.
* Pemeriksaan status saat website diakses.
* Perintah scheduler yang dijalankan berkala.
Sistem tidak membutuhkan queue worker permanen.

20. FLOWCHART 4.13 PORTOFOLIO DAN QR CODE SISWA
20.1 Portofolio digital
flowchart TD
    A([Pengguna membuka portofolio]) --> B[Periksa status login]
    B --> C{Sudah login?}

    C -- Tidak --> D[Tampilkan data publik terbatas]
    D --> Z([Selesai])

    C -- Ya --> E[Periksa identitas dan role]
    E --> F{Jenis pengguna}

    F -- Siswa --> G[Periksa siswa adalah pemilik data]
    F -- Orang tua --> H[Periksa relasi orang tua dan siswa]
    F -- Wali kelas --> I[Periksa penugasan wali kelas]
    F -- Pimpinan atau petugas --> J[Periksa permission]

    G --> K{Akses valid?}
    H --> K
    I --> K
    J --> K

    K -- Tidak --> L[Tolak akses]
    L --> Z

    K -- Ya --> M[Ambil biodata]
    M --> N[Ambil riwayat kelas]
    N --> O[Ambil kehadiran]
    O --> P[Ambil nilai dan rapor]
    P --> Q[Ambil prestasi dan ekstrakurikuler]
    Q --> R[Ambil tahfidz dan pembiasaan]
    R --> S[Ambil pembayaran sesuai izin]
    S --> T[Saring data konseling]
    T --> U[Tampilkan portofolio]
    U --> Z
20.2 Pemindaian QR Code
flowchart TD
    A([QR Code dipindai]) --> B[Baca token]
    B --> C[Cari token pada database]
    C --> D{Token ditemukan?}

    D -- Tidak --> E[Tampilkan QR tidak valid]
    E --> Z([Selesai])

    D -- Ya --> F{Token aktif?}
    F -- Tidak --> G[Tampilkan QR tidak aktif]
    G --> Z

    F -- Ya --> H{Token kedaluwarsa?}
    H -- Ya --> I[Tampilkan QR kedaluwarsa]
    I --> Z

    H -- Tidak --> J[Periksa pengaturan akses]
    J --> K{Akses publik diizinkan?}

    K -- Ya --> L[Tampilkan profil publik terbatas]
    L --> Z

    K -- Tidak --> M[Minta pengguna login]
    M --> N[Periksa role dan relasi]
    N --> O{Akses diizinkan?}

    O -- Tidak --> P[Tolak akses]
    O -- Ya --> Q[Tampilkan data sesuai permission]
    P --> Z
    Q --> Z

21. FLOWCHART 4.14 BIMBINGAN DAN KONSELING
flowchart TD
    A([Mulai]) --> B[Guru BK memilih siswa]
    B --> C[Periksa kewenangan]
    C --> D{Akses diizinkan?}

    D -- Tidak --> E[Tolak akses]
    E --> Z([Selesai])

    D -- Ya --> F[Pilih jenis konseling]
    F --> G[Masukkan tanggal dan topik]
    G --> H[Masukkan permasalahan]
    H --> I[Masukkan hasil asesmen]
    I --> J[Masukkan tindakan dan tindak lanjut]
    J --> K[Tentukan tingkat kerahasiaan]
    K --> L[Validasi data]

    L --> M{Data lengkap?}
    M -- Tidak --> N[Tampilkan kesalahan]
    N --> F

    M -- Ya --> O[Simpan catatan konseling]
    O --> P[Atur daftar pihak yang boleh melihat]
    P --> Q[Simpan audit log]
    Q --> Z
21.1 Tingkat kerahasiaan
Contoh tingkat akses:
1. Hanya Guru BK.
2. Guru BK dan Kepala Madrasah.
3. Guru BK dan Wali Kelas.
4. Ringkasan untuk orang tua.
5. Catatan internal rahasia.
Sistem harus menyaring catatan sebelum menampilkannya.

22. FLOWCHART 4.15 PERPUSTAKAAN
22.1 Peminjaman buku
flowchart TD
    A([Mulai]) --> B[Petugas mencari anggota]
    B --> C[Periksa status anggota]
    C --> D{Anggota aktif?}

    D -- Tidak --> E[Tolak peminjaman]
    E --> Z([Selesai])

    D -- Ya --> F[Periksa jumlah pinjaman aktif]
    F --> G{Melebihi batas?}

    G -- Ya --> H[Tolak peminjaman]
    H --> Z

    G -- Tidak --> I[Pindai atau pilih buku]
    I --> J[Periksa ketersediaan]
    J --> K{Buku tersedia?}

    K -- Tidak --> L[Tampilkan buku tidak tersedia]
    L --> Z

    K -- Ya --> M[Tentukan tanggal jatuh tempo]
    M --> N[Simpan transaksi peminjaman]
    N --> O[Kurangi stok tersedia]
    O --> P[Cetak atau tampilkan bukti]
    P --> Z
22.2 Pengembalian buku
flowchart TD
    A([Mulai]) --> B[Cari transaksi peminjaman]
    B --> C[Masukkan tanggal pengembalian]
    C --> D[Hitung keterlambatan]
    D --> E{Terlambat?}

    E -- Tidak --> F[Status dikembalikan]
    E -- Ya --> G{Madrasah menerapkan denda?}

    G -- Tidak --> F
    G -- Ya --> H[Hitung denda]
    H --> I[Catat pembayaran atau status denda]
    I --> F

    F --> J[Periksa kondisi buku]
    J --> K{Buku rusak atau hilang?}

    K -- Ya --> L[Catat kerusakan atau kehilangan]
    K -- Tidak --> M[Tambahkan stok tersedia]

    L --> N[Simpan tindak lanjut]
    M --> O[Simpan transaksi pengembalian]
    N --> O
    O --> Z([Selesai])

23. FLOWCHART 4.16 INVENTARIS DAN PEMELIHARAAN
23.1 Pencatatan barang
flowchart TD
    A([Mulai]) --> B[Masukkan data barang]
    B --> C[Pilih kategori]
    C --> D[Masukkan sumber dana]
    D --> E[Masukkan jumlah dan harga]
    E --> F[Pilih lokasi]
    F --> G[Unggah foto dan dokumen]
    G --> H[Validasi data]

    H --> I{Data valid?}
    I -- Tidak --> J[Tampilkan kesalahan]
    J --> B

    I -- Ya --> K[Buat kode barang]
    K --> L[Simpan data inventaris]
    L --> M[Buat QR Code barang]
    M --> N[Simpan activity log]
    N --> Z([Selesai])
23.2 Mutasi barang
flowchart TD
    A([Mulai]) --> B[Pilih barang]
    B --> C[Pilih lokasi tujuan]
    C --> D[Masukkan tanggal dan alasan]
    D --> E[Periksa status barang]
    E --> F{Barang dapat dipindahkan?}

    F -- Tidak --> G[Tolak mutasi]
    G --> Z([Selesai])

    F -- Ya --> H[Simpan lokasi lama]
    H --> I[Buat record mutasi]
    I --> J[Perbarui lokasi aktif barang]
    J --> K[Simpan audit log]
    K --> Z
23.3 Pemeliharaan barang
1. Petugas memilih barang.
2. Sistem menampilkan kondisi terakhir.
3. Petugas mencatat kerusakan.
4. Petugas menentukan tindakan.
5. Petugas mencatat biaya.
6. Petugas mencatat penyedia jasa.
7. Sistem menyimpan riwayat pemeliharaan.
8. Sistem memperbarui kondisi barang.
9. Sistem mencatat audit log.

24. FLOWCHART 4.17 PKKM DAN AKREDITASI
24.1 Pengelolaan eviden
flowchart TD
    A([Mulai]) --> B[Pilih periode penilaian]
    B --> C[Pilih standar atau komponen]
    C --> D[Pilih indikator]
    D --> E[Periksa penanggung jawab]
    E --> F{Pengguna berwenang?}

    F -- Tidak --> G[Tolak akses]
    G --> Z([Selesai])

    F -- Ya --> H[Pilih jenis eviden]
    H --> I[Unggah file atau masukkan tautan]
    I --> J[Masukkan deskripsi]
    J --> K[Validasi file]

    K --> L{File valid?}
    L -- Tidak --> M[Tampilkan kesalahan]
    M --> H

    L -- Ya --> N[Simpan eviden]
    N --> O[Status menunggu verifikasi]
    O --> P[Tim melakukan pemeriksaan]
    P --> Q{Hasil verifikasi}

    Q -- Ditolak --> R[Masukkan catatan perbaikan]
    R --> S[Status perlu perbaikan]
    S --> H

    Q -- Diterima --> T[Status terverifikasi]
    T --> U[Hitung persentase kelengkapan]
    U --> V[Perbarui dashboard]
    V --> Z
24.2 Perhitungan kelengkapan
Persentase kelengkapan tidak hanya berdasarkan jumlah file.
Sistem dapat menggunakan status:
* Belum diisi.
* Sudah diisi.
* Menunggu verifikasi.
* Perlu perbaikan.
* Terverifikasi.
Contoh perhitungan:
Persentase kelengkapan =
Jumlah indikator terverifikasi
dibagi
Jumlah seluruh indikator wajib
dikali 100 persen

25. FLOWCHART 4.18 IMPORT DATA
25.1 Tujuan
Mencegah file Excel langsung masuk ke database tanpa pemeriksaan.
flowchart TD
    A([Mulai]) --> B[Unduh template]
    B --> C[Isi data pada template]
    C --> D[Unggah file]
    D --> E[Validasi jenis dan ukuran file]

    E --> F{File valid?}
    F -- Tidak --> G[Tolak file]
    G --> Z([Selesai])

    F -- Ya --> H[Baca header kolom]
    H --> I{Header sesuai template?}

    I -- Tidak --> J[Tampilkan kesalahan format]
    J --> Z

    I -- Ya --> K[Baca setiap baris]
    K --> L[Validasi data]
    L --> M[Periksa duplikasi]
    M --> N[Kelompokkan data valid dan gagal]
    N --> O[Tampilkan preview]

    O --> P{Pengguna menyetujui import?}
    P -- Tidak --> Q[Batalkan proses]
    Q --> Z

    P -- Ya --> R[Mulai database transaction]
    R --> S[Import data valid]
    S --> T{Terjadi kesalahan kritis?}

    T -- Ya --> U[Rollback transaction]
    U --> V[Tampilkan kegagalan]
    V --> Z

    T -- Tidak --> W[Commit transaction]
    W --> X[Simpan riwayat import]
    X --> Y[Sediakan laporan baris gagal]
    Y --> Z
25.2 Aturan import
1. Template harus memiliki versi.
2. Header harus sesuai.
3. Data harus melalui preview.
4. Data gagal tidak boleh menghentikan seluruh proses kecuali terjadi kesalahan kritis.
5. Pengguna dapat mengunduh laporan kesalahan.
6. Riwayat import harus mencatat:
    * Nama file.
    * Pengguna.
    * Waktu.
    * Jumlah berhasil.
    * Jumlah gagal.
    * Jenis data.

26. FLOWCHART 4.19 AKSES DOKUMEN DAN FILE
flowchart TD
    A([Permintaan file]) --> B[Periksa identitas file]
    B --> C{File ditemukan?}

    C -- Tidak --> D[Tampilkan file tidak ditemukan]
    D --> Z([Selesai])

    C -- Ya --> E[Periksa status file]
    E --> F{File publik?}

    F -- Ya --> G[Tampilkan atau unduh file]
    G --> Z

    F -- Tidak --> H[Periksa status login]
    H --> I{Sudah login?}

    I -- Tidak --> J[Arahkan ke login]
    J --> Z

    I -- Ya --> K[Periksa permission]
    K --> L[Periksa relasi pengguna dengan data]
    L --> M{Akses diizinkan?}

    M -- Tidak --> N[Tolak akses]
    N --> O[Simpan activity log]
    O --> Z

    M -- Ya --> P[Catat akses file jika diperlukan]
    P --> G
26.1 Aturan penyimpanan file
1. Nama file asli tidak digunakan langsung sebagai nama penyimpanan.
2. Sistem membuat nama file unik.
3. File disimpan berdasarkan modul dan periode.
4. File privat tidak boleh diakses melalui URL langsung.
5. Download file privat harus melalui controller.
6. Controller memeriksa permission sebelum mengirim file.
7. MIME type dan ekstensi harus divalidasi.
8. Ukuran file harus dibatasi.

27. FLOWCHART 4.20 BACKUP DAN RESTORE
27.1 Backup
flowchart TD
    A([Mulai]) --> B[Super Admin memilih backup]
    B --> C[Pilih jenis backup]
    C --> D{Jenis backup}

    D -- Database --> E[Proses dump database]
    D -- File --> F[Proses arsip file]
    D -- Database dan file --> G[Proses keduanya]

    E --> H{Proses berhasil?}
    F --> H
    G --> H

    H -- Tidak --> I[Catat kegagalan]
    I --> J[Tampilkan pesan gagal]
    J --> Z([Selesai])

    H -- Ya --> K[Buat nama backup]
    K --> L[Simpan informasi backup]
    L --> M[Simpan activity log]
    M --> N[Sediakan tombol download]
    N --> Z
27.2 Restore
flowchart TD
    A([Mulai]) --> B[Super Admin memilih restore]
    B --> C[Periksa permission khusus]
    C --> D{Permission valid?}

    D -- Tidak --> E[Tolak restore]
    E --> Z([Selesai])

    D -- Ya --> F[Pilih file backup]
    F --> G[Validasi file backup]
    G --> H{File valid?}

    H -- Tidak --> I[Tampilkan backup tidak valid]
    I --> Z

    H -- Ya --> J[Tampilkan peringatan risiko]
    J --> K[Masukkan password dan konfirmasi]
    K --> L{Konfirmasi valid?}

    L -- Tidak --> M[Batalkan restore]
    M --> Z

    L -- Ya --> N[Buat backup sebelum restore]
    N --> O[Aktifkan mode pemeliharaan]
    O --> P[Jalankan restore]
    P --> Q{Restore berhasil?}

    Q -- Tidak --> R[Pulihkan keadaan sebelumnya]
    R --> S[Catat kegagalan]
    S --> T[Nonaktifkan mode pemeliharaan]
    T --> Z

    Q -- Ya --> U[Periksa integritas data]
    U --> V[Catat audit log]
    V --> W[Nonaktifkan mode pemeliharaan]
    W --> X[Tampilkan restore berhasil]
    X --> Z
27.3 Catatan shared hosting
Restore melalui aplikasi memiliki risiko tinggi.
Pada implementasi awal:
* Backup dapat dibuat dan diunduh.
* Restore aplikasi dibatasi.
* Restore dapat dilakukan melalui prosedur administrator.
* Backup eksternal tetap wajib dilakukan secara berkala.

28. FLOWCHART 4.21 AUDIT LOG
flowchart TD
    A([Aktivitas perubahan data]) --> B[Periksa apakah data termasuk data sensitif]
    B --> C{Perlu audit log?}

    C -- Tidak --> D[Simpan activity log jika diperlukan]
    D --> Z([Selesai])

    C -- Ya --> E[Ambil nilai sebelum perubahan]
    E --> F[Jalankan perubahan]
    F --> G[Ambil nilai setelah perubahan]
    G --> H[Ambil identitas pengguna]
    H --> I[Ambil waktu dan alamat IP]
    I --> J[Ambil alasan perubahan]
    J --> K[Simpan audit log]
    K --> L[Lanjutkan proses utama]
    L --> Z
28.1 Data yang masuk audit log
Audit log dapat menyimpan:
* Nama modul.
* Jenis data.
* ID data.
* Aksi.
* Nilai lama.
* Nilai baru.
* Pengguna.
* Waktu.
* Alamat IP.
* User agent.
* Alasan.
* Referensi transaksi.
28.2 Data prioritas
Audit log wajib untuk:
1. Nilai.
2. Rapor.
3. Pembayaran.
4. Absensi.
5. Status siswa.
6. Penempatan kelas.
7. Role dan permission.
8. Berita.
9. Dokumen PKKM.
10. Dokumen akreditasi.
11. Inventaris.
12. Restore database.

29. ALUR STATUS UTAMA
29.1 Status berita
Draft
↓
Diajukan
↓
Dalam Review
↓
Memerlukan Revisi
↓
Diajukan Kembali
↓
Disetujui
↓
Dijadwalkan
↓
Dipublikasikan
↓
Diarsipkan
29.2 Status nilai
Draft
↓
Lengkap
↓
Diajukan
↓
Diverifikasi
↓
Dipublikasikan
Jika terdapat kesalahan:
Diajukan
↓
Perlu Perbaikan
↓
Draft
29.3 Status rapor
Draft
↓
Diajukan
↓
Diverifikasi
↓
Disetujui
↓
Diterbitkan
↓
Diarsipkan
29.4 Status tagihan
Belum Bayar
↓
Cicilan
↓
Lunas
Status alternatif:
* Dibebaskan.
* Dibatalkan.
* Mendapat keringanan.
29.5 Status eviden
Belum Diisi
↓
Menunggu Verifikasi
↓
Terverifikasi
Jika tidak sesuai:
Menunggu Verifikasi
↓
Perlu Perbaikan
↓
Menunggu Verifikasi
29.6 Status PPDB
Draft
↓
Diajukan
↓
Diverifikasi
↓
Mengikuti Seleksi
↓
Diterima atau Cadangan atau Tidak Diterima
↓
Daftar Ulang
↓
Dikonversi Menjadi Siswa

30. HUBUNGAN ANTARFLOWCHART
30.1 Alur akademik
Tahun Ajaran dan Semester
↓
Kelas dan Rombel
↓
Penempatan Siswa
↓
Penugasan Mengajar
↓
Jadwal
↓
Jurnal Mengajar
↓
Kehadiran
↓
Penilaian
↓
Rapor
↓
Portal Siswa dan Orang Tua
30.2 Alur keuangan
Data Siswa
↓
Penempatan Kelas
↓
Jenis Tagihan
↓
Tagihan Siswa
↓
Pembayaran
↓
Kwitansi
↓
Laporan
↓
Portal Orang Tua
30.3 Alur PPDB
Periode PPDB
↓
Pendaftaran
↓
Verifikasi
↓
Seleksi
↓
Pengumuman
↓
Daftar Ulang
↓
Konversi Data
↓
Data Siswa Aktif
30.4 Alur portofolio
Data Siswa
↓
Riwayat Kelas
↓
Kehadiran
↓
Nilai
↓
Rapor
↓
Prestasi
↓
Tahfidz
↓
Pembayaran
↓
Portofolio Digital
30.5 Alur PKKM dan akreditasi
Periode
↓
Standar atau Komponen
↓
Indikator
↓
Penanggung Jawab
↓
Eviden
↓
Verifikasi
↓
Persentase Kelengkapan
↓
Laporan

31. PENANGANAN KESALAHAN
Setiap flowchart harus memiliki alur kesalahan.
31.1 Kesalahan validasi
Contoh:
* Kolom wajib kosong.
* Format tanggal salah.
* Nominal tidak valid.
* Nilai di luar rentang.
* File terlalu besar.
Tindakan sistem:
1. Tidak menyimpan data.
2. Menampilkan pesan yang jelas.
3. Mempertahankan input pengguna jika aman.
4. Menunjukkan bagian yang salah.
31.2 Kesalahan hak akses
Contoh:
* Guru membuka kelas yang tidak diajar.
* Orang tua membuka siswa yang tidak terhubung.
* Pengguna tanpa permission mengubah pembayaran.
Tindakan sistem:
1. Menolak akses.
2. Menampilkan halaman 403.
3. Mencatat percobaan akses jika diperlukan.
31.3 Kesalahan database
Tindakan sistem:
1. Membatalkan database transaction.
2. Menampilkan pesan umum.
3. Tidak menampilkan detail teknis kepada pengguna.
4. Menyimpan detail kesalahan dalam log aplikasi.
31.4 Kesalahan upload
Tindakan sistem:
1. Memeriksa ekstensi.
2. Memeriksa MIME type.
3. Memeriksa ukuran.
4. Menghapus file sementara jika proses gagal.
5. Tidak membuat record dokumen sebelum file berhasil disimpan.

32. KEPUTUSAN DESAIN FLOWCHART
Area	Keputusan
Jenis flowchart	Flowchart proses bisnis dan sistem
Jumlah flowchart utama	21 flowchart
Proses autentikasi	Terpusat
Pemeriksaan akses	Permission, Policy, relasi, dan penugasan
Penyimpanan histori	Record baru, bukan menimpa data lama
Proses penting	Menggunakan database transaction
Perubahan sensitif	Menghasilkan audit log
Proses dokumen	Validasi sebelum penyimpanan
Import Excel	Preview dan validasi sebelum import
Pembayaran	Status dihitung oleh sistem
Nilai	Menggunakan alur draft sampai publish
Rapor	Menggunakan snapshot dan arsip PDF
QR Code	Menggunakan token dan pemeriksaan akses
Backup	Dapat diunduh dan disimpan eksternal
Restore	Dibatasi untuk Super Admin tertentu
Penjadwalan berita	Menggunakan scheduler tanpa queue permanen
33. KEUNTUNGAN PERANCANGAN FLOWCHART
1. Urutan proses menjadi jelas.
2. Titik validasi dapat diketahui.
3. Titik persetujuan dapat diketahui.
4. Hak akses dapat diperiksa pada tahap yang tepat.
5. Risiko kehilangan histori berkurang.
6. Proses database transaction dapat direncanakan.
7. Kebutuhan tabel mulai terlihat.
8. Relasi antarmodul mulai terlihat.
9. Controller dapat dibuat lebih terarah.
10. Service class dapat digunakan pada proses kompleks.
11. Pengujian dapat mengikuti alur utama dan alternatif.
12. Pengguna pemula dapat memahami proses sebelum melihat kode.

34. KEKURANGAN DAN TANTANGAN
34.1 Jumlah flowchart cukup banyak
Hal ini terjadi karena SIM Madrasah memiliki banyak modul.
Solusi:
* Kelompokkan flowchart berdasarkan domain.
* Fokus pada alur utama.
* Buat flowchart rinci saat modul akan dikembangkan.
34.2 Beberapa proses berubah sesuai kebijakan madrasah
Contohnya:
* Persetujuan berita.
* Persetujuan rapor.
* Denda perpustakaan.
* Aturan kenaikan kelas.
* Bentuk seleksi PPDB.
Solusi:
* Gunakan konfigurasi.
* Jangan menanam aturan yang terlalu kaku dalam kode.
* Simpan kebijakan tertentu pada tabel pengaturan.
34.3 Proses lintas modul cukup kompleks
Contohnya, rapor menggunakan banyak sumber data.
Solusi:
* Gunakan Service Class.
* Gunakan database transaction.
* Gunakan query yang terkontrol.
* Hindari duplikasi data.

35. HASIL TAHAP 4
Tahap Perancangan Flowchart telah menghasilkan:
1. Konsep flowchart.
2. Perbedaan Use Case Diagram dan flowchart.
3. Simbol flowchart.
4. Prinsip perancangan flowchart.
5. Flowchart umum SIM Madrasah.
6. Flowchart login.
7. Flowchart pemeriksaan hak akses.
8. Flowchart tahun ajaran dan semester.
9. Flowchart riwayat kelas siswa.
10. Flowchart penugasan guru.
11. Flowchart jadwal.
12. Flowchart jurnal mengajar.
13. Flowchart kehadiran.
14. Flowchart penilaian.
15. Flowchart rapor.
16. Flowchart tagihan dan pembayaran.
17. Flowchart PPDB.
18. Flowchart portal berita.
19. Flowchart portofolio dan QR Code.
20. Flowchart konseling.
21. Flowchart perpustakaan.
22. Flowchart inventaris.
23. Flowchart PKKM dan akreditasi.
24. Flowchart import.
25. Flowchart akses dokumen.
26. Flowchart backup dan restore.
27. Flowchart audit log.
28. Alur status utama.
29. Hubungan antarflowchart.
30. Aturan penanganan kesalahan.
31. Keputusan desain proses.
Mengapa Tahap 4 Penting
Tahap ini menjelaskan bagaimana setiap proses berjalan sebelum database dirancang.
Flowchart membantu menentukan:
* Entitas yang harus dibuat.
* Data yang harus disimpan.
* Relasi yang diperlukan.
* Status yang harus tersedia.
* Proses yang membutuhkan transaction.
* Proses yang membutuhkan audit log.
* Proses yang membutuhkan persetujuan.
* Proses yang membutuhkan validasi khusus.
Hasil Tahap 4 akan menjadi dasar penyusunan Tahap 5: Entity Relationship Diagram atau ERD.
