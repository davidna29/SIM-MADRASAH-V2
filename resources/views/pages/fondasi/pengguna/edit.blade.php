<x-layouts.page
    :title="'Ubah Pengguna'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="pengguna.index">

    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Ubah Pengguna</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Perbarui data akun <strong class="font-semibold text-ink">{{ $user->name }}</strong> — ubah informasi dan role pengguna.
            </p>
        </div>

        @if ($errors->any())
            <x-ui.alert variant="danger" class="mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('pengguna.update', $user) }}">
            @csrf
            @method('PUT')

            <x-ui.sheet>
                <x-ui.form-section title="Data Akun" description="Informasi dasar akun pengguna untuk masuk ke sistem.">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-ink-soft mb-1.5">Nama Lengkap <span class="text-danger">*</span></label>
                            <x-ui.input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required />
                            @error('name') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="username" class="block text-xs font-semibold text-ink-soft mb-1.5">Username</label>
                            <x-ui.input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" />
                            @error('username') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-semibold text-ink-soft mb-1.5">Email <span class="text-danger">*</span></label>
                            <x-ui.input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required />
                            @error('email') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="role" class="block text-xs font-semibold text-ink-soft mb-1.5">Role Utama <span class="text-danger">*</span></label>
                            <x-ui.select name="role" id="role" :options="$roleOptions" :selected="old('role', $user->role)" />
                            @error('role') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Ubah Password" description="Kosongkan jika tidak ingin mengubah password.">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="block text-xs font-semibold text-ink-soft mb-1.5">Password Baru</label>
                            <x-ui.input type="password" name="password" id="password" placeholder="Minimal 8 karakter" />
                            @error('password') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-ink-soft mb-1.5">Konfirmasi Password</label>
                            <x-ui.input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password" />
                        </div>
                    </div>
                </x-ui.form-section>

                <x-ui.form-section title="Role Tambahan" description="Pilih role tambahan jika pengguna memiliki lebih dari satu peran.">
                    @php
                        $currentAdditionalRoles = $user->userRoles->pluck('role')->toArray();
                    @endphp
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($roleOptions as $value => $label)
                            @if ($value !== 'super_admin')
                                <label class="flex items-center gap-2 rounded-[var(--radius-control)] bg-paper px-3 py-2 ring-1 ring-inset ring-rule transition hover:bg-paper-deep cursor-pointer">
                                    <input type="checkbox" name="additional_roles[]" value="{{ $value }}" {{ in_array($value, old('additional_roles', $currentAdditionalRoles)) ? 'checked' : '' }} class="size-4 rounded border-rule-strong text-primary focus:ring-primary">
                                    <span class="text-sm text-ink">{{ $label }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @error('additional_roles') <p class="mt-1 text-xs text-danger">{{ $message }}</p> @enderror
                </x-ui.form-section>

                <div class="flex items-center justify-end gap-3 border-t border-rule/70 px-5 py-4 sm:px-6">
                    <x-ui.button variant="ghost" href="{{ route('pengguna.index') }}">Batal</x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="check">Perbarui Pengguna</x-ui.button>
                </div>
            </x-ui.sheet>
        </form>
    </div>
</x-layouts.page>
