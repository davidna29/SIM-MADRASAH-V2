<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\StudentResource;
use App\Models\Student;
use App\Support\Api\ApiAcademicYear;
use App\Support\Api\ApiLookup;
use App\Support\Api\ApiResponse;
use App\Support\Api\ApiScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Student::class);

        $year = ApiAcademicYear::active();
        $perPage = min(max((int) $request->integer('per_page', 20), 1), 100);
        $classGroup = $request->filled('kelas') ? ApiLookup::classGroup($request->string('kelas')->toString()) : null;

        $students = Student::query()
            ->with([
                'person:id,name,gender',
                'enrollments' => fn ($query) => $query
                    ->where('academic_year_id', $year->id)
                    ->where('status', 'aktif')
                    ->with('classGroup:id,name,grade_level'),
            ])
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $search = $request->string('q')->toString();
                $query->where('nis', 'like', "%{$search}%")
                    ->orWhereHas('person', fn ($person) => $person->where('name', 'like', "%{$search}%"));
            }))
            ->when($classGroup, fn ($query) => $query->whereHas('enrollments', fn ($enrollment) => $enrollment
                ->where('academic_year_id', $year->id)
                ->where('class_group_id', $classGroup->id)))
            ->whereDoesntHave('enrollments', fn ($enrollment) => $enrollment
                ->where('academic_year_id', $year->id)
                ->where('status', 'keluar'))
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::paginated(
            $students,
            $students->getCollection()->map(fn (Student $student) => StudentResource::make($student)->resolve($request))->all(),
            [
                'filters' => [
                    'q' => $request->input('q'),
                    'kelas' => $classGroup?->name,
                    'academic_year_id' => $year->id,
                ],
            ],
            ['Data pribadi sensitif (NIK, NISN, KK, alamat, kontak) tidak ditampilkan pada daftar.'],
        );
    }

    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $year = ApiAcademicYear::active();
        $student->load([
            'person',
            'enrollments' => fn ($query) => $query->where('academic_year_id', $year->id)->with('classGroup'),
            'guardians',
        ]);

        if (! $request->user()->tokenCan(ApiScope::AKADEMIK_WRITE)) {
            return ApiResponse::success(
                StudentResource::make($student)->resolve($request),
                warnings: ['Detail lengkap membutuhkan scope akademik:write.'],
            );
        }

        $person = $student->person;

        return ApiResponse::success([
            ...StudentResource::make($student)->resolve($request),
            'nisn' => $student->nisn,
            'birth_place' => $person?->birth_place,
            'birth_date' => $person?->birth_date?->toDateString(),
            'religion' => $person?->religion,
            'address' => $person?->address,
            'person' => $person ? [
                'nik' => $person->nik,
                'name' => $person->name,
                'gender' => $person->gender,
                'birth_place' => $person->birth_place,
                'birth_date' => $person->birth_date?->toDateString(),
                'phone' => $person->phone,
                'email' => $person->email,
                'address' => $person->address,
                'province' => $person->province,
                'city' => $person->city,
                'district' => $person->district,
                'village' => $person->village,
                'postal_code' => $person->postal_code,
            ] : null,
            'student_profile' => [
                'previous_school' => $student->previous_school,
                'entry_date' => $student->entry_date?->toDateString(),
                'kk_number' => $student->kk_number,
                'kk_head_name' => $student->kk_head_name,
                'parent_address' => $student->parent_address,
                'parent_province' => $student->parent_province,
                'parent_city' => $student->parent_city,
                'parent_district' => $student->parent_district,
                'parent_village' => $student->parent_village,
                'parent_postal_code' => $student->parent_postal_code,
            ],
            'guardians' => $student->guardians->map(fn ($guardian) => [
                'id' => $guardian->id,
                'name' => $guardian->name,
                'nik' => $guardian->nik,
                'status' => $guardian->status,
                'birth_place' => $guardian->birth_place,
                'birth_date' => $guardian->birth_date?->toDateString(),
                'education' => $guardian->education,
                'job' => $guardian->job,
                'phone' => $guardian->phone,
                'relation' => $guardian->pivot?->relation,
            ])->values(),
        ], warnings: ['Data identitas lengkap hanya tersedia dengan scope akademik:write.']);
    }
}
