<?php

namespace App\Http\Controllers\Fondasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    private const AVAILABLE_ROLES = [
        'super_admin' => 'Super Admin',
        'kepala_madrasah' => 'Kepala Madrasah',
        'wakamad_kurikulum' => 'Wakamad Kurikulum',
        'wakamad_kesiswaan' => 'Wakamad Kesiswaan',
        'wakamad_humas' => 'Wakamad Humas',
        'wakamad_sarpras' => 'Wakamad Sarpras',
        'bendahara' => 'Bendahara',
        'tata_usaha' => 'Tata Usaha',
        'guru' => 'Guru',
        'guru_bk' => 'Guru BK',
        'editor_berita' => 'Editor Berita',
        'orang_tua' => 'Orang Tua',
        'siswa' => 'Siswa',
    ];

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('userRoles')
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->when(request('role'), fn ($q, $role) => $q->where('role', $role))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pages.fondasi.pengguna.index', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Fondasi & Pengaturan', 'href' => route('dashboard')],
                ['label' => 'Pengguna & Role'],
            ],
            'users' => $users,
            'roleOptions' => self::AVAILABLE_ROLES,
        ]);
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        return view('pages.fondasi.pengguna.show', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Fondasi & Pengaturan', 'href' => route('dashboard')],
                ['label' => 'Pengguna & Role', 'href' => route('pengguna.index')],
                ['label' => $user->name],
            ],
            'user' => $user->load('userRoles'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('pages.fondasi.pengguna.create', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Fondasi & Pengaturan', 'href' => route('dashboard')],
                ['label' => 'Pengguna & Role', 'href' => route('pengguna.index')],
                ['label' => 'Tambah Pengguna'],
            ],
            'editing' => false,
            'roleOptions' => self::AVAILABLE_ROLES,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'student_id' => $validated['student_id'] ?? null,
            ]);

            if (! empty($validated['additional_roles'])) {
                foreach ($validated['additional_roles'] as $additionalRole) {
                    if ($additionalRole !== $validated['role']) {
                        UserRole::create([
                            'user_id' => $user->id,
                            'role' => $additionalRole,
                        ]);
                    }
                }
            }

            return $user;
        });

        activity('fondasi')
            ->performedOn($user)
            ->withProperties(['nama' => $user->name])
            ->log('pengguna_baru');

        return redirect()->route('pengguna.show', $user)->with('status', 'Pengguna berhasil ditambahkan dan disematkan ke papan.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('pages.fondasi.pengguna.edit', [
            'roleLabel' => 'Super Admin',
            'breadcrumb' => [
                ['label' => 'Fondasi & Pengaturan', 'href' => route('dashboard')],
                ['label' => 'Pengguna & Role', 'href' => route('pengguna.index')],
                ['label' => 'Ubah '.$user->name],
            ],
            'editing' => true,
            'user' => $user->load('userRoles'),
            'roleOptions' => self::AVAILABLE_ROLES,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $user) {
            $data = [
                'name' => $validated['name'],
                'username' => $validated['username'] ?? null,
                'email' => $validated['email'],
                'role' => $validated['role'],
                'student_id' => $validated['student_id'] ?? null,
            ];

            if (! empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            $user->userRoles()->delete();

            if (! empty($validated['additional_roles'])) {
                foreach ($validated['additional_roles'] as $additionalRole) {
                    if ($additionalRole !== $validated['role']) {
                        UserRole::create([
                            'user_id' => $user->id,
                            'role' => $additionalRole,
                        ]);
                    }
                }
            }
        });

        activity('fondasi')
            ->performedOn($user)
            ->withProperties(['nama' => $user->name])
            ->log('pengguna_diubah');

        return redirect()->route('pengguna.show', $user)->with('status', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $nama = $user->name;
        $user->userRoles()->delete();
        $user->delete();

        activity('fondasi')
            ->withProperties(['nama' => $nama])
            ->log('pengguna_dihapus');

        return redirect()->route('pengguna.index')->with('status', 'Pengguna dihapus.');
    }
}
