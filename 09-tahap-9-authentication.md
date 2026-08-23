# Tahap 9 — Authentication

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 11206–14762.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

=========================================================
Tahap 9: Authentication.
Sebelum mulai, ada catatan keamanan penting. Kredensial database telah tertulis di percakapan. Karena itu, sebaiknya ganti password pengguna aplikasi setelah konfigurasi dasar selesai. Jangan memakai password tersebut pada server produksi, jangan memasukkannya ke Git, dan jangan mengunggah file .env.
Tahap 9: Authentication
1. Konsep sederhana
Authentication adalah proses untuk memastikan identitas pengguna sebelum sistem memberikan akses.
Authentication menjawab pertanyaan:
* Siapa pengguna yang sedang masuk?
* Apakah kata sandinya benar?
* Apakah akunnya aktif?
* Apakah pengguna boleh masuk?
* Ke halaman mana pengguna diarahkan setelah login?
Authentication berbeda dari authorization.
* Authentication menentukan siapa pengguna.
* Authorization menentukan tindakan apa yang boleh dilakukan pengguna.
Role dan permission akan dibangun pada Tahap 10.
2. Analogi sederhana
Authentication seperti pemeriksaan identitas di gerbang madrasah.
Petugas keamanan memeriksa:
1. Siapa orang tersebut.
2. Apakah identitasnya benar.
3. Apakah orang tersebut masih aktif.
4. Apakah orang tersebut boleh masuk.
Setelah berhasil masuk, kunci ruangan tetap berbeda. Guru, bendahara, siswa, dan kepala madrasah tidak memiliki akses yang sama. Kunci ruangan tersebut merupakan role dan permission.
Keputusan authentication SIM Madrasah
Kita akan menggunakan:
* Laravel Breeze.
* Blade.
* Tailwind CSS.
* Session authentication.
* Login menggunakan username atau email.
* Pemeriksaan status akun.
* Pembatasan percobaan login.
* Lupa dan reset password.
* Activity log untuk login berhasil dan gagal.
* Logout melalui metode POST.
Breeze masih dapat digunakan pada Laravel 12. Paket laravel/breeze saat ini mendukung Laravel Framework 11, 12, dan 13. Laravel starter kit juga menyediakan fondasi route, controller, view, login, logout, reset password, dan verifikasi email. (Packagist)
Registrasi publik tidak digunakan
Halaman /register nantinya akan dinonaktifkan.
Alasannya:
* Akun guru dibuat oleh administrator.
* Akun pegawai dibuat oleh administrator.
* Akun siswa dibuat atau dihasilkan dari data siswa.
* Akun orang tua dihubungkan dengan siswa.
* Calon siswa memakai alur PPDB tersendiri.
* Masyarakat umum tidak boleh membuat akun internal secara bebas.
Rencana Tahap 9
Tahap 9 akan dibagi menjadi beberapa bagian kecil:
1. Memasang Laravel Breeze.
2. Memeriksa file authentication yang dibuat.
3. Menyesuaikan tabel people dan users.
4. Menyesuaikan model User.
5. Membuat login menggunakan username atau email.
6. Menambahkan pemeriksaan status akun.
7. Menonaktifkan registrasi publik.
8. Mengatur lupa dan reset password.
9. Membuat pengguna Super Admin pertama.
10. Menguji login, logout, dan pembatasan akses.
Kita mulai dari pemasangan Breeze saja.
Tahap 9.1: Membuat checkpoint proyek
Masuk ke folder proyek:
cd ~/Herd/sim-madrasah
Periksa posisi folder:
pwd
Hasilnya seharusnya:
/Users/NAMA_USER_ANDA/Herd/sim-madrasah
Periksa versi Laravel:
php artisan --version
Periksa status aplikasi:
php artisan about
Membuat checkpoint Git
Periksa apakah proyek sudah menjadi repository Git:
git status
Jika muncul pesan:
fatal: not a git repository
jalankan:
git init
Kemudian simpan kondisi proyek setelah Tahap 8:
git add .
git commit -m "chore: complete Laravel 12 installation"
Mengapa checkpoint diperlukan?
Checkpoint menjadi titik pemulihan. Jika pemasangan authentication mengalami masalah, kita dapat melihat perubahan yang dibuat atau mengembalikan proyek ke kondisi sebelum Breeze dipasang.
Pastikan .env tidak ikut tersimpan:
git check-ignore .env
Hasil yang benar:
.env
Apabila tidak ada hasil, buka .gitignore:
nano .gitignore
Pastikan terdapat baris:
.env
Tahap 9.2: Memasang Laravel Breeze
Jalankan:
composer require laravel/breeze --dev
Setelah Composer selesai, pasang stack Blade:
php artisan breeze:install blade
Keterangan:
* breeze:install memasang fondasi authentication.
* blade menentukan bahwa tampilan memakai Blade.
* Breeze akan membuat route, controller, form request, view, dan pengujian authentication.
* Tailwind dan Vite akan dikonfigurasi untuk tampilan login.
Setelah itu, pasang dependensi frontend:
npm install
Bangun aset frontend:
npm run build
Bersihkan cache aplikasi:
php artisan optimize:clear
Jalankan migration:
php artisan migrate
Pada tahap ini kemungkinan hasilnya:
Nothing to migrate.
Itu normal karena migration dasar sudah dijalankan pada Tahap 8.
Tahap 9.3: Memeriksa hasil pemasangan
Periksa route authentication:
php artisan route:list --path=login
Periksa route logout:
php artisan route:list --path=logout
Periksa route reset password:
php artisan route:list --path=password
Periksa seluruh route authentication:
php artisan route:list
Beberapa route yang seharusnya tersedia:
GET|HEAD   login
POST       login
POST       logout
GET|HEAD   register
POST       register
GET|HEAD   forgot-password
POST       forgot-password
GET|HEAD   reset-password/{token}
POST       reset-password
Buka halaman login:
open http://sim-madrasah.test/login
Halaman login Breeze seharusnya muncul.
Jangan melakukan registrasi dahulu
Jangan membuat akun melalui /register pada langkah ini. Struktur pengguna bawaan Breeze masih menggunakan:
name
email
password
Sementara desain database SIM Madrasah menggunakan pemisahan:
people
users
employees
students
guardians
Pada langkah berikutnya, kita akan menyesuaikan fondasi authentication agar:
* Identitas orang disimpan pada people.
* Akun login disimpan pada users.
* Pengguna dapat login menggunakan username atau email.
* Akun nonaktif tidak dapat login.
* Registrasi publik dinonaktifkan.
* Data pengguna tidak bercampur dengan data siswa atau pegawai.
Pemeriksaan akhir langkah ini
Jalankan:
composer show laravel/breeze
Kemudian:
php artisan route:list --path=login
Dan:
npm run build
Hasil yang harus terpenuhi:
* Paket laravel/breeze terpasang.
* Halaman /login dapat dibuka.
* Route login dan logout tersedia.
* Build Vite berhasil.
* Tidak ada error database.
* Belum ada akun yang dibuat melalui registrasi publik.
Setelah langkah tersebut berhasil, kirimkan keluaran composer show laravel/breeze, php artisan route:list --path=login, dan pesan terakhir dari npm run build.
=========================================================
Tahap 9.4: Membuat tabel identitas people
1. Konsep sederhana
Tabel users digunakan untuk menyimpan akun login.
Tabel people digunakan untuk menyimpan identitas orang.
Contohnya:
people
- Nama lengkap
- Tempat lahir
- Tanggal lahir
- Jenis kelamin
- Alamat
- Nomor telepon
Sedangkan:
users
- Username
- Email login
- Password
- Status akun
- Waktu login terakhir
Pemisahan ini mengikuti desain database yang sudah kita tetapkan pada Tahap 5 dan Tahap 6.
2. Analogi sederhana
Tabel people seperti berkas identitas seseorang di tata usaha.
Tabel users seperti kartu akses untuk masuk ke aplikasi.
Seseorang dapat tetap tercatat sebagai guru, siswa, pegawai, atau orang tua meskipun akun loginnya belum dibuat atau sedang dinonaktifkan.
Karena itu, data identitas tidak boleh bergantung sepenuhnya pada akun login.
3. Membuat migration
Pastikan Terminal berada di folder proyek:
cd ~/Herd/sim-madrasah
Buat migration:
php artisan make:migration create_people_table
Hasilnya kurang lebih:
INFO  Migration created successfully.
Buka migration tersebut:
nano database/migrations/*_create_people_table.php
Ganti seluruh isinya dengan kode berikut:
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel identitas umum seseorang.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();

            $table->string('national_id_number', 32)
                ->nullable()
                ->unique();

            $table->string('full_name', 150);

            $table->string('birth_place', 100)
                ->nullable();

            $table->date('birth_date')
                ->nullable();

            $table->string('gender', 20)
                ->nullable();

            $table->string('religion', 30)
                ->nullable();

            $table->string('email', 191)
                ->nullable();

            $table->string('phone', 30)
                ->nullable();

            $table->text('address')
                ->nullable();

            /*
             * Foreign key ke file akan dibuat setelah
             * tabel pengelolaan file tersedia.
             */
            $table->unsignedBigInteger('photo_file_id')
                ->nullable();

            $table->timestamps();

            $table->index('full_name');
            $table->index('phone');
        });
    }

    /**
     * Menghapus tabel people saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
Simpan file:
1. Tekan Control + O.
2. Tekan Enter.
3. Tekan Control + X.
4. Penjelasan migration
id
$table->id();
Membuat primary key bertipe BIGINT UNSIGNED.
national_id_number
$table->string('national_id_number', 32)
    ->nullable()
    ->unique();
Digunakan untuk menyimpan NIK atau nomor identitas nasional.
Kolom dibuat nullable karena tidak semua data awal memiliki NIK. Kolom dibuat unique agar satu NIK tidak digunakan oleh dua orang.
full_name
$table->string('full_name', 150);
Nama lengkap wajib diisi karena setiap identitas harus memiliki nama.
Data kelahiran
$table->string('birth_place', 100)->nullable();
$table->date('birth_date')->nullable();
Tempat dan tanggal lahir dipisahkan agar data mudah dicari, difilter, dan digunakan pada dokumen resmi.
gender dan religion
Keduanya menggunakan VARCHAR, bukan ENUM.
Alasannya, nilai yang digunakan dapat berkembang dan lebih mudah dikendalikan melalui Laravel Validation atau PHP Enum.
Email pada people
$table->string('email', 191)->nullable();
Email di tabel people merupakan informasi kontak.
Email di tabel users nanti merupakan identitas login. Keduanya dapat sama, tetapi memiliki fungsi yang berbeda.
photo_file_id
Foreign key belum dibuat karena tabel penyimpanan file belum tersedia. Setelah modul dokumen dibuat, kita dapat menambahkan foreign key melalui migration baru.
Index
$table->index('full_name');
$table->index('phone');
Index membantu pencarian nama dan nomor telepon ketika data sudah banyak.
5. Menjalankan migration
Jalankan:
php artisan migrate
Hasil yang benar kurang lebih:
xxxx_xx_xx_xxxxxx_create_people_table ........ DONE
Periksa statusnya:
php artisan migrate:status
Migration create_people_table harus berstatus:
Ran
Periksa tabel melalui Laravel:
php artisan db:table people
Perintah tersebut seharusnya menampilkan struktur kolom tabel people.
6. Pemeriksaan melalui MySQL
Masuk menggunakan akun aplikasi:
mysql -h 127.0.0.1 -u sim_madrasah_user -p sim_madrasah
Masukkan password database Anda, lalu jalankan:
DESCRIBE people;
Kolom yang seharusnya terlihat:
id
national_id_number
full_name
birth_place
birth_date
gender
religion
email
phone
address
photo_file_id
created_at
updated_at
Keluar dari MySQL:
EXIT;
7. Checkpoint Git
Setelah migration berhasil:
git add .
git commit -m "feat: create people identity table"
Kirimkan hasil dari dua perintah berikut:
php artisan migrate
php artisan db:table people
Setelah tabel people berhasil, langkah berikutnya adalah menyesuaikan tabel users agar mendukung username, status akun, hubungan ke people, pembatasan login, dan riwayat login.


Ya, kita lanjutkan. GitHub tidak menghambat proses ini. Setelah setiap perubahan berhasil, kita akan melakukan commit dan push.
Tahap 9.4: Membuat tabel identitas people
Laravel menggunakan migration untuk mengelola perubahan struktur database secara bertahap. Artisan juga menyediakan db:table untuk memeriksa kolom, tipe data, key, dan index pada sebuah tabel. (Laravel)
Pada langkah ini kita hanya akan:
1. Membuat model Person.
2. Membuat migration tabel people.
3. Menjalankan migration.
4. Memeriksa hasilnya.
5. Mengunggah perubahan ke GitHub.
1. Masuk ke folder proyek
cd ~/Herd/sim-madrasah
Periksa status Git:
git status
Pastikan tidak ada perubahan yang belum Anda pahami.
2. Buat model dan migration
Jalankan:
php artisan make:model Person -m
Perintah tersebut menghasilkan:
app/Models/Person.php
database/migrations/xxxx_xx_xx_xxxxxx_create_people_table.php
Kita membuat model dan migration sekaligus karena tabel people nantinya akan diakses melalui model Eloquent Person.
3. Buka migration
Jalankan:
nano $(ls -t database/migrations/*_create_people_table.php | head -n 1)
Hapus isi file tersebut, lalu ganti dengan kode berikut:
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel identitas umum seseorang.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();

            $table->string('national_id_number', 32)
                ->nullable()
                ->unique();

            $table->string('full_name', 150);

            $table->string('birth_place', 100)
                ->nullable();

            $table->date('birth_date')
                ->nullable();

            $table->string('gender', 20)
                ->nullable();

            $table->string('religion', 30)
                ->nullable();

            $table->string('email', 191)
                ->nullable();

            $table->string('phone', 30)
                ->nullable();

            $table->text('address')
                ->nullable();

            /*
             * Foreign key belum dibuat karena tabel files
             * belum tersedia pada tahap authentication.
             */
            $table->unsignedBigInteger('photo_file_id')
                ->nullable();

            $table->timestamps();

            $table->index('full_name');
            $table->index('phone');
        });
    }

    /**
     * Menghapus tabel people saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
Simpan dengan:
1. Tekan Control + O.
2. Tekan Enter.
3. Tekan Control + X.
4. Penjelasan struktur
national_id_number
$table->string('national_id_number', 32)
    ->nullable()
    ->unique();
Kolom ini dapat menyimpan NIK atau nomor identitas lain.
Kolom dibuat nullable karena data identitas mungkin belum lengkap saat pertama dimasukkan. Kolom dibuat unique untuk mencegah satu nomor identitas digunakan oleh dua orang.
full_name
$table->string('full_name', 150);
Nama lengkap wajib diisi. Semua akun guru, siswa, pegawai, dan orang tua nantinya mengacu pada identitas orang.
email
Email pada tabel people berfungsi sebagai informasi kontak.
Email pada tabel users berfungsi sebagai identitas login. Nilainya dapat sama, tetapi tanggung jawab kedua kolom berbeda.
photo_file_id
Kolom ini belum memiliki foreign key karena tabel pusat penyimpanan file belum dibuat.
Kita tidak boleh membuat foreign key yang mengarah ke tabel yang belum tersedia. Foreign key akan ditambahkan melalui migration baru saat modul dokumen dibuat.
Index
$table->index('full_name');
$table->index('phone');
Index membantu pencarian identitas berdasarkan nama dan nomor telepon.
5. Jalankan migration
php artisan migrate
Hasil yang diharapkan:
xxxx_xx_xx_xxxxxx_create_people_table ........ DONE
Periksa status migration:
php artisan migrate:status
Migration create_people_table harus berstatus:
Ran
Periksa struktur tabel:
php artisan db:table people
Kolom yang harus tersedia:
id
national_id_number
full_name
birth_place
birth_date
gender
religion
email
phone
address
photo_file_id
created_at
updated_at
6. Periksa model Person
Buka model:
nano app/Models/Person.php
Ganti isinya menjadi:
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi melalui mass assignment.
     *
     * Relasi dengan User akan ditambahkan setelah
     * struktur tabel users selesai disesuaikan.
     */
    protected $fillable = [
        'national_id_number',
        'full_name',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'email',
        'phone',
        'address',
        'photo_file_id',
    ];

    /**
     * Mengubah tipe data database menjadi tipe PHP yang sesuai.
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }
}
Simpan file.
Mengapa menggunakan $fillable?
$fillable menentukan kolom yang boleh diisi melalui mass assignment.
Contohnya nanti:
Person::create($validatedData);
Tanpa pembatasan yang benar, aplikasi dapat menerima kolom yang seharusnya tidak boleh diubah pengguna.
Mengapa birth_date menggunakan cast?
'birth_date' => 'date',
Cast membuat Laravel memperlakukan tanggal lahir sebagai objek tanggal, bukan hanya teks biasa.
7. Jalankan pemeriksaan kode
php artisan test
Kemudian:
./vendor/bin/pint --test
Jika Pint menemukan masalah format, jalankan:
./vendor/bin/pint
Lalu periksa kembali:
./vendor/bin/pint --test
8. Simpan ke GitHub
Periksa perubahan:
git status
Tambahkan file:
git add app/Models/Person.php database/migrations/
Buat commit:
git commit -m "feat: add people identity table"
Unggah ke GitHub:
git push
Kirimkan hasil dari:
php artisan migrate
php artisan db:table people
php artisan test
Setelah berhasil, kita masuk ke Tahap 9.5: Menyesuaikan tabel users agar terhubung dengan people dan mendukung username serta status akun.


Bisa kita lanjutkan. Hasil pemeriksaan sudah benar:
* Tabel users memiliki 18 kolom.
* Foreign key users.person_id → people.id sudah aktif.
* Index email, username, person_id, serta gabungan status + account_type sudah tersedia.
* Seluruh 25 tests dengan 61 assertions berhasil.
Perubahan berikut juga tepat:
$this->assertSoftDeleted($user);
Model User sekarang memakai SoftDeletes. Laravel tidak menghapus record secara fisik, tetapi mengisi deleted_at. Karena itu, assertSoftDeleted() lebih tepat untuk menguji perilaku yang memang kita inginkan. (Laravel)
Tahap 9.6: Login dengan Username atau Email
Pada langkah ini kita akan:
1. Mengganti kolom formulir login dari email menjadi login.
2. Mendeteksi apakah pengguna memasukkan username atau email.
3. Menolak akun nonaktif.
4. Menolak akun yang sedang dikunci.
5. Menghitung kegagalan login.
6. Mengunci akun selama 15 menit setelah lima kegagalan.
7. Menyimpan waktu dan alamat IP login terakhir.
8. Mempertahankan rate limiting bawaan Breeze.
Breeze memang menempatkan logika autentikasi dan pembatasan login pada LoginRequest. Implementasi bawaan menggunakan lima percobaan berdasarkan identitas login dan alamat IP. Kita akan memperluas pola tersebut untuk kebutuhan SIM Madrasah.
1. Ubah LoginRequest
Buka file:
nano app/Http/Requests/Auth/LoginRequest.php
Ganti seluruh isinya dengan:
<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Jumlah maksimal kegagalan login sebelum akun dikunci.
     */
    private const MAX_ACCOUNT_ATTEMPTS = 5;

    /**
     * Lama penguncian akun dalam menit.
     */
    private const ACCOUNT_LOCK_MINUTES = 15;

    /**
     * Menentukan apakah request diizinkan.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Membersihkan input sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'login' => trim((string) $this->input('login')),
        ]);
    }

    /**
     * Aturan validasi formulir login.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => [
                'required',
                'string',
                'max:191',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Menjalankan proses autentikasi.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $identifier = (string) $this->input('login');

        /*
         * Jika input memiliki format email, cari melalui kolom email.
         * Selain itu, anggap input sebagai username.
         */
        $loginColumn = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $user = User::query()
            ->where($loginColumn, $identifier)
            ->first();

        /*
         * Bersihkan status penguncian jika waktunya sudah berakhir.
         */
        if ($user !== null) {
            $this->clearExpiredAccountLock($user);

            if ($this->isAccountLocked($user)) {
                $remainingMinutes = max(
                    1,
                    (int) ceil(now()->diffInSeconds($user->locked_until) / 60)
                );

                throw ValidationException::withMessages([
                    'login' => "Akun sedang dikunci. Coba kembali dalam {$remainingMinutes} menit.",
                ]);
            }
        }

        /*
         * Rate limiting tetap digunakan untuk melindungi endpoint
         * dari percobaan login berulang berdasarkan login dan IP.
         */
        $this->ensureIsNotRateLimited();

        if (
            $user === null ||
            ! Hash::check((string) $this->input('password'), $user->password)
        ) {
            $this->recordFailedLogin($user);

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        /*
         * Password benar, tetapi akun harus tetap berstatus aktif.
         */
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'login' => 'Akun tidak aktif. Hubungi administrator SIM Madrasah.',
            ]);
        }

        /*
         * Login menggunakan instance User yang sudah diperiksa.
         */
        Auth::login(
            $user,
            $this->boolean('remember')
        );

        /*
         * Reset kegagalan dan simpan informasi login terakhir.
         */
        $user->forceFill([
            'failed_login_count' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $this->ip(),
        ])->save();

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Mencatat kegagalan login.
     */
    private function recordFailedLogin(?User $user): void
    {
        RateLimiter::hit($this->throttleKey());

        /*
         * Pengguna yang tidak ditemukan tetap dibatasi oleh
         * RateLimiter, tetapi tidak memiliki record untuk diperbarui.
         */
        if ($user === null) {
            return;
        }

        $user->increment('failed_login_count');
        $user->refresh();

        if ($user->failed_login_count >= self::MAX_ACCOUNT_ATTEMPTS) {
            $user->forceFill([
                'locked_until' => now()->addMinutes(
                    self::ACCOUNT_LOCK_MINUTES
                ),
            ])->save();
        }
    }

    /**
     * Memeriksa apakah akun masih dikunci.
     */
    private function isAccountLocked(User $user): bool
    {
        return $user->locked_until !== null
            && $user->locked_until->isFuture();
    }

    /**
     * Membuka kembali akun yang masa pengunciannya sudah berakhir.
     */
    private function clearExpiredAccountLock(User $user): void
    {
        if (
            $user->locked_until === null ||
            ! $user->locked_until->isPast()
        ) {
            return;
        }

        $user->forceFill([
            'failed_login_count' => 0,
            'locked_until' => null,
        ])->save();
    }

    /**
     * Memastikan endpoint login tidak melewati batas percobaan.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Membuat identitas unik untuk rate limiting.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower((string) $this->input('login'))
            .'|'
            .$this->ip()
        );
    }
}
Simpan:
Control + O
Enter
Control + X
2. Penjelasan alurnya
Misalnya pengguna memasukkan:
admin@sim-madrasah.test
Sistem mendeteksinya sebagai email, lalu mencari:
User::where('email', $identifier)
Jika pengguna memasukkan:
superadmin
Sistem mencari:
User::where('username', $identifier)
Kita menggunakan Hash::check() karena password dalam database sudah berbentuk hash. Setelah semua pemeriksaan berhasil, Auth::login() membuat pengguna menjadi pengguna yang terautentikasi. Laravel menyediakan metode tersebut untuk melakukan login menggunakan instance model pengguna. (Laravel)
Dua lapisan perlindungan
Sistem sekarang memiliki dua perlindungan:
* RateLimiter membatasi percobaan berdasarkan login dan alamat IP.
* failed_login_count serta locked_until mengunci akun tertentu.
Rate limiter melindungi endpoint. Penguncian database melindungi akun.
3. Ubah tampilan login
Buka:
nano resources/views/auth/login.blade.php
Ganti seluruh isinya dengan:
<x-guest-layout>
    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username atau Email -->
        <div>
            <x-input-label
                for="login"
                :value="__('Username atau Email')"
            />

            <x-text-input
                id="login"
                class="block mt-1 w-full"
                type="text"
                name="login"
                :value="old('login')"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan username atau email"
            />

            <x-input-error
                :messages="$errors->get('login')"
                class="mt-2"
            />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label
                for="password"
                :value="__('Password')"
            />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember"
                >

                <span class="ms-2 text-sm text-gray-600">
                    {{ __('Ingat saya') }}
                </span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Lupa password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
Untuk sementara komponen Breeze masih menggunakan warna indigo. Warna hijau madrasah akan kita atur pada tahap desain dashboard dan komponen UI agar perubahan dilakukan secara terpusat.
4. Perbarui pengujian authentication
Karena nama input berubah dari email menjadi login, pengujian bawaan Breeze juga harus diperbarui.
Buka:
nano tests/Feature/Auth/AuthenticationTest.php
Ganti seluruh isinya dengan:
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_user_can_authenticate_using_email(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(
            route('dashboard', absolute: false)
        );
    }

    public function test_user_can_authenticate_using_username(): void
    {
        $user = User::factory()->create([
            'username' => 'guru.matematika',
        ]);

        $response = $this->post('/login', [
            'login' => 'guru.matematika',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(
            route('dashboard', absolute: false)
        );
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'login' => $user->username,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors('login');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'failed_login_count' => 1,
        ]);
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'status' => 'inactive',
        ]);

        $response = $this->post('/login', [
            'login' => $user->username,
            'password' => 'password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors('login');
    }

    public function test_locked_user_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'failed_login_count' => 5,
            'locked_until' => now()->addMinutes(10),
        ]);

        $response = $this->post('/login', [
            'login' => $user->username,
            'password' => 'password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors('login');
    }

    public function test_successful_login_resets_failed_login_data(): void
    {
        $user = User::factory()->create([
            'failed_login_count' => 3,
            'locked_until' => now()->subMinute(),
        ]);

        $this->post('/login', [
            'login' => $user->username,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);

        $user->refresh();

        $this->assertSame(0, $user->failed_login_count);
        $this->assertNull($user->locked_until);
        $this->assertNotNull($user->last_login_at);
        $this->assertNotNull($user->last_login_ip);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/logout');

        $this->assertGuest();

        $response->assertRedirect('/');
    }
}
5. Format kode
Jalankan:
./vendor/bin/pint
Kemudian:
./vendor/bin/pint --test
Targetnya tidak ada masalah format.
6. Jalankan semua pengujian
php artisan test
Jumlah pengujian akan bertambah dari 25 karena kita menambahkan beberapa skenario authentication.
Target utamanya:
Tests: seluruhnya passed
Tidak boleh ada test gagal.
7. Build ulang tampilan
Karena tampilan login berubah, jalankan:
npm run build
Kemudian bersihkan cache:
php artisan optimize:clear
Buka halaman:
open http://sim-madrasah.test/login
Kolom pertama harus berubah menjadi:
Username atau Email
8. Uji menggunakan akun Tinker sebelumnya
Apabila akun superadmin sudah dibuat, gunakan:
Username: superadmin
Password: password
Akun tersebut masih akun pengembangan. Password akan diganti ketika kita membuat Super Admin resmi.
Apabila akun tersebut belum dibuat, jangan membuat akun baru melalui /register. Pengguna Super Admin resmi akan dibuat melalui seeder pada langkah berikutnya.
9. Simpan perubahan ke GitHub
Periksa dahulu:
git status
Pastikan .env tidak tercantum.
Kemudian:
git add .
Buat commit:
git commit -m "feat: support secure username and email login"
Push ke GitHub:
git push
Perubahan ProfileTest.php yang menggunakan assertSoftDeleted() juga akan ikut tersimpan dalam commit ini jika belum pernah di-commit.
Pemeriksaan akhir Tahap 9.6
Jalankan dan kirimkan hasilnya:
./vendor/bin/pint --test
php artisan test
npm run build
git status
Setelah seluruhnya berhasil, kita masuk ke Tahap 9.7: Menonaktifkan registrasi publik dan menghapus fitur penghapusan akun mandiri. Untuk SIM Madrasah, akun internal tidak boleh dibuat atau dihapus sendiri oleh pengguna.
Tahap 9.5: Menyesuaikan tabel users
1. Konsep sederhana
Saat ini tabel users bawaan Breeze hanya berfokus pada kebutuhan login dasar:
name
email
password
SIM Madrasah membutuhkan informasi akun yang lebih lengkap:
person_id
username
account_type
status
last_login_at
last_login_ip
password_changed_at
failed_login_count
locked_until
deleted_at
Tabel people menyimpan identitas orang. Tabel users menyimpan akun masuk ke aplikasi.
2. Analogi sederhana
people seperti data identitas pegawai atau siswa.
users seperti kartu akses sistem.
Satu kartu akses hanya boleh terhubung ke satu identitas. Akun dapat dinonaktifkan tanpa menghapus identitas pemiliknya.
Laravel menyediakan foreignId, foreign-key constraint, serta aturan seperti restrictOnDelete. Relasi Eloquent juga didefinisikan melalui metode pada model, misalnya belongsTo dan hasOne. (Laravel API)
3. Membuat migration tambahan
Jangan mengubah migration bawaan Laravel yang sudah dijalankan. Buat migration baru agar histori perubahan database tetap jelas.
Jalankan:
cd ~/Herd/sim-madrasah

php artisan make:migration add_sim_madrasah_auth_columns_to_users_table --table=users
Buka migration terbaru:
nano $(ls -t database/migrations/*_add_sim_madrasah_auth_columns_to_users_table.php | head -n 1)
Ganti seluruh isinya dengan:
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kebutuhan akun SIM Madrasah
     * ke tabel users bawaan Laravel Breeze.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('person_id')
                ->nullable()
                ->unique()
                ->after('id')
                ->constrained('people')
                ->restrictOnDelete();

            /*
             * Username dibuat nullable sementara agar instalasi
             * Breeze dan data lama tetap kompatibel.
             */
            $table->string('username', 100)
                ->nullable()
                ->unique()
                ->after('name');

            $table->string('account_type', 30)
                ->default('internal')
                ->after('password');

            $table->string('status', 30)
                ->default('active')
                ->after('account_type');

            $table->timestamp('last_login_at')
                ->nullable()
                ->after('email_verified_at');

            $table->string('last_login_ip', 45)
                ->nullable()
                ->after('last_login_at');

            $table->timestamp('password_changed_at')
                ->nullable()
                ->after('last_login_ip');

            $table->unsignedSmallInteger('failed_login_count')
                ->default(0)
                ->after('password_changed_at');

            $table->timestamp('locked_until')
                ->nullable()
                ->after('failed_login_count');

            $table->softDeletes();

            $table->index(
                ['status', 'account_type'],
                'users_status_account_type_index'
            );
        });
    }

    /**
     * Mengembalikan struktur users ke kondisi sebelumnya.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_status_account_type_index');

            $table->dropConstrainedForeignId('person_id');

            $table->dropColumn([
                'username',
                'account_type',
                'status',
                'last_login_at',
                'last_login_ip',
                'password_changed_at',
                'failed_login_count',
                'locked_until',
                'deleted_at',
            ]);
        });
    }
};
Simpan:
1. Control + O
2. Enter
3. Control + X
4. Mengapa username masih nullable?
Tujuan akhir kita adalah setiap akun internal memiliki username.
Namun, saat ini Breeze masih membuat pengguna menggunakan:
name
email
password
Jika username langsung diwajibkan, pengujian registrasi Breeze akan gagal sebelum controller, factory, dan form registrasi disesuaikan.
Urutan aman:
1. Tambahkan kolom sebagai nullable.
2. Sesuaikan model dan factory.
3. Sesuaikan proses pembuatan akun.
4. Isi username untuk semua akun.
5. Jadikan username wajib jika memang diperlukan.
5. Menyesuaikan model User
Buka:
nano app/Models/User.php
Ganti seluruh isinya dengan:
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Kolom yang dapat diisi melalui mass assignment.
     *
     * Kolom keamanan seperti failed_login_count dan locked_until
     * tidak dimasukkan agar tidak dapat diubah sembarangan
     * melalui request pengguna.
     */
    protected $fillable = [
        'person_id',
        'name',
        'username',
        'email',
        'password',
        'account_type',
        'status',
    ];

    /**
     * Kolom yang tidak ditampilkan saat model diubah
     * menjadi array atau JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi tipe data database ke tipe data PHP.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'locked_until' => 'datetime',
            'failed_login_count' => 'integer',
            'password' => 'hashed',
        ];
    }

    /**
     * Identitas orang yang memiliki akun ini.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
6. Menambahkan relasi pada model Person
Buka:
nano app/Models/Person.php
Tambahkan import berikut setelah import Model:
use Illuminate\Database\Eloquent\Relations\HasOne;
Kemudian tambahkan metode berikut sebelum kurung penutup terakhir class:
/**
 * Akun login yang terhubung dengan identitas ini.
 */
