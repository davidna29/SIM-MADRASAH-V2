<x-layouts.page
    :title="'Portal Orang Tua'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ortu.dashboard">

    <div class="mx-auto max-w-4xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Anak Saya</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Lihat rapor anak Anda yang telah diterbitkan oleh madrasah.
                </p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            @forelse ($students as $student)
                @php
                    $terbit = $student->reports->where('status', 'terbit')->first();
                @endphp
                <x-ui.sheet
                    :title="$student->name"
                    :subtitle="'NIS ' . $student->nis"
                    pinned
                    :actions="$terbit ? view('components.ui.button', ['variant' => 'primary', 'size' => 'sm', 'icon' => 'eye', 'href' => route('ortu.rapor', $student)])->withSlot('Lihat Rapor')->render() : view('components.ui.badge', ['variant' => 'neutral', 'dot' => false])->withSlot('Menunggu')->render()">
                    <div class="flex items-center gap-2">
                        @if ($terbit)
                            <x-ui.badge variant="success" icon="check">Rapor telah terbit</x-ui.badge>
                        @else
                            <x-ui.badge variant="warning" icon="clock">Menunggu penerbitan</x-ui.badge>
                        @endif
                    </div>
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
