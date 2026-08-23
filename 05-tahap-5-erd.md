# Tahap 5 — Entity Relationship Diagram / ERD

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 6134–7277.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

=========================================================
TAHAP 5
PERANCANGAN ENTITY RELATIONSHIP DIAGRAM SIM MADRASAH
1. Konsep Sederhana
Entity Relationship Diagram atau ERD adalah diagram yang menggambarkan:
1. Data apa saja yang disimpan.
2. Hubungan antara satu data dan data lainnya.
3. Data mana yang menjadi induk.
4. Data mana yang menjadi turunan.
5. Apakah satu data dapat memiliki satu atau banyak hubungan.
6. Bagaimana histori data tetap dipertahankan.
Pada tahap sebelumnya, Use Case Diagram menjelaskan siapa yang menggunakan sistem. Flowchart menjelaskan bagaimana proses berjalan. ERD menjelaskan data apa yang diperlukan agar seluruh proses tersebut dapat dijalankan.
Flowchart Tahap 4 memang dirancang sebagai dasar untuk menentukan entitas, data yang disimpan, relasi, status, proses transaksi, audit, dan validasi.
Pada Tahap 5 ini, kita belum menentukan seluruh tipe data MySQL secara terperinci. Penentuan seperti VARCHAR(100), BIGINT, DECIMAL, index, foreign key, dan migration akan dibahas pada Tahap 6, yaitu Desain Database.

2. Analogi Sederhana
ERD dapat dianalogikan sebagai lemari arsip madrasah.
Dalam lemari tersebut terdapat beberapa kelompok dokumen:
* Map identitas siswa.
* Map identitas guru.
* Map tahun ajaran.
* Map kelas.
* Map nilai.
* Map pembayaran.
* Map prestasi.
* Map pelanggaran.
* Map surat.
* Map inventaris.
Dokumen tersebut tidak boleh disimpan tanpa hubungan yang jelas.
Contohnya, dokumen nilai harus menjawab:
* Nilai milik siswa siapa?
* Nilai untuk mata pelajaran apa?
* Diberikan oleh guru siapa?
* Nilai untuk kelas mana?
* Nilai pada tahun ajaran apa?
* Nilai pada semester apa?
* Apakah nilai masih draft atau sudah dipublikasikan?
ERD memastikan setiap data memiliki tempat dan hubungan yang benar.

3. Tujuan Perancangan ERD
ERD SIM Madrasah dibuat untuk:
1. Menentukan entitas yang dibutuhkan.
2. Menentukan hubungan antardata.
3. Menentukan data master dan data transaksi.
4. Menentukan data yang harus memiliki histori.
5. Mencegah duplikasi data.
6. Mendukung role dan permission.
7. Mendukung akses berdasarkan penugasan.
8. Mendukung transaksi akademik berdasarkan periode.
9. Mendukung portofolio digital siswa.
10. Mendukung audit perubahan.
11. Menjadi dasar desain tabel pada Tahap 6.
12. Menjadi dasar pembuatan model dan relasi Eloquent.
13. Menjadi dasar migration.
14. Menjadi dasar foreign key dan index.
15. Menjadi dasar validasi integritas data.

4. Istilah Dasar ERD
4.1 Entity
Entity adalah objek atau kelompok data yang perlu disimpan.
Contoh:
* Pengguna.
* Siswa.
* Guru.
* Tahun ajaran.
* Semester.
* Kelas.
* Mata pelajaran.
* Nilai.
* Pembayaran.
Dalam database, entity biasanya diwujudkan sebagai tabel.
4.2 Attribute
Attribute adalah informasi yang dimiliki suatu entity.
Contoh entity students memiliki attribute:
* NIS.
* NISN.
* Nama.
* Tanggal masuk.
* Status siswa.
Pada Tahap 5, kita hanya menetapkan attribute utama. Rincian lengkapnya akan dibuat pada Tahap 6.
4.3 Primary Key
Primary key adalah identitas unik sebuah record.
Contoh:
students.id
users.id
academic_years.id
subjects.id
Setiap siswa memiliki id yang berbeda.
4.4 Foreign Key
Foreign key adalah penghubung antara satu tabel dan tabel lainnya.
Contoh:
semesters.academic_year_id
Kolom tersebut menunjukkan bahwa sebuah semester menjadi bagian dari tahun ajaran tertentu.
4.5 Cardinality
Cardinality menjelaskan jumlah hubungan antardata.
Notasi	Arti
1 : 1	Satu data berhubungan dengan satu data
1 : N	Satu data berhubungan dengan banyak data
N : M	Banyak data berhubungan dengan banyak data
Contoh:
* Satu tahun ajaran memiliki banyak semester.
* Satu siswa memiliki banyak riwayat kelas.
* Satu pengguna dapat memiliki banyak role.
* Satu role dapat dimiliki banyak pengguna.

