<x-layouts.page
    :title="'Ubah Jurnal'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="guru.jurnal.edit">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Ubah Jurnal</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    {{ $assignment->subject->name }} · kelas {{ $assignment->classGroup->name }}
                    · {{ $journal->journal_date->isoFormat('dddd, D MMM YYYY') }}.
                </p>
            </div>
            <x-ui.badge variant="info" icon="book-open">{{ $assignment->subject->code }}</x-ui.badge>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>
                    <strong class="font-bold">Periksa kembali:</strong>
                    @foreach ($errors->all() as $error) {{ $error }} @endforeach
                </x-ui.alert>
            </div>
        @endif

        <div class="mt-6">
            @include('pages.guru.jurnal._form', ['assignment' => $assignment, 'journal' => $journal])
        </div>
    </div>
</x-layouts.page>
