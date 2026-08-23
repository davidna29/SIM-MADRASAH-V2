<x-layouts.page
    :title="'Penempatan Kelas'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb">

    <div class="mx-auto max-w-5xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kelas {{ $classGroup->name }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Siswa aktif di kelas ini untuk Tahun Ajaran {{ $tahun->name }}. Penempatan lama tidak pernah dihapus — hanya ditandai alumni.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="pencil-square" href="{{ route('kelas.edit', $classGroup) }}">Ubah Kelas</x-ui.button>
            </div>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[1fr_360px]">
            <!-- Daftar siswa -->
            <x-ui.sheet title="Siswa Aktif" :subtitle="count($enrollments) . ' siswa'" pinned :padding="false">
                <x-ui.table :headers="['NIS', 'Nama Siswa', '']">
                    <x-slot name="emptySlot">Belum ada siswa di kelas ini.</x-slot>
                    <x-slot>
                        @foreach ($enrollments as $enrollment)
                            <tr class="transition hover:bg-paper/60">
                                <td class="tabular px-4 py-3 font-mono text-xs font-semibold text-ink-faint">{{ $enrollment->student->nis }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">
                                            {{ mb_substr($enrollment->student->name, 0, 1) }}
                                        </span>
                                        <span class="font-semibold text-ink">{{ $enrollment->student->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('kelas.unplace', [$classGroup, $enrollment]) }}" onsubmit="return confirm('Keluarkan siswa ini dari kelas? (status menjadi alumni)');">
                                        @csrf
                                        <x-ui.button type="submit" size="sm" variant="ghost" icon="x-mark">Keluarkan</x-ui.button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
            </x-ui.sheet>

            <!-- Tambah penempatan -->
            <x-ui.sheet title="Tempatkan Siswa" subtitle="Pilih siswa yang belum terdaftar di kelas lain" pinned>
                <form method="POST" action="{{ route('kelas.place', $classGroup) }}" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-ink">Siswa tersedia ({{ count($availableStudents) }})</label>
                        <div class="max-h-72 space-y-1 overflow-y-auto rounded-[var(--radius-control)] ring-1 ring-inset ring-rule/70 p-2">
                            @forelse ($availableStudents as $student)
                                <label class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 transition hover:bg-paper-deep">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="size-4 rounded border-rule-strong text-primary focus:ring-primary">
                                    <span class="text-[13px] font-medium text-ink">{{ $student->name }}</span>
                                    <span class="tabular ml-auto font-mono text-xs text-ink-faint">{{ $student->nis }}</span>
                                </label>
                            @empty
                                <p class="px-2 py-3 text-xs text-ink-faint">Semua siswa sudah ditempatkan.</p>
                            @endforelse
                        </div>
                    </div>
                    <x-ui.button type="submit" variant="primary" icon="user-plus" class="w-full">Tempatkan ke Kelas Ini</x-ui.button>
                </form>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