public function user(): HasOne
{
    return $this->hasOne(User::class);
}
Bentuk akhirnya kurang lebih:
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id_number',
        'full_name',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'email',
        'phone',
        'address',
        'photo_file_id',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Akun login yang terhubung dengan identitas ini.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
7. Menyesuaikan UserFactory
Buka:
nano database/factories/UserFactory.php
Pada method definition(), ubah bagian return menjadi:
return [
    'name' => fake()->name(),
    'username' => fake()->unique()->userName(),
    'email' => fake()->unique()->safeEmail(),
    'email_verified_at' => now(),
    'password' => static::$password ??= Hash::make('password'),
    'account_type' => 'internal',
    'status' => 'active',
    'remember_token' => Str::random(10),
];
Jangan hapus import berikut:
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
Factory membantu pengujian membuat data pengguna secara konsisten. Laravel mendukung penggunaan factory untuk menghasilkan model dan relasi dalam proses testing. (Laravel)
8. Menjalankan migration
Jalankan:
php artisan migrate
Hasil yang diharapkan:
xxxx_xx_xx_xxxxxx_add_sim_madrasah_auth_columns_to_users_table ... DONE
Periksa struktur tabel:
php artisan db:table users
Kolom tambahan yang harus terlihat:
person_id
username
account_type
status
last_login_at
last_login_ip
password_changed_at
failed_login_count
locked_until
deleted_at
Periksa foreign key:
php artisan db:table users
Harus terlihat hubungan:
users.person_id -> people.id
9. Memeriksa format dan pengujian
Jalankan:
./vendor/bin/pint
Kemudian:
php artisan test
Targetnya:
25 passed
Jumlah assertions dapat sedikit berubah, tetapi tidak boleh ada test gagal.
10. Menguji relasi melalui Tinker
Jalankan:
php artisan tinker
Masukkan satu per satu:
$person = App\Models\Person::create([
    'full_name' => 'Administrator SIM Madrasah',
    'email' => 'admin@sim-madrasah.test',
]);
Kemudian:
$user = App\Models\User::create([
    'person_id' => $person->id,
    'name' => $person->full_name,
    'username' => 'superadmin',
    'email' => 'admin@sim-madrasah.test',
    'password' => 'password',
    'account_type' => 'internal',
    'status' => 'active',
]);
Periksa relasi dari akun ke identitas:
$user->person->full_name;
Hasil:
"Administrator SIM Madrasah"
Periksa relasi dari identitas ke akun:
$person->user->username;
Hasil:
"superadmin"
Keluar:
exit
Akun tersebut hanya data uji. Password password tidak boleh digunakan sebagai akun final.
11. Simpan perubahan ke GitHub
git status

