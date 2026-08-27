<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeAttendanceRequest;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\OrganizationalUnit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeAttendanceController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', EmployeeAttendance::class);

        $date = request('date') ? Carbon::parse(request('date')) : Carbon::today();

        $employees = $this->activeEmployees();

        $attendances = EmployeeAttendance::where('attendance_date', $date->toDateString())
            ->get()
            ->keyBy('employee_id');

        return view('pages.kepegawaian.kehadiran.index', [
            'roleLabel' => 'Kepegawaian',
            'breadcrumb' => [
                ['label' => 'Kepegawaian', 'href' => route('dashboard')],
                ['label' => 'Kehadiran Guru & Pegawai'],
            ],
            'date' => $date,
            'employees' => $employees,
            'attendances' => $attendances,
            'units' => OrganizationalUnit::orderBy('name')->get(),
            'statuses' => EmployeeAttendance::STATUSES,
        ]);
    }

    public function store(StoreEmployeeAttendanceRequest $request): RedirectResponse
    {
        $this->authorize('create', EmployeeAttendance::class);

        $validated = $request->validated();

        foreach ($validated['attendances'] as $employeeId => $data) {
            if (! Employee::find($employeeId)) {
                continue;
            }

            EmployeeAttendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'attendance_date' => $validated['attendance_date'],
                ],
                [
                    'status' => $data['status'] ?? 'hadir',
                    'clock_in' => $data['clock_in'] ?? null,
                    'clock_out' => $data['clock_out'] ?? null,
                    'note' => $data['note'] ?? null,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        activity('kepegawaian')->log('kehadiran_pegawai_diinput');

        return back()->with('status', 'Kehadiran guru & pegawai berhasil disimpan.');
    }

    public function rekapBulanan(): View
    {
        $this->authorize('viewAny', EmployeeAttendance::class);

        $month = request('month') ? Carbon::parse(request('month').'-01') : Carbon::now()->startOfMonth();
        $daysInMonth = $month->daysInMonth;
        $start = $month->copy()->startOfMonth()->toDateString();
        $end = $month->copy()->endOfMonth()->toDateString();

        $employees = $this->activeEmployees();

        $records = EmployeeAttendance::whereBetween('attendance_date', [$start, $end])
            ->get()
            ->groupBy(fn ($a) => $a->employee_id.'|'.$a->attendance_date->format('Y-m-d'));

        $rows = $employees->map(function (Employee $employee) use ($records, $month, $daysInMonth) {
            $cells = [];
            $tally = array_fill_keys(array_keys(EmployeeAttendance::STATUSES), 0);

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = $month->copy()->day($d)->format('Y-m-d');
                $record = $records->get($employee->id.'|'.$dateStr)?->first();

                $cells[$d] = $record ? EmployeeAttendance::markFor($record->status) : null;
                if ($record) {
                    $tally[$record->status]++;
                }
            }

            $hariTercatat = array_sum($tally);

            return [
                'employee' => $employee,
                'cells' => $cells,
                'tally' => $tally,
                'hari_tercatat' => $hariTercatat,
                'jumlah_ketidakhadiran' => $hariTercatat - $tally['hadir'],
                'persentase_kehadiran' => $hariTercatat > 0
                    ? round($tally['hadir'] / $hariTercatat * 100, 1)
                    : null,
            ];
        });

        $summary = [
            'tally' => array_fill_keys(array_keys(EmployeeAttendance::STATUSES), 0),
            'hari_tercatat' => 0,
            'ketidakhadiran' => 0,
            'persentase_kehadiran' => null,
        ];
        foreach ($rows as $row) {
            foreach (array_keys(EmployeeAttendance::STATUSES) as $status) {
                $summary['tally'][$status] += $row['tally'][$status];
            }
            $summary['hari_tercatat'] += $row['hari_tercatat'];
            $summary['ketidakhadiran'] += $row['jumlah_ketidakhadiran'];
        }
        $summary['persentase_kehadiran'] = $summary['hari_tercatat'] > 0
            ? round($summary['tally']['hadir'] / $summary['hari_tercatat'] * 100, 1)
            : null;

        return view('pages.kepegawaian.kehadiran.rekap-bulanan', [
            'roleLabel' => 'Kepegawaian',
            'breadcrumb' => [
                ['label' => 'Kepegawaian', 'href' => route('dashboard')],
                ['label' => 'Kehadiran Guru & Pegawai', 'href' => route('pegawai.kehadiran.index')],
                ['label' => 'Rekap Bulanan'],
            ],
            'month' => $month,
            'daysInMonth' => $daysInMonth,
            'employees' => $employees,
            'rows' => $rows,
            'summary' => $summary,
            'units' => OrganizationalUnit::orderBy('name')->get(),
        ]);
    }

    public function rekapTahunan(): View
    {
        $this->authorize('viewAny', EmployeeAttendance::class);

        $year = (int) (request('year') ?: Carbon::now()->year);
        $start = $year.'-01-01';
        $end = $year.'-12-31';

        $employees = $this->activeEmployees();

        $records = EmployeeAttendance::whereBetween('attendance_date', [$start, $end])->get();

        $rows = $employees->map(function (Employee $employee) use ($records) {
            $months = [];
            $totalHadir = 0;
            $totalTercatat = 0;

            for ($m = 1; $m <= 12; $m++) {
                $monthRecords = $records->filter(
                    fn ($r) => $r->employee_id === $employee->id && (int) $r->attendance_date->format('n') === $m
                );
                $hadir = $monthRecords->where('status', 'hadir')->count();

                $months[$m] = ['hadir' => $hadir, 'tercatat' => $monthRecords->count()];
                $totalHadir += $hadir;
                $totalTercatat += $monthRecords->count();
            }

            return [
                'employee' => $employee,
                'months' => $months,
                'total_hadir' => $totalHadir,
                'total_tercatat' => $totalTercatat,
                'ketidakhadiran' => $totalTercatat - $totalHadir,
                'persentase_kehadiran' => $totalTercatat > 0
                    ? round($totalHadir / $totalTercatat * 100, 1)
                    : null,
            ];
        });

        $summary = [
            'total_hadir' => 0,
            'hari_tercatat' => 0,
            'ketidakhadiran' => 0,
            'persentase_kehadiran' => null,
        ];
        foreach ($rows as $row) {
            $summary['total_hadir'] += $row['total_hadir'];
            $summary['hari_tercatat'] += $row['total_tercatat'];
            $summary['ketidakhadiran'] += $row['ketidakhadiran'];
        }
        $summary['persentase_kehadiran'] = $summary['hari_tercatat'] > 0
            ? round($summary['total_hadir'] / $summary['hari_tercatat'] * 100, 1)
            : null;

        return view('pages.kepegawaian.kehadiran.rekap-tahunan', [
            'roleLabel' => 'Kepegawaian',
            'breadcrumb' => [
                ['label' => 'Kepegawaian', 'href' => route('dashboard')],
                ['label' => 'Kehadiran Guru & Pegawai', 'href' => route('pegawai.kehadiran.index')],
                ['label' => 'Rekap Tahunan'],
            ],
            'year' => $year,
            'employees' => $employees,
            'rows' => $rows,
            'summary' => $summary,
            'units' => OrganizationalUnit::orderBy('name')->get(),
        ]);
    }

    /** Pegawai aktif untuk input harian & rekap (mirror enrollment aktif siswa). */
    protected function activeEmployees(): Collection
    {
        return Employee::with(['person', 'position', 'organizationalUnit'])
            ->where('status', 'aktif')
            ->when(request('unit_id'), fn ($q, $id) => $q->where('organizational_unit_id', $id))
            ->when(request('q'), fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->whereHas('person', fn ($p) => $p->where('name', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%"))
                    ->orWhere('nip', 'like', "%{$search}%");
            }))
            ->orderBy('id')
            ->get();
    }
}
