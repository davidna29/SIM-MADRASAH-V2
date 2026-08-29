<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\EmployeePositionHistory;
use App\Models\OrganizationalUnit;
use App\Models\Person;
use App\Models\Position;
use App\Models\User;
use App\Support\AccountProvisioning;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Employee::class);

        $employees = Employee::with(['person', 'position', 'organizationalUnit'])
            ->when(request('q'), fn ($q, $search) => $q->whereHas('person', function ($person) use ($search) {
                $person->where('name', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            })->orWhere('nip', 'like', "%{$search}%"))
            ->when(request('position_id'), fn ($q, $id) => $q->where('position_id', $id))
            ->when(request('employee_status'), fn ($q, $s) => $q->where('employee_status', $s))
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.pegawai.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Kepegawaian', 'href' => route('dashboard')],
                ['label' => 'Data Guru & Pegawai'],
            ],
            'employees' => $employees,
            'positionOptions' => Position::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function show(Employee $employee): View
    {
        $this->authorize('view', $employee);

        return view('pages.pegawai.show', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Kepegawaian', 'href' => route('dashboard')],
                ['label' => 'Data Guru & Pegawai', 'href' => route('pegawai.index')],
                ['label' => $employee->person->name],
            ],
            'employee' => $employee->load(['person', 'position', 'organizationalUnit', 'positionHistories.position']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Employee::class);

        return view('pages.pegawai.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Kepegawaian', 'href' => route('dashboard')],
                ['label' => 'Data Guru & Pegawai', 'href' => route('pegawai.index')],
                ['label' => 'Tambah Guru/Pegawai'],
            ],
            'editing' => false,
            'positions' => Position::orderBy('name')->get(),
            'units' => OrganizationalUnit::orderBy('name')->get(),
        ]);
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $this->authorize('create', Employee::class);

        $validated = $request->validated();

        $employee = DB::transaction(function () use ($validated) {
            $person = Person::create([
                'nik' => $validated['nik'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'religion' => $validated['religion'] ?? 'Islam',
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
            ]);

            $employee = Employee::create([
                'person_id' => $person->id,
                'organizational_unit_id' => $validated['organizational_unit_id'],
                'position_id' => $validated['position_id'],
                'nip' => $validated['nip'] ?: null,
                'employee_status' => $validated['employee_status'],
                'status' => $validated['status'],
                'tmt' => $validated['tmt'] ?? null,
            ]);

            // Riwayat jabatan awal — pengangkatan
            EmployeePositionHistory::create([
                'employee_id' => $employee->id,
                'position_id' => $validated['position_id'],
                'organizational_unit_id' => $validated['organizational_unit_id'],
                'started_at' => $validated['tmt'] ?? now()->toDateString(),
                'reason' => 'pengangkatan',
            ]);

            return $employee;
        });

        activity('kepegawaian')
            ->performedOn($employee)
            ->withProperties(['nama' => $employee->person->name])
            ->log('pegawai_baru');

        $warning = $this->provisionEmployeeAccount($employee);

        $redirect = redirect()->route('pegawai.show', $employee)
            ->with('status', 'Data pegawai berhasil disimpan dan disematkan ke papan.');

        return $warning ? $redirect->with('warning', $warning) : $redirect;
    }

    /**
     * Provisioning akun otomatis untuk pegawai baru berstatus 'aktif'.
     * Mengembalikan string peringatan bila akun tidak dibuat (bukan silent fail).
     */
    protected function provisionEmployeeAccount(Employee $employee): ?string
    {
        if ($employee->status !== 'aktif') {
            return null;
        }

        $account = AccountProvisioning::employeeAccountPayload($employee);

        if (! $account['ok']) {
            return $account['reason'];
        }

        $user = User::create($account['payload']);

        $employee->update([
            'user_id' => $user->id,
            'username_source' => $account['source'],
        ]);

        activity('account_provisioning')
            ->performedOn($employee)
            ->causedBy(auth()->user())
            ->log('Akun pegawai dibuat otomatis: '.$user->username);

        return null;
    }

    public function edit(Employee $employee): View
    {
        $this->authorize('update', $employee);

        return view('pages.pegawai.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Kepegawaian', 'href' => route('dashboard')],
                ['label' => 'Data Guru & Pegawai', 'href' => route('pegawai.index')],
                ['label' => 'Ubah '.$employee->person->name],
            ],
            'editing' => true,
            'employee' => $employee->load(['person', 'position', 'organizationalUnit']),
            'positions' => Position::orderBy('name')->get(),
            'units' => OrganizationalUnit::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $employee);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $employee) {
            $employee->person->update([
                'nik' => $validated['nik'],
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'religion' => $validated['religion'] ?? 'Islam',
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
            ]);

            $employee->update([
                'organizational_unit_id' => $validated['organizational_unit_id'],
                'position_id' => $validated['position_id'],
                'nip' => $validated['nip'] ?: null,
                'employee_status' => $validated['employee_status'],
                'status' => $validated['status'],
                'tmt' => $validated['tmt'] ?? null,
            ]);

            // Mutasi jabatan dicatat sebagai riwayat baru, tidak menimpa lama
            if ($employee->wasChanged('position_id')) {
                EmployeePositionHistory::create([
                    'employee_id' => $employee->id,
                    'position_id' => $validated['position_id'],
                    'organizational_unit_id' => $validated['organizational_unit_id'],
                    'started_at' => now()->toDateString(),
                    'reason' => 'mutasi',
                ]);
            }
        });

        activity('kepegawaian')
            ->performedOn($employee)
            ->withProperties(['nama' => $employee->person->name])
            ->log('pegawai_diubah');

        return redirect()->route('pegawai.show', $employee)->with('status', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->authorize('delete', $employee);

        if ($employee->user_id && $employee->user_id === Auth::id()) {
            return back()->withErrors(['delete' => 'Anda tidak dapat menghapus data pegawai Anda sendiri.']);
        }

        $nama = $employee->person->name;
        $employee->delete();

        activity('kepegawaian')
            ->withProperties(['nama' => $nama])
            ->log('pegawai_dihapus');

        return redirect()->route('pegawai.index')->with('status', 'Data pegawai dihapus (soft delete).');
    }
}
