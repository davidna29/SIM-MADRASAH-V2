# Tahap 6 — Desain Database

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 7278–9329.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

=========================================================
TAHAP 6
DESAIN DATABASE SIM MADRASAH
1. Konsep Sederhana
Desain database adalah proses menerjemahkan ERD menjadi rancangan tabel yang siap dibuat melalui Laravel Migration.
Pada Tahap 5, kita telah menentukan:
* Entitas yang dibutuhkan.
* Hubungan antardata.
* Kardinalitas.
* Data master.
* Data transaksi.
* Data histori.
* Entity yang tidak boleh menduplikasi data.
Pada Tahap 6, kita menentukan hal yang lebih teknis, yaitu:
1. Nama tabel.
2. Nama kolom.
3. Tipe data.
4. Primary key.
5. Foreign key.
6. Unique constraint.
7. Index.
8. Kolom yang boleh kosong.
9. Status data.
10. Aturan penghapusan.
11. Urutan pembuatan tabel.
12. Data awal yang perlu dimasukkan melalui seeder.
Tahap ini belum masuk ke pembuatan file migration. Coding migration baru dilakukan setelah struktur folder, instalasi Laravel, dan fondasi aplikasi siap.

2. Analogi Sederhana
ERD dapat dianalogikan sebagai denah ruang arsip.
Desain database adalah rincian setiap lemari di dalam ruang tersebut.
Contohnya:
* Nama lemari.
* Nomor lemari.
* Jenis dokumen.
* Susunan rak.
* Kunci lemari.
* Hubungan dengan lemari lain.
* Dokumen yang boleh dipindahkan.
* Dokumen yang tidak boleh dimusnahkan.
Jika desain database tidak disiapkan dengan baik, kita dapat mengalami:
* Data siswa ganda.
* Nilai tidak memiliki semester.
* Pembayaran tidak diketahui milik tagihan mana.
* Guru dapat mengubah nilai kelas yang tidak diajar.
* Riwayat kelas hilang.
* Rapor berubah setelah diterbitkan.
* Dokumen tidak diketahui pemiliknya.
* Foreign key gagal dibuat.
* Migration harus dibongkar berulang kali.

3. Tujuan Desain Database
Desain database SIM Madrasah bertujuan untuk:
1. Menjaga integritas data.
2. Mencegah duplikasi data.
3. Menjaga histori akademik.
4. Mendukung multi-role.
5. Mendukung penugasan berbasis periode.
6. Mendukung relasi orang tua dan siswa.
7. Mendukung pembayaran cicilan.
8. Mendukung workflow persetujuan.
9. Mendukung audit log.
10. Mendukung portal siswa dan orang tua.
11. Mendukung laporan berdasarkan periode.
12. Menjaga performa pada shared hosting.
13. Mempermudah pembuatan Laravel Migration.
14. Mempermudah pembuatan model Eloquent.
15. Mempermudah validasi dan pengujian.
Seluruh transaksi akademik harus tetap memeriksa tahun ajaran, semester, status periode, penugasan, relasi pengguna, dan status publikasi.

4. STANDAR UMUM DATABASE
4.1 Database Engine
Gunakan:
InnoDB
Alasannya:
* Mendukung foreign key.
* Mendukung database transaction.
* Mendukung rollback.
* Cocok untuk Laravel.
* Tersedia pada MySQL dan MariaDB.
* Cocok untuk shared hosting.
4.2 Character Set
Gunakan:
utf8mb4
Collation yang disarankan:
utf8mb4_unicode_ci
atau collation setara yang tersedia pada server.
utf8mb4 diperlukan agar database dapat menyimpan:
* Huruf Indonesia.
* Huruf Arab.
* Simbol.
* Emoji.
* Teks multibahasa.
4.3 Penamaan Tabel
Gunakan nama:
* Bahasa Inggris.
* Huruf kecil.
* Bentuk jamak.
* Format snake_case.
Contoh:
users
students
academic_years
student_enrollments
teaching_assignments
assessment_scores
Hindari:
DataSiswa
tbl_siswa
TabelNilai
tb_pembayaran
Penamaan standar mempermudah penggunaan Eloquent.
4.4 Penamaan Primary Key
Setiap tabel utama menggunakan:
id
Tipe data:
BIGINT UNSIGNED
Laravel dapat membuatnya melalui:
id()
Namun, kode migration belum dibuat pada tahap ini.
4.5 Penamaan Foreign Key
Format foreign key:
nama_entity_id
Contoh:
student_id
semester_id
employee_id
class_group_id
teaching_assignment_id
4.6 Timestamps
Sebagian besar tabel menggunakan:
created_at
updated_at
Tabel transaksi tertentu juga memiliki:
created_by
updated_by
Namun, tidak semua tabel perlu memiliki created_by dan updated_by. Audit perubahan penting tetap disimpan pada audit_logs.
4.7 Soft Delete
Kolom:
deleted_at
hanya digunakan pada data yang boleh dipulihkan.
Contoh:
* Akun pengguna.
* Halaman publik.
* Berita yang belum menjadi arsip resmi.
* Kategori.
* Data master tertentu.
Soft delete tidak digunakan sebagai cara utama menghapus:
* Nilai.
* Pembayaran.
* Rapor.
* Kehadiran.
* Riwayat kelas.
* Status siswa.
* Audit log.
Data tersebut menggunakan status, koreksi, pembatalan, atau versi baru.

5. STANDAR TIPE DATA
Kebutuhan	Tipe Data
Primary key	BIGINT UNSIGNED
Foreign key	BIGINT UNSIGNED
Kode pendek	VARCHAR(30)
Nama	VARCHAR(150)
Email	VARCHAR(191)
Nomor telepon	VARCHAR(30)
Status	VARCHAR(30)
Judul	VARCHAR(255)
Deskripsi pendek	TEXT
Isi panjang	LONGTEXT
Tanggal	DATE
Waktu	TIME
Tanggal dan waktu	DATETIME
Nominal uang	DECIMAL(15,2)
Nilai akademik	DECIMAL(5,2)
Persentase	DECIMAL(5,2)
Urutan	SMALLINT UNSIGNED
Boolean	TINYINT(1)
IP address	VARCHAR(45)
Token	VARCHAR(100)
Data snapshot	LONGTEXT berformat JSON
Nilai audit	LONGTEXT berformat JSON
5.1 Mengapa tidak menggunakan tipe FLOAT untuk uang
FLOAT dapat menghasilkan masalah pembulatan.
Gunakan:
DECIMAL(15,2)
Contoh:
300000.00
1250000.50
5.2 Mengapa status menggunakan VARCHAR
Database MySQL memiliki tipe ENUM. Namun, status sistem dapat berkembang.
Contohnya, status nilai dapat berubah dari:
draft
submitted
verified
published
menjadi:
draft
incomplete
submitted
revision
verified
published
locked
Menggunakan VARCHAR membuat sistem lebih mudah dikembangkan.
Nilai status tetap dibatasi melalui:
* Validation.
* Konstanta.
* PHP Enum.
* Service Layer.
* Pengujian.
5.3 Data JSON
Untuk kompatibilitas MySQL dan MariaDB pada shared hosting, data seperti snapshot dan audit dapat disimpan dalam:
LONGTEXT
Isi kolom tetap menggunakan struktur JSON.
Contoh:
{
  "old_score": 75,
  "new_score": 85,
  "reason": "Koreksi hasil pemeriksaan"
}

