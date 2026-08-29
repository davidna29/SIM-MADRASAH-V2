<x-layouts.page
    :title="'Ganti Kata Sandi'"
    :roleLabel="auth()->user()->role"
    :breadcrumb="[['label' => 'Akun'], ['label' => 'Ganti Kata Sandi']]"
    active-route="password.change">

    <div class="mx-auto max-w-md">
        <x-ui.sheet title="Ganti Kata Sandi" subtitle="Anda wajib mengganti kata sandi sebelum melanjutkan." pinned ruled>
            @if (session('status'))
                <div class="mb-4"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                <x-ui.field label="Kata Sandi Saat Ini" required :error="$errors->first('current_password')">
                    <input type="password" name="current_password" required autocomplete="current-password" autofocus
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="••••••••">
                </x-ui.field>

                <x-ui.field label="Kata Sandi Baru" required :error="$errors->first('password')">
                    <input type="password" name="password" required autocomplete="new-password" minlength="8"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Minimal 8 karakter">
                </x-ui.field>

                <x-ui.field label="Ulangi Kata Sandi Baru" required :error="$errors->first('password_confirmation')">
                    <input type="password" name="password_confirmation" required autocomplete="new-password" minlength="8"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Ulangi kata sandi baru">
                </x-ui.field>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <x-ui.button type="submit" variant="primary" size="md" icon="check">Simpan Kata Sandi</x-ui.button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <x-ui.button type="submit" variant="ghost" size="sm">Keluar dari Akun</x-ui.button>
            </form>
        </x-ui.sheet>
    </div>
</x-layouts.page>