<?php

namespace App\Http\Controllers\Akademik\UjianPpi;

use App\Http\Controllers\Controller;
use App\Models\PpiExamExaminer;
use App\Models\PpiExamGroup;
use App\Models\PpiExamParticipant;
use App\Models\PpiExamPeriod;
use App\Models\PpiExamRoom;
use App\Services\PpiExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersiapanController extends Controller
{
    public function __construct(protected PpiExamService $service) {}

    // ============ Ruang & Penguji ============

    public function ruang(PpiExamPeriod $periode): View
    {
        $this->authorize('manage', $periode);

        $assignedEmployeeIds = $periode->examiners()->pluck('employee_id');

        return view('pages.ujianppi.persiapan.ruang', [
            'periode' => $periode,
            'rooms' => $periode->rooms()->with('examiners.employee.person')->get(),
            'employees' => $this->service->employees(),
            'assignedEmployeeIds' => $assignedEmployeeIds,
            'editable' => $this->editable($periode),
        ]);
    }

    public function ruangStore(Request $request, PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
        ]);

        $periode->rooms()->create($validated);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_ruang_ditambah');

        return redirect()->route('ujianppi.persiapan.ruang', $periode)->with('status', 'Ruang ujian ditambahkan.');
    }

    public function ruangUpdate(Request $request, PpiExamPeriod $periode, PpiExamRoom $room): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'penguji_1' => ['nullable', 'exists:employees,id'],
            'penguji_2' => ['nullable', 'exists:employees,id'],
            'penguji_3' => ['nullable', 'exists:employees,id'],
        ]);

        $picked = collect([$validated['penguji_1'] ?? null, $validated['penguji_2'] ?? null, $validated['penguji_3'] ?? null])
            ->filter()
            ->values();

        if ($picked->unique()->count() !== $picked->count()) {
            return back()->withErrors(['penguji' => 'Seorang guru tidak boleh mengisi lebih dari satu slot penguji pada ruang yang sama.'])->withInput();
        }

        // 1 guru = 1 ruang per periode (kecuali guru yang sudah di ruang ini)
        $conflict = $periode->examiners()
            ->whereIn('employee_id', $picked)
            ->where('exam_room_id', '!=', $room->id)
            ->with(['room', 'employee.person'])
            ->get();

        if ($conflict->isNotEmpty()) {
            $names = $conflict->map(fn ($e) => $this->service->employeeName($e->employee)." (Ruang {$e->room?->nama})")->implode(', ');

            return back()->withErrors(['penguji' => "Guru berikut sudah menjadi penguji di ruang lain periode ini: {$names}."])->withInput();
        }

        $room->update(['nama' => $validated['nama']]);

        // Simpan ulang 3 slot penguji
        $room->examiners()->delete();
        foreach ([1 => $validated['penguji_1'] ?? null, 2 => $validated['penguji_2'] ?? null, 3 => $validated['penguji_3'] ?? null] as $urutan => $employeeId) {
            if ($employeeId) {
                $room->examiners()->create([
                    'exam_period_id' => $periode->id,
                    'employee_id' => $employeeId,
                    'urutan' => $urutan,
                ]);
            }
        }

        activity('akademik')->performedOn($periode)->log('ujian_ppi_ruang_diubah');

        return redirect()->route('ujianppi.persiapan.ruang', $periode)->with('status', 'Ruang & penguji disimpan.');
    }

    public function ruangDestroy(PpiExamPeriod $periode, PpiExamRoom $room): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        if ($room->participants()->exists()) {
            return back()->withErrors(['ruang' => 'Ruang masih memiliki peserta; pindahkan/lepas peserta terlebih dahulu.']);
        }

        $room->delete();

        activity('akademik')->performedOn($periode)->log('ujian_ppi_ruang_dihapus');

        return redirect()->route('ujianppi.persiapan.ruang', $periode)->with('status', 'Ruang ujian dihapus.');
    }

    // ============ Grup Setoran & Pembimbing ============

    public function grup(PpiExamPeriod $periode): View
    {
        $this->authorize('manage', $periode);

        return view('pages.ujianppi.persiapan.grup', [
            'periode' => $periode,
            'groups' => $periode->groups()->with('pembimbing.person', 'participants')->get(),
            'employees' => $this->service->employees(),
            'editable' => $this->editable($periode),
        ]);
    }

    public function grupStore(Request $request, PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'pembimbing_employee_id' => ['required', 'exists:employees,id'],
        ]);

        $periode->groups()->create($validated);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_grup_ditambah');

        return redirect()->route('ujianppi.persiapan.grup', $periode)->with('status', 'Grup setoran ditambahkan.');
    }

    public function grupUpdate(Request $request, PpiExamPeriod $periode, PpiExamGroup $group): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'pembimbing_employee_id' => ['required', 'exists:employees,id'],
        ]);

        $group->update($validated);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_grup_diubah');

        return redirect()->route('ujianppi.persiapan.grup', $periode)->with('status', 'Grup setoran diperbarui.');
    }

    public function grupDestroy(PpiExamPeriod $periode, PpiExamGroup $group): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        if ($group->participants()->exists()) {
            return back()->withErrors(['grup' => 'Grup masih memiliki peserta; pindahkan/lepas peserta terlebih dahulu.']);
        }

        $group->delete();

        activity('akademik')->performedOn($periode)->log('ujian_ppi_grup_dihapus');

        return redirect()->route('ujianppi.persiapan.grup', $periode)->with('status', 'Grup setoran dihapus.');
    }

    // ============ Peserta ============

    public function peserta(PpiExamPeriod $periode): View
    {
        $this->authorize('manage', $periode);

        $year = $periode->academicYear;
        $assignedIds = $periode->participants()->pluck('student_id');

        $available = $this->service->studentsKelasVI($year)
            ->reject(fn ($s) => $assignedIds->contains($s->id))
            ->values();

        return view('pages.ujianppi.persiapan.peserta', [
            'periode' => $periode,
            'available' => $available,
            'assigned' => $periode->participants()->with(['student', 'room', 'group'])->orderBy('no_urut')->get(),
            'rooms' => $periode->rooms()->get(),
            'groups' => $periode->groups()->get(),
            'editable' => $this->editable($periode),
        ]);
    }

    public function pesertaAssign(Request $request, PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer'],
            'exam_room_id' => ['required', 'exists:ppi_exam_rooms,id'],
            'group_id' => ['nullable', 'exists:ppi_exam_groups,id'],
        ]);

        $room = PpiExamRoom::where('id', $validated['exam_room_id'])->where('exam_period_id', $periode->id)->firstOrFail();
        $group = null;
        if (! empty($validated['group_id'])) {
            $group = PpiExamGroup::where('id', $validated['group_id'])->where('exam_period_id', $periode->id)->firstOrFail();
        }

        $assignedIds = $periode->participants()->pluck('student_id');
        $maxNoUrut = $periode->participants()->max('no_urut') ?? 0;

        $students = $this->service->studentsKelasVI($periode->academicYear)
            ->whereIn('id', $validated['student_ids'])
            ->reject(fn ($s) => $assignedIds->contains($s->id))
            ->sortBy('name');

        if ($students->isEmpty()) {
            return back()->withErrors(['student_ids' => 'Tidak ada siswa valid yang dapat di-assign (mungkin sudah ter-assign).']);
        }

        $count = $students->count();
        foreach ($students->values() as $i => $student) {
            $enrollment = $student->enrollments->first();
            PpiExamParticipant::create([
                'exam_period_id' => $periode->id,
                'student_id' => $student->id,
                'exam_room_id' => $room->id,
                'group_id' => $group?->id,
                'class_group_id' => $enrollment?->class_group_id,
                'no_urut' => $maxNoUrut + $i + 1,
                'status' => 'aktif',
            ]);
        }

        activity('akademik')
            ->performedOn($periode)
            ->withProperties(['jumlah' => $count, 'ruang' => $room->nama])
            ->log('ujian_ppi_peserta_assign');

        return redirect()->route('ujianppi.persiapan.peserta', $periode)
            ->with('status', "{$count} siswa di-assign ke Ruang {$room->nama}.");
    }

    public function pesertaUpdate(Request $request, PpiExamPeriod $periode, PpiExamParticipant $peserta): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $validated = $request->validate([
            'no_urut' => ['required', 'integer', 'min:1'],
            'exam_room_id' => ['required', 'exists:ppi_exam_rooms,id'],
            'group_id' => ['nullable', 'exists:ppi_exam_groups,id'],
        ]);

        $duplicate = $periode->participants()
            ->where('id', '!=', $peserta->id)
            ->where('no_urut', $validated['no_urut'])
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['no_urut' => "Nomor urut {$validated['no_urut']} sudah dipakai peserta lain."]);
        }

        $peserta->update($validated);

        activity('akademik')->performedOn($periode)->log('ujian_ppi_peserta_diubah');

        return redirect()->route('ujianppi.persiapan.peserta', $periode)->with('status', 'Data peserta diperbarui.');
    }

    public function pesertaDestroy(PpiExamPeriod $periode, PpiExamParticipant $peserta): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $nama = $peserta->student?->name ?? 'Siswa';
        $peserta->delete();

        activity('akademik')->performedOn($periode)->withProperties(['nama' => $nama])->log('ujian_ppi_peserta_dilepas');

        return redirect()->route('ujianppi.persiapan.peserta', $periode)->with('status', "{$nama} dilepas dari periode ini.");
    }

    // ============ Salin dari Periode Sebelumnya ============

    public function copyRooms(PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $source = PpiExamPeriod::where('id', '!=', $periode->id)
            ->whereHas('rooms')
            ->orderByDesc('id')
            ->first();

        if (! $source) {
            return back()->withErrors(['rooms' => 'Tidak ada periode sebelumnya dengan data ruang.']);
        }

        $copied = 0;

        foreach ($source->rooms as $sourceRoom) {
            $room = PpiExamRoom::firstOrCreate(
                ['exam_period_id' => $periode->id, 'nama' => $sourceRoom->nama]
            );

            foreach ($sourceRoom->examiners()->with('employee')->get() as $sourceExaminer) {
                PpiExamExaminer::updateOrCreate(
                    ['exam_room_id' => $room->id, 'urutan' => $sourceExaminer->urutan],
                    ['exam_period_id' => $periode->id, 'employee_id' => $sourceExaminer->employee_id]
                );
            }

            $copied++;
        }

        activity('akademik')
            ->performedOn($periode)
            ->withProperties(['jumlah' => $copied, 'sumber' => $source->judul])
            ->log('ujian_ppi_ruang_disalin');

        return redirect()->route('ujianppi.persiapan.ruang', $periode)
            ->with('status', "Ruang & penguji disalin dari \"{$source->judul}\" ({$copied} ruang).");
    }

    public function copyGroups(PpiExamPeriod $periode): RedirectResponse
    {
        $this->authorize('manage', $periode);
        $this->service->assertConfigEditable($periode);

        $source = PpiExamPeriod::where('id', '!=', $periode->id)
            ->whereHas('groups')
            ->orderByDesc('id')
            ->first();

        if (! $source) {
            return back()->withErrors(['groups' => 'Tidak ada periode sebelumnya dengan data grup.']);
        }

        $copied = 0;

        foreach ($source->groups as $sourceGroup) {
            PpiExamGroup::firstOrCreate(
                ['exam_period_id' => $periode->id, 'nama' => $sourceGroup->nama],
                ['pembimbing_employee_id' => $sourceGroup->pembimbing_employee_id]
            );
            $copied++;
        }

        activity('akademik')
            ->performedOn($periode)
            ->withProperties(['jumlah' => $copied, 'sumber' => $source->judul])
            ->log('ujian_ppi_grup_disalin');

        return redirect()->route('ujianppi.persiapan.grup', $periode)
            ->with('status', "Grup & pembimbing disalin dari \"{$source->judul}\" ({$copied} grup).");
    }

    protected function editable(PpiExamPeriod $periode): bool
    {
        try {
            $this->service->assertConfigEditable($periode);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
