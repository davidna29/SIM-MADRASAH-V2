# Tahap 10 — Role & Permission / RBAC

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 14763–18700.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

Tahap 10.1: Fondasi RBAC
1. Konsep sederhana
RBAC berarti Role Based Access Control.
Artinya, hak akses pengguna tidak ditentukan satu per satu secara acak, tetapi melalui kelompok peran.
Contoh:
User: Ahmad
Role: Guru Mata Pelajaran
Permission:
- nilai.input
- jurnal.create
- jadwal.view
Jadi sistem tidak bertanya:
Apakah Ahmad boleh input nilai?
Tetapi sistem bertanya:
Apakah role Ahmad memiliki permission nilai.input?
2. Analogi sederhana
Role seperti jabatan akses di madrasah.
Permission seperti kunci ruangan.
Contoh:
Role Bendahara
  punya kunci:
  - melihat tagihan
  - input pembayaran
  - cetak kwitansi

Role Guru Mapel
  punya kunci:
  - melihat jadwal
  - input jurnal
  - input nilai
Satu pengguna dapat memiliki lebih dari satu role.
Contoh:
Ibu Siti
- Guru Mata Pelajaran
- Wali Kelas
- Editor Berita
3. Keputusan arsitektur
Kita akan membuat RBAC custom, bukan langsung memakai package.
Alasannya:
* Sesuai desain database Tahap 6.
* Lebih mudah dipahami oleh pemula.
* Struktur tabel bisa dikendalikan penuh.
* Cocok untuk shared hosting.
* Tidak bergantung pada package tambahan.
* Mudah dihubungkan dengan Policy, Gate, dan Middleware.
Tabel yang akan kita buat sekarang:
roles
permissions
role_permissions
user_roles
user_permissions
Kita mulai dari migration saja. Belum masuk menu, middleware permission, atau dashboard role.

4. Buat migration RBAC
Jalankan:
cd ~/Herd/sim-madrasah
Lalu:
php artisan make:migration create_roles_table
php artisan make:migration create_permissions_table
php artisan make:migration create_role_permissions_table
php artisan make:migration create_user_roles_table
php artisan make:migration create_user_permissions_table

5. Migration roles
Buka file terbaru untuk roles:
nano $(ls -t database/migrations/*_create_roles_table.php | head -n 1)
Ganti isinya:
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel role.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)->unique();
            $table->string('display_name', 150);
            $table->text('description')->nullable();

            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Menghapus tabel role.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
Simpan.

6. Migration permissions
Buka:
nano $(ls -t database/migrations/*_create_permissions_table.php | head -n 1)
Ganti isinya:
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel permission.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150)->unique();
            $table->string('module', 100);
            $table->string('action', 50);
            $table->string('display_name', 150);
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('module');
            $table->index('action');
            $table->index(['module', 'action']);
            $table->index('is_active');
        });
    }

    /**
     * Menghapus tabel permission.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
Simpan.

7. Migration role_permissions
Buka:
nano $(ls -t database/migrations/*_create_role_permissions_table.php | head -n 1)
Ganti isinya:
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel penghubung role dan permission.
     */
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamp('created_at')->nullable();

            $table->primary([
                'role_id',
                'permission_id',
            ]);
        });
    }

    /**
     * Menghapus tabel penghubung role dan permission.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
Simpan.

8. Migration user_roles
Buka:
nano $(ls -t database/migrations/*_create_user_roles_table.php | head -n 1)
Ganti isinya:
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel penghubung user dan role.
     */
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->primary([
                'user_id',
                'role_id',
            ]);

            $table->index('expires_at');
        });
    }

    /**
     * Menghapus tabel penghubung user dan role.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
Simpan.

9. Migration user_permissions
Buka:
nano $(ls -t database/migrations/*_create_user_permissions_table.php | head -n 1)
Ganti isinya:
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat permission langsung untuk user.
     *
     * Digunakan hanya untuk pengecualian khusus.
     */
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('permission_mode', 20)->default('allow');

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->primary([
                'user_id',
                'permission_id',
            ]);

            $table->index('permission_mode');
            $table->index('expires_at');
        });
    }

    /**
     * Menghapus permission langsung user.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
Simpan.

10. Mengapa tabelnya dipisah?
roles
Menyimpan nama kelompok akses.
Contoh:
super_admin
kepala_madrasah
guru_mapel
bendahara
siswa
orang_tua
permissions
Menyimpan izin kecil yang spesifik.
Contoh:
users.view
users.create
payments.create
scores.input
news.approve
role_permissions
Menghubungkan role dengan permission.
Contoh:
Role bendahara memiliki permission payments.create
user_roles
Menghubungkan user dengan role.
Contoh:
User superadmin memiliki role super_admin
user_permissions
Digunakan untuk kasus khusus.
Contoh:
User tertentu diberi izin reports.export
atau:
User tertentu dilarang payments.correct
Tabel ini tidak boleh digunakan berlebihan karena dapat membuat hak akses sulit dilacak.

11. Jalankan migration
php artisan migrate
Targetnya semua migration RBAC berstatus DONE.
Lalu periksa:
php artisan db:table roles
php artisan db:table permissions
php artisan db:table role_permissions
php artisan db:table user_roles
php artisan db:table user_permissions

12. Jalankan test
php artisan test
Target:
semua test PASS

13. Commit ke GitHub
Jika migration dan test berhasil:
git status
Lalu:
git add database/migrations/
git commit -m "feat: add RBAC database tables"
git push
Periksa:
git status
Target:
nothing to commit, working tree clean

Kirim hasil ini
Kirim output dari:
php artisan migrate
php artisan db:table roles
php artisan db:table permissions
php artisan test
git status
Setelah berhasil, kita lanjut ke Tahap 10.2: Membuat Model Role, Permission, Relasi User, dan Seeder Role Awal.




Tahap 10.2A: Membuat Model Role dan Permission
Laravel menggunakan relasi Eloquent untuk menghubungkan model, termasuk relasi many-to-many melalui belongsToMany. Ini cocok untuk hubungan user dengan role, role dengan permission, dan user dengan permission langsung. (Laravel)
1. Buat model
Jalankan:
cd ~/Herd/sim-madrasah

php artisan make:model Role
php artisan make:model Permission

2. Isi model Role
Buka:
nano app/Models/Role.php
Ganti seluruh isinya:
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    /**
     * Kolom yang boleh diisi.
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system',
        'is_active',
    ];

    /**
     * Konversi tipe data.
     */
    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Permission yang dimiliki role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        );
    }

    /**
     * User yang memiliki role ini.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_roles',
            'role_id',
            'user_id'
        )->withPivot([
            'assigned_by',
            'assigned_at',
            'expires_at',
        ]);
    }
}

3. Isi model Permission
Buka:
nano app/Models/Permission.php
Ganti seluruh isinya:
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    /**
     * Kolom yang boleh diisi.
     */
    protected $fillable = [
        'name',
        'module',
        'action',
        'display_name',
        'description',
        'is_active',
    ];

    /**
     * Konversi tipe data.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Role yang memiliki permission ini.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_permissions',
            'permission_id',
            'role_id'
        );
    }

    /**
     * User yang mendapat permission langsung.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_permissions',
            'permission_id',
            'user_id'
        )->withPivot([
            'permission_mode',
            'assigned_by',
            'assigned_at',
            'expires_at',
        ]);
    }
}

Tahap 10.2B: Menambahkan Relasi dan Helper pada User
1. Buka model User
nano app/Models/User.php
Tambahkan import ini di bagian atas:
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
Lalu tambahkan method berikut di dalam class User, sebelum kurung penutup terakhir:
/**
 * Role yang dimiliki user.
 */
public function roles(): BelongsToMany
{
    return $this->belongsToMany(
        Role::class,
        'user_roles',
        'user_id',
        'role_id'
    )->withPivot([
        'assigned_by',
        'assigned_at',
        'expires_at',
    ]);
}

/**
 * Permission langsung yang diberikan ke user.
 *
 * Permission langsung dipakai hanya untuk pengecualian khusus.
 */
public function directPermissions(): BelongsToMany
{
    return $this->belongsToMany(
        Permission::class,
        'user_permissions',
        'user_id',
        'permission_id'
    )->withPivot([
        'permission_mode',
        'assigned_by',
        'assigned_at',
        'expires_at',
    ]);
}

/**
 * Memeriksa apakah user memiliki role aktif.
 */
public function hasRole(string $roleName): bool
{
    return $this->roles()
        ->where('roles.name', $roleName)
        ->where('roles.is_active', true)
        ->where(function ($query): void {
            $query
                ->whereNull('user_roles.expires_at')
                ->orWhere('user_roles.expires_at', '>', now());
        })
        ->exists();
}