git add app/Models/User.php \
        app/Models/Person.php \
        database/factories/UserFactory.php \
        database/migrations/

git commit -m "feat: extend users table for SIM Madrasah authentication"

git push
Kirimkan hasil:
php artisan migrate
php artisan db:table users
php artisan test
Setelah berhasil, kita lanjut ke Tahap 9.6: Mengubah login agar menerima username atau email serta menolak akun nonaktif dan akun terkunci.
Tahap 9.6: Login dengan Username atau Email
Pada langkah ini kita akan:
1. Mengganti kolom formulir login dari email menjadi login.
2. Mendeteksi apakah pengguna memasukkan username atau email.
3. Menolak akun nonaktif.
4. Menolak akun yang sedang dikunci.
5. Menghitung kegagalan login.
6. Mengunci akun selama 15 menit setelah lima kegagalan.
7. Menyimpan waktu dan alamat IP login terakhir.
8. Mempertahankan rate limiting bawaan Breeze.
Breeze memang menempatkan logika autentikasi dan pembatasan login pada LoginRequest. Implementasi bawaan menggunakan lima percobaan berdasarkan identitas login dan alamat IP. Kita akan memperluas pola tersebut untuk kebutuhan SIM Madrasah.
1. Ubah LoginRequest
Buka file:
nano app/Http/Requests/Auth/LoginRequest.php
Ganti seluruh isinya dengan:
<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Jumlah maksimal kegagalan login sebelum akun dikunci.
     */
    private const MAX_ACCOUNT_ATTEMPTS = 5;

    /**
     * Lama penguncian akun dalam menit.
     */
    private const ACCOUNT_LOCK_MINUTES = 15;

    /**
     * Menentukan apakah request diizinkan.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Membersihkan input sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'login' => trim((string) $this->input('login')),
        ]);
    }

    /**
     * Aturan validasi formulir login.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => [
                'required',
                'string',
                'max:191',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Menjalankan proses autentikasi.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $identifier = (string) $this->input('login');

        /*
         * Jika input memiliki format email, cari melalui kolom email.
         * Selain itu, anggap input sebagai username.
         */
        $loginColumn = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $user = User::query()
            ->where($loginColumn, $identifier)
            ->first();

        /*
         * Bersihkan status penguncian jika waktunya sudah berakhir.
         */
        if ($user !== null) {
            $this->clearExpiredAccountLock($user);

            if ($this->isAccountLocked($user)) {
                $remainingMinutes = max(
                    1,
                    (int) ceil(now()->diffInSeconds($user->locked_until) / 60)
                );

                throw ValidationException::withMessages([
                    'login' => "Akun sedang dikunci. Coba kembali dalam {$remainingMinutes} menit.",
                ]);
            }
        }

        /*
         * Rate limiting tetap digunakan untuk melindungi endpoint
         * dari percobaan login berulang berdasarkan login dan IP.
         */
        $this->ensureIsNotRateLimited();

        if (
            $user === null ||
            ! Hash::check((string) $this->input('password'), $user->password)
        ) {
            $this->recordFailedLogin($user);

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        /*
         * Password benar, tetapi akun harus tetap berstatus aktif.
         */
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'login' => 'Akun tidak aktif. Hubungi administrator SIM Madrasah.',
            ]);
        }

        /*
         * Login menggunakan instance User yang sudah diperiksa.
         */
        Auth::login(
            $user,
            $this->boolean('remember')
        );

        /*
         * Reset kegagalan dan simpan informasi login terakhir.
         */
        $user->forceFill([
            'failed_login_count' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $this->ip(),
        ])->save();

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Mencatat kegagalan login.
     */
    private function recordFailedLogin(?User $user): void
    {
        RateLimiter::hit($this->throttleKey());

        /*
         * Pengguna yang tidak ditemukan tetap dibatasi oleh
         * RateLimiter, tetapi tidak memiliki record untuk diperbarui.
         */
        if ($user === null) {
            return;
        }

        $user->increment('failed_login_count');
        $user->refresh();

        if ($user->failed_login_count >= self::MAX_ACCOUNT_ATTEMPTS) {
            $user->forceFill([
                'locked_until' => now()->addMinutes(
                    self::ACCOUNT_LOCK_MINUTES
                ),
            ])->save();
        }
    }

    /**
     * Memeriksa apakah akun masih dikunci.
     */
    private function isAccountLocked(User $user): bool
    {
        return $user->locked_until !== null
            && $user->locked_until->isFuture();
    }

    /**
     * Membuka kembali akun yang masa pengunciannya sudah berakhir.
     */
    private function clearExpiredAccountLock(User $user): void
    {
        if (
            $user->locked_until === null ||
            ! $user->locked_until->isPast()
        ) {
            return;
        }

        $user->forceFill([
            'failed_login_count' => 0,
            'locked_until' => null,
        ])->save();
    }

    /**
     * Memastikan endpoint login tidak melewati batas percobaan.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Membuat identitas unik untuk rate limiting.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower((string) $this->input('login'))
            .'|'
            .$this->ip()
        );
    }
}
Simpan:
Control + O
Enter
Control + X
2. Penjelasan alurnya
Misalnya pengguna memasukkan:
admin@sim-madrasah.test
Sistem mendeteksinya sebagai email, lalu mencari:
User::where('email', $identifier)
Jika pengguna memasukkan:
superadmin
Sistem mencari:
User::where('username', $identifier)
Kita menggunakan Hash::check() karena password dalam database sudah berbentuk hash. Setelah semua pemeriksaan berhasil, Auth::login() membuat pengguna menjadi pengguna yang terautentikasi. Laravel menyediakan metode tersebut untuk melakukan login menggunakan instance model pengguna. (Laravel)
Dua lapisan perlindungan
Sistem sekarang memiliki dua perlindungan:
* RateLimiter membatasi percobaan berdasarkan login dan alamat IP.
* failed_login_count serta locked_until mengunci akun tertentu.
Rate limiter melindungi endpoint. Penguncian database melindungi akun.
3. Ubah tampilan login
Buka:
nano resources/views/auth/login.blade.php
Ganti seluruh isinya dengan:
<x-guest-layout>
    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username atau Email -->
        <div>
            <x-input-label
                for="login"
                :value="__('Username atau Email')"
            />

            <x-text-input
                id="login"
                class="block mt-1 w-full"
                type="text"
                name="login"
                :value="old('login')"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan username atau email"
            />

            <x-input-error
                :messages="$errors->get('login')"
                class="mt-2"
            />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label
                for="password"
                :value="__('Password')"
            />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember"
                >

                <span class="ms-2 text-sm text-gray-600">
                    {{ __('Ingat saya') }}
                </span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}"
                >
                    {{ __('Lupa password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
Untuk sementara komponen Breeze masih menggunakan warna indigo. Warna hijau madrasah akan kita atur pada tahap desain dashboard dan komponen UI agar perubahan dilakukan secara terpusat.
4. Perbarui pengujian authentication
Karena nama input berubah dari email menjadi login, pengujian bawaan Breeze juga harus diperbarui.
Buka:
nano tests/Feature/Auth/AuthenticationTest.php
Ganti seluruh isinya dengan:
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_user_can_authenticate_using_email(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(
            route('dashboard', absolute: false)
        );
    }

    public function test_user_can_authenticate_using_username(): void
    {
        $user = User::factory()->create([
            'username' => 'guru.matematika',
        ]);

        $response = $this->post('/login', [
            'login' => 'guru.matematika',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(
            route('dashboard', absolute: false)
        );
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'login' => $user->username,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors('login');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'failed_login_count' => 1,
        ]);
    }

    public function test_inactive_user_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'status' => 'inactive',
        ]);

        $response = $this->post('/login', [
            'login' => $user->username,
            'password' => 'password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors('login');
    }

    public function test_locked_user_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'failed_login_count' => 5,
            'locked_until' => now()->addMinutes(10),
        ]);

        $response = $this->post('/login', [
            'login' => $user->username,
            'password' => 'password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors('login');
    }

    public function test_successful_login_resets_failed_login_data(): void
    {
        $user = User::factory()->create([
            'failed_login_count' => 3,
            'locked_until' => now()->subMinute(),
        ]);

        $this->post('/login', [
            'login' => $user->username,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);

        $user->refresh();

        $this->assertSame(0, $user->failed_login_count);
        $this->assertNull($user->locked_until);
        $this->assertNotNull($user->last_login_at);
        $this->assertNotNull($user->last_login_ip);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/logout');

        $this->assertGuest();

        $response->assertRedirect('/');
    }
}
5. Format kode
Jalankan:
./vendor/bin/pint
Kemudian:
./vendor/bin/pint --test
Targetnya tidak ada masalah format.
6. Jalankan semua pengujian
php artisan test
Jumlah pengujian akan bertambah dari 25 karena kita menambahkan beberapa skenario authentication.
Target utamanya:
Tests: seluruhnya passed
Tidak boleh ada test gagal.
7. Build ulang tampilan
Karena tampilan login berubah, jalankan:
npm run build
Kemudian bersihkan cache:
php artisan optimize:clear
Buka halaman:
open http://sim-madrasah.test/login
Kolom pertama harus berubah menjadi:
Username atau Email
8. Uji menggunakan akun Tinker sebelumnya
Apabila akun superadmin sudah dibuat, gunakan:
Username: superadmin
Password: password
Akun tersebut masih akun pengembangan. Password akan diganti ketika kita membuat Super Admin resmi.
Apabila akun tersebut belum dibuat, jangan membuat akun baru melalui /register. Pengguna Super Admin resmi akan dibuat melalui seeder pada langkah berikutnya.
9. Simpan perubahan ke GitHub
Periksa dahulu:
git status
Pastikan .env tidak tercantum.
Kemudian:
git add .
Buat commit:
git commit -m "feat: support secure username and email login"
Push ke GitHub:
git push
Perubahan ProfileTest.php yang menggunakan assertSoftDeleted() juga akan ikut tersimpan dalam commit ini jika belum pernah di-commit.
Pemeriksaan akhir Tahap 9.6
Jalankan dan kirimkan hasilnya:
./vendor/bin/pint --test
php artisan test
npm run build
git status
Setelah seluruhnya berhasil, kita masuk ke Tahap 9.7: Menonaktifkan registrasi publik dan menghapus fitur penghapusan akun mandiri. Untuk SIM Madrasah, akun internal tidak boleh dibuat atau dihapus sendiri oleh pengguna.
Tahap 9.7: Menutup registrasi publik
1. Konsep sederhana
Pada aplikasi umum, pengguna dapat membuka /register lalu membuat akun sendiri.
SIM Madrasah tidak boleh menggunakan pola tersebut karena:
* Akun guru dibuat berdasarkan data pegawai.
* Akun siswa dibuat berdasarkan data siswa.
* Akun orang tua harus terhubung dengan siswa.
* Akun petugas diberikan oleh administrator.
* Calon siswa menggunakan modul PPDB, bukan registrasi internal.
* Setiap akun nantinya harus memperoleh role dan permission yang tepat.
Karena itu, registrasi publik harus dihapus dari sisi route, controller, view, dan pengujian.
2. Hapus route registrasi
Buka:
nano routes/auth.php
Hapus import berikut:
use App\Http\Controllers\Auth\RegisteredUserController;
Di dalam grup middleware guest, hapus dua route berikut:
Route::get('register', [RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('register', [RegisteredUserController::class, 'store']);
Jangan menghapus route:
* Login.
* Lupa password.
* Reset password.
* Verifikasi email.
* Konfirmasi password.
* Logout.
Fitur lupa dan reset password tetap diperlukan untuk akun yang dibuat administrator. Laravel menyediakan mekanisme password broker dan reset password sebagai bagian authentication application starter. (Laravel)
Simpan:
Control + O
Enter
Control + X
3. Hapus controller dan tampilan registrasi
Setelah route registrasi dihapus, controller dan view berikut tidak digunakan lagi:
rm app/Http/Controllers/Auth/RegisteredUserController.php
rm resources/views/auth/register.blade.php
Kita tidak akan memakai controller registrasi publik untuk membuat akun internal. Nanti akun akan dibuat melalui:
* Seeder untuk Super Admin pertama.
* Modul manajemen pengguna.
* Konversi data siswa.
* Konversi data PPDB.
* Proses pembuatan akun orang tua.
Tahap 9.8: Menutup penghapusan akun mandiri
1. Mengapa fitur ini harus ditutup?
Breeze menyediakan fitur pengguna menghapus akunnya sendiri melalui halaman profil.
Fitur tersebut tidak sesuai untuk SIM Madrasah.
Contohnya, guru tidak boleh menghapus akun sendiri karena akun tersebut memiliki hubungan dengan:
* Nilai.
* Jurnal mengajar.
* Absensi.
* Riwayat penugasan.
* Berita.
* Persetujuan.
* Activity log.
* Audit log.
Siswa dan orang tua juga tidak boleh menghapus akun sendiri karena hubungan datanya harus tetap terjaga.
Akun nantinya dikelola melalui status:
active
inactive
suspended
archived
SoftDeletes tetap dipertahankan pada model User. Administrator dapat menggunakannya nanti melalui proses yang memiliki permission dan audit log.
2. Hapus route penghapusan profil
Buka:
nano routes/web.php
Cari bagian:
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});
Hapus route delete, sehingga hasil akhirnya:
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});
Simpan file.
3. Rapikan ProfileController
Buka:
nano app/Http/Controllers/ProfileController.php
Ganti seluruh isinya menjadi:
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna.
     */
    public function update(
        ProfileUpdateRequest $request
    ): RedirectResponse {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }
}
Method destroy() sudah dihapus.
Import berikut juga tidak digunakan lagi:
use Illuminate\Support\Facades\Auth;
4. Hapus formulir penghapusan akun
Buka:
nano resources/views/profile/edit.blade.php
Ganti seluruh isinya menjadi:
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include(
                        'profile.partials.update-profile-information-form'
                    )
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include(
                        'profile.partials.update-password-form'
                    )
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
Hapus partial yang sudah tidak digunakan:
rm resources/views/profile/partials/delete-user-form.blade.php
Sekarang halaman profil hanya menyediakan:
* Perubahan informasi profil.
* Perubahan password.
Pengguna tidak dapat menghapus akun sendiri.
Tahap 9.9: Menyesuaikan pengujian
1. Ubah RegistrationTest
Buka:
nano tests/Feature/Auth/RegistrationTest.php
Ganti seluruh isinya menjadi:
<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_is_not_available(): void
    {
        $response = $this->get('/register');

        $response->assertNotFound();
    }

    public function test_public_registration_is_not_available(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pengguna Baru',
            'email' => 'pengguna@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertNotFound();

        $this->assertDatabaseCount('users', 0);
    }
}
Pengujian ini memastikan route registrasi tidak muncul kembali secara tidak sengaja.
2. Perbarui ProfileTest
Buka:
nano tests/Feature/ProfileTest.php
Hapus dua method lama:
test_user_can_delete_their_account
dan:
test_correct_password_must_be_provided_to_delete_account
Perubahan Anda sebelumnya dari:
$this->assertNull($user->fresh());
menjadi:
$this->assertSoftDeleted($user);
memang benar ketika penghapusan akun masih diizinkan.
Namun, sekarang fitur tersebut sengaja dinonaktifkan. Karena itu, kedua pengujian penghapusan harus diganti dengan pengujian bahwa akun tidak dapat dihapus sendiri.
Tambahkan method berikut di dalam class ProfileTest:
public function test_user_cannot_delete_their_own_account(): void
{
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response->assertStatus(405);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'deleted_at' => null,
    ]);
}
Status 405 berarti URI /profile tersedia, tetapi metode HTTP DELETE tidak diizinkan.


