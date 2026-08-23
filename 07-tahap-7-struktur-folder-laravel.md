# Tahap 7 — Struktur Folder Laravel

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 9330–10989.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

=========================================================
TAHAP 7
STRUKTUR FOLDER LARAVEL SIM MADRASAH
1. Konsep Sederhana
Struktur folder adalah aturan penempatan setiap bagian kode dalam aplikasi.
Struktur folder menjawab pertanyaan berikut:
1. Di mana controller disimpan?
2. Di mana model disimpan?
3. Di mana validasi diletakkan?
4. Di mana aturan hak akses ditulis?
5. Di mana logika pembayaran ditempatkan?
6. Di mana tampilan Blade disimpan?
7. Di mana route setiap modul diletakkan?
8. Di mana laporan dan query kompleks ditempatkan?
9. Di mana file import dan export disimpan?
10. Bagaimana setiap modul tetap terpisah dalam satu aplikasi Laravel?
Laravel memiliki struktur dasar yang sudah baku. Controller secara default berada pada app/Http/Controllers, Form Request berada pada app/Http/Requests, view berada pada resources/views, dan route berada pada folder routes.
SIM Madrasah akan mengikuti struktur resmi tersebut. Kita hanya menambahkan pengelompokan berdasarkan domain agar aplikasi yang memiliki banyak modul tetap mudah dipelajari.

2. Analogi Sederhana
Struktur folder dapat dianalogikan sebagai lemari arsip madrasah.
Dokumen tidak diletakkan secara acak.
Contohnya:
* Dokumen akademik masuk ke lemari akademik.
* Dokumen keuangan masuk ke lemari keuangan.
* Surat masuk dan keluar berada di lemari tata usaha.
* Data inventaris berada di lemari sarana.
* Dokumen PKKM berada di lemari penjaminan mutu.
Jika semua dokumen dicampur dalam satu lemari, petugas akan kesulitan mencari, memperbarui, dan memeriksanya.
Kode Laravel bekerja dengan prinsip yang sama.

3. TUJUAN STRUKTUR FOLDER
Struktur folder SIM Madrasah dibuat agar:
1. Pemula mudah menemukan file.
2. Setiap modul memiliki batas tanggung jawab.
3. Controller tidak menjadi terlalu besar.
4. Logika bisnis tidak bercampur dengan tampilan.
5. Validasi tidak ditulis berulang.
6. Hak akses dapat diuji.
7. Query laporan dapat dipisahkan.
8. Fitur dapat dibangun bertahap.
9. Konflik antarmodul dapat dikurangi.
10. Deployment shared hosting tetap sederhana.
11. Pengujian dapat dikelompokkan.
12. Kode mudah dipelihara.
13. Struktur tetap mengikuti konvensi Laravel.
14. Aplikasi dapat dikembangkan tanpa menjadi kumpulan file acak.

4. ARSITEKTUR FOLDER YANG DIPILIH
Kita menggunakan:
Hybrid Modular Monolith
Artinya:
* Sistem tetap menjadi satu aplikasi Laravel.
* Sistem tetap menggunakan satu database.
* Sistem tetap menggunakan satu authentication.
* Sistem tetap menggunakan satu proses deployment.
* Kode dikelompokkan berdasarkan domain.
* Kita tidak membuat package Laravel terpisah untuk setiap modul.
* Kita tidak membuat microservice.
* Kita tidak menggunakan package pembuat modul pada tahap awal.
Tahap 1 telah menetapkan modular monolith karena lebih mudah dipelajari, lebih sederhana untuk deployment shared hosting, dan tidak membutuhkan banyak server.

5. MENGAPA TIDAK MENGGUNAKAN FOLDER Modules
Beberapa proyek Laravel menggunakan struktur seperti:
Modules/
├── Academic/
├── Finance/
├── Student/
└── Library/
Struktur tersebut dapat digunakan, tetapi tidak dipilih pada versi awal SIM Madrasah.
Alasannya:
1. Membutuhkan konfigurasi namespace tambahan.
2. Membutuhkan service provider tambahan.
3. Dapat membutuhkan package pihak ketiga.
4. Menambah konsep yang harus dipahami pemula.
5. Menambah kerumitan route dan migration.
6. Dapat membuat proses deployment lebih rumit.
7. Tidak memberikan manfaat besar pada tahap awal.
8. Dapat membuat pengembangan bergantung pada package modul.
Kita tetap memperoleh pemisahan modular melalui subfolder pada:
* Controller.
* Form Request.
* Service.
* Query.
* View.
* Route.
* Test.
* Import dan export.

6. STRUKTUR ROOT PROJECT
Struktur utama proyek direncanakan sebagai berikut:
sim-madrasah/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── vendor/
├── .env
├── .env.example
├── artisan
├── composer.json
├── package.json
├── phpunit.xml
└── vite.config.js
6.1 Folder app
Berisi kode inti aplikasi.
Contohnya:
* Controller.
* Model.
* Service.
* Policy.
* Form Request.
* Enum.
* Query.
* Middleware.
* Notification.
6.2 Folder bootstrap
Berisi proses awal aplikasi.
Pada struktur Laravel modern, bootstrap/app.php digunakan untuk konfigurasi awal aplikasi dan pemuatan route. Service provider buatan aplikasi didaftarkan melalui bootstrap/providers.php.
6.3 Folder config
Berisi konfigurasi Laravel dan package.
Contohnya:
config/app.php
config/auth.php
config/database.php
config/filesystems.php
config/mail.php
config/services.php
Nilai yang berbeda antara komputer lokal dan server tidak ditulis langsung di file config. Nilai tersebut dibaca dari .env. Dokumentasi Laravel juga menempatkan konfigurasi framework pada folder config dan konfigurasi lingkungan pada .env.
6.4 Folder database
Berisi:
* Migration.
* Seeder.
* Factory.
6.5 Folder public
Berisi file yang boleh diakses browser.
Contohnya:
* index.php
* Hasil build CSS dan JavaScript.
* Favicon.
* File publik tertentu.
Folder ini tidak digunakan untuk menyimpan dokumen pribadi siswa.
6.6 Folder resources
Berisi:
* Blade view.
* CSS.
* JavaScript.
* File bahasa.
6.7 Folder routes
Berisi definisi alamat aplikasi.
6.8 Folder storage
Berisi:
* File upload privat.
* File upload publik.
* Cache.
* Session berbasis file.
* Log aplikasi.
* File sementara.
* Hasil export.
* Backup sementara.
6.9 Folder tests
Berisi pengujian otomatis.
6.10 Folder vendor
Berisi package Composer.
Folder ini dibuat oleh Composer dan tidak diedit secara manual.

