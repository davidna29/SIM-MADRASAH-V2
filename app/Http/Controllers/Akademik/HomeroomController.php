<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassGroup;
use App\Models\HomeroomAssignment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class HomeroomController extends Controller
{
    public function store(ClassGroup $classGroup): RedirectResponse
    {
        $this->authorize('update', $classGroup);

        $validated = request()->validate([
            'user_id' => ['required', 'exists:users,id'],
        ], [
            'user_id.exists' => 'Guru yang dipilih tidak valid.',
        ]);

        $tahun = AcademicYear::active();

        $existing = HomeroomAssignment::where('class_group_id', $classGroup->id)
            ->where('academic_year_id', $tahun->id)
            ->where('status', 'aktif')
            ->first();

        if ($existing) {
            $existing->update(['status' => 'selesai']);
        }

        $homeroom = HomeroomAssignment::create([
            'class_group_id' => $classGroup->id,
            'academic_year_id' => $tahun->id,
            'user_id' => $validated['user_id'],
            'status' => 'aktif',
            'created_by' => auth()->id(),
        ]);

        $teacherName = User::find($validated['user_id'])->name;

        activity('akademik')
            ->performedOn($classGroup)
            ->withProperties(['wali_kelas' => $teacherName])
            ->log('wali_kelas_ditugaskan');

        return back()->with('status', "Wali kelas {$teacherName} berhasil ditugaskan ke Kelas {$classGroup->name}.");
    }

    public function destroy(ClassGroup $classGroup, HomeroomAssignment $homeroom): RedirectResponse
    {
        $this->authorize('update', $classGroup);

        $teacherName = $homeroom->teacher->name;
        $homeroom->update(['status' => 'selesai']);

        activity('akademik')
            ->performedOn($classGroup)
            ->withProperties(['wali_kelas' => $teacherName])
            ->log('wali_kelas_dilepas');

        return back()->with('status', "Wali kelas {$teacherName} berhasil dilepas dari Kelas {$classGroup->name}.");
    }
}