Tahap 9.10: Pemeriksaan route
Bersihkan cache:
php artisan optimize:clear
Periksa seluruh route:
php artisan route:list --except-vendor
Pastikan tidak ditemukan:
register
profile.destroy
Pemeriksaan cepat dapat dilakukan dengan:
php artisan route:list --except-vendor \
| grep -E 'register|profile.destroy' \
|| echo "OK: registrasi dan hapus akun mandiri sudah nonaktif"
Hasil yang diharapkan:
OK: registrasi dan hapus akun mandiri sudah nonaktif
Periksa route login tetap tersedia:
php artisan route:list --path=login
Harus tetap menampilkan:
GET|HEAD  login
POST      login
Periksa route reset password:
php artisan route:list --path=password
Route lupa password dan reset password harus tetap tersedia.


Tahap 9.11: Format dan pengujian
Jalankan:
./vendor/bin/pint
Kemudian:
./vendor/bin/pint --test
Jalankan seluruh test:
php artisan test
Jumlah test kemungkinan berubah dari 29 menjadi sekitar 28 karena dua pengujian penghapusan diganti dengan satu pengujian penolakan penghapusan. Angka pastinya tidak menjadi masalah selama seluruh test berstatus PASS.
Buka registrasi melalui browser:
open http://sim-madrasah.test/register
Hasilnya harus halaman 404.
Buka login:
open http://sim-madrasah.test/login
Halaman login harus tetap tersedia.
Setelah login, buka:
open http://sim-madrasah.test/profile
Bagian Delete Account atau Hapus Akun tidak boleh tampil.
Tahap 9.12: Simpan ke GitHub
Periksa perubahan:
git status
Gunakan -A karena kita menghapus beberapa file:
git add -A
Buat commit:
git commit -m "feat: disable public registration and self account deletion"
Push ke GitHub:
git push
Periksa hasil akhir:
git status
Targetnya:
On branch main
Your branch is up to date with 'origin/main'.