6. ATURAN FOREIGN KEY
6.1 Restrict
Gunakan RESTRICT untuk data yang tidak boleh dihapus karena masih digunakan.
Contoh:
academic_years
semesters
students
subjects
fee_types
Tahun ajaran tidak boleh dihapus jika sudah memiliki nilai, kehadiran, atau rapor.
6.2 Cascade
Gunakan CASCADE hanya untuk data anak yang tidak memiliki arti tanpa induknya.
Contoh:
role_permissions
user_roles
post_tags
temporary_import_rows
Jika relasi role pengguna dicabut, record pivot dapat dihapus.
6.3 Set Null
Gunakan SET NULL ketika data utama tetap harus tersedia walaupun pengguna atau penanggung jawab tidak lagi aktif.
Contoh:
approved_by
verified_by
uploaded_by
Riwayat pembayaran tetap ada meskipun akun bendahara dinonaktifkan.
6.4 Tidak semua aturan dikerjakan oleh foreign key
Beberapa aturan tidak mudah diterapkan hanya melalui foreign key.
Contoh:
* Hanya satu semester aktif.
* Hanya satu penempatan siswa yang aktif.
* Guru tidak boleh bentrok jadwal.
* Ruangan tidak boleh bentrok.
* Pembayaran tidak boleh melebihi tagihan.
* Orang tua hanya melihat anak yang terhubung.
Aturan tersebut dikerjakan melalui:
* Validation.
* Service Class.
* Database transaction.
* Policy.
* Query relasi.
* Audit log.

7. TABEL FONDASI SISTEM
7.1 Tabel madrasahs
Menyimpan identitas madrasah.
Kolom	Tipe	Aturan
id	BIGINT	Primary key
code	VARCHAR(30)	Unique
name	VARCHAR(150)	Wajib
nsm	VARCHAR(30)	Unique, nullable
npsn	VARCHAR(30)	Unique, nullable
email	VARCHAR(191)	Nullable
phone	VARCHAR(30)	Nullable
address	TEXT	Nullable
village	VARCHAR(100)	Nullable
district	VARCHAR(100)	Nullable
city	VARCHAR(100)	Nullable
province	VARCHAR(100)	Nullable
postal_code	VARCHAR(10)	Nullable
logo_file_id	BIGINT	Nullable
timezone	VARCHAR(50)	Default Asia/Jakarta
is_active	BOOLEAN	Default true
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
Walaupun versi pertama hanya digunakan oleh satu madrasah, tabel ini tetap diperlukan agar identitas madrasah tidak ditanam langsung dalam kode.
7.2 Tabel settings
Kolom	Tipe	Aturan
id	BIGINT	Primary key
madrasah_id	BIGINT	Foreign key
group_name	VARCHAR(50)	Wajib
setting_key	VARCHAR(100)	Wajib
setting_value	LONGTEXT	Nullable
value_type	VARCHAR(30)	Default string
is_public	BOOLEAN	Default false
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
Unique constraint:
madrasah_id + setting_key
Contoh pengaturan:
school_name
active_semester
report_approval_required
news_approval_required
maximum_upload_size
receipt_number_format
Pengaturan yang sering digunakan dan penting tidak seluruhnya harus disimpan sebagai JSON besar. Setiap pengaturan dibuat sebagai record tersendiri.

8. TABEL IDENTITAS DAN PENGGUNA
8.1 Tabel people
Menyimpan identitas dasar seseorang.
Kolom	Tipe	Aturan
id	BIGINT	Primary key
national_id_number	VARCHAR(32)	Unique, nullable
full_name	VARCHAR(150)	Wajib
birth_place	VARCHAR(100)	Nullable
birth_date	DATE	Nullable
gender	VARCHAR(20)	Nullable
religion	VARCHAR(30)	Nullable
email	VARCHAR(191)	Nullable
phone	VARCHAR(30)	Nullable
address	TEXT	Nullable
photo_file_id	BIGINT	Nullable
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
Catatan:
* NIK termasuk data sensitif.
* NIK tidak ditampilkan kepada pengguna umum.
* Aksesnya dibatasi melalui permission.
* Enkripsi pada aplikasi akan dipertimbangkan saat implementasi.
8.2 Tabel users
Menyimpan akun login.
Kolom	Tipe	Aturan
id	BIGINT	Primary key
person_id	BIGINT	Unique, nullable
username	VARCHAR(100)	Unique
email	VARCHAR(191)	Unique, nullable
password	VARCHAR(255)	Wajib
account_type	VARCHAR(30)	Wajib
status	VARCHAR(30)	Default active
email_verified_at	DATETIME	Nullable
last_login_at	DATETIME	Nullable
last_login_ip	VARCHAR(45)	Nullable
password_changed_at	DATETIME	Nullable
failed_login_count	SMALLINT	Default 0
locked_until	DATETIME	Nullable
remember_token	VARCHAR(100)	Nullable
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
deleted_at	DATETIME	Nullable
Contoh account_type:
internal
student
guardian
applicant
Data pengguna tidak sama dengan identitas guru atau siswa. Akun hanya digunakan untuk masuk ke sistem. Pemisahan role, jabatan, penugasan, dan identitas merupakan keputusan dasar sistem.
8.3 Tabel employees
Menyimpan guru dan tenaga kependidikan.
Kolom	Tipe	Aturan
id	BIGINT	Primary key
person_id	BIGINT	Unique, wajib
employee_number	VARCHAR(50)	Unique
nip	VARCHAR(30)	Unique, nullable
nuptk	VARCHAR(30)	Unique, nullable
employee_type	VARCHAR(30)	Wajib
employment_status	VARCHAR(50)	Nullable
join_date	DATE	Nullable
leave_date	DATE	Nullable
last_education	VARCHAR(100)	Nullable
is_teacher	BOOLEAN	Default false
is_active	BOOLEAN	Default true
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
Contoh employee_type:
teacher
education_staff
administrative_staff
contractor
8.4 Tabel students
Kolom	Tipe	Aturan
id	BIGINT	Primary key
person_id	BIGINT	Unique, wajib
nis	VARCHAR(30)	Unique
nisn	VARCHAR(30)	Unique, nullable
admission_number	VARCHAR(50)	Unique, nullable
admission_date	DATE	Wajib
origin_school	VARCHAR(150)	Nullable
current_status	VARCHAR(30)	Wajib
graduation_date	DATE	Nullable
alumni_year	SMALLINT	Nullable
special_needs_notes	TEXT	Nullable
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
current_status hanya digunakan untuk mempercepat pencarian status terkini.
Histori status tetap berada pada:
student_status_histories
8.5 Tabel guardians
Kolom	Tipe	Aturan
id	BIGINT	Primary key
person_id	BIGINT	Unique, wajib
occupation	VARCHAR(100)	Nullable
education_level	VARCHAR(50)	Nullable
monthly_income	DECIMAL(15,2)	Nullable
workplace	VARCHAR(150)	Nullable
is_active	BOOLEAN	Default true
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
8.6 Tabel student_guardians
Kolom	Tipe	Aturan
id	BIGINT	Primary key
student_id	BIGINT	Foreign key
guardian_id	BIGINT	Foreign key
relationship_type	VARCHAR(30)	Wajib
is_primary_contact	BOOLEAN	Default false
is_financial_responsible	BOOLEAN	Default false
can_access_portal	BOOLEAN	Default true
started_at	DATE	Nullable
ended_at	DATE	Nullable
status	VARCHAR(30)	Default active
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
Unique constraint:
student_id + guardian_id + relationship_type

9. TABEL ROLE DAN PERMISSION
9.1 Tabel roles
Kolom	Tipe	Aturan
id	BIGINT	Primary key
name	VARCHAR(100)	Unique
display_name	VARCHAR(150)	Wajib
description	TEXT	Nullable
is_system	BOOLEAN	Default false
is_active	BOOLEAN	Default true
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
9.2 Tabel permissions
Kolom	Tipe	Aturan
id	BIGINT	Primary key
name	VARCHAR(150)	Unique
module	VARCHAR(100)	Wajib
action	VARCHAR(50)	Wajib
display_name	VARCHAR(150)	Wajib
description	TEXT	Nullable
is_active	BOOLEAN	Default true
Contoh:
students.view
students.create
students.update
scores.input
scores.verify
scores.publish
payments.create
payments.correct
reports.export
9.3 Tabel user_roles
Kolom	Tipe
user_id	BIGINT
role_id	BIGINT
assigned_by	BIGINT, nullable
assigned_at	DATETIME
expires_at	DATETIME, nullable
Primary atau unique constraint:
user_id + role_id
9.4 Tabel role_permissions
Kolom	Tipe
role_id	BIGINT
permission_id	BIGINT
Unique constraint:
role_id + permission_id
9.5 Tabel user_permissions
Digunakan hanya jika pengguna membutuhkan permission langsung.
Kolom	Tipe
user_id	BIGINT
permission_id	BIGINT
permission_mode	VARCHAR(20)
assigned_by	BIGINT, nullable
expires_at	DATETIME, nullable
permission_mode dapat berupa:
allow
deny
Permission langsung tidak digunakan secara berlebihan karena dapat membuat pengelolaan akses sulit dipahami.