7. STRUKTUR FOLDER app
Struktur yang direkomendasikan:
app/
├── Console/
│   └── Commands/
├── Enums/
├── Exports/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Imports/
├── Models/
├── Notifications/
├── Observers/
├── Policies/
├── Providers/
├── Queries/
├── Rules/
├── Services/
├── Support/
└── View/
    └── Components/
Folder tidak harus dibuat semuanya saat instalasi.
Folder dibuat ketika benar-benar dibutuhkan.

8. STRUKTUR CONTROLLER
8.1 Lokasi
app/Http/Controllers/
Laravel menyimpan controller pada folder tersebut secara default. Controller mengelompokkan penanganan request yang berhubungan dan dapat menerima dependency melalui service container.
8.2 Struktur controller SIM Madrasah
app/Http/Controllers/
├── Auth/
├── Dashboard/
├── Foundation/
├── Academic/
├── StudentAffairs/
├── Finance/
├── Administration/
├── HumanResources/
├── Facilities/
├── Library/
├── Laboratory/
├── QualityAssurance/
├── PublicSite/
├── News/
├── Admissions/
├── Portals/
└── System/
8.3 Isi setiap folder
Auth
AuthenticatedSessionController.php
PasswordController.php
PasswordResetController.php
Sebagian file authentication akan dibuat oleh Laravel Breeze.
Dashboard
DashboardController.php
HeadmasterDashboardController.php
TeacherDashboardController.php
HomeroomDashboardController.php
TreasurerDashboardController.php
Foundation
UserController.php
RoleController.php
PermissionController.php
SettingController.php
OrganizationalUnitController.php
PositionController.php
Academic
AcademicYearController.php
SemesterController.php
GradeLevelController.php
ClassGroupController.php
StudentEnrollmentController.php
HomeroomAssignmentController.php
SubjectController.php
CurriculumController.php
TeachingAssignmentController.php
ScheduleController.php
LearningDocumentController.php
TeachingJournalController.php
AttendanceController.php
AssessmentController.php
ReportCardController.php
StudentAffairs
AchievementController.php
ViolationController.php
CounselingController.php
ExtracurricularController.php
TahfidzController.php
HabitController.php
StudentOrganizationController.php
StudentPortfolioController.php
StudentQrCodeController.php
Finance
FeeTypeController.php
BillingController.php
PaymentController.php
PaymentCorrectionController.php
ReceiptController.php
FinanceReportController.php
Administration
IncomingLetterController.php
OutgoingLetterController.php
DispositionController.php
DecreeController.php
PartnershipController.php
ArchiveController.php
HumanResources
EmployeeController.php
EmployeePositionController.php
EmployeeAttendanceController.php
TrainingController.php
CertificateController.php
Facilities
RoomController.php
InventoryItemController.php
InventoryMovementController.php
InventoryLoanController.php
MaintenanceController.php
DisposalController.php
Library
BookController.php
BookCopyController.php
LibraryMemberController.php
BookLoanController.php
BookReturnController.php
EbookController.php
Laboratory
LaboratoryController.php
LabItemController.php
LabStockController.php
LabLoanController.php
LabMaintenanceController.php
QualityAssurance
QualityFrameworkController.php
QualityPeriodController.php
QualityIndicatorController.php
QualityAssignmentController.php
QualityEvidenceController.php
EvidenceReviewController.php
WorkProgramController.php
PublicSite
HomeController.php
ProfileController.php
PublicAgendaController.php
PublicAnnouncementController.php
PublicGalleryController.php
PublicAdmissionController.php
News
PostController.php
PostReviewController.php
PostCategoryController.php
TagController.php
PostMediaController.php
Admissions
AdmissionPeriodController.php
ApplicantController.php
ApplicationController.php
ApplicationVerificationController.php
SelectionController.php
ReRegistrationController.php
ApplicantConversionController.php
Portals
StudentPortalController.php
GuardianPortalController.php
AlumniPortalController.php
System
FileController.php
ImportController.php
ExportController.php
ActivityLogController.php
AuditLogController.php
BackupController.php
RestoreController.php
HelpController.php

9. TANGGUNG JAWAB CONTROLLER
Controller hanya menangani alur request dan response.
Controller bertugas:
1. Menerima Form Request.
2. Menjalankan authorization.
3. Memanggil Service atau Query.
4. Mengirim data ke view.
5. Melakukan redirect.
6. Menampilkan pesan berhasil atau gagal.
Controller tidak bertugas:
* Menghitung status pembayaran.
* Memproses kenaikan kelas.
* Mengonversi pendaftar menjadi siswa.
* Membuat snapshot rapor.
* Membuat banyak query laporan.
* Mengatur file secara langsung.
* Menentukan hak akses kompleks.
* Menjalankan proses bisnis lintas tabel.
Contoh alur konseptual:
Route
↓
Middleware
↓
Form Request
↓
Controller
↓
Policy
↓
Service
↓
Model dan Database
↓
Controller
↓
Blade View

10. STRUKTUR FORM REQUEST
10.1 Lokasi
app/Http/Requests/
Laravel menempatkan Form Request pada folder tersebut. Form Request memisahkan validasi dan authorization request dari controller. Validasi dijalankan sebelum metode controller diproses.
10.2 Struktur folder
app/Http/Requests/
├── Foundation/
├── Academic/
├── StudentAffairs/
├── Finance/
├── Administration/
├── HumanResources/
├── Facilities/
├── Library/
├── Laboratory/
├── QualityAssurance/
├── News/
├── Admissions/
└── System/
10.3 Contoh nama file
Academic/
├── StoreAcademicYearRequest.php
├── UpdateAcademicYearRequest.php
├── ActivateSemesterRequest.php
├── StoreStudentEnrollmentRequest.php
├── TransferStudentClassRequest.php
├── StoreTeachingAssignmentRequest.php
├── StoreScheduleRequest.php
├── StoreAttendanceRequest.php
├── StoreAssessmentScoreRequest.php
└── PublishReportCardRequest.php
Finance/
├── StoreFeeTypeRequest.php
├── GenerateBillingRequest.php
├── StorePaymentRequest.php
├── CorrectPaymentRequest.php
└── CancelPaymentRequest.php
10.4 Mengapa setiap tindakan memiliki request sendiri
StorePaymentRequest dan CorrectPaymentRequest memiliki aturan berbeda.
Pembayaran baru membutuhkan:
* Siswa.
* Tagihan.
* Nominal.
* Metode pembayaran.
Koreksi pembayaran membutuhkan:
* Transaksi lama.
* Permission koreksi.
* Alasan.
* Data pengganti.
* Persetujuan jika diwajibkan.
Menggabungkan semuanya dalam satu request akan membuat aturan sulit dipahami.