/**
 * Memeriksa apakah user memiliki permission tertentu.
 *
 * Urutan pemeriksaan:
 * 1. Permission langsung dengan mode deny menolak akses.
 * 2. Permission langsung dengan mode allow memberi akses.
 * 3. Permission dari role aktif memberi akses.
 */
public function hasPermission(string $permissionName): bool
{
    $directPermission = $this->directPermissions()
        ->where('permissions.name', $permissionName)
        ->where('permissions.is_active', true)
        ->where(function ($query): void {
            $query
                ->whereNull('user_permissions.expires_at')
                ->orWhere('user_permissions.expires_at', '>', now());
        })
        ->first();

    if ($directPermission !== null) {
        return $directPermission->pivot->permission_mode === 'allow';
    }

    return $this->roles()
        ->where('roles.is_active', true)
        ->where(function ($query): void {
            $query
                ->whereNull('user_roles.expires_at')
                ->orWhere('user_roles.expires_at', '>', now());
        })
        ->whereHas('permissions', function ($query) use ($permissionName): void {
            $query
                ->where('permissions.name', $permissionName)
                ->where('permissions.is_active', true);
        })
        ->exists();
}
Mengapa deny diperiksa lebih dulu?
Karena user_permissions dipakai untuk pengecualian khusus.
Contoh:
User A memiliki role bendahara.
Role bendahara punya permission payments.correct.
Tetapi kepala madrasah ingin melarang User A melakukan koreksi pembayaran.
Maka kita beri direct permission:
payments.correct = deny
Hasilnya, izin dari role kalah oleh larangan langsung pada user.