nothing to commit, working tree clean
Kirimkan hasil dari:
php artisan route:list --except-vendor \
| grep -E 'register|profile.destroy' \
|| echo "OK: route dinonaktifkan"

php artisan test

git status
Setelah berhasil, kita lanjut ke Tahap 9.13: Membuat Super Admin pertama melalui seeder yang aman.


Tahap 9.13: Initial Administrator Seeder
1. Konsep sederhana
Seeder adalah mekanisme Laravel untuk memasukkan data awal ke database secara terkontrol.
Laravel menyediakan seeder di folder database/seeders, dan seeder dapat dijalankan secara individual menggunakan php artisan db:seed --class=.... (Laravel)
Kita akan menggunakannya untuk membuat:
Person
   ↓
User
Belum:
User
   ↓
Role Super Admin
Relasi role akan dibuat pada Tahap 10.

2. Jangan memasukkan password ke source code
Kita tidak akan membuat seperti ini:
'password' => 'Admin123'
atau:
'password' => 'password'
Password administrator akan disimpan sementara di .env.
Laravel sendiri menyarankan nilai sensitif berada di environment configuration dan .env tidak dimasukkan ke source control. Selain itu, env() sebaiknya digunakan di file konfigurasi, lalu aplikasi membaca nilainya melalui config(). (Laravel)