10. TABEL ORGANISASI DAN JABATAN
10.1 Tabel organizational_units
Kolom	Tipe
id	BIGINT
madrasah_id	BIGINT
parent_id	BIGINT, nullable
code	VARCHAR(30)
name	VARCHAR(150)
description	TEXT, nullable
is_active	BOOLEAN
sort_order	SMALLINT
10.2 Tabel positions
Kolom	Tipe
id	BIGINT
organizational_unit_id	BIGINT
code	VARCHAR(30)
name	VARCHAR(150)
level	SMALLINT, nullable
is_active	BOOLEAN
10.3 Tabel employee_position_histories
Kolom	Tipe
id	BIGINT
employee_id	BIGINT
position_id	BIGINT
academic_year_id	BIGINT, nullable
start_date	DATE
end_date	DATE, nullable
decree_number	VARCHAR(100), nullable
decree_file_id	BIGINT, nullable
status	VARCHAR(30)
created_by	BIGINT, nullable
Satu pegawai dapat memiliki beberapa riwayat jabatan.
Record lama tidak diperbarui menjadi jabatan baru. Record lama ditutup dengan end_date, kemudian record baru dibuat.

11. TABEL TAHUN AJARAN DAN SEMESTER
11.1 Tabel academic_years
Kolom	Tipe	Aturan
id	BIGINT	Primary key
code	VARCHAR(20)	Unique
name	VARCHAR(30)	Wajib
start_date	DATE	Wajib
end_date	DATE	Wajib
status	VARCHAR(30)	Wajib
is_active	BOOLEAN	Default false
is_locked	BOOLEAN	Default false
locked_at	DATETIME	Nullable
locked_by	BIGINT	Nullable
created_at	DATETIME	Otomatis
updated_at	DATETIME	Otomatis
Contoh:
code: 2026-2027
name: 2026/2027
11.2 Tabel semesters
Kolom	Tipe	Aturan
id	BIGINT	Primary key
academic_year_id	BIGINT	Foreign key
code	VARCHAR(20)	Wajib
name	VARCHAR(50)	Wajib
sequence	SMALLINT	Wajib
start_date	DATE	Wajib
end_date	DATE	Wajib
status	VARCHAR(30)	Wajib
is_active	BOOLEAN	Default false
is_locked	BOOLEAN	Default false
locked_at	DATETIME	Nullable
locked_by	BIGINT	Nullable
Unique constraint:
academic_year_id + code
Contoh:
GANJIL
GENAP
Sistem hanya mengizinkan satu semester aktif. Aturan ini dijaga melalui Service Class dan database transaction.

12. TABEL KELAS DAN RIWAYAT SISWA
12.1 Tabel grade_levels
Kolom	Tipe
id	BIGINT
code	VARCHAR(20)
name	VARCHAR(50)
sequence	SMALLINT
is_active	BOOLEAN
Contoh:
VII
VIII
IX
12.2 Tabel rooms
Tabel ini digunakan bersama oleh akademik, inventaris, dan laboratorium.
Kolom	Tipe
id	BIGINT
code	VARCHAR(30)
name	VARCHAR(100)
building_name	VARCHAR(100), nullable
floor_number	SMALLINT, nullable
capacity	SMALLINT, nullable
room_type	VARCHAR(50)
condition_status	VARCHAR(30)
responsible_employee_id	BIGINT, nullable
is_active	BOOLEAN
12.3 Tabel class_groups
Rombongan belajar dibuat berdasarkan tahun ajaran.
Kolom	Tipe
id	BIGINT
academic_year_id	BIGINT
grade_level_id	BIGINT
room_id	BIGINT, nullable
code	VARCHAR(30)
name	VARCHAR(100)
capacity	SMALLINT
program_name	VARCHAR(100), nullable
status	VARCHAR(30)
created_at	DATETIME
updated_at	DATETIME
Unique constraint:
academic_year_id + code
12.4 Tabel student_enrollments
Tabel ini merupakan pusat histori kelas siswa.
Kolom	Tipe
id	BIGINT
student_id	BIGINT
semester_id	BIGINT
class_group_id	BIGINT
previous_enrollment_id	BIGINT, nullable
enrollment_status	VARCHAR(30)
effective_from	DATE
effective_to	DATE, nullable
is_current	BOOLEAN
change_reason	TEXT, nullable
processed_by	BIGINT, nullable
created_at	DATETIME
updated_at	DATETIME
Contoh status:
active
transferred
completed
promoted
retained
graduated
closed
Satu siswa dapat memiliki lebih dari satu record dalam semester yang sama jika terjadi perpindahan kelas.
Sistem harus memastikan hanya satu record yang berstatus aktif dan is_current = true.
Data historis berbasis periode harus dibuat sebagai record baru, bukan menimpa record lama.
12.5 Tabel student_status_histories
Kolom	Tipe
id	BIGINT
student_id	BIGINT
semester_id	BIGINT, nullable
old_status	VARCHAR(30), nullable
new_status	VARCHAR(30)
effective_date	DATE
reason	TEXT, nullable
supporting_file_id	BIGINT, nullable
changed_by	BIGINT
created_at	DATETIME
13. TABEL WALI KELAS DAN PENUGASAN
13.1 Tabel homeroom_assignments
Kolom	Tipe
id	BIGINT
employee_id	BIGINT
class_group_id	BIGINT
semester_id	BIGINT
assignment_type	VARCHAR(30)
start_date	DATE
end_date	DATE, nullable
status	VARCHAR(30)
decree_file_id	BIGINT, nullable
created_by	BIGINT
Unique constraint konseptual:
class_group_id + semester_id + assignment_type + status aktif
13.2 Tabel subjects
Kolom	Tipe
id	BIGINT
code	VARCHAR(30)
name	VARCHAR(150)
subject_group	VARCHAR(50), nullable
description	TEXT, nullable
is_active	BOOLEAN
created_at	DATETIME
updated_at	DATETIME
13.3 Tabel curricula
Kolom	Tipe
id	BIGINT
code	VARCHAR(30)
name	VARCHAR(150)
effective_year	SMALLINT
description	TEXT, nullable
document_file_id	BIGINT, nullable
is_active	BOOLEAN
13.4 Tabel curriculum_subjects
Kolom	Tipe
id	BIGINT
curriculum_id	BIGINT
subject_id	BIGINT
grade_level_id	BIGINT
weekly_hours	DECIMAL(5,2)
report_order	SMALLINT
is_required	BOOLEAN
is_active	BOOLEAN
Unique constraint:
curriculum_id + subject_id + grade_level_id
13.5 Tabel teaching_assignments
Kolom	Tipe
id	BIGINT
semester_id	BIGINT
employee_id	BIGINT
curriculum_subject_id	BIGINT
class_group_id	BIGINT
weekly_hours	DECIMAL(5,2)
start_date	DATE
end_date	DATE, nullable
status	VARCHAR(30)
assignment_file_id	BIGINT, nullable
created_by	BIGINT
created_at	DATETIME
updated_at	DATETIME
Unique constraint:
semester_id
+ employee_id
+ curriculum_subject_id
+ class_group_id
Guru hanya memperoleh akses nilai, jurnal, dan kehadiran dari penugasan aktifnya.