Tahap 10.2C: Membuat Seeder RBAC Awal
Laravel menyimpan semua seeder di folder database/seeders, dan seeder bisa dijalankan secara khusus dengan opsi --class. (Laravel)
1. Buat seeder
php artisan make:seeder RbacSeeder
Buka file:
nano database/seeders/RbacSeeder.php
Ganti seluruh isinya:
<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    /**
     * Membuat role, permission dasar, dan memberikan
     * role super_admin kepada akun superadmin lokal.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $roles = [
                [
                    'name' => 'super_admin',
                    'display_name' => 'Super Admin',
                    'description' => 'Mengelola seluruh sistem SIM Madrasah.',
                    'is_system' => true,
                ],
                [
                    'name' => 'kepala_madrasah',
                    'display_name' => 'Kepala Madrasah',
                    'description' => 'Memantau laporan, persetujuan, PKKM, dan akreditasi.',
                    'is_system' => true,
                ],
                [
                    'name' => 'wakamad_kurikulum',
                    'display_name' => 'Wakamad Kurikulum',
                    'description' => 'Mengelola akademik, jadwal, kurikulum, nilai, dan rapor.',
                    'is_system' => true,
                ],
                [
                    'name' => 'wakamad_kesiswaan',
                    'display_name' => 'Wakamad Kesiswaan',
                    'description' => 'Mengelola kesiswaan, prestasi, pelanggaran, dan pembiasaan.',
                    'is_system' => true,
                ],
                [
                    'name' => 'wakamad_sarpras',
                    'display_name' => 'Wakamad Sarpras',
                    'description' => 'Mengelola sarana, prasarana, inventaris, ruangan, dan laboratorium.',
                    'is_system' => true,
                ],
                [
                    'name' => 'wakamad_humas',
                    'display_name' => 'Wakamad Humas',
                    'description' => 'Mengelola publikasi, berita, agenda, alumni, dan kerja sama.',
                    'is_system' => true,
                ],
                [
                    'name' => 'tata_usaha',
                    'display_name' => 'Tata Usaha',
                    'description' => 'Mengelola administrasi siswa, pegawai, surat, dan arsip.',
                    'is_system' => true,
                ],
                [
                    'name' => 'bendahara',
                    'display_name' => 'Bendahara',
                    'description' => 'Mengelola tagihan, pembayaran, kwitansi, dan laporan keuangan.',
                    'is_system' => true,
                ],
                [
                    'name' => 'wali_kelas',
                    'display_name' => 'Wali Kelas',
                    'description' => 'Memantau siswa dalam kelas, rapor, absensi, dan perkembangan siswa.',
                    'is_system' => true,
                ],
                [
                    'name' => 'guru_mata_pelajaran',
                    'display_name' => 'Guru Mata Pelajaran',
                    'description' => 'Mengelola pembelajaran, jurnal mengajar, absensi, dan nilai.',
                    'is_system' => true,
                ],
                [
                    'name' => 'guru_bk',
                    'display_name' => 'Guru BK',
                    'description' => 'Mengelola konseling, tindak lanjut, dan data kedisiplinan tertentu.',
                    'is_system' => true,
                ],
                [
                    'name' => 'petugas_perpustakaan',
                    'display_name' => 'Petugas Perpustakaan',
                    'description' => 'Mengelola buku, anggota, peminjaman, pengembalian, dan ebook.',
                    'is_system' => true,
                ],
                [
                    'name' => 'petugas_laboratorium',
                    'display_name' => 'Petugas Laboratorium',
                    'description' => 'Mengelola alat, bahan, peminjaman, kerusakan, dan pemeliharaan laboratorium.',
                    'is_system' => true,
                ],
                [
                    'name' => 'editor_berita',
                    'display_name' => 'Editor Berita',
                    'description' => 'Mengelola naskah, gambar, SEO, review, dan publikasi berita.',
                    'is_system' => true,
                ],
                [
                    'name' => 'orang_tua',
                    'display_name' => 'Orang Tua',
                    'description' => 'Mengakses portal orang tua dan data anak yang terhubung.',
                    'is_system' => true,
                ],
                [
                    'name' => 'siswa',
                    'display_name' => 'Siswa',
                    'description' => 'Mengakses portal siswa dan portofolio pribadi.',
                    'is_system' => true,
                ],
            ];

            foreach ($roles as $role) {
                Role::query()->updateOrCreate(
                    ['name' => $role['name']],
                    [
                        'display_name' => $role['display_name'],
                        'description' => $role['description'],
                        'is_system' => $role['is_system'],
                        'is_active' => true,
                    ]
                );
            }

            $permissions = [
                [
                    'name' => 'dashboard.view',
                    'module' => 'dashboard',
                    'action' => 'view',
                    'display_name' => 'Melihat Dashboard',
                ],
                [
                    'name' => 'profile.view',
                    'module' => 'profile',
                    'action' => 'view',
                    'display_name' => 'Melihat Profil',
                ],
                [
                    'name' => 'profile.update',
                    'module' => 'profile',
                    'action' => 'update',
                    'display_name' => 'Mengubah Profil',
                ],
                [
                    'name' => 'users.view',
                    'module' => 'users',
                    'action' => 'view',
                    'display_name' => 'Melihat Pengguna',
                ],
                [
                    'name' => 'users.create',
                    'module' => 'users',
                    'action' => 'create',
                    'display_name' => 'Membuat Pengguna',
                ],
                [
                    'name' => 'users.update',
                    'module' => 'users',
                    'action' => 'update',
                    'display_name' => 'Mengubah Pengguna',
                ],
                [
                    'name' => 'users.deactivate',
                    'module' => 'users',
                    'action' => 'deactivate',
                    'display_name' => 'Menonaktifkan Pengguna',
                ],
                [
                    'name' => 'roles.view',
                    'module' => 'roles',
                    'action' => 'view',
                    'display_name' => 'Melihat Role',
                ],
                [
                    'name' => 'roles.create',
                    'module' => 'roles',
                    'action' => 'create',
                    'display_name' => 'Membuat Role',
                ],
                [
                    'name' => 'roles.update',
                    'module' => 'roles',
                    'action' => 'update',
                    'display_name' => 'Mengubah Role',
                ],
                [
                    'name' => 'roles.assign',
                    'module' => 'roles',
                    'action' => 'assign',
                    'display_name' => 'Memberikan Role ke Pengguna',
                ],
                [
                    'name' => 'permissions.view',
                    'module' => 'permissions',
                    'action' => 'view',
                    'display_name' => 'Melihat Permission',
                ],
                [
                    'name' => 'permissions.create',
                    'module' => 'permissions',
                    'action' => 'create',
                    'display_name' => 'Membuat Permission',
                ],
                [
                    'name' => 'permissions.update',
                    'module' => 'permissions',
                    'action' => 'update',
                    'display_name' => 'Mengubah Permission',
                ],
                [
                    'name' => 'settings.view',
                    'module' => 'settings',
                    'action' => 'view',
                    'display_name' => 'Melihat Pengaturan Sistem',
                ],
                [
                    'name' => 'settings.update',
                    'module' => 'settings',
                    'action' => 'update',
                    'display_name' => 'Mengubah Pengaturan Sistem',
                ],
                [
                    'name' => 'activity_logs.view',
                    'module' => 'activity_logs',
                    'action' => 'view',
                    'display_name' => 'Melihat Activity Log',
                ],
                [
                    'name' => 'audit_logs.view',
                    'module' => 'audit_logs',
                    'action' => 'view',
                    'display_name' => 'Melihat Audit Log',
                ],
            ];

            foreach ($permissions as $permission) {
                Permission::query()->updateOrCreate(
                    ['name' => $permission['name']],
                    [
                        'module' => $permission['module'],
                        'action' => $permission['action'],
                        'display_name' => $permission['display_name'],
                        'description' => $permission['description'] ?? null,
                        'is_active' => true,
                    ]
                );
            }

            $basePermissionNames = [
                'dashboard.view',
                'profile.view',
                'profile.update',
            ];

            $basePermissionIds = Permission::query()
                ->whereIn('name', $basePermissionNames)
                ->pluck('id')
                ->all();

            Role::query()
                ->where('is_active', true)
                ->get()
                ->each(function (Role $role) use ($basePermissionIds): void {
                    $role->permissions()->syncWithoutDetaching(
                        $basePermissionIds
                    );
                });

            $superAdminRole = Role::query()
                ->where('name', 'super_admin')
                ->firstOrFail();

            $allPermissionIds = Permission::query()
                ->pluck('id')
                ->all();

            $superAdminRole->permissions()->sync($allPermissionIds);

            $superAdminUser = User::query()
                ->where('username', 'superadmin')
                ->first();

            if ($superAdminUser !== null) {
                $superAdminUser->roles()->syncWithoutDetaching([
                    $superAdminRole->id => [
                        'assigned_at' => now(),
                    ],
                ]);
            }
        });
    }
}

Tahap 10.2D: Jalankan Seeder
Jalankan:
php artisan db:seed --class=RbacSeeder
Target:
INFO  Seeding database.
Periksa isi tabel:
php artisan tinker
Masukkan:
App\Models\Role::count();
Target:
16
Lalu:
App\Models\Permission::count();
Target:
18
Lalu:
$user = App\Models\User::where('username', 'superadmin')->first();

$user->hasRole('super_admin');
Target:
true
Lalu:
$user->hasPermission('users.view');
Target:
true
Keluar:
exit

Tahap 10.2E: Membuat Test RBAC
1. Buat test
php artisan make:test Rbac/RbacRelationshipTest
Buka:
nano tests/Feature/Rbac/RbacRelationshipTest.php
Ganti seluruh isinya:
<?php

namespace Tests\Feature\Rbac;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_role(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'guru_mata_pelajaran',
            'display_name' => 'Guru Mata Pelajaran',
            'description' => 'Mengelola pembelajaran dan nilai.',
            'is_system' => true,
            'is_active' => true,
        ]);

        $user->roles()->attach($role->id, [
            'assigned_at' => now(),
        ]);

        $this->assertTrue(
            $user->hasRole('guru_mata_pelajaran')
        );
    }

    public function test_user_can_have_permission_through_role(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'guru_mata_pelajaran',
            'display_name' => 'Guru Mata Pelajaran',
            'is_system' => true,
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'scores.input',
            'module' => 'scores',
            'action' => 'input',
            'display_name' => 'Input Nilai',
            'is_active' => true,
        ]);

        $role->permissions()->attach($permission->id);

        $user->roles()->attach($role->id, [
            'assigned_at' => now(),
        ]);

        $this->assertTrue(
            $user->hasPermission('scores.input')
        );
    }

    public function test_direct_deny_permission_overrides_role_permission(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'bendahara',
            'display_name' => 'Bendahara',
            'is_system' => true,
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'payments.correct',
            'module' => 'payments',
            'action' => 'correct',
            'display_name' => 'Koreksi Pembayaran',
            'is_active' => true,
        ]);

        $role->permissions()->attach($permission->id);

        $user->roles()->attach($role->id, [
            'assigned_at' => now(),
        ]);

        $user->directPermissions()->attach($permission->id, [
            'permission_mode' => 'deny',
            'assigned_at' => now(),
        ]);

        $this->assertFalse(
            $user->hasPermission('payments.correct')
        );
    }

    public function test_expired_role_does_not_grant_permission(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'editor_berita',
            'display_name' => 'Editor Berita',
            'is_system' => true,
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'news.publish',
            'module' => 'news',
            'action' => 'publish',
            'display_name' => 'Publikasi Berita',
            'is_active' => true,
        ]);

        $role->permissions()->attach($permission->id);

        $user->roles()->attach($role->id, [
            'assigned_at' => now()->subMonth(),
            'expires_at' => now()->subDay(),
        ]);

        $this->assertFalse(
            $user->hasPermission('news.publish')
        );
    }
}

Tahap 10.2F: Format, Test, dan Commit
Jalankan:
./vendor/bin/pint
./vendor/bin/pint --test
php artisan test
Target:
semua test PASS
Jumlah test akan bertambah karena kita menambahkan test RBAC.
Jika semua berhasil:
git status
git add app/Models/Role.php \
        app/Models/Permission.php \
        app/Models/User.php \
        database/seeders/RbacSeeder.php \
        tests/Feature/Rbac/RbacRelationshipTest.php

git commit -m "feat: add RBAC models and seeders"
git push
git status
Target akhir:
nothing to commit, working tree clean

Kirim hasil ini
Kirim output dari:
php artisan db:seed --class=RbacSeeder
php artisan test
git status
Setelah berhasil, kita lanjut ke Tahap 10.3: Middleware Permission dan Gate Authorization.




Tahap 10.3: Middleware Permission dan Gate Authorization.
Pada tahap ini kita mulai memakai permission untuk membatasi akses route. Kita mulai dari route dashboard dulu. Ini aman karena dashboard adalah halaman internal pertama setelah login.

Tahap 10.3: Middleware Permission dan Gate Authorization
1. Konsep sederhana
Sampai tahap 10.2, data role dan permission sudah ada di database.
Sekarang kita buat sistem bertanya:
Apakah user ini punya permission dashboard.view?
Jika punya, user boleh masuk dashboard.
Jika tidak punya, sistem menolak dengan status:
403 Forbidden
2. Analogi sederhana
Role adalah jabatan akses.
Permission adalah kunci ruangan.
Middleware adalah penjaga pintu.
Contoh:
User ingin masuk Dashboard
        ↓
Middleware memeriksa permission dashboard.view
        ↓
Jika punya izin, masuk
Jika tidak punya izin, ditolak

3. Buat middleware permission
Jalankan:
php artisan make:middleware EnsureUserHasPermission
Buka file:
nano app/Http/Middleware/EnsureUserHasPermission.php
Ganti seluruh isinya:
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Memastikan user memiliki permission tertentu.
     */
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        if ($user === null) {
            abort(403, 'Anda belum login.');
        }

        if (! $user->can('permission', $permission)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
Penjelasan
Bagian ini:
$user->can('permission', $permission)
memanggil Gate bernama permission.
Nanti Gate tersebut akan memanggil method:
$user->hasPermission($permission)
yang sudah kita buat pada model User.

4. Daftarkan middleware
Buka:
nano bootstrap/app.php
Cari bagian:
->withMiddleware(function (Middleware $middleware): void {
Sebelumnya kita sudah punya:
'active.account' => \App\Http\Middleware\EnsureAccountIsActive::class,
Tambahkan alias baru:
'permission' => \App\Http\Middleware\EnsureUserHasPermission::class,
Hasil akhirnya kurang lebih seperti ini:
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'active.account' => \App\Http\Middleware\EnsureAccountIsActive::class,
        'permission' => \App\Http\Middleware\EnsureUserHasPermission::class,
    ]);
})
Jika ada alias lain, jangan dihapus.

5. Tambahkan Gate Authorization
Buka:
nano app/Providers/AppServiceProvider.php
Ganti seluruh isinya menjadi:
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * Super Admin selalu diberi akses.
         *
         * Ini membuat Super Admin tetap bisa mengelola sistem,
         * meskipun nanti ada permission baru yang belum dipetakan.
         */
        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->hasRole('super_admin')) {
                return true;
            }

            return null;
        });

        /*
         * Gate umum untuk memeriksa permission.
         *
         * Contoh pemakaian:
         * $user->can('permission', 'dashboard.view')
         */
        Gate::define(
            'permission',
            fn (User $user, string $permission): bool => $user
                ->hasPermission($permission)
        );
    }
}
Mengapa memakai Gate?
Middleware cocok untuk route.
Gate cocok untuk pengecekan di controller, service, dan Blade.
Contoh nanti:
Gate::authorize('permission', 'users.view');
atau di Blade:
@can('permission', 'users.view')
    <a href="#">Manajemen Pengguna</a>
@endcan