11. STRUKTUR MODEL
11.1 Lokasi
app/Models/
11.2 Keputusan untuk versi awal
Model tetap disimpan langsung pada app/Models.
Contoh:
app/Models/
├── User.php
├── Person.php
├── Employee.php
├── Student.php
├── Guardian.php
├── AcademicYear.php
├── Semester.php
├── ClassGroup.php
├── StudentEnrollment.php
├── Subject.php
├── TeachingAssignment.php
├── AssessmentScore.php
├── ReportCard.php
├── StudentBill.php
├── Payment.php
└── Post.php
11.3 Mengapa model belum dikelompokkan ke subfolder domain
Keuntungan model tanpa subfolder:
1. Namespace lebih sederhana.
2. Relasi lebih mudah dibaca pemula.
3. Policy discovery lebih sederhana.
4. Factory lebih mudah ditemukan.
5. Import class lebih pendek.
6. Lebih dekat dengan konvensi awal Laravel.
7. Refactor lebih mudah dilakukan setelah pengujian tersedia.
Kekurangannya:
* Jumlah file pada app/Models akan banyak.
Kekurangan tersebut masih dapat ditangani melalui:
* Pencarian nama file.
* Penamaan model konsisten.
* Folder Concerns untuk trait.
* Dokumentasi domain.
Kita tidak perlu membuat struktur yang lebih rumit sebelum masalah nyata muncul.
11.4 Folder trait model
app/Models/Concerns/
├── HasActivityLog.php
├── HasAuditTrail.php
├── HasStatus.php
└── BelongsToAcademicPeriod.php
Trait hanya digunakan untuk perilaku yang benar-benar berulang.
11.5 Tanggung jawab model
Model bertugas:
* Mendefinisikan relasi.
* Mendefinisikan casts.
* Mendefinisikan fillable atau guarded.
* Mendefinisikan query scope sederhana.
* Mendefinisikan accessor dan mutator sederhana.
* Mendefinisikan status dasar.
Model tidak menjadi tempat untuk workflow besar.
Contoh workflow yang tidak diletakkan dalam model:
Konversi pendaftar menjadi siswa
Penerbitan rapor
Koreksi pembayaran
Kenaikan kelas massal
Pembuatan tagihan massal

12. STRUKTUR SERVICE
12.1 Lokasi
app/Services/
Folder ini tidak selalu tersedia pada instalasi Laravel baru. Folder tersebut merupakan keputusan arsitektur SIM Madrasah untuk memisahkan logika bisnis dari controller.
12.2 Struktur folder
app/Services/
├── Foundation/
├── Academic/
├── StudentAffairs/
├── Finance/
├── Administration/
├── HumanResources/
├── Facilities/
├── Library/
├── Laboratory/
├── QualityAssurance/
├── News/
├── Admissions/
├── Files/
└── System/
12.3 Contoh service
app/Services/Academic/
├── AcademicPeriodService.php
├── StudentEnrollmentService.php
├── StudentPromotionService.php
├── TeachingAssignmentService.php
├── ScheduleService.php
├── AttendanceService.php
├── AssessmentService.php
└── ReportCardService.php
app/Services/Finance/
├── BillingService.php
├── PaymentService.php
├── PaymentAllocationService.php
├── PaymentCorrectionService.php
└── ReceiptService.php
app/Services/Admissions/
├── ApplicationService.php
├── ApplicationVerificationService.php
├── SelectionService.php
├── ReRegistrationService.php
└── ApplicantConversionService.php
12.4 Tanggung jawab service
Service menangani:
* Aturan bisnis.
* Perubahan beberapa tabel.
* Database transaction.
* Perhitungan.
* Workflow.
* Pembuatan histori.
* Pemanggilan service lain secara terkendali.
* Pencatatan audit yang terkait proses.
Contoh PaymentService:
Validasi tagihan
↓
Membuat pembayaran
↓
Membuat alokasi
↓
Menghitung total pembayaran
↓
Menghitung sisa
↓
Menentukan status tagihan
↓
Membuat nomor transaksi
↓
Commit transaction
Proses penting seperti pembayaran, kenaikan kelas, penerbitan rapor, dan konversi PPDB harus menggunakan database transaction.

13. STRUKTUR QUERY
13.1 Lokasi
app/Queries/
Query class digunakan untuk proses baca yang kompleks.
13.2 Mengapa query dipisahkan dari service
Service lebih fokus pada perubahan data.
Query lebih fokus pada:
* Laporan.
* Dashboard.
* Filter kompleks.
* Rekap.
* Matriks.
* Portofolio.
* Pencarian lintas tabel.
13.3 Struktur folder
app/Queries/
├── Dashboard/
├── Academic/
├── StudentAffairs/
├── Finance/
├── Reports/
├── Portfolios/
├── Documents/
└── QualityAssurance/
13.4 Contoh query class
Dashboard/
├── HeadmasterDashboardQuery.php
├── TeacherDashboardQuery.php
├── HomeroomDashboardQuery.php
└── TreasurerDashboardQuery.php
Finance/
├── ClassPaymentMatrixQuery.php
├── StudentBillingHistoryQuery.php
├── MonthlyPaymentRecapQuery.php
└── OutstandingBillsQuery.php
Portfolios/
└── StudentPortfolioQuery.php
Portofolio, dashboard, portal, pusat laporan, dan pusat dokumen tidak membuat salinan data. Semuanya membaca data dari sumber asli.

