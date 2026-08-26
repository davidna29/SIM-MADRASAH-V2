<x-layouts.page
    :title="'Backup & Restore'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="backup.index">

    <div class="mx-auto max-w-6xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Backup & Restore</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Cadangkan dan pulihkan data database serta file storage madrasah.
            </p>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    {{ $errors->first() }}
                </x-ui.alert>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-control)] bg-primary-soft">
                        <x-svg-archive-box class="size-5 text-primary" />
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold tabular text-ink">{{ $backupCount }}</p>
                        <p class="text-xs text-ink-soft">Total Backup</p>
                    </div>
                </div>
            </div>

            <div class="rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-control)] bg-info-soft">
                        <x-svg-server-stack class="size-5 text-info" />
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold tabular text-ink">{{ $totalSize }}</p>
                        <p class="text-xs text-ink-soft">Total Ukuran</p>
                    </div>
                </div>
            </div>

            <div class="rounded-sheet bg-sheet p-5 shadow-sheet ring-1 ring-inset ring-rule/60">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-[var(--radius-control)] bg-success-soft">
                        <x-svg-clock class="size-5 text-success" />
                    </div>
                    <div>
                        <p class="text-sm font-bold tabular text-ink">
                            {{ $backups->isNotEmpty() ? $backups->first()['date_human'] : '—' }}
                        </p>
                        <p class="text-xs text-ink-soft">Backup Terakhir</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-6 flex flex-wrap gap-3" x-data="{ creatingDb: false, creatingFiles: false }">
            <form method="POST" action="{{ route('backup.store-db') }}" @submit="creatingDb = true">
                @csrf
                <button type="submit" x-bind:disabled="creatingDb"
                    class="inline-flex items-center justify-center rounded-[var(--radius-control)] font-semibold transition duration-150 ease-out active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50 h-10 px-4 text-sm gap-2 bg-primary text-white shadow-sm hover:bg-primary-strong active:bg-primary-strong focus-visible:outline-primary">
                    <x-svg-cloud-arrow-down class="size-4 shrink-0" />
                    <span x-show="!creatingDb">Backup Database</span>
                    <span x-show="creatingDb" x-cloak>Memproses…</span>
                </button>
            </form>

            <form method="POST" action="{{ route('backup.store-files') }}" @submit="creatingFiles = true">
                @csrf
                <button type="submit" x-bind:disabled="creatingFiles"
                    class="inline-flex items-center justify-center rounded-[var(--radius-control)] font-semibold transition duration-150 ease-out active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50 h-10 px-4 text-sm gap-2 bg-sheet text-ink ring-1 ring-inset ring-rule-strong hover:bg-paper-deep active:bg-paper-deep">
                    <x-svg-cloud-arrow-up class="size-4 shrink-0" />
                    <span x-show="!creatingFiles">Backup File Storage</span>
                    <span x-show="creatingFiles" x-cloak>Memproses…</span>
                </button>
            </form>
        </div>

        {{-- Upload Section --}}
        <div class="mt-6">
            <x-ui.sheet title="Unggah Backup" subtitle="Unggah file .sql atau .zip untuk dipulihkan nanti" pinned ruled>
                <form method="POST" action="{{ route('backup.upload') }}" enctype="multipart/form-data"
                    class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div class="flex-1 min-w-[200px]">
                        <label for="backup_file" class="block pb-1.5 text-xs font-bold text-ink">File Backup</label>
                        <input type="file" name="backup_file" id="backup_file" accept=".sql,.zip"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition file:mr-3 file:rounded-[var(--radius-control)] file:border-0 file:bg-primary-soft file:px-3 file:py-1 file:text-xs file:font-semibold file:text-primary hover:file:bg-primary/20 focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <x-ui.button type="submit" variant="success" size="md" icon="arrow-up-tray">Unggah</x-ui.button>
                </form>
            </x-ui.sheet>
        </div>

        {{-- Backup List --}}
        <div class="mt-6">
            <x-ui.sheet title="Daftar Backup" :subtitle="$backups->count() . ' file'" pinned ruled>
                @if ($backups->isEmpty())
                    <div class="py-12 text-center">
                        <x-svg-archive-box class="mx-auto size-12 text-ink-faint/40" />
                        <p class="mt-3 text-sm text-ink-faint">Belum ada backup. Klik tombol di atas untuk membuat backup pertama.</p>
                    </div>
                @else
                    <x-ui.table :headers="['Nama File', 'Tipe', 'Ukuran', 'Tanggal', 'Aksi']">
                        @foreach ($backups as $backup)
                            @php
                                $encodedName = urlencode($backup['name']);
                            @endphp
                            <tr class="hover:bg-paper-deep/50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if ($backup['type'] === 'Database')
                                            <x-svg-server-stack class="size-4 shrink-0 text-primary" />
                                        @elseif ($backup['type'] === 'File Storage')
                                            <x-svg-archive-box class="size-4 shrink-0 text-info" />
                                        @else
                                            <x-svg-arrow-up-tray class="size-4 shrink-0 text-success" />
                                        @endif
                                        <span class="font-mono text-xs text-ink">{{ $backup['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :variant="match($backup['type']) {
                                        'Database' => 'primary',
                                        'File Storage' => 'info',
                                        default => 'success',
                                    }" :dot="false">{{ $backup['type'] }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-xs tabular text-ink-soft">{{ $backup['size_human'] }}</td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ $backup['date_human'] }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        {{-- Download --}}
                                        <a href="{{ route('backup.download', $encodedName) }}"
                                            class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-primary transition hover:bg-primary-soft"
                                            title="Unduh">
                                            <x-svg-arrow-down-tray class="size-3.5" />
                                        </a>

                                        {{-- Restore (only for .sql files) --}}
                                        @if ($backup['extension'] === 'sql')
                                            <button type="button"
                                                x-data="{ open: false, filename: '{{ $encodedName }}', confirmation: '' }"
                                                @click="open = true; confirmation = ''"
                                                class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-warning transition hover:bg-warning-soft"
                                                title="Restore">
                                                <x-svg-arrow-path class="size-3.5" />
                                            </button>

                                            {{-- Restore Modal --}}
                                            <div x-data="{ open: false }"
                                                x-show="open" x-cloak
                                                class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
                                                x-transition:enter="transition ease-out duration-150"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-end="opacity-0"
                                                @keydown.escape.window="open = false"
                                                role="dialog" aria-modal="true">
                                                <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]"></div>
                                                <div class="relative w-full max-w-lg rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule">
                                                    <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                                                        <h3 class="text-sm font-bold tracking-tight text-ink">Restore Database</h3>
                                                        <button type="button" @click="open = false"
                                                            class="rounded-md p-1 text-ink-faint transition hover:bg-paper-deep hover:text-ink">
                                                            <x-svg-x-mark class="size-5" />
                                                        </button>
                                                    </header>
                                                    <div class="px-5 py-5">
                                                        <x-ui.alert variant="warning" :dismissible="false">
                                                            <strong>Peringatan:</strong> Semua data database saat ini akan diganti dengan isi backup ini. Tindakan ini tidak dapat dibatalkan.
                                                        </x-ui.alert>
                                                        <div class="mt-4">
                                                            <label class="block pb-1.5 text-xs font-bold text-ink">Ketik <strong>RESTORE</strong> untuk mengonfirmasi</label>
                                                            <x-ui.input type="text" name="confirmation" placeholder="RESTORE"
                                                                x-model="confirmation"
                                                                autocomplete="off" />
                                                        </div>
                                                    </div>
                                                    <footer class="flex items-center justify-end gap-2 border-t border-rule/70 px-5 py-4">
                                                        <x-ui.button variant="ghost" size="md" @click="open = false">Batal</x-ui.button>
                                                        <form method="POST" action="{{ route('backup.restore') }}" class="inline">
                                                            @csrf
                                                            <input type="hidden" name="filename" :value="filename">
                                                            <input type="hidden" name="confirmation" :value="confirmation">
                                                            <button type="submit"
                                                                x-bind:disabled="confirmation !== 'RESTORE'"
                                                                class="inline-flex items-center justify-center rounded-[var(--radius-control)] font-semibold transition duration-150 ease-out active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50 h-10 px-4 text-sm gap-2 bg-danger text-white shadow-sm hover:brightness-95 active:brightness-90">
                                                                <x-svg-arrow-path class="size-4 shrink-0" />
                                                                Restore
                                                            </button>
                                                        </form>
                                                    </footer>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('backup.destroy', $encodedName) }}"
                                            x-data="{ confirming: false }"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="confirming = true"
                                                class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-danger transition hover:bg-danger-soft"
                                                title="Hapus">
                                                <x-svg-trash class="size-3.5" />
                                            </button>

                                            {{-- Delete Confirmation --}}
                                            <div x-show="confirming" x-cloak
                                                class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
                                                x-transition:enter="transition ease-out duration-150"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-end="opacity-0"
                                                @keydown.escape.window="confirming = false"
                                                role="dialog" aria-modal="true">
                                                <div class="absolute inset-0 bg-board-deep/60 backdrop-blur-[2px]"></div>
                                                <div class="relative w-full max-w-md rounded-sheet bg-sheet shadow-sheet-raised ring-1 ring-inset ring-rule">
                                                    <header class="flex items-center justify-between border-b border-rule/70 px-5 py-4">
                                                        <h3 class="text-sm font-bold tracking-tight text-ink">Hapus Backup</h3>
                                                        <button type="button" @click="confirming = false"
                                                            class="rounded-md p-1 text-ink-faint transition hover:bg-paper-deep hover:text-ink">
                                                            <x-svg-x-mark class="size-5" />
                                                        </button>
                                                    </header>
                                                    <div class="px-5 py-5">
                                                        <p class="text-sm text-ink">Yakin ingin menghapus backup ini?</p>
                                                        <p class="mt-1 text-xs text-ink-soft">Tindakan ini tidak dapat dibatalkan.</p>
                                                    </div>
                                                    <footer class="flex items-center justify-end gap-2 border-t border-rule/70 px-5 py-4">
                                                        <x-ui.button variant="ghost" size="md" @click="confirming = false">Batal</x-ui.button>
                                                        <x-ui.button type="submit" variant="danger" size="md" icon="trash">Hapus</x-ui.button>
                                                    </footer>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