6. Terapkan permission ke dashboard
Buka:
nano routes/web.php
Cari route dashboard:
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware([
    'auth',
    'verified',
    'active.account',
])->name('dashboard');
Ubah menjadi:
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware([
    'auth',
    'verified',
    'active.account',
    'permission:dashboard.view',
])->name('dashboard');
Simpan.
Mengapa baru dashboard?
Kita mulai dari satu route agar mudah diuji.
Setelah dashboard aman, pola yang sama akan dipakai pada:
users.view
users.create
roles.view
roles.assign
settings.update
Jangan langsung pasang ke semua route agar error lebih mudah dilacak.

7. Perbarui test Active Account
Karena dashboard sekarang membutuhkan permission dashboard.view, test lama yang hanya membuat user aktif perlu diberi permission.
Buka:
nano tests/Feature/Auth/ActiveAccountMiddlewareTest.php
Ganti seluruh isinya:
<?php

namespace Tests\Feature\Auth;

use App\Models\Permission;
use App\Models\Role;
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

        $this->grantPermissionToUser($user, 'dashboard.view');

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

    private function grantPermissionToUser(
        User $user,
        string $permissionName
    ): void {
        $role = Role::create([
            'name' => 'test_role',
            'display_name' => 'Test Role',
            'is_system' => false,
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => $permissionName,
            'module' => 'dashboard',
            'action' => 'view',
            'display_name' => 'Melihat Dashboard',
            'is_active' => true,
        ]);

        $role->permissions()->attach($permission->id);

        $user->roles()->attach($role->id, [
            'assigned_at' => now(),
        ]);
    }
}

8. Buat test Permission Middleware
Jalankan:
php artisan make:test Rbac/PermissionMiddlewareTest
Buka:
nano tests/Feature/Rbac/PermissionMiddlewareTest.php
Ganti seluruh isinya:
<?php

namespace Tests\Feature\Rbac;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_permission_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $this->grantPermissionToUser($user, 'dashboard.view');

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_user_without_permission_cannot_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertForbidden();
    }

    public function test_super_admin_can_access_dashboard_without_explicit_permission(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Admin',
            'is_system' => true,
            'is_active' => true,
        ]);

        $user->roles()->attach($role->id, [
            'assigned_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    private function grantPermissionToUser(
        User $user,
        string $permissionName
    ): void {
        $role = Role::create([
            'name' => 'test_dashboard_role',
            'display_name' => 'Test Dashboard Role',
            'is_system' => false,
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => $permissionName,
            'module' => 'dashboard',
            'action' => 'view',
            'display_name' => 'Melihat Dashboard',
            'is_active' => true,
        ]);

        $role->permissions()->attach($permission->id);

        $user->roles()->attach($role->id, [
            'assigned_at' => now(),
        ]);
    }
}

9. Periksa route
Jalankan:
php artisan optimize:clear
Lalu:
php artisan route:list --path=dashboard
Route dashboard harus tetap ada.

10. Format dan test
Jalankan:
./vendor/bin/pint
Lalu:
./vendor/bin/pint --test
Kemudian:
php artisan test
Target:
semua test PASS
Jumlah test akan bertambah dari 35 menjadi sekitar 38.

11. Uji manual
Login sebagai superadmin.
Buka:
open http://sim-madrasah.test/dashboard
Harus tetap bisa masuk.
Jika terjadi 403, berarti role super_admin belum terpasang ke user superadmin. Jalankan ulang:
php artisan db:seed --class=RbacSeeder
Lalu coba login ulang.

12. Commit ke GitHub
Jika semua test lulus:
git status
Kemudian:
git add app/Http/Middleware/EnsureUserHasPermission.php \
        app/Providers/AppServiceProvider.php \
        bootstrap/app.php \
        routes/web.php \
        tests/Feature/Auth/ActiveAccountMiddlewareTest.php \
        tests/Feature/Rbac/PermissionMiddlewareTest.php
Commit:
git commit -m "feat: add permission middleware and gate authorization"
Push:
git push
Periksa:
git status
Target:
nothing to commit, working tree clean

Kirim hasil dari:
php artisan test
git status
Setelah berhasil, kita lanjut ke Tahap 10.4: Menyiapkan halaman admin sederhana untuk melihat role dan permission.






Tahap 10.4: Halaman Admin Sederhana untuk Melihat Role dan Permission.
Pada tahap ini kita belum membuat fitur tambah, ubah, hapus, atau assign role. Kita hanya membuat halaman read-only agar Super Admin dapat melihat data role dan permission yang sudah dibuat oleh RbacSeeder.
Tahap 10.4: Halaman Admin Role dan Permission
1. Konsep sederhana
Kita sudah punya data:
roles
permissions
role_permissions
user_roles
user_permissions
Sekarang kita buat halaman untuk melihatnya.
Halaman yang dibuat:
/admin/roles
/admin/permissions
Hak aksesnya:
/admin/roles       membutuhkan roles.view
/admin/permissions membutuhkan permissions.view
2. Analogi sederhana
Tahap sebelumnya kita sudah membuat lemari kunci akses.
Sekarang kita membuat papan informasi agar admin bisa melihat:
* Role apa saja yang tersedia.
* Permission apa saja yang tersedia.
* Role memiliki berapa permission.
* Permission dipakai oleh berapa role.
* Role aktif atau tidak.
* Permission aktif atau tidak.

3. Buat controller admin
Jalankan:
php artisan make:controller Admin/RoleController
php artisan make:controller Admin/PermissionController

4. Isi RoleController
Buka:
nano app/Http/Controllers/Admin/RoleController.php
Ganti seluruh isinya:
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Contracts\View\View;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar role.
     */
    public function index(): View
    {
        $roles = Role::query()
            ->withCount([
                'users',
                'permissions',
            ])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }
}
Penjelasan
Bagian ini:
withCount([
    'users',
    'permissions',
])
membuat sistem menampilkan jumlah:
berapa user memakai role ini
berapa permission dimiliki role ini
Kita belum menampilkan detail user atau detail permission. Itu akan dibuat setelah struktur halaman admin lebih stabil.

5. Isi PermissionController
Buka:
nano app/Http/Controllers/Admin/PermissionController.php
Ganti seluruh isinya:
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Contracts\View\View;

class PermissionController extends Controller
{
    /**
     * Menampilkan daftar permission.
     */
    public function index(): View
    {
        $permissions = Permission::query()
            ->withCount('roles')
            ->orderBy('module')
            ->orderBy('action')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.permissions.index', [
            'permissions' => $permissions,
        ]);
    }
}
Penjelasan
Permission diurutkan berdasarkan:
module
action
name
Agar lebih mudah dibaca.
Contoh:
dashboard.view
permissions.view
roles.assign
roles.create
roles.update
users.create
users.update
users.view

6. Tambahkan route admin
Buka:
nano routes/web.php
Tambahkan import di bagian atas:
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
Lalu tambahkan route ini setelah route dashboard atau sebelum route profile:
Route::middleware([
    'auth',
    'verified',
    'active.account',
])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.view')
        ->name('roles.index');

    Route::get('/permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permissions.view')
        ->name('permissions.index');
});
Mengapa pakai prefix admin?
Agar semua halaman pengelolaan sistem berada dalam alamat yang rapi:
/admin/roles
/admin/permissions
/admin/users
/admin/settings
Nanti modul admin lain akan mengikuti pola yang sama.

7. Buat folder view
Jalankan:
mkdir -p resources/views/admin/roles
mkdir -p resources/views/admin/permissions

8. Buat view daftar role
Buat file:
nano resources/views/admin/roles/index.blade.php
Isi:
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Role') }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Daftar kelompok hak akses pengguna SIM Madrasah.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Nama Role
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Label
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Permission
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        User
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Sistem
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Status
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($roles as $role)
                                    <tr>
                                        <td class="px-4 py-3 font-mono text-gray-900">
                                            {{ $role->name }}
                                        </td>

                                        <td class="px-4 py-3 text-gray-700">
                                            <div class="font-medium">
                                                {{ $role->display_name }}
                                            </div>

                                            @if ($role->description)
                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $role->description }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $role->permissions_count }}
                                        </td>

                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $role->users_count }}
                                        </td>

                                        <td class="px-4 py-3">
                                            @if ($role->is_system)
                                                <span class="rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                                    Ya
                                                </span>
                                            @else
                                                <span class="rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600">
                                                    Tidak
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3">
                                            @if ($role->is_active)
                                                <span class="rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                            Belum ada data role.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

9. Buat view daftar permission
Buat file:
nano resources/views/admin/permissions/index.blade.php
Isi:
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Permission') }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Daftar izin rinci yang digunakan untuk membatasi akses fitur.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Permission
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Module
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Action
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Label
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Role
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                        Status
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($permissions as $permission)
                                    <tr>
                                        <td class="px-4 py-3 font-mono text-gray-900">
                                            {{ $permission->name }}
                                        </td>

                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $permission->module }}
                                        </td>

                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $permission->action }}
                                        </td>

                                        <td class="px-4 py-3 text-gray-700">
                                            <div class="font-medium">
                                                {{ $permission->display_name }}
                                            </div>

                                            @if ($permission->description)
                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $permission->description }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $permission->roles_count }}
                                        </td>

                                        <td class="px-4 py-3">
                                            @if ($permission->is_active)
                                                <span class="rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                            Belum ada data permission.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $permissions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