14. STRUKTUR POLICY
14.1 Lokasi
app/Policies/
Laravel menggunakan Policy untuk mengelompokkan authorization yang berhubungan dengan model atau resource. Laravel juga dapat menemukan policy secara otomatis jika model dan policy mengikuti konvensi penamaan.
14.2 Contoh policy
app/Policies/
├── UserPolicy.php
├── StudentPolicy.php
├── StudentEnrollmentPolicy.php
├── TeachingAssignmentPolicy.php
├── AssessmentScorePolicy.php
├── ReportCardPolicy.php
├── StudentBillPolicy.php
├── PaymentPolicy.php
├── CounselingSessionPolicy.php
├── PostPolicy.php
├── QualityEvidencePolicy.php
└── FilePolicy.php
14.3 Contoh tanggung jawab policy
AssessmentScorePolicy
Memeriksa:
* Pengguna memiliki permission.
* Pengguna merupakan guru yang ditugaskan.
* Kelas sesuai.
* Mata pelajaran sesuai.
* Semester sesuai.
* Periode belum dikunci.
* Nilai masih boleh diubah.
GuardianPortalPolicy
Memeriksa:
* Akun terhubung dengan guardian.
* Guardian terhubung dengan siswa.
* Relasi masih aktif.
* Akses portal diizinkan.
* Data telah dipublikasikan.
CounselingSessionPolicy
Memeriksa:
* Pengguna adalah Guru BK.
* Pengguna memiliki akses khusus.
* Tingkat kerahasiaan mengizinkan.
* Data siswa berada dalam cakupan kewenangan.
14.4 Perbedaan Policy dan Permission
Permission menjawab:
Apakah pengguna boleh melakukan tindakan ini?
Policy menjawab:
Apakah pengguna boleh melakukan tindakan ini pada record tersebut?
Contoh:
Permission:
scores.update

Policy:
Guru hanya boleh mengubah nilai pada kelas dan mata pelajaran yang menjadi penugasannya.
Laravel menyediakan Gate dan Policy sebagai mekanisme authorization. Gate cocok untuk tindakan umum, sedangkan Policy cocok untuk authorization terhadap model atau resource tertentu.

15. STRUKTUR MIDDLEWARE
15.1 Lokasi
app/Http/Middleware/
15.2 Middleware yang direncanakan
EnsureAccountIsActive.php
EnsureUserHasPermission.php
EnsureAcademicPeriodIsActive.php
EnsureAcademicPeriodIsOpen.php
EnsureApplicantOwnsApplication.php
EnsureGuardianHasStudentAccess.php
SetActiveAcademicPeriod.php
SetUserContext.php
RecordUserActivity.php
15.3 Fungsi middleware
Middleware digunakan untuk pemeriksaan umum sebelum request masuk ke controller.
Contoh:
* Pengguna sudah login.
* Akun aktif.
* Memiliki permission.
* Semester aktif tersedia.
* Periode tidak terkunci.
* Pendaftar membuka formulir miliknya.
* Orang tua membuka siswa yang terhubung.
Middleware bukan pengganti Policy.
Middleware memeriksa akses umum.
Policy memeriksa akses pada record tertentu.
15.4 Pendaftaran middleware Laravel 12
Pada struktur Laravel 12, middleware khusus ditempatkan di app/Http/Middleware. Alias dan konfigurasi middleware aplikasi diatur melalui bootstrap/app.php, bukan dengan meniru tutorial Laravel lama yang bergantung pada pengeditan app/Http/Kernel.php. Pemuatan route juga dikonfigurasi melalui bootstrap/app.php.

16. STRUKTUR ENUM
16.1 Lokasi
app/Enums/
16.2 Struktur
app/Enums/
├── AccountStatus.php
├── StudentStatus.php
├── EnrollmentStatus.php
├── SemesterStatus.php
├── AttendanceStatus.php
├── ScoreStatus.php
├── ReportCardStatus.php
├── BillStatus.php
├── PaymentStatus.php
├── PostStatus.php
├── EvidenceStatus.php
├── ApplicationStatus.php
├── FileVisibility.php
└── AuditAction.php
16.3 Mengapa menggunakan enum
Database menyimpan status sebagai string.
Kode PHP menggunakan enum agar:
* Salah ketik berkurang.
* Nilai status konsisten.
* Autocomplete bekerja.
* Workflow lebih mudah dibaca.
* Validation lebih jelas.
* Perubahan status dapat dikendalikan.
Contoh konseptual:
ScoreStatus::Draft
ScoreStatus::Submitted
ScoreStatus::Verified
ScoreStatus::Published
Kita belum membuat kode enum pada tahap ini.

17. STRUKTUR RULE
17.1 Lokasi
app/Rules/
17.2 Contoh rule khusus
ValidAcademicPeriod.php
UniqueActiveStudentEnrollment.php
TeacherScheduleAvailable.php
ClassScheduleAvailable.php
RoomScheduleAvailable.php
PaymentDoesNotExceedBill.php
ValidReportCardPublication.php
AllowedPrivateFile.php
ValidStudentGuardianRelation.php
17.3 Kapan custom rule digunakan
Custom Rule digunakan ketika aturan validasi:
* Dipakai berulang.
* Tidak cukup dengan rule standar.
* Membutuhkan pemeriksaan relasi.
* Membutuhkan pesan kesalahan khusus.
Custom Rule tidak digunakan untuk seluruh logika bisnis.
Proses lintas tabel tetap berada pada Service.

18. STRUKTUR OBSERVER
18.1 Lokasi
app/Observers/
18.2 Contoh observer
UserObserver.php
StudentObserver.php
PaymentObserver.php
PostObserver.php
QualityEvidenceObserver.php
18.3 Batas penggunaan observer
Observer cocok untuk proses otomatis sederhana seperti:
* Menetapkan UUID.
* Mengatur slug.
* Mencatat aktivitas sederhana.
* Menghapus file sementara setelah model dihapus.
* Menetapkan metadata awal.
Observer tidak digunakan untuk menyembunyikan workflow penting.
Kenaikan kelas, pembayaran, penerbitan rapor, dan konversi PPDB tetap dijalankan melalui Service agar alurnya terlihat jelas.

19. STRUKTUR NOTIFICATION
19.1 Lokasi
app/Notifications/
19.2 Contoh notification
ScoreNeedsRevisionNotification.php
ReportCardPublishedNotification.php
PaymentRecordedNotification.php
PostNeedsReviewNotification.php
EvidenceNeedsRevisionNotification.php
BookOverdueNotification.php
ApplicationVerifiedNotification.php
Versi awal menggunakan database notification.
Notifikasi akan tampil ketika pengguna membuka sistem.
Sistem tidak membutuhkan WebSocket dan tidak bergantung pada queue worker permanen.

20. STRUKTUR IMPORT DAN EXPORT
20.1 Folder import
app/Imports/
├── StudentsImport.php
├── EmployeesImport.php
├── AssessmentScoresImport.php
├── PaymentsImport.php
└── InventoryItemsImport.php
20.2 Folder export
app/Exports/
├── StudentsExport.php
├── AttendanceExport.php
├── AssessmentScoresExport.php
├── PaymentsExport.php
├── InventoryExport.php
└── QualityEvidenceExport.php
20.3 Prinsip import
Import tidak langsung memasukkan file ke database.
Alurnya:
Upload
↓
Validasi file
↓
Validasi header
↓
Baca baris
↓
Validasi data
↓
Preview
↓
Konfirmasi
↓
Import
↓
Laporan baris gagal
Aturan Tahap 2 menetapkan bahwa import wajib melalui validasi dan preview.

