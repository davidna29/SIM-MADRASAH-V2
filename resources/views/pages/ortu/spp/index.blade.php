<x-layouts.page
    :title="'SPP Anak'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ortu.spp.index">

    <div class="mx-auto max-w-4xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">SPP Anak</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Status pembayaran SPP anak Anda pada tahun ajaran berjalan — hanya bersifat lihat.
            </p>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            @forelse ($students as $student)
                <x-ui.sheet
                    :title="$student->name"
                    :subtitle="'NIS ' . $student->nis"
                    pinned
                    :actions="view('components.ui.button', ['variant' => 'primary', 'size' => 'sm', 'icon' => 'banknotes', 'href' => route('ortu.spp.show', $student)])->withSlot('Lihat SPP')->render()">
                    <x-ui.badge variant="info" icon="banknotes">Status SPP tersedia</x-ui.badge>
                </x-ui.sheet>
            @empty
                <div class="rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60 px-5 py-10 text-center sm:col-span-2">
                    <p class="text-sm font-semibold text-ink">Belum ada data anak terhubung ke akun Anda.</p>
                    <p class="mt-1 text-xs text-ink-faint">Hubungi tata usaha madrasah untuk menghubungkan anak Anda.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.page>