10. Tambahkan menu sederhana di navigation
Buka:
nano resources/views/layouts/navigation.blade.php
Cari area menu desktop, biasanya terdapat link Dashboard seperti ini:
<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-nav-link>
Tambahkan setelah link Dashboard:
@can('permission', 'roles.view')
    <x-nav-link
        :href="route('admin.roles.index')"
        :active="request()->routeIs('admin.roles.*')"
    >
        {{ __('Role') }}
    </x-nav-link>
@endcan

@can('permission', 'permissions.view')
    <x-nav-link
        :href="route('admin.permissions.index')"
        :active="request()->routeIs('admin.permissions.*')"
    >
        {{ __('Permission') }}
    </x-nav-link>
@endcan
Kemudian cari area menu responsive, biasanya ada:
<x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-responsive-nav-link>
Tambahkan setelahnya:
@can('permission', 'roles.view')
    <x-responsive-nav-link
        :href="route('admin.roles.index')"
        :active="request()->routeIs('admin.roles.*')"
    >
        {{ __('Role') }}
    </x-responsive-nav-link>
@endcan

@can('permission', 'permissions.view')
    <x-responsive-nav-link
        :href="route('admin.permissions.index')"
        :active="request()->routeIs('admin.permissions.*')"
    >
        {{ __('Permission') }}
    </x-responsive-nav-link>
@endcan
Mengapa menu memakai @can?
Agar menu hanya muncul untuk pengguna yang memiliki permission.
Namun, ini hanya menyembunyikan menu. Keamanan utama tetap ada pada route middleware:
permission:roles.view
permission:permissions.view
Jadi pengguna tidak bisa masuk hanya dengan mengetik URL manual.

11. Buat test halaman admin
Jalankan:
php artisan make:test Rbac/AdminRolePermissionPageTest
Buka:
nano tests/Feature/Rbac/AdminRolePermissionPageTest.php
Ganti seluruh isinya:
<?php

namespace Tests\Feature\Rbac;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRolePermissionPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_roles_view_permission_can_open_roles_page(): void
    {
        $user = User::factory()->create();

        $this->grantPermissionToUser($user, 'roles.view');

        Role::create([
            'name' => 'guru_mata_pelajaran',
            'display_name' => 'Guru Mata Pelajaran',
            'is_system' => true,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin/roles');

        $response
            ->assertStatus(200)
            ->assertSee('Guru Mata Pelajaran');
    }

    public function test_user_without_roles_view_permission_cannot_open_roles_page(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/admin/roles');

        $response->assertForbidden();
    }

    public function test_user_with_permissions_view_permission_can_open_permissions_page(): void
    {
        $user = User::factory()->create();

        $this->grantPermissionToUser($user, 'permissions.view');

        Permission::create([
            'name' => 'users.view',
            'module' => 'users',
            'action' => 'view',
            'display_name' => 'Melihat Pengguna',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/admin/permissions');

        $response
            ->assertStatus(200)
            ->assertSee('Melihat Pengguna');
    }

    public function test_user_without_permissions_view_permission_cannot_open_permissions_page(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/admin/permissions');

        $response->assertForbidden();
    }

    private function grantPermissionToUser(
        User $user,
        string $permissionName
    ): void {
        $role = Role::create([
            'name' => 'test_admin_role',
            'display_name' => 'Test Admin Role',
            'is_system' => false,
            'is_active' => true,
        ]);

        [$module, $action] = explode('.', $permissionName);

        $permission = Permission::create([
            'name' => $permissionName,
            'module' => $module,
            'action' => $action,
            'display_name' => $permissionName,
            'is_active' => true,
        ]);

        $role->permissions()->attach($permission->id);

        $user->roles()->attach($role->id, [
            'assigned_at' => now(),
        ]);
    }
}

12. Bersihkan cache dan periksa route
Jalankan:
php artisan optimize:clear
Periksa route:
php artisan route:list --path=admin
Harus muncul:
GET|HEAD admin/roles
GET|HEAD admin/permissions

13. Jalankan format, test, dan build
./vendor/bin/pint
./vendor/bin/pint --test
php artisan test
npm run build
Target:
semua test PASS
build berhasil
Jumlah test akan bertambah dari 38 menjadi sekitar 42.

14. Uji manual
Pastikan superadmin sudah punya role dan permission dari RBAC seeder:
php artisan db:seed --class=RbacSeeder
Login sebagai superadmin, lalu buka:
open http://sim-madrasah.test/admin/roles
open http://sim-madrasah.test/admin/permissions
Keduanya harus tampil.
Jika menu Role dan Permission belum muncul, logout lalu login ulang. Jika masih belum muncul, jalankan:
php artisan optimize:clear

15. Commit ke GitHub
Jika semua berhasil:
git status
Lalu:
git add app/Http/Controllers/Admin/RoleController.php \
        app/Http/Controllers/Admin/PermissionController.php \
        routes/web.php \
        resources/views/admin/roles/index.blade.php \
        resources/views/admin/permissions/index.blade.php \
        resources/views/layouts/navigation.blade.php \
        tests/Feature/Rbac/AdminRolePermissionPageTest.php
Commit:
git commit -m "feat: add admin role and permission pages"
Push:
git push
Periksa:
git status
Target:
nothing to commit, working tree clean

Kirim hasil ini
Kirim output dari:
php artisan route:list --path=admin
php artisan test
npm run build
git status
Setelah berhasil, kita lanjut ke Tahap 10.5: Halaman detail role dan daftar permission di dalam role.






Tahap 10.5: Halaman Detail Role dan Daftar Permission di Dalam Role.
Pada tahap ini kita belum membuat fitur edit role dan belum membuat fitur assign permission. Kita hanya membuat halaman detail agar Super Admin dapat melihat:
Role apa ini?
Berapa user yang memakai role ini?
Permission apa saja yang dimiliki role ini?
Module apa saja yang terkait?
Status role aktif atau tidak?

Tahap 10.5: Detail Role
1. Konsep sederhana
Pada tahap sebelumnya, halaman role hanya menampilkan daftar ringkas.
Sekarang kita buat halaman detail:
/admin/roles/{role}
Contoh:
/admin/roles/1
Halaman ini menampilkan satu role secara lebih lengkap.

2. Analogi sederhana
Halaman daftar role seperti daftar nama ruangan.
Halaman detail role seperti membuka satu ruangan dan melihat kunci apa saja yang tersedia di dalamnya.
Contoh:
Role: Bendahara
Permission:
- dashboard.view
- profile.view
- profile.update
- payments.create
- payments.correct
- payments.report

3. Tambahkan method show di RoleController
Buka:
nano app/Http/Controllers/Admin/RoleController.php
Ganti seluruh isinya menjadi:
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Contracts\View\View;

class RoleController extends Controller
{
    /**
     * Menampilkan daftar role.
     */
    public function index(): View
    {
        $roles = Role::query()
            ->withCount([
                'users',
                'permissions',
            ])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Menampilkan detail role dan permission yang dimiliki.
     */
    public function show(Role $role): View
    {
        $role->loadCount([
            'users',
            'permissions',
        ]);

        $role->load([
            'permissions' => function ($query): void {
                $query
                    ->orderBy('module')
                    ->orderBy('action')
                    ->orderBy('name');
            },
        ]);

        $permissionsByModule = $role->permissions
            ->groupBy('module');

        return view('admin.roles.show', [
            'role' => $role,
            'permissionsByModule' => $permissionsByModule,
        ]);
    }
}
Penjelasan
Bagian ini:
public function show(Role $role): View
menggunakan route model binding.
Artinya, Laravel otomatis mencari data role berdasarkan {role} pada URL.
Contoh:
/admin/roles/1
Laravel akan mencari:
roles.id = 1
Jika tidak ditemukan, Laravel otomatis menampilkan 404.

4. Tambahkan route detail role
Buka:
nano routes/web.php
Cari route admin yang sudah dibuat:
Route::middleware([
    'auth',
    'verified',
    'active.account',
])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.view')
        ->name('roles.index');

    Route::get('/permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permissions.view')
        ->name('permissions.index');
});
Ubah menjadi:
Route::middleware([
    'auth',
    'verified',
    'active.account',
])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.view')
        ->name('roles.index');

    Route::get('/roles/{role}', [RoleController::class, 'show'])
        ->middleware('permission:roles.view')
        ->name('roles.show');

    Route::get('/permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permissions.view')
        ->name('permissions.index');
});
Mengapa tetap memakai roles.view?
Karena halaman detail role masih bersifat baca data.
Permission baru seperti roles.update baru dipakai ketika kita membuat fitur edit role atau assign permission.