3. Buat konfigurasi SIM Madrasah
Jalankan:
cd ~/Herd/sim-madrasah
Buat file:
nano config/sim.php
Isi:
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Initial Administrator
    |--------------------------------------------------------------------------
    |
    | Digunakan hanya untuk membuat akun administrator pertama
    | pada saat instalasi awal aplikasi.
    |
    */

    'initial_admin' => [
        'name' => env(
            'SIM_INITIAL_ADMIN_NAME',
            'Administrator SIM Madrasah'
        ),

        'username' => env(
            'SIM_INITIAL_ADMIN_USERNAME',
            'superadmin'
        ),

        'email' => env(
            'SIM_INITIAL_ADMIN_EMAIL'
        ),

        'password' => env(
            'SIM_INITIAL_ADMIN_PASSWORD'
        ),
    ],

];
Simpan:
Control + O
Enter
Control + X

4. Mengapa membuat config/sim.php?
Kita sebenarnya bisa membaca:
env('SIM_INITIAL_ADMIN_PASSWORD')
langsung dari seeder.
Tetapi kita tidak memilih cara tersebut.
Laravel menjelaskan bahwa ketika configuration caching digunakan, .env tidak lagi dimuat secara langsung selama request atau Artisan command. Karena itu, env() seharusnya dipanggil dari file dalam folder config, sedangkan kode aplikasi membaca nilai melalui config(). (Laravel)
Jadi alurnya:
.env
 ↓
config/sim.php
 ↓
Seeder
Ini lebih aman untuk deployment nanti.

5. Tambahkan konfigurasi administrator ke .env
Buka:
nano .env
Tambahkan di bagian paling bawah:
# Initial Administrator
SIM_INITIAL_ADMIN_NAME="Administrator SIM Madrasah"
SIM_INITIAL_ADMIN_USERNAME="superadmin"
SIM_INITIAL_ADMIN_EMAIL="admin@sim-madrasah.test"
SIM_INITIAL_ADMIN_PASSWORD="ISI_PASSWORD_ADMIN_YANG_KUAT"
Untuk:
SIM_INITIAL_ADMIN_PASSWORD
buat password baru sendiri.
Jangan gunakan:
password
12345678
admin
Admin123
dan jangan kirim password tersebut ke saya.
Simpan .env.

6. Tambahkan template ke .env.example
Buka:
nano .env.example
Tambahkan:
# Initial Administrator
SIM_INITIAL_ADMIN_NAME="Administrator SIM Madrasah"
SIM_INITIAL_ADMIN_USERNAME="superadmin"
SIM_INITIAL_ADMIN_EMAIL="admin@example.test"
SIM_INITIAL_ADMIN_PASSWORD=
Perhatikan:
SIM_INITIAL_ADMIN_PASSWORD=
sengaja kosong.
Kita boleh mengunggah .env.example ke GitHub karena tidak memiliki password asli.
Sedangkan:
.env
tetap tidak boleh masuk Git.

7. Buat seeder
Jalankan:
php artisan make:seeder InitialAdminSeeder
Laravel akan membuat:
database/seeders/InitialAdminSeeder.php
Buka:
nano database/seeders/InitialAdminSeeder.php
Ganti seluruh isinya:
<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class InitialAdminSeeder extends Seeder
{
    /**
     * Membuat administrator awal SIM Madrasah.
     */
    public function run(): void
    {
        /*
         * Seeder ini hanya digunakan pada tahap instalasi lokal.
         *
         * Pembuatan administrator production akan kita atur
         * secara khusus ketika masuk tahap deployment.
         */
        if (! app()->environment('local')) {
            throw new RuntimeException(
                'InitialAdminSeeder hanya boleh dijalankan pada environment local.'
            );
        }

        $name = trim(
            (string) config('sim.initial_admin.name')
        );

        $username = trim(
            (string) config('sim.initial_admin.username')
        );

        $email = trim(
            (string) config('sim.initial_admin.email')
        );

        $password = (string) config(
            'sim.initial_admin.password'
        );

        /*
         * Tidak menyediakan password bawaan.
         */
        if (
            $name === '' ||
            $username === '' ||
            $email === '' ||
            $password === ''
        ) {
            throw new RuntimeException(
                'Konfigurasi Initial Administrator belum lengkap.'
            );
        }

        DB::transaction(function () use (
            $name,
            $username,
            $email,
            $password
        ): void {

            /*
             * Cari akun lama jika sebelumnya administrator
             * pernah dibuat melalui Tinker atau seeder.
             */
            $user = User::withTrashed()
                ->where('username', $username)
                ->orWhere('email', $email)
                ->first();

            /*
             * Jika user sudah memiliki person,
             * gunakan person tersebut.
             */
            $person = $user?->person;

            /*
             * Jika belum memiliki person, cari berdasarkan email.
             */
            if ($person === null) {
                $person = Person::query()
                    ->where('email', $email)
                    ->first();
            }

            /*
             * Jika identitas sama sekali belum tersedia,
             * buat identitas baru.
             */
            if ($person === null) {
                $person = Person::create([
                    'full_name' => $name,
                    'email' => $email,
                ]);
            } else {
                $person->update([
                    'full_name' => $name,
                    'email' => $email,
                ]);
            }

            /*
             * Jika akun belum tersedia, buat akun baru.
             */
            if ($user === null) {
                User::create([
                    'person_id' => $person->id,
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'account_type' => 'internal',
                    'status' => 'active',
                    'failed_login_count' => 0,
                ]);

                return;
            }

            /*
             * Jika akun sebelumnya soft deleted,
             * aktifkan kembali.
             */
            if ($user->trashed()) {
                $user->restore();
            }

            /*
             * Perbarui akun administrator.
             *
             * Password sengaja diperbarui agar password lama
             * seperti "password" dari pengujian Tinker
             * tidak tetap digunakan.
             */
            $user->forceFill([
                'person_id' => $person->id,
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'account_type' => 'internal',
                'status' => 'active',
                'failed_login_count' => 0,
                'locked_until' => null,
            ])->save();
        });
    }
}
Laravel menyediakan Hash::make() untuk menyimpan password dalam bentuk hash, bukan plain text. (Laravel)