21. STRUKTUR PROVIDER
21.1 Lokasi
app/Providers/
21.2 Provider awal
Pada tahap awal cukup menggunakan:
AppServiceProvider.php
Provider tambahan dibuat hanya jika konfigurasi sudah cukup besar.
Contoh provider yang mungkin dibuat:
ViewServiceProvider.php
AuthorizationServiceProvider.php
EventServiceProvider.php
Laravel menggunakan service provider sebagai tempat bootstrap aplikasi. Provider buatan aplikasi didaftarkan dalam bootstrap/providers.php. Metode register digunakan untuk binding service container, sedangkan proses bootstrap yang membutuhkan service lain diletakkan pada boot.
21.3 Yang tidak dilakukan
Kita tidak membuat satu service provider untuk setiap modul sejak awal.
Contoh yang tidak diperlukan:
AcademicServiceProvider
FinanceServiceProvider
StudentServiceProvider
LibraryServiceProvider
Terlalu banyak provider akan menambah kerumitan tanpa manfaat langsung.

22. STRUKTUR SUPPORT
22.1 Lokasi
app/Support/
22.2 Isi yang diperbolehkan
app/Support/
├── Numbering/
├── Files/
├── Money/
├── Dates/
└── Helpers/
Contoh class:
Numbering/ReceiptNumberGenerator.php
Numbering/LetterNumberGenerator.php
Files/SafeFileNameGenerator.php
Money/RupiahFormatter.php
Dates/AcademicPeriodFormatter.php
Folder Support tidak menjadi tempat membuang kode yang tidak diketahui lokasinya.
Setiap class tetap harus memiliki tanggung jawab yang jelas.

23. REPOSITORY PATTERN
23.1 Keputusan awal
Kita tidak membuat folder Repository pada awal proyek.
Contoh yang tidak dilakukan:
app/Repositories/
├── StudentRepository.php
├── TeacherRepository.php
├── PaymentRepository.php
└── PostRepository.php
23.2 Mengapa Repository tidak digunakan pada semua model
Eloquent sudah menyediakan:
* Query builder.
* Relationship.
* Scope.
* Pagination.
* Create.
* Update.
* Delete.
* Transaction melalui database layer.
Membuat repository untuk seluruh model dapat menghasilkan:
* Kode ganda.
* Interface yang tidak memberikan manfaat.
* Terlalu banyak file.
* Alur kode lebih panjang.
* Kesulitan bagi pemula.
23.3 Kapan Repository boleh digunakan
Repository dapat dibuat jika:
1. Sumber data dapat diganti.
2. Data berasal dari layanan eksternal.
3. Query yang sama digunakan oleh banyak service.
4. Implementasi penyimpanan memiliki beberapa versi.
5. Pengujian membutuhkan kontrak penyimpanan tertentu.
Sebelum kebutuhan tersebut muncul, gunakan:
* Eloquent.
* Query Class.
* Service Class.
* Model Scope.

24. ACTION DAN DTO
24.1 Folder Actions
Folder Actions belum dibuat pada tahap awal.
Service digunakan lebih dahulu.
Jika satu Service menjadi terlalu besar, proses tunggal dapat dipindahkan menjadi Action.
Contoh:
app/Actions/Admissions/ConvertApplicantToStudent.php
app/Actions/Academic/PromoteStudents.php
app/Actions/Finance/CorrectPayment.php
app/Actions/Reports/PublishReportCard.php
24.2 Folder Data atau DTO
DTO belum wajib pada tahap awal.
Form Request dapat mengirim data tervalidasi ke Service.
DTO dipertimbangkan jika:
* Parameter service terlalu banyak.
* Data digunakan oleh beberapa proses.
* Bentuk data harus sangat ketat.
* Integrasi API mulai dikembangkan.
Keputusan ini menghindari overengineering.

25. STRUKTUR ROUTE
25.1 Struktur folder
routes/
├── web.php
├── auth.php
├── console.php
└── modules/
    ├── public.php
    ├── dashboard.php
    ├── foundation.php
    ├── academic.php
    ├── student-affairs.php
    ├── finance.php
    ├── administration.php
    ├── human-resources.php
    ├── facilities.php
    ├── library.php
    ├── laboratory.php
    ├── quality-assurance.php
    ├── news.php
    ├── admissions.php
    ├── portals.php
    └── system.php
Laravel secara default memuat route melalui konfigurasi bootstrap/app.php. Route web memperoleh middleware group web, termasuk session dan CSRF protection.
25.2 Fungsi web.php
routes/web.php menjadi pintu utama route web.
File tersebut memuat file route per domain.
Secara konseptual:
web.php
├── public.php
├── dashboard.php
├── foundation.php
├── academic.php
├── finance.php
└── domain lainnya
25.3 Mengapa route dipisahkan
Jika seluruh route dimasukkan ke web.php, file tersebut akan menjadi sangat panjang.
Pemisahan membuat:
* Route mudah dicari.
* Prefix konsisten.
* Middleware mudah dikelompokkan.
* Nama route mudah dikelola.
* Konflik route lebih mudah ditemukan.
25.4 Standar nama route
Gunakan format:
academic.students.index
academic.students.create
academic.students.store
academic.students.show
academic.students.edit
academic.students.update
Contoh lain:
finance.payments.store
finance.payments.receipt
finance.payments.correct
quality.evidences.verify
admissions.applications.convert
25.5 Route API
routes/api.php belum diperlukan pada MVP karena sistem menggunakan Blade.
Saat REST API benar-benar dibutuhkan, route API dibuat terpisah dan menggunakan controller atau resource API khusus.
Kita tidak membuat API kosong hanya untuk terlihat modern.