14. TABEL JADWAL
14.1 Tabel lesson_periods
Kolom	Tipe
id	BIGINT
period_number	SMALLINT
name	VARCHAR(50)
start_time	TIME
end_time	TIME
period_type	VARCHAR(30)
is_active	BOOLEAN
Contoh period_type:
lesson
break
ceremony
worship
14.2 Tabel schedules
Kolom	Tipe
id	BIGINT
teaching_assignment_id	BIGINT
lesson_period_id	BIGINT
room_id	BIGINT, nullable
day_of_week	SMALLINT
effective_from	DATE
effective_to	DATE, nullable
status	VARCHAR(30)
created_by	BIGINT
created_at	DATETIME
updated_at	DATETIME
Index:
day_of_week + lesson_period_id
teaching_assignment_id + status
room_id + day_of_week + lesson_period_id
Bentrok guru, kelas, dan ruangan diperiksa melalui Service Class sebelum jadwal disimpan.

15. TABEL PERANGKAT DAN JURNAL PEMBELAJARAN
15.1 Tabel learning_documents
Kolom	Tipe
id	BIGINT
teaching_assignment_id	BIGINT
document_type	VARCHAR(50)
title	VARCHAR(255)
description	TEXT, nullable
file_id	BIGINT
version_number	SMALLINT
status	VARCHAR(30)
reviewed_by	BIGINT, nullable
reviewed_at	DATETIME, nullable
review_notes	TEXT, nullable
uploaded_by	BIGINT
created_at	DATETIME
updated_at	DATETIME
Contoh document_type:
atp
teaching_module
annual_program
semester_program
learning_material
learning_media
15.2 Tabel teaching_journals
Kolom	Tipe
id	BIGINT
teaching_assignment_id	BIGINT
schedule_id	BIGINT, nullable
meeting_date	DATE
meeting_number	SMALLINT, nullable
learning_objective	TEXT, nullable
material	TEXT
method	TEXT, nullable
activity_notes	TEXT, nullable
follow_up	TEXT, nullable
attachment_file_id	BIGINT, nullable
status	VARCHAR(30)
created_by	BIGINT
created_at	DATETIME
updated_at	DATETIME
Unique constraint konseptual:
teaching_assignment_id + meeting_date + meeting_number

16. TABEL KEHADIRAN SISWA
16.1 Tabel attendance_sessions
Kolom	Tipe
id	BIGINT
semester_id	BIGINT
class_group_id	BIGINT
teaching_assignment_id	BIGINT, nullable
schedule_id	BIGINT, nullable
attendance_date	DATE
session_type	VARCHAR(30)
status	VARCHAR(30)
opened_by	BIGINT
closed_at	DATETIME, nullable
created_at	DATETIME
updated_at	DATETIME
Contoh session_type:
daily
lesson
ceremony
activity
16.2 Tabel attendance_records
Kolom	Tipe
id	BIGINT
attendance_session_id	BIGINT
student_enrollment_id	BIGINT
attendance_status	VARCHAR(30)
arrival_time	TIME, nullable
departure_time	TIME, nullable
notes	TEXT, nullable
evidence_file_id	BIGINT, nullable
recorded_by	BIGINT
created_at	DATETIME
updated_at	DATETIME
Unique constraint:
attendance_session_id + student_enrollment_id
16.3 Tabel attendance_corrections
Kolom	Tipe
id	BIGINT
attendance_record_id	BIGINT
old_status	VARCHAR(30)
new_status	VARCHAR(30)
reason	TEXT
corrected_by	BIGINT
corrected_at	DATETIME
Koreksi kehadiran tidak hanya mengganti status. Sistem juga membuat record koreksi dan audit log.

17. TABEL PENILAIAN
17.1 Tabel assessment_components
Kolom	Tipe
id	BIGINT
teaching_assignment_id	BIGINT
name	VARCHAR(150)
assessment_type	VARCHAR(50)
assessment_date	DATE, nullable
weight	DECIMAL(5,2)
maximum_score	DECIMAL(5,2)
minimum_score	DECIMAL(5,2)
sequence	SMALLINT
status	VARCHAR(30)
created_by	BIGINT
created_at	DATETIME
updated_at	DATETIME
Contoh assessment_type:
assignment
formative
summative
practice
project
exam
remedial
enrichment
17.2 Tabel assessment_scores
Kolom	Tipe
id	BIGINT
assessment_component_id	BIGINT
student_enrollment_id	BIGINT
original_score	DECIMAL(5,2), nullable
remedial_score	DECIMAL(5,2), nullable
final_score	DECIMAL(5,2), nullable
achievement_description	TEXT, nullable
status	VARCHAR(30)
verified_by	BIGINT, nullable
verified_at	DATETIME, nullable
published_by	BIGINT, nullable
published_at	DATETIME, nullable
created_at	DATETIME
updated_at	DATETIME
Unique constraint:
assessment_component_id + student_enrollment_id
17.3 Tabel assessment_score_revisions
Kolom	Tipe
id	BIGINT
assessment_score_id	BIGINT
old_score	DECIMAL(5,2), nullable
new_score	DECIMAL(5,2), nullable
reason	TEXT
revision_status	VARCHAR(30)
requested_by	BIGINT
verified_by	BIGINT, nullable
created_at	DATETIME
verified_at	DATETIME, nullable
Nilai yang telah dipublikasikan tidak boleh dikoreksi tanpa alasan, permission khusus, verifikasi ulang, dan audit log.

18. TABEL RAPOR
18.1 Tabel report_cards
Kolom	Tipe
id	BIGINT
student_enrollment_id	BIGINT
report_number	VARCHAR(100)
status	VARCHAR(30)
current_version_number	SMALLINT
created_by	BIGINT
created_at	DATETIME
updated_at	DATETIME
Unique constraint:
student_enrollment_id
18.2 Tabel report_card_versions
Kolom	Tipe
id	BIGINT
report_card_id	BIGINT
version_number	SMALLINT
status	VARCHAR(30)
homeroom_notes	TEXT, nullable
promotion_status	VARCHAR(30), nullable
attendance_summary	LONGTEXT, nullable
snapshot_data	LONGTEXT
pdf_file_id	BIGINT, nullable
submitted_by	BIGINT, nullable
verified_by	BIGINT, nullable
approved_by	BIGINT, nullable
published_by	BIGINT, nullable
submitted_at	DATETIME, nullable
verified_at	DATETIME, nullable
approved_at	DATETIME, nullable
published_at	DATETIME, nullable
revision_reason	TEXT, nullable
is_official	BOOLEAN
Unique constraint:
report_card_id + version_number
18.3 Tabel report_card_items
Kolom	Tipe
id	BIGINT
report_card_version_id	BIGINT
subject_id	BIGINT
final_score	DECIMAL(5,2), nullable
predicate	VARCHAR(10), nullable
achievement_description	TEXT, nullable
sort_order	SMALLINT
Unique constraint:
report_card_version_id + subject_id
Rapor membutuhkan snapshot agar rapor lama tidak berubah ketika data sumber dikoreksi. Proses penerbitan rapor termasuk proses penting yang menggunakan database transaction.