8. Mengapa menggunakan DB::transaction()?
Perhatikan bagian:
DB::transaction(function () {
Kita membuat dua jenis data:
people
users
Bayangkan proses:
Person berhasil dibuat
        ↓
User gagal dibuat
Tanpa transaction, database dapat berisi identitas administrator tetapi tidak memiliki akun.
Dengan transaction:
Person berhasil
User berhasil
       ↓
COMMIT
atau:
Person berhasil
User gagal
       ↓
ROLLBACK semuanya
Ini konsisten dengan prinsip flowchart dan database yang sudah kita tetapkan sebelumnya.

9. Jangan masukkan seeder ini ke DatabaseSeeder
Untuk saat ini jangan menambahkan:
$this->call([
    InitialAdminSeeder::class,
]);
ke:
database/seeders/DatabaseSeeder.php
Mengapa?
Karena password administrator tidak boleh di-reset setiap kali kita menjalankan:
php artisan db:seed
Seeder administrator ini hanya akan kita jalankan secara eksplisit.

10. Bersihkan konfigurasi
Setelah .env selesai diisi:
php artisan config:clear
Kemudian pastikan Laravel membaca konfigurasi tanpa menampilkan password:
php artisan tinker
Masukkan:
config('sim.initial_admin.username');
Hasil:
"superadmin"
Kemudian:
config('sim.initial_admin.email');
Hasil:
"admin@sim-madrasah.test"
Jangan jalankan:
config('sim.initial_admin.password');
karena tidak ada alasan menampilkan password di Terminal.
Keluar:
exit

11. Jalankan Initial Admin Seeder
Sekarang jalankan:
php artisan db:seed --class=InitialAdminSeeder
Hasil normal:
INFO  Seeding database.
Tidak boleh ada error.
Seeder dapat dijalankan secara individual menggunakan opsi --class. (Laravel)

12. Periksa akun tanpa menampilkan password
Jalankan:
php artisan tinker
Masukkan:
$user = App\Models\User::where('username', 'superadmin')->first();
Kemudian:
$user?->only([
    'id',
    'person_id',
    'name',
    'username',
    'email',
    'account_type',
    'status',
]);
Hasil kurang lebih:
[
    "id" => 1,
    "person_id" => 1,
    "name" => "Administrator SIM Madrasah",
    "username" => "superadmin",
    "email" => "admin@sim-madrasah.test",
    "account_type" => "internal",
    "status" => "active",
]
Periksa relasi people:
$user->person->full_name;
Target:
"Administrator SIM Madrasah"
Keluar:
exit

13. Uji melalui browser
Buka:
open http://sim-madrasah.test/login
Coba login pertama menggunakan:
Username:
superadmin

Password:
password yang Anda masukkan sendiri di .env
Jika berhasil, Anda seharusnya diarahkan ke dashboard Breeze.
Logout.
Kemudian coba lagi menggunakan:
Email:
admin@sim-madrasah.test

Password:
password yang sama
Harus berhasil juga.
Ini sekaligus memastikan implementasi Tahap 9.6 masih bekerja.

14. Setelah login berhasil, hapus password bootstrap dari .env
Ini penting.
Buka:
nano .env
Ubah:
SIM_INITIAL_ADMIN_PASSWORD="PASSWORD_ANDA"
menjadi:
SIM_INITIAL_ADMIN_PASSWORD=
Kemudian:
php artisan config:clear
Mengapa?
Karena password tidak perlu terus disimpan sebagai konfigurasi bootstrap setelah akun berhasil dibuat.
Password yang ada di database sudah disimpan dalam bentuk hash.
Seeder administrator juga tidak akan bisa dijalankan secara tidak sengaja sampai Anda memasukkan password bootstrap lagi.

15. Jalankan seluruh pemeriksaan
./vendor/bin/pint
Lalu:
./vendor/bin/pint --test
Kemudian:
php artisan test
Target:
seluruh test PASS
Kita belum mengubah logika test yang ada, jadi 28 tests seharusnya tetap lulus jika tidak ada perubahan lain.

16. Periksa .env
Sebelum Git:
git status
Pastikan yang terlihat hanya kurang lebih:
modified: .env.example
new file: config/sim.php
new file: database/seeders/InitialAdminSeeder.php
.env tidak boleh muncul.
Periksa lagi:
git check-ignore -v .env
Lalu:
git ls-files .env
Perintah kedua harus tidak menghasilkan apa pun.

17. Commit ke GitHub
Setelah semuanya benar:
git add config/sim.php
git add database/seeders/InitialAdminSeeder.php
git add .env.example
Kemudian:
git commit -m "feat: add secure initial administrator seeder"
Push:
git push
Terakhir:
git status
Target:
On branch main
Your branch is up to date with 'origin/main'.

nothing to commit, working tree clean
Titik berhenti
Setelah ini jangan masuk ke Role dan Permission dulu.
Kirimkan hanya hasil:
php artisan db:seed --class=InitialAdminSeeder
kemudian:
php artisan test
dan:
git status
Jika ketiganya berhasil, kita lanjut ke Tahap 9.14, yaitu memastikan akun administrator, pergantian password, reset password, session, serta security authentication benar-benar siap sebelum menutup Tahap 9 dan masuk ke Tahap 10: Role & Permission.


Tahap 9.14: Finalisasi Authentication.
Tujuan tahap ini adalah memastikan akun yang sudah login tetap aman, bukan hanya aman saat proses login.
Saat ini sistem sudah bisa:
* Login dengan username atau email.
* Menolak akun nonaktif.
* Menolak akun terkunci.
* Menonaktifkan registrasi publik.
* Menonaktifkan hapus akun mandiri.
* Membuat akun superadmin melalui seeder.
Sekarang kita tambahkan satu lapisan penting:
Jika akun dinonaktifkan saat pengguna masih login, sistem harus memaksa logout.

Tahap 9.14A: Middleware Pemeriksa Status Akun
1. Konsep sederhana
Login hanya memeriksa akun saat pengguna masuk.
Masalahnya:
1. Guru login pagi hari.
2. Admin menonaktifkan akun guru tersebut.
3. Guru masih membuka aplikasi karena session masih aktif.
Karena itu, setiap halaman internal perlu memeriksa ulang status akun.
2. Analogi sederhana
Login seperti pemeriksaan di gerbang.
Middleware seperti petugas yang berjaga di setiap lorong penting. Walaupun seseorang sudah masuk gerbang, ia tetap bisa diminta keluar jika kartu aksesnya dicabut.

3. Buat middleware
Jalankan:
php artisan make:middleware EnsureAccountIsActive
Buka file:
nano app/Http/Middleware/EnsureAccountIsActive.php
Ganti seluruh isinya dengan:
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * Memastikan akun yang sedang login masih aktif.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $isInactive = $user->status !== 'active';

        $isLocked = $user->locked_until !== null
            && $user->locked_until->isFuture();

        if (! $isInactive && ! $isLocked) {
            return $next($request);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'login' => 'Sesi dihentikan karena akun tidak aktif atau sedang dikunci.',
            ]);
    }
}
Simpan.

4. Daftarkan middleware
Buka:
nano bootstrap/app.php
Cari bagian:
->withMiddleware(function (Middleware $middleware): void {
    //
})
Ubah menjadi seperti ini:
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'active.account' => \App\Http\Middleware\EnsureAccountIsActive::class,
    ]);
})
Jika di dalamnya sudah ada isi lain, jangan hapus. Tambahkan hanya bagian alias.