26. STRUKTUR BLADE VIEW
26.1 Lokasi
resources/views/
Laravel menyimpan view pada folder tersebut dan biasanya menggunakan Blade sebagai template. View memisahkan tampilan dari logika controller atau aplikasi.
26.2 Struktur view
resources/views/
├── auth/
├── components/
├── layouts/
├── dashboard/
├── foundation/
├── academic/
├── student-affairs/
├── finance/
├── administration/
├── human-resources/
├── facilities/
├── library/
├── laboratory/
├── quality-assurance/
├── news/
├── admissions/
├── portals/
├── public/
├── reports/
├── errors/
└── help/
26.3 Layout utama
resources/views/layouts/
├── app.blade.php
├── guest.blade.php
├── public.blade.php
├── portal.blade.php
├── print.blade.php
└── pdf.blade.php
app.blade.php
Digunakan untuk aplikasi internal.
guest.blade.php
Digunakan untuk login dan reset password.
public.blade.php
Digunakan untuk website publik.
portal.blade.php
Digunakan untuk portal siswa dan orang tua.
print.blade.php
Digunakan untuk tampilan cetak umum.
pdf.blade.php
Digunakan untuk PDF yang dibuat melalui DomPDF.

27. STRUKTUR BLADE COMPONENT
27.1 Folder komponen
resources/views/components/
├── ui/
├── forms/
├── tables/
├── navigation/
├── feedback/
├── status/
├── cards/
└── modals/
27.2 Contoh komponen
ui/button.blade.php
ui/badge.blade.php
ui/card.blade.php
ui/dropdown.blade.php
ui/empty-state.blade.php
forms/input.blade.php
forms/select.blade.php
forms/textarea.blade.php
forms/file-upload.blade.php
forms/date-input.blade.php
forms/error.blade.php
tables/table.blade.php
tables/header.blade.php
tables/pagination.blade.php
tables/filter.blade.php
status/payment-status.blade.php
status/score-status.blade.php
status/application-status.blade.php
27.3 Keuntungan komponen
1. Tombol konsisten.
2. Form konsisten.
3. Warna status konsisten.
4. Pesan kesalahan konsisten.
5. Perubahan desain cukup dilakukan di satu tempat.
6. Tampilan lebih mudah dipahami pengguna.

28. STRUKTUR VIEW PER MODUL
Contoh modul akademik:
resources/views/academic/
├── academic-years/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── semesters/
├── class-groups/
├── student-enrollments/
├── teaching-assignments/
├── schedules/
├── teaching-journals/
├── attendances/
├── assessments/
└── report-cards/
Contoh modul pembayaran:
resources/views/finance/
├── fee-types/
├── billings/
├── payments/
├── receipts/
└── reports/
Contoh portal orang tua:
resources/views/portals/guardian/
├── dashboard.blade.php
├── select-student.blade.php
├── attendance.blade.php
├── scores.blade.php
├── report-cards.blade.php
├── achievements.blade.php
├── bills.blade.php
└── payments.blade.php

29. STRUKTUR ASSET
resources/
├── css/
│   └── app.css
└── js/
    └── app.js
Tailwind digunakan melalui proses build Vite.
JavaScript hanya digunakan saat memberikan manfaat nyata, seperti:
* Dropdown.
* Modal.
* Preview file.
* Filter dinamis sederhana.
* Konfirmasi.
* Pemilihan data.
* Tabel interaktif ringan.
Kita tidak menjadikan aplikasi sebagai SPA.

30. STRUKTUR DATABASE
database/
├── factories/
├── migrations/
└── seeders/
30.1 Migration
Migration tetap berada dalam satu folder:
database/migrations/
Migration tidak dibagi ke subfolder per modul pada tahap awal.
Alasannya:
1. Urutan timestamp mudah diperiksa.
2. Artisan langsung mengenali migration.
3. Foreign key lebih mudah ditelusuri.
4. Tidak membutuhkan konfigurasi path tambahan.
5. Lebih mudah bagi pemula.
Nama migration harus jelas.
Contoh:
create_people_table
create_users_table
create_students_table
create_academic_years_table
create_semesters_table
create_student_enrollments_table
create_teaching_assignments_table
create_assessment_scores_table
create_payments_table
30.2 Seeder
Seeder dapat dikelompokkan:
database/seeders/
├── DatabaseSeeder.php
├── Foundation/
├── Academic/
├── StudentAffairs/
├── Finance/
└── QualityAssurance/
Contoh:
Foundation/RoleSeeder.php
Foundation/PermissionSeeder.php
Academic/GradeLevelSeeder.php
Academic/AttendanceStatusSeeder.php
Finance/PaymentMethodSeeder.php
30.3 Factory
Factory digunakan untuk:
* Data pengujian.
* Data dummy.
* Simulasi.
* Pengembangan lokal.
Factory tidak digunakan untuk membuat data produksi secara sembarangan.

31. STRUKTUR STORAGE
Struktur penyimpanan yang direkomendasikan:
storage/app/
├── private/
│   ├── students/
│   ├── employees/
│   ├── counseling/
│   ├── finance/
│   ├── report-cards/
│   ├── admissions/
│   ├── quality/
│   ├── archives/
│   └── backups/
├── public/
│   ├── news/
│   ├── galleries/
│   ├── profiles/
│   └── public-documents/
├── imports/
├── exports/
└── temporary/
31.1 File privat
File privat meliputi:
* Dokumen siswa.
* Konseling.
* Rapor.
* Pembayaran.
* Eviden internal.
* Dokumen kepegawaian.
* Arsip rahasia.
* Backup.
File tersebut tidak boleh diberikan sebagai URL publik langsung.
31.2 File publik
File publik meliputi:
* Gambar berita.
* Galeri.
* Logo.
* Gambar profil yang disetujui.
* Dokumen publik.
31.3 File sementara
File sementara harus dibersihkan setelah:
* Import selesai.
* Export kedaluwarsa.
* Upload gagal.
* Preview selesai.
* Backup diunduh dan melewati masa simpan.

32. STRUKTUR TEST
tests/
├── Feature/
│   ├── Auth/
│   ├── Foundation/
│   ├── Academic/
│   ├── StudentAffairs/
│   ├── Finance/
│   ├── Administration/
│   ├── Facilities/
│   ├── Library/
│   ├── QualityAssurance/
│   ├── Admissions/
│   └── Portals/
└── Unit/
    ├── Services/
    ├── Queries/
    ├── Rules/
    └── Support/
32.1 Feature Test
Feature Test menguji proses aplikasi.
Contoh:
TeacherCanOnlyInputScoresForAssignedClassTest.php
GuardianCanOnlyViewLinkedStudentTest.php
PaymentCanBePaidInInstallmentsTest.php
PublishedReportCardCannotBeSilentlyChangedTest.php
StudentPromotionKeepsPreviousEnrollmentTest.php
32.2 Unit Test
Unit Test menguji bagian kecil.
Contoh:
PaymentStatusCalculatorTest.php
ReceiptNumberGeneratorTest.php
AcademicPeriodServiceTest.php
StudentPortfolioQueryTest.php

