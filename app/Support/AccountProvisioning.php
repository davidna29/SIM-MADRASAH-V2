<?php

namespace App\Support;

use App\Models\Employee;
use App\Models\Position;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;

class AccountProvisioning
{
    /**
     * Username siswa: NISN bila ada, fallback NIS. Null bila keduanya kosong (data tidak lengkap).
     */
    public static function usernameForStudent(Student $student): ?string
    {
        $username = $student->nisn ?: $student->nis;

        return blank($username) ? null : (string) $username;
    }

    /**
     * Username pegawai: NIP (default) atau NIK dari Person (bila username_source = 'nik').
     * Catatan: kolom NIK tidak ada di tabel employees - NIK tinggal di people.nik (person).
     */
    public static function usernameForEmployee(Employee $employee): ?string
    {
        $username = $employee->username_source === 'nik' ? $employee->person?->nik : $employee->nip;

        return blank($username) ? null : (string) $username;
    }

    /**
     * Tentukan sumber username pegawai: NIP bila ada, kalau tidak NIK (people.nik).
     */
    public static function employeeUsernameSource(Employee $employee): string
    {
        return blank($employee->nip) ? 'nik' : 'nip';
    }

    /**
     * Resolusi username dengan strategi collision: coba base dulu, kalau bentrok tambah prefix
     * jenis akun (s- siswa / p- pegawai) HANYA saat terjadi collision. Kasus collision dicatat
     * ke activity log (tanpa password) untuk direview admin. Null bila tetap bentrok.
     */
    public static function uniqueUsername(string $base, string $type): ?string
    {
        $base = trim((string) $base);

        if (blank($base)) {
            return null;
        }

        if (! User::where('username', $base)->exists()) {
            return $base;
        }

        $prefixed = $type.'-'.$base;

        if (! User::where('username', $prefixed)->exists()) {
            activity('account_provisioning')
                ->withProperties(['collision' => $base, 'resolved' => $prefixed])
                ->log("Collision username '".$base."' saat provisioning - fallback '".$prefixed."'. Kemungkinan data ganda, periksa manual.");

            return $prefixed;
        }

        activity('account_provisioning')
            ->withProperties(['collision' => $base])
            ->log("Collision username '".$base."' tidak dapat diselesaikan - periksa data ganda secara manual.");

        return null;
    }

    /**
     * Email unik: pakai email person bila ada & bebas; fallback username@akun.madrasah.local.
     */
    public static function uniqueEmail(string $username, ?string $preferred = null): string
    {
        $candidates = array_values(array_filter([$preferred, $username.'@akun.madrasah.local']));

        foreach ($candidates as $candidate) {
            if (! User::where('email', $candidate)->exists()) {
                return $candidate;
            }
        }

        $i = 1;

        while ($i < 50) {
            $candidate = $username.'.'.$i.'@akun.madrasah.local';

            if (! User::where('email', $candidate)->exists()) {
                return $candidate;
            }

            $i++;
        }

        return $username.'.'.uniqid().'@akun.madrasah.local';
    }

    /**
     * Password default: tanggal lahir ddmmyyyy (8 digit). Null bila tanggal lahir kosong.
     */
    public static function defaultPassword(?Carbon $birthDate): ?string
    {
        return $birthDate?->format('dmY');
    }

    /**
     * Role user dari jabatan (posisi) pegawai. Posisi unmapped -> 'guru' (mayoritas pegawai madrasah).
     */
    public static function roleForEmployee(?Position $position): string
    {
        return match ($position?->code) {
            'GURU_MAPEL' => 'guru',
            'GURU_BK' => 'guru_bk',
            'KEPALA_MADRASAH' => 'kepala_madrasah',
            'WAKAMAD_KURIKULUM' => 'wakamad_kurikulum',
            'WAKAMAD_KESISWAAN' => 'wakamad_kesiswaan',
            'WAKAMAD_SARPRAS' => 'wakamad_sarpras',
            'WAKAMAD_HUMAS' => 'wakamad_humas',
            'BENDAHARA' => 'bendahara',
            'TATA_USAHA' => 'tata_usaha',
            'PUSTAKAWAN' => 'pustakawan',
            'SATPAM' => 'tata_usaha',
            'OPERATOR' => 'tata_usaha',
            'LABORAN' => 'tata_usaha',
            default => 'guru',
        };
    }

    /**
     * Payload akun siswa (untuk User::updateOrCreate oleh controller aktivasi)
     * - tidak berisi logika insert agar controller tetap memegang kendali batch.
     */
    public static function studentAccountPayload(Student $student): array
    {
        $username = static::usernameForStudent($student);

        if ($username === null) {
            return ['ok' => false, 'reason' => 'NISN dan NIS kosong (data tidak lengkap).'];
        }

        $resolved = static::uniqueUsername($username, 's');

        if ($resolved === null) {
            return ['ok' => false, 'reason' => 'Username "'.$username.'" bentrok dan tidak dapat diselesaikan otomatis.'];
        }

        $birthDate = $student->person?->birth_date;
        $password = static::defaultPassword($birthDate);

        if ($password === null) {
            return ['ok' => false, 'reason' => 'Tanggal lahir siswa kosong - password default tidak dapat dibuat.'];
        }

        return [
            'ok' => true,
            'payload' => [
                'name' => $student->displayName(),
                'username' => $resolved,
                'email' => static::uniqueEmail($resolved, $student->person?->email),
                'password' => $password,
                'role' => 'siswa',
                'student_id' => $student->id,
                'must_change_password' => true,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Siapkan akun pegawai otomatis (dipanggil EmployeeController::store saat status aktif).
     * Kembalian: ['ok' => true, 'payload' => [...]] atau ['ok' => false, 'reason' => '...'].
     */
    public static function employeeAccountPayload(Employee $employee): array
    {
        if (blank($employee->nip) && blank($employee->person?->nik)) {
            return ['ok' => false, 'reason' => 'NIP dan NIK kosong - akun belum bisa dibuat sampai salah satu diisi.'];
        }

        $source = static::employeeUsernameSource($employee);
        $base = $source === 'nik' ? $employee->person?->nik : $employee->nip;
        $resolved = static::uniqueUsername((string) $base, 'p');

        if ($resolved === null) {
            return ['ok' => false, 'reason' => 'Username "'.$base.'" bentrok dan tidak dapat diselesaikan otomatis.'];
        }

        $password = static::defaultPassword($employee->person?->birth_date);

        if ($password === null) {
            return ['ok' => false, 'reason' => 'Tanggal lahir pegawai kosong - password default tidak dapat dibuat.'];
        }

        return [
            'ok' => true,
            'source' => $source,
            'payload' => [
                'name' => $employee->person->name,
                'username' => $resolved,
                'email' => static::uniqueEmail($resolved, $employee->person?->email),
                'password' => $password,
                'role' => static::roleForEmployee($employee->position),
                'must_change_password' => true,
                'is_active' => true,
            ],
        ];
    }
}
