<x-layouts.page
    :title="'Penugasan Mengajar'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="penugasan.index">

    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Penugasan Mengajar</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Guru mapel per kelas pada Tahun Ajaran {{ $tahun->name }} — dasar otorisasi input nilai dan jadwal.
                </p>
            </div>
            <x-ui.button variant="primary" icon="plus" href="{{ route('penugasan.create') }}">Tambah Penugasan</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        <form method="GET" action="{{ route('penugasan.index') }}" class="mt-6 rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 p-4">
            <div class="flex flex-wrap items-center gap-3">
                <x-ui.select name="grade_level" :full="false" class="w-32" :options="$gradeOptions" :selected="request('grade_level')" placeholder="Semua tingkat" />
                <x-ui.select name="subject_id" :full="false" class="w-56" :options="$subjectOptions" :selected="request('subject_id')" placeholder="Semua mapel" />
                <x-ui.button type="submit" variant="secondary" size="md">Terapkan</x-ui.button>
                @if (request()->hasAny(['grade_level', 'subject_id']))
                    <x-ui.button variant="ghost" size="md" href="{{ route('penugasan.index') }}">Reset</x-ui.button>
                @endif
            </div>
        </form>

        <div class="mt-4">
            <x-ui.sheet :padding="false">
                <x-ui.table :headers="['Guru', 'Kelas', 'Mata Pelajaran', '']">
                    <x-slot name="emptySlot">Belum ada penugasan mengajar.</x-slot>
                    <x-slot>
                        @foreach ($assignments as $assignment)
                            <tr class="transition hover:bg-paper/60">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary-soft text-[11px] font-extrabold text-primary-strong">
                                            {{ mb_substr($assignment->teacher->name, 0, 1) }}
                                        </span>
                                        <span class="font-semibold text-ink">{{ $assignment->teacher->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <x-ui.badge variant="info">{{ $assignment->classGroup->name }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 font-medium text-ink">{{ $assignment->subject->name }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('penugasan.edit', $assignment) }}">Ubah</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-ui.table>
                <div class="border-t border-rule/70 px-4 py-3.5">
                    <x-ui.pagination :current="$assignments->currentPage()" :last="$assignments->lastPage()" />
                </div>
            </x-ui.sheet>
        </div>

        <div class="mt-6">
            <x-ui.alert variant="info" dismissible>
                <strong class="font-bold">Catatan akses:</strong> penugasan menentukan siapa yang boleh mengisi nilai dan jurnal pada
                kelas & mapel tertentu — ditegakkan pada lapisan penugasan, bukan sekadar role.
            </x-ui.alert>
        </div>
    </div>
</x-layouts.page>