33. PENEMPATAN LOGIKA
Jenis Logika	Lokasi
Alamat aplikasi	Route
Validasi input	Form Request
Pemeriksaan akses umum	Middleware
Pemeriksaan akses record	Policy
Alur request dan response	Controller
Relasi database	Model
Perubahan data dan workflow	Service
Laporan dan query kompleks	Query
Status tetap	Enum
Validasi khusus berulang	Rule
Efek otomatis sederhana	Observer
Tampilan	Blade View
Komponen antarmuka	Blade Component
Import Excel	Import Class
Export Excel	Export Class
Perintah terminal	Console Command
Konfigurasi binding	Service Provider
Format atau generator umum	Support
34. CONTOH ALUR FILE FITUR PEMBAYARAN
Saat Bendahara mencatat pembayaran, file yang terlibat secara konseptual adalah:
routes/modules/finance.php
↓
app/Http/Middleware/EnsureUserHasPermission.php
↓
app/Http/Requests/Finance/StorePaymentRequest.php
↓
app/Http/Controllers/Finance/PaymentController.php
↓
app/Policies/PaymentPolicy.php
↓
app/Services/Finance/PaymentService.php
↓
app/Models/Payment.php
app/Models/PaymentAllocation.php
app/Models/StudentBill.php
↓
resources/views/finance/payments/
Laporan pembayaran menggunakan:
app/Queries/Finance/ClassPaymentMatrixQuery.php
Export menggunakan:
app/Exports/PaymentsExport.php

35. CONTOH ALUR FILE FITUR NILAI
routes/modules/academic.php
↓
EnsureAccountIsActive
EnsureAcademicPeriodIsOpen
↓
StoreAssessmentScoreRequest
↓
AssessmentController
↓
AssessmentScorePolicy
↓
AssessmentService
↓
AssessmentScore
AssessmentComponent
TeachingAssignment
StudentEnrollment
↓
academic/assessments/index.blade.php
Policy memastikan guru hanya mengubah nilai berdasarkan penugasan. Hak akses tidak cukup dijaga dengan menyembunyikan menu. Tahap Use Case telah menetapkan pemeriksaan permission, Policy, relasi, penugasan, dan status periode.

36. CONTOH ALUR FILE RAPOR
PublishReportCardRequest
↓
ReportCardController
↓
ReportCardPolicy
↓
ReportCardService
↓
ReportCard
ReportCardVersion
ReportCardItem
↓
ReportCardPublishedNotification
↓
resources/views/academic/report-cards/
resources/views/pdf/report-card.blade.php
ReportCardService menangani:
* Pengambilan nilai terverifikasi.
* Rekap kehadiran.
* Ekstrakurikuler.
* Prestasi.
* Tahfidz.
* Pembiasaan.
* Snapshot.
* PDF.
* Versi rapor.
* Audit log.

37. CONTOH ALUR FILE PPDB
routes/modules/admissions.php
↓
ApplicationController
↓
StoreApplicationRequest
↓
ApplicationService
↓
Applicant
Application
ApplicationDocument
Saat konversi:
ConvertApplicantRequest
↓
ApplicantConversionController
↓
ApplicantConversionPolicy
↓
ApplicantConversionService
↓
Database Transaction
↓
Person
Student
Guardian
StudentGuardian
StudentStatusHistory
User
ApplicantConversion

38. FILE DAN FOLDER YANG TIDAK BOLEH DIGUNAKAN SEMBARANGAN
38.1 Jangan mengedit folder vendor
Package akan diperbarui melalui Composer.
Perubahan manual di dalam vendor akan hilang.
38.2 Jangan menyimpan password dalam source code
Password database dan credential harus berada pada .env.
38.3 Jangan menyimpan file privat di public
Dokumen siswa, rapor, pembayaran, konseling, dan backup harus berada pada storage privat.
38.4 Jangan menulis query berat di Blade
Blade hanya menampilkan data.
Blade tidak mencari data langsung dari database.
38.5 Jangan menaruh logika bisnis di route
Route tidak digunakan untuk memproses pembayaran atau nilai.
38.6 Jangan membuat helper global untuk semua kebutuhan
Gunakan class khusus pada Support atau Service.
38.7 Jangan membuat satu controller untuk seluruh modul
Contoh yang tidak digunakan:
AcademicController.php
FinanceController.php
StudentController.php
Setiap resource atau proses besar memiliki controller yang lebih fokus.

39. STANDAR PENAMAAN CLASS
Jenis	Contoh
Model	StudentEnrollment
Controller	StudentEnrollmentController
Store Request	StoreStudentEnrollmentRequest
Update Request	UpdateStudentEnrollmentRequest
Service	StudentEnrollmentService
Policy	StudentEnrollmentPolicy
Query	StudentEnrollmentHistoryQuery
Enum	EnrollmentStatus
Rule	UniqueActiveStudentEnrollment
Observer	StudentEnrollmentObserver
Notification	StudentEnrollmentChangedNotification
Export	StudentEnrollmentsExport
Import	StudentEnrollmentsImport
Test	StudentPromotionKeepsHistoryTest
40. STRUKTUR FINAL YANG DIREKOMENDASIKAN
sim-madrasah/
├── app/
│   ├── Console/
│   │   └── Commands/
│   ├── Enums/
│   ├── Exports/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── Dashboard/
│   │   │   ├── Foundation/
│   │   │   ├── Academic/
│   │   │   ├── StudentAffairs/
│   │   │   ├── Finance/
│   │   │   ├── Administration/
│   │   │   ├── HumanResources/
│   │   │   ├── Facilities/
│   │   │   ├── Library/
│   │   │   ├── Laboratory/
│   │   │   ├── QualityAssurance/
│   │   │   ├── PublicSite/
│   │   │   ├── News/
│   │   │   ├── Admissions/
│   │   │   ├── Portals/
│   │   │   └── System/
│   │   ├── Middleware/
│   │   └── Requests/
│   │       ├── Foundation/
│   │       ├── Academic/
│   │       ├── StudentAffairs/
│   │       ├── Finance/
│   │       ├── Administration/
│   │       ├── HumanResources/
│   │       ├── Facilities/
│   │       ├── Library/
│   │       ├── Laboratory/
│   │       ├── QualityAssurance/
│   │       ├── News/
│   │       ├── Admissions/
│   │       └── System/
│   ├── Imports/
│   ├── Models/
│   │   └── Concerns/
│   ├── Notifications/
│   ├── Observers/
│   ├── Policies/
│   ├── Providers/
│   ├── Queries/
│   │   ├── Dashboard/
│   │   ├── Academic/
│   │   ├── StudentAffairs/
│   │   ├── Finance/
│   │   ├── Reports/
│   │   ├── Portfolios/
│   │   ├── Documents/
│   │   └── QualityAssurance/
│   ├── Rules/
│   ├── Services/
│   │   ├── Foundation/
│   │   ├── Academic/
│   │   ├── StudentAffairs/
│   │   ├── Finance/
│   │   ├── Administration/
│   │   ├── HumanResources/
│   │   ├── Facilities/
│   │   ├── Library/
│   │   ├── Laboratory/
│   │   ├── QualityAssurance/
│   │   ├── News/
│   │   ├── Admissions/
│   │   ├── Files/
│   │   └── System/
│   ├── Support/
│   └── View/
│       └── Components/
├── bootstrap/
│   ├── app.php
│   └── providers.php
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── auth/
│       ├── components/
│       ├── layouts/
│       ├── dashboard/
│       ├── foundation/
│       ├── academic/
│       ├── student-affairs/
│       ├── finance/
│       ├── administration/
│       ├── human-resources/
│       ├── facilities/
│       ├── library/
│       ├── laboratory/
│       ├── quality-assurance/
│       ├── news/
│       ├── admissions/
│       ├── portals/
│       ├── public/
│       ├── reports/
│       ├── errors/
│       └── help/
├── routes/
│   ├── web.php
│   ├── auth.php
│   ├── console.php
│   └── modules/
├── storage/
│   └── app/
│       ├── private/
│       ├── public/
│       ├── imports/
│       ├── exports/
│       └── temporary/
└── tests/
    ├── Feature/
    └── Unit/

41. KEUNTUNGAN STRUKTUR INI
1. Tetap mengikuti struktur Laravel.
2. Mudah dipahami pemula.
3. Modul mudah ditemukan.
4. Tidak bergantung pada package modular.
5. Controller tetap kecil.
6. Validasi terpisah.
7. Hak akses terpisah.
8. Logika bisnis terpisah.
9. Query laporan terpisah.
10. View konsisten.
11. Route tidak menumpuk.
12. File privat lebih aman.
13. Pengujian dapat dikelompokkan.
14. Shared hosting tetap menggunakan satu aplikasi.
15. Refactor dapat dilakukan bertahap.
16. Tidak menggunakan pola yang belum dibutuhkan.

42. KEKURANGAN DAN TANTANGAN
42.1 Model akan cukup banyak
Solusi:
* Gunakan penamaan konsisten.
* Gunakan pencarian IDE.
* Jangan membuat model yang tidak diperlukan.
* Refactor ke subfolder hanya setelah pengujian tersedia.
42.2 Folder domain harus konsisten
Nama domain pada Controller, Request, Service, View, Route, dan Test harus seragam.
Contoh yang digunakan:
Academic
StudentAffairs
Finance
QualityAssurance
Admissions
Jangan menggunakan beberapa nama untuk domain yang sama.
42.3 Service dapat menjadi terlalu besar
Jika service terlalu besar:
* Pisahkan berdasarkan proses.
* Gunakan class Action untuk workflow tertentu.
* Jangan memindahkan masalah ke helper global.
42.4 Query dapat bercampur dengan service
Aturannya:
* Service untuk perubahan data.
* Query untuk membaca dan merangkum data kompleks.

43. CATATAN KOMPATIBILITAS LARAVEL 12
Struktur ini tetap menggunakan Laravel 12 sesuai keputusan proyek.
Dokumentasi Laravel 12 masih tersedia, tetapi halaman dokumentasi resminya saat ini menampilkan anjuran untuk mempertimbangkan Laravel 13.
Kita tidak mengubah versi pada tahap ini.
Sebelum instalasi pada Tahap 8, kita akan memeriksa:
1. Dukungan PHP 8.4.
2. Versi Composer.
3. Kompatibilitas Laravel Breeze.
4. Kompatibilitas Tailwind CSS.
5. Kompatibilitas DomPDF.
6. Kompatibilitas Laravel Excel.
7. Kompatibilitas package QR Code.
8. Dukungan MySQL atau MariaDB Hostinger.
9. Kebijakan dukungan keamanan Laravel 12.
10. Pilihan instalasi yang paling aman untuk deployment.

44. HASIL TAHAP 7
Tahap Struktur Folder Laravel telah menghasilkan:
1. Pendekatan hybrid modular monolith.
2. Struktur root project.
3. Struktur controller.
4. Struktur Form Request.
5. Struktur model.
6. Struktur Service.
7. Struktur Query.
8. Struktur Policy.
9. Struktur Middleware.
10. Struktur Enum.
11. Struktur Rule.
12. Struktur Observer.
13. Struktur Notification.
14. Struktur Import dan Export.
15. Struktur Provider.
16. Struktur Support.
17. Keputusan Repository Pattern.
18. Keputusan Action dan DTO.
19. Struktur route.
20. Struktur Blade view.
21. Struktur Blade component.
22. Struktur asset.
23. Struktur migration.
24. Struktur seeder.
25. Struktur storage.
26. Struktur pengujian.
27. Aturan penempatan logika.
28. Contoh alur file pembayaran.
29. Contoh alur file nilai.
30. Contoh alur file rapor.
31. Contoh alur file PPDB.
32. Standar penamaan class.
33. Batas penggunaan setiap folder.
34. Struktur final aplikasi.
Mengapa Tahap 7 penting
Tahap ini memastikan setiap kode memiliki tempat yang jelas sebelum Laravel dipasang dan file mulai dibuat.
Tanpa struktur folder:
* Controller dapat menjadi terlalu besar.
* Validasi dapat berulang.
* Query dapat bercampur dengan tampilan.
* Hak akses dapat tersebar.
* Logika bisnis sulit diuji.
* Pengembang sulit menemukan file.
* Modul saling bercampur.
* Proyek sulit dikembangkan.
Tahap berikutnya adalah Tahap 8: Instalasi Laravel.
Tahap 8 akan mencakup:
* Pemeriksaan komputer pengembangan.
* Instalasi PHP 8.4.
* Instalasi Composer.
* Instalasi Node.js dan NPM.
* Pembuatan proyek Laravel 12.
* Konfigurasi .env.
* Konfigurasi MySQL atau MariaDB.
* Instalasi Blade dan Tailwind.
* Persiapan Laravel Breeze.
* Pengujian aplikasi lokal.
* Persiapan struktur deployment shared hosting.
