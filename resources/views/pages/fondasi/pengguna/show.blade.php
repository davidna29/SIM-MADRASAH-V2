<x-layouts.page
    :title="$user->name"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pengguna.index">

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $user->name }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Detail akun pengguna — informasi identitas dan role yang dimiliki.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="pencil-square" href="{{ route('pengguna.edit', $user) }}">Ubah</x-ui.button>
                @can('delete', $user)
                    <form method="POST" action="{{ route('pengguna.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="danger" icon="trash">Hapus</x-ui.button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="mt-6 space-y-4">
            <!-- Info Dasar -->
            <x-ui.sheet>
                <x-ui.form-section title="Informasi Akun" description="Data dasar akun pengguna.">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm font-medium text-ink">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Username</dt>
                            <dd class="mt-1 font-mono text-sm font-semibold text-ink-faint">{{ $user->username ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Email</dt>
                            <dd class="mt-1 text-sm text-ink">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Role Utama</dt>
                            <dd class="mt-1">
                                @php
                                    $roleBadge = match($user->role) {
                                        'super_admin' => ['Super Admin', 'danger'],
                                        'kepala_madrasah' => ['Kepala Madrasah', 'info'],
                                        'guru' => ['Guru', 'primary'],
                                        'siswa' => ['Siswa', 'success'],
                                        'orang_tua' => ['Orang Tua', 'warning'],
                                        default => [ucfirst(str_replace('_', ' ', $user->role)), 'neutral'],
                                    };
                                @endphp
                                <x-ui.badge :variant="$roleBadge[1]">{{ $roleBadge[0] }}</x-ui.badge>
                            </dd>
                        </div>
                        @if ($user->student)
                            <div>
                                <dt class="text-xs font-semibold text-ink-soft">Terkait Siswa</dt>
                                <dd class="mt-1 text-sm text-ink">{{ $user->student->person->name ?? '—' }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-semibold text-ink-soft">Dibuat</dt>
                            <dd class="mt-1 text-sm text-ink">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </x-ui.form-section>
            </x-ui.sheet>

            <!-- Role Tambahan -->
            <x-ui.sheet>
                <x-ui.form-section title="Role Tambahan" description="Role tambahan yang dimiliki pengguna selain role utama.">
                    @forelse ($user->userRoles as $ur)
                        @php
                            $extraLabel = match($ur->role) {
                                'super_admin' => 'Super Admin',
                                'kepala_madrasah' => 'Kepala Madrasah',
                                'guru' => 'Guru',
                                'siswa' => 'Siswa',
                                'orang_tua' => 'Orang Tua',
                                default => ucfirst(str_replace('_', ' ', $ur->role)),
                            };
                        @endphp
                        <x-ui.badge :variant="'neutral'" :dot="false">{{ $extraLabel }}</x-ui.badge>
                    @empty
                        <p class="text-sm text-ink-faint">Tidak ada role tambahan.</p>
                    @endforelse
                </x-ui.form-section>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