5. Tambahkan tombol detail pada halaman daftar role
Buka:
nano resources/views/admin/roles/index.blade.php
Cari bagian tabel header.
Tambahkan kolom baru setelah kolom Status:
<th class="px-4 py-3 text-left font-semibold text-gray-700">
    Aksi
</th>
Sehingga bagian header menjadi kurang lebih:
<tr>
    <th class="px-4 py-3 text-left font-semibold text-gray-700">
        Nama Role
    </th>
    <th class="px-4 py-3 text-left font-semibold text-gray-700">
        Label
    </th>
    <th class="px-4 py-3 text-left font-semibold text-gray-700">
        Permission
    </th>
    <th class="px-4 py-3 text-left font-semibold text-gray-700">
        User
    </th>
    <th class="px-4 py-3 text-left font-semibold text-gray-700">
        Sistem
    </th>
    <th class="px-4 py-3 text-left font-semibold text-gray-700">
        Status
    </th>
    <th class="px-4 py-3 text-left font-semibold text-gray-700">
        Aksi
    </th>
</tr>
Lalu pada bagian isi baris role, tambahkan setelah kolom status:
<td class="px-4 py-3">
    <a
        href="{{ route('admin.roles.show', $role) }}"
        class="text-sm font-medium text-green-700 hover:text-green-900"
    >
        Detail
    </a>
</td>
Karena sekarang jumlah kolom menjadi 7, ubah bagian @empty:
<td colspan="6" class="px-4 py-6 text-center text-gray-500">
menjadi:
<td colspan="7" class="px-4 py-6 text-center text-gray-500">

6. Buat halaman detail role
Buat file:
nano resources/views/admin/roles/show.blade.php
Isi dengan:
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail Role
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Informasi role dan permission yang dimiliki.
                </p>
            </div>

            <a
                href="{{ route('admin.roles.index') }}"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
            >
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                        <div>
                            <div class="text-sm text-gray-500">
                                Nama Role
                            </div>

                            <div class="mt-1 font-mono text-gray-900">
                                {{ $role->name }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Label
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $role->display_name }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Jumlah Permission
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $role->permissions_count }}
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Jumlah User
                            </div>

                            <div class="mt-1 font-medium text-gray-900">
                                {{ $role->users_count }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <div class="text-sm text-gray-500">
                                Role Sistem
                            </div>

                            <div class="mt-2">
                                @if ($role->is_system)
                                    <span class="rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                        Ya
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600">
                                        Tidak
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Status
                            </div>

                            <div class="mt-2">
                                @if ($role->is_active)
                                    <span class="rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">
                                Dibuat
                            </div>

                            <div class="mt-1 text-gray-900">
                                {{ $role->created_at?->format('d M Y H:i') ?? '-' }}
                            </div>
                        </div>
                    </div>

                    @if ($role->description)
                        <div class="mt-6">
                            <div class="text-sm text-gray-500">
                                Deskripsi
                            </div>

                            <p class="mt-1 text-gray-700">
                                {{ $role->description }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Permission Role
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Daftar permission yang dimiliki oleh role ini, dikelompokkan berdasarkan module.
                            </p>
                        </div>
                    </div>

                    @if ($permissionsByModule->isEmpty())
                        <div class="rounded-md border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                            Role ini belum memiliki permission.
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($permissionsByModule as $module => $permissions)
                                <div class="rounded-lg border border-gray-200">
                                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                                        <h4 class="font-semibold text-gray-800">
                                            Module: {{ $module }}
                                        </h4>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                                        Permission
                                                    </th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                                        Action
                                                    </th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                                        Label
                                                    </th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                                        Status
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                @foreach ($permissions as $permission)
                                                    <tr>
                                                        <td class="px-4 py-3 font-mono text-gray-900">
                                                            {{ $permission->name }}
                                                        </td>

                                                        <td class="px-4 py-3 text-gray-700">
                                                            {{ $permission->action }}
                                                        </td>

                                                        <td class="px-4 py-3 text-gray-700">
                                                            {{ $permission->display_name }}
                                                        </td>

                                                        <td class="px-4 py-3">
                                                            @if ($permission->is_active)
                                                                <span class="rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                                                                    Aktif
                                                                </span>
                                                            @else
                                                                <span class="rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                                                                    Nonaktif
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

7. Buat test halaman detail role
Jalankan:
php artisan make:test Rbac/AdminRoleDetailPageTest
Buka:
nano tests/Feature/Rbac/AdminRoleDetailPageTest.php
Ganti seluruh isinya:
<?php

namespace Tests\Feature\Rbac;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleDetailPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_roles_view_permission_can_open_role_detail_page(): void
    {
        $user = User::factory()->create();

        $this->grantPermissionToUser($user, 'roles.view');

        $role = Role::create([
            'name' => 'bendahara',
            'display_name' => 'Bendahara',
            'description' => 'Mengelola pembayaran siswa.',
            'is_system' => true,
            'is_active' => true,
        ]);

        $permission = Permission::create([
            'name' => 'payments.create',
            'module' => 'payments',
            'action' => 'create',
            'display_name' => 'Input Pembayaran',
            'is_active' => true,
        ]);

        $role->permissions()->attach($permission->id);

        $response = $this
            ->actingAs($user)
            ->get(route('admin.roles.show', $role));

        $response
            ->assertStatus(200)
            ->assertSee('Bendahara')
            ->assertSee('payments.create')
            ->assertSee('Input Pembayaran');
    }

    public function test_user_without_roles_view_permission_cannot_open_role_detail_page(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'guru_bk',
            'display_name' => 'Guru BK',
            'is_system' => true,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('admin.roles.show', $role));

        $response->assertForbidden();
    }

    public function test_role_detail_page_returns_not_found_for_unknown_role(): void
    {
        $user = User::factory()->create();

        $this->grantPermissionToUser($user, 'roles.view');

        $response = $this
            ->actingAs($user)
            ->get('/admin/roles/999999');

        $response->assertNotFound();
    }

    private function grantPermissionToUser(
        User $user,
        string $permissionName
    ): void {
        $role = Role::create([
            'name' => 'test_role_viewer',
            'display_name' => 'Test Role Viewer',
            'is_system' => false,
            'is_active' => true,
        ]);

        [$module, $action] = explode('.', $permissionName);

        $permission = Permission::create([
            'name' => $permissionName,
            'module' => $module,
            'action' => $action,
            'display_name' => $permissionName,
            'is_active' => true,
        ]);

        $role->permissions()->attach($permission->id);

        $user->roles()->attach($role->id, [
            'assigned_at' => now(),
        ]);
    }
}

8. Bersihkan cache dan periksa route
Jalankan:
php artisan optimize:clear
Lalu:
php artisan route:list --path=admin/roles
Target route yang muncul:
GET|HEAD admin/roles
GET|HEAD admin/roles/{role}

9. Jalankan format, test, dan build
./vendor/bin/pint
./vendor/bin/pint --test
php artisan test
npm run build
Target:
semua test PASS
build berhasil
Jumlah test akan bertambah dari 42 menjadi sekitar 45.

10. Uji manual
Pastikan data role dan permission sudah tersedia:
php artisan db:seed --class=RbacSeeder
Login sebagai superadmin.
Buka:
open http://sim-madrasah.test/admin/roles
Klik link Detail pada salah satu role.
Atau langsung buka:
open http://sim-madrasah.test/admin/roles/1
Halaman detail role harus tampil.

11. Commit ke GitHub
Jika semua berhasil:
git status
Kemudian:
git add app/Http/Controllers/Admin/RoleController.php \
        routes/web.php \
        resources/views/admin/roles/index.blade.php \
        resources/views/admin/roles/show.blade.php \
        tests/Feature/Rbac/AdminRoleDetailPageTest.php
Commit:
git commit -m "feat: add role detail page"
Push:
git push
Periksa:
git status
Target:
nothing to commit, working tree clean

Kirim hasil ini
Kirim output dari:
php artisan route:list --path=admin/roles
php artisan test
npm run build
git status
Setelah berhasil, kita lanjut ke Tahap 10.6: Halaman Detail Permission dan Role yang Menggunakan Permission Tersebut.






Tahap 10.6: Halaman Detail Permission dan Daftar Role yang Menggunakan Permission Tersebut.
Status terakhir yang sudah tercapai:
✅ Laravel 12 ✅ Breeze Authentication ✅ User model dengan RBAC ✅ Role & Permission database ✅ Middleware Permission ✅ Gate Authorization ✅ Halaman daftar Role ✅ Halaman detail Role + daftar Permission dalam Role
Sekarang kita balik arah:
Sebelumnya:
ROLE
 |
 | memiliki
 |
