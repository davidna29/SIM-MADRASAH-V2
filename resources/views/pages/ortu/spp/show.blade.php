<x-layouts.page
    :title="'SPP Anak'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ortu.spp.show">

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">SPP {{ $student->name }}</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Status pembayaran SPP Semester {{ ucfirst($tahun->semester) }} {{ $tahun->name }}.
                </p>
            </div>
        </div>

        <div class="mt-6">
            <x-ui.sheet title="Status SPP" pinned :padding="false">
                <div class="grid grid-cols-1 gap-px overflow-hidden rounded-[var(--radius-control)] bg-rule-strong ring-1 ring-inset ring-rule-strong sm:grid-cols-4 mx-5 mt-5 sm:mx-6">
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">NIS</p>
                        <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">{{ $student->nis }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Kelas</p>
                        <p class="mt-0.5 text-sm font-bold text-ink">{{ $enrollment?->classGroup?->name ?? '—' }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Tahun Pelajaran</p>
                        <p class="mt-0.5 text-sm font-bold text-ink">{{ $tahun->name }}</p>
                    </div>
                    <div class="bg-paper px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-ink-faint">Nominal</p>
                        <p class="tabular mt-0.5 font-mono text-sm font-bold text-ink">
                            {{ $nominal !== null ? 'Rp '.number_format($nominal, 0, ',', '.') : '—' }}
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto px-5 pb-5 pt-4 sm:px-6">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-rule-strong">
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Bulan</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-ink-soft">Nominal</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-ink-soft">Status</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Tanggal Bayar</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-ink-soft">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule/70">
                            @foreach ($months as $bulan)
                                @php
                                    $payment = $payments->get($bulan);
                                    $lunas = $payment && $payment->isLunas();
                                @endphp
                                <tr class="transition hover:bg-paper/60">
                                    <td class="px-4 py-3 font-semibold text-ink">{{ $semesterMonthsLabel[$bulan] }}</td>
                                    <td class="tabular px-4 py-3 text-right font-mono font-semibold text-ink">
                                        {{ $payment ? 'Rp '.number_format($payment->nominal, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($lunas)
                                            <x-ui.badge variant="success" icon="check">Lunas</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="warning" icon="clock">Belum Bayar</x-ui.badge>
                                        @endif
                                    </td>
                                    <td class="tabular px-4 py-3 font-mono text-xs text-ink-soft">
                                        {{ $payment?->tanggal_bayar ? $payment->tanggal_bayar->format('d M Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-ink-soft">{{ ucfirst($payment?->metode ?? '—') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.sheet>
        </div>
    </div>
</x-layouts.page>
