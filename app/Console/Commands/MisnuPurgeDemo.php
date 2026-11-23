<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Support\Misnu\MisnuReference;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MisnuPurgeDemo extends Command
{
    protected $signature = 'misnu:purge-demo
        {--students : Hapus seluruh data siswa demo beserta transaksi turunannya}
        {--employees : Nonaktifkan (soft delete) pegawai demo yang tidak ada di data MIS NU}
        {--force : Lewati konfirmasi interaktif}
        {--production : Izinkan eksekusi di production (wajib bersama --force)}';

    protected $description = 'Membersihkan data demo lokal sebelum onboarding data MIS NU (khusus environment local/testing).';

    public function handle(): int
    {
        if (! app()->environment(['local', 'testing'])) {
            if (! $this->option('production') || ! $this->option('force')) {
                $this->error('Di production, jalankan dengan --production --force setelah backup database.');

                return self::FAILURE;
            }

            $this->warn('PERHATIAN: pembersihan data dijalankan di PRODUCTION.');
        }

        $students = (bool) $this->option('students');
        $employees = (bool) $this->option('employees');

        if (! $students && ! $employees) {
            $students = $employees = true;
        }

        if (! $this->option('force') && ! $this->confirm('Hapus data demo terpilih? Tindakan ini tidak dapat dibatalkan.', false)) {
            $this->warn('Dibatalkan.');

            return self::SUCCESS;
        }

        $studentCount = 0;
        $guardianCount = 0;
        $employeeCount = 0;

        DB::transaction(function () use ($students, $employees, &$studentCount, &$guardianCount, &$employeeCount) {
            if ($students) {
                $studentIds = DB::table('students')->pluck('id')->all();
                $studentCount = count($studentIds);

                if ($studentIds !== []) {
                    // RESTRICT pada peserta ujian PPI — bersihkan lebih dulu.
                    DB::table('ppi_exam_participants')->whereIn('student_id', $studentIds)->delete();
                    DB::table('students')->whereIn('id', $studentIds)->delete();
                }

                $guardianCount = DB::table('guardians')
                    ->whereNotExists(fn ($query) => $query->select(DB::raw(1))
                        ->from('guardian_student')
                        ->whereColumn('guardian_student.guardian_id', 'guardians.id'))
                    ->delete();
            }

            if ($employees) {
                $keep = collect(MisnuReference::employees())->pluck('name')->all();

                $demo = Employee::with('person')->get()
                    ->filter(fn (Employee $employee) => ! in_array($employee->person?->name, $keep, true));

                foreach ($demo as $employee) {
                    $employee->delete();
                    $employeeCount++;
                }
            }

            // Person yatim (tanpa student/employee) ikut dibersihkan agar NIK bisa dipakai ulang.
            DB::table('people')
                ->whereNotExists(fn ($query) => $query->select(DB::raw(1))->from('students')->whereColumn('students.person_id', 'people.id'))
                ->whereNotExists(fn ($query) => $query->select(DB::raw(1))->from('employees')->whereColumn('employees.person_id', 'people.id'))
                ->delete();
        });

        $this->table(['Data', 'Jumlah'], [
            ['Siswa dihapus', $studentCount],
            ['Wali yatim dihapus', $guardianCount],
            ['Pegawai demo dinonaktifkan', $employeeCount],
        ]);

        $this->info('Pembersihan selesai.');

        return self::SUCCESS;
    }
}