PERMISSION
Sekarang kita buat:
PERMISSION
 |
 | digunakan oleh
 |
ROLE
Ini penting karena nanti saat sistem sudah besar, admin perlu menjawab pertanyaan seperti:
"Siapa saja yang memiliki akses payments.create?"
atau:
"Role mana saja yang bisa melihat data siswa?"

Tahap 10.6
Detail Permission
Tujuan
Membuat halaman:
/admin/permissions/{permission}
Contoh:
/admin/permissions/15
Menampilkan:
Permission:
students.view

Module:
students

Action:
view

Digunakan oleh Role:

✓ Tata Usaha
✓ Wali Kelas
✓ Kepala Madrasah
✓ Super Admin

1. Tambahkan method show PermissionController
Buka:
app/Http/Controllers/Admin/PermissionController.php
Ubah menjadi:
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Contracts\View\View;

class PermissionController extends Controller
{
    /**
     * Menampilkan daftar permission.
     */
    public function index(): View
    {
        $permissions = Permission::query()
            ->withCount('roles')
            ->orderBy('module')
            ->orderBy('action')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.permissions.index', [
            'permissions' => $permissions,
        ]);
    }


    /**
     * Menampilkan detail permission.
     */
    public function show(Permission $permission): View
    {
        $permission->load([
            'roles' => function ($query): void {
                $query
                    ->orderBy('name');
            },
        ]);

        return view('admin.permissions.show', [
            'permission' => $permission,
        ]);
    }
}

Penjelasan
Sebelumnya:
withCount('roles')
hanya menghitung:
Permission A
dipakai 5 role
Sekarang:
load('roles')
mengambil data lengkap:
Permission A

Role:
- Admin
- Guru
- Wali Kelas

2. Tambahkan route detail permission
Buka:
routes/web.php
Cari:
Route::get('/permissions', [PermissionController::class, 'index'])
    ->middleware('permission:permissions.view')
    ->name('permissions.index');
Tambahkan:
Route::get('/permissions/{permission}', [PermissionController::class, 'show'])
    ->middleware('permission:permissions.view')
    ->name('permissions.show');
Hasil:
Route::get('/permissions', ...)
Route::get('/permissions/{permission}', ...)

3. Tambahkan tombol Detail di daftar Permission
Buka:
resources/views/admin/permissions/index.blade.php
Tambahkan header baru:
Cari:
<th class="px-4 py-3 text-left font-semibold text-gray-700">
    Status
</th>
Tambahkan setelahnya:
<th class="px-4 py-3 text-left font-semibold text-gray-700">
    Aksi
</th>

Cari bagian isi tabel:
<td class="px-4 py-3">
    @if ($permission->is_active)
Setelah blok status selesai tambahkan:
<td class="px-4 py-3">

    <a
        href="{{ route('admin.permissions.show', $permission) }}"
        class="text-sm font-medium text-green-700 hover:text-green-900"
    >
        Detail
    </a>

</td>

Jangan lupa ubah:
colspan="6"
menjadi:
colspan="7"

4. Buat halaman detail Permission
Buat folder jika belum ada:
mkdir -p resources/views/admin/permissions
Buat file:
resources/views/admin/permissions/show.blade.php
Isi:
<x-app-layout>

<x-slot name="header">

<div class="flex items-center justify-between">

<div>

<h2 class="font-semibold text-xl text-gray-800">
Detail Permission
</h2>

<p class="mt-1 text-sm text-gray-500">
Informasi permission dan role yang menggunakan akses ini.
</p>

</div>


<a
href="{{ route('admin.permissions.index') }}"
class="rounded-md border px-4 py-2 text-sm"
>
Kembali
</a>


</div>

</x-slot>



<div class="py-8">

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">



<div class="bg-white shadow-sm rounded-lg">

<div class="p-6">


<div class="grid grid-cols-1 md:grid-cols-4 gap-6">


<div>

<div class="text-sm text-gray-500">
Permission
</div>

<div class="mt-1 font-mono">
{{ $permission->name }}
</div>

</div>



<div>

<div class="text-sm text-gray-500">
Module
</div>

<div class="mt-1">
{{ $permission->module }}
</div>

</div>



<div>

<div class="text-sm text-gray-500">
Action
</div>

<div class="mt-1">
{{ $permission->action }}
</div>

</div>



<div>

<div class="text-sm text-gray-500">
Status
</div>


<div class="mt-2">

@if($permission->is_active)

<span class="rounded-full bg-green-50 px-2 py-1 text-xs text-green-700">
Aktif
</span>

@else

<span class="rounded-full bg-red-50 px-2 py-1 text-xs text-red-700">
Nonaktif
</span>

@endif


</div>


</div>



</div>



@if($permission->description)

<div class="mt-6">

<div class="text-sm text-gray-500">
Deskripsi
</div>

<p class="mt-1">
{{ $permission->description }}
</p>

</div>

@endif



</div>

</div>





<div class="bg-white shadow-sm rounded-lg">


<div class="p-6">


<h3 class="text-lg font-semibold">
Role yang memiliki permission ini
</h3>


<p class="mt-1 text-sm text-gray-500">
Daftar role yang dapat menggunakan akses ini.
</p>



<div class="mt-6 overflow-x-auto">


<table class="min-w-full divide-y divide-gray-200">


<thead class="bg-gray-50">

<tr>

<th class="px-4 py-3 text-left">
Role
</th>

<th class="px-4 py-3 text-left">
Label
</th>

<th class="px-4 py-3 text-left">
Status
</th>

</tr>

</thead>



<tbody class="divide-y">


@forelse($permission->roles as $role)


<tr>


<td class="px-4 py-3 font-mono">

{{ $role->name }}

</td>


<td class="px-4 py-3">

{{ $role->display_name }}

</td>


<td class="px-4 py-3">


@if($role->is_active)

<span class="text-green-700">
Aktif
</span>

@else

<span class="text-red-700">
Nonaktif
</span>

@endif


</td>


</tr>


@empty


<tr>

<td colspan="3"
class="px-4 py-6 text-center text-gray-500">

Belum ada role.

</td>

</tr>


@endforelse


</tbody>


</table>


</div>


</div>

</div>



</div>

</div>


</x-app-layout>

5. Buat Test
Jalankan:
php artisan make:test Rbac/AdminPermissionDetailPageTest
Buka:
tests/Feature/Rbac/AdminPermissionDetailPageTest.php
Isi:
<?php

namespace Tests\Feature\Rbac;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionDetailPageTest extends TestCase
{
    use RefreshDatabase;


    public function test_user_with_permission_can_open_permission_detail()
    {

        $user = User::factory()->create();


        $role = Role::create([
            'name'=>'guru',
            'display_name'=>'Guru',
            'is_active'=>true,
            'is_system'=>true,
        ]);


        $viewPermission = Permission::create([
            'name'=>'permissions.view',
            'module'=>'permissions',
            'action'=>'view',
            'display_name'=>'Melihat Permission',
            'is_active'=>true,
        ]);


        $targetPermission = Permission::create([
            'name'=>'students.view',
            'module'=>'students',
            'action'=>'view',
            'display_name'=>'Melihat Data Siswa',
            'is_active'=>true,
        ]);


        $role->permissions()->attach($targetPermission);


        $user->roles()->attach($role);


        $role->permissions()->attach($viewPermission);



        $response=$this
            ->actingAs($user)
            ->get(
                route(
                    'admin.permissions.show',
                    $targetPermission
                )
            );


        $response
            ->assertStatus(200)
            ->assertSee('students.view')
            ->assertSee('Guru');

    }



    public function test_user_without_permission_cannot_open_permission_detail()
    {

        $user=User::factory()->create();


        $permission=Permission::create([
            'name'=>'students.view',
            'module'=>'students',
            'action'=>'view',
            'display_name'=>'Melihat Siswa',
            'is_active'=>true,
        ]);


        $response=$this
            ->actingAs($user)
            ->get(
                route(
                    'admin.permissions.show',
                    $permission
                )
            );


        $response->assertForbidden();

    }


}

6. Jalankan pemeriksaan
Urutan:
php artisan optimize:clear
Lalu:
php artisan route:list --path=admin/permissions
Target:
GET admin/permissions
GET admin/permissions/{permission}
Kemudian:
./vendor/bin/pint
php artisan test
npm run build

7. Commit
Jika semua aman:
git add .
git commit -m "feat: add permission detail page"
git push

Target setelah selesai:
Tests:
45+ passed

Build:
success

Git:
working tree clean

Setelah ini kita masuk ke Tahap 10.7: Role Permission Management (Assign Permission ke Role melalui UI).
Ini tahap terakhir RBAC sebelum masuk ke Tahap 11 Dashboard. Di tahap 10.7 nanti kita mulai membuat form interaktif pertama di SIM Madrasah.