19. TABEL KESISWAAN
19.1 Tabel achievements
Kolom	Tipe
id	BIGINT
student_enrollment_id	BIGINT
achievement_type	VARCHAR(50)
competition_level	VARCHAR(50)
activity_name	VARCHAR(255)
organizer	VARCHAR(150), nullable
achievement_date	DATE
rank	VARCHAR(50), nullable
supervisor_employee_id	BIGINT, nullable
certificate_file_id	BIGINT, nullable
photo_file_id	BIGINT, nullable
verification_status	VARCHAR(30)
verified_by	BIGINT, nullable
published_at	DATETIME, nullable
19.2 Tabel violation_categories
Kolom	Tipe
id	BIGINT
code	VARCHAR(30)
name	VARCHAR(150)
severity_level	VARCHAR(30)
default_points	SMALLINT
is_active	BOOLEAN
19.3 Tabel student_violations
Kolom	Tipe
id	BIGINT
student_enrollment_id	BIGINT
violation_category_id	BIGINT
incident_date	DATE
chronology	TEXT
points	SMALLINT
reported_by	BIGINT
evidence_file_id	BIGINT, nullable
status	VARCHAR(30)
created_at	DATETIME
updated_at	DATETIME
19.4 Tabel violation_actions
Kolom	Tipe
id	BIGINT
student_violation_id	BIGINT
action_type	VARCHAR(50)
action_date	DATE
description	TEXT
handled_by	BIGINT
supporting_file_id	BIGINT, nullable
follow_up_date	DATE, nullable
19.5 Tabel counseling_sessions
Kolom	Tipe
id	BIGINT
student_enrollment_id	BIGINT
counselor_employee_id	BIGINT
session_date	DATE
counseling_type	VARCHAR(50)
topic	VARCHAR(255)
problem_description	LONGTEXT, nullable
assessment_result	LONGTEXT, nullable
action_taken	LONGTEXT, nullable
follow_up_plan	LONGTEXT, nullable
confidentiality_level	VARCHAR(30)
status	VARCHAR(30)
created_at	DATETIME
updated_at	DATETIME
19.6 Tabel counseling_accesses
Digunakan jika catatan tertentu dibagikan kepada pengguna khusus.
Kolom	Tipe
counseling_session_id	BIGINT
user_id	BIGINT
access_level	VARCHAR(30)
granted_by	BIGINT
expires_at	DATETIME, nullable
20. TABEL EKSTRAKURIKULER, TAHFIDZ, DAN PEMBIASAAN
20.1 Tabel extracurriculars
Kolom	Tipe
id	BIGINT
code	VARCHAR(30)
name	VARCHAR(150)
description	TEXT, nullable
is_active	BOOLEAN
20.2 Tabel extracurricular_periods
Kolom	Tipe
id	BIGINT
extracurricular_id	BIGINT
semester_id	BIGINT
supervisor_employee_id	BIGINT
schedule_description	TEXT, nullable
location	VARCHAR(150), nullable
status	VARCHAR(30)
20.3 Tabel extracurricular_memberships
Kolom	Tipe
id	BIGINT
extracurricular_period_id	BIGINT
student_enrollment_id	BIGINT
join_date	DATE
membership_status	VARCHAR(30)
final_grade	VARCHAR(20), nullable
description	TEXT, nullable
Unique constraint:
extracurricular_period_id + student_enrollment_id
20.4 Tabel tahfidz_targets
Kolom	Tipe
id	BIGINT
student_enrollment_id	BIGINT
surah_start	VARCHAR(100), nullable
surah_end	VARCHAR(100), nullable
juz_target	VARCHAR(50), nullable
target_description	TEXT
status	VARCHAR(30)
20.5 Tabel tahfidz_records
Kolom	Tipe
id	BIGINT
tahfidz_target_id	BIGINT
record_date	DATE
record_type	VARCHAR(30)
surah_name	VARCHAR(100)
verse_start	SMALLINT, nullable
verse_end	SMALLINT, nullable
juz_number	SMALLINT, nullable
fluency_score	DECIMAL(5,2), nullable
tajwid_score	DECIMAL(5,2), nullable
notes	TEXT, nullable
examiner_employee_id	BIGINT
20.6 Tabel habit_types
Kolom	Tipe
id	BIGINT
code	VARCHAR(30)
name	VARCHAR(150)
category	VARCHAR(50)
is_active	BOOLEAN
20.7 Tabel habit_records
Kolom	Tipe
id	BIGINT
student_enrollment_id	BIGINT
habit_type_id	BIGINT
record_date	DATE
value	VARCHAR(50)
score	DECIMAL(5,2), nullable
notes	TEXT, nullable
recorded_by	BIGINT
21. TABEL QR CODE SISWA
21.1 Tabel student_qr_codes
Kolom	Tipe
id	BIGINT
student_id	BIGINT
token	VARCHAR(100)
access_type	VARCHAR(30)
is_active	BOOLEAN
expires_at	DATETIME, nullable
generated_by	BIGINT
generated_at	DATETIME
revoked_by	BIGINT, nullable
revoked_at	DATETIME, nullable
revocation_reason	TEXT, nullable
Unique constraint:
token
QR Code tidak menyimpan data siswa. QR Code hanya menyimpan token untuk proses pemeriksaan akses.

22. TABEL KEUANGAN SISWA
22.1 Penyempurnaan dari ERD konseptual
Pada ERD konseptual, satu tagihan digambarkan memiliki banyak pembayaran.
Pada desain fisik, kita menambahkan tabel:
payment_allocations
Alasannya, satu transaksi pembayaran dapat digunakan untuk membayar beberapa tagihan sekaligus.
Contoh:
Satu kwitansi:
SPP Juli       Rp300.000
SPP Agustus    Rp300.000
Total          Rp600.000
Dengan tabel alokasi:
* Satu pembayaran dapat dialokasikan ke beberapa tagihan.
* Satu tagihan dapat menerima beberapa pembayaran cicilan.
Hubungannya menjadi many-to-many melalui payment_allocations.
22.2 Tabel fee_types
Kolom	Tipe
id	BIGINT
code	VARCHAR(30)
name	VARCHAR(150)
billing_frequency	VARCHAR(30)
default_amount	DECIMAL(15,2), nullable
is_mandatory	BOOLEAN
is_active	BOOLEAN
22.3 Tabel billing_batches
Kolom	Tipe
id	BIGINT
batch_number	VARCHAR(50)
fee_type_id	BIGINT
semester_id	BIGINT
target_type	VARCHAR(30)
target_reference_id	BIGINT, nullable
billing_month	SMALLINT, nullable
amount	DECIMAL(15,2)
due_date	DATE
status	VARCHAR(30)
created_by	BIGINT
created_at	DATETIME
22.4 Tabel student_bills
Kolom	Tipe
id	BIGINT
billing_batch_id	BIGINT, nullable
student_id	BIGINT
semester_id	BIGINT
fee_type_id	BIGINT
bill_number	VARCHAR(50)
billing_month	SMALLINT, nullable
original_amount	DECIMAL(15,2)
adjustment_amount	DECIMAL(15,2)
net_amount	DECIMAL(15,2)
paid_amount	DECIMAL(15,2)
remaining_amount	DECIMAL(15,2)
due_date	DATE
status	VARCHAR(30)
created_at	DATETIME
updated_at	DATETIME
Unique constraint konseptual:
student_id
+ semester_id
+ fee_type_id
+ billing_month
22.5 Tabel bill_adjustments
Kolom	Tipe
id	BIGINT
student_bill_id	BIGINT
adjustment_type	VARCHAR(30)
amount	DECIMAL(15,2)
reason	TEXT
approval_status	VARCHAR(30)
requested_by	BIGINT
approved_by	BIGINT, nullable
created_at	DATETIME
approved_at	DATETIME, nullable
Contoh adjustment_type:
discount
relief
waiver
correction
22.6 Tabel payments
Kolom	Tipe
id	BIGINT
transaction_number	VARCHAR(50)
student_id	BIGINT
payment_date	DATETIME
payment_method	VARCHAR(30)
total_amount	DECIMAL(15,2)
received_by	BIGINT
payer_name	VARCHAR(150), nullable
reference_number	VARCHAR(100), nullable
notes	TEXT, nullable
status	VARCHAR(30)
created_at	DATETIME
updated_at	DATETIME
Unique constraint:
transaction_number
22.7 Tabel payment_allocations
Kolom	Tipe
id	BIGINT
payment_id	BIGINT
student_bill_id	BIGINT
allocated_amount	DECIMAL(15,2)
created_at	DATETIME
Unique constraint:
payment_id + student_bill_id
22.8 Tabel payment_corrections
Kolom	Tipe
id	BIGINT
payment_id	BIGINT
correction_type	VARCHAR(30)
old_values	LONGTEXT
new_values	LONGTEXT, nullable
reason	TEXT
status	VARCHAR(30)
requested_by	BIGINT
approved_by	BIGINT, nullable
created_at	DATETIME
approved_at	DATETIME, nullable
22.9 Tabel payment_receipts
Kolom	Tipe
id	BIGINT
payment_id	BIGINT
receipt_number	VARCHAR(50)
version_number	SMALLINT
pdf_file_id	BIGINT, nullable
printed_by	BIGINT
printed_at	DATETIME
Transaksi penting seperti pembayaran wajib menggunakan database transaction dan tidak boleh menghilangkan transaksi lama ketika dikoreksi.

23. TABEL WEBSITE DAN CMS BERITA
23.1 Tabel public_pages
Kolom	Tipe
id	BIGINT
slug	VARCHAR(191)
title	VARCHAR(255)
content	LONGTEXT
page_type	VARCHAR(50)
status	VARCHAR(30)
published_at	DATETIME, nullable
created_by	BIGINT
updated_by	BIGINT, nullable
created_at	DATETIME
updated_at	DATETIME
deleted_at	DATETIME, nullable
23.2 Tabel post_categories
Kolom	Tipe
id	BIGINT
name	VARCHAR(150)
slug	VARCHAR(191)
description	TEXT, nullable
is_active	BOOLEAN
23.3 Tabel posts
Kolom	Tipe
id	BIGINT
post_category_id	BIGINT, nullable
author_user_id	BIGINT
editor_user_id	BIGINT, nullable
title	VARCHAR(255)
slug	VARCHAR(191)
summary	TEXT, nullable
content	LONGTEXT
featured_file_id	BIGINT, nullable
meta_title	VARCHAR(255), nullable
meta_description	VARCHAR(500), nullable
status	VARCHAR(30)
scheduled_at	DATETIME, nullable
published_at	DATETIME, nullable
archived_at	DATETIME, nullable
created_at	DATETIME
updated_at	DATETIME
deleted_at	DATETIME, nullable
Unique constraint:
slug
Index:
status + published_at
post_category_id + status
23.4 Tabel tags
Kolom	Tipe
id	BIGINT
name	VARCHAR(100)
slug	VARCHAR(191)
23.5 Tabel post_tags
Kolom	Tipe
post_id	BIGINT
tag_id	BIGINT
23.6 Tabel post_media
Kolom	Tipe
id	BIGINT
post_id	BIGINT
file_id	BIGINT, nullable
external_url	VARCHAR(500), nullable
media_type	VARCHAR(30)
caption	TEXT, nullable
sort_order	SMALLINT
23.7 Tabel post_reviews
Kolom	Tipe
id	BIGINT
post_id	BIGINT
reviewer_user_id	BIGINT
old_status	VARCHAR(30)
new_status	VARCHAR(30)
action	VARCHAR(30)
notes	TEXT, nullable
created_at	DATETIME
Workflow berita, nilai, rapor, eviden, dan PPDB harus menggunakan status yang jelas dan dapat dilacak.

24. TABEL PPDB
24.1 Tabel admission_periods
Kolom	Tipe
id	BIGINT
academic_year_id	BIGINT
name	VARCHAR(150)
start_date	DATE
end_date	DATE
announcement_date	DATE, nullable
status	VARCHAR(30)
is_active	BOOLEAN
24.2 Tabel admission_tracks
Kolom	Tipe
id	BIGINT
admission_period_id	BIGINT
code	VARCHAR(30)
name	VARCHAR(150)
quota	SMALLINT
requirements	LONGTEXT, nullable
is_active	BOOLEAN
24.3 Tabel applicants
Kolom	Tipe
id	BIGINT
user_id	BIGINT, nullable
registration_number	VARCHAR(50)
nisn	VARCHAR(30), nullable
full_name	VARCHAR(150)
birth_place	VARCHAR(100), nullable
birth_date	DATE
gender	VARCHAR(20)
origin_school	VARCHAR(150), nullable
address	TEXT, nullable
created_at	DATETIME
updated_at	DATETIME
24.4 Tabel applications
Kolom	Tipe
id	BIGINT
applicant_id	BIGINT
admission_period_id	BIGINT
admission_track_id	BIGINT
status	VARCHAR(30)
submitted_at	DATETIME, nullable
verified_at	DATETIME, nullable
selection_result	VARCHAR(30), nullable
final_score	DECIMAL(8,2), nullable
converted_at	DATETIME, nullable
created_at	DATETIME
updated_at	DATETIME
Unique constraint:
applicant_id + admission_period_id
24.5 Tabel pendukung PPDB
Tabel tambahan:
applicant_guardians
application_documents
application_verifications
selection_components
selection_scores
re_registrations
applicant_conversions
applicant_conversions menyimpan hubungan antara:
application_id
student_id
converted_by
converted_at
Konversi PPDB harus menggunakan database transaction agar data siswa, orang tua, dokumen, status, dan akun tidak terbentuk sebagian.

25. TABEL TATA USAHA
Tabel utama:
incoming_letters
letter_dispositions
outgoing_letters
letter_approvals
decrees
partnerships
archive_categories
archives
Setiap surat minimal menyimpan:
* Nomor surat.
* Nomor agenda.
* Tanggal surat.
* Tanggal diterima atau diterbitkan.
* Pengirim atau tujuan.
* Perihal.
* Status.
* File.
* Pengguna yang memproses.
Surat keluar menggunakan tabel letter_approvals agar status draft, diajukan, disetujui, dan diterbitkan dapat dilacak.

26. TABEL KEPEGAWAIAN
Tabel utama:
employee_education_histories
employee_rank_histories
employee_status_histories
employee_attendance_sessions
employee_attendance_records
training_activities
employee_trainings
employee_certificates
Setiap data riwayat menggunakan:
start_date
end_date
status
supporting_file_id
Data kepegawaian tidak ditimpa ketika pegawai memperoleh pendidikan, jabatan, pangkat, atau sertifikat baru.

27. TABEL INVENTARIS
27.1 Tabel utama
item_categories
inventory_items
inventory_units
inventory_movements
inventory_loans
inventory_loan_items
maintenance_records
inventory_condition_histories
disposal_proposals
27.2 Perbedaan item dan unit
inventory_items menyimpan jenis barang.
Contoh:
Laptop Lenovo ThinkPad
Meja Siswa
Proyektor Epson
inventory_units menyimpan unit fisik.
Contoh:
Laptop-001
Laptop-002
Laptop-003
Setiap unit dapat memiliki:
* Kode aset.
* QR Code.
* Ruangan.
* Kondisi.
* Harga perolehan.
* Tahun perolehan.
* Status.

28. TABEL LABORATORIUM
Tabel utama:
laboratories
lab_item_categories
lab_items
lab_stock_movements
lab_usage_schedules
lab_loans
lab_loan_items
lab_maintenance_records
Bahan habis pakai menggunakan pergerakan stok.
Alat yang dilacak secara individual dapat memiliki kode unit.

29. TABEL PERPUSTAKAAN
Tabel utama:
book_categories
authors
publishers
books
book_authors
book_copies
library_members
book_loans
book_loan_items
library_fines
ebooks
29.1 Buku dan eksemplar dipisahkan
books menyimpan informasi judul.
book_copies menyimpan eksemplar fisik.
Contoh:
Buku:
Matematika Kelas IX

Eksemplar:
MTK-IX-001
MTK-IX-002
MTK-IX-003
Satu judul dapat memiliki banyak eksemplar.

30. TABEL PKKM DAN AKREDITASI
Tabel utama:
quality_frameworks
quality_periods
quality_nodes
quality_assignments
quality_evidences
quality_evidence_reviews
quality_scores
work_programs
work_program_realizations
30.1 Struktur quality_nodes
Kolom	Tipe
id	BIGINT
quality_framework_id	BIGINT
parent_id	BIGINT, nullable
node_type	VARCHAR(30)
code	VARCHAR(50)
name	VARCHAR(255)
description	LONGTEXT, nullable
weight	DECIMAL(8,2), nullable
is_required	BOOLEAN
sort_order	SMALLINT
is_active	BOOLEAN
Contoh node_type:
standard
component
subcomponent
indicator
subindicator
Struktur ini memungkinkan PKKM dan akreditasi memiliki tingkat indikator yang berbeda tanpa membuat tabel baru.

31. TABEL FILE DAN DOKUMEN
31.1 Tabel files
Kolom	Tipe
id	BIGINT
original_name	VARCHAR(255)
stored_name	VARCHAR(255)
file_path	VARCHAR(500)
disk_name	VARCHAR(50)
extension	VARCHAR(20)
mime_type	VARCHAR(150)
file_size	BIGINT
file_hash	VARCHAR(100), nullable
visibility	VARCHAR(20)
uploaded_by	BIGINT, nullable
uploaded_at	DATETIME
status	VARCHAR(30)
File privat tidak boleh diakses langsung melalui URL publik.
Download file privat harus melewati:
* Controller.
* Authentication.
* Permission.
* Policy.
* Relasi pengguna dengan data.
31.2 Tabel relasi file
File dihubungkan melalui tabel domain, misalnya:
student_documents
employee_documents
application_documents
post_media
quality_evidences
learning_documents
archive_files
Pusat dokumen tidak menyimpan salinan file. Pusat dokumen hanya membaca metadata dan relasi file dari modul sumber. Prinsip ini juga berlaku pada dashboard, portal, dan portofolio.

32. TABEL NOTIFIKASI, IMPORT, LOG, DAN BACKUP
32.1 Tabel notifications
Kolom	Tipe
id	CHAR(36) atau VARCHAR(100)
user_id	BIGINT
notification_type	VARCHAR(100)
title	VARCHAR(255)
message	TEXT
data_payload	LONGTEXT, nullable
read_at	DATETIME, nullable
created_at	DATETIME
32.2 Tabel import_batches
Kolom	Tipe
id	BIGINT
import_type	VARCHAR(50)
file_id	BIGINT
template_version	VARCHAR(30)
total_rows	INTEGER
valid_rows	INTEGER
failed_rows	INTEGER
status	VARCHAR(30)
started_by	BIGINT
started_at	DATETIME
completed_at	DATETIME, nullable
32.3 Tabel import_failures
Kolom	Tipe
id	BIGINT
import_batch_id	BIGINT
row_number	INTEGER
row_data	LONGTEXT
error_messages	LONGTEXT
created_at	DATETIME
Import wajib melalui preview dan validasi. Data tidak boleh langsung dimasukkan hanya karena file berhasil diunggah.
32.4 Tabel activity_logs
Kolom	Tipe
id	BIGINT
user_id	BIGINT, nullable
module	VARCHAR(100)
activity	VARCHAR(150)
description	TEXT, nullable
subject_type	VARCHAR(150), nullable
subject_id	BIGINT, nullable
ip_address	VARCHAR(45), nullable
user_agent	TEXT, nullable
created_at	DATETIME
32.5 Tabel audit_logs
Kolom	Tipe
id	BIGINT
user_id	BIGINT, nullable
module	VARCHAR(100)
auditable_type	VARCHAR(150)
auditable_id	BIGINT
action	VARCHAR(50)
old_values	LONGTEXT, nullable
new_values	LONGTEXT, nullable
reason	TEXT, nullable
reference_number	VARCHAR(100), nullable
ip_address	VARCHAR(45), nullable
user_agent	TEXT, nullable
created_at	DATETIME
Index:
auditable_type + auditable_id
user_id + created_at
module + created_at
action + created_at
Audit log wajib untuk nilai, rapor, pembayaran, absensi, status siswa, penempatan kelas, role, permission, berita, eviden, inventaris, dan restore database.
32.6 Tabel backups
Kolom	Tipe
id	BIGINT
backup_type	VARCHAR(30)
file_id	BIGINT
file_size	BIGINT
status	VARCHAR(30)
created_by	BIGINT
created_at	DATETIME
expired_at	DATETIME, nullable
32.7 Tabel restore_logs
Kolom	Tipe
id	BIGINT
backup_id	BIGINT
status	VARCHAR(30)
started_by	BIGINT
started_at	DATETIME
completed_at	DATETIME, nullable
notes	LONGTEXT, nullable
pre_restore_backup_id	BIGINT, nullable
33. INDEX DATABASE
33.1 Konsep sederhana
Index dapat dianalogikan sebagai daftar isi buku.
Tanpa daftar isi, database harus memeriksa banyak record untuk menemukan data tertentu.
Namun, terlalu banyak index juga memperlambat penyimpanan.
Karena itu, index diberikan pada kolom yang sering digunakan untuk:
* Pencarian.
* Filter.
* Relasi.
* Pengurutan.
* Laporan.
33.2 Index wajib
Semua foreign key secara umum harus memiliki index.
Contoh:
student_id
semester_id
employee_id
class_group_id
teaching_assignment_id
33.3 Composite index penting
Riwayat kelas
student_id + semester_id + is_current
class_group_id + semester_id + enrollment_status
Penugasan guru
employee_id + semester_id + status
class_group_id + semester_id + status
Kehadiran
attendance_date + class_group_id
student_enrollment_id + attendance_status
Nilai
student_enrollment_id + status
assessment_component_id + status
Tagihan
student_id + semester_id + status
due_date + status
Pembayaran
payment_date + status
student_id + payment_date
Berita
status + published_at
post_category_id + status
Audit
auditable_type + auditable_id + created_at
module + created_at

34. UNIQUE CONSTRAINT UTAMA
Tabel	Unique Constraint
users	username
users	email
students	nis
students	nisn
employees	employee_number
employees	nip
roles	name
permissions	name
academic_years	code
semesters	academic_year_id + code
class_groups	academic_year_id + code
subjects	code
student_guardians	student_id + guardian_id + relationship_type
teaching_assignments	semester_id + employee_id + curriculum_subject_id + class_group_id
assessment_scores	assessment_component_id + student_enrollment_id
report_cards	student_enrollment_id
payments	transaction_number
posts	slug
student_qr_codes	token
applicants	registration_number
35. DATA YANG BOLEH NULL
Kolom boleh NULL hanya ketika informasi tersebut memang:
* Belum tersedia.
* Tidak berlaku.
* Opsional.
* Menunggu proses berikutnya.
Contoh yang boleh NULL:
nip
nuptk
nisn
graduation_date
approved_by
published_at
attachment_file_id
end_date
Contoh yang tidak boleh NULL:
student_id
semester_id
status
payment_amount
attendance_status
transaction_number
assessment_component_id
Hindari membuat hampir semua kolom nullable karena dapat menghasilkan data tidak lengkap.

36. STATUS UTAMA SISTEM
36.1 Status akun
active
inactive
locked
suspended
36.2 Status siswa
prospective
active
inactive
transferred
graduated
alumni
withdrawn
deceased
36.3 Status nilai
draft
incomplete
submitted
revision
verified
published
locked
36.4 Status rapor
draft
submitted
verified
approved
published
archived
revised
36.5 Status tagihan
unpaid
partial
paid
waived
cancelled
36.6 Status pembayaran
completed
cancelled
corrected
pending
36.7 Status berita
draft
submitted
in_review
revision
approved
scheduled
published
archived
36.8 Status eviden
empty
submitted
in_review
revision
verified
rejected
36.9 Status PPDB
draft
submitted
administratively_verified
selection
accepted
waiting_list
rejected
re_registration
converted
cancelled

37. DATABASE TRANSACTION WAJIB
Database transaction wajib digunakan pada proses berikut:
1. Konversi pendaftar menjadi siswa.
2. Pembuatan tagihan massal.
3. Pencatatan pembayaran.
4. Alokasi pembayaran.
5. Koreksi pembayaran.
6. Kenaikan kelas massal.
7. Perpindahan kelas.
8. Penerbitan rapor.
9. Koreksi nilai setelah publikasi.
10. Restore database.
Jika salah satu langkah gagal, seluruh perubahan harus dibatalkan agar data tidak setengah tersimpan. Prinsip ini sudah ditetapkan pada flowchart sistem.