5. Prinsip Utama ERD SIM Madrasah
5.1 Akun dan identitas orang dipisahkan
Tabel users digunakan untuk login.
Tabel identitas digunakan untuk menyimpan data pribadi dan profesional.
Karena itu:
* users bukan tabel guru.
* users bukan tabel siswa.
* users bukan tabel orang tua.
* Satu orang dapat memiliki identitas dan peran yang berbeda.
Pemisahan ini sesuai dengan kebutuhan bahwa akun pengguna tidak sama dengan data guru, siswa, pegawai, atau orang tua. Sistem juga harus mendukung satu pengguna dengan beberapa role dan penugasan.
5.2 Role, jabatan, dan penugasan dipisahkan
Ketiga konsep tersebut tidak boleh disimpan pada satu kolom.
Contoh:
* Role: Guru Mata Pelajaran.
* Jabatan: Wakamad Kurikulum.
* Penugasan: Mengajar Matematika VIII A semester ganjil.
Seseorang dapat memiliki beberapa role dan beberapa penugasan pada periode yang sama.
5.3 Tahun ajaran dan semester menjadi pusat histori
Transaksi akademik harus terhubung dengan periode.
Contohnya:
* Penempatan siswa.
* Penugasan mengajar.
* Jadwal.
* Jurnal mengajar.
* Kehadiran.
* Nilai.
* Rapor.
* Tahfidz.
* Pembiasaan.
Akses guru juga harus dibatasi berdasarkan kelas, mata pelajaran, tahun ajaran, semester, dan penugasan aktif.
5.4 Data lama tidak ditimpa
Saat siswa naik kelas, sistem tidak mengubah kelas lama pada tabel siswa.
Sistem membuat record baru pada tabel penempatan siswa.
Prinsip yang sama berlaku untuk:
* Jabatan.
* Penugasan guru.
* Status siswa.
* Nilai.
* Rapor.
* Pembayaran.
* Riwayat inventaris.
Proses berbasis periode harus membuat record baru agar histori tetap tersedia.
5.5 Modul tampilan tidak membuat salinan data
Beberapa modul hanya membaca dan merangkum data.
Modul tersebut meliputi:
* Dashboard.
* Portofolio digital.
* Portal siswa.
* Portal orang tua.
* Pusat laporan.
* Pusat dokumen.
Portofolio tidak membuat salinan nilai, kehadiran, prestasi, dan pembayaran. Dashboard juga tidak menyimpan ulang jumlah siswa atau total pembayaran.
5.6 Data transaksi penting tidak dihapus langsung
Transaksi penting menggunakan:
* Status pembatalan.
* Record koreksi.
* Record revisi.
* Riwayat perubahan.
* Audit log.
Prinsip ini diterapkan pada:
* Nilai.
* Pembayaran.
* Rapor.
* Kehadiran.
* Status siswa.
* Inventaris.

6. Mengapa ERD Dibagi Berdasarkan Domain
SIM Madrasah memiliki 68 modul dan submodul. Menempatkan seluruh entity dalam satu diagram akan menghasilkan diagram yang sangat besar dan sulit dipahami.
Karena itu, ERD dibagi menjadi delapan kelompok:
1. Fondasi, pengguna, dan organisasi.
2. Periode dan data akademik.
3. Kesiswaan dan portofolio.
4. Keuangan siswa.
5. Website publik, berita, dan PPDB.
6. Tata usaha dan kepegawaian.
7. Inventaris, laboratorium, dan perpustakaan.
8. PKKM, akreditasi, dokumen, dan audit.
Semua kelompok tetap berada dalam satu database MySQL atau MariaDB.