Tahap 10.7: Role Permission Management (Assign Permission ke Role melalui UI).
Ini adalah tahap terakhir dari modul RBAC (Role Based Access Control) sebelum masuk ke Tahap 11 Dashboard.
Status arsitektur RBAC kita sekarang:
USER
 |
 | memiliki
 |
ROLE
 |
 | memiliki
 |
PERMISSION
Sampai tahap 10.6:
✅ Melihat daftar role ✅ Melihat detail role ✅ Melihat permission dalam role ✅ Melihat detail permission ✅ Melihat role yang memakai permission
Sekarang kita membuat fitur:
Admin
 |
 | memilih Role
 |
 | mencentang Permission
 |
 | Simpan
 |
Database role_permissions berubah

Tahap 10.7
Assign Permission ke Role melalui UI
Tujuan
Membuat halaman:
/admin/roles/{role}/permissions
Contoh:
/admin/roles/5/permissions
Menampilkan:
Role:
Bendahara

☑ dashboard.view
☑ payments.view
☑ payments.create
☐ users.delete

[Simpan Permission]

1. Konsep sederhana
Sebelumnya hubungan role dan permission dibuat melalui Seeder:
$role->permissions()->attach($permission);
Sekarang kita pindahkan ke halaman admin.
Jadi nanti Super Admin tidak perlu edit kode.

Analogi
Sebelumnya:
Programmer memasukkan kunci ke lemari
Sekarang:
Administrator sekolah bisa memilih sendiri
kunci mana yang diberikan kepada jabatan tertentu
Contoh:
Kepala Madrasah:
✓ lihat laporan
✓ lihat akademik
✓ lihat keuangan
Bendahara:
✓ pembayaran
✓ laporan SPP
✗ data guru

2. Tambahkan Permission Baru
Karena fitur ini mengubah hubungan role, kita buat permission khusus:
roles.permission.assign
Artinya:
"User boleh mengatur permission sebuah role"

Buka:
database/seeders/RbacSeeder.php
Cari daftar permission.
Tambahkan:
[
    'name' => 'roles.permission.assign',
    'module' => 'roles',
    'action' => 'permission_assign',
    'display_name' => 'Mengatur Permission Role',
    'description' => 'Memberikan atau mencabut permission dari role.',
    'is_active' => true,
],

Jalankan:
php artisan db:seed --class=RbacSeeder

3. Tambahkan method controller
Buka:
app/Http/Controllers/Admin/RoleController.php
Tambahkan import:
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

Tambahkan dua method:
/**
 * Halaman pengaturan permission role.
 */
public function permissions(Role $role): View
{
    $permissions = Permission::query()
        ->orderBy('module')
        ->orderBy('action')
        ->get()
        ->groupBy('module');


    $rolePermissionIds = $role
        ->permissions()
        ->pluck('permissions.id')
        ->toArray();


    return view('admin.roles.permissions', [
        'role' => $role,
        'permissions' => $permissions,
        'rolePermissionIds' => $rolePermissionIds,
    ]);
}



/**
 * Menyimpan permission role.
 */
public function updatePermissions(
    Request $request,
    Role $role
): RedirectResponse {


    $validated = $request->validate([
        'permissions' => [
            'nullable',
            'array',
        ],

        'permissions.*' => [
            'exists:permissions,id',
        ],
    ]);



    $role
        ->permissions()
        ->sync(
            $validated['permissions'] ?? []
        );


    return redirect()
        ->route(
            'admin.roles.show',
            $role
        )
        ->with(
            'success',
            'Permission role berhasil diperbarui.'
        );
}

Penjelasan penting
Bagian ini:
sync()
sangat penting.
Bedanya:
attach()
Tambah saja.
Contoh:
Role punya:
A,B

Tambah C

hasil:
A,B,C

sync()
Samakan dengan pilihan terbaru.
Contoh:
Database:
A,B,C
Admin memilih:
A,C,D
Maka hasil:
A,C,D
B otomatis dilepas.
Untuk pengaturan permission, sync() adalah pilihan yang benar.

4. Tambahkan Route
Buka:
routes/web.php
Tambahkan:
Route::get(
    '/roles/{role}/permissions',
    [RoleController::class,'permissions']
)
->middleware('permission:roles.permission.assign')
->name('roles.permissions');


Route::put(
    '/roles/{role}/permissions',
    [RoleController::class,'updatePermissions']
)
->middleware('permission:roles.permission.assign')
->name('roles.permissions.update');

Hasil route:
GET

/admin/roles/{role}/permissions


PUT

/admin/roles/{role}/permissions

5. Buat halaman assign permission
Buat file:
resources/views/admin/roles/permissions.blade.php
Isi:
<x-app-layout>


<x-slot name="header">

<div class="flex justify-between">

<div>

<h2 class="text-xl font-semibold">
Atur Permission Role
</h2>

<p class="text-sm text-gray-500">
{{ $role->display_name }}
</p>

</div>


<a
href="{{ route('admin.roles.show',$role) }}"
class="border px-4 py-2 rounded"
>
Kembali
</a>


</div>

</x-slot>



<div class="py-8">

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


<form method="POST"
action="{{ route('admin.roles.permissions.update',$role) }}">


@csrf

@method('PUT')



<div class="bg-white rounded shadow p-6">


<h3 class="font-semibold mb-5">
Daftar Permission
</h3>



<div class="space-y-6">


@foreach($permissions as $module=>$items)


<div class="border rounded p-4">


<h4 class="font-semibold text-gray-700 mb-3 uppercase">

{{ $module }}

</h4>



<div class="grid md:grid-cols-3 gap-3">


@foreach($items as $permission)


<label class="flex items-center gap-2">


<input
type="checkbox"
name="permissions[]"
value="{{ $permission->id }}"

@if(in_array(
$permission->id,
$rolePermissionIds
))

checked

@endif

>


<div>

<div class="font-mono text-sm">

{{ $permission->name }}

</div>


<div class="text-xs text-gray-500">

{{ $permission->display_name }}

</div>

</div>


</label>


@endforeach


</div>


</div>


@endforeach



</div>




<div class="mt-6">


<button
type="submit"
class="bg-green-700 text-white px-5 py-2 rounded"
>

Simpan Permission

</button>


</div>



</div>


</form>


</div>

</div>


</x-app-layout>

6. Tambahkan tombol di detail role
Buka:
resources/views/admin/roles/show.blade.php
Cari tombol:
<a
href="{{ route('admin.roles.index') }}"
Tambahkan sebelum tombol kembali:
@can(
'permission',
'roles.permission.assign'
)

<a
href="{{ route('admin.roles.permissions',$role) }}"
class="rounded-md bg-green-700 text-white px-4 py-2 text-sm"
>
Atur Permission
</a>

@endcan

7. Update RbacSeeder
Agar Super Admin dapat mengakses fitur ini.
Jalankan:
php artisan db:seed --class=RbacSeeder
Jika seeder Anda memakai reset:
php artisan migrate:fresh --seed
jangan dilakukan karena nanti data development hilang.

8. Buat Test
Jalankan:
php artisan make:test Rbac/RolePermissionManagementTest
Isi:
<?php

namespace Tests\Feature\Rbac;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{

use RefreshDatabase;


public function test_admin_can_assign_permission_to_role()
{


$user=User::factory()->create();


$role=Role::create([
'name'=>'bendahara',
'display_name'=>'Bendahara',
'is_active'=>true,
'is_system'=>false,
]);


$assignPermission=Permission::create([
'name'=>'roles.permission.assign',
'module'=>'roles',
'action'=>'permission_assign',
'display_name'=>'Assign Permission',
'is_active'=>true,
]);


$role->permissions()->attach(
$assignPermission
);


$user->roles()->attach($role);



$paymentPermission=Permission::create([
'name'=>'payments.view',
'module'=>'payments',
'action'=>'view',
'display_name'=>'Lihat Pembayaran',
'is_active'=>true,
]);



$response=$this
->actingAs($user)
->put(
route(
'admin.roles.permissions.update',
$role
),
[
'permissions'=>[
$paymentPermission->id
]
]
);



$response->assertRedirect();



$this->assertTrue(
$role
->fresh()
->permissions
->contains(
$paymentPermission
)
);


}


}

9. Jalankan pengecekan
Urutan:
php artisan optimize:clear
cek route:
php artisan route:list --path=permissions
Format:
./vendor/bin/pint
Test:
php artisan test
Build:
npm run build

Target Tahap 10.7 selesai
Target:
Tests:
46+ passed

Build:
success

RBAC:
100% selesai

Setelah tahap ini selesai, modul RBAC sudah matang:
Authentication
        ✓

Role
        ✓

Permission
        ✓

Middleware
        ✓

Gate
        ✓

Role Detail
        ✓

Permission Detail
        ✓

Assign Permission UI
        ✓
Kemudian kita masuk ke:
