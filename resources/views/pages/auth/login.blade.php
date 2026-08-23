<x-layouts.root :title="'Masuk'">
    <div class="paper-grain flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <!-- Kepala papan -->
        <div class="mb-8 flex flex-col items-center text-center">
            <div class="flex size-14 items-center justify-center rounded-2xl bg-board text-board-ink shadow-sheet-raised">
                <x-svg-academic-cap class="size-8" aria-hidden="true" />
            </div>
            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">SIM Madrasah</h1>
            <p class="mt-1 max-w-sm text-sm leading-relaxed text-ink-soft">
                MTs Al-Ikhlas Mulia — Sistem Informasi Manajemen Madrasah Terintegrasi
            </p>
        </div>

        <div class="w-full max-w-sm">
            <div class="relative rounded-sheet bg-sheet p-7 shadow-sheet-raised ring-1 ring-inset ring-rule/60">
                <span class="pin-dot absolute -top-1.5 left-1/2 size-2.5 -translate-x-1/2 bg-primary" aria-hidden="true"></span>

                <h2 class="text-center text-lg font-extrabold tracking-tight text-ink">Masuk ke papan Anda</h2>
                <p class="mt-1 text-center text-xs text-ink-faint">Gunakan akun yang diberikan oleh tata usaha.</p>

                <form class="mt-6 space-y-4" action="{{ route('login.attempt') }}" method="POST">
                    @csrf
                    @if ($errors->any())
                        <x-ui.alert variant="danger" dismissible>
                            {{ $errors->first() }}
                        </x-ui.alert>
                    @endif
                    <x-ui.field label="Username atau Email" required :error="$errors->first('login')">
                        <x-ui.input name="login" :value="old('login')" autocomplete="username" placeholder="guru.umar atau admin@madrasah.sch.id" required autofocus />
                    </x-ui.field>
                    <x-ui.field label="Kata Sandi" required :error="$errors->first('password')">
                        <div class="relative" x-data="{ show: false }">
                            <x-ui.input name="password" x-bind:type="show ? 'text' : 'password'" autocomplete="current-password" placeholder="••••••••" required />
                            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink-faint transition hover:text-ink" aria-label="Tampilkan kata sandi" :aria-pressed="show" @click="show = !show">
                                <template x-if="!show">
                                    <x-svg-eye class="size-4" aria-hidden="true" />
                                </template>
                                <template x-if="show">
                                    <x-svg-eye-slash class="size-4" aria-hidden="true" />
                                </template>
                            </button>
                        </div>
                    </x-ui.field>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-xs font-medium text-ink-soft">
                            <input type="checkbox" name="remember" class="size-4 rounded border-rule-strong text-primary focus:ring-primary" checked />
                            Ingat saya
                        </label>
                    </div>

                    <x-ui.button type="submit" variant="primary" size="lg" icon="arrow-right-on-rectangle" class="w-full">Masuk ke Sistem</x-ui.button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs leading-relaxed text-ink-faint">
                Semua aktivitas Anda dicatat pada activity log untuk keperluan transparansi dan keamanan data
                (UU PDP No. 27 Tahun 2022).
            </p>
        </div>

        <div class="mt-8 flex flex-col items-center gap-2 text-xs text-ink-faint">
            <p>© 2026 MTs Al-Ikhlas Mulia · SIM Madrasah v2.1</p>
            <x-ui.badge variant="neutral" :dot="false">Data demo — bukan data riil</x-ui.badge>
        </div>
    </div>
</x-layouts.root>