38. URUTAN PEMBUATAN MIGRATION
Migration harus dibuat berdasarkan ketergantungan foreign key.
Kelompok 1: Fondasi
1. files
2. madrasahs
3. settings
4. people
5. users
Kelompok 2: Hak Akses
1. roles
2. permissions
3. user_roles
4. role_permissions
5. user_permissions
Kelompok 3: Identitas
1. employees
2. students
3. guardians
4. student_guardians
Kelompok 4: Organisasi
1. organizational_units
2. positions
3. employee_position_histories
Kelompok 5: Periode dan Kelas
1. academic_years
2. semesters
3. grade_levels
4. rooms
5. class_groups
6. student_enrollments
7. student_status_histories
8. homeroom_assignments
Kelompok 6: Akademik
1. curricula
2. subjects
3. curriculum_subjects
4. teaching_assignments
5. lesson_periods
6. schedules
7. learning_documents
8. teaching_journals
Kelompok 7: Kehadiran dan Nilai
1. attendance_sessions
2. attendance_records
3. attendance_corrections
4. assessment_components
5. assessment_scores
6. assessment_score_revisions
7. report_cards
8. report_card_versions
9. report_card_items
Kelompok 8: Kesiswaan
1. achievements
2. violation_categories
3. student_violations
4. violation_actions
5. counseling_sessions
6. counseling_accesses
7. extracurriculars
8. extracurricular_periods
9. extracurricular_memberships
10. tahfidz_targets
11. tahfidz_records
12. habit_types
13. habit_records
14. student_qr_codes
Kelompok 9: Keuangan
1. fee_types
2. billing_batches
3. student_bills
4. bill_adjustments
5. payments
6. payment_allocations
7. payment_corrections
8. payment_receipts
Kelompok 10: Website dan PPDB
1. public_pages
2. post_categories
3. posts
4. tags
5. post_tags
6. post_media
7. post_reviews
8. admission_periods
9. admission_tracks
10. applicants
11. applications
12. tabel pendukung PPDB
Kelompok 11: Administrasi dan Sumber Daya
1. tabel tata usaha
2. tabel kepegawaian
3. tabel inventaris
4. tabel laboratorium
5. tabel perpustakaan
Kelompok 12: Penjaminan Mutu
1. quality_frameworks
2. quality_periods
3. quality_nodes
4. quality_assignments
5. quality_evidences
6. quality_evidence_reviews
7. quality_scores
Kelompok 13: Log dan Pemeliharaan
1. notifications
2. import_batches
3. import_failures
4. activity_logs
5. audit_logs
6. backups
7. restore_logs
Urutan tersebut masih dapat dipecah menjadi file migration lebih kecil ketika implementasi dimulai.

39. RENCANA SEEDER
Seeder awal diperlukan untuk data yang harus tersedia setelah instalasi.
39.1 Role
Seeder membuat 16 role:
1. Super Admin.
2. Kepala Madrasah.
3. Wakamad Kurikulum.
4. Wakamad Kesiswaan.
5. Wakamad Sarpras.
6. Wakamad Humas.
7. Tata Usaha.
8. Bendahara.
9. Wali Kelas.
10. Guru Mata Pelajaran.
11. Guru BK.
12. Petugas Perpustakaan.
13. Petugas Laboratorium.
14. Editor Berita.
15. Orang Tua.
16. Siswa.
39.2 Data referensi
Seeder juga dapat membuat:
* Tingkat kelas.
* Semester ganjil dan genap.
* Status kehadiran.
* Jenis hubungan orang tua.
* Jenis dokumen.
* Status workflow.
* Jenis pelanggaran awal.
* Jenis pembiasaan.
* Kategori file.
* Jenis kelamin.
* Jenis akun.
* Metode pembayaran.
39.3 Super Admin
Seeder awal membuat satu akun Super Admin.
Password awal tidak boleh ditanam secara permanen dalam repository produksi.
Administrator harus mengganti password setelah login pertama.

40. KOMPATIBILITAS SHARED HOSTING
Agar database tetap cocok untuk Hostinger atau Niagahoster:
1. Gunakan MySQL atau MariaDB.
2. Gunakan InnoDB.
3. Hindari stored procedure yang kompleks.
4. Hindari trigger database sebagai logika utama.
5. Hindari materialized view.
6. Hindari ketergantungan fitur database yang terlalu baru.
7. Gunakan LONGTEXT untuk snapshot JSON jika tipe JSON tidak konsisten.
8. Gunakan index secara terukur.
9. Jalankan import secara bertahap.
10. Gunakan pagination.
11. Hindari query dashboard yang mengambil seluruh transaksi.
12. Simpan video besar melalui tautan eksternal.
13. Bersihkan file sementara setelah import.
14. Simpan backup di luar server secara berkala.
Sistem sejak awal memang dibatasi agar tidak bergantung pada Redis, WebSocket, queue permanen, Docker produksi, atau layanan yang tidak sesuai dengan shared hosting.

41. KEPUTUSAN DESAIN DATABASE
Area	Keputusan
Database	MySQL atau MariaDB
Engine	InnoDB
Character set	utf8mb4
Primary key	BIGINT UNSIGNED
Penamaan tabel	English plural snake_case
Penamaan foreign key	singular_entity_id
Uang	DECIMAL(15,2)
Nilai akademik	DECIMAL(5,2)
Status	VARCHAR dengan validasi aplikasi
Snapshot	LONGTEXT berformat JSON
Histori	Record baru atau tabel versi
Authentication	users
Identitas umum	people
Guru dan tendik	employees
Siswa	students
Orang tua	guardians
Relasi orang tua	student_guardians
Riwayat kelas	student_enrollments
Penugasan guru	teaching_assignments
Rapor	Header, versi, dan item
Pembayaran	Payment dan allocation
File	Metadata terpusat
Penghapusan transaksi	Tidak menggunakan hard delete
Audit	activity_logs dan audit_logs
Proses kompleks	Database transaction
Performa	Index dan pagination
42. HASIL TAHAP 6
Tahap Desain Database telah menghasilkan:
1. Standar penamaan database.
2. Standar tipe data.
3. Standar primary key.
4. Standar foreign key.
5. Aturan nullable.
6. Aturan soft delete.
7. Aturan penghapusan.
8. Desain tabel pengguna.
9. Desain role dan permission.
10. Desain identitas guru, siswa, dan orang tua.
11. Desain organisasi dan jabatan.
12. Desain tahun ajaran dan semester.
13. Desain kelas dan riwayat siswa.
14. Desain penugasan guru.
15. Desain jadwal.
16. Desain jurnal mengajar.
17. Desain kehadiran.
18. Desain penilaian.
19. Desain rapor.
20. Desain kesiswaan.
21. Desain keuangan siswa.
22. Desain website dan CMS.
23. Desain PPDB.
24. Desain tata usaha.
25. Desain kepegawaian.
26. Desain inventaris.
27. Desain laboratorium.
28. Desain perpustakaan.
29. Desain PKKM dan akreditasi.
30. Desain file dan dokumen.
31. Desain import.
32. Desain activity log.
33. Desain audit log.
34. Desain backup dan restore.
35. Daftar unique constraint.
36. Daftar composite index.
37. Proses yang membutuhkan database transaction.
38. Urutan pembuatan migration.
39. Rencana seeder.
40. Strategi kompatibilitas shared hosting.
Mengapa Tahap 6 penting
Tahap ini mencegah kita membuat migration secara coba-coba.
Dengan rancangan ini:
* Foreign key dapat dibuat dalam urutan yang benar.
* Histori tetap terjaga.
* Nilai tidak mudah ganda.
* Tagihan tidak mudah terduplikasi.
* Pembayaran cicilan dapat dihitung.
* Rapor dapat memiliki versi.
* Orang tua hanya terhubung dengan anak yang benar.
* Guru hanya mengakses penugasannya.
* Query laporan dapat dioptimalkan.
* Sistem tetap dapat berkembang tanpa mengganti fondasi database.
Tahap berikutnya adalah Tahap 7: Struktur Folder Laravel. Tahap tersebut akan menentukan lokasi controller, model, service, request, policy, enum, view, route, dan file pendukung berdasarkan domain sistem.