7. ERD 5.1 FONDASI, PENGGUNA, DAN ORGANISASI
7.1 Entity utama
Entity	Fungsi
madrasahs	Menyimpan identitas madrasah
settings	Menyimpan konfigurasi sistem
people	Menyimpan identitas umum seseorang
users	Menyimpan akun login
employees	Menyimpan data guru dan tenaga kependidikan
students	Menyimpan identitas utama siswa
guardians	Menyimpan identitas orang tua atau wali
student_guardians	Menghubungkan siswa dengan orang tua atau wali
roles	Menyimpan role
permissions	Menyimpan permission
user_roles	Menghubungkan pengguna dengan role
role_permissions	Menghubungkan role dengan permission
organizational_units	Menyimpan unit organisasi
positions	Menyimpan jabatan
employee_position_histories	Menyimpan riwayat jabatan pegawai
7.2 Mengapa menggunakan tabel people
Data dasar seperti nama, tempat lahir, tanggal lahir, jenis kelamin, alamat, dan kontak dapat dimiliki oleh:
* Guru.
* Pegawai.
* Siswa.
* Orang tua.
Daripada menyimpan identitas umum secara tidak teratur, kita menggunakan tabel people.
Hubungannya:
people
├── users
├── employees
├── students
└── guardians
Satu orang dapat menjadi pegawai sekaligus orang tua siswa.
Dengan desain ini, sistem tidak perlu membuat identitas orang yang sama dua kali.
7.3 Hubungan siswa dan orang tua
Hubungan siswa dengan orang tua bersifat banyak ke banyak.
Alasannya:
* Satu siswa dapat memiliki ayah, ibu, dan wali.
* Satu orang tua dapat memiliki lebih dari satu anak.
Karena itu diperlukan tabel penghubung student_guardians.
Attribute pentingnya antara lain:
* student_id
* guardian_id
* relationship_type
* is_primary_contact
* is_financial_responsible
* can_access_portal
* started_at
* ended_at
7.4 Diagram konseptual
erDiagram
    MADRASAHS ||--o{ SETTINGS : memiliki
    MADRASAHS ||--o{ ORGANIZATIONAL_UNITS : memiliki

    PEOPLE ||--o| USERS : memiliki_akun
    PEOPLE ||--o| EMPLOYEES : menjadi
    PEOPLE ||--o| STUDENTS : menjadi
    PEOPLE ||--o| GUARDIANS : menjadi

    STUDENTS ||--o{ STUDENT_GUARDIANS : memiliki
    GUARDIANS ||--o{ STUDENT_GUARDIANS : mendampingi

    USERS ||--o{ USER_ROLES : memiliki
    ROLES ||--o{ USER_ROLES : diberikan

    ROLES ||--o{ ROLE_PERMISSIONS : memiliki
    PERMISSIONS ||--o{ ROLE_PERMISSIONS : diberikan

    ORGANIZATIONAL_UNITS ||--o{ POSITIONS : memiliki
    EMPLOYEES ||--o{ EMPLOYEE_POSITION_HISTORIES : menjabat
    POSITIONS ||--o{ EMPLOYEE_POSITION_HISTORIES : ditempati
7.5 Keputusan penting
1. users hanya menyimpan kebutuhan authentication.
2. Profil seseorang disimpan pada people.
3. Guru dan tenaga kependidikan disimpan pada employees.
4. Jenis pegawai dapat berupa guru atau tenaga kependidikan.
5. Jabatan tidak disimpan langsung pada users.
6. Riwayat jabatan disimpan sebagai record tersendiri.
7. Role tidak menentukan kelas atau mata pelajaran yang diajar.
8. Hak atas kelas dan mata pelajaran berasal dari penugasan.

8. ERD 5.2 TAHUN AJARAN, KELAS, DAN AKADEMIK
8.1 Entity periode
Entity	Fungsi
academic_years	Menyimpan tahun ajaran
semesters	Menyimpan semester dalam tahun ajaran
grade_levels	Menyimpan tingkat kelas
class_groups	Menyimpan rombongan belajar
rooms	Menyimpan ruangan
student_enrollments	Menyimpan penempatan siswa per periode
homeroom_assignments	Menyimpan penugasan wali kelas
8.2 Entity kurikulum dan pembelajaran
Entity	Fungsi
curricula	Menyimpan kurikulum
subjects	Menyimpan mata pelajaran
curriculum_subjects	Menghubungkan kurikulum dan mata pelajaran
teaching_assignments	Menyimpan penugasan mengajar
lesson_periods	Menyimpan jam pelajaran
schedules	Menyimpan jadwal
learning_documents	Menyimpan ATP dan perangkat pembelajaran
teaching_journals	Menyimpan jurnal mengajar
8.3 Entity kehadiran, nilai, dan rapor
Entity	Fungsi
attendance_sessions	Menyimpan kegiatan absensi
attendance_records	Menyimpan status kehadiran setiap siswa
assessment_components	Menyimpan jenis atau komponen penilaian
assessment_scores	Menyimpan nilai siswa
report_cards	Menyimpan header rapor
report_card_items	Menyimpan komponen rapor
report_card_versions	Menyimpan snapshot dan versi rapor
8.4 Mengapa student_enrollments menjadi entity penting
Tabel students hanya menyimpan identitas permanen siswa.
Kelas siswa tidak disimpan sebagai informasi histori utama pada tabel students.
Tabel student_enrollments menyimpan:
* Siswa.
* Tahun ajaran.
* Semester.
* Kelas.
* Status penempatan.
* Tanggal mulai.
* Tanggal selesai.
* Alasan perubahan.
* Pengguna yang memproses.
Contoh:
Siswa	Tahun Ajaran	Semester	Kelas
Ahmad	2026/2027	Ganjil	VII A
Ahmad	2026/2027	Genap	VII A
Ahmad	2027/2028	Ganjil	VIII B
Data lama tidak berubah ketika siswa naik kelas.
8.5 Diagram konseptual akademik
erDiagram
    ACADEMIC_YEARS ||--o{ SEMESTERS : memiliki

    GRADE_LEVELS ||--o{ CLASS_GROUPS : memiliki
    ROOMS ||--o{ CLASS_GROUPS : digunakan

    STUDENTS ||--o{ STUDENT_ENROLLMENTS : ditempatkan
    CLASS_GROUPS ||--o{ STUDENT_ENROLLMENTS : berisi
    SEMESTERS ||--o{ STUDENT_ENROLLMENTS : berlaku

    EMPLOYEES ||--o{ HOMEROOM_ASSIGNMENTS : ditugaskan
    CLASS_GROUPS ||--o{ HOMEROOM_ASSIGNMENTS : memiliki
    SEMESTERS ||--o{ HOMEROOM_ASSIGNMENTS : berlaku

    CURRICULA ||--o{ CURRICULUM_SUBJECTS : memiliki
    SUBJECTS ||--o{ CURRICULUM_SUBJECTS : terdaftar

    EMPLOYEES ||--o{ TEACHING_ASSIGNMENTS : mengajar
    SUBJECTS ||--o{ TEACHING_ASSIGNMENTS : diajarkan
    CLASS_GROUPS ||--o{ TEACHING_ASSIGNMENTS : menerima
    SEMESTERS ||--o{ TEACHING_ASSIGNMENTS : berlaku

    TEACHING_ASSIGNMENTS ||--o{ SCHEDULES : dijadwalkan
    LESSON_PERIODS ||--o{ SCHEDULES : menggunakan
    ROOMS ||--o{ SCHEDULES : bertempat

    TEACHING_ASSIGNMENTS ||--o{ LEARNING_DOCUMENTS : memiliki
    TEACHING_ASSIGNMENTS ||--o{ TEACHING_JOURNALS : dicatat

    TEACHING_ASSIGNMENTS ||--o{ ATTENDANCE_SESSIONS : memiliki
    ATTENDANCE_SESSIONS ||--o{ ATTENDANCE_RECORDS : berisi
    STUDENT_ENROLLMENTS ||--o{ ATTENDANCE_RECORDS : dicatat

    TEACHING_ASSIGNMENTS ||--o{ ASSESSMENT_COMPONENTS : memiliki
    ASSESSMENT_COMPONENTS ||--o{ ASSESSMENT_SCORES : menghasilkan
    STUDENT_ENROLLMENTS ||--o{ ASSESSMENT_SCORES : memperoleh

    STUDENT_ENROLLMENTS ||--o{ REPORT_CARDS : memiliki
    REPORT_CARDS ||--o{ REPORT_CARD_ITEMS : berisi
    REPORT_CARDS ||--o{ REPORT_CARD_VERSIONS : memiliki
8.6 Hubungan penting
Tahun ajaran dan semester
academic_years 1 : N semesters
Satu tahun ajaran dapat memiliki beberapa semester.
Versi awal menggunakan:
* Semester ganjil.
* Semester genap.
Siswa dan riwayat kelas
students 1 : N student_enrollments
Satu siswa memiliki banyak riwayat penempatan.
Guru dan penugasan
employees 1 : N teaching_assignments
Satu guru dapat memiliki beberapa penugasan mengajar.
Penugasan dan jadwal
teaching_assignments 1 : N schedules
Satu penugasan dapat muncul lebih dari satu kali dalam jadwal mingguan.
Penilaian dan nilai siswa
assessment_components 1 : N assessment_scores
student_enrollments 1 : N assessment_scores
Nilai mengacu pada penempatan siswa, bukan hanya siswa.
Dengan demikian, nilai otomatis memiliki konteks kelas dan periode.

9. ERD 5.3 RAPOR DAN SNAPSHOT
9.1 Mengapa rapor membutuhkan tabel versi
Rapor adalah dokumen resmi.
Setelah diterbitkan, perubahan nilai tidak boleh mengubah rapor lama secara diam-diam.
Karena itu diperlukan:
* report_cards
* report_card_items
* report_card_versions
report_cards menyimpan identitas utama rapor.
report_card_items menyimpan hasil mata pelajaran dan komponen lain.
report_card_versions menyimpan:
* Nomor versi.
* Data snapshot.
* Waktu penerbitan.
* Pengguna yang menerbitkan.
* Status resmi.
* File PDF.
* Alasan revisi.
Workflow rapor mengikuti status draft, diajukan, diverifikasi, disetujui, diterbitkan, dan diarsipkan. Workflow nilai, rapor, berita, tagihan, eviden, dan PPDB harus menggunakan status yang dapat dilacak.
9.2 Keuntungan snapshot
1. Rapor lama tetap tersedia.
2. Revisi menghasilkan versi baru.
3. Sistem mengetahui versi resmi.
4. Perubahan nilai dapat dilacak.
5. PDF rapor dapat dicetak kembali.
6. Histori tidak bergantung pada kondisi data terbaru.

10. ERD 5.4 KESISWAAN DAN PORTOFOLIO DIGITAL
10.1 Entity utama
Entity	Fungsi
student_status_histories	Riwayat status siswa
student_qr_codes	Token QR Code siswa
achievements	Prestasi siswa
violation_categories	Kategori pelanggaran
student_violations	Pelanggaran siswa
violation_actions	Tindak lanjut pelanggaran
counseling_sessions	Catatan konseling
extracurriculars	Data ekstrakurikuler
extracurricular_memberships	Keanggotaan siswa
extracurricular_attendances	Kehadiran ekstrakurikuler
tahfidz_targets	Target hafalan
tahfidz_records	Setoran dan perkembangan hafalan
habit_types	Jenis pembiasaan
habit_records	Catatan pembiasaan
student_organizations	Organisasi siswa
organization_memberships	Kepengurusan siswa
student_works	Karya siswa
student_certificates	Sertifikat siswa
10.2 Diagram konseptual
erDiagram
    STUDENTS ||--o{ STUDENT_STATUS_HISTORIES : memiliki
    STUDENTS ||--o{ STUDENT_QR_CODES : memiliki

    STUDENT_ENROLLMENTS ||--o{ ACHIEVEMENTS : memperoleh
    STUDENT_ENROLLMENTS ||--o{ STUDENT_VIOLATIONS : melakukan
    VIOLATION_CATEGORIES ||--o{ STUDENT_VIOLATIONS : mengelompokkan
    STUDENT_VIOLATIONS ||--o{ VIOLATION_ACTIONS : ditindaklanjuti

    STUDENT_ENROLLMENTS ||--o{ COUNSELING_SESSIONS : mengikuti
    EMPLOYEES ||--o{ COUNSELING_SESSIONS : menangani

    EXTRACURRICULARS ||--o{ EXTRACURRICULAR_MEMBERSHIPS : memiliki
    STUDENT_ENROLLMENTS ||--o{ EXTRACURRICULAR_MEMBERSHIPS : mengikuti
    EXTRACURRICULAR_MEMBERSHIPS ||--o{ EXTRACURRICULAR_ATTENDANCES : memiliki

    STUDENT_ENROLLMENTS ||--o{ TAHFIDZ_TARGETS : memiliki
    TAHFIDZ_TARGETS ||--o{ TAHFIDZ_RECORDS : direalisasikan

    HABIT_TYPES ||--o{ HABIT_RECORDS : digunakan
    STUDENT_ENROLLMENTS ||--o{ HABIT_RECORDS : dicatat

    STUDENTS ||--o{ STUDENT_WORKS : menghasilkan
    STUDENTS ||--o{ STUDENT_CERTIFICATES : memiliki
10.3 Portofolio tidak membutuhkan tabel transaksi baru
Portofolio digital adalah halaman agregasi.
Portofolio membaca data dari:
* students
* student_enrollments
* attendance_records
* assessment_scores
* report_cards
* achievements
* student_violations
* counseling_sessions
* extracurricular_memberships
* tahfidz_records
* habit_records
* student_bills
* payments
* student_works
* student_certificates
Kita tidak membuat tabel yang menyalin seluruh data tersebut.
Tabel khusus yang diperlukan hanya student_qr_codes, karena sistem perlu menyimpan:
* Token.
* Status aktif.
* Waktu kedaluwarsa.
* Jenis akses.
* Waktu dibuat.
* Waktu dinonaktifkan.
QR Code tidak menyimpan data siswa. QR Code hanya mengarahkan pengguna ke proses pemeriksaan akses.

11. ERD 5.5 KEUANGAN SISWA
11.1 Entity utama
Entity	Fungsi
fee_types	Menyimpan jenis tagihan
billing_batches	Menyimpan proses pembuatan tagihan massal
student_bills	Menyimpan tagihan siswa
bill_adjustments	Menyimpan potongan, keringanan, atau pembebasan
payments	Menyimpan transaksi pembayaran
payment_corrections	Menyimpan koreksi atau pembatalan
receipts	Menyimpan informasi kwitansi
11.2 Diagram konseptual
erDiagram
    FEE_TYPES ||--o{ BILLING_BATCHES : digunakan
    ACADEMIC_YEARS ||--o{ BILLING_BATCHES : berlaku
    SEMESTERS ||--o{ BILLING_BATCHES : berlaku

    BILLING_BATCHES ||--o{ STUDENT_BILLS : menghasilkan
    STUDENTS ||--o{ STUDENT_BILLS : memiliki
    FEE_TYPES ||--o{ STUDENT_BILLS : berjenis

    STUDENT_BILLS ||--o{ BILL_ADJUSTMENTS : memiliki
    STUDENT_BILLS ||--o{ PAYMENTS : dibayar

    PAYMENTS ||--o{ PAYMENT_CORRECTIONS : dikoreksi
    PAYMENTS ||--o| RECEIPTS : menghasilkan

    USERS ||--o{ PAYMENTS : menerima
    USERS ||--o{ PAYMENT_CORRECTIONS : memproses
11.3 Mengapa tagihan dan pembayaran dipisahkan
Tagihan adalah kewajiban siswa.
Pembayaran adalah transaksi yang mengurangi kewajiban.
Contoh:
Tagihan SPP Agustus = Rp300.000
Pembayaran pertama = Rp100.000
Pembayaran kedua = Rp200.000
Status akhir = Lunas
Satu tagihan dapat memiliki banyak pembayaran.
Hubungannya:
student_bills 1 : N payments
11.4 Status pembayaran tidak diinput sembarangan
Status dihitung dari:
Nominal tagihan
dikurangi potongan atau pembebasan
dibandingkan dengan total pembayaran yang sah
Hasilnya dapat berupa:
* Belum bayar.
* Cicilan.
* Lunas.
* Dibebaskan.
* Dibatalkan.
Kolom status dapat disimpan untuk mempercepat pencarian, tetapi nilainya harus dihitung ulang oleh sistem setiap ada transaksi.
11.5 Koreksi pembayaran
Transaksi pembayaran lama tidak dihapus.
Jika terjadi kesalahan:
1. Pengguna memilih transaksi.
2. Pengguna memasukkan alasan.
3. Sistem membuat record koreksi.
4. Sistem menyimpan nilai lama.
5. Sistem menghitung ulang tagihan.
6. Sistem membuat audit log.
Proses pencatatan pembayaran, pembuatan tagihan massal, penerbitan rapor, kenaikan kelas, dan konversi PPDB harus menggunakan database transaction.

12. ERD 5.6 WEBSITE PUBLIK DAN PORTAL BERITA
12.1 Entity utama
Entity	Fungsi
public_pages	Menyimpan halaman profil madrasah
posts	Menyimpan berita
post_categories	Menyimpan kategori
tags	Menyimpan tag
post_tags	Menghubungkan berita dan tag
post_media	Menyimpan gambar atau video
post_reviews	Menyimpan review dan persetujuan
agendas	Menyimpan agenda
announcements	Menyimpan pengumuman
galleries	Menyimpan album
gallery_media	Menyimpan media album
12.2 Diagram konseptual
erDiagram
    USERS ||--o{ POSTS : menulis
    POST_CATEGORIES ||--o{ POSTS : mengelompokkan

    POSTS ||--o{ POST_TAGS : memiliki
    TAGS ||--o{ POST_TAGS : digunakan

    POSTS ||--o{ POST_MEDIA : memiliki
    POSTS ||--o{ POST_REVIEWS : direview
    USERS ||--o{ POST_REVIEWS : melakukan

    GALLERIES ||--o{ GALLERY_MEDIA : memiliki
    USERS ||--o{ AGENDAS : membuat
    USERS ||--o{ ANNOUNCEMENTS : membuat
12.3 Workflow berita
Status berita disimpan pada posts.
Riwayat review dan persetujuan disimpan pada post_reviews.
Contoh alur:
Draft
Diajukan
Dalam Review
Memerlukan Revisi
Disetujui
Dijadwalkan
Dipublikasikan
Diarsipkan
Riwayat workflow tidak cukup hanya disimpan pada satu kolom status.
Tabel post_reviews diperlukan untuk menyimpan:
* Status sebelumnya.
* Status sesudahnya.
* Reviewer.
* Catatan.
* Waktu.
* Jenis tindakan.

13. ERD 5.7 PPDB
13.1 Entity utama
Entity	Fungsi
admission_periods	Menyimpan periode PPDB
admission_tracks	Menyimpan jalur pendaftaran
applicants	Menyimpan calon siswa
applicant_guardians	Menyimpan orang tua calon siswa
applications	Menyimpan pengajuan PPDB
application_documents	Menyimpan dokumen
application_verifications	Menyimpan hasil verifikasi
selection_components	Menyimpan komponen seleksi
selection_scores	Menyimpan nilai seleksi
re_registrations	Menyimpan daftar ulang
applicant_conversions	Menyimpan hasil konversi menjadi siswa
13.2 Mengapa calon siswa tidak langsung masuk tabel students
Tidak semua pendaftar diterima.
Jika seluruh pendaftar langsung dimasukkan ke tabel students, data siswa akan tercampur dengan:
* Pendaftar yang belum lengkap.
* Pendaftar yang tidak diterima.
* Pendaftar yang mengundurkan diri.
* Pendaftar yang belum daftar ulang.
Karena itu, data PPDB disimpan terpisah.
Setelah pendaftar diterima dan daftar ulangnya lengkap, sistem melakukan konversi:
applicants
↓
people
↓
students
↓
student_status_histories
↓
student_guardians
Proses tersebut menggunakan database transaction agar tidak menghasilkan data siswa setengah jadi.
13.3 Diagram konseptual
erDiagram
    ADMISSION_PERIODS ||--o{ ADMISSION_TRACKS : memiliki
    ADMISSION_PERIODS ||--o{ APPLICATIONS : menerima
    ADMISSION_TRACKS ||--o{ APPLICATIONS : digunakan

    APPLICANTS ||--o{ APPLICATIONS : mengajukan
    APPLICANTS ||--o{ APPLICANT_GUARDIANS : memiliki

    APPLICATIONS ||--o{ APPLICATION_DOCUMENTS : memiliki
    APPLICATIONS ||--o{ APPLICATION_VERIFICATIONS : diverifikasi

    ADMISSION_PERIODS ||--o{ SELECTION_COMPONENTS : memiliki
    SELECTION_COMPONENTS ||--o{ SELECTION_SCORES : menghasilkan
    APPLICATIONS ||--o{ SELECTION_SCORES : memperoleh

    APPLICATIONS ||--o| RE_REGISTRATIONS : melakukan
    APPLICATIONS ||--o| APPLICANT_CONVERSIONS : dikonversi
    STUDENTS ||--o| APPLICANT_CONVERSIONS : dihasilkan

14. ERD 5.8 TATA USAHA DAN KEPEGAWAIAN
14.1 Entity tata usaha
Entity	Fungsi
incoming_letters	Surat masuk
letter_dispositions	Disposisi
outgoing_letters	Surat keluar
letter_approvals	Persetujuan surat
decrees	Surat keputusan
partnerships	MOU dan kerja sama
archives	Metadata arsip
archive_categories	Kategori arsip
14.2 Entity kepegawaian
Entity	Fungsi
employee_education_histories	Riwayat pendidikan
employee_rank_histories	Riwayat pangkat
employee_status_histories	Riwayat status pegawai
employee_attendance_sessions	Sesi kehadiran
employee_attendance_records	Kehadiran pegawai
training_activities	Workshop dan pelatihan
employee_trainings	Keikutsertaan pegawai
employee_certificates	Sertifikat pegawai
14.3 Prinsip histori kepegawaian
Pendidikan, jabatan, pangkat, status, dan pelatihan tidak disimpan sebagai satu nilai yang selalu ditimpa.
Contoh:
employee_position_histories
menyimpan:
* Pegawai.
* Jabatan.
* Unit kerja.
* Tanggal mulai.
* Tanggal selesai.
* Nomor SK.
* Status aktif.
Dengan cara ini, sistem dapat mengetahui riwayat jabatan seseorang.

15. ERD 5.9 INVENTARIS, LABORATORIUM, DAN PERPUSTAKAAN
15.1 Entity inventaris
Entity	Fungsi
item_categories	Kategori barang
inventory_items	Master jenis barang
inventory_units	Unit aset individual
inventory_movements	Mutasi barang
inventory_loans	Peminjaman barang
maintenance_records	Pemeliharaan
inventory_condition_histories	Riwayat kondisi
disposal_proposals	Pengajuan penghapusan
15.2 Mengapa barang dan unit barang dipisahkan
Contoh:
Master barang:
Laptop Lenovo ThinkPad

Unit aset:
Laptop 001
Laptop 002
Laptop 003
Tabel inventory_items menyimpan informasi jenis barang.
Tabel inventory_units menyimpan setiap unit yang memiliki:
* Kode aset.
* QR Code.
* Lokasi.
* Kondisi.
* Tahun perolehan.
* Harga.
* Status.
Desain ini cocok untuk barang yang harus dilacak secara individual.
Barang habis pakai dapat menggunakan mekanisme stok yang berbeda.
15.3 Entity laboratorium
Entity	Fungsi
laboratories	Data laboratorium
lab_items	Alat dan bahan
lab_stock_movements	Perubahan stok
lab_usage_schedules	Jadwal penggunaan
lab_loans	Peminjaman alat
lab_maintenance_records	Pemeliharaan alat
15.4 Entity perpustakaan
Entity	Fungsi
books	Master judul buku
book_copies	Eksemplar buku
book_categories	Kategori buku
authors	Penulis
book_authors	Relasi buku dan penulis
publishers	Penerbit
library_members	Anggota
book_loans	Transaksi peminjaman
book_loan_items	Buku dalam transaksi
library_fines	Denda
ebooks	Koleksi buku digital
15.5 Diagram konseptual perpustakaan
erDiagram
    BOOK_CATEGORIES ||--o{ BOOKS : mengelompokkan
    PUBLISHERS ||--o{ BOOKS : menerbitkan

    BOOKS ||--o{ BOOK_AUTHORS : memiliki
    AUTHORS ||--o{ BOOK_AUTHORS : menulis

    BOOKS ||--o{ BOOK_COPIES : memiliki

    PEOPLE ||--o| LIBRARY_MEMBERS : menjadi
    LIBRARY_MEMBERS ||--o{ BOOK_LOANS : meminjam

    BOOK_LOANS ||--o{ BOOK_LOAN_ITEMS : berisi
    BOOK_COPIES ||--o{ BOOK_LOAN_ITEMS : dipinjam

    BOOK_LOAN_ITEMS ||--o| LIBRARY_FINES : menghasilkan

16. ERD 5.10 PKKM, AKREDITASI, DAN PENJAMINAN MUTU
16.1 Pendekatan tabel bersama
PKKM dan akreditasi memiliki pola data yang hampir sama:
Periode
↓
Standar atau komponen
↓
Indikator
↓
Penanggung jawab
↓
Eviden
↓
Verifikasi
↓
Kelengkapan
Agar tidak membuat dua struktur database yang hampir sama, kita menggunakan struktur umum.
16.2 Entity utama
Entity	Fungsi
quality_frameworks	Menyimpan jenis instrumen
quality_periods	Menyimpan periode penilaian
quality_nodes	Menyimpan standar, komponen, subkomponen, dan indikator
quality_assignments	Menyimpan penanggung jawab
quality_evidences	Menyimpan eviden
quality_evidence_reviews	Menyimpan verifikasi
quality_scores	Menyimpan skor internal
work_programs	Menyimpan program kerja
work_program_realizations	Menyimpan realisasi
Contoh quality_frameworks:
Nama	Jenis
PKKM 2026	PKKM
Akreditasi Madrasah	Akreditasi
16.3 Mengapa menggunakan quality_nodes
Struktur instrumen dapat memiliki tingkat berbeda.
Contoh:
Standar
└── Komponen
    └── Subkomponen
        └── Indikator
Tabel quality_nodes menggunakan hubungan ke dirinya sendiri:
quality_nodes.parent_id
Dengan desain tersebut, sistem dapat mengikuti perubahan instrumen tanpa membuat tabel baru untuk setiap tingkat.
16.4 Diagram konseptual
erDiagram
    QUALITY_FRAMEWORKS ||--o{ QUALITY_PERIODS : digunakan
    QUALITY_FRAMEWORKS ||--o{ QUALITY_NODES : memiliki

    QUALITY_NODES ||--o{ QUALITY_NODES : memiliki_anak
    QUALITY_PERIODS ||--o{ QUALITY_ASSIGNMENTS : memiliki
    QUALITY_NODES ||--o{ QUALITY_ASSIGNMENTS : ditugaskan
    USERS ||--o{ QUALITY_ASSIGNMENTS : bertanggung_jawab

    QUALITY_ASSIGNMENTS ||--o{ QUALITY_EVIDENCES : mengunggah
    QUALITY_EVIDENCES ||--o{ QUALITY_EVIDENCE_REVIEWS : direview
    USERS ||--o{ QUALITY_EVIDENCE_REVIEWS : memeriksa

    QUALITY_NODES ||--o{ QUALITY_SCORES : dinilai
    QUALITY_PERIODS ||--o{ QUALITY_SCORES : berlaku
16.5 Perhitungan persentase
Persentase kelengkapan tidak membutuhkan tabel ringkasan khusus.
Sistem menghitung:
Jumlah indikator wajib yang terverifikasi
dibagi
Jumlah seluruh indikator wajib
dikali 100 persen
Nilai dapat dihitung dari data sumber dan ditampilkan pada dashboard.

17. ERD 5.11 DOKUMEN DAN FILE
17.1 Entity utama
Entity	Fungsi
files	Menyimpan metadata file fisik
file_access_logs	Mencatat akses file tertentu
Tabel dokumen per domain	Menghubungkan file dengan data sumber
Attribute utama files:
* Nama file asli.
* Nama penyimpanan.
* Path.
* Ekstensi.
* MIME type.
* Ukuran.
* Disk penyimpanan.
* Status publik atau privat.
* Pengunggah.
* Hash file.
* Waktu upload.
17.2 Pendekatan yang dipilih
Kita menggunakan pendekatan hybrid.
Tabel files
Menyimpan informasi fisik file.
Tabel penghubung domain
Contohnya:
* student_documents
* employee_documents
* application_documents
* post_media
* quality_evidences
* archive_files
* learning_documents
Setiap tabel domain mengacu pada files.id.
Keuntungannya:
1. Metadata file tidak berulang.
2. File dapat dikelola secara terpusat.
3. Relasi database tetap jelas.
4. Pusat dokumen dapat mencari file dari berbagai modul.
5. File privat tetap diperiksa berdasarkan sumber datanya.
Pusat dokumen tidak menggandakan file. Modul tersebut hanya menjadi indeks dan pencarian terpusat.

18. ERD 5.12 NOTIFIKASI, IMPORT, AUDIT, DAN BACKUP
18.1 Entity pendukung
Entity	Fungsi
notifications	Notifikasi internal
import_batches	Riwayat proses import
import_failures	Baris import yang gagal
activity_logs	Aktivitas pengguna
audit_logs	Perubahan data penting
backups	Metadata file backup
restore_logs	Riwayat restore
system_logs	Kesalahan aplikasi tertentu
18.2 Perbedaan activity log dan audit log
Activity log
Mencatat aktivitas.
Contoh:
* Login.
* Logout.
* Membuka laporan.
* Mengunduh file.
* Menjalankan import.
Audit log
Mencatat perubahan data.
Contoh:
* Nilai 75 menjadi 85.
* Status siswa aktif menjadi pindah.
* Pembayaran Rp200.000 menjadi Rp250.000.
* Role pengguna berubah.
* Eviden ditolak menjadi terverifikasi.
Data audit yang dibutuhkan meliputi modul, jenis data, ID data, aksi, nilai lama, nilai baru, pengguna, waktu, alamat IP, alasan, dan referensi transaksi.
18.3 Struktur audit log
Attribute konseptual:
audit_logs
- id
- user_id
- module
- auditable_type
- auditable_id
- action
- old_values
- new_values
- reason
- ip_address
- user_agent
- created_at
old_values dan new_values dapat menggunakan format JSON.

19. ENTITY YANG TIDAK PERLU DIBUAT
Tidak setiap menu harus memiliki tabel sendiri.
19.1 Dashboard
Tidak membutuhkan tabel dashboard.
Dashboard membaca:
* Jumlah siswa.
* Kehadiran.
* Nilai.
* Pembayaran.
* Inventaris.
* PKKM.
* Akreditasi.
19.2 Portal siswa
Tidak membutuhkan tabel akademik khusus portal siswa.
Portal siswa mengambil data berdasarkan siswa yang sedang login.
19.3 Portal orang tua
Tidak membutuhkan salinan data anak.
Portal memeriksa student_guardians kemudian mengambil data siswa terkait.
19.4 Portofolio digital
Tidak menyimpan ulang data perjalanan siswa.
Portofolio adalah gabungan query dari modul sumber.
19.5 Pusat laporan
Tidak menjadi tempat input transaksi.
Pusat laporan hanya mengolah data yang tersedia.
19.6 Pusat dokumen
Tidak menyalin file dari seluruh modul.
Pusat dokumen menjadi indeks pencarian file.

20. RELASI KRITIS YANG WAJIB DIJAGA
20.1 Satu siswa hanya memiliki satu penempatan aktif per semester
Constraint konseptual:
student_id + semester_id harus unik untuk penempatan aktif
Perpindahan kelas dilakukan dengan menutup penempatan lama dan membuat record baru.
20.2 Satu guru tidak boleh memiliki penugasan yang sama dua kali
Constraint konseptual:
employee_id
+ subject_id
+ class_group_id
+ semester_id
harus unik untuk penugasan aktif.
20.3 Nilai tidak boleh ganda
Constraint konseptual:
assessment_component_id
+ student_enrollment_id
harus unik.
20.4 Tagihan tidak boleh dibuat dua kali
Constraint konseptual dapat melibatkan:
student_id
+ fee_type_id
+ academic_year_id
+ billing_period
20.5 Relasi orang tua dan siswa tidak boleh ganda
Constraint konseptual:
student_id
+ guardian_id
+ relationship_type
20.6 Hanya satu semester aktif
Sistem versi pertama hanya boleh memiliki satu semester aktif.
Pemeriksaan dilakukan melalui validasi aplikasi dan constraint desain yang sesuai.
20.7 Satu token QR Code harus unik
Token QR tidak boleh dapat digunakan oleh dua siswa.

21. ATURAN PENGHAPUSAN DATA
21.1 Master data
Master data sebaiknya menggunakan:
* Status aktif.
* Status nonaktif.
* Soft delete jika diperlukan.
Contoh:
* Mata pelajaran.
* Kelas.
* Guru.
* Ruangan.
* Jenis tagihan.
21.2 Data histori
Data histori tidak boleh dihapus melalui proses biasa.
Contoh:
* Penempatan kelas.
* Nilai.
* Kehadiran.
* Rapor.
* Pembayaran.
* Status siswa.
* Jabatan.
* Inventaris.
21.3 Penggunaan cascade delete
CASCADE hanya digunakan pada data anak yang tidak bermakna tanpa induknya.
Contoh:
* post_tags ketika berita dihapus sebelum dipublikasikan.
* Baris preview import sementara.
* Pivot role yang dicabut.
Data resmi dan historis lebih aman menggunakan RESTRICT atau perubahan status.

22. KEPUTUSAN DESAIN ERD
Area	Keputusan
Arsitektur data	Satu database modular
Identitas orang	Menggunakan tabel people
Authentication	Dipisahkan pada users
Guru dan tendik	Menggunakan employees
Siswa	Menggunakan students
Orang tua	Menggunakan guardians
Hubungan orang tua dan siswa	Many-to-many
Role pengguna	Many-to-many
Role dan permission	Many-to-many
Jabatan	Menggunakan riwayat jabatan
Periode akademik	Tahun ajaran dan semester
Kelas siswa	Menggunakan student_enrollments
Penugasan guru	Berbasis kelas, mapel, dan semester
Nilai	Terhubung ke penempatan siswa
Rapor	Menggunakan versi dan snapshot
Pembayaran	Satu tagihan dapat memiliki banyak pembayaran
Koreksi transaksi	Membuat record koreksi
Portofolio	Query agregasi tanpa duplikasi
QR Code	Token terpisah dan aman
File	Metadata terpusat dengan relasi domain
PKKM dan akreditasi	Menggunakan struktur framework bersama
Audit	Memisahkan activity log dan audit log
Penghapusan histori	Tidak menggunakan hard delete biasa
Dashboard	Tidak memiliki tabel transaksi sendiri
23. KEUNTUNGAN DESAIN ERD INI
1. Histori siswa tetap tersedia.
2. Siswa dapat ditelusuri dari masuk sampai lulus.
3. Data guru dan akun login tidak tercampur.
4. Satu pengguna dapat memiliki beberapa role.
5. Satu guru dapat memiliki beberapa penugasan.
6. Satu orang tua dapat terhubung dengan beberapa anak.
7. Nilai selalu memiliki konteks kelas dan periode.
8. Rapor resmi dapat dilacak per versi.
9. Pembayaran cicilan dapat dihitung dengan benar.
10. Koreksi transaksi tidak menghilangkan jejak lama.
11. Portofolio tidak menduplikasi data.
12. PKKM dan akreditasi dapat mengikuti perubahan instrumen.
13. File privat dapat dikendalikan.
14. Audit perubahan dapat dilakukan.
15. Struktur tetap cocok untuk Laravel modular monolith.
16. Database tetap dapat dijalankan pada shared hosting.

24. KEKURANGAN DAN TANTANGAN
24.1 Jumlah tabel cukup banyak
Hal ini terjadi karena sistem mencakup banyak bidang.
Solusinya:
* Mengembangkan berdasarkan prioritas.
* Membuat migration per domain.
* Menggunakan penamaan konsisten.
* Mendokumentasikan setiap relasi.
24.2 Relasi akademik cukup kompleks
Nilai harus terhubung dengan:
* Siswa.
* Penempatan kelas.
* Guru.
* Mata pelajaran.
* Tahun ajaran.
* Semester.
Kompleksitas ini diperlukan agar hak akses dan histori dapat dijaga.
24.3 Query portofolio dan dashboard dapat menjadi berat
Solusinya:
* Menggunakan index.
* Menggunakan eager loading.
* Membatasi periode.
* Menggunakan pagination.
* Tidak mengambil seluruh data sekaligus.
* Menggunakan query agregasi yang efisien.
24.4 Struktur kualitas bersifat umum
Tabel quality_nodes memerlukan pemahaman lebih karena memiliki relasi ke dirinya sendiri.
Namun, desain ini membuat PKKM dan akreditasi lebih fleksibel.

25. PRIORITAS ENTITY UNTUK MVP
ERD lengkap mencakup seluruh sistem. Implementasi awal tetap mengikuti MVP yang sudah ditetapkan, yaitu authentication, pengguna, role dan permission, pengaturan madrasah, periode akademik, guru, siswa, orang tua, mata pelajaran, kelas, riwayat kelas, penugasan, jadwal, kehadiran, jurnal, nilai, rapor, tagihan, pembayaran, dashboard, portal, activity log, dan backup.
Entity prioritas awal:
1. madrasahs
2. settings
3. people
4. users
5. roles
6. permissions
7. user_roles
8. role_permissions
9. employees
10. students
11. guardians
12. student_guardians
13. academic_years
14. semesters
15. grade_levels
16. class_groups
17. student_enrollments
18. subjects
19. teaching_assignments
20. schedules
21. teaching_journals
22. attendance_sessions
23. attendance_records
24. assessment_components
25. assessment_scores
26. report_cards
27. report_card_items
28. report_card_versions
29. fee_types
30. student_bills
31. payments
32. activity_logs
33. files
34. backups
Entity lain dibangun setelah fondasi dan modul akademik utama stabil.

26. HASIL TAHAP 5
Tahap Perancangan ERD telah menghasilkan:
1. Prinsip pemodelan data SIM Madrasah.
2. Pemisahan akun dan identitas.
3. Pemisahan role, jabatan, dan penugasan.
4. Struktur data tahun ajaran dan semester.
5. Struktur histori kelas siswa.
6. Struktur penugasan guru.
7. Struktur jadwal dan jurnal mengajar.
8. Struktur kehadiran.
9. Struktur penilaian.
10. Struktur rapor dan snapshot.
11. Struktur kesiswaan.
12. Struktur portofolio digital.
13. Struktur keuangan siswa.
14. Struktur CMS berita.
15. Struktur PPDB.
16. Struktur tata usaha.
17. Struktur kepegawaian.
18. Struktur inventaris.
19. Struktur laboratorium.
20. Struktur perpustakaan.
21. Struktur PKKM dan akreditasi.
22. Struktur dokumen dan file.
23. Struktur activity log dan audit log.
24. Relasi utama dan kardinalitas.
25. Aturan penghapusan data.
26. Daftar entity prioritas MVP.
Mengapa Tahap 5 penting
Tahap ini menjadi penghubung antara proses bisnis dan database fisik.
Tanpa ERD:
* Migration dapat dibuat tidak konsisten.
* Foreign key dapat salah.
* Histori dapat hilang.
* Data dapat terduplikasi.
* Hak akses per record sulit diterapkan.
* Dashboard dapat mengambil data yang tidak tepat.
* Perubahan tabel pada masa depan menjadi lebih mahal.
Hasil Tahap 5 akan menjadi dasar Tahap 6: Desain Database, yang akan menetapkan nama tabel final, kolom, tipe data, primary key, foreign key, unique constraint, index, status, aturan nullable, dan urutan migration.
