<?php

namespace App\Support;

use App\Models\StudentEnrollment;
use App\Models\StudentMutation;
use Illuminate\Support\Facades\DB;

class MutasiKeluarService
{
    /**
     * Catat mutasi keluar + ubah status enrollment aktif tahun berjalan menjadi 'keluar'.
     * Transaksional: jika salah satu gagal, tidak ada perubahan parsial.
     */
    public static function create(array $data, int $academicYearId, ?int $userId = null): StudentMutation
    {
        return DB::transaction(function () use ($data, $academicYearId, $userId) {
            $mutation = StudentMutation::create($data + [
                'academic_year_id' => $academicYearId,
                'created_by' => $userId,
            ]);

            // Lepas dari rombel aktif tahun berjalan (status aktif -> keluar).
            // Update per-instance (bukan mass update) agar Eloquent observer terpicu.
            StudentEnrollment::query()
                ->where('student_id', $data['student_id'])
                ->where('academic_year_id', $academicYearId)
                ->where('status', 'aktif')
                ->get()
                ->each(fn (StudentEnrollment $e) => $e->update(['status' => 'keluar']));

            activity('mutasi')
                ->performedOn($mutation)
                ->event('created')
                ->log('Mutasi keluar: '.$mutation->student->displayName().' → '.$mutation->sekolah_tujuan);

            return $mutation;
        });
    }

    /**
     * Batal/hapus mutasi keluar. Mengembalikan status enrollment aktif tahun berjalan
     * menjadi 'aktif' HANYA jika saat ini masih 'keluar' (tidak menyentuh bila sudah diubah lagi).
     */
    public static function undo(StudentMutation $mutation, ?int $activeYearId = null): void
    {
        $yearId = $activeYearId ?? $mutation->academic_year_id;

        DB::transaction(function () use ($mutation, $yearId) {
            // Kembalikan per-instance (bukan mass update) agar Eloquent observer terpicu.
            StudentEnrollment::query()
                ->where('student_id', $mutation->student_id)
                ->where('academic_year_id', $yearId)
                ->where('status', 'keluar')
                ->get()
                ->each(fn (StudentEnrollment $e) => $e->update(['status' => 'aktif']));

            activity('mutasi')
                ->performedOn($mutation)
                ->event('deleted')
                ->log('Mutasi keluar dibatalkan: '.$mutation->student->displayName());

            $mutation->delete();
        });
    }
}