5. Terapkan middleware ke route internal
Buka:
nano routes/web.php
Cari route dashboard. Biasanya seperti ini:
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Ubah menjadi:
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware([
    'auth',
    'verified',
    'active.account',
])->name('dashboard');
Kemudian cari group profile:
Route::middleware('auth')->group(function () {
Ubah menjadi:
Route::middleware([
    'auth',
    'active.account',
])->group(function () {
Simpan.

6. Mengapa middleware ini penting?
Middleware ini melindungi aplikasi dari session lama.
Tanpa middleware ini, akun yang sudah dinonaktifkan masih bisa menggunakan aplikasi sampai logout atau session berakhir.
Dengan middleware ini:
* Akun aktif tetap bisa mengakses dashboard.
* Akun nonaktif langsung logout.
* Akun terkunci langsung logout.
* Session lama tidak dapat dipakai.
* Sistem lebih siap untuk Role dan Permission pada Tahap 10.

7. Tambahkan pengujian middleware
Buat file test:
php artisan make:test Auth/ActiveAccountMiddlewareTest
Buka:
nano tests/Feature/Auth/ActiveAccountMiddlewareTest.php
Ganti seluruh isinya:
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveAccountMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'locked_until' => null,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_inactive_logged_in_user_is_logged_out(): void
    {
        $user = User::factory()->create([
            'status' => 'inactive',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_locked_logged_in_user_is_logged_out(): void
    {
        $user = User::factory()->create([
            'status' => 'active',
            'locked_until' => now()->addMinutes(10),
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }
}

8. Format kode
Jalankan:
./vendor/bin/pint
Lalu:
./vendor/bin/pint --test

9. Jalankan test
php artisan test
Targetnya semua PASS.
Jumlah test akan bertambah dari 28 menjadi sekitar 31.

10. Uji manual
Login sebagai superadmin.
Buka:
open http://sim-madrasah.test/dashboard
Harus bisa masuk.
Nanti saat modul manajemen pengguna sudah dibuat, jika status akun diubah menjadi inactive, middleware ini akan langsung memutus session pengguna tersebut.

11. Commit ke GitHub
Jika semua test lulus:
git status
Kemudian:
git add app/Http/Middleware/EnsureAccountIsActive.php \
        bootstrap/app.php \
        routes/web.php \
        tests/Feature/Auth/ActiveAccountMiddlewareTest.php
Commit:
git commit -m "feat: enforce active account middleware"
Push:
git push
Periksa:
git status
Target:
nothing to commit, working tree clean

Kirim hasil ini
Setelah selesai, kirimkan output dari:
php artisan test
git status
Jika berhasil, kita lanjut ke Tahap 9.14B: mencatat waktu perubahan password dan final checklist authentication.


Baik, kita lanjut ke Tahap 9.14B: Mencatat Waktu Perubahan Password.
Tujuan langkah ini sederhana: setiap kali password pengguna berubah, sistem harus menyimpan waktu perubahan pada kolom:
password_changed_at
Kolom ini sudah ada di tabel users. Sekarang kita aktifkan penggunaannya.

Tahap 9.14B: Password Change Tracking
1. Konsep sederhana
Saat password berubah, sistem perlu tahu:
Kapan password terakhir diganti?
Data ini penting untuk:
* keamanan akun,
* audit dasar,
* pemeriksaan akun lama,
* kebijakan wajib ganti password,
* persiapan activity log pada tahap berikutnya.
Contoh:
Super Admin mengganti password pada 2026-08-08 21:55
Maka kolom password_changed_at harus terisi.

2. File yang akan kita ubah
Ada dua alur perubahan password di Breeze:
1. Pengguna login lalu mengganti password dari halaman Profile
2. Pengguna lupa password lalu reset password melalui link email
Jadi kita ubah dua controller:
app/Http/Controllers/Auth/PasswordController.php
app/Http/Controllers/Auth/NewPasswordController.php

3. Ubah PasswordController
Buka:
nano app/Http/Controllers/Auth/PasswordController.php
Cari method update.
Biasanya bentuk awalnya seperti ini:
$request->user()->update([
    'password' => Hash::make($request->password),
]);
Ganti seluruh isi file menjadi:
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Memperbarui password pengguna yang sedang login.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                Password::defaults(),
                'confirmed',
            ],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
            'failed_login_count' => 0,
            'locked_until' => null,
        ])->save();

        return back()->with('status', 'password-updated');
    }
}
Mengapa memakai forceFill()?
Kolom seperti ini tidak kita masukkan ke $fillable:
password_changed_at
failed_login_count
locked_until
Alasannya, pengguna tidak boleh mengubah kolom keamanan melalui request biasa.
Namun controller resmi sistem boleh mengubahnya. Karena itu kita gunakan:
forceFill()

4. Ubah NewPasswordController
Buka:
nano app/Http/Controllers/Auth/NewPasswordController.php
Cari bagian callback Password::reset.
Biasanya ada bagian seperti ini:
$user->forceFill([
    'password' => Hash::make($request->password),
    'remember_token' => Str::random(60),
])->save();
Ganti seluruh isi file menjadi:
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
     * Menampilkan halaman reset password.
     */
    public function create(Request $request)
    {
        return view('auth.reset-password', [
            'request' => $request,
        ]);
    }

    /**
     * Memproses reset password.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user) use ($request): void {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'password_changed_at' => now(),
                    'failed_login_count' => 0,
                    'locked_until' => null,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()
                ->route('login')
                ->with('status', __($status))
            : back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => __($status),
                ]);
    }
}

5. Tambahkan pengujian password update
Buka:
nano tests/Feature/Auth/PasswordUpdateTest.php
Ganti seluruh isinya menjadi:
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create([
            'password_changed_at' => null,
            'failed_login_count' => 3,
            'locked_until' => now()->addMinutes(10),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertNotNull($user->password_changed_at);
        $this->assertSame(0, $user->failed_login_count);
        $this->assertNull($user->locked_until);
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }
}

6. Mengapa reset gagal login saat password berubah?
Saat password berhasil diganti, kita reset:
'failed_login_count' => 0,
'locked_until' => null,
Alasannya:
* pengguna sudah membuktikan identitasnya,
* password baru sudah sah,
* akun tidak perlu tetap terkunci karena kegagalan lama,
* sistem menjadi lebih ramah untuk admin dan pengguna.

7. Jalankan format kode
./vendor/bin/pint
Lalu:
./vendor/bin/pint --test

8. Jalankan test
php artisan test
Target:
semua test PASS
Jumlah test kemungkinan tetap sekitar 31 jika Tahap 9.14A sudah dikerjakan. Bisa juga berbeda sedikit, tetapi tidak boleh ada test gagal.

9. Uji manual
Login sebagai superadmin.
Buka:
open http://sim-madrasah.test/profile
Ganti password melalui form profil.
Setelah berhasil, periksa lewat Tinker:
php artisan tinker
Masukkan:
$user = App\Models\User::where('username', 'superadmin')->first();

$user->only([
    'username',
    'email',
    'status',
    'password_changed_at',
    'failed_login_count',
    'locked_until',
]);
Target:
password_changed_at terisi
failed_login_count = 0
locked_until = null
Keluar:
exit

10. Commit ke GitHub
git status
Kemudian:
git add app/Http/Controllers/Auth/PasswordController.php \
        app/Http/Controllers/Auth/NewPasswordController.php \
        tests/Feature/Auth/PasswordUpdateTest.php
Commit:
git commit -m "feat: track password changes"
Push:
git push
Periksa akhir:
git status
Target:
nothing to commit, working tree clean

Kirimkan hasil dari:
php artisan test
git status
Setelah ini kita lanjut ke Tahap 9.14C: Final Checklist Authentication dan Penutupan Tahap 9.

Baik. Kita lanjut ke Tahap 9.14C: Final Checklist Authentication dan Penutupan Tahap 9.
Tahap ini tidak banyak menambah kode. Fokusnya memastikan fondasi authentication sudah stabil sebelum kita masuk ke Tahap 10: Role & Permission.
Tahap 9.14C: Final Checklist Authentication
1. Bersihkan seluruh cache Laravel
Jalankan:
php artisan optimize:clear
Tujuannya agar route, config, view, dan cache lama tidak mengganggu hasil pengecekan.

2. Periksa route authentication
Jalankan:
php artisan route:list --except-vendor
Pastikan route berikut ada:
GET|HEAD   login
POST       login
POST       logout
GET|HEAD   forgot-password
POST       forgot-password
GET|HEAD   reset-password/{token}
POST       reset-password
GET|HEAD   dashboard
GET|HEAD   profile
PATCH      profile
PUT        password
Pastikan route berikut tidak ada:
register
profile.destroy
DELETE profile
Gunakan pemeriksaan cepat:
php artisan route:list --except-vendor \
| grep -E 'register|profile.destroy|DELETE.*profile' \
|| echo "OK: registrasi publik dan hapus akun mandiri tidak aktif"
Target:
OK: registrasi publik dan hapus akun mandiri tidak aktif

3. Periksa middleware aktif
Jalankan:
grep -n "active.account" bootstrap/app.php routes/web.php
Targetnya harus terlihat bahwa middleware:
active.account
terdaftar di bootstrap/app.php dan dipakai pada route internal di routes/web.php.
Middleware ini penting karena akun yang sudah login tetap harus dicek ulang. Jika status akun berubah menjadi inactive atau locked_until masih aktif, session harus dihentikan.

4. Periksa struktur tabel users
Jalankan:
php artisan db:table users
Pastikan kolom penting berikut tersedia:
person_id
username
email
password
account_type
status
last_login_at
last_login_ip
password_changed_at
failed_login_count
locked_until
deleted_at
Pastikan foreign key ini ada:
users.person_id references people.id

5. Periksa struktur tabel people
Jalankan:
php artisan db:table people
Pastikan kolom penting berikut tersedia:
id
national_id_number
full_name
birth_place
birth_date
gender
religion
email
phone
address
photo_file_id
created_at
updated_at

6. Periksa akun administrator awal
Jalankan:
php artisan tinker
Masukkan:
$user = App\Models\User::where('username', 'superadmin')->first();

$user?->only([
    'id',
    'person_id',
    'name',
    'username',
    'email',
    'account_type',
    'status',
    'last_login_at',
    'password_changed_at',
    'failed_login_count',
    'locked_until',
]);
Target minimal:
username = superadmin
email = admin@sim-madrasah.test
account_type = internal
status = active
failed_login_count = 0
locked_until = null
Lalu cek relasi:
$user?->person?->full_name;
Target:
Administrator SIM Madrasah
Keluar:
exit

7. Pastikan password bootstrap sudah dikosongkan
Karena akun administrator sudah dibuat, password bootstrap tidak boleh dibiarkan di .env.
Jalankan:
grep -n '^SIM_INITIAL_ADMIN_PASSWORD' .env
Target yang benar:
SIM_INITIAL_ADMIN_PASSWORD=
Jika masih berisi password, buka:
nano .env
Ubah menjadi:
SIM_INITIAL_ADMIN_PASSWORD=
Lalu:
php artisan config:clear
Ini tidak mengubah password login superadmin. Password akun sudah tersimpan sebagai hash di database.

8. Pastikan .env tidak masuk Git
Jalankan:
git check-ignore -v .env
Harus menampilkan bahwa .env diabaikan oleh .gitignore.
Lalu jalankan:
git ls-files .env
Targetnya: tidak ada output.
Jika ada output, hentikan dulu. Itu berarti .env pernah masuk Git dan harus dibersihkan sebelum push.

9. Jalankan format dan test
Jalankan:
./vendor/bin/pint
Lalu:
./vendor/bin/pint --test
Kemudian:
php artisan test
Target:
semua test PASS
Jika Tahap 9.14A dan 9.14B sudah masuk, jumlah test kemungkinan sekitar 31. Jika sedikit berbeda, tidak masalah. Yang penting tidak ada test gagal.

10. Jalankan build frontend
npm run build
Target:
built
Tidak boleh ada error Vite.

11. Uji manual di browser
Buka login:
open http://sim-madrasah.test/login
Uji 1:
Username: superadmin
Password: password admin Anda
Harus berhasil masuk dashboard.
Logout.
Uji 2:
Email: admin@sim-madrasah.test
Password: password yang sama
Harus berhasil masuk dashboard.
Kemudian buka:
open http://sim-madrasah.test/register
Target: halaman 404.
Setelah login, buka:
open http://sim-madrasah.test/profile
Pastikan halaman profil hanya menampilkan:
Update Profile Information
Update Password
Bagian hapus akun tidak boleh ada.

12. Commit jika masih ada perubahan
Periksa:
git status
Jika ada file yang berubah, commit:
git add -A
git commit -m "chore: finalize authentication stage"
git push
Jika hasilnya:
nothing to commit, working tree clean
berarti semua sudah tersimpan.

13. Status akhir Tahap 9
Jika semua checklist di atas berhasil, maka Tahap 9 Authentication selesai.
Fondasi yang sudah selesai:
Laravel Breeze terpasang
Login username atau email aktif
Akun nonaktif ditolak
Akun terkunci ditolak
Percobaan login gagal dicatat
Akun dikunci sementara setelah gagal berulang
last_login_at dicatat
last_login_ip dicatat
password_changed_at dicatat
Registrasi publik ditutup
Hapus akun mandiri ditutup
Middleware active.account aktif
Initial administrator seeder tersedia
Superadmin lokal berhasil dibuat
.env aman dari Git
Test lulus
Build frontend berhasil
Titik berhenti
Jalankan dan kirimkan hasil tiga perintah ini:
php artisan test
npm run build
git status
Setelah hasilnya bersih, baru kita masuk ke Tahap 10: Role & Permission.



